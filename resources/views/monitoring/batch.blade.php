<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-10 max-w-5xl mx-auto">
    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 card col-span-3">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-layer-group text-emerald-600"></i> Batch Realtime (per Sensor)
            </h2>
            <div class="flex items-center gap-3">
                <button id="saveBatchSnapshotBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-3 rounded-lg shadow transition-all duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan Snapshot Batch
                </button>
                <div id="batch-loader" class="loader" style="display:none;"></div>
            </div>
        </div>
        <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
            <label for="batchSensorDropdown" class="text-gray-700 font-medium text-sm sm:text-base">Sensor #:</label>
            <select id="batchSensorDropdown" class="w-full sm:w-auto border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
        </div>

        <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
            <div id="batch-ph-card" class="p-6 sm:p-5 bg-emerald-50 rounded-xl flex flex-col items-center w-full sm:w-2/3 md:w-1/2 lg:w-1/3 shadow">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">pH</span>
                <div id="batch-ph" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 6.0 - 7.5</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="batch-ph-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="batch-ph-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div id="batch-suhu-card" class="p-6 sm:p-5 bg-orange-50 rounded-xl flex flex-col items-center w-full sm:w-2/3 md:w-1/2 lg:w-1/3 shadow">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Suhu Tanah</span>
                <div id="batch-suhu" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 22°C - 30°C</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="batch-suhu-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="batch-suhu-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>

            <div id="batch-tds-card" class="p-6 sm:p-5 bg-amber-50 rounded-xl flex flex-col items-center w-full sm:w-2/3 md:w-1/2 lg:w-1/3 shadow">
                <span class="font-medium text-gray-600 mb-1 text-sm sm:text-base">Kelembapan Tanah</span>
                <div id="batch-tds" class="text-3xl sm:text-2xl font-bold">-</div>
                <div class="text-xs text-gray-500 mt-1">Optimal: 20% - 80%</div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
                    <div id="batch-tds-bar" class="h-full w-0 transition-all duration-500"></div>
                </div>
                <div id="batch-tds-status" class="text-xs font-medium mt-1 text-gray-600 italic">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Batch History Charts -->
<div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 card mb-10 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 sm:mb-6">
        <h2 class="text-lg sm:text-xl font-bold text-emerald-800 flex items-center gap-2">
            <i class="fas fa-chart-line text-emerald-600"></i> Grafik History (Batch)
        </h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-2 sm:p-3 chart-box">
            <canvas id="batchPhChart" height="220"></canvas>
        </div>
        <div class="p-2 sm:p-3 chart-box">
            <canvas id="batchSuhuChart" height="220"></canvas>
        </div>
        <div class="md:col-span-2 p-2 sm:p-3 chart-box">
            <canvas id="batchTdsChart" height="220"></canvas>
        </div>
    </div>
</div>
