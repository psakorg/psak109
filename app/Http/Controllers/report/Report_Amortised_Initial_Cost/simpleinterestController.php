<?php

namespace App\Http\Controllers\report\Report_Amortised_Initial_Cost;

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
use Illuminate\Support\Facades\DB;

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
        //  dd($loans);
        return view('report.amortised_initial_cost.simple_interest.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt)
    {
        $no_acc = trim($no_acc);
        $loan = report_simpleinterest::getLoanDetails($no_acc, $id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc, $id_pt);

        if (!$loan) {
            abort(404, 'Loan not found');
        }


        return view('report.amortised_initial_cost.simple_interest.view', compact('loan', 'reports'));
    }

    public function exportExcel($no_acc, $id_pt)
    {
        // Ambil data loan dan reports
        $loan = report_simpleinterest::getLoanDetails(trim($no_acc), trim($id_pt));
        $reports = report_simpleinterest::getReportsByNoAcc(trim($no_acc), trim($id_pt));
        $entityName = DB::table('public.tblobalcorporateloan')
        ->join('public.tbl_pt', 'tblobalcorporateloan.id_pt', '=', 'tbl_pt.id_pt')
        ->where('tblobalcorporateloan.no_branch', $id_pt)
        ->select('tbl_pt.nama_pt')
        ->first();

        // Cek apakah data loan dan reports adaa
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

 $infoRows = [
     ['Entity Name', ':', $entityName ? $entityName->nama_pt : ''],
     ['Account Number', ':', "'" . $loan->no_acc],
     ['Debitor Name', ':', $loan->deb_name],
     ['Original Amount', ':', number_format($loan->org_bal, 2)],
     ['Original Loan Date', ':', date('d-m-Y', strtotime($loan->org_date))],
     ['Maturity Date', ':',  date('d-m-Y', strtotime($loan->mtr_date))],
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
 // Misalkan trxcost adalah string dengan simbol mata uang
 $trxcost = $loan->trxcost; // Ambil nilai dari database
 // Hapus simbol mata uang dan pemisah ribuan
 $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
 // Konversi ke float
 $trxcostFloat = (float)$trxcost;
 $infoRows = [
 ['Transaction Cost', ':', number_format($trxcostFloat ?? 0, 2)],
 ['Outstanding Amount Initial Cost', ':', number_format($loan->org_bal ?? 0, 2) ],
 ['EIR Cost Calculated', ':', number_format($loan->eircalc_cost*100, 14). '%'],
 ['Term', ':', number_format($loan-> term).' Month'],
 ['Interest Rate', ':', number_format($loan->rate * 100, 5) . '%'],
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
        $sheet->setCellValue('A10', 'Amortised Initial Cost Simple Interest Report - Report Details');
        $sheet->mergeCells('A10:L10'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Transaction Date',
            'Days Interest',
            'Payment Amount',
            'Withdrawal',
            'Reimbursement',
            'Effective Interest Based on Effective Yield (UF/TC)',
            'Effective Interest Based on Effective (UF)',
            'Amortised Transaction Cost',
            'Outstanding Amount Initial Transaction Cost',
            'Cummulative Transaction Cost',
            'Unamortized Transaction Cost'
        
        ];
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
        $cumulativeAmortized = 0; // Inisialisasi variabel kumulatif
        $totalharibunga = 0;
        foreach ($reports as $report) {
            $amortized = $report->amortisecost; // Ambil nilai amortized dari laporan
                                    $cumulativeAmortized += $amortized; // Tambahkan amortized ke total kumulatif
                                    $unamortrxcost = $trxcostFloat;
                                    // Hitung nilai unamortized
                                    if ($row == 13) {
                                        // Untuk baris pertama, gunakan nilai trxcost
                                        $unamort = $unamortrxcost;
                                    } else {
                                        // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                                        $unamort = $unamort + $amortized;
                                    }
                                $totalharibunga += $report->haribunga ?? 0;
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
            $sheet->setCellValue('G' . $row, number_format($report->accrcost ?? 0));
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, number_format($report->accrconv ?? 0));
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('I' . $row, number_format($report->amortisecost ?? 0));
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('J' . $row, number_format($report->outsamtcost ?? 0));
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
          $sheet->setCellValue('G' . $row, number_format($reports->sum('accrcost')));
          $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('H' . $row, number_format($reports->sum('accrconv')));
          $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('I' . $row, number_format($reports->sum('amortisecost')));
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
        $sheet->getStyle('A13:L' . $row)->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan nama file
        $filename = "ReportAmortisedInitialCostCorporateLoan_$no_acc.xlsx";

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
    $entityName = DB::table('public.tblobalcorporateloan')
        ->join('public.tbl_pt', 'tblobalcorporateloan.id_pt', '=', 'tbl_pt.id_pt')
        ->where('tblobalcorporateloan.no_branch', $id_pt)
        ->select('tbl_pt.nama_pt')
        ->first();

    // Cek apakah data loan dan reports ada
    if (!$loan || $reports->isEmpty()) {
        return response()->json(['message' => 'No data found for the given account number.'], 404);
    }

    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

    // Set informasi pinjaman
 $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
 $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
 $sheet->getPageMargins()->setTop(0.5);
 $sheet->getPageMargins()->setRight(0.5);
 $sheet->getPageMargins()->setLeft(0.5);
 $sheet->getPageMargins()->setBottom(0.5);

 $infoRows = [
     ['Entity Name', ':', $entityName ? $entityName->nama_pt : ''],
     ['Account Number', ':', $loan->no_acc],
     ['Debitor Name', ':', $loan->deb_name],
     ['Original Amount', ':', number_format($loan->org_bal, 2)],
     ['Original Loan Date', ':', date('d-m-Y', strtotime($loan->org_date))],
     ['Maturity Date', ':',  date('d-m-Y', strtotime($loan->mtr_date))],
 ];


 $currentRow = 3;
 foreach ($infoRows as $info) {
     $sheet->setCellValue('A' . $currentRow, $info[0]);
     $sheet->setCellValue('B' . $currentRow, $info[1]);
     $sheet->setCellValue('C' . $currentRow, $info[2]);
     $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getRowDimension($currentRow)->setRowHeight(15);
     $currentRow++;
 }
 // Misalkan trxcost adalah string dengan simbol mata uang
 $trxcost = $loan->trxcost; // Ambil nilai dari database
 // Hapus simbol mata uang dan pemisah ribuan
 $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
 // Konversi ke float
 $trxcostFloat = (float)$trxcost;
 $infoRows = [
 ['Transaction Cost', ':', number_format($trxcostFloat ?? 0, 2)],
 ['Outstanding Amount Initial Cost', ':', number_format($loan->org_bal ?? 0, 2) ],
 ['EIR Cost Calculated', ':', number_format($loan->eircalc_cost*100, 14). '%'],
 ['Term', ':', number_format($loan-> term).' Month'],
 ['Interest Rate', ':', number_format($loan->rate * 100, 5) . '%'],
 ];
 $currentRow = 3;
 foreach ($infoRows as $info) {
     $sheet->setCellValue('F' . $currentRow, $info[0]);
     $sheet->setCellValue('G' . $currentRow, $info[1]);
     $sheet->setCellValue('H' . $currentRow, $info[2]);
     $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     $sheet->getRowDimension($currentRow)->setRowHeight(15);
     $currentRow++;
 }

        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Amortised Initial Cost Simple Interest Report - Report Details');
        $sheet->mergeCells('A10:L10'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getRowDimension(11)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Transaction Date',
            'Days Interest',
            'Payment Amount',
            'Withdrawal',
            'Reimbursement',
            'Effective Interest Based on Effective Yield (UF/TC)',
            'Effective Interest Based on Effective (UF)',
            'Amortised Transaction Cost',
            'Outstanding Amount Initial Transaction Cost',
            'Cummulative Transaction Cost',
            'Unamortized Transaction Cost'
        
        ];
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
        $cumulativeAmortized = 0; // Inisialisasi variabel kumulatif
        $totalharibunga = 0;
        foreach ($reports as $report) {
            $amortized = $report->amortisecost; // Ambil nilai amortized dari laporan
                                    $cumulativeAmortized += $amortized; // Tambahkan amortized ke total kumulatif
                                    $unamortrxcost = $trxcostFloat;
                                    // Hitung nilai unamortized
                                    if ($row == 13) {
                                        // Untuk baris pertama, gunakan nilai trxcost
                                        $unamort = $unamortrxcost;
                                    } else {
                                        // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                                        $unamort = $unamort + $amortized;
                                    }
                                $totalharibunga += $report->haribunga ?? 0;
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
            $sheet->setCellValue('G' . $row, number_format($report->accrcost ?? 0));
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, number_format($report->accrconv ?? 0));
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('I' . $row, number_format($report->amortisecost ?? 0));
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('J' . $row, number_format($report->outsamtcost ?? 0));
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
          $sheet->setCellValue('G' . $row, number_format($reports->sum('accrcost')));
          $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('H' . $row, number_format($reports->sum('accrconv')));
          $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
          $sheet->setCellValue('I' . $row, number_format($reports->sum('amortisecost')));
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
    $sheet->getStyle('A13:L' . $row)->applyFromArray($styleArray);

    // Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'L') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Siapkan nama file
    $filename = "ReportAmortisedInitialCostCorporateLoan_$no_acc.pdf";

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
