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
        // dd($no_acc, $id_pt); // Debugging parameterr
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
        $sheet->setCellValue('C5', 'Rp ' . number_format($loan->org_bal, 2));
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
        $sheet->setCellValue('E5', number_format($loan->org_bal, 2));
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
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt, 2));
            $sheet->setCellValue('E' . $row, number_format($report->penarikan, 2));
            $sheet->setCellValue('F' . $row, number_format($report->pengembalian, 2));
            $sheet->setCellValue('G' . $row, number_format($report->bunga, 2));
            $sheet->setCellValue('H' . $row, number_format($report->balance, 2));
            $sheet->setCellValue('I' . $row, $report->timegap);
            $sheet->setCellValue('J' . $row, number_format($report->outsamtconv, 2));

            // Mengatur font menjadi bold untuk setiap baris data
            $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

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
    $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
    $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
    $sheet->getPageMargins()->setTop(0.5);
    $sheet->getPageMargins()->setRight(0.5);
    $sheet->getPageMargins()->setLeft(0.5);
    $sheet->getPageMargins()->setBottom(0.5);

    // Set lebar kolom untuk informasi header (kiri dan kanan)
    $sheet->getColumnDimension('A')->setWidth(30)->setAutoSize(true);
    $sheet->getColumnDimension('B')->setWidth(5)->setAutoSize(true);
    $sheet->getColumnDimension('C')->setWidth(30);
    $sheet->getColumnDimension('D')->setWidth(30)->setAutoSize(true);
    $sheet->getColumnDimension('G')->setWidth(30)->setAutoSize(true);
    $sheet->getColumnDimension('H')->setWidth(30)->setAutoSize(true);
    $sheet->getColumnDimension('I')->setWidth(30)->setAutoSize(true);

    // Informasi pinjaman sebelah kiri
    $leftInfoRows = [
        ['Account Number', ':', $loan->no_acc],
        ['Debitor Name', ':', $loan->deb_name],
        ['Original Amount', ':', 'Rp ' . number_format($loan->org_bal, 2)],
        ['Term', ':', $loan->TERM . ' Months']
    ];

    // Informasi pinjaman sebelah kanan
    $rightInfoRows = [
        ['Outstanding Amount', ':', 'Rp ' . number_format($loan->org_bal, 2)],
        ['EIR Conversion Calculated', ':', number_format($loan->eircalc_conv*100, 14) . '%'],
        ['Original Loan Date', ':', date('d/m/Y', strtotime($loan->org_date))],
        ['Maturity Date', ':', date('d/m/Y', strtotime($loan->mtr_date))]
    ];

    $currentRow = 3;
    foreach ($leftInfoRows as $index => $leftInfo) {
        // Informasi sebelah kiri
        $sheet->setCellValue('A' . $currentRow, $leftInfo[0]);
        $sheet->setCellValue('B' . $currentRow, $leftInfo[1]); 
        $sheet->setCellValue('C' . $currentRow, $leftInfo[2]);

        // Informasi sebelah kanan (mulai dari kolom G untuk menyesuaikan dengan lebar tabel)
        $rightInfo = $rightInfoRows[$index];
        $sheet->setCellValue('H' . $currentRow, $rightInfo[0]);
        $sheet->setCellValue('I' . $currentRow, $rightInfo[1]);
        $sheet->setCellValue('J' . $currentRow, $rightInfo[2]);

        // Styling untuk baris sebelah kiri
        $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => false
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        // Styling untuk baris sebelah kanan
        $sheet->getStyle('H' . $currentRow . ':J' . $currentRow)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        // Set alignment kiri
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set alignment kanan
        $sheet->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set tinggi baris
        $sheet->getRowDimension($currentRow)->setRowHeight(25);

        $currentRow++;
    }

        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Accrual Interest Report - Report Details');
        $sheet->mergeCells('A10:J10');
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10')->getFill()->getStartColor()->setARGB('FF006600');
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Set header tabel
        $headers = ['Months', 'Transaction Date', 'Day of Interest', 'Payment Amount', 'Withdrawal', 'Reimbursement', 'Accrued interest', 'Interest Payment', 'Time Gap', 'Outstanding Amount'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '12', $header);
            $sheet->getStyle($columnIndex . '12')->getFont()->setBold(false);
            $sheet->getStyle($columnIndex . '12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '12')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '12')->getFill()->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($columnIndex . '12')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 13;
        $totalPmtAmt = 0;
        $totalPenarikan = 0;
        $totalPengembalian = 0;
        $totalBunga = 0;
        $totalBalance = 0;
        $totalTimeGap = 0;
        $totalOutstanding = 0;

        foreach ($reports as $report) {
            // Menambah total
            $totalPmtAmt += $report->pmtamt;
            $totalPenarikan += $report->penarikan;
            $totalPengembalian += $report->pengembalian;
            $totalBunga += $report->bunga;
            $totalBalance += $report->balance;
            $totalTimeGap += $report->timegap;
            $totalOutstanding += $report->outsamtconv;

            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->setCellValue('B' . $row, date('Y-m-d', strtotime($report->tglangsuran)));
            $sheet->setCellValue('C' . $row, $report->haribunga);
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($report->pmtamt, 2));
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($report->penarikan, 2));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($report->pengembalian, 2));
            $sheet->setCellValue('G' . $row, 'Rp ' . number_format($report->bunga, 2));
            $sheet->setCellValue('H' . $row, 'Rp ' . number_format($report->balance, 2));
            $sheet->setCellValue('I' . $row, number_format($report->timegap, 2));
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('J' . $row, 'Rp ' . number_format($report->outsamtconv, 2));

            // Mengatur alignment
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Menambahkan warna latar belakang alternatif
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF');
            }

            $row++;
        }

        // Menambahkan baris total
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, 'TOTAL');
        $sheet->mergeCells('A' . $totalRow . ':C' . $totalRow);
        $sheet->setCellValue('D' . $totalRow, 'Rp ' . number_format($totalPmtAmt, 2));
        $sheet->setCellValue('E' . $totalRow, 'Rp ' . number_format($totalPenarikan, 2));
        $sheet->setCellValue('F' . $totalRow, 'Rp ' . number_format($totalPengembalian, 2));
        $sheet->setCellValue('G' . $totalRow, 'Rp ' . number_format($totalBunga, 2));
        $sheet->setCellValue('H' . $totalRow, 'Rp ' . number_format($totalBalance, 2));
        $sheet->setCellValue('I' . $totalRow, number_format($totalTimeGap, 2));
        $sheet->setCellValue('J' . $totalRow, 'Rp ' . number_format($totalOutstanding, 2));

        // Styling untuk baris total
        $sheet->getStyle('A' . $totalRow . ':J' . $totalRow)->applyFromArray([
            'font' => ['bold' => false],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD3D3D3']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ]
        ]);

        // Alignment untuk baris total
        $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $totalRow . ':J' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Border untuk seluruh tabel
        $sheet->getStyle('A12:J' . $totalRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(10);  // Bulanke
        $sheet->getColumnDimension('B')->setWidth(15);  // Tgl Angsuran
        $sheet->getColumnDimension('C')->setWidth(35);   // Hari Bunga (diatur lebih kecil)
        $sheet->getColumnDimension('D')->setAutoSize(true);  // PMT Amt
        $sheet->getColumnDimension('E')->setAutoSize(true);  // Penarikan
        $sheet->getColumnDimension('F')->setAutoSize(true);  // Pengembalian
        $sheet->getColumnDimension('G')->setAutoSize(true);  // Bunga
        $sheet->getColumnDimension('H')->setAutoSize(true);  // Balance
        $sheet->getColumnDimension('I')->setAutoSize(true);  // Time Gap
        $sheet->getColumnDimension('J')->setAutoSize(true);  // Outstanding Amount

        // Siapkan nama file
        $filename = "accrual_interest_report_$no_acc.pdf";

        // Set pengaturan untuk PDF
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
}
