<?php

namespace App\Http\Controllers\report\Report_Expective_Cash_Flow;

use App\Models\report_effective;
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
use Illuminate\Support\Facades\DB;

use Dompdf\Dompdf;
use Dompdf\Options;

class effectiveController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporatt
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
           // Ambil jumlah item per halaman dari query string, default 10
           $perPage = $request->input('per_page', 10);
           // Ambil data dengan pagination
           $loans = report_effective::fetchAll($id_pt, $perPage);

        return view('report.expective_cash_flow.effective.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akunn
    public function view($no_acc,$id_pt)
    {
        $no_acc = trim($no_acc);
        $loan = report_effective::getLoanDetails($no_acc,$id_pt);
        $master=report_effective::getMasterDataByNoAcc($no_acc,$id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc,$id_pt);

        if (!$loan) {
            abort(404, 'Loan not found');
        }


        return view('report.expective_cash_flow.effective.view', compact('loan', 'reports','master'));
    }

    public function exportExcel($no_acc,$id_pt)
    {
        // Ambil data loan dan reports
        $loan = report_effective::getLoanDetails(trim($no_acc), trim($id_pt));
        $reports = report_effective::getReportsByNoAcc(trim($no_acc), trim($id_pt));
        $master=report_effective::getMasterDataByNoAcc(trim($no_acc),trim($id_pt));
        $entityName = DB::table('public.tblobaleffective')
        ->join('public.tbl_pt', 'tblobaleffective.id_pt', '=', 'tbl_pt.id_pt')
        ->where('tblobaleffective.no_branch', $id_pt)
        ->select('tbl_pt.nama_pt')
        ->first();

        // Cek apakah data loan dan reports ada
        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        $infoRows = [
        ['Entity Name', ': ' . $entityName->nama_pt ],
        ['Account Number', ': ' . $loan->no_acc],
        ['Original Amount', ': ' . number_format($loan->org_bal, 2)],
        ['Term', ': ' . $master->term . ' Month'],
        ['Interest Rate', ': ' .  number_format($master->rate  * 100, 5). '%'],
        ];

        $currentRow = 2;
        foreach ($infoRows as $info) {
        $sheet->setCellValue('A' . $currentRow, $info[0]);
        $sheet->setCellValue('C' . $currentRow, $info[1]);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        }

$infoRows = [
    ['Debitor Name', ': ' . $loan->deb_name],
    ['Original Loan Date', ': ' .  date('d-m-Y', strtotime($loan->org_date))],
    ['Maturity Loan Date', ': ' . date('d-m-Y', strtotime($loan->mtr_date))],
    ['Payment Amount', ': ' . number_format($master->pmtamt, 2)],
];
$currentRow = 2;
foreach ($infoRows as $info) {
    $sheet->setCellValue('F' . $currentRow, $info[0]);
    $sheet->setCellValue('G' . $currentRow, $info[1]);
    $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
}

        // Set judul tabel laporan
        $sheet->setCellValue('A8', 'Expected Cash Flow Effective Report - Report Details');
        $sheet->mergeCells('A8:G8'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A8')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A8')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(9)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Payment Date',
            'Payment Amount',
            'Interest Payment',
            'Principal Payment',
            'Balance Contractual',
            'Unearned Interest Income',
        ];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '10', $header);
            $sheet->getStyle($columnIndex . '10')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '10')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '10')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '10')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 11; // Mulai dari baris 13 untuk data laporan
        $cumulativebunga = 0;
        $totalPaymentAmount = 0;
        $totalInterestPayment = 0;
        $totalPrincipalPayment = 0;
        $totalBalanceContractual = 0;
        $sumBunga = 0;
        $totalInterestIncome = 0;
        foreach ($reports as $report)
            $totalInterestIncome += $report->bunga;
        foreach ($reports as $report) {
            $bunga = $report->bunga;
            $interestPayment = $report->bunga;
            $sumBunga += $bunga;
            $totalPaymentAmount += $report->pmtamt;
            $totalInterestPayment += $report->bunga;
            $totalBalanceContractual += $report->balance;
            if ($row == 11) {
                $interestIncome = $totalInterestIncome;
            } else {
                $totalInterestIncome -= $bunga;
                $interestIncome = $totalInterestIncome;
            }
    // Mengisi data ke dalam kolom
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A' . $row, $report->bulanke ?? 'Data tidak ditemukan');
    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('B' . $row, isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate');
    $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('C' . $row, number_format($report->pmtamt ?? 0));
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('D' . $row, number_format($report->bunga ?? 0));
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('E' . $row, number_format($report->pokok ?? 0)); // Sama dengan kolom D
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('F' . $row, number_format($report->balance ?? 0));
    $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('G' . $row, number_format($interestIncome ?? 0));

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        //TOTAL EXPECTIVE
      $sheet->setCellValue('A' . $row, "TOTAL");
      $sheet->mergeCells('A' . $row . ':B' . $row); 
      $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('C' . $row, number_format($totalPaymentAmount));
      $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('D' . $row, number_format($totalInterestPayment));
      $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('E' . $row, number_format($totalPrincipalPayment));
      $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('F' . $row, null);
      $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('G' . $row, null);

      $sheet->mergeCells('A2:B2');
      $sheet->mergeCells('A3:B3');
      $sheet->mergeCells('A4:B4');
      $sheet->mergeCells('A5:B5');
      $sheet->mergeCells('A6:B6');
      $sheet->mergeCells('C2:D2');
      $sheet->mergeCells('C3:D3');
      $sheet->mergeCells('C4:D4');
      $sheet->mergeCells('C5:D5');
      $sheet->mergeCells('C6:D6');
  
    //   foreach (range('A', 'G') as $columnID) {
    //       $sheet->getColumnDimension($columnID)->setAutoSize(true);
    //   }

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(24);
        $sheet->getColumnDimension('E')->setWidth(24);
        $sheet->getColumnDimension('F')->setWidth(26);
        $sheet->getColumnDimension('G')->setWidth(26);

        // Set border untuk header tabel
        $sheet->getStyle('A10:G10')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A11:G' . $row)->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        // foreach (range('A', 'G') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        // Siapkan nama file
        $filename = "ReportExpectedCashFlowEffective_$no_acc.xlsx";

        // Buat writer dan simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        // Kembalikan response Excel
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }



    // Method untuk mengekspor data ke PDF
    public function exportPdf($no_acc,$id_pt)
{
        // Ambil data loan dan reports
        $loan = report_effective::getLoanDetails(trim($no_acc), trim($id_pt));
        $reports = report_effective::getReportsByNoAcc(trim($no_acc), trim($id_pt));
        $master=report_effective::getMasterDataByNoAcc(trim($no_acc),trim($id_pt));
        $entityName = DB::table('public.tblobaleffective')
        ->join('public.tbl_pt', 'tblobaleffective.id_pt', '=', 'tbl_pt.id_pt')
        ->where('tblobaleffective.no_branch', $id_pt)
        ->select('tbl_pt.nama_pt')
        ->first();

        // Cek apakah data loan dan reports ada
        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        $infoRows = [
        ['Entity Name', ': ' . $entityName->nama_pt ],
        ['Account Number', ': ' . $loan->no_acc],
        ['Original Amount', ': ' . number_format($loan->org_bal, 2)],
        ['Term', ': ' . $master->term . ' Month'],
        ['Interest Rate', ': ' .  number_format($master->rate  * 100, 5). '%'],
        ];

        $currentRow = 2;
        foreach ($infoRows as $info) {
        $sheet->setCellValue('A' . $currentRow, $info[0]);
        $sheet->setCellValue('C' . $currentRow, $info[1]);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        }

$infoRows = [
    ['Debitor Name', ': ' . $loan->deb_name],
    ['Original Loan Date', ': ' .  date('d-m-Y', strtotime($loan->org_date))],
    ['Maturity Loan Date', ': ' . date('d-m-Y', strtotime($loan->mtr_date))],
    ['Payment Amount', ': ' . number_format($master->pmtamt, 2)],
];
$currentRow = 2;
foreach ($infoRows as $info) {
    $sheet->setCellValue('F' . $currentRow, $info[0]);
    $sheet->setCellValue('G' . $currentRow, $info[1]);
    $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
}

        // Set judul tabel laporan
        $sheet->setCellValue('A8', 'Expected Cash Flow Effective Report - Report Details');
        $sheet->mergeCells('A8:G8'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A8')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A8')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(9)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Payment Date',
            'Payment Amount',
            'Interest Payment',
            'Principal Payment',
            'Balance Contractual',
            'Unearned Interest Income',
        ];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '10', $header);
            $sheet->getStyle($columnIndex . '10')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '10')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '10')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '10')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 11; // Mulai dari baris 13 untuk data laporan
        $cumulativebunga = 0;
        $totalPaymentAmount = 0;
        $totalInterestPayment = 0;
        $totalPrincipalPayment = 0;
        $totalBalanceContractual = 0;
        $sumBunga = 0;
        $totalInterestIncome = 0;
        foreach ($reports as $report)
            $totalInterestIncome += $report->bunga;
        foreach ($reports as $report) {
            $bunga = $report->bunga;
            $interestPayment = $report->bunga;
            $sumBunga += $bunga;
            $totalPaymentAmount += $report->pmtamt;
            $totalInterestPayment += $report->bunga;
            $totalBalanceContractual += $report->balance;
            if ($row == 11) {
                $interestIncome = $totalInterestIncome;
            } else {
                $totalInterestIncome -= $bunga;
                $interestIncome = $totalInterestIncome;
            }
    // Mengisi data ke dalam kolom
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A' . $row, $report->bulanke ?? 'Data tidak ditemukan');
    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('B' . $row, isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate');
    $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('C' . $row, number_format($report->pmtamt ?? 0));
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('D' . $row, number_format($report->bunga ?? 0));
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('E' . $row, number_format($report->pokok ?? 0)); // Sama dengan kolom D
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('F' . $row, number_format($report->balance ?? 0));
    $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('G' . $row, number_format($interestIncome ?? 0));

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        //TOTAL EXPECTIVE
      $sheet->setCellValue('A' . $row, "TOTAL");
      $sheet->mergeCells('A' . $row . ':B' . $row); 
      $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('C' . $row, number_format($totalPaymentAmount));
      $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('D' . $row, number_format($totalInterestPayment));
      $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('E' . $row, number_format($totalPrincipalPayment));
      $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('F' . $row, null);
      $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('G' . $row, null);

      $sheet->mergeCells('A2:B2');
      $sheet->mergeCells('A3:B3');
      $sheet->mergeCells('A4:B4');
      $sheet->mergeCells('A5:B5');
      $sheet->mergeCells('A6:B6');
      $sheet->mergeCells('C2:D2');
      $sheet->mergeCells('C3:D3');
      $sheet->mergeCells('C4:D4');
      $sheet->mergeCells('C5:D5');
      $sheet->mergeCells('C6:D6');
  
    //   foreach (range('A', 'G') as $columnID) {
    //       $sheet->getColumnDimension($columnID)->setAutoSize(true);
    //   }

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(24);
        $sheet->getColumnDimension('E')->setWidth(24);
        $sheet->getColumnDimension('F')->setWidth(26);
        $sheet->getColumnDimension('G')->setWidth(26);

        // Set border untuk header tabel
        $sheet->getStyle('A10:G10')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A11:G' . $row)->applyFromArray($styleArray);

    // // Mengatur lebar kolom agar lebih rapi
    // foreach (range('A', 'G') as $columnID) {
    //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
    // }

    // Siapkan nama file
    $filename = "ReportExpectedCashFlowEffective_$no_acc.pdf";

    // Set pengaturan untuk PDF
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);

    // Siapkan direktori untuk menyimpan file sementara
    $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');

    // Simpan file PDF
    $writer->save($temp_file);

    // Kembalikan response PDF
    return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
}

public function checkData($no_acc, $id_pt)
{
    try {
        $no_acc = trim($no_acc);
        
        $loan = report_effective::getLoanDetails($no_acc, $id_pt);
        $master = report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);

        if (!$loan) {
            return response()->json(['success' => false, 'message' => 'Data loan tidak ditemukan']);
        }
        if (!$master) {
            return response()->json(['success' => false, 'message' => 'Data master tidak ditemukan']);
        }
        if ($reports->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data laporan tidak ditemukan']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data' => [
                'loan' => $loan,
                'master' => $master,
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
