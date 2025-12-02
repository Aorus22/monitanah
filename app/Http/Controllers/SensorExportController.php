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
            $data = $this->getRealtimeData($startDate, $endDate, $sensorType);
            $title = 'Laporan Data Sensor Real-time';
        } else {
            $data = $this->getDailyData($startDate, $endDate, $sensorType);
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
        // mulai dengan sheet kosong
        $spreadsheet->removeSheetByIndex(0);

        if ($dataType === 'realtime') {
            $data = $this->getRealtimeData($startDate, $endDate, $sensorType);
            $grouped = $data->groupBy(function ($item) {
                return $item->parameter . '-' . $item->sensor_no;
            });
            $first = true;
            foreach ($grouped as $key => $items) {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle(substr('RT-' . strtoupper($key), 0, 31));
                $this->fillRealtimeExcel($sheet, $startDate, $endDate, $items);
                if ($first) {
                    $spreadsheet->setActiveSheetIndex($spreadsheet->getIndex($sheet));
                    $first = false;
                }
            }
        } else {
            $data = $this->getDailyData($startDate, $endDate, $sensorType);
            $grouped = $data->groupBy(function ($item) {
                return $item->parameter . '-' . $item->sensor_no;
            });
            $first = true;
            foreach ($grouped as $key => $items) {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle(substr('DL-' . strtoupper($key), 0, 31));
                $this->fillDailyExcel($sheet, $startDate, $endDate, $items);
                if ($first) {
                    $spreadsheet->setActiveSheetIndex($spreadsheet->getIndex($sheet));
                    $first = false;
                }
            }
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
    private function getRealtimeData($startDate, $endDate, $sensorType = 'all')
    {
        return SensorHistory::whereBetween('created_at', [$startDate, $endDate])
            ->when($sensorType !== 'all', function ($query) use ($sensorType) {
                $query->where('parameter', $sensorType);
            })
            ->select(['parameter', 'sensor_no', 'value', 'status_pump_ph', 'status_pump_ppm', 'created_at'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get daily aggregated data
     * Agregasi langsung dari sensor_histories per hari
     */
    private function getDailyData($startDate, $endDate, $sensorType = 'all')
    {
        return SensorHistory::whereBetween('created_at', [$startDate, $endDate])
            ->when($sensorType !== 'all', function ($query) use ($sensorType) {
                $query->where('parameter', $sensorType);
            })
            ->selectRaw('DATE(`created_at`) as log_date')
            ->selectRaw('parameter')
            ->selectRaw('sensor_no')
            ->selectRaw('ROUND(AVG(value), 2) as avg_value')
            ->selectRaw('ROUND(MIN(value), 2) as min_value')
            ->selectRaw('ROUND(MAX(value), 2) as max_value')
            ->selectRaw('SUM(CASE WHEN status_pump_ph = 1 THEN 1 ELSE 0 END) as pump_ph_activations')
            ->selectRaw('SUM(CASE WHEN status_pump_ppm = 1 THEN 1 ELSE 0 END) as pump_ppm_activations')
            ->selectRaw('COUNT(*) as total_records')
            ->groupBy('log_date', 'parameter', 'sensor_no')
            ->orderBy('log_date', 'asc')
            ->orderBy('parameter')
            ->orderBy('sensor_no')
            ->get();
    }

    /**
     * Fill Excel with realtime data
     */
    private function fillRealtimeExcel($sheet, $startDate, $endDate, $items)
    {
        // Header row
        $row = 4;
        $headers = ['No', 'Tanggal & Waktu', 'Parameter', 'Sensor #', 'Nilai', 'Pompa pH', 'Pompa PPM'];
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
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->created_at->format('d/m/Y H:i:s'));
            $sheet->setCellValue('C' . $row, strtoupper($item->parameter));
            $sheet->setCellValue('D' . $row, $item->sensor_no);
            $sheet->setCellValue('E' . $row, $item->value);
            $sheet->setCellValue('F' . $row, $item->parameter === 'ph' && $item->status_pump_ph ? 'ON' : 'OFF');
            $sheet->setCellValue('G' . $row, $item->parameter === 'tds' && $item->status_pump_ppm ? 'ON' : 'OFF');

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

        // Auto-size columns for readability
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Make date column wider to prevent truncation
        $sheet->getColumnDimension('B')->setWidth(22);
    }

    /**
     * Fill Excel with daily data
     */
    private function fillDailyExcel($sheet, $startDate, $endDate, $items)
    {
        // Header row
        $row = 4;
        $headers = ['No', 'Tanggal', 'Parameter', 'Sensor #', 'Avg', 'Min', 'Max', 'Pompa pH', 'Pompa PPM', 'Total Data'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        // Style header
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);
        $sheet->getStyle('A4:J4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A4:J4')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data rows
        $row = 5;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, Carbon::parse($item->log_date)->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, strtoupper($item->parameter));
            $sheet->setCellValue('D' . $row, $item->sensor_no);
            $sheet->setCellValue('E' . $row, $item->avg_value);
            $sheet->setCellValue('F' . $row, $item->min_value);
            $sheet->setCellValue('G' . $row, $item->max_value);
            $sheet->setCellValue('H' . $row, $item->pump_ph_activations . 'x');
            $sheet->setCellValue('I' . $row, $item->pump_ppm_activations . 'x');
            $sheet->setCellValue('J' . $row, $item->total_records);

            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A4:J' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Auto-size columns for readability
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Make date column wider to prevent truncation
        $sheet->getColumnDimension('B')->setWidth(18);
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
