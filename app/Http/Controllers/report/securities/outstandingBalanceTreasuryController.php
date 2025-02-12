<?php

namespace App\Http\Controllers\report\securities;

use App\Models\report_securities;
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
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Dompdf\Dompdf;
use Dompdf\Options;

class outstandingBalanceTreasuryController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get parameters from request with defaults
        $id_pt = $user->id_pt;
        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('n');
        $tanggal = $request->input('tanggal') ?? date('d');
        $no_acc = $request->input('no_acc');
        $status = $request->input('status', '2');

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $securities = report_securities::getOutstandingFVTOCISecurities(
        intval($user->id_pt),
        intval($tahun),
        intval($bulan),
        intval($tanggal)
        );

        // Convert to Collection
        $securities = collect($securities);

        $selectedDate = "$tahun-$bulan-$tanggal";
        $securities = $securities->filter(function ($loan) use ($selectedDate) {
            // Format kedua tanggal ke 'Y-m-d' untuk membandingkan hanya tanggal saja
            $loanDate = date('Y-m-d', strtotime(explode(' ', $loan->transac_dt)[0]));
            return $loanDate == $selectedDate;
        });


        $securities = $securities->sortBy('no_acc');

        $count = $securities->count();

        // dd($id_pt, $bulan, $tahun, $status);
        // dd($securities);

        $securities = new LengthAwarePaginator(
            $securities->forPage($page, $perPage),
            $securities->count(),
            $perPage,
            $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );
        return view('report.securities.report_outstanding_balance_treasury_bond.master',
            compact('securities', 'tahun', 'bulan', 'tanggal', 'user', 'page', 'perPage', 'count')
        );
    }

    public function executeStoredProcedure(Request $request)
    {
        try {
            // Validate the hidden inputs just to be safe
            $request->validate([
                'tahun' => 'required|integer',
                'bulan' => 'required|integer|min:1|max:12',
                'tanggal' => 'required|integer|min:1|max:31',
            ]);

            $user = Auth::user();
            $id_pt = $user->id_pt;

            DB::transaction(function () use ($request, $id_pt) {
                DB::select("CALL securities.spcreatemastersecurities_daily(?, ?, ?, ?)", [
                    $request->tahun,
                    $request->bulan,
                    $request->tanggal,
                    $id_pt
                ]);

                // Execute first stored procedure
                DB::select("CALL securities.spevaluationtreasury_bonds(?, ?, ?, ?)", [
                    $request->tahun,
                    $request->bulan,
                    $request->tanggal,
                    $id_pt
                ]);

                // Execute second stored procedure
                DB::select("CALL securities.spoutbaltreasury_daily_bonds(?, ?, ?, ?)", [
                    $id_pt,
                    $request->tahun,
                    $request->bulan,
                    $request->tanggal
                ]);
            });

            return redirect()->back()->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Stored procedures berhasil dijalankan',
                'icon' => 'success'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error executing stored procedures: ' . $e->getMessage());
            return redirect()->back()->with('swal', [
                'title' => 'Error!',
                'text' => 'Gagal menjalankan stored procedures: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akunn
    public function view($no_acc, $id_pt)
    {

        $no_acc = trim($no_acc);
        $loan = report_securities::getLoanDetails($no_acc, $id_pt);
        $master=report_securities::getMasterDataByNoAcc($no_acc, $id_pt);
        $reports = report_securities::getReportsByNoAcc($no_acc, $id_pt);
        if (!$loan) {
            abort(404, 'Loan not found');
        }

        // dd($loan, $master, $reports);

        return view('report.securities.report_outstanding_balance_treasury_bond.view', compact('loan', 'reports','master'));
    }

    public function exportExcel(Request $request, $id_pt)
    {
        $user = Auth::user();

        // Get parameters from request with defaults
        $id_pt = $user->id_pt;
        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('n');
        $tanggal = $request->input('tanggal') ?? date('d');
        $no_acc = $request->input('no_acc');
        $status = $request->input('status', '2');

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $securities = report_securities::getOutstandingFVTOCISecurities(
        intval($user->id_pt),
        intval($tahun),
        intval($bulan),
        intval($tanggal),
        );

        // Convert to Collection
        $securities = collect($securities);

        $selectedDate = "$tahun-$bulan-$tanggal";
        $securities = $securities->filter(function ($loan) use ($selectedDate) {
            // Format kedua tanggal ke 'Y-m-d' untuk membandingkan hanya tanggal saja
            $loanDate = date('Y-m-d', strtotime(explode(' ', $loan->transac_dt)[0]));
            return $loanDate == $selectedDate;
        });

        $securities = $securities->sortBy('no_acc');

        $count = $securities->count();

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

        $entityName = DB::table('securities.tblpsaklbutreasury')
        ->join('public.tbl_pt', 'tblpsaklbutreasury.id_pt', '=', 'tbl_pt.id_pt')
        ->where('tblpsaklbutreasury.id_pt', $id_pt)
        ->select('tbl_pt.nama_pt')
        ->first();

        $bulan = $namaBulan[$bulan];

        $bulanAngka =  $request->input('bulan', date('n'));

        if ($securities->isEmpty()) {
            // Return a more detailed error message
            return response()->json([
                'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
                'details' => [
                    'branch' => $id_pt,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'tanggal' => $tanggal
                ]
            ], 404);
        }

        // // Cek apakah data loan dan reports ada
        // if (!$loan || $reports->isEmpty()) {
        //     return response()->json(['message' => 'No data found for the given account number.'], 404);
        // }

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
            ['Entity Number', ': ' . $user->id_pt],
            ['Entity Name', ': ' . $entityName->nama_pt ],
            ['Date of Report', ': ' . $tanggal . ' - ' . $bulan . ' - ' . $tahun],
        ];


        $currentRow = 2;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $currentRow, $info[0]);
            $sheet->setCellValue('B' . $currentRow, $info[1]);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getRowDimension($currentRow)->setRowHeight(15);
            $currentRow++;
        }

        // $sheet->mergeCells('A2:B2');
        // $sheet->mergeCells('A3:B3');
        // $sheet->mergeCells('A4:B4');
        // $sheet->mergeCells('A5:B5');
        // $sheet->mergeCells('A6:B6');
        // $sheet->mergeCells('A7:B7');
        // $sheet->mergeCells('A8:B8');
        // $sheet->mergeCells('C2:D2');
        // $sheet->mergeCells('C3:D3');
        // $sheet->mergeCells('C4:D4');
        // $sheet->mergeCells('C5:D5');
        // $sheet->mergeCells('C6:D6');
        // $sheet->mergeCells('C7:D7');
        // $sheet->mergeCells('C8:D8');


        // Set judul tabel laporan
    $sheet->setCellValue('A7', 'Report Outstanding - Treasury Bonds');
    $sheet->mergeCells('A7:X7'); // Menggabungkan sel untuk judul tabel
    $sheet->getStyle('A7')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('A7')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A7:X7')->getFill()->getStartColor()->setARGB('8359A3'); // Warna latar belakang
    $sheet->getStyle('A7')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

    // Set judul kolom tabel
    $headers = [
        'No',
        'Branch Number',
        'Account Number',
        'Bond ID',
        'Issuer Name',
        'GL Account',
        'Bond Type',
        'GL Group',
        'Settlement Date',
        'Tenor (TTM)',
        'Maturity Date',
        'Coupon Rate',
        'Yield (YTM)',
        'EIR Amortized Cost Exposure',
        'EIR Amortized Cost Calculated',
        'Face Value',
        'Price',
        'Mark to Market (MTM)',
        'Carrying Amount',
        'Unamortized At Discount',
        'Unamortized At Premium',
        'Unamortized Brokerage Fee',
        'Cummlative Time Gap',
        'Unreleased Gain/Losses'
    ];
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
    $row = 9; // Mulai dari baris 14 untuk data laporan
    $nourut = 0;
    foreach ($securities as $report) {
        $nourut = $nourut + 1;

        // Mengisi data ke dalam kolom
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A' . $row, $nourut);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B' . $row, $report->no_branch );
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('C' . $row, "'" . $report->no_acc );
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, $report->bond_id);
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E' . $row, $report->issuer_name);
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('F' . $row, $report->coa );
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('G' . $row, $report->bond_type);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . $row, $report->gl_group);
        $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('I' . $row, date('d/m/Y', strtotime($report->org_date_dt)));
        $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J' . $row, ($report->tenor) . ' days');
        $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('K' . $row,  date('d/m/Y', strtotime($report->mtr_date_dt)));
        $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L' . $row, number_format($report->coupon_rate*100,5).'%');
        $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M' . $row, number_format($report->yield*100,5).'%');
        $sheet->getStyle('N' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N' . $row, number_format($report->eirex*100,14).'%');
        $sheet->getStyle('O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('O' . $row, number_format($report->eircalc*100,14).'%');
        $sheet->getStyle('P' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('P' . $row, number_format((float) str_replace(['$', ','], '',$report->face_value)));
        $sheet->getStyle('Q' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('Q' . $row, number_format($report->price, 5));
        $sheet->getStyle('R' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('R' . $row, number_format((float) str_replace(['$', ','], '', $report->mtm_price)));
        $sheet->getStyle('S' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('S' . $row, number_format((float) str_replace(['$', ','], '',$report->carrying_amount)));
        $sheet->getStyle('T' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('T' . $row, '-' . number_format((float) str_replace(['$', ','], '', $report->atdiscount - $report->cum_amortise_disc)));
        $sheet->getStyle('U' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('U' . $row, number_format((float) str_replace(['$', ','], '',$report->atpremium - $report->cum_amortise_prem )));
        $sheet->getStyle('V' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('V' . $row, number_format((float) str_replace(['$', ','], '',$report->brokerage - $report->cum_amortise_brok)));
        $sheet->getStyle('W' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('W' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_timegap)));
        $sheet->getStyle('X' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('X' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_gain_losses)));

        $row++; // Pindah ke baris berikutnya
      }

    $sumFaceValue = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->face_value));
    $sumMtmPrice = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->mtm_price));
    $sumCarryingAmount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->carrying_amount));
    $sumAtdiscount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atdiscount));
    $sumAtpremium = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atpremium));
    $sumBrokerage = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->brokerage));
    $sumTimegap = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_timegap));
    $sumGainLoss = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_gain_losses));

      //TOTAL OUTSTANDING SECURITIES
      $sheet->setCellValue('A' . $row, "TOTAL");
      $sheet->mergeCells('A' . $row . ':K' . $row);
      $sheet->getStyle('A' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('L' . $row, number_format($securities->avg('coupon_rate')*100,5).'%');
      $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('M' . $row, number_format($securities->avg('yield')*100,5).'%');
      $sheet->getStyle('N' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('N' . $row, number_format($securities->avg('eirex')*100,14).'%');
      $sheet->getStyle('O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('O' . $row, number_format($securities->avg('eircalc')*100,14).'%');
      $sheet->getStyle('P' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('P' . $row, number_format($sumFaceValue));
      $sheet->getStyle('Q' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('Q' . $row, number_format($securities->sum('price'), 5));
      $sheet->getStyle('R' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('R' . $row, number_format($sumMtmPrice));
      $sheet->getStyle('S' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('S' . $row, number_format($sumCarryingAmount));
      $sheet->getStyle('T' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('T' . $row, '-' . number_format($sumAtdiscount));
      $sheet->getStyle('U' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('U' . $row, number_format($sumAtpremium));
      $sheet->getStyle('V' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('V' . $row, number_format($sumBrokerage));
      $sheet->getStyle('W' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('W' . $row, number_format($sumTimegap));
      $sheet->getStyle('X' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('X' . $row, number_format($sumGainLoss));

      $sheet->getStyle('A' . $row . ':X' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F81BD');
      $sheet->getStyle('A' . $row . ':X' . $row)->getFont()->setBold(true);

    // //Tabel ke 2
    // // Set judul kolom tabel
    // $headers = [
    //     'Yield (YTM)',
    //     'EIR Amortized Cost Exposure',
    //     'EIR Amortized Cost Calculated',
    //     'Face Value',
    //     'Price',
    //     'Mark to Market (MTM)',
    //     'Carrying Amount',
    //     'Unamortized At Discount',
    //     'Unamortized At Premium',
    //     'Unamortized Brokerage Fee',
    //     'Cummlative Time Gap',
    //     'Unreleased Gain/Losses'
    // ];
    // $columnIndex = 'A';
    // foreach ($headers as $header) {
    //     $sheet->setCellValue($columnIndex . '13', $header);
    //     $sheet->getStyle($columnIndex . '13')->getFont()->setBold(true);
    //     $sheet->getStyle($columnIndex . '13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->getStyle($columnIndex . '13')->getFill()->setFillType(Fill::FILL_SOLID);
    //     $sheet->getStyle($columnIndex . '13')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
    //     $sheet->getStyle($columnIndex . '13')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
    //     $columnIndex++;
    // }

    // $row = 14; // Mulai dari baris 18 untuk data laporan
    // foreach ($securities as $report) {


    //     $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->setCellValue('A' . $row, number_format($report->yield*100,5).'%');
    //     $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->setCellValue('B' . $row, number_format($report->eirex*100,14).'%');
    //     $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->setCellValue('C' . $row, number_format($report->eircalc*100,14).'%');
    //     $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('D' . $row, number_format((float) str_replace(['$', ','], '',$report->face_value)));
    //     $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('E' . $row, number_format($report->price, 5));
    //     $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('F' . $row, number_format((float) str_replace(['$', ','], '', $report->mtm_price)));
    //     $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('G' . $row, number_format((float) str_replace(['$', ','], '',$report->carrying_amount)));
    //     $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('H' . $row, '-' . number_format((float) str_replace(['$', ','], '', $report->atdiscount)));
    //     $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('I' . $row, number_format((float) str_replace(['$', ','], '',$report->atpremium)));
    //     $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('J' . $row, number_format((float) str_replace(['$', ','], '',$report->brokerage)));
    //     $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('K' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_timegap)));
    //     $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('L' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_gain_losses)));

    //     $row++; // Pindah ke baris berikutnya
    // }

    // $sumFaceValue = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->face_value));
    // $sumMtmPrice = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->mtm_price));
    // $sumCarryingAmount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->carrying_amount));
    // $sumAtdiscount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atdiscount));
    // $sumAtpremium = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atpremium));
    // $sumBrokerage = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->brokerage));
    // $sumTimegap = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_timegap));
    // $sumGainLoss = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_gain_losses));

    //   //TOTAL OUTSTANDING
    //   $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //   $sheet->setCellValue('A' . $row, number_format($securities->avg('yield')*100,5).'%');
    //   $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //   $sheet->setCellValue('B' . $row, number_format($securities->avg('eirex')*100,14).'%');
    //   $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //   $sheet->setCellValue('C' . $row, number_format($securities->avg('eircalc')*100,14).'%');
    //   $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('D' . $row, number_format($sumFaceValue));
    //   $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('E' . $row, number_format($securities->sum('price'), 5));
    //   $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('F' . $row, number_format($sumMtmPrice));
    //   $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('G' . $row, number_format($sumCarryingAmount));
    //   $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('H' . $row, number_format($sumAtdiscount));
    //   $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('I' . $row, number_format($sumAtpremium));
    //   $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('J' . $row, number_format($sumBrokerage));
    //   $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('K' . $row, number_format($sumTimegap));
    //   $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('L' . $row, number_format($sumGainLoss));

    //   $sheet->getStyle('A' . $row . ':L' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F81BD');
    //   $sheet->getStyle('A' . $row . ':L' . $row)->getFont()->setBold(true);

    //   foreach (range('A', 'I') as $columnID) {
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

    // Set border untuk header tabel
    $sheet->getStyle('A7:X7')->applyFromArray($styleArray);

    // Set border untuk semua data laporan
    $sheet->getStyle('A7:X' . $row)->applyFromArray($styleArray);

    //Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'X') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // $sheet->getColumnDimension('A')->setWidth(30);
    // $sheet->getColumnDimension('B')->setWidth(30);
    // $sheet->getColumnDimension('C')->setWidth(30);
    // $sheet->getColumnDimension('D')->setWidth(30);
    // $sheet->getColumnDimension('E')->setWidth(30);
    // $sheet->getColumnDimension('F')->setWidth(30);
    // $sheet->getColumnDimension('G')->setWidth(30);
    // $sheet->getColumnDimension('H')->setWidth(30);
    // $sheet->getColumnDimension('I')->setWidth(30);
    // $sheet->getColumnDimension('J')->setWidth(30);
    // $sheet->getColumnDimension('K')->setWidth(30);
    // $sheet->getColumnDimension('L')->setWidth(30);


        // Siapkan nama file
        $filename = "ReportOutstandingBalanceTreasuryBond_{$id_pt}_{$tanggal}_{$bulan}_{$tahun}.xlsx";

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
    $user = Auth::user();

        // Get parameters from request with defaults
        $id_pt = $user->id_pt;
        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('n');
        $tanggal = $request->input('tanggal') ?? date('d');
        $no_acc = $request->input('no_acc');
        $status = $request->input('status', '2');

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $securities = report_securities::getOutstandingFVTOCISecurities(
        intval($user->id_pt),
        intval($tahun),
        intval($bulan),
        intval($tanggal),
        );

        // Convert to Collection
        $securities = collect($securities);

        $selectedDate = "$tahun-$bulan-$tanggal";
        $securities = $securities->filter(function ($loan) use ($selectedDate) {
            // Format kedua tanggal ke 'Y-m-d' untuk membandingkan hanya tanggal saja
            $loanDate = date('Y-m-d', strtotime(explode(' ', $loan->transac_dt)[0]));
            return $loanDate == $selectedDate;
        });

        $securities = $securities->sortBy('no_acc');

        $count = $securities->count();

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

    $entityName = DB::table('securities.tblpsaklbutreasury')
    ->join('public.tbl_pt', 'tblpsaklbutreasury.id_pt', '=', 'tbl_pt.id_pt')
    ->where('tblpsaklbutreasury.id_pt', $id_pt)
    ->select('tbl_pt.nama_pt')
    ->first();

    $bulan = $namaBulan[$bulan];

    $bulanAngka =  $request->input('bulan', date('n'));

    if ($securities->isEmpty()) {
        // Return a more detailed error message
        return response()->json([
            'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
            'details' => [
                'branch' => $id_pt,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'tanggal' => $tanggal
            ]
        ], 404);
    }

    // // Cek apakah data loan dan reports ada
    // if (!$loan || $reports->isEmpty()) {
    //     return response()->json(['message' => 'No data found for the given account number.'], 404);
    // }

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
        ['Entity Number', ': ' . $user->id_pt],
        ['Entity Name', ': ' . $entityName->nama_pt ],
        ['Date of Report', ': ' . $tanggal . ' - ' . $bulan . ' - ' . $tahun],
    ];


    $currentRow = 2;
    foreach ($infoRows as $info) {
        $sheet->setCellValue('A' . $currentRow, $info[0]);
        $sheet->setCellValue('B' . $currentRow, $info[1]);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
    }

    // $sheet->mergeCells('A2:B2');
    // $sheet->mergeCells('A3:B3');
    // $sheet->mergeCells('A4:B4');
    // $sheet->mergeCells('A5:B5');
    // $sheet->mergeCells('A6:B6');
    // $sheet->mergeCells('A7:B7');
    // $sheet->mergeCells('A8:B8');
    // $sheet->mergeCells('C2:D2');
    // $sheet->mergeCells('C3:D3');
    // $sheet->mergeCells('C4:D4');
    // $sheet->mergeCells('C5:D5');
    // $sheet->mergeCells('C6:D6');
    // $sheet->mergeCells('C7:D7');
    // $sheet->mergeCells('C8:D8');


    // Set judul tabel laporan
    $sheet->setCellValue('A7', 'Report Outstanding - Treasury Bonds');
    $sheet->mergeCells('A7:X7'); // Menggabungkan sel untuk judul tabel
    $sheet->getStyle('A7')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('A7')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A7:X7')->getFill()->getStartColor()->setARGB('8359A3'); // Warna latar belakang
    $sheet->getStyle('A7')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

    // Set judul kolom tabel
    $headers = [
        'No',
        'Branch Number',
        'Account Number',
        'Bond ID',
        'Issuer Name',
        'GL Account',
        'Bond Type',
        'GL Group',
        'Settlement Date',
        'Tenor (TTM)',
        'Maturity Date',
        'Coupon Rate',
        'Yield (YTM)',
        'EIR Amortized Cost Exposure',
        'EIR Amortized Cost Calculated',
        'Face Value',
        'Price',
        'Mark to Market (MTM)',
        'Carrying Amount',
        'Unamortized At Discount',
        'Unamortized At Premium',
        'Unamortized Brokerage Fee',
        'Cummlative Time Gap',
        'Unreleased Gain/Losses'
    ];
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
    $row = 9; // Mulai dari baris 14 untuk data laporan
    $nourut = 0;
    foreach ($securities as $report) {
        $nourut = $nourut + 1;

        // Mengisi data ke dalam kolom
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A' . $row, $nourut);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B' . $row, $report->no_branch );
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('C' . $row, "'" . $report->no_acc );
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, $report->bond_id);
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E' . $row, $report->issuer_name);
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('F' . $row, $report->coa );
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('G' . $row, $report->bond_type);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . $row, $report->gl_group);
        $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('I' . $row, date('d/m/Y', strtotime($report->org_date_dt)));
        $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J' . $row, ($report->tenor) . ' days');
        $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('K' . $row,  date('d/m/Y', strtotime($report->mtr_date_dt)));
        $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L' . $row, number_format($report->coupon_rate*100,5).'%');
        $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M' . $row, number_format($report->yield*100,5).'%');
        $sheet->getStyle('N' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N' . $row, number_format($report->eirex*100,14).'%');
        $sheet->getStyle('O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('O' . $row, number_format($report->eircalc*100,14).'%');
        $sheet->getStyle('P' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('P' . $row, number_format((float) str_replace(['$', ','], '',$report->face_value)));
        $sheet->getStyle('Q' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('Q' . $row, number_format($report->price, 5));
        $sheet->getStyle('R' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('R' . $row, number_format((float) str_replace(['$', ','], '', $report->mtm_price)));
        $sheet->getStyle('S' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('S' . $row, number_format((float) str_replace(['$', ','], '',$report->carrying_amount)));
        $sheet->getStyle('T' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('T' . $row, '-' . number_format((float) str_replace(['$', ','], '', $report->atdiscount)));
        $sheet->getStyle('U' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('U' . $row, number_format((float) str_replace(['$', ','], '',$report->atpremium)));
        $sheet->getStyle('V' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('V' . $row, number_format((float) str_replace(['$', ','], '',$report->brokerage)));
        $sheet->getStyle('W' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('W' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_timegap)));
        $sheet->getStyle('X' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('X' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_gain_losses)));

        $row++; // Pindah ke baris berikutnya
      }

    $sumFaceValue = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->face_value));
    $sumMtmPrice = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->mtm_price));
    $sumCarryingAmount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->carrying_amount));
    $sumAtdiscount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atdiscount));
    $sumAtpremium = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atpremium));
    $sumBrokerage = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->brokerage));
    $sumTimegap = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_timegap));
    $sumGainLoss = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_gain_losses));

      //TOTAL OUTSTANDING SECURITIES
      $sheet->setCellValue('A' . $row, "TOTAL");
      $sheet->mergeCells('A' . $row . ':K' . $row);
      $sheet->getStyle('A' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('L' . $row, number_format($securities->avg('coupon_rate')*100,5).'%');
      $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('M' . $row, number_format($securities->avg('yield')*100,5).'%');
      $sheet->getStyle('N' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('N' . $row, number_format($securities->avg('eirex')*100,14).'%');
      $sheet->getStyle('O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValue('O' . $row, number_format($securities->avg('eircalc')*100,14).'%');
      $sheet->getStyle('P' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('P' . $row, number_format($sumFaceValue));
      $sheet->getStyle('Q' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('Q' . $row, number_format($securities->sum('price'), 5));
      $sheet->getStyle('R' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('R' . $row, number_format($sumMtmPrice));
      $sheet->getStyle('S' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('S' . $row, number_format($sumCarryingAmount));
      $sheet->getStyle('T' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('T' . $row, '-' . number_format($sumAtdiscount));
      $sheet->getStyle('U' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('U' . $row, number_format($sumAtpremium));
      $sheet->getStyle('V' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('V' . $row, number_format($sumBrokerage));
      $sheet->getStyle('W' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('W' . $row, number_format($sumTimegap));
      $sheet->getStyle('X' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->setCellValue('X' . $row, number_format($sumGainLoss));

      $sheet->getStyle('A' . $row . ':X' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F81BD');
      $sheet->getStyle('A' . $row . ':X' . $row)->getFont()->setBold(true);

    // //Tabel ke 2
    // // Set judul kolom tabel
    // $headers = [
    //     'Yield (YTM)',
    //     'EIR Amortized Cost Exposure',
    //     'EIR Amortized Cost Calculated',
    //     'Face Value',
    //     'Price',
    //     'Mark to Market (MTM)',
    //     'Carrying Amount',
    //     'Unamortized At Discount',
    //     'Unamortized At Premium',
    //     'Unamortized Brokerage Fee',
    //     'Cummlative Time Gap',
    //     'Unreleased Gain/Losses'
    // ];
    // $columnIndex = 'A';
    // foreach ($headers as $header) {
    //     $sheet->setCellValue($columnIndex . '13', $header);
    //     $sheet->getStyle($columnIndex . '13')->getFont()->setBold(true);
    //     $sheet->getStyle($columnIndex . '13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->getStyle($columnIndex . '13')->getFill()->setFillType(Fill::FILL_SOLID);
    //     $sheet->getStyle($columnIndex . '13')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
    //     $sheet->getStyle($columnIndex . '13')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
    //     $columnIndex++;
    // }

    // $row = 14; // Mulai dari baris 18 untuk data laporan
    // foreach ($securities as $report) {


    //     $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->setCellValue('A' . $row, number_format($report->yield*100,5).'%');
    //     $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->setCellValue('B' . $row, number_format($report->eirex*100,14).'%');
    //     $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->setCellValue('C' . $row, number_format($report->eircalc*100,14).'%');
    //     $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('D' . $row, number_format((float) str_replace(['$', ','], '',$report->face_value)));
    //     $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('E' . $row, number_format($report->price, 5));
    //     $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('F' . $row, number_format((float) str_replace(['$', ','], '', $report->mtm_price)));
    //     $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('G' . $row, number_format((float) str_replace(['$', ','], '',$report->carrying_amount)));
    //     $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('H' . $row, '-' . number_format((float) str_replace(['$', ','], '', $report->atdiscount)));
    //     $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('I' . $row, number_format((float) str_replace(['$', ','], '',$report->atpremium)));
    //     $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('J' . $row, number_format((float) str_replace(['$', ','], '',$report->brokerage)));
    //     $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('K' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_timegap)));
    //     $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     $sheet->setCellValue('L' . $row, number_format((float) str_replace(['$', ','], '',$report->cum_gain_losses)));

    //     $row++; // Pindah ke baris berikutnya
    // }

    // $sumFaceValue = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->face_value));
    // $sumMtmPrice = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->mtm_price));
    // $sumCarryingAmount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->carrying_amount));
    // $sumAtdiscount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atdiscount));
    // $sumAtpremium = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->atpremium));
    // $sumBrokerage = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->brokerage));
    // $sumTimegap = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_timegap));
    // $sumGainLoss = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_gain_losses));

    //   //TOTAL OUTSTANDING
    //   $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //   $sheet->setCellValue('A' . $row, number_format($securities->avg('yield')*100,5).'%');
    //   $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //   $sheet->setCellValue('B' . $row, number_format($securities->avg('eirex')*100,14).'%');
    //   $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //   $sheet->setCellValue('C' . $row, number_format($securities->avg('eircalc')*100,14).'%');
    //   $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('D' . $row, number_format($sumFaceValue));
    //   $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('E' . $row, number_format($securities->sum('price'), 5));
    //   $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('F' . $row, number_format($sumMtmPrice));
    //   $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('G' . $row, number_format($sumCarryingAmount));
    //   $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('H' . $row, number_format($sumAtdiscount));
    //   $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('I' . $row, number_format($sumAtpremium));
    //   $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('J' . $row, number_format($sumBrokerage));
    //   $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('K' . $row, number_format($sumTimegap));
    //   $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //   $sheet->setCellValue('L' . $row, number_format($sumGainLoss));

    //   $sheet->getStyle('A' . $row . ':L' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F81BD');
    //   $sheet->getStyle('A' . $row . ':L' . $row)->getFont()->setBold(true);

    //   foreach (range('A', 'I') as $columnID) {
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

    // Set border untuk header tabel
    $sheet->getStyle('A7:X7')->applyFromArray($styleArray);

    // Set border untuk semua data laporan
    $sheet->getStyle('A7:X' . $row)->applyFromArray($styleArray);

    //Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'X') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // $sheet->getColumnDimension('A')->setWidth(30);
    // $sheet->getColumnDimension('B')->setWidth(30);
    // $sheet->getColumnDimension('C')->setWidth(30);
    // $sheet->getColumnDimension('D')->setWidth(30);
    // $sheet->getColumnDimension('E')->setWidth(30);
    // $sheet->getColumnDimension('F')->setWidth(30);
    // $sheet->getColumnDimension('G')->setWidth(30);
    // $sheet->getColumnDimension('H')->setWidth(30);
    // $sheet->getColumnDimension('I')->setWidth(30);
    // $sheet->getColumnDimension('J')->setWidth(30);
    // $sheet->getColumnDimension('K')->setWidth(30);
    // $sheet->getColumnDimension('L')->setWidth(30);

    // Siapkan nama file
    $filename = "ReportOutstandingBalanceTreasuryBond_{$id_pt}_{$tanggal}_{$bulan}_{$tahun}.pdf";

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

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

public function exportCSV(Request $request, $id_pt)
{
    $user = Auth::user();

    // Get parameters from request with defaults
    $id_pt = $user->id_pt;
    $tahun = $request->input('tahun') ?? date('Y');
    $bulan = $request->input('bulan') ?? date('n');
    $tanggal = $request->input('tanggal') ?? date('d');

    // Ambil data securities
    $securities = report_securities::getOutstandingFVTOCISecurities(
        intval($user->id_pt),
        intval($tahun),
        intval($bulan),
        intval($tanggal)
    );

    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers sesuai dengan yang diminta
    $headers = [
        'ID',
        'NO_ACC',
        'NO_BRANCH',
        'BOND_ID',
        'ISSUER_NAME',
        'RATING',
        'MTR_DATE_DT',
        'COUPON_RATE',
        'FACE_VALUE',
        'CARRYING AMOUNT',
        'UNRELEAZED GAIN / (LOSSES)'
    ];

    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }

    // Isi data
    $row = 2;
    foreach ($securities as $index => $security) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $security->no_acc);
        $sheet->setCellValue('C' . $row, $security->no_branch);
        $sheet->setCellValue('D' . $row, $security->bond_id);
        $sheet->setCellValue('E' . $row, $security->issuer_name);
        $sheet->setCellValue('F' . $row, $security->rating);
        $sheet->setCellValue('G' . $row, $security->mtr_date_dt);
        $sheet->setCellValue('H' . $row, $security->coupon_rate);
        $sheet->setCellValue('I' . $row, $security->face_value);
        $sheet->setCellValue('J' . $row, $security->carrying_amount);
        $sheet->setCellValue('K' . $row, $security->gain_losses);
        $row++;
    }

    // Auto-size columns
    foreach (range('A', 'K') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Siapkan nama file
    $filename = "ReportOutstandingBalanceFVTOCI_{$id_pt}_{$tanggal}_{$bulan}_{$tahun}.csv";

    // Buat writer CSV dan set konfigurasi
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
    $writer->setDelimiter(',');
    $writer->setEnclosure('"');
    $writer->setLineEnding("\r\n");
    $writer->setSheetIndex(0);

    // Simpan ke temporary file
    $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
    $writer->save($temp_file);

    // Kembalikan response dengan header untuk CSV
    return response()->download($temp_file, $filename, [
        'Content-Type' => 'text/csv',
    ])->deleteFileAfterSend(true);
}
}
