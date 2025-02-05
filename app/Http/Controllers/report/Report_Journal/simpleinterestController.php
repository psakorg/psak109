<?php

namespace App\Http\Controllers\report\Report_Journal;

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
        $user = Auth::user();
        $id_pt = $user->id_pt;
        
        // Get parameters from request
        $perPage = $request->input('per_page', 10);
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
//        $jenis = $request->input('jenis', 'Initial Recognition'); // Default value sesuai DB

        $isSuperAdmin = $user->role === 'superadmin';
        
        // Query menggunakan whereRaw untuk case-insensitive matching
        $master = DB::table('tbljournal_corporateloan_total')
            ->where('branch_no', $id_pt)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            //->whereRaw('LOWER(TRIM(jenis)) = ?', [strtolower(trim($jenis))])
            //->whereRaw('LOWER(TRIM(jenis)) <> ?', [strtolower(trim('0'))])
            ->paginate($perPage);

            return view('report.journal.simple_interest.master', compact('master', 'bulan', 'tahun', 'isSuperAdmin', 'user'));
    }


    public function executeStoredProcedure(Request $request)
    {
        try {
            // Validate the hidden inputs just to be safe
            $request->validate([
                'tahun' => 'required|integer',
                'bulan' => 'required|integer|min:1|max:12',
            ]);

            $user = Auth::user();
            $id_pt = $user->id_pt;


            DB::transaction(function () use ($request, $id_pt) {
                DB::select("CALL public.spcreatejournalcorporatefinal(?, ?, ?)", [
                    $id_pt,
                    $request->bulan,
                    $request->tahun
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

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc,$id_pt)
    {
        $no_acc = trim($no_acc);
        $loan = report_simpleinterest::getLoanDetails($no_acc,$id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc,$id_pt);

        if (!$loan) {
            abort(404, 'Loan not found');
        }


        return view('report.journal.simple_interest.view', compact('loan', 'reports'));
    }

    public function exportExcel(Request $request,$id_pt)
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

    $master = DB::table('public.tbljournal_corporateloan_total')
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderBy('tbljournal_corporateloan_total.id_jrnl', 'asc')
        ->orderBy('tbljournal_corporateloan_total.post', 'asc')
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


    // // Set informasi pinjaman
     $sheet->setCellValue('A2', 'Branch Number');
     $sheet->getStyle('A2')->getFont()->setBold(true);
     $sheet->setCellValue('B2', $loanFirst->branch_no);
     $sheet->setCellValue('A3', 'Branch Name');
     $sheet->getStyle('A3')->getFont()->setBold(true);
     $sheet->setCellValue('B3', $loanFirst->branch_name);
     $sheet->setCellValue('A4', 'Date Of Report');
     $sheet->getStyle('A4')->getFont()->setBold(true);
     $sheet->setCellValue('B4', $bulan . ' - ' . $tahun);
     $sheet->getStyle('B2:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    // Set judul tabel laporan
    $sheet->setCellValue('A6', 'Journal Simple Interest Report - Report Details');
    $sheet->mergeCells('A6:G6'); // Menggabungkan sel untuk judul tabel
    $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
    $sheet->getStyle('A6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

    // Set judul kolom tabel
    $headers = ['No','Entity Number','GL Account', 'Description', 'Debit', 'Credit','Posting Date'];
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
    $nourut = 0;
    $totalDebit = 0;
    $totalCredit = 0;
    foreach ($master as $loan) {
        // Hitung total debit dan credit
        if ($loan->post == 'D') {
            $totalDebit += $loan->amount;
        } else if ($loan->post == 'C') {
            $totalCredit += $loan->amount;
        }
        $nourut += 1;
        $sheet->getStyle('A' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A' . $row, $nourut);
        $sheet->getStyle('B' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B' . $row, $loan->branch_no);
        $sheet->getStyle('C' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('C' . $row, $loan->id_coa);
        $sheet->getStyle('D' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, $loan->deskripsi);
        // $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->setCellValue('E' . $row, $loan->post);
        $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, $loan->post == 'D' ? number_format($loan->amount, 2) : '' );
        $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, $loan->post == 'C' ? number_format($loan->amount, 2) : '' );
        $sheet->getStyle('G' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($loan->post_date)));

        // Mengatur font menjadi bold untuk setiap baris data
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);

        // Menambahkan warna latar belakang alternatif pada baris data
        if ($row % 2 == 0) {
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
        }

        $row++;
    }
    //TOTAL PDF
    $sheet->setCellValue('A' . $row, "TOTAL:");
    $sheet->mergeCells('A' . $row . ':D' . $row); // Merge cells A to E
    $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('E' . $row, number_format($totalDebit, 2));
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('F' . $row, number_format($totalCredit, 2));
    $sheet->setCellValue('H' . $row, '');

    // Mengatur font menjadi bold untuk setiap baris data
    $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);

    // Menambahkan warna latar belakang alternatif pada baris data
    $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang untuk baris genap
    $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
           
    // Mengatur border untuk tabel
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
        ],
    ];

    // Set border untuk header tabell
    //$sheet->getStyle('A8:H8')->applyFromArray($styleArray);

    // Set border untuk semua data laporan
    //$sheet->getStyle('A8:H' . $row)->applyFromArray($styleArray);

    // Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
        // Siapkan nama file
        //$filename = "{$id_pt}_PSAKLBUCorporateloan_{$tahun}_{$bulanAngka}.xlsx";
        $filename = "ReportJournalCorporateLoan_{$bulan}_{$tahun}.xlsx";

        // Buat writer dan simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        // Kembalikan response Excel
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }


    public function exportReportExcel(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
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
        
        $master = DB::table('public.tbljournal_corporateloan_total')
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderBy('tbljournal_corporateloan_total.id_jrnl', 'asc')
        ->orderBy('tbljournal_corporateloan_total.post', 'asc')
        ->get();
        
        $bulan = $namaBulan[$bulan];
        
        if ($master->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang sesuai dengan kriteria yang dipilih untuk:
                Branch: ' . $id_pt . ',
                Bulan: ' . $bulan . ',
                Tahun: ' . $tahun);
        }
        
        $loanFirst = $master->first();
        $bulanAngka =  $request->input('bulan', date('n'));

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->setCellValue('A2', 'Entity Number');
        $sheet->getStyle('A2')->getFont()->setBold(true); 
        $sheet->setCellValue('B2', $loanFirst->branch_no);
        $sheet->setCellValue('A3', 'Entitiy Name');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->setCellValue('B3', $loanFirst->branch_name);
        $sheet->setCellValue('A4', 'Date Of Report');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Date Of Report
        $sheet->setCellValue('B4', $bulan . ' - ' . $tahun);


        // Set judul tabel laporan
        $sheet->setCellValue('A6', 'Journal Report - Simple Interest');
        $sheet->mergeCells('A6:G6'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(5);

        // Set judul kolom tabel
        $headers = ['No','Entity Number','GL Account','Description','Debit','Credit','Posting Date'];
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

        // Mengisi data laporan ke dalam tabel
        $totalDebit = 0;
        $totalCredit = 0;
        $txtpost = 'C';
        $nourut = 1;
        foreach ($master as $loan){
            $sheet->getStyle('A' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $row, $nourut);
            $sheet->getStyle('B' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, $loan->branch_no ?? 0);
            $sheet->getStyle('C' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('C' . $row, " ".$loan->id_coa ?? 0);
            $sheet->getStyle('D' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('D' . $row, $loan->deskripsi ?? 0);
            $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // $txtpost = $loan->post;
            // $sheet->setCellValue('E' . $row, $txtpost ?? 0);
            if ($txtpost == 'D') {
                $sheet->setCellValue('E' . $row, number_format($loan->amount, 0));
                $totalDebit += $loan->amount;
            }
            else {     
                $sheet->setCellValue('F' . $row, number_format($loan->amount, 0));
                $totalCredit += $loan->amount;
            }
            $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // $sheet->getStyle('H' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($loan->post_date ?? 0)));

            // Mengatur font menjadi bold untuk setiap baris data
            $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }
            $row++;
        }
        
        //TOTAL EXCEL
        $sheet->setCellValue('A' . $row, "TOTAL:");
        $sheet->mergeCells('A' . $row . ':D' . $row); // Merge cells A to J for the Total row
        $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($totalDebit,0) ?? 0);
        $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($totalCredit,0) ?? 0);

        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);

        // Menambahkan warna latar belakang alternatif pada baris data
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris 

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
        $sheet->getStyle('A6:G6')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A8:G' . ($row))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan nama file
        //$filename = "{$id_pt}_PSAKLBUCorporateloan_{$tahun}_{$bulanAngka}.xlsx";
        $filename = "ReportJournalCorporateLoan_{$bulan}_{$tahun}.xlsx";

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

    $master = DB::table('public.tbljournal_corporateloan_total')
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderBy('tbljournal_corporateloan_total.id_jrnl', 'asc')
        ->orderBy('tbljournal_corporateloan_total.post', 'asc')
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


    // // Set informasi pinjaman
     $sheet->setCellValue('A2', 'Branch Number');
     $sheet->getStyle('A2')->getFont()->setBold(true);
     $sheet->setCellValue('B2', $loanFirst->branch_no);
     $sheet->setCellValue('A3', 'Branch Name');
     $sheet->getStyle('A3')->getFont()->setBold(true);
     $sheet->setCellValue('B3', $loanFirst->branch_name);
     $sheet->setCellValue('A4', 'Date Of Report');
     $sheet->getStyle('A4')->getFont()->setBold(true);
     $sheet->setCellValue('B4', $bulan . ' - ' . $tahun);
     $sheet->getStyle('B2:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    // Set judul tabel laporan
    $sheet->setCellValue('A6', 'Journal Simple Interest Report - Report Details');
    $sheet->mergeCells('A6:G6'); // Menggabungkan sel untuk judul tabel
    $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
    $sheet->getStyle('A6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

    // Set judul kolom tabel
    $headers = ['No','Entity Number','GL Account', 'Description', 'Debit', 'Credit','Posting Date'];
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
    $nourut = 0;
    $totalDebit = 0;
    $totalCredit = 0;
    foreach ($master as $loan) {
        // Hitung total debit dan credit
        if ($loan->post == 'D') {
            $totalDebit += $loan->amount;
        } else if ($loan->post == 'C') {
            $totalCredit += $loan->amount;
        }
        $nourut += 1;
        $sheet->getStyle('A' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A' . $row, $nourut);
        $sheet->getStyle('B' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B' . $row, $loan->branch_no);
        $sheet->getStyle('C' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('C' . $row, $loan->id_coa);
        $sheet->getStyle('D' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, $loan->deskripsi);
        // $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->setCellValue('E' . $row, $loan->post);
        $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, $loan->post == 'D' ? number_format($loan->amount, 2) : '' );
        $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, $loan->post == 'C' ? number_format($loan->amount, 2) : '' );
        $sheet->getStyle('G' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($loan->post_date)));

        // Mengatur font menjadi bold untuk setiap baris data
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);

        // Menambahkan warna latar belakang alternatif pada baris data
        if ($row % 2 == 0) {
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
        }

        $row++;
    }
    //TOTAL PDF
    $sheet->setCellValue('A' . $row, "TOTAL:");
    $sheet->mergeCells('A' . $row . ':D' . $row); // Merge cells A to E
    $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('E' . $row, number_format($totalDebit, 2));
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('F' . $row, number_format($totalCredit, 2));
    $sheet->setCellValue('H' . $row, '');

    // Mengatur font menjadi bold untuk setiap baris data
    $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);

    // Menambahkan warna latar belakang alternatif pada baris data
    $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang untuk baris genap
    $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
           
    // Mengatur border untuk tabel
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
        ],
    ];

    // Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Siapkan nama file
    $filename = "ReportJournalCorporateLoan_{$id_pt}_{$bulan}_{$tahun}.pdf";

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


    $master = DB::table('public.tbljournal_corporateloan')
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderBy('tbljournal_corporateloan.id_jrnl', 'asc')
        ->orderBy('tbljournal_corporateloan.post', 'asc')
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
    $csvData[] = ['Entity Number','GL Account', 'Acount Number', 'Description', 'Debit', 'Credit', 'Posting Date'];

    $row = 1; // Mulai dari baris 13 untuk data laporan
    $nourut = 0;
    // Mengisi data laporan ke dalam CSV
    foreach ($master as $loan) {
        $nourut += 1;
        $row++;
        $csvData[] = [
            $loan->branch_no,
            $loan->id_coa,
            $loan->acct_no,
            $loan->deskripsi,
            ($loan->post == 'D') ? number_format($loan->amount, 2) : '0',
            ($loan->post == 'C') ? number_format($loan->amount, 2) : '0',
            date('Y-m-d', strtotime($loan->post_date))
        ];
    }

    // Siapkan nama file
    $filename = "ReportJournalCorporateLoan_{$bulan}_{$tahun}.csv";

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
        $no_acc = trim($no_acc);
        $loan = report_simpleinterest::getLoanDetails($no_acc,$id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc,$id_pt);

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => 'Data loan tidak ditemukan'
            ]);
        }

        if (!$reports || $reports->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data reports tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data' => [
                'loan' => $loan,
                'reports' => $reports
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
