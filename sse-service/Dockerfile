FROM golang:1.21-alpine AS build
WORKDIR /app
COPY . .
RUN go build -o sse-service main.go

FROM alpine:3.18
WORKDIR /app
COPY --from=build /app/sse-service /usr/local/bin/sse-service
EXPOSE 6673
CMD ["sse-service"]
