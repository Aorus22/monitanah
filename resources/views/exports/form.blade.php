<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data Sensor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .export-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        @media (min-width: 768px) {
            body {
                padding: 40px 0;
            }
            .export-card {
                padding: 40px;
            }
        }
        .export-header {
            text-align: center;
            margin-bottom: 30px;
        }
        @media (min-width: 768px) {
            .export-header {
                margin-bottom: 40px;
            }
        }
        .export-header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        @media (min-width: 768px) {
            .export-header h1 {
                font-size: 2rem;
            }
        }
        .export-header p {
            color: #666;
            font-size: 0.9rem;
        }
        @media (min-width: 768px) {
            .export-header p {
                font-size: 1rem;
            }
        }
        .btn-export {
            padding: 12px 20px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        @media (min-width: 768px) {
            .btn-export {
                padding: 12px 30px;
            }
        }
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        @media (min-width: 768px) {
            .form-label {
                font-size: 1rem;
            }
        }
        .icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin-bottom: 20px;
        }
        @media (min-width: 768px) {
            .icon-box {
                width: 80px;
                height: 80px;
            }
        }
        .icon-box i {
            font-size: 30px;
            color: white;
        }
        @media (min-width: 768px) {
            .icon-box i {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container px-3 px-md-4">
        <div class="export-card">
            <div class="export-header">
                <div class="icon-box mx-auto">
                    <i class="bi bi-file-earmark-arrow-down"></i>
                </div>
                <h1>Export Data Sensor</h1>
                <p>Pilih filter dan format untuk mengekspor data monitoring sensor aquaponik</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="exportForm">
                <div class="row mb-3 mb-md-4">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <label class="form-label">Tipe Data</label>
                        <select name="data_type" id="data_type" class="form-select" required>
                            <option value="realtime">Data Real-time (10 menit)</option>
                            <option value="daily">Data Log Harian</option>
                        </select>
                        <small class="text-muted">Real-time: Data per 10 menit | Harian: Agregasi per hari</small>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Filter Sensor</label>
                        <select name="sensor_type" id="sensor_type" class="form-select">
                            <option value="all">Semua Sensor</option>
                            <option value="ph">pH saja</option>
                            <option value="suhu">Suhu saja</option>
                            <option value="tds">TDS saja</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3 mb-md-4">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Tips:</strong> Untuk export bulanan, pilih tanggal 1 hingga akhir bulan. Untuk export per sensor, pilih sensor spesifik di filter.
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-3 mt-md-4">
                    <button type="button" class="btn btn-danger btn-export" onclick="exportData('pdf')">
                        <i class="bi bi-file-pdf me-2"></i>Export ke PDF
                    </button>
                    <button type="button" class="btn btn-success btn-export" onclick="exportData('excel')">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export ke Excel
                    </button>
                </div>

                <div class="text-center mt-3 mt-md-4">
                    <a href="{{ route('home') }}" class="btn btn-link">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportData(format) {
            const form = document.getElementById('exportForm');
            const formData = new FormData(form);

            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Build URL with query parameters
            const params = new URLSearchParams(formData);
            let url = '';

            if (format === 'pdf') {
                url = '{{ route("sensor.export.pdf") }}?' + params.toString();
            } else {
                url = '{{ route("sensor.export.excel") }}?' + params.toString();
            }

            // Open in new window to download
            window.location.href = url;
        }

        // Set max date to today
        document.getElementById('end_date').max = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
