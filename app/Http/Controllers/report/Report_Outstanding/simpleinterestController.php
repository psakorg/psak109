<?php

namespace App\Http\Controllers\report\Report_Outstanding;

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

        return view('report.outstanding.simple_interest.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view(Request $request, $id_pt)
    {
        if (!$id_pt) {
            abort(404, 'Invalid ID');
        }
    
        // Validate if the authenticated user has access to this `id_pt`
        if ($id_pt != Auth::user()->id_pt) {
            abort(403, 'Unauthorized');
        }
        $loan = report_simpleinterest::getLoanDetailsbyidpt(trim($id_pt));
        // $corporateLoans = report_simpleinterest::getCorporateLoans($id_pt);
        // $reports = report_simpleinterest::getReportsByNoAcc(trim($id_pt));
        $reports = report_simpleinterest::getLoanDetailsbyidpt(trim($id_pt));


        $user = Auth::user();

        if (!$user) {
            return redirect('https://psak.pramatech.id');
        }

        // if (!$loan) {
        //     abort(404, 'Loan not found');
        // }

        //dd($loan, $reports,$user);
        // $bulan = $request->input('bulan', date('n'));
        // $tahun = $request->input('tahun', date('Y'));

        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        $master = DB::table('public.tblpsaklbucorporateloan')
        ->join('public.tblobalcorporateloan', 'tblpsaklbucorporateloan.no_acc', '=', 'tblobalcorporateloan.no_acc')
        ->where('tblpsaklbucorporateloan.no_branch', $id_pt)
        ->where('tblpsaklbucorporateloan.bulan', $bulan)
        ->where('tblpsaklbucorporateloan.tahun', $tahun)
        ->get();


     //dd(route('report-outstanding-si.view', ['id_pt' => Auth::user()->id_pt]));
        $isSuperAdmin = $user->role === 'superadmin';
        return view('report.outstanding.simple_interest.view', compact('loan', 'isSuperAdmin', 'user', 'master',  'reports', 'bulan', 'tahun'));
        //dd($master);
        //dd(compact('loan', 'no_acc', 'id_pt', 'user'));
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

        $master = DB::table('public.tblpsaklbueffective')
        ->where('no_branch', $id_pt)
        ->get();

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->setCellValue('A2', 'Entitiy Name');
$sheet->getStyle('A2')->getFont()->setBold(true);
$entitiyName = 'PT. PACIFIC MULTI FINANCE';
$sheet->setCellValue('B2', $entitiyName);
        $sheet->setCellValue('A3', 'Branch Number');
        $sheet->getStyle('A3')->getFont()->setBold(true); // Set bold untuk Branch Number
        $sheet->setCellValue('B3', $loan->no_branch);
        $sheet->setCellValue('A4', 'Branch Name');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Branch Name
        $sheet->setCellValue('B4', $loan->deb_name);
        $sheet->setCellValue('A5', 'GL Group');
        $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk GL Group
        $sheet->setCellValue('B5', number_format($loan->org_bal, 2));
        $sheet->setCellValue('A6', 'Date Of Report');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bold untuk Date Of Report
        $sheet->setCellValue('B6', date('Y-m-d', strtotime($loan->org_date)));


        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Outstanding Simple Interest - Report Details');
        $sheet->mergeCells('A10:J10'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Set judul kolom tabel
        $headers = ['No','Entity Number','Account Number','Debitor Name','GL Account','Loan Type','GL Group','Original Date','Term (Months)','Maturity Date','Interest Rate','EIR Armotised Cost Exposure','EIR Amortised Cost Calculated','Currennt Balance','Carrying Amount','Outstanding Receivable','Outstanding Interest','Unamortized Transation Cost','Unamortized UpFront Fee','Unearned Interest Income'];
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
        $totalOutstandingInterest = 0;
        $outstandingReceivable = 0;
        $outstandingInterest = 0;
        $totalUnamortCost = 0;
        $totalUnamortFee = 0;
        $totalInterestIncome = 0;
        $totalOutstandingReceivable = 0;
        $nourut =0;
            foreach ($master as $loan){
            $trxcost = $loan->trxcost; 
            $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
            $trxcostFloat = (float)$trxcost;
            $outstandingInterest = $loan->bilint ?? 0;
            $totalOutstandingInterest += $outstandingInterest;
            $amortized = $loan->cum_amortisecost; // Ambil nilai amortized dari laporan
                // Hitung nilai unamortized
                if ($row == 13) {
                    // Untuk baris pertama, gunakan nilai trxcost
                    $unamortCost = $trxcostFloat;;
                } else {
                    // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                    $unamortCost = $trxcostFloat - $amortized;
                }
            $totalUnamortCost += $unamortCost;

            $prov = $loan->prov; // Ambil nilai dari database
            // Hapus simbol mata uang dan pemisah ribuan
            $prov = preg_replace('/[^\d.]/', '', $prov);
            // Konversi ke float
            $provFloat = (float)$prov* -1;
            $amortizedUpFrontFee = $loan->cum_amortisefee;

            // Hitung nilai unamortized Fee
            if ($row == 13) {
            $unamortFee = $provFloat;
            } else {
            $unamortFee = $provFloat + $amortizedUpFrontFee;
            }
            $totalUnamortFee += $unamortFee;

            $bunga = $loan->cum_bunga;
            $totalInterestIncome += $loan->cum_bunga;
            // hitung nilai unaerned interest income
                if ($row == 13) {
                        $interestIncome = $totalInterestIncome;
                    } else {
                        $totalInterestIncome -= $bunga;
                        $interestIncome = $totalInterestIncome;
            }

            $bilint = $loan->bilint;
            $bilprn = $loan->bilprn;
            $outstandingReceivable = $bilprn + $bilint;
            $totalOutstandingReceivable += $outstandingReceivable;
            $nourut += 1;
       
            $sheet->setCellValue('A' . $row, $nourut);
            $sheet->setCellValue('B' . $row, date('Y-m-d', strtotime($loan->no_branch)));
            $sheet->setCellValue('C' . $row, $loan->no_acc);
            $sheet->setCellValue('D' . $row, $loan->deb_name);
            $sheet->setCellValue('E' . $row, $loan->coa);
            $sheet->setCellValue('F' . $row, $loan->ln_type);
            $sheet->setCellValue('G' . $row, $loan->GROUP);
            $sheet->setCellValue('H' . $row, $loan->org_date_dt);
            $sheet->setCellValue('I' . $row, $loan->term);
            $sheet->setCellValue('J' . $row, $loan->mtr_date_dt);
            $sheet->setCellValue('K' . $row, $loan->rate*100);
            $sheet->setCellValue('L' . $row, $loan->eirex*100);
            $sheet->setCellValue('M' . $row, $loan->eircalc*100);
            $sheet->setCellValue('N' . $row, $loan->cbal);
            $sheet->setCellValue('O' . $row, $loan->carrying_amount);
            $sheet->setCellValue('P' . $row, $loan->$outstandingReceivable);
            $sheet->setCellValue('Q' . $row, $loan->bilint);
            $sheet->setCellValue('R' . $row, $unamortCost);
            $sheet->setCellValue('S' . $row, $unamortFee);
            $sheet->setCellValue('T' . $row, $loan->cum_bunga);

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
        $filename = "outstanding_simple_interest_report_$no_acc.xlsx";

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
$sheet->setCellValue('A3', 'Branch Number');
$sheet->getStyle('A3')->getFont()->setBold(true); // Set bold untuk Branch Number
        $sheet->setCellValue('B3', $loan->no_branch);
        $sheet->setCellValue('A4', 'Branch Name');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Branch Name
        $sheet->setCellValue('B4', $loan->deb_name);
        $sheet->setCellValue('A5', 'GL Group');
        $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk GL Group
        $sheet->setCellValue('B5', number_format($loan->org_bal, 2));
        $sheet->setCellValue('A6', 'Date Of Report');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bold untuk Date Of Report
        $sheet->setCellValue('B6', date('Y-m-d', strtotime($loan->org_date)));

    // Set judul tabel laporan
    $sheet->setCellValue('A10', 'Accrual Interest Report - Report Details');
    $sheet->mergeCells('A10:J10'); // Menggabungkan sel untuk judul tabel
    $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A10')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
    $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

    // Set judul kolom tabel
    $headers =['No','Entity Number','Account Number','Debitor Name','GL Account','Loan Type','GL Group','Original Date','Term (Months)','Maturity Date','Interest Rate','EIR Armotised Cost Exposure','EIR Amortised Cost Calculated','Currennt Balance','Carrying Amount','Outstanding Receivable','Outstanding Interest','Unamortized Transation Cost','Unamortized UpFront Fee','Unearned Interest Income'];
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
      $totalOutstandingInterest = 0;
      $outstandingReceivable = 0;
      $outstandingInterest = 0;
      $totalUnamortCost = 0;
      $totalUnamortFee = 0;
      $totalInterestIncome = 0;
      $totalOutstandingReceivable = 0;
      $nourut =0;
          foreach ($master as $loan){
          $trxcost = $loan->trxcost; 
          $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
          $trxcostFloat = (float)$trxcost;
          $outstandingInterest = $loan->bilint ?? 0;
          $totalOutstandingInterest += $outstandingInterest;
          $amortized = $loan->cum_amortisecost; // Ambil nilai amortized dari laporan
              // Hitung nilai unamortized
              if ($row == 13) {
                  // Untuk baris pertama, gunakan nilai trxcost
                  $unamortCost = $trxcostFloat;;
              } else {
                  // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                  $unamortCost = $trxcostFloat - $amortized;
              }
          $totalUnamortCost += $unamortCost;

          $prov = $loan->prov; // Ambil nilai dari database
          // Hapus simbol mata uang dan pemisah ribuan
          $prov = preg_replace('/[^\d.]/', '', $prov);
          // Konversi ke float
          $provFloat = (float)$prov* -1;
          $amortizedUpFrontFee = $loan->cum_amortisefee;

          // Hitung nilai unamortized Fee
          if ($row == 13) {
          $unamortFee = $provFloat;
          } else {
          $unamortFee = $provFloat + $amortizedUpFrontFee;
          }
          $totalUnamortFee += $unamortFee;

          $bunga = $loan->cum_bunga;
          $totalInterestIncome += $loan->cum_bunga;
          // hitung nilai unaerned interest income
              if ($row == 13) {
                      $interestIncome = $totalInterestIncome;
                  } else {
                      $totalInterestIncome -= $bunga;
                      $interestIncome = $totalInterestIncome;
          }

          $bilint = $loan->bilint;
          $bilprn = $loan->bilprn;
          $outstandingReceivable = $bilprn + $bilint;
          $totalOutstandingReceivable += $outstandingReceivable;
          $nourut += 1;
     
          $sheet->setCellValue('A' . $row, $nourut);
          $sheet->setCellValue('B' . $row, date('Y-m-d', strtotime($loan->no_branch)));
          $sheet->setCellValue('C' . $row, $loan->no_acc);
          $sheet->setCellValue('D' . $row, $loan->deb_name);
          $sheet->setCellValue('E' . $row, $loan->coa);
          $sheet->setCellValue('F' . $row, $loan->ln_type);
          $sheet->setCellValue('G' . $row, $loan->GROUP);
          $sheet->setCellValue('H' . $row, $loan->org_date_dt);
          $sheet->setCellValue('I' . $row, $loan->term);
          $sheet->setCellValue('J' . $row, $loan->mtr_date_dt);
          $sheet->setCellValue('K' . $row, $loan->rate*100);
          $sheet->setCellValue('L' . $row, $loan->eirex*100);
          $sheet->setCellValue('M' . $row, $loan->eircalc*100);
          $sheet->setCellValue('N' . $row, $loan->cbal);
          $sheet->setCellValue('O' . $row, $loan->carrying_amount);
          $sheet->setCellValue('P' . $row, $loan->$outstandingReceivable);
          $sheet->setCellValue('Q' . $row, $loan->bilint);
          $sheet->setCellValue('R' . $row, $unamortCost);
          $sheet->setCellValue('S' . $row, $unamortFee);
          $sheet->setCellValue('T' . $row, $loan->cum_bunga);

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
    $filename = "outstanding_simple_interest_report_$no_acc.pdf";

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

public function exportCsv($no_acc, $id_pt)
{
    // Ambil data loan dan reports
    $loan = report_simpleinterest::getLoanDetails(trim($no_acc), trim($id_pt));
    $reports = report_simpleinterest::getReportsByNoAcc(trim($no_acc), trim($id_pt));

    // Cek apakah data loan dan reports ada
    if (!$loan || $reports->isEmpty()) {
        return response()->json(['message' => 'No data found for the given account number.'], 404);
    }

    // Siapkan data CSV
    $csvData = [];
    $csvData[] = ['Branch Number', $loan->no_acc];
    $csvData[] = ['Branch Name', $loan->deb_name];
    $csvData[] = ['GL Group', number_format($loan->org_bal, 2)];
    $csvData[] = ['Date Of Report', date('Y-m-d', strtotime($loan->org_date))];
    $csvData[] = [];
    $csvData[] = ['Accrual Interest Report - Report Details'];
    $csvData[] = ['Bulanke', 'Tgl Angsuran', 'Hari Bunga', 'PMT Amt', 'Penarikan', 'Pengembalian', 'Bunga', 'Balance', 'Time Gap', 'Outs Amt Conv'];

    // Mengisi data laporan ke dalam CSV
    foreach ($reports as $report) {
        $csvData[] = [
            $report->bulanke,
            date('Y-m-d', strtotime($report->tglangsuran)),
            $report->haribunga,
            number_format($report->pmtamt, 2),
            number_format($report->penarikan, 2),
            number_format($report->pengembalian, 2),
            number_format($report->bunga, 2),
            number_format($report->balance, 2),
            $report->timegap,
            number_format($report->outsamtconv, 2)
        ];
    }

    // Siapkan nama file
    $filename = "accrual_interest_report_$no_acc.csv";

    // Buat file CSV
    $handle = fopen('php://output', 'w');
    ob_start();
    foreach ($csvData as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
    $csvContent = ob_get_clean();

    // Kembalikan response CSV
    return response($csvContent)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', "attachment; filename=\"$filename\"");
}
}
