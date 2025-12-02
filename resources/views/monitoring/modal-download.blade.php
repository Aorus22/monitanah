<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-11/12 max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-download text-emerald-600"></i> Export Data Sensor
            </h3>
            <button id="closeExportModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Mulai</label>
                <input type="date" id="exportStart" class="border rounded w-full px-3 py-2 text-sm" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Selesai</label>
                <input type="date" id="exportEnd" class="border rounded w-full px-3 py-2 text-sm" value="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Tipe Data</label>
                <select id="exportDataType" class="border rounded w-full px-3 py-2 text-sm">
                    <option value="realtime">Realtime</option>
                    <option value="daily">Daily</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Parameter</label>
                <select id="exportSensorType" class="border rounded w-full px-3 py-2 text-sm">
                    <option value="all">Semua</option>
                    <option value="ph">pH</option>
                    <option value="suhu">Suhu</option>
                    <option value="tds">TDS</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button id="exportPdfBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-3 rounded-lg shadow transition-all duration-200 flex items-center gap-1">
                <i class="fas fa-file-pdf"></i><span>PDF</span>
            </button>
            <button id="exportExcelBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-3 rounded-lg shadow transition-all duration-200 flex items-center gap-1">
                <i class="fas fa-file-excel"></i><span>Excel</span>
            </button>
        </div>
    </div>
</div>
