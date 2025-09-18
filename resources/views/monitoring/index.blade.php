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

<body class="bg-gray-50 text-gray-800 min-h-screen p-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <header class="text-center py-8">
            <div class="flex items-center justify-center gap-3">
                <div>
                    <img src="/images/LOGO PT. JBG.png" alt="Logo PT. JBG" class="h-20 w-28 object-contain" />
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-emerald-800">Sistem Monitoring Tanah</h1>
            </div>
            <p class="mt-2 text-gray-600">Monitoring real-time kondisi tanah Anda</p>
        </header>
<!-- Realtime Sensor Data -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <!-- Box Sensor Data -->
    <div class="bg-white shadow-lg rounded-xl p-6 card col-span-3">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-sync-alt text-emerald-600"></i> Data Sensor Real-time
            </h2>
            <div id="realtime-loader" class="loader" style="display: none;"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- pH -->
            <div class="p-4 bg-emerald-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-600">pH Tanah</span>
                    <i class="fas fa-seeding text-blue-500"></i>
                </div>
                <div id="ph" class="text-3xl font-bold mt-2">-</div>
                <div class="text-sm text-gray-500 mt-1">Optimal: 6.3 - 7.5</div>

                <!-- Bar indikator pH -->
                <div class="mt-2">
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="ph-bar" class="h-full w-0 bg-gray-400 transition-all duration-500"></div>
                    </div>
                    <div id="ph-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
                </div>

            </div>

            <!-- Suhu -->
            <div class="p-4 bg-orange-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-600">Suhu Tanah</span>
                    <i class="fas fa-thermometer-half text-orange-500"></i>
                </div>
                <div id="suhu" class="text-3xl font-bold mt-2">-</div>
                <div class="text-sm text-gray-500 mt-1">Optimal: 22°C - 30°C</div>

                <!-- Bar indikator suhu -->
                <div class="mt-2">
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="suhu-bar" class="h-full w-0 transition-all duration-500"></div>
                    </div>
                    <div id="suhu-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
                </div>

                <!-- Peringatan jika suhu tidak optimal -->
    <!--<div id="suhu-note" class="mt-4 p-4 bg-red-100 border border-yellow-400 text-yellow-800 rounded-md hidden">-->
    <!--    <div class="font-semibold mb-1">-->
    <!--        ⚠️ Peringatan: Suhu Tidak Optimal!-->
    <!--    </div>-->
    <!--    <p class="text-sm mb-2">-->
    <!--        Suhu air saat ini berada di luar kisaran optimal (22°C – 28°C), yang dapat mempengaruhi:-->
    <!--    </p>-->
    <!--    <ul class="list-disc ml-5 text-sm mb-2">-->
    <!--        <li>Kesehatan dan metabolisme ikan (misalnya ikan nila atau lele).</li>-->
    <!--        <li>Efisiensi penyerapan nutrisi oleh tanaman.</li>-->
    <!--        <li>Performa mikroorganisme dalam sistem monitoring tanah.</li>-->
    <!--    </ul>-->
    <!--    <p class="text-sm font-medium mb-1">✅ <strong>Tindakan yang disarankan:</strong></p>-->
    <!--    <ul class="list-decimal ml-5 text-sm">-->
    <!--        <li>Jika suhu terlalu tinggi: tambahkan aerasi & tutup sistem dari sinar matahari langsung.</li>-->
    <!--        <li>Periksa sensor suhu dan pastikan berfungsi dengan baik.</li>-->
    <!--    </ul>-->
    <!--</div>-->

            </div>

            <!-- TDS -->
            <div class="p-4 bg-amber-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-600">Kelembapan Tanah</span>
                    <i class="fas fa-flask text-amber-500"></i>
                </div>
                <div id="tds" class="text-3xl font-bold mt-2">-</div>
                <div class="text-sm text-gray-500 mt-1">Optimal: 20% - 60&</div>

                <!-- Bar indikator tds -->
                <div class="mt-2">
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="tds-bar" class="h-full w-0 transition-all duration-500"></div>
                    </div>
                    <div id="tds-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
                </div>

                <!-- Peringatan jika TDS tidak optimal -->
    <div id="tds-note" class="mt-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-md hidden">
        <!-- Konten diisi oleh JavaScript -->
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
            <div class="p-4 bg-gray-50 rounded-lg md:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-600">ID Sensor</span>
                    <i class="fas fa-microchip text-gray-500"></i>
                </div>
                <div id="sensor_id" class="text-xl font-mono font-bold mt-2 text-gray-700">-</div>
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
        <div class="bg-white shadow-lg rounded-xl p-6 card mb-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-emerald-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-emerald-600"></i> Grafik History Sensor
                </h2>
                <div id="chart-loader" class="loader" style="display: none;"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <canvas id="phChart" height="200"></canvas>
                </div>
                <div>
                    <canvas id="suhuChart" height="200"></canvas>
                </div>
                <div class="md:col-span-2">
                    <canvas id="tdsChart" height="200"></canvas>
                </div>
            </div>

            <div id="chart-error" class="mt-4 text-red-500 text-sm hidden">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>Gagal mengambil data history. Mencoba lagi...</span>
            </div>
        </div>

        <div class="max-w-6xl mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Riwayat Semprotan Pompa pH</h2>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Waktu Semprot</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">pH Sebelum</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">pH Sesudah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Semprotan ke-</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($logs as $log)
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-800">{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ \Carbon\Carbon::parse($log->sprayed_at)->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ number_format($log->ph_before, 2) }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        @if ($log->ph_after !== null)
                            {{ number_format($log->ph_after, 2) }}
                        @else
                            <span class="text-gray-400 italic">Belum tersedia</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $log->spray_number }}</td>
                    <td class="px-4 py-2 text-sm">
                        @if ($log->ph_after !== null)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-md">
                                Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-md">
                                Menunggu After pH
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-400">
                        Belum ada riwayat semprotan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-6">
        {{ $logs->links('pagination::tailwind') }}
    </div>
