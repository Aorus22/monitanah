@extends('layouts')
@section('content')
    <title>Sistem Monitoring Tanah</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f766e',
                        secondary: '#14b8a6',
                        accent: '#06b6d4',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                        success: '#10b981'
                    }
                }
            }
        }
    </script>
    <style>

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .loader {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid #0f766e;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .last-updated {
            font-size: 0.85rem;
            color: #6b7280;
            text-align: right;
            margin-top: 10px;
        }
    </style>

<body class="bg-gray-50 text-gray-800 min-h-screen px-4 py-6 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header with Export Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                <i class="fas fa-chart-line text-emerald-600"></i> Dashboard Monitoring
            </h1>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('sensor.export.pdf') }}?data_type=realtime&sensor_type=all&start_date={{ date('Y-m-d', strtotime('-7 days')) }}&end_date={{ date('Y-m-d') }}"
                   class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transition-all duration-300 flex items-center gap-2 flex-1 sm:flex-initial justify-center"
                   target="_blank">
                    <i class="fas fa-file-pdf"></i>
                    <span class="hidden sm:inline">PDF</span>
                </a>
                <a href="{{ route('sensor.export.excel') }}?data_type=realtime&sensor_type=all&start_date={{ date('Y-m-d', strtotime('-7 days')) }}&end_date={{ date('Y-m-d') }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transition-all duration-300 flex items-center gap-2 flex-1 sm:flex-initial justify-center">
                    <i class="fas fa-file-excel"></i>
                    <span class="hidden sm:inline">Excel</span>
                </a>
            </div>
        </div>
<!-- Realtime Sensor Data -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-10">
    <!-- Box Sensor Data -->
    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 card col-span-3">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-sync-alt text-emerald-600"></i> Data Sensor Real-time
            </h2>
            <div id="realtime-loader" class="loader" style="display: none;"></div>
        </div>
        <!-- Dropdown Sensor -->
        <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
            <label for="sensorDropdown" class="text-gray-700 font-medium text-sm sm:text-base">Pilih Parameter:</label>
            <select id="sensorDropdown" class="w-full sm:w-auto border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>

        </div>

    <div id="sensorContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <!-- Card pH -->
            <!-- <div class="p-4 sm:p-3 bg-emerald-50 rounded-lg flex flex-col items-center">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Sensor 1</span>
                <div id="sensor-value-1" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1" id="sensor-range-1">Optimal: 6.0 - 7.5</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="sensor-bar-1" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="sensor-status-1" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div class="p-4 sm:p-3 bg-emerald-50 rounded-lg flex flex-col items-center">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Sensor 2</span>
                <div id="sensor-value-2" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1" id="sensor-range-2">Optimal: 6.0 - 7.5</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="sensor-bar-2" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="sensor-status-2" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div class="p-4 sm:p-3 bg-emerald-50 rounded-lg flex flex-col items-center">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Sensor 3</span>
                <div id="sensor-value-3" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1" id="sensor-range-3">Optimal: 6.0 - 7.5</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="sensor-bar-3" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="sensor-status-3" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div class="p-4 sm:p-3 bg-emerald-50 rounded-lg flex flex-col items-center">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Sensor 4</span>
                <div id="sensor-value-4" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1" id="sensor-range-4">Optimal: 6.0 - 7.5</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="sensor-bar-4" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="sensor-status-4" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div> -->

            <div class="p-4 sm:p-3 bg-emerald-50 rounded-lg flex flex-col items-center">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">pH</span>
                <div id="ph" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 6.0 - 7.5</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="ph-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="ph_status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <!-- Card Suhu -->
            <div class="p-4 sm:p-3 bg-orange-50 rounded-lg flex flex-col items-center">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Suhu Tanah</span>
                <div id="suhu" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 22°C - 30°C</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="suhu-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="suhu-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div> -->
            <!-- Card TDS -->
            <div class="p-4 sm:p-3 bg-amber-50 rounded-lg flex flex-col items-center">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Kelembapan Tanah</span>
                <div id="tds" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 20% - 80%</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="tds-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="tds-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>
