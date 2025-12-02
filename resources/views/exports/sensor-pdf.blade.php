<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4472C4;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
        }
        td {
            padding: 6px;
            text-align: center;
            border: 1px solid #000;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
        @if($sensorType !== 'all')
            <p>Sensor: {{ strtoupper($sensorType) }}</p>
        @endif
    </div>

    @if($dataType === 'realtime')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Parameter</th>
                    <th>Sensor #</th>
                    <th>Nilai</th>
                    <th>Pompa pH</th>
                    <th>Pompa PPM</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ strtoupper($item->parameter) }}</td>
                        <td>{{ $item->sensor_no }}</td>
                        <td>{{ number_format($item->value, 2) }}</td>
                        <td>{{ $item->parameter === 'ph' && $item->status_pump_ph ? 'ON' : 'OFF' }}</td>
                        <td>{{ $item->parameter === 'tds' && $item->status_pump_ppm ? 'ON' : 'OFF' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Parameter</th>
                    <th>Sensor #</th>
                    <th>Avg</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Pompa pH</th>
                    <th>Pompa PPM</th>
                    <th>Total Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->log_date)->format('d/m/Y') }}</td>
                        <td>{{ strtoupper($item->parameter) }}</td>
                        <td>{{ $item->sensor_no }}</td>
                        <td>{{ number_format($item->avg_value, 2) }}</td>
                        <td>{{ number_format($item->min_value, 2) }}</td>
                        <td>{{ number_format($item->max_value, 2) }}</td>
                        <td>{{ $item->pump_ph_activations }}x</td>
                        <td>{{ $item->pump_ppm_activations }}x</td>
                        <td>{{ $item->total_records }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistem Monitoring Aquaponik</p>
    </div>
</body>
</html>
