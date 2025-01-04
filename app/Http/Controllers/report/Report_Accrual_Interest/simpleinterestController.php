<?php

namespace App\Http\Controllers\report\Report_Accrual_Interest;

use App\Models\report_simpleinterest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

use Dompdf\Dompdf;
use Dompdf\Options;

class simpleinterestController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
        // Ambil jumlah item per halaman dari query string, default 10
        $perPage = $request->input('per_page', 10);
        // Ambil data dengan pagination
        $loans = report_simpleinterest::fetchAll($id_pt, $perPage);
        // dd($loans);
        return view('report.accrual_interest.simple_interest.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt)
    {
        // dd($no_acc, $id_pt); // Debugging parameterrr
        $no_acc = trim($no_acc);
        $loan = report_simpleinterest::getLoanDetails($no_acc, $id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc, $id_pt);
        // dd($loan, $reports);
        if (!$loan) {
            abort(404, 'Loan not found');
        }

        return view('report.accrual_interest.simple_interest.view', compact('loan', 'reports'));

    }

    public function exportExcel($no_acc, $id_pt)
    {
        // Ambil data loan dan reports
        $loan = report_simpleinterest::getLoanDetails(trim($no_acc), trim($id_pt));
        $reports = report_simpleinterest::getReportsByNoAcc(trim($no_acc), trim($id_pt));

        // Cek apakah data loan dan reports ada
        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->setCellValue('A3', 'Account Number');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->setCellValue('B3', ':');
        $sheet->setCellValue('C3', $loan->no_acc);
        $sheet->getStyle('A3')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A4', 'Debitor Name');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->setCellValue('B4', ':');
        $sheet->setCellValue('C4', $loan->deb_name);
        $sheet->getStyle('A4')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A5', 'Original Amount');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->setCellValue('B5', ':');
        $sheet->setCellValue('C5', $loan->org_bal);
        $sheet->getStyle('A5')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A6', 'Term');
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->setCellValue('B6', ':');
        $sheet->setCellValue('C6', $loan->TERM . ' Months');
        $sheet->getStyle('A6')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A7', 'Interest Rate');
        $sheet->getStyle('B7')->getFont()->setBold(true); // Set bol untuk  Interest Rate
        $sheet->setCellValue('D3', 'Outstanding Amount');
        $sheet->getStyle('D3')->getFont()->setBold(true); // Set bold untuk  Outstanding Amount
        $sheet->setCellValue('E3', $loan->no_acc);
        $sheet->setCellValue('D4', 'EIR Conversion Calculated');
        $sheet->getStyle('D4')->getFont()->setBold(true); // Set bold untuk EIR Conversion Calculated
        $sheet->setCellValue('E4', $loan->deb_name);
        $sheet->setCellValue('D5', 'Original Loan Date');
        $sheet->getStyle('D5')->getFont()->setBold(true); // Set bold untuk Original Loan Date
        $sheet->setCellValue('E5', $loan->org_bal);
        $sheet->setCellValue('D6', 'Maturity Date');
        $sheet->getStyle('E6')->getFont()->setBold(true); // Set bold untuk Maturity Date

        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Accrual Interest Report - Report Details');
        $sheet->mergeCells('A10:J10'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Set judul kolom tabel
        $headers = ['Bulanke', 'Tgl Angsuran', 'Hari Bunga', 'PMT Amt', 'Penarikan', 'Pengembalian', 'Bunga', 'Balance', 'Time Gap', 'Outs Amt Conv'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '12', $header);
            $sheet->getStyle($columnIndex . '12')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '12')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '12')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '12')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 13; // Mulai dari baris 13 untuk data laporan
        foreach ($reports as $report) {
            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->setCellValue('B' . $row, date('Y-m-d', strtotime($report->tglangsuran)));
            $sheet->setCellValue('C' . $row, $report->haribunga);
            $sheet->setCellValue('D' . $row, $report->pmtamt);
            $sheet->setCellValue('E' . $row, $report->penarikan);
            $sheet->setCellValue('F' . $row, $report->pengembalian);
            $sheet->setCellValue('G' . $row, $report->bunga);
            $sheet->setCellValue('H' . $row, $report->balance);
            $sheet->setCellValue('I' . $row, $report->timegap);
            $sheet->setCellValue('J' . $row, $report->outsamtconv);

            // Mengatur font menjadi bold untuk setiap baris data
            $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('D' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        // Set border untuk header tabel
        $sheet->getStyle('A12:J12')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A13:J' . ($row - 1))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        

        // Siapkan nama file
        $filename = "accrual_interest_report_$no_acc.xlsx";

        // Buat writer dan simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        // Kembalikan response Excel
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    // Method untuk mengekspor data ke PDF
    public function exportPdf($no_acc, $id_pt)
    {
        // Ambil data loan dan reports
        $loan = report_simpleinterest::getLoanDetails(trim($no_acc), trim($id_pt));
        $reports = report_simpleinterest::getReportsByNoAcc(trim($no_acc), trim($id_pt));

        // Cek apakah data loan dan reports ada
        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(30);

        $infoRows = [
            ['No. Account', ':', $loan->no_acc],
            ['Debtor Name', ':', $loan->deb_name],
            ['Original Balance', ':', number_format($loan->org_bal, 2)],
            ['Original Date', ':', date('d/m/Y', strtotime($loan->org_date))],
            ['Term', ':', $loan->TERM . ' Months'],
            ['Maturity Date', ':', date('d/m/Y', strtotime($loan->mtr_date))],
        ];

        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $currentRow, $info[0]);
            $sheet->setCellValue('B' . $currentRow, $info[1]);
            $sheet->setCellValue('C' . $currentRow, $info[2]);
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $currentRow++;
        }

        $sheet->setCellValue('A10', 'Accrual Interest Report - Report Details');
        $sheet->mergeCells('A10:H10');
        $sheet->getStyle('A10:H10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10:H10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10:H10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10:H10')->getFill()->getStartColor()->setARGB('FF006600');
        $sheet->getStyle('A10:H10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $headers = [
            'Month',
            'Payment Date',
            'Payment Amount',
            'Accrued Interest',
            'Interest Payment',
            'Time Gap',
            'Outstanding Amount',
            'Cummulative Time Gap'
        ];

        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '12', $header);
            $sheet->getStyle($columnIndex . '12')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '12')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '12')->getFill()->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($columnIndex . '12')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        $row = 13;
        $totalTimeGap = 0;
        foreach ($reports as $report) {
            $totalTimeGap += $report->timegap;

            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->setCellValue('B' . $row, date('Y-m-d', strtotime($report->tglangsuran)));
            $sheet->setCellValue('C' . $row, number_format($report->pmtamt, 2));
            $sheet->setCellValue('D' . $row, number_format($report->accrconv ?? 0, 2));
            $sheet->setCellValue('E' . $row, number_format($report->bunga, 2));
            $sheet->setCellValue('F' . $row, number_format($report->timegap, 2));
            $sheet->setCellValue('G' . $row, number_format($report->outsamtconv, 2));
            $sheet->setCellValue('H' . $row, number_format($totalTimeGap, 2));

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('C' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "accrual_interest_report_$no_acc.pdf";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function checkData($no_acc, $id_pt)
    {
        try {
            // Ambil data loan
            $loan = report_simpleinterest::getLoanDetails(trim($no_acc), trim($id_pt));
            
            // Ambil data reports
            $reports = report_simpleinterest::getReportsByNoAcc(trim($no_acc), trim($id_pt));


            // Cek keberadaan data
            if (!$loan || $reports->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk nomor akun dan ID PT yang diberikan'
                ]);
            }

            // Jika data ditemukan
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => [
                    'loan' => $loan,
                    'reports_count' => $reports->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function exportCsv($no_acc, $id_pt)
    {
        $loan = report_simpleinterest::getLoanDetails($no_acc, $id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc, $id_pt);

        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        $csvData = [];
        $csvData[] = ['Month', 'Payment Date', 'Payment Amount', 'Accrued Interest', 'Interest Payment', 'Time Gap', 'Outstanding Amount', 'Cummulative Time Gap'];

        $totalTimeGap = 0;
        foreach ($reports as $report) {
            $totalTimeGap += $report->timegap;
            $csvData[] = [
                $report->bulanke,
                date('Y-m-d', strtotime($report->tglangsuran)),
                number_format($report->pmtamt, 2),
                number_format($report->accrconv ?? 0, 2),
                number_format($report->bunga, 2),
                number_format($report->timegap, 2),
                number_format($report->outsamtconv, 2),
                number_format($totalTimeGap, 2)
            ];
        }

        $filename = "accrual_interest_report_$no_acc.csv";
        $handle = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->streamDownload(function() use ($handle) {
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
    
}