<!--    <script>-->
<!--   function updateTdsDisplay(tdsValue) {-->
<!--    const tdsDisplay = document.getElementById('tds');-->
<!--    const tdsBar = document.getElementById('tds-bar');-->
<!--    const tdsStatus = document.getElementById('tds-status');-->
<!--    const tdsNote = document.getElementById('tds-note');-->


<!--    tdsDisplay.textContent = tdsValue.toFixed(0) + " ppm";-->

<!--    // Hapus semua kelas warna sebelumnya-->
<!--    tdsDisplay.classList.remove('text-blue-500', 'text-green-600', 'text-red-700');-->

<!--    // Atur warna teks berdasarkan rentang TDS-->
<!--    if (tdsValue < 800) {-->
<!--        tdsDisplay.classList.add('text-blue-500'); // TDS terlalu rendah-->
<!--    } else if (tdsValue >= 800 && tdsValue <= 1200) {-->
<!--        tdsDisplay.classList.add('text-green-600'); // TDS optimal-->
<!--    } else {-->
<!--        tdsDisplay.classList.add('text-red-700'); // TDS terlalu tinggi-->
<!--    }-->

<!--    // Hitung panjang bar (maks 2000 ppm)-->
<!--    let percentage = Math.min((tdsValue / 2000) * 100, 100);-->
<!--    tdsBar.style.width = percentage + "%";-->

    <!--// Reset class bar-->
<!--    tdsBar.className = 'h-full transition-all duration-500';-->

    <!--// Logika status & warna bar & catatan-->
<!--    if (tdsValue < 800) {-->
<!--        tdsStatus.textContent = "Terlalu Rendah";-->
<!--        tdsBar.classList.add("bg-blue-400");-->
<!--        tdsNote.classList.remove("hidden");-->
<!--        tdsNote.innerHTML = `-->
<!--            <div class="font-semibold mb-1">⚠️ TDS Terlalu Rendah!</div>-->
<!--            <p class="text-sm mb-2">-->
<!--                Nilai TDS di bawah 800 ppm dapat menyebabkan kekurangan nutrisi bagi tanaman.-->
<!--            </p>-->
<!--            <ul class="list-disc ml-5 text-sm">-->
<!--                <li>Tambahkan nutrisi cair (AB Mix) sesuai dosis.</li>-->
<!--                <li>Periksa pompa atau sistem dosing otomatis.</li>-->
<!--                <li>Pastikan tidak ada kebocoran atau pengenceran berlebih.</li>-->
<!--            </ul>-->
<!--        `;-->
<!--    } else if (tdsValue > 1200) {-->
<!--        tdsStatus.textContent = "Terlalu Tinggi";-->
<!--        tdsBar.classList.add("bg-red-500");-->
<!--        tdsNote.classList.remove("hidden");-->
<!--        tdsNote.innerHTML = `-->
<!--            <div class="font-semibold mb-1">⚠️ TDS Terlalu Tinggi!</div>-->
<!--            <p class="text-sm mb-2">-->
<!--                Nilai TDS di atas 1200 ppm dapat merusak akar tanaman dan membahayakan ikan.-->
<!--            </p>-->
<!--            <ul class="list-disc ml-5 text-sm">-->
<!--                <li>Tambahkan air bersih (tanpa nutrisi) untuk mengencerkan larutan.</li>-->
<!--                <li>Kurangi pemberian nutrisi secara bertahap.</li>-->
<!--                <li>Periksa akurasi sensor TDS jika nilai ekstrem.</li>-->
<!--            </ul>-->
<!--        `;-->
<!--    } else {-->
<!--        tdsStatus.textContent = "Optimal";-->
<!--        tdsBar.classList.add("bg-green-500");-->
<!--        tdsNote.classList.add("hidden");-->
<!--    }-->
<!-- }-->

<!--Contoh panggilan fungsi:-->
<!--updateTdsDisplay(1350);-->
<!--// Ganti dengan nilai dinamis-->
<!--</script>-->

            </div>

            <!-- Sensor ID -->
            <div class="p-2 bg-gray-50 rounded-lg md:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-600">ID Sensor</span>
                    <i class="fas fa-microchip text-gray-500"></i>
                </div>
                <div id="sensor_id" class="text-xl font-mono font-bold mt-2 text-gray-700">-</div>
                <!-- Centered Save Snapshot button below the ID -->
                <div class="mt-3 flex justify-center">
                    <button id="saveSnapshotBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-3 rounded-lg shadow transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>Simpan Snapshot
                    </button>
                </div>
            </div>
        </div>

        <div id="realtime-error" class="mt-4 text-red-500 text-sm hidden">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>Gagal mengambil data real-time. Mencoba lagi...</span>
        </div>

        <div class="last-updated text-sm text-gray-500 mt-4">
    <span id="last-updated">
        Terakhir diperbarui: {{ $lastUpdated ? \Carbon\Carbon::parse($lastUpdated)->format('d-m-Y H:i:s') : '-' }}
    </span>
</div>

    </div>
</div>

    <!-- History Charts -->
    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 card mb-10 max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-emerald-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-emerald-600"></i> Grafik History Sensor
                </h2>
                <div id="chart-loader" class="loader" style="display: none;"></div>
            </div>

            <div class="max-w-4xl mx-auto w-full px-0 sm:px-2 md:px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-2 sm:p-3">
                        <canvas id="phChart" height="220" style="width:100%"></canvas>
                    </div>
                    <div class="p-2 sm:p-3">
                        <canvas id="suhuChart" height="220" style="width:100%"></canvas>
                    </div>
                    <div class="md:col-span-2 p-2 sm:p-3">
                        <canvas id="tdsChart" height="220" style="width:100%"></canvas>
                    </div>
                </div>
            </div>

            <div id="chart-error" class="mt-4 text-red-500 text-sm hidden">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>Gagal mengambil data history. Mencoba lagi...</span>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center py-6 text-gray-500 text-sm border-t mt-10">
            <div class="flex flex-col items-center gap-2">
                <div class="flex items-center gap-3">
                    <img src="/images/LOGO PT. JBG.png" alt="Logo JBG" class="h-8 w-auto" />
                </div>
                <p>© 2025 Sistem Monitoring Tanah</p>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>

let selectedSensorId = 1; // Default sensor 1

let isNoteShown = false;
let isTempNoteShown = false;
let isTdsNoteShown = false;

