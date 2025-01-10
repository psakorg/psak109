<?php

namespace App\Http\Controllers\report\Report_Amortised_Cost;

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
        return view('report.amortised_cost.simple_interest.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc,$id_pt)
    {
        $no_acc = trim($no_acc);
        $loan = report_simpleinterest::getLoanDetails($no_acc, $id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc, $id_pt);

        // dd($loan);

        if (!$loan) {
            abort(404, 'Loan not found');
        }
        // dd($reports, $loan);

        return view('report.amortised_cost.simple_interest.view', compact('loan', 'reports'));
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
 $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
 $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
 $sheet->getPageMargins()->setTop(0.5);
 $sheet->getPageMargins()->setRight(0.5);
 $sheet->getPageMargins()->setLeft(0.5);
 $sheet->getPageMargins()->setBottom(0.5);

 $sheet->getColumnDimension('A')->setWidth(20);
 $sheet->getColumnDimension('B')->setWidth(5);
 $sheet->getColumnDimension('C')->setWidth(30);
 $entityName = "PT PRAMATECH";

 $infoRows = [
     ['Entity Name', ':', $entityName],
     ['Account Number', ':', "'" . $loan->no_acc],
     ['Debitor Name', ':', $loan->deb_name],
     ['Original Amount', ':', number_format($loan->nbal, 2)],
     ['Original Loan Date', ':', date('d-m-Y', strtotime($loan->org_date))],
     ['Term', ':', $loan->term . ' Month'],
     ['Maturity Loan Date', ':',  date('d-m-Y', strtotime($loan->mtr_date))],
     ['Interest Rate', ':', number_format($loan->rate*100, 5) . '%'],
 ];


 $currentRow = 3;
 foreach ($infoRows as $info) {
     $sheet->setCellValue('A' . $currentRow, $info[0]);
     $sheet->setCellValue('B' . $currentRow, $info[1]);
     $sheet->setCellValue('C' . $currentRow, $info[2]);
     $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getRowDimension($currentRow)->setRowHeight(25);
     $currentRow++;
 }
 $sheet->getColumnDimension('F')->setWidth(20);
 $sheet->getColumnDimension('G')->setWidth(5);
 $sheet->getColumnDimension('H')->setWidth(30);
 // Menghitung nilai org amount
 $upfrontFee = round(-($loan->org_bal * 0.01), 0);
 $CarryingAmount = $loan->org_bal+$upfrontFee;
 $infoRows = [
 ['Outstanding Interest', ':', number_format($loan->bilint ?? 0)],
 ['Up Front Fee', ':', number_format($loan->prov ?? 0, 2) ],
 ['Transaction Cost', ':', number_format($loan->trxcost, 2)],
 ['Carrying Amount', ':', number_format($CarryingAmount, 2)],
 ['EIR Exposure', ':', number_format($loan->eirex * 100, 14) . '%'],
 ['EIR Calculated', ':',  number_format($loan->eircalc * 100, 14) . '%'],
 ];
 $currentRow = 3;
 foreach ($infoRows as $info) {
     $sheet->setCellValue('F' . $currentRow, $info[0]);
     $sheet->setCellValue('G' . $currentRow, $info[1]);
     $sheet->setCellValue('H' . $currentRow, $info[2]);
     $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getRowDimension($currentRow)->setRowHeight(25);
     $currentRow++;
 }
        // Set judul tabel laporan
        $sheet->setCellValue('A12', 'Amortised Cost Simple Interest Report - Report Details');
        $sheet->mergeCells('A12:L12'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A12')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A12')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A12')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A12')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Transaction Date',
            'Days Interest',
            'Payment Amount',
            'Withdrawal',
            'Reimbursement',
            'Interest Recognition',
            'Interest Payment',
            'Amortised',
            'Carrying Amount',
            'Cummulative Amortized',
            'Unamortized',
        ];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '13', $header);
            $sheet->getStyle($columnIndex . '13')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '13')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '13')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '13')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 14; // Mulai dari baris 14 untuk data laporan
        $totalAmortised = 0;
        $totalDaysInterest = 0;
        $totalWithdrawal = 0;
        $totalReimbursement = 0;
        $totalInterestPayment = 0;
        $totalPaymentAmount = 0;
        $totalInterestRecognition = 0;
        $totalCarryingAmount = 0;
        $totalCummulativeAmortized = 0;
        $totalUnamortized = 0;
        $cumulativeAmortized = 0;
        foreach ($reports as $report) {
            $amortized = $report->amortized;
            $cumulativeAmortized += $amortized;

            // Hitung nilai unamortized
                if ($row == 14) {
                    // Untuk baris pertama, gunakan nilai upfrontFee
                    $unamortized = -$loan->prov;
                    //$unamortized = $upfrontFee;
                } else {
                    // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                    $unamortized = $unamortized + $amortized;
                }

                $totalAmortised += $report->amortized ?? 0;
                $totalDaysInterest += $report->haribunga ?? 0;
                $totalWithdrawal += $report->penarikan ?? 0;
                $totalReimbursement += $report->pengembalian ?? 0;
                $totalInterestPayment += $report->bunga ?? 0;
                $totalPaymentAmount += $report->pmtamt ?? 0;
                $totalInterestRecognition += $report->bungaeir ?? 0; // Assuming interest recognition is bunga
                $totalCarryingAmount += $report->baleir ?? 0;
                $totalCummulativeAmortized += $cumulativeAmortized ?? 0;
                $totalUnamortized += $report->amortized + $report->outsamtconv ?? 0;

            // Mengisi data ke dalam kolom
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $row, $report->bulanke ?? 'Data tidak ditemukan');
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate');
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('C' . $row, number_format($report->haribunga ?? 0));
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt ?? 0));
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('E' . $row, number_format($report->penarikan ?? 0));
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('F' . $row, number_format($report->pengembalian ?? 0));
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('G' . $row, number_format($report->bungaeir ?? 0));
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, number_format($report->bunga ?? 0));
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('I' . $row, number_format($report->amortized ?? 0));
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('J' . $row, number_format($report->baleir ?? 0));
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('K' . $row, number_format($cumulativeAmortized ?? 0));
            $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('L' . $row, number_format($unamortized ?? 0));

             // Mengatur angka menjadi rata kanan
            $sheet->getStyle('C' . $row . ':L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++; // Pindah ke baris berikutnya
        }
          //TOTAL AMORTISED COST
          $sheet->setCellValue('A' . $row, "TOTAL");
          $sheet->mergeCells('A' . $row . ':B' . $row); 
          $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
          $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('C' . $row, number_format($reports->sum('haribunga')));
          $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('D' . $row, number_format($reports->sum('pmtamt')));
          $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('E' . $row, number_format($reports->sum('penarikan')));
          $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('F' . $row, number_format($reports->sum('pengembalian')));
          $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('G' . $row, number_format($reports->sum('bungaeir')));
          $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('H' . $row, number_format($reports->sum('bunga')));
          $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('I' . $row, number_format($reports->sum('amortized')));
          $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('J' . $row, null);
          $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('K' . $row, null);
          $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('L' . $row, null);
          
  
          foreach (range('A', 'L') as $columnID) {
              $sheet->getColumnDimension($columnID)->setAutoSize(true);
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
        $sheet->getStyle('A12:L12')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A13:L' . ($row - 1))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan nama file
        $filename = "ReportAmortisedCostCorporateLoan_$no_acc.xlsx";

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


    // Set informasi pinjaman
 $sheet->setCellValue('A2', 'Entitiy Name');
$sheet->getStyle('A2')->getFont()->setBold(true);
$entitiyName = 'PT. PACIFIC MULTI FINANCE';
$sheet->setCellValue('B2', $entitiyName);
    $sheet->setCellValue('A3', 'Account Number');
        $sheet->getStyle('A3')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->setCellValue('B3', $loan->no_acc);
        $sheet->setCellValue('A4', 'Debitor Name');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Debitor Name
        $sheet->setCellValue('B4', $loan->deb_name);
        $sheet->setCellValue('A5', 'Original Amount');
        $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk Original Amount
        $sheet->setCellValue('B5', number_format($loan->org_bal, 2));
        $sheet->setCellValue('A6', 'Original Loan Date');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bold untuk Original Loan Date
        $sheet->setCellValue('B6', date('Y-m-d', strtotime($loan->org_date)));
        $sheet->setCellValue('A7', 'Term');
        $sheet->getStyle('A7')->getFont()->setBold(true); // Set bold untuk Term
        $sheet->setCellValue('B7', $loan->TERM);
        $sheet->setCellValue('A8', 'Maturity Loan Date');
        $sheet->getStyle('A8')->getFont()->setBold(true);
        $sheet->setCellValue('B8', date('Y-m-d', strtotime($loan->mtr_date))); // Set bold untuk Maturity Date
        $sheet->setCellValue('A9', 'Interest Rate');
        $sheet->getStyle('A9')->getFont()->setBold(true); // Set bol untuk Interest Rate
        $sheet->setCellValue('D3', 'Outstanding Interest');
        $sheet->getStyle('D3')->getFont()->setBold(true); // Set bold untuk Outstanding Interest
        $sheet->setCellValue('E3', $loan->no_acc);
        $sheet->setCellValue('D4', 'Up Front Fee');
        $sheet->getStyle('D4')->getFont()->setBold(true); // Set bold untuk Up Front Fee
        $sheet->setCellValue('E4', $loan->deb_name);
        $sheet->setCellValue('D5', 'Transaction Cost');
        $sheet->getStyle('D5')->getFont()->setBold(true); // Set bold untuk Transaction Cost
        $sheet->setCellValue('E5', number_format($loan->org_bal, 2));
        $sheet->setCellValue('D6', 'Carrying Amount');
        $sheet->getStyle('D6')->getFont()->setBold(true); // Set bold untuk Carrying Amount
        $sheet->setCellValue('E6', date('Y-m-d', strtotime($loan->org_date)));
        $sheet->setCellValue('D7', 'EIR Exposure');
        $sheet->getStyle('D7')->getFont()->setBold(true); // Set bold untuk EIR Exposure
        $sheet->setCellValue('E7', $loan->TERM);
        $sheet->setCellValue('D8', 'EIR Calculated');
        $sheet->getStyle('D8')->getFont()->setBold(true);
        $sheet->setCellValue('E8', date('Y-m-d', strtotime($loan->mtr_date))); // Set bold untuk EIR Calculated





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
    $filename = "ReportAmortisedCostCorporateLoan_$no_acc.pdf";

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
        // Ambil data loan
        $loan = report_simpleinterest::getLoanDetails(trim($no_acc), trim($id_pt));
        
        // Ambil data reports
        $reports = report_simpleinterest::getReportsByNoAcc(trim($no_acc), trim($id_pt));

        // dd($loan, $reports);

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
