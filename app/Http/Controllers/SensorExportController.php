<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SensorExportController extends Controller
{
    /**
     * Export sensor data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'sensor_type' => 'nullable|in:ph,suhu,tds,all',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'data_type' => 'required|in:realtime,daily',
        ]);

        $sensorType = $request->input('sensor_type', 'all');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $dataType = $request->input('data_type');

        if ($dataType === 'realtime') {
            $data = $this->getRealtimeData($startDate, $endDate);
            $title = 'Laporan Data Sensor Real-time';
        } else {
            $data = $this->getDailyData($startDate, $endDate);
            $title = 'Laporan Data Sensor Harian';
        }

        $pdf = Pdf::loadView('exports.sensor-pdf', [
            'data' => $data,
            'sensorType' => $sensorType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dataType' => $dataType,
            'title' => $title,
        ]);

        $filename = 'sensor-report-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export sensor data to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'sensor_type' => 'nullable|in:ph,suhu,tds,all',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'data_type' => 'required|in:realtime,daily',
        ]);

        $sensorType = $request->input('sensor_type', 'all');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $dataType = $request->input('data_type');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $title = $dataType === 'realtime' ? 'Laporan Data Sensor Real-time' : 'Laporan Data Sensor Harian';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set date range
        $sheet->setCellValue('A2', 'Periode: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($dataType === 'realtime') {
            $this->fillRealtimeExcel($sheet, $startDate, $endDate, $sensorType);
        } else {
            $this->fillDailyExcel($sheet, $startDate, $endDate, $sensorType);
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'sensor-report-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx';

        // Create writer and download
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Get realtime sensor data
     * DATA YANG SAMA DENGAN GRAFIK - Langsung dari sensor_histories!
     */
    private function getRealtimeData($startDate, $endDate)
    {
        return SensorHistory::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get daily aggregated data
     * Agregasi langsung dari sensor_histories per hari
     */
    private function getDailyData($startDate, $endDate)
    {
        return SensorHistory::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as log_date')
            ->selectRaw('ROUND(AVG(ph), 2) as avg_ph')
            ->selectRaw('ROUND(MIN(ph), 2) as min_ph')
            ->selectRaw('ROUND(MAX(ph), 2) as max_ph')
            ->selectRaw('ROUND(AVG(suhu), 2) as avg_suhu')
            ->selectRaw('ROUND(MIN(suhu), 2) as min_suhu')
            ->selectRaw('ROUND(MAX(suhu), 2) as max_suhu')
            ->selectRaw('ROUND(AVG(tds), 2) as avg_tds')
            ->selectRaw('ROUND(MIN(tds), 2) as min_tds')
            ->selectRaw('ROUND(MAX(tds), 2) as max_tds')
            ->selectRaw('SUM(CASE WHEN status_pump_ph = 1 THEN 1 ELSE 0 END) as pump_ph_activations')
            ->selectRaw('SUM(CASE WHEN status_pump_ppm = 1 THEN 1 ELSE 0 END) as pump_ppm_activations')
            ->selectRaw('COUNT(*) as total_records')
            ->groupBy('log_date')
            ->orderBy('log_date', 'asc')
            ->get();
    }

    /**
     * Fill Excel with realtime data
     */
    private function fillRealtimeExcel($sheet, $startDate, $endDate, $sensorType)
    {
        $data = $this->getRealtimeData($startDate, $endDate);

        // Header row
        $row = 4;
        $headers = ['No', 'Tanggal & Waktu', 'pH', 'Suhu (°C)', 'TDS (ppm)', 'Pompa pH', 'Pompa PPM'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        // Style header
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A4:G4')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data rows
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->created_at->format('d/m/Y H:i:s'));

            if ($sensorType === 'all' || $sensorType === 'ph') {
                $sheet->setCellValue('C' . $row, $item->ph);
            }
            if ($sensorType === 'all' || $sensorType === 'suhu') {
                $sheet->setCellValue('D' . $row, $item->suhu);
            }
            if ($sensorType === 'all' || $sensorType === 'tds') {
                $sheet->setCellValue('E' . $row, $item->tds);
            }
            if ($sensorType === 'all') {
                $sheet->setCellValue('F' . $row, $item->status_pump_ph ? 'ON' : 'OFF');
                $sheet->setCellValue('G' . $row, $item->status_pump_ppm ? 'ON' : 'OFF');
            }

            $row++;
        }

        // Add borders
        $lastRow = $row - 1;
        $sheet->getStyle('A4:G' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

    /**
     * Fill Excel with daily data
     */
    private function fillDailyExcel($sheet, $startDate, $endDate, $sensorType)
    {
        $data = $this->getDailyData($startDate, $endDate);

        // Header row
        $row = 4;
        $headers = ['No', 'Tanggal', 'Avg pH', 'Min pH', 'Max pH', 'Avg Suhu', 'Min Suhu', 'Max Suhu', 'Avg TDS', 'Min TDS', 'Max TDS', 'Pompa pH', 'Pompa PPM', 'Total Data'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        // Style header
        $sheet->getStyle('A4:N4')->getFont()->setBold(true);
        $sheet->getStyle('A4:N4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A4:N4')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:N4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data rows
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, Carbon::parse($item->log_date)->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $item->avg_ph);
            $sheet->setCellValue('D' . $row, $item->min_ph);
            $sheet->setCellValue('E' . $row, $item->max_ph);
            $sheet->setCellValue('F' . $row, $item->avg_suhu);
            $sheet->setCellValue('G' . $row, $item->min_suhu);
            $sheet->setCellValue('H' . $row, $item->max_suhu);
            $sheet->setCellValue('I' . $row, $item->avg_tds);
            $sheet->setCellValue('J' . $row, $item->min_tds);
            $sheet->setCellValue('K' . $row, $item->max_tds);
            $sheet->setCellValue('L' . $row, $item->pump_ph_activations . 'x');
            $sheet->setCellValue('M' . $row, $item->pump_ppm_activations . 'x');
            $sheet->setCellValue('N' . $row, $item->total_records);

            $row++;
        }

        // Add borders
        $lastRow = $row - 1;
        $sheet->getStyle('A4:N' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Auto-size additional columns
        foreach (range('I', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Show export form - NOT USED ANYMORE
     * Export buttons are now directly in the dashboard
     */
    // public function showExportForm()
    // {
    //     return view('exports.form');
    // }
}