let lastPhCondition = 'normal';
let lastTempCondition = 'normal'; // <-- ❗ Wajib tambahkan ini
let lastTdsCondition = 'normal';

let currentType = 'ph'; // default tampilan pH

// Event listener untuk dropdown sensor
document.addEventListener('DOMContentLoaded', () => {
    const sensorDropdown = document.getElementById('sensorDropdown');

    sensorDropdown.addEventListener('change', function() {
        selectedSensorId = parseInt(this.value);
        console.log('Sensor changed to:', selectedSensorId);

        // Refetch data
        updateRealtimeSection();
        updateHistoryCharts();
    });

    // Initial load (history charts are initialized later — updateHistoryCharts
    // will be called after Chart.js charts are created to avoid referencing
    // chart variables before declaration.)
    updateRealtimeSection();

    // Attach save snapshot button handler if present
    const saveBtn = document.getElementById('saveSnapshotBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
            try {
                await saveSnapshot();
            } catch (err) {
                console.error('Failed to save snapshot', err);
                showToast('Gagal menyimpan snapshot', true);
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Snapshot';
            }
        });
    }
});

// Fungsi untuk format waktu
function formatTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

// Fungsi untuk format tanggal dan waktu
function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

// // Ambil data realtime semua node NEWWWW
// async function fetchRealtimeData() {
//   try {
//     const res = await fetch('/api/sensor/realtime');
//     if (!res.ok) throw new Error('Gagal ambil data');
//     return await res.json(); // hasilnya array
//   } catch (err) {
//     console.error(err);
//     return null;
//   }
// }

// Ambil data realtime terbaru dari API dengan penanganan error
async function fetchRealtimeData() {
    const loader = document.getElementById('realtime-loader');
    const errorElement = document.getElementById('realtime-error');

    loader.style.display = 'block';
    errorElement.classList.add('hidden');

    try {
        const res = await fetch(`/api/sensor/realtime?sensor_id=${selectedSensorId}`);
        if (!res.ok) throw new Error('Response not OK');
        return await res.json();
    } catch (error) {
        console.error('Error fetching realtime data:', error);
        errorElement.classList.remove('hidden');
        return null;
    } finally {
        loader.style.display = 'none';
    }
}

