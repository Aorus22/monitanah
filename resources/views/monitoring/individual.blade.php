<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-10 max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 card col-span-3">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-sync-alt text-emerald-600"></i> Data Sensor Real-time
            </h2>
            <div class="flex items-center gap-3">
                <button id="saveSnapshotBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-3 rounded-lg shadow transition-all duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan Snapshot
                </button>
                <div id="realtime-loader" class="loader" style="display:none;"></div>
            </div>
        </div>
        <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
            <label for="parameterDropdown" class="text-gray-700 font-medium text-sm sm:text-base">Parameter:</label>
            <select id="parameterDropdown" class="w-full sm:w-auto border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="ph">pH</option>
                <option value="suhu">Suhu</option>
                <option value="tds">TDS</option>
            </select>
            <label for="sensorDropdown" class="text-gray-700 font-medium text-sm sm:text-base">Sensor #:</label>
            <select id="sensorDropdown" class="w-full sm:w-auto border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
        </div>

        <div id="sensorContainer" class="flex flex-wrap justify-center gap-3 sm:gap-4 max-w-5xl mx-auto">
            <div id="card-ph" class="p-6 sm:p-5 bg-emerald-50 rounded-xl flex flex-col items-center w-full sm:w-2/3 md:w-1/2 lg:w-2/5 shadow">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">pH</span>
                <div id="ph" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 6.0 - 7.5</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="ph-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="ph_status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div id="card-suhu" class="p-6 sm:p-5 bg-orange-50 rounded-xl flex flex-col items-center w-full sm:w-2/3 md:w-1/2 lg:w-2/5 shadow">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Suhu Tanah</span>
                <div id="suhu" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 22°C - 30°C</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="suhu-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="suhu-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div id="card-tds" class="p-6 sm:p-5 bg-amber-50 rounded-xl flex flex-col items-center w-full sm:w-2/3 md:w-1/2 lg:w-2/5 shadow">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Kelembapan Tanah</span>
                <div id="tds" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 20% - 80%</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="tds-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="tds-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div class="p-2 bg-gray-50 rounded-lg md:col-span-2 lg:col-span-1">
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

        <div class="last-updated text-sm text-gray-500 mt-4 text-right">
            <span id="last-updated">Terakhir diperbarui: -</span>
        </div>
    </div>
</div>

<!-- History Charts -->
<div id="history-card-container" class="bg-white shadow-lg rounded-xl p-4 sm:p-6 card mb-10 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 sm:mb-6">
        <h2 class="text-lg sm:text-xl font-bold text-emerald-800 flex items-center gap-2">
            <i class="fas fa-chart-line text-emerald-600"></i> Grafik History Sensor
        </h2>
        <div id="chart-loader" class="loader" style="display:none;"></div>
    </div>

    <div class="max-w-4xl mx-auto w-full px-0 sm:px-2 md:px-4">
        <div class="grid grid-cols-1 gap-4">
            <div id="phChartWrap" class="p-2 sm:p-3 chart-box">
                <canvas id="phChart" height="220"></canvas>
            </div>
            <div id="suhuChartWrap" class="p-2 sm:p-3 chart-box">
                <canvas id="suhuChart" height="220"></canvas>
            </div>
            <div id="tdsChartWrap" class="p-2 sm:p-3 chart-box">
                <canvas id="tdsChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <div id="chart-error" class="mt-4 text-red-500 text-sm hidden">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>Gagal mengambil data history. Mencoba lagi...</span>
    </div>
</div>