</div>
<div class="max-w-6xl mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Riwayat Semprotan Pompa PPM</h2>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Waktu Semprot</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">PPM Sebelum</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">PPM Sesudah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Semprotan ke-</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($logsppm as $log)
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-800">{{ $loop->iteration + ($logsppm->currentPage() - 1) * $logs->perPage() }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ \Carbon\Carbon::parse($log->sprayed_at)->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ number_format($log->ppm_before, 0) }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        @if ($log->ppm_after !== null)
                            {{ number_format($log->ppm_after, 0) }}
                        @else
                            <span class="text-gray-400 italic">Belum tersedia</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $log->spray_number }}</td>
                    <td class="px-4 py-2 text-sm">
                        @if ($log->ppm_after !== null)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-md">Selesai</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-md">Menunggu</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-400">Belum ada riwayat semprotan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $logs->links('pagination::tailwind') }}
    </div>
</div>

        <!-- Footer -->
        <footer class="text-center py-6 text-gray-500 text-sm border-t">
            <p>© 2025 Sistem Monitoring Tanah | Dibuat oleh mahasiswa POLIBAN <i class="fas fa-heart text-red-500"></i> untuk pertanian modern</p>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>

let isNoteShown = false;
let isTempNoteShown = false;
let isTdsNoteShown = false;

let lastPhCondition = 'normal';
let lastTempCondition = 'normal'; // <-- ❗ Wajib tambahkan ini
let lastTdsCondition = 'normal';

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

