@extends('layouts')
@section('content')
    <title>Sistem Monitoring Tanah</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="sse-url" content="{{ config('app.sse_url', env('SSE_URL', 'http://localhost:8081')) }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome -->
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
        .card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .loader { border: 4px solid rgba(255,255,255,0.3); border-radius: 50%; border-top: 4px solid #0f766e; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
        .tab-button { padding: 0.6rem 1rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.2s ease; border: 1px solid transparent; }
        .tab-active { background: linear-gradient(135deg, #0f766e, #14b8a6); color: #fff; box-shadow: 0 10px 25px rgba(15,118,110,0.25); border-color: #0f766e; }
        .tab-inactive { background: #e2e8f0; color: #334155; }
        .chart-box canvas { display: block; width: 100% !important; max-width: 100%; height: 220px !important; }
        .chart-box.summary canvas { height: 220px !important; }
    </style>

<body class="bg-gray-50 text-gray-800 min-h-screen px-4 py-6 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                <i class="fas fa-chart-line text-emerald-600"></i> Dashboard Monitoring
            </h1>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="button" id="openExportModal" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-download"></i><span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6">
            <button id="tab-btn-realtime" class="tab-button tab-active">Realtime & Grafik</button>
            <button id="tab-btn-batch" class="tab-button tab-inactive">Batch (per Sensor)</button>
            <button id="tab-btn-summary" class="tab-button tab-inactive">Semua Sensor</button>
        </div>

        <!-- Tab: Individual -->
        <div id="tab-realtime-section">
            @include('monitoring.individual')
        </div>

        <!-- Tab: Batch -->
        <div id="tab-batch-section" style="display:none;">
            @include('monitoring.batch')
        </div>

        <!-- Tab: All Sensors -->
        <div id="tab-summary-section" style="display:none;">
            @include('monitoring.all')
        </div>

        @include('monitoring.modal-download')

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

    @include('monitoring.scripts')
</body>
@endsection
