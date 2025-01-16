<?php

namespace App\Http\Controllers\report\Report_Outstanding;

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
use Illuminate\Support\Facades\Log;

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
        return view('report.outstanding.effective.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view(Request $request, $id_pt)
    {
        if (!$id_pt) {
            abort(404, 'Invalid ID');
        }
    
        // Validate if the authenticated user has access to this `id_pt`'
        if ($id_pt != Auth::user()->id_pt) {
            abort(403, 'Unauthorized');
        }
        // $loan = report_effective::getLoanDetailsbyidpt(trim($id_pt));
        // $corporateLoans = report_simpleinterest::getCorporateLoans($id_pt);
        // $reports = report_simpleinterest::getReportsByNoAcc(trim($id_pt));
        // $reports = report_effective::getLoanDetailsbyidpt(trim($id_pt));

        $user = Auth::user();

        if (!$user) {
            return redirect('https://psak.pramatech.id');
        }

        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        $isSuperAdmin = $user->role === 'superadmin';

        $master = DB::table('public.tblpsaklbueffective')
        ->where('no_branch', $id_pt)
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderBy('tblpsaklbueffective.no_acc', 'asc')
        ->get();

        // dd($master);

        // return view('report.outstanding.effective.view', compact('master', 'loan','loanfirst','loanjoin'));
        return view('report.outstanding.effective.view', compact('master', 'bulan', 'tahun' ,'isSuperAdmin', "user"));
    }

    public function exportExcel(Request $request, $id_pt)
    {
        // Ambil data loan dan reports
        $user_id_pt = Auth::user()->id_pt;
    
        $namaBulan = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    
        $bulan = $request->input('bulan', date('n')); // This will be 1-12
        $tahun = $request->input('tahun', date('Y'));
    
    
        $master = DB::table('public.tblpsaklbueffective')
        ->join('public.CABANG-', 'tblpsaklbueffective.no_branch', '=', 'CABANG-.jdbr')
        ->where('tblpsaklbueffective.no_branch', $id_pt)
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderBy('tblpsaklbueffective.no_acc', 'asc')
        ->get();
    
        $bulan = $namaBulan[$bulan];
    
        if ($master->isEmpty()) {
            // Return a more detailed error message
            return response()->json([
                'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
                'details' => [
                    'branch' => $id_pt,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]
            ], 404);
        }
    
        $loanFirst = $master->first();
        $bulanAngka =  $request->input('bulan', date('n'));
    
    
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->setCellValue('A2', 'Entity Number');
        $sheet->getStyle('A2')->getFont()->setBold(true); 
        $sheet->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C2', $loanFirst->no_branch);
        $sheet->setCellValue('A3', 'Entitiy Name');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        // $entitiyName = 'PT. PACIFIC MULTI FINANCE';
        $sheet->setCellValue('C3', $loanFirst->jdname);
        // $sheet->setCellValue('A4', 'Branch Number');
        // $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Branch Number
        // $sheet->setCellValue('B4', " ".$loanFirst->no_acc );
        // $sheet->setCellValue('A5', 'Branch Name');
        // $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk Branch Name
        // $sheet->setCellValue('B5', $loanFirst->deb_name);
        // $sheet->setCellValue('A4', 'GL Group');
        // $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk GL Group
        // $sheet->setCellValue('B4', number_format($loanFirst->org_bal, 2));
        $sheet->setCellValue('A4', 'Date Of Report');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untukDate Of Report
        // $sheet->setCellValue('B5', date('Y-m-d', strtotime($loanFirst->org_date)));
        // $periode = $bulan . ' - ' . $tahun; // Concatenate with a hyphen
        $sheet->setCellValue('B4', $bulan . ' - ' . $tahun); // Set the concatenated period value
 
        // Set judul tabel laporan
        $sheet->setCellValue('A6', 'Outstanding Effective Report - Report Details');
        $sheet->mergeCells('A6:V6'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');
  
        $sheet->getRowDimension(7)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = ['No', 'Branch Number', 'Account Number','Debitor Name ','GL Account','Loan Type','GL Group','Original Date', 'Term (Months)', 'Maturity Date','Interest Rate','Payment Amount','EIR Amortised Cost Exposure','EIR Amortised Cost Calculated','Current Balance', 'Carrying Amount', 'Outstanding Receivable','Outstanding Interest','Cumulative Time Gap','Unamortized Transaction Cost','Unamortized UpFront Fee', 'Unearned Interest Income'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '8', $header);
            $sheet->getStyle($columnIndex . '8')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '8')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '8')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 9; // Mulai dari baris 13 untuk data laporan

        $totalOutstandingReceivable = 0;
        $totalOutstandingInterest = 0;
        $outstandingReceivable = 0;
        $outstandingInterest = 0;
        $totalUnamortCost = 0;
        $totalUnamortFee = 0;
        $totalInterestIncome = 0;
        $nourut = 0;

            foreach ($master as $loan){

            $trxcost = $loan->trxcost; 
            $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
            $trxcostFloat = (float)$trxcost;
            $outstandingInterest = $loan->bilint ?? 0;
            $totalOutstandingInterest += $outstandingInterest;
            $amortized = $loan->cum_amortisecost; // Ambil nilai amortized dari laporan
                // Hitung nilai unamortized
                if ($row == 9) {
                    // Untuk baris pertama, gunakan nilai trxcost
                    $unamortCost = $trxcostFloat;
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
            $unamortFee = $loan->prov * -1 + $loan->cum_amortisefee;
            $totalUnamortFee += $unamortFee;

            $bunga = $loan->cum_bunga;
            $totalInterestIncome += $loan->cum_bunga;
            // hitung nilai unaerned interest income
            //    if ($row == 9) {
            //            $interestIncome = $totalInterestIncome;
            //        } else {
            //            $totalInterestIncome -= $bunga;
            //            $interestIncome = $totalInterestIncome;
            //}

            $bilint = $loan->bilint;
            $bilprn = $loan->bilprn;
            $outstandingReceivable = $bilprn + $bilint;
            $totalOutstandingReceivable += $outstandingReceivable;
            $nourut += 1;

         $sheet->getStyle('A' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('A' . $row, $nourut);
         $sheet->getStyle('B' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('B' . $row, $loan->no_branch);
         $sheet->getStyle('C' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('C' . $row, " " . $loan->no_acc);
         $sheet->getStyle('D' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('D' . $row, $loan->deb_name);
         $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('E' . $row, $loan->coa);
         $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('F' . $row, $loan->ln_type);
         $sheet->getStyle('G' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('G' . $row, $loan->GROUP);
         $sheet->getStyle('H' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($loan->org_date_dt)));
         $sheet->getStyle('I' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('I' . $row, $loan->term);
         $sheet->getStyle('J' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('J' . $row, date('d/m/Y', strtotime($loan->mtr_date_dt)));
         $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('K' . $row, number_format($loan->rate*100,5). '%');
         $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('L' . $row, number_format($loan->pmtamt));
         $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('M' . $row, ($loan->eirex*100). '%');
         $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('N' . $row, ($loan->eircalc*100) . '%');
         $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('O' . $row, number_format($loan->cbal));
         $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('P' . $row, number_format($loan->carrying_amount));
         $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('Q' . $row, number_format($outstandingReceivable));
         $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('R' . $row, number_format($loan->bilint));
         $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('S' . $row, number_format($loan->cum_timegap));
         $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('T' . $row, number_format($unamortCost));
         $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('U' . $row, number_format($unamortFee));
         $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('V' . $row, number_format($loan->cum_bunga));
            


            // Mengatur font menjadi bold untuk setiap baris data
            $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        //TOTAL EXCEL
        $sheet->setCellValue('A' . $row, "TOTAL:");
        $sheet->mergeCells('A' . $row . ':J' . $row); // Merge cells A to J for the Total row
        $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $row, number_format($master->avg('rate')*100, 5).'%');
        $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('L' . $row, number_format($master->sum('pmtamt')));
        $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('M' . $row, number_format($master->avg('eirex')*100, 14).'%');
        $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('N' . $row, number_format($master->avg('eircalc')*100, 14) . '%');
        $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('O' . $row, number_format($master->sum('cbal')));
        $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('P' . $row, number_format($master->sum('carrying_amount')));
        $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('Q' . $row, number_format($totalOutstandingReceivable));
        $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('R' . $row, number_format($master->sum('bilint')));
        $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('S' . $row, number_format($master->sum('cum_timegap')));
        $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('T' . $row, number_format($totalUnamortCost));
        $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('U' . $row, number_format($totalUnamortFee));
        $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('V' . $row, number_format($totalInterestIncome));
           


           // Mengatur font menjadi bold untuk setiap baris data
           $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);

           // Menambahkan warna latar belakang alternatif pada baris data
               $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
               $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris

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
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(24);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(14);
        $sheet->getColumnDimension('L')->setWidth(18);
        $sheet->getColumnDimension('M')->setWidth(18);
        $sheet->getColumnDimension('N')->setWidth(18);
        $sheet->getColumnDimension('O')->setWidth(18);
        $sheet->getColumnDimension('P')->setWidth(18);
        $sheet->getColumnDimension('Q')->setWidth(18);
        $sheet->getColumnDimension('R')->setWidth(18);
        $sheet->getColumnDimension('S')->setWidth(18);
        $sheet->getColumnDimension('T')->setWidth(18);
        $sheet->getColumnDimension('U')->setWidth(18);
        $sheet->getColumnDimension('V')->setWidth(18);

        // Set border untuk header tabel
        $sheet->getStyle('A6:V6')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A8:V' . $row )->applyFromArray($styleArray);

        // // Mengatur lebar kolom agar lebih rapi
        // foreach (range('A', 'V') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        // Siapkan nama file
        //$filename = "{$id_pt}_PSAKLBUEffective_{$tahun}_{$bulanAngka}.xlsx";
        $filename = "ReportOutstandingBalanceEffective_{$id_pt}_{$bulan}_{$tahun}.xlsx";

        // Buat writer dan simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        // Kembalikan response Excel
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }




    // Method untuk mengekspor data ke PDF
    public function exportPdf(Request $request, $id_pt)
{
    $user_id_pt = Auth::user()->id_pt;
    
    $namaBulan = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];

    $bulan = $request->input('bulan', date('n')); // This will be 1-12
    $tahun = $request->input('tahun', date('Y'));


    $master = DB::table('public.tblpsaklbueffective')
    ->join('public.CABANG-', 'tblpsaklbueffective.no_branch', '=', 'CABANG-.jdbr')
    ->where('tblpsaklbueffective.no_branch', $id_pt)
    ->where('bulan', $bulan)
    ->where('tahun', $tahun)
    ->orderBy('tblpsaklbueffective.no_acc', 'asc')
    ->get();

    $bulan = $namaBulan[$bulan];

    if ($master->isEmpty()) {
        // Return a more detailed error message
        return response()->json([
            'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
            'details' => [
                'branch' => $id_pt,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]
        ], 404);
    }
    $loanFirst = $master->first();
    $bulanAngka =  $request->input('bulan', date('n'));


    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);


    // Set informasi pinjaman
    $sheet->setCellValue('A2', 'Entity Number');
    $sheet->getStyle('A2')->getFont()->setBold(true); 
    $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('B2', $loanFirst->no_branch);
    $sheet->setCellValue('A3', 'Entitiy Name');
    $sheet->getStyle('A3')->getFont()->setBold(true);
    // $entitiyName = 'PT. PACIFIC MULTI FINANCE';
    $sheet->setCellValue('B3', $loanFirst->jdname);
        // $sheet->setCellValue('A4', 'Branch Number');
        // $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Branch Number
        // $sheet->setCellValue('B4', $loanFirst->no_acc);
        // $sheet->setCellValue('A5', 'Branch Name');
        // $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk Branch Name
        // $sheet->setCellValue('B5', $loanFirst->deb_name);
        // $sheet->setCellValue('A4', 'GL Group');
        // $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk GL Group
        // $sheet->setCellValue('B4', number_format($loanFirst->org_bal, 2));
        $sheet->setCellValue('A4', 'Date Of Report');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untukDate Of Report
        // $periode = $bulan . ' - ' . $tahun; // Concatenate with a hyphen
        $sheet->setCellValue('B4', $bulan . ' - ' . $tahun); // Set the concatenated period value
        // $sheet->setCellValue('B5', date('Y-m-d', strtotime($loanFirst->org_date)));
 
    // Set judul tabel laporan
    $sheet->setCellValue('A6', 'Outstanding Effective Report - Report Details');
    $sheet->mergeCells('A6:V6'); // Menggabungkan sel untuk judul tabel
    $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
    $sheet->getStyle('A6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

    // Set judul kolom tabel
    $headers = ['No', 'Branch Number', 'Account Number','Debitor Name ','GL Account','Loan Type','GL Group','Original Date', 'Term (Months)', 'Maturity Date','Interest Rate','Payment Amount','EIR Amortised Cost Exposure','EIR Amortised Cost Calculated','Current Balance', 'Carrying Amount', 'Outstanding Receivable','Outstanding Interest','Cumulative Time Gap','Unamortized Transaction Cost','Unamortized UpFront Fee', 'Unearned Interest Income'];
    $columnIndex = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($columnIndex . '8', $header);
        $sheet->getStyle($columnIndex . '8')->getFont()->setBold(true);
        $sheet->getStyle($columnIndex . '8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($columnIndex . '8')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($columnIndex . '8')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($columnIndex . '8')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
        $sheet->getStyle($columnIndex . '8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $columnIndex++;
    }

     // Mengisi data laporan ke dalam tabel
     $row = 11; // Mulai dari baris 13 untuk data laporan
     $sheet->getStyle('A8:V8')->getAlignment()->setWrapText(true);

     $totalOutstandingReceivable = 0;
     $totalOutstandingInterest = 0;
     $outstandingReceivable = 0;
     $outstandingInterest = 0;
     $totalUnamortCost = 0;
     $totalUnamortFee = 0;
     $totalInterestIncome = 0;
     $nourut = 0;

         foreach ($master as $loan){

         $trxcost = $loan->trxcost; 
         $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
         $trxcostFloat = (float)$trxcost;
         $outstandingInterest = $loan->bilint ?? 0;
         $totalOutstandingInterest += $outstandingInterest;
         $amortized = $loan->cum_amortisecost; // Ambil nilai amortized dari laporan
             // Hitung nilai unamortized
             if ($row == 9) {
                 // Untuk baris pertama, gunakan nilai trxcost
                 $unamortCost = $trxcostFloat;
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
         if ($row == 9) {
         $unamortFee = $provFloat;
         } else {
         $unamortFee = $provFloat + $amortizedUpFrontFee;
         }
         $totalUnamortFee += $unamortFee;

         $bunga = $loan->cum_bunga;
         $totalInterestIncome += $loan->cum_bunga;
         // hitung nilai unaerned interest income
         //    if ($row == 9) {
         //            $interestIncome = $totalInterestIncome;
         //        } else {
         //            $totalInterestIncome -= $bunga;
         //            $interestIncome = $totalInterestIncome;
         //}

         $bilint = $loan->bilint;
         $bilprn = $loan->bilprn;
         $outstandingReceivable = $bilprn + $bilint;
         $totalOutstandingReceivable += $outstandingReceivable;
         $nourut += 1;
         $sheet->getStyle('A' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('A' . $row, $nourut);
         $sheet->getStyle('B' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('B' . $row, $loan->no_branch);
         $sheet->getStyle('C' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('C' . $row, " " . $loan->no_acc);
         $sheet->getStyle('D' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
         $sheet->setCellValue('D' . $row, $loan->deb_name);
         $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('E' . $row, $loan->coa);
         $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('F' . $row, $loan->ln_type);
         $sheet->getStyle('G' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('G' . $row, $loan->GROUP);
         $sheet->getStyle('H' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($loan->org_date_dt)));
         $sheet->getStyle('I' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('I' . $row, $loan->term);
         $sheet->getStyle('J' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('J' . $row, date('d/m/Y', strtotime($loan->mtr_date_dt)));
         $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('K' . $row, number_format($loan->rate*100,5). '%');
         $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('L' . $row, number_format($loan->pmtamt));
         $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('M' . $row, ($loan->eirex*100). '%');
         $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('N' . $row, ($loan->eircalc*100) . '%');
         $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('O' . $row, number_format($loan->cbal));
         $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('P' . $row, number_format($loan->carrying_amount));
         $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('Q' . $row, number_format($outstandingReceivable));
         $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('R' . $row, number_format($loan->bilint));
         $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('S' . $row, number_format($loan->cum_timegap));
         $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('T' . $row, number_format($unamortCost));
         $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('U' . $row, number_format($unamortFee));
         $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
         $sheet->setCellValue('V' . $row, number_format($loan->cum_bunga));

        // Mengatur font menjadi bold untuk setiap baris data
        $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);

        // Menambahkan warna latar belakang alternatif pada baris data
        if ($row % 2 == 0) {
            $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
        }

        $row++;
    }
    //TOTAL PDF
    $sheet->setCellValue('A' . $row, "TOTAL:");
    $sheet->mergeCells('A' . $row . ':J' . $row); // Merge cells A to J for the Total row
    $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('K' . $row, number_format($master->avg('rate')*100, 5).'%');
    $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('L' . $row, number_format($master->sum('pmtamt')));
    $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('M' . $row, number_format($master->avg('eirex')*100, 14).'%');
    $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('N' . $row, number_format($master->avg('eircalc')*100, 14) . '%');
    $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('O' . $row, number_format($master->sum('cbal')));
    $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('P' . $row, number_format($master->sum('carrying_amount')));
    $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('Q' . $row, number_format($totalOutstandingReceivable));
    $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('R' . $row, number_format($master->sum('bilint')));
    $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('S' . $row, number_format($master->sum('cum_timegap')));
    $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('T' . $row, number_format($totalUnamortCost));
    $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('U' . $row, number_format($totalUnamortFee));
    $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('V' . $row, number_format($totalInterestIncome));
       


       // Mengatur font menjadi bold untuk setiap baris data
       $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);

       // Menambahkan warna latar belakang alternatif pada baris data
   
        $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris


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
    $sheet->getStyle('A6:V6')->applyFromArray($styleArray);

    // Set border untuk semua data laporan
    $sheet->getStyle('A8:V' . $row)->applyFromArray($styleArray);

    // Mengatur lebar kolom agar lebih rapi
    // foreach (range('A', 'V') as $columnID) {
    //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
    // }

    // Siapkan nama file
    //$filename = "{$id_pt}_PSAKLBUEffective_{$tahun}_{$bulanAngka}.pdf";
    $filename = "ReportOutstandingBalanceEffective_{$bulan}_{$tahun}.pdf";

    // Set pengaturan untuk PDF
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);

    // Siapkan direktori untuk menyimpan file sementara
    $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');

    // Simpan file PDF
    $writer->save($temp_file);

    // Kembalikan response PDF
    return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
}
public function exportCsv(Request $request, $id_pt)
{
    // Ambil data loan dan reports
    $user_id_pt = Auth::user()->id_pt;
    
    $namaBulan = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];

    $bulan = $request->input('bulan', date('n')); // This will be 1-12
    $tahun = $request->input('tahun', date('Y'));


    $master = DB::table('public.tblpsaklbueffective')
    ->join('public.CABANG-', 'tblpsaklbueffective.no_branch', '=', 'CABANG-.jdbr')
    ->where('tblpsaklbueffective.no_branch', $id_pt)
    ->where('bulan', $bulan)
    ->where('tahun', $tahun)
    ->orderBy('tblpsaklbueffective.no_acc', 'asc')
    ->get();

    $bulan = $namaBulan[$bulan];

    if ($master->isEmpty()) {
        // Return a more detailed error message
        return response()->json([
            'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
            'details' => [
                'branch' => $id_pt,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]
        ], 404);
    }

    $loanFirst = $master->first();
    $bulanAngka =  $request->input('bulan', date('n'));

    // Siapkan data CSV
    //$csvData[] = ['Outstanding Effective Report - Report Details'];
    $csvData[] = ['Entity Number', 'Account Number','Debitor Name', 'EIR Exposure', 'Current Balance', 'Carrying Amount', 'Outstanding Interest', 'Unamortized Transaction Cost','Unamortized UpFront Fee','Unearned Interest Income'];

    $row = 1; // Mulai dari baris 13 untuk data laporan
    $totalInterestIncome = 0;
    $nourut = 0;
    // Mengisi data laporan ke dalam CSV
    foreach ($master as $loan) {
        
        $trxcost = $loan->trxcost; 
        $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
        $trxcostFloat = (float)$trxcost;
        $amortized = $loan->cum_amortisecost; // Ambil nilai amortized dari laporan
            // Hitung nilai unamortized
            if ($row === 1) {
                // Untuk baris pertama, gunakan nilai trxcost
                $unamortCost = $trxcostFloat;
            } else {
                // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                $unamortCost = $trxcostFloat - $amortized;
            }

        $prov = $loan->prov; // Ambil nilai dari database
        // Hapus simbol mata uang dan pemisah ribuan
        $prov = preg_replace('/[^\d.]/', '', $prov);
        // Konversi ke float
        $provFloat = (float)$prov* -1;
        $amortizedUpFrontFee = $loan->cum_amortisefee;

        // Hitung nilai unamortized Fee
        $unamortFee = $loan->prov * -1 + $loan->cum_amortisefee;
        $bunga = $loan->cum_bunga;
        $totalInterestIncome += $loan->cum_bunga;
        // hitung nilai unaerned interest income
       //     if ($row == 9) {
         //           $interestIncome = $totalInterestIncome;
           //     } else {
           //         $totalInterestIncome -= $bunga;
           //         $interestIncome = $totalInterestIncome;
      //  }
        $nourut += 1;
        $row++;
        $csvData[] = [
            //$nourut,
            $loan->no_branch,
            $loan->no_acc,
            $loan->deb_name,
            number_format($loan->eirex, 5 ?? 0),
            number_format($loan->cbal, 2 ?? 0),
            number_format($loan->carrying_amount, 2 ?? 0),
            number_format($loan->bilint, 2 ?? 0),
            number_format($unamortCost, 2 ?? 0),            
            number_format(abs($unamortFee), 2 ?? 0),
            number_format($loan->cum_bunga, 2 ?? 0)
        ];
    }

    // Siapkan nama file
    $filename = "PSAKLBUEffective_{$bulan}_{$tahun}.csv";

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

    public function checkData($no_acc, $id_pt)
    {
        try {
            $id_pt = Auth::user()->id_pt;

            $loan = report_effective::getLoanDetailsbyidpt($id_pt);
            $loanjoin = report_effective::getLoanjoinByIdPt($id_pt);
            $loanfirst = $loan->first();
            $master = report_effective::getMasterByIdPt($id_pt);

            if (!$loan) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data loan tidak ditemukan'
                ]);
            }

            if (!$master) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data master tidak ditemukan'
                ]);
            }

            if (!$loanjoin) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data loan join tidak ditemukan'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => [
                    'loan' => $loan,
                    'master' => $master,
                    'loanjoin' => $loanjoin,
                    'loanfirst' => $loanfirst
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