// Ambil data realtime terbaru dari API dengan penanganan error
async function fetchRealtimeData() {
    const loader = document.getElementById('realtime-loader');
    const errorElement = document.getElementById('realtime-error');

    loader.style.display = 'block';
    errorElement.classList.add('hidden');

    try {
        const res = await fetch('api/sensor/realtime');
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

// Update tampilan realtime dari data
async function updateRealtimeSection() {
    const data = await fetchRealtimeData();
    if (!data) return;

    document.getElementById('sensor_id').textContent = data.id || '-';
    document.getElementById('ph').textContent = data.ph || '-';
    document.getElementById('suhu').textContent = data.suhu ? `${data.suhu}°C` : '-';
    document.getElementById('tds').textContent = data.tds || '-';


    //Display Ph
    const phVal = parseFloat(data.ph);
    const phDisplay = document.getElementById('ph');
    const phBar = document.getElementById('ph-bar');
    const phStatus = document.getElementById('ph-status');

    if (!isNaN(phVal)) {
    phDisplay.textContent = phVal.toFixed(2);

    phDisplay.classList.remove('text-green-600', 'text-yellow-500', 'text-red-700');

        if (phVal < 6.3) {
            phDisplay.classList.add('text-yellow-500');
            phBar.style.width = '33%';
            phBar.style.backgroundColor = '#facc15';
            phStatus.textContent = 'Terlalu rendah';

            if (lastPhCondition !== 'low') {
                showActionGuide(phVal, 'low');
                lastPhCondition = 'low';
            }

        } else if (phVal <= 7.5) {
            phDisplay.classList.add('text-green-600');
            phBar.style.width = '66%';
            phBar.style.backgroundColor = '#0ee697';
            phStatus.textContent = 'Optimal';

            // ✅ Reset kondisi
            lastPhCondition = 'normal';
            isNoteShown = false;

        } else {
            phDisplay.classList.add('text-red-700');
            phBar.style.width = '100%';
            phBar.style.backgroundColor = '#de2121';
            phStatus.textContent = 'Terlalu tinggi';

            if (lastPhCondition !== 'high') {
                showActionGuide(phVal, 'high');
                lastPhCondition = 'high';
            }
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
    document.body.appendChild(noteBox);
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

    if (tdsVal < 600) {
        tdsDisplay.style.color = '#facc15'; // Kuning
        tdsBar.style.width = '33%';
        tdsBar.style.backgroundColor = '#facc15';
        tdsStatus.textContent = 'Terlalu rendah';

        if (lastTdsCondition !== 'low') {
            showTdsActionGuide(tdsVal, 'low');
            lastTdsCondition = 'low';
        }

    } else if (tdsVal <= 1200) {
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



// --- Display Suhu ---
const suhuVal     = parseFloat(data.suhu);
const suhuDisplay = document.getElementById('suhu');
const suhuBar     = document.getElementById('suhu-bar');
const suhuStatus  = document.getElementById('suhu-status');

if (!isNaN(suhuVal)) {
  suhuDisplay.textContent = `${suhuVal.toFixed(1)}°C`;
  suhuDisplay.style.color = '';

  if (suhuVal < 22) {
    suhuBar.style.width           = '33%';
    suhuBar.style.backgroundColor = '#facc15';
    suhuDisplay.style.color       = '#f59e0b';
    suhuStatus.textContent        = 'Terlalu dingin';

    if (lastTempCondition !== 'cold') {
      showTempActionGuide(suhuVal, 'cold');
      lastTempCondition = 'cold';
    }

  } else if (suhuVal <= 30) {
    suhuBar.style.width           = '66%';
    suhuBar.style.backgroundColor = '#34d399';
    suhuDisplay.style.color       = '#16a34a';
    suhuStatus.textContent        = 'Optimal';

    lastTempCondition = 'normal';
    isTempNoteShown   = false;

  } else {
    suhuBar.style.width           = '100%';
    suhuBar.style.backgroundColor = '#de2121';
    suhuDisplay.style.color       = '#dc2626';
    suhuStatus.textContent        = 'Terlalu panas';

    if (lastTempCondition !== 'hot') {
      showTempActionGuide(suhuVal, 'hot');
      lastTempCondition = 'hot';
    }
  }
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
                const res = await fetch('/api/sensor/history');
                if (!res.ok) throw new Error('Response not OK');
                return await res.json();
            } catch (error) {
                console.error('Error fetching history data:', error);
                errorElement.classList.remove('hidden');
                return [];
            } finally {
                loader.style.display = 'none';
            }
        }

        // Update grafik riwayat
        async function updateHistoryCharts() {
            const history = await fetchHistory();
            if (history.length === 0) return;

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
                        text: 'Suhu Air (°C)',
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
                        text: 'Tingkat TDS (ppm)',
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

        // Update data setiap 5 detik
        setInterval(() => {
            updateRealtimeSection();
            updateHistoryCharts();
        }, 5000);

        // Panggilan awal saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            updateRealtimeSection();
            updateHistoryCharts();
        });
    </script>
</body>
@endsection

