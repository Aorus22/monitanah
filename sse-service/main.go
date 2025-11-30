package main

import (
	"context"
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"os"
	"strconv"
	"time"

	_ "github.com/go-sql-driver/mysql"
	"github.com/joho/godotenv"
)

type SensorData struct {
	Parameter string    `json:"parameter"`
	SensorNo  int       `json:"sensor_no"`
	Value     float64   `json:"value"`
	UpdatedAt time.Time `json:"updated_at"`
}

type SensorHistory struct {
	Parameter string    `json:"parameter"`
	SensorNo  int       `json:"sensor_no"`
	Value     float64   `json:"value"`
	CreatedAt time.Time `json:"created_at"`
}

type Event struct {
	Type      string      `json:"type"` // realtime|history
	Payload   interface{} `json:"payload"`
	Timestamp time.Time   `json:"timestamp"`
}

func dsn() string {
	host := os.Getenv("DB_HOST")
	port := os.Getenv("DB_PORT")
	user := os.Getenv("DB_USER")
	pass := os.Getenv("DB_PASS")
	name := os.Getenv("DB_NAME")
	// Fallback to Laravel-style env names
	if user == "" {
		user = os.Getenv("DB_USERNAME")
	}
	if pass == "" {
		pass = os.Getenv("DB_PASSWORD")
	}
	if name == "" {
		name = os.Getenv("DB_DATABASE")
	}
	return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true", user, pass, host, port, name)
}

func fetchRealtime(db *sql.DB) (map[string]SensorData, error) {
	rows, err := db.Query(`SELECT parameter, sensor_no, value, updated_at FROM sensor_data`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	result := make(map[string]SensorData)
	for rows.Next() {
		var d SensorData
		if err := rows.Scan(&d.Parameter, &d.SensorNo, &d.Value, &d.UpdatedAt); err != nil {
			return nil, err
		}
		key := fmt.Sprintf("%s-%d", d.Parameter, d.SensorNo)
		result[key] = d
	}
	return result, nil
}

func fetchHistory(db *sql.DB, since time.Time) ([]SensorHistory, error) {
	rows, err := db.Query(`SELECT parameter, sensor_no, value, created_at FROM sensor_histories WHERE created_at > ? ORDER BY created_at ASC`, since)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []SensorHistory
	for rows.Next() {
		var h SensorHistory
		if err := rows.Scan(&h.Parameter, &h.SensorNo, &h.Value, &h.CreatedAt); err != nil {
			return nil, err
		}
		list = append(list, h)
	}
	return list, nil
}

func main() {
	// Load env from local files when running via air/go run
	_ = godotenv.Load(".env")
	_ = godotenv.Load("../.env")

	db, err := sql.Open("mysql", dsn())
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	intervalMs, _ := strconv.Atoi(os.Getenv("POLL_INTERVAL_MS"))
	if intervalMs == 0 {
		intervalMs, _ = strconv.Atoi(os.Getenv("SSE_POLL_INTERVAL_MS"))
	}
	if intervalMs == 0 {
		intervalMs = 2000
	}

	http.HandleFunc("/status", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		ctx, cancel := context.WithTimeout(r.Context(), 2*time.Second)
		defer cancel()
		if err := db.PingContext(ctx); err != nil {
			w.WriteHeader(http.StatusServiceUnavailable)
			_ = json.NewEncoder(w).Encode(map[string]string{"status": "unhealthy", "error": err.Error()})
			return
		}
		_ = json.NewEncoder(w).Encode(map[string]string{"status": "ok"})
	})

	http.HandleFunc("/events", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "text/event-stream")
		w.Header().Set("Cache-Control", "no-cache")
		w.Header().Set("Connection", "keep-alive")
		w.Header().Set("Access-Control-Allow-Origin", "*")

		ctx := r.Context()

		// initial state snapshot
		lastHistoryTime := time.Now().Add(-1 * time.Hour)
		lastRealtime, _ := fetchRealtime(db)

		send := func(ev Event) {
			b, _ := json.Marshal(ev)
			fmt.Fprintf(w, "data: %s\n\n", string(b))
			if f, ok := w.(http.Flusher); ok {
				f.Flush()
			}
		}

		// send initial realtime snapshot
		for _, v := range lastRealtime {
			send(Event{Type: "realtime", Payload: v, Timestamp: time.Now()})
		}

		ticker := time.NewTicker(time.Duration(intervalMs) * time.Millisecond)
		defer ticker.Stop()

		for {
			select {
			case <-ctx.Done():
				return
			case <-ticker.C:
				currentRealtime, err := fetchRealtime(db)
				if err == nil {
					for k, v := range currentRealtime {
						prev, ok := lastRealtime[k]
						if !ok || v.UpdatedAt.After(prev.UpdatedAt) || v.Value != prev.Value {
							send(Event{Type: "realtime", Payload: v, Timestamp: time.Now()})
						}
					}
					lastRealtime = currentRealtime
				}
				if histories, err := fetchHistory(db, lastHistoryTime); err == nil && len(histories) > 0 {
					for _, h := range histories {
						send(Event{Type: "history", Payload: h, Timestamp: time.Now()})
						if h.CreatedAt.After(lastHistoryTime) {
							lastHistoryTime = h.CreatedAt
						}
					}
				}
			}
		}
	})

	port := "6673"
	log.Printf("SSE service running on :%s", port)
	if err := http.ListenAndServe(":"+port, nil); err != nil {
		log.Fatal(err)
	}
}
