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

use Dompdf\Dompdf;
use Dompdf\Options;

class effectiveController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporatt
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
          // Ambil jumlah item per halaman dari query string, default 10

          // Ambil data dengan pagination
          $loans = report_effective::getLoanDetailsbyidpt($id_pt);

        return view('report.outstanding.effective.master', compact('loans'));
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
        $loan = report_effective::getLoanDetailsbyidpt(trim($id_pt));
        // $corporateLoans = report_simpleinterest::getCorporateLoans($id_pt);
        // $reports = report_simpleinterest::getReportsByNoAcc(trim($id_pt));
        $reports = report_effective::getLoanDetailsbyidpt(trim($id_pt));


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
        ->get();

        // dd($master);

        // return view('report.outstanding.effective.view', compact('master', 'loan','loanfirst','loanjoin'));
        return view('report.outstanding.effective.view', compact('master', 'bulan', 'tahun' ,'isSuperAdmin', "user"));
    }

    public function exportExcel($id_pt)
    {
        // Ambil data loan dan reports
        $user_id_pt = Auth::user()->id_pt;
        // Ambil data loan dan reports
        $loan = report_effective::getLoanDetailsbyidpt(trim($id_pt));
        $reports = report_effective::getLoanDetailsbyidpt(trim($id_pt));
    
        // Cek apakah data loan dan reports ada
        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }
    
        $loanFirst = $loan->first();
    
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->setCellValue('A3', 'Branch Number');
        $sheet->getStyle('A3')->getFont()->setBold(true); // Set bold untuk Branch Number
        $sheet->setCellValue('B3', $loanFirst->no_acc);
        $sheet->setCellValue('A4', 'Branch Name');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Branch Name
        $sheet->setCellValue('B4', $loanFirst->deb_name);
        $sheet->setCellValue('A5', 'GL Group');
        $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk GL Group
        $sheet->setCellValue('B5', number_format($loanFirst->org_bal, 2));
        $sheet->setCellValue('A6', 'Date Of Report');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bold untukDate Of Report
        $sheet->setCellValue('B6', date('Y-m-d', strtotime($loanFirst->org_date)));


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
            $sheet->setCellValue('C' . $row, $report->haribunga ?? 0);
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt, 2));
            $sheet->setCellValue('E' . $row, number_format($report->penarikan?? 0));
            $sheet->setCellValue('F' . $row, number_format($report->pengembalian?? 0));
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
        $filename = "accrual_interest_report_$id_pt.xlsx";

        // Buat writer dan simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        // Kembalikan response Excel
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }



    // Method untuk mengekspor data ke PDF
    public function exportPdf($id_pt)
{
    $user_id_pt = Auth::user()->id_pt;
    // Ambil data loan dan reports
    $loan = report_effective::getLoanDetailsbyidpt(trim($id_pt));
    $reports = report_effective::getLoanDetailsbyidpt(trim($id_pt));

    // Cek apakah data loan dan reports ada
    if (!$loan || $reports->isEmpty()) {
        return response()->json(['message' => 'No data found for the given account number.'], 404);
    }

    $loanFirst = $loan->first();

    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);


    // Set informasi pinjaman
    $sheet->setCellValue('A3', 'Branch Number');
        $sheet->getStyle('A3')->getFont()->setBold(true); // Set bold untuk Branch Number
        $sheet->setCellValue('B3', $loanFirst->no_acc);
        $sheet->setCellValue('A4', 'Branch Name');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Branch Name
        $sheet->setCellValue('B4', $loanFirst->deb_name);
        $sheet->setCellValue('A5', 'GL Group');
        $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk GL Group
        $sheet->setCellValue('B5', number_format($loanFirst->org_bal, 2));
        $sheet->setCellValue('A6', 'Date Of Report');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bold untukDate Of Report
        $sheet->setCellValue('B6', date('Y-m-d', strtotime($loanFirst->org_date)));

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
        $sheet->setCellValue('C' . $row, $report->haribunga ?? 0);
        $sheet->setCellValue('D' . $row, number_format($report->pmtamt, 2));
        $sheet->setCellValue('E' . $row, number_format($report->penarikan?? 0));
        $sheet->setCellValue('F' . $row, number_format($report->pengembalian?? 0));
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
    $filename = "accrual_interest_report_$id_pt.pdf";

    // Set pengaturan untuk PDF
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);

    // Siapkan direktori untuk menyimpan file sementara
    $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');

    // Simpan file PDF
    $writer->save($temp_file);

    // Kembalikan response PDF
    return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
}
public function exportCsv($id_pt)
{
    // Ambil data loan dan reports
    $user_id_pt = Auth::user()->id_pt;
    // Ambil data loan dan reports
    $loan = report_effective::getLoanDetailsbyidpt(trim($id_pt));
    $reports = report_effective::getLoanDetailsbyidpt(trim($id_pt));

    // Cek apakah data loan dan reports ada
    if (!$loan || $reports->isEmpty()) {
        return response()->json(['message' => 'No data found for the given account number.'], 404);
    }

    $loanFirst = $loan->first();

    // Siapkan data CSV
    $csvData = [];
    $csvData[] = ['Branch Number', $loanFirst->no_acc];
    $csvData[] = ['Branch Name', $loanFirst->deb_name];
    $csvData[] = ['GL Group', number_format($loanFirst->org_bal, 2)];
    $csvData[] = ['Date Of Report', date('Y-m-d', strtotime($loanFirst->org_date))];
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
    $filename = "accrual_interest_report_$id_pt.csv";

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