// Fungsi untuk menandai tindakan selesai
function markActionComplete() {
    const noteBox = document.querySelector('.ph-action-note');
    if (noteBox) {
        noteBox.style.animation = 'fadeOut 0.3s ease-in';
        setTimeout(() => {
            noteBox.remove();
            isNoteShown = false;
        }, 300);
    } else {
        isNoteShown = false;
    }

    const confirmation = document.createElement('div');
    confirmation.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        font-weight: 500;
        z-index: 1001;
        animation: slideIn 0.3s ease-out;
    `;
    confirmation.textContent = '✅ Tindakan telah ditandai selesai';
    document.body.appendChild(confirmation);

    setTimeout(() => {
        confirmation.remove();
    }, 3000);
}

// // Update tampilan semua sensor sesuai tipe
// async function updateRealtimeSection() {
//   const sensors = await fetchRealtimeData();
//   if (!sensors) return;

//   sensors.forEach(sensor => {
//     const id = sensor.id;
//     const value = sensor[currentType]; // ambil ph/suhu/tds sesuai dropdown
//     const elemValue = document.getElementById(`sensor-value-${id}`);
//     const elemBar = document.getElementById(`sensor-bar-${id}`);
//     const elemStatus = document.getElementById(`sensor-status-${id}`);
//     const elemRange = document.getElementById(`sensor-range-${id}`);

//     if (!elemValue) return;

//     // Isi nilai
//     elemValue.textContent = currentType === 'suhu' ? `${value.toFixed(1)}°C` : value.toFixed(2);

//     // Tampilan bar dan status tergantung jenis sensor
//     if (currentType === 'ph') {
//       elemRange.textContent = 'Optimal: 6.0 - 7.5';
//       elemBar.style.width = `${Math.min(Math.max(((value - 4) / 4) * 100, 0), 100)}%`;
//       elemBar.style.backgroundColor = value >= 6 && value <= 7.5 ? '#34d399' : '#f87171';
//       elemStatus.textContent = value < 6 ? 'Asam' : value > 7.5 ? 'Basa' : 'Optimal';
//     }
//     else if (currentType === 'suhu') {
//       elemRange.textContent = 'Ideal: 20°C - 30°C';
//       elemBar.style.width = `${Math.min(Math.max(((value - 10) / 30) * 100, 0), 100)}%`;
//       elemBar.style.backgroundColor = value >= 20 && value <= 30 ? '#34d399' : '#fbbf24';
//       elemStatus.textContent = value < 20 ? 'Terlalu Dingin' : value > 30 ? 'Terlalu Panas' : 'Ideal';
//     }
//     else if (currentType === 'tds') {
//       elemRange.textContent = 'Ideal: 40% - 80%';
//       elemBar.style.width = `${Math.min(Math.max(value, 0), 100)}%`;
//       elemBar.style.backgroundColor = value >= 40 && value <= 80 ? '#34d399' : '#f87171';
//       elemStatus.textContent = value < 40 ? 'Kering' : value > 80 ? 'Jenuh Air' : 'Ideal';
//     }
//   });
// }

// NOTE: the dropdown id used in the template is `sensorDropdown`.
// The earlier code referenced `sensorType` which does not exist and caused
// "Cannot read properties of null (reading 'addEventListener')" errors.
// The sensor dropdown is already handled inside DOMContentLoaded above,
// so we no longer need this extra listener.


// Update tampilan realtime dari data
async function updateRealtimeSection() {
    const data = await fetchRealtimeData();
    const sensorIdElement = document.getElementById('sensor_id');
    if (sensorIdElement) {
        sensorIdElement.textContent = selectedSensorId || '-';
    } else {
        console.warn('updateRealtimeSection: element #sensor_id not found');
    }

    if (!data) return;

    if (sensorIdElement) {
        sensorIdElement.textContent = (data && (data.sensor_id || data.id)) || selectedSensorId || '-';
    }
    const phElem = document.getElementById('ph');
    const suhuElem = document.getElementById('suhu');
    const tdsElem = document.getElementById('tds');

    if (phElem) phElem.textContent = (data && data.ph) != null ? data.ph : '-';
    if (suhuElem) suhuElem.textContent = (data && data.suhu) != null ? `${data.suhu}°C` : '-';
    if (tdsElem) tdsElem.textContent = (data && data.tds) != null ? data.tds : '-';


    //Display Ph
    const phVal = parseFloat(data.ph);
    const phDisplay = document.getElementById('ph');
    const phBar = document.getElementById('ph-bar');
    const phStatus = document.getElementById('ph-status');

    if (!isNaN(phVal)) {
        phDisplay.textContent = phVal.toFixed(2);
        phDisplay.classList.remove('text-green-600', 'text-yellow-500', 'text-red-700');

        if (phVal < 6) {
            phDisplay.classList.add('text-red-700');
            phBar.style.width = '33%';
            phBar.style.backgroundColor = '#de2121';
            phStatus.textContent = 'Terlalu rendah';
            if (lastPhCondition !== 'low') {
                showActionGuide(phVal, 'low');
                lastPhCondition = 'low';
            }
        } else {
            phDisplay.classList.add('text-green-600');
            phBar.style.width = '100%';
            phBar.style.backgroundColor = '#0ee697';
            phStatus.textContent = 'Optimal';
            lastPhCondition = 'normal';
            isNoteShown = false;
        }
    }

    //Display Suhu
    const suhuVal = parseFloat(data.suhu);
    const suhuDisplay = document.getElementById('suhu');
    const suhuBar = document.getElementById('suhu-bar');
    const suhuStatus = document.getElementById('suhu-status');

    if (!isNaN(suhuVal)) {
        suhuDisplay.textContent = suhuVal.toFixed(1) + '°C';

        if (suhuVal < 22) {
            suhuBar.style.width = '33%';
            suhuBar.style.backgroundColor = '#facc15';
            suhuStatus.textContent = 'Terlalu dingin';
        } else if (suhuVal <= 30) {
            suhuBar.style.width = '66%';
            suhuBar.style.backgroundColor = '#34d399';
            suhuStatus.textContent = 'Optimal';
        } else {
            suhuBar.style.width = '100%';
            suhuBar.style.backgroundColor = '#de2121';
            suhuStatus.textContent = 'Terlalu panas';
        }
    }


    // Fungsi untuk membuat note box dengan panduan tindakan manual
    function showActionGuide(phValue, condition) {
        if (isNoteShown) return; // ❌ Jika sudah pernah muncul, langsung keluar dari fungsi
        isNoteShown = true;      // ✅ Tandai bahwa modal sudah tampil
        // Hapus note box sebelumnya jika ada
        const existingNote = document.querySelector('.ph-action-note');
        if (existingNote) {
            existingNote.remove();
        }

        let noteContent = '';
        let noteClass = '';

        // if (condition === 'low') {
        //     noteClass = 'note-warning';
        //     noteContent = `
        //         <div class="note-header">
        //             <span class="note-icon">⚠️</span>
        //             <h3>pH Terlalu Rendah (${phValue.toFixed(1)})</h3>
        //         </div>
        //         <div class="note-body">
        //             <h4>🔧 Tindakan Manual yang Diperlukan:</h4>
        //             <ol>
        //                 <li><strong>Hentikan sistem sementara</strong> - Matikan pompa dan aliran air</li>
        //                 <li><strong>Tambahkan buffer alkali</strong> - Gunakan sodium bicarbonate (NaHCO₃) atau potassium hydroxide (KOH)</li>
        //                 <li><strong>Aduk secara manual</strong> - Pastikan distribusi merata selama 10-15 menit</li>
        //                 <li><strong>Tunggu stabilisasi</strong> - Biarkan selama 30 menit sebelum mengukur ulang</li>
        //                 <li><strong>Uji kembali pH</strong> - Pastikan berada di range 6.5-7.5</li>
        //             </ol>

        //             <div class="warning-box">
        //                 <strong>⚠️ Peringatan:</strong> Tambahkan buffer secara bertahap untuk menghindari perubahan pH yang terlalu drastis!
        //             </div>
        //         </div>
        //     `;
        // } else if (condition === 'high') {
        //     noteClass = 'note-danger';
        //     noteContent = `
        //         <div class="note-header">
        //             <span class="note-icon">🚨</span>
        //             <h3>pH Terlalu Tinggi (${phValue.toFixed(1)})</h3>
        //         </div>
        //         <div class="note-body">
        //             <h4>🚨 Tindakan Manual Darurat:</h4>
        //             <ol>
        //                 <li><strong>STOP sistem segera!</strong> - Matikan semua peralatan dan isolasi area</li>
        //                 <li><strong>Tambahkan asam lemah</strong> - Gunakan asam sitrat atau asam asetat (jangan HCl)</li>
        //                 <li><strong>Aduk perlahan</strong> - Gunakan alat non-logam, hindari percikan</li>
        //                 <li><strong>Ventilasi area kerja</strong> - Pastikan sirkulasi udara yang baik</li>
        //                 <li><strong>Monitor terus-menerus</strong> - Cek pH setiap 15 menit sampai stabil</li>
        //                 <li><strong>Bilas peralatan</strong> - Bersihkan semua sensor dan peralatan</li>
        //             </ol>


        //         </div>
        //     `;
        // }

        // Buat elemen note box
        // const noteBox = document.createElement('div');
        // noteBox.className = `ph-action-note ${noteClass}`;
        // noteBox.innerHTML = `
        //     <div class="note-container">
        //         ${noteContent}
        //         <div class="note-actions">

        //             <button class="btn-secondary" onclick="this.closest('.ph-action-note').remove(); isNoteShown = false;">Tutup</button>
        //         </div>
        //     </div>
        // `;

        // Tambahkan CSS jika belum ada
        if (!document.querySelector('#action-note-styles')) {
            const style = document.createElement('style');
            style.id = 'action-note-styles';
            style.textContent = `
                .ph-action-note {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    max-width: 500px;
                    max-height: 80vh;
                    overflow-y: auto;
                    z-index: 1000;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    border-radius: 12px;
                    animation: popIn 0.3s ease-out;
                }

                .ph-action-note::before {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: -1;
                }

                .note-container {
                    background: white;
                    border-radius: 12px;
                    overflow: hidden;
                }

                .note-warning {
                    border-top: 5px solid #f59e0b;
                }

                .note-danger {
                    border-top: 5px solid #dc2626;
                }

                .note-header {
                    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                    padding: 20px;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    border-bottom: 1px solid #e2e8f0;
                }

                .note-icon {
                    font-size: 24px;
                }

                .note-header h3 {
                    margin: 0;
                    color: #1e293b;
                    font-size: 18px;
                    font-weight: 600;
                }

                .note-body {
                    padding: 25px;
                }

                .note-body h4 {
                    color: #374151;
                    margin: 0 0 15px 0;
                    font-size: 16px;
                    font-weight: 600;
                }

                .note-body ol {
                    margin: 0 0 20px 0;
                    padding-left: 20px;
                }

                .note-body li {
                    margin-bottom: 8px;
                    line-height: 1.5;
                    color: #4b5563;
                }

                .note-body li strong {
                    color: #1f2937;
                }

                .warning-box, .danger-box {
                    padding: 12px 15px;
                    border-radius: 8px;
                    margin-top: 15px;
                    font-size: 14px;
                    line-height: 1.4;
                }

                .warning-box {
                    background: #fef3c7;
                    border: 1px solid #f59e0b;
                    color: #92400e;
                }

                .danger-box {
                    background: #fee2e2;
                    border: 1px solid #dc2626;
                    color: #991b1b;
                }

                .note-actions {
                    padding: 20px;
                    background: #f8fafc;
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                    border-top: 1px solid #e2e8f0;
                }

                .btn-primary, .btn-secondary {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 6px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.2s;
                }

                .btn-primary {
                    background: #059669;
                    color: white;
                }

                .btn-primary:hover {
                    background: #047857;
                }

                .btn-secondary {
                    background: #e5e7eb;
                    color: #374151;
                }

                .btn-secondary:hover {
                    background: #d1d5db;
                }

                @keyframes popIn {
                    from {
                        transform: translate(-50%, -50%) scale(0.8);
                        opacity: 0;
                    }
                    to {
                        transform: translate(-50%, -50%) scale(1);
                        opacity: 1;
                    }
                }

                @media (max-width: 600px) {
                    .ph-action-note {
                        max-width: 90%;
                        max-height: 90vh;
                    }

                    .note-actions {
                        flex-direction: column-reverse;
                    }

                    .btn-primary, .btn-secondary {
                        width: 100%;
                    }
                }
            `;
            document.head.appendChild(style);
        }

    // Tambahkan note box ke halaman
    // Append noteBox if it exists; if the more detailed noteBox creation
    // was accidentally commented out, create a lightweight fallback so the
    // app doesn't crash when showActionGuide() is called.
    if (typeof noteBox !== 'undefined') {
        document.body.appendChild(noteBox);
    } else {
        const fallback = document.createElement('div');
        fallback.className = 'ph-action-note';
        fallback.innerHTML = `
            <div class="note-container">
                <div class="note-body">
                    <h3 class="text-lg font-semibold">Peringatan pH</h3>
                    <p class="text-sm">Nilai pH: ${phValue}</p>
                    <div class="note-actions">
                        <button class="btn-secondary" onclick="this.closest('.ph-action-note').remove(); isNoteShown = false;">Tutup</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(fallback);
    }
}



    // if (!isNaN(tdsVal)) {
    //     tdsDisplay.textContent = tdsVal.toFixed(0); // Tampilkan angka tanpa desimal

    //     if (tdsVal < 500) {
    //         tdsDisplay.style.color = '#facc15'; // Kuning – Terlalu rendah
    //     } else if (tdsVal >= 500 && tdsVal <= 1000) {
    //         tdsDisplay.style.color = '#16a34a'; // Hijau – Optimal
    //     } else if (tdsVal > 1000 && tdsVal <= 1500) {
    //         tdsDisplay.style.color = '#facc15'; // Kuning – Mulai tinggi
    //     } else {
    //         tdsDisplay.style.color = '#dc2626'; // Merah – Terlalu tinggi
    //     }
    // } else {
    //     tdsDisplay.textContent = '-';
    //     tdsDisplay.style.color = '#6b7280'; // Abu-abu
    // }


    // --- Bar indikator TDS ---
// --- Display TDS ---
const tdsVal = parseFloat(data.tds);
const tdsDisplay = document.getElementById('tds');
const tdsBar = document.getElementById('tds-bar');
const tdsStatus = document.getElementById('tds-status');

if (!isNaN(tdsVal)) {
    tdsDisplay.textContent = `${tdsVal.toFixed(0)}%`;
    tdsDisplay.style.color = '';

    if (tdsVal < 20) {
        tdsDisplay.style.color = '#facc15'; // Kuning
        tdsBar.style.width = '33%';
        tdsBar.style.backgroundColor = '#facc15';
        tdsStatus.textContent = 'Terlalu rendah';

        if (lastTdsCondition !== 'low') {
            showTdsActionGuide(tdsVal, 'low');
            lastTdsCondition = 'low';
        }

    } else if (tdsVal <= 80) {
        tdsDisplay.style.color = '#16a34a'; // Hijau
        tdsBar.style.width = '66%';
        tdsBar.style.backgroundColor = '#34d399';
        tdsStatus.textContent = 'Optimal';

        lastTdsCondition = 'normal';
        isTdsNoteShown = false;

    } else {
        tdsDisplay.style.color = '#dc2626'; // Merah
        tdsBar.style.width = '100%';
        tdsBar.style.backgroundColor = '#de2121';
        tdsStatus.textContent = 'Berlebihan';

        if (lastTdsCondition !== 'high') {
            showTdsActionGuide(tdsVal, 'high');
            lastTdsCondition = 'high';
        }
    }
}

function showTdsActionGuide(tdsValue, condition) {
  if (isTdsNoteShown) return;
  isTdsNoteShown = true;

  // Hapus modal sebelumnya jika ada
  document.querySelectorAll('.tds-action-note').forEach(el => el.remove());

  // Konfigurasi isi dan tampilan berdasarkan kondisi
  // const config = {
  //   low: {
  //     icon: '🧪',
  //     title: `TDS Terlalu Rendah (${tdsValue.toFixed(0)} ppm)`,
  //     tips: [
  //       'Tambahkan nutrisi cair sesuai kebutuhan tanaman',
  //       'Pastikan air sistem tidak terlalu encer akibat penambahan air',
  //       'Gunakan EC meter sebagai verifikasi tambahan',
  //       'Aduk sistem agar nutrisi merata',
  //       'Ukur kembali TDS setelah 15 menit'
  //     ],
  //     border: 'border-yellow-400',
  //     note: `
  //       <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-800 p-3 rounded-md text-sm">
  //         ⚠️ Tambahkan nutrisi secara bertahap agar tidak melewati batas optimal!
  //       </div>
  //     `
  //   },
  //   high: {
  //     icon: '⚠️',
  //     title: `TDS Terlalu Tinggi (${tdsValue.toFixed(0)} ppm)`,
  //     tips: [
  //       'Buang sebagian air sistem (20-30%)',
  //       'Ganti dengan air bersih tanpa nutrisi',
  //       'Pastikan sirkulasi air tetap aktif',
  //       'Periksa efek pada ikan dan tanaman',
  //       'Ukur ulang setelah penggantian air'
  //     ],
  //     border: 'border-red-500',
  //     note: `
  //       <div class="mt-4 bg-red-100 border border-red-500 text-red-800 p-3 rounded-md text-sm">
  //         ‼️ TDS tinggi bisa menyebabkan stres pada ikan dan menghambat penyerapan nutrisi tanaman.
  //       </div>
  //     `
  //   }
  // };

  const { icon, title, tips, border, note } = config[condition];

  // Buat modal
  const noteBox = document.createElement('div');
  noteBox.className = `tds-action-note fixed inset-0 flex items-center justify-center z-50`;
  noteBox.innerHTML = `
    <div class="relative w-[90%] max-w-md bg-white rounded-xl shadow-xl border-t-4 ${border} animate-scale-in">
      <div class="flex items-center gap-3 p-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
        <span class="text-2xl">${icon}</span>
        <h3 class="text-lg font-semibold text-gray-800">${title}</h3>
      </div>
      <div class="p-5 text-sm text-gray-700">
        <ol class="list-decimal list-inside space-y-2">
          ${tips.map(t => `<li>${t}</li>`).join('')}
        </ol>
        ${note}
      </div>
      <div class="flex justify-end gap-2 px-4 py-3 border-t border-gray-100 bg-gray-50 rounded-b-xl">
        <button onclick="
          this.closest('.tds-action-note').remove();
          isTdsNoteShown = false;
        " class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-md transition">Tutup</button>
      </div>
    </div>
  `;

  // Tambahkan animasi scale-in jika belum ada
  if (!document.getElementById('tailwind-animation')) {
    const animStyle = document.createElement('style');
    animStyle.id = 'tailwind-animation';
    animStyle.textContent = `
      @keyframes scale-in {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
      }
      .animate-scale-in {
        animation: scale-in 0.2s ease-out;
      }
    `;
    document.head.appendChild(animStyle);
  }

  document.body.appendChild(noteBox);
}

function showTempActionGuide(tempValue, condition) {
  if (isTempNoteShown) return;
  isTempNoteShown = true;

  // Hapus modal suhu sebelumnya jika ada
  document.querySelectorAll('.suhu-action-note').forEach(el => el.remove());

  // Konfigurasi berdasarkan kondisi suhu
  // const config = {
  //   cold: {
  //     icon: '❄️',
  //     title: `Suhu Terlalu Dingin (${tempValue.toFixed(1)}°C)`,
  //     tips: [
  //       'Periksa sirkulasi air dan lokasi sumber dingin',
  //       'Tambahkan pemanas air jika tersedia',
  //       'Isolasi area sistem dari udara luar',
  //       'Gunakan penutup transparan',
  //       'Monitor suhu setiap 30 menit'
  //     ],
  //     border: 'border-yellow-400'
  //   },
  //   hot: {
  //     icon: '🔥',
  //     title: `Suhu Terlalu Panas (${tempValue.toFixed(1)}°C)`,
  //     tips: [
  //       'Kurangi pencahayaan langsung',
  //       'Tambahkan sistem pendingin atau kipas',
  //       'Alirkan air segar perlahan',
  //       'Pastikan sirkulasi tidak stagnan',
  //       'Aerasi tambahan untuk ikan'
  //     ],
  //     border: 'border-red-500'
  //   }
  // };

  const { icon, title, tips, border } = config[condition];

  // Buat modal dengan Tailwind
  const noteBox = document.createElement('div');
  noteBox.className = `suhu-action-note fixed inset-0 flex items-center justify-center z-50`;
  noteBox.innerHTML = `
    <div class="relative w-[90%] max-w-md bg-white rounded-xl shadow-xl border-t-4 ${border} animate-scale-in">
      <div class="flex items-center gap-3 p-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
        <span class="text-2xl">${icon}</span>
        <h3 class="text-lg font-semibold text-gray-800">${title}</h3>
      </div>
      <div class="p-5 text-sm text-gray-700">
        <ol class="list-decimal list-inside space-y-2">
          ${tips.map(t => `<li>${t}</li>`).join('')}
        </ol>
      </div>
      <div class="flex justify-end gap-2 px-4 py-3 border-t border-gray-100 bg-gray-50 rounded-b-xl">
        <button onclick="
          this.closest('.suhu-action-note').remove();
          isTempNoteShown = false;
        " class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-md transition">Tutup</button>
      </div>
    </div>
  `;

  // Tambahkan animasi Tailwind jika belum ada (opsional)
  if (!document.getElementById('tailwind-animation')) {
    const animStyle = document.createElement('style');
    animStyle.id = 'tailwind-animation';
    animStyle.textContent = `
      @keyframes scale-in {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
      }
      .animate-scale-in {
        animation: scale-in 0.2s ease-out;
      }
    `;
    document.head.appendChild(animStyle);
  }

  document.body.appendChild(noteBox);
}


}

        // Ambil data riwayat untuk chart dengan penanganan error
        async function fetchHistory() {
            const loader = document.getElementById('chart-loader');
            const errorElement = document.getElementById('chart-error');

            loader.style.display = 'block';
            errorElement.classList.add('hidden');

            try {
                const res = await fetch(`/api/sensor/history?sensor_id=${selectedSensorId}`);
                if (!res.ok) throw new Error('Response not OK');
                return await res.json();
            } catch (error) {
                console.error('Error fetching history data:', error);
                return null;
            } finally {
                loader.style.display = 'none';
            }
        }

        // Update grafik riwayat
        async function updateHistoryCharts() {
            const history = await fetchHistory();

            const chartError = document.getElementById('chart-error');

            // Distinguish between network/error (history === null) and empty-but-valid data ([])
            if (history === null) {
                // Network or server error while fetching
                if (chartError) {
                    chartError.classList.remove('hidden');
                    // Make it look like an error
                    chartError.classList.remove('text-gray-600');
                    chartError.classList.add('text-red-500');
                    chartError.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i><span>Gagal mengambil data history. Periksa koneksi atau server.</span>';
                }
                // Clear charts to avoid stale data
                if (phChart) {
                    phChart.data.labels = [];
                    phChart.data.datasets[0].data = [];
                    phChart.update();
                }
                if (suhuChart) {
                    suhuChart.data.labels = [];
                    suhuChart.data.datasets[0].data = [];
                    suhuChart.update();
                }
                if (tdsChart) {
                    tdsChart.data.labels = [];
                    tdsChart.data.datasets[0].data = [];
                    tdsChart.update();
                }
                return;
            }

            if (history.length === 0) {
                // Valid response but no entries for this sensor
                if (chartError) {
                    chartError.classList.remove('hidden');
                    // Informational style (not error)
                    chartError.classList.remove('text-red-500');
                    chartError.classList.add('text-gray-600');
                    chartError.innerHTML = '<i class="fas fa-info-circle mr-2"></i><span>Tidak ada data history untuk sensor ' + selectedSensorId + '.</span>';
                }
                // Clear charts
                if (phChart) {
                    phChart.data.labels = [];
                    phChart.data.datasets[0].data = [];
                    phChart.update();
                }
                if (suhuChart) {
                    suhuChart.data.labels = [];
                    suhuChart.data.datasets[0].data = [];
                    suhuChart.update();
                }
                if (tdsChart) {
                    tdsChart.data.labels = [];
                    tdsChart.data.datasets[0].data = [];
                    tdsChart.update();
                }
                return;
            }

            if (chartError) chartError.classList.add('hidden');

            const labels = history.map(d => formatTime(d.created_at));

            if (phChart) {
                phChart.data.labels = labels;
                phChart.data.datasets[0].data = history.map(d => d.ph);
                phChart.update();
            }

            if (suhuChart) {
                suhuChart.data.labels = labels;
                suhuChart.data.datasets[0].data = history.map(d => d.suhu);
                suhuChart.update();
            }

            if (tdsChart) {
                tdsChart.data.labels = labels;
                tdsChart.data.datasets[0].data = history.map(d => d.tds);
                tdsChart.update();
            }
        }

        // Inisialisasi Chart.js dengan opsi yang lebih baik
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            tension: 0.3,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#0f766e',
                    bodyColor: '#334155',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                point: {
                    radius: 0,
                    hoverRadius: 6
                }
            }
        };

        // Inisialisasi chart untuk pH
        const phCtx = document.getElementById('phChart').getContext('2d');
        const phChart = new Chart(phCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'pH',
                    data: [],
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderColor: 'rgba(14, 165, 233, 1)',
                    borderWidth: 3,
                    fill: true,
                    pointBackgroundColor: 'rgba(14, 165, 233, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(14, 165, 233, 1)'
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Tingkat pH Tanah',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            bottom: 16
                        }
                    }
                }
            }
        });

        // Inisialisasi chart untuk suhu
        const suhuCtx = document.getElementById('suhuChart').getContext('2d');
        const suhuChart = new Chart(suhuCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Suhu (°C)',
                    data: [],
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 3,
                    fill: true,
                    pointBackgroundColor: 'rgba(249, 115, 22, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(249, 115, 22, 1)'
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Suhu Tanah (°C)',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            bottom: 16
                        }
                    }
                }
            }
        });

        // Inisialisasi chart untuk TDS
        const tdsCtx = document.getElementById('tdsChart').getContext('2d');
        const tdsChart = new Chart(tdsCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'TDS (ppm)',
                    data: [],
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 3,
                    fill: true,
                    pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(139, 92, 246, 1)'
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Tingkat Kelembapan (%)',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            bottom: 16
                        }
                    }
                }
            }
        });

    // Populate history charts once charts are initialized
    updateHistoryCharts();

        // Function to show a small toast notification
        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: ${isError ? '#ef4444' : '#10b981'};
                color: white;
                padding: 10px 16px;
                border-radius: 8px;
                box-shadow: 0 6px 18px rgba(0,0,0,0.12);
                z-index: 1100;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Save current realtime snapshot to history API
        async function saveSnapshot() {
            // Prefer fetching the latest realtime data to ensure accuracy
            const data = await fetchRealtimeData();
            if (!data) {
                showToast('Tidak ada data realtime untuk disimpan', true);
                return;
            }

            const payload = {
                sensor_id: selectedSensorId,
                ph: data.ph != null ? parseFloat(data.ph) : null,
                suhu: data.suhu != null ? parseFloat(data.suhu) : null,
                tds: data.tds != null ? parseFloat(data.tds) : null,
                status_pump_ph: data.status_pump_ph != null ? Boolean(data.status_pump_ph) : false,
                status_pump_ppm: data.status_pump_ppm != null ? Boolean(data.status_pump_ppm) : false
            };

            try {
                const res = await fetch('/api/sensor/history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    const text = await res.text();
                    console.error('Save snapshot error:', res.status, text);
                    showToast('Gagal menyimpan snapshot', true);
                    return;
                }

                showToast('Snapshot disimpan');
                // Refresh history charts to show the new snapshot
                updateHistoryCharts();
            } catch (err) {
                console.error('Network error while saving snapshot', err);
                showToast('Gagal menyimpan snapshot', true);
            }
        }

        // // Update data realtime setiap 10 menit (600000 ms)
        // setInterval(() => {
        //     updateRealtimeSection();
        // }, 600000);

        // Refresh tiap 5 detik
        setInterval(updateRealtimeSection, 5000);
        updateRealtimeSection();

        // // Update data log/history setiap 1 hari (86400000 ms)
        // setInterval(() => {
        //     updateHistoryCharts();
        // }, 86400000);

        // Update data log/history setiap 1 hari (86400000 ms)
        setInterval(() => {
            updateHistoryCharts();
        }, 10000);
    </script>
</body>
@endsection

