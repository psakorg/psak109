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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;


use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Tag\Dd;

class amortisedinitialpremController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    // Tampilkan data dengan pagination
    public function index(Request $request)
    {
        // Mengambil id_pt pengguna yang sedang login
        $id_pt = Auth::user()->id_pt;

        // Ambil jumlah item per halaman dari query string, default 10



        $perPage = $request->input('per_page', 10);
        // Ambil data pinjaman hanya untuk id_pt yang sesuai, dengan pagination
        $loans = report_securities::fetchAll($id_pt, $perPage);
    // dd($loans);
        return view('report.securities.report_amortised_initial_prem.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt)
{
    $no_acc = trim($no_acc);

    $loan = report_securities::getLoanDetails($no_acc, $id_pt);
    $master = report_securities::getMasterDataByNoAcc($no_acc, $id_pt);
    // dd($master);
    $reports = report_securities::getReportsByNoAcc($no_acc, $id_pt);


    // Debugging: Jika salah satu data tidak ditemukan, tampilkan pesan atau log
    if (!$loan) {
        return response()->json(['error' => 'Data loan tidak ditemukan'], 404);
    }
    if (!$master) {
        return response()->json(['error' => 'Data master tidak ditemukan'], 404);
    }
    if ($reports->isEmpty()) {
        return response()->json(['error' => 'Data laporan tidak ditemukan'], 404);
    }

    return view('report.securities.report_amortised_initial_prem.view', compact('loan', 'reports', 'master'));
}

    public function exportExcel($no_acc,$id_pt)
    {
        // Ambil data loan dan reports

        $loan = report_securities::getLoanDetails(trim($no_acc), trim($id_pt));
        $reports = report_securities::getReportsByNoAcc(trim($no_acc), trim($id_pt));

        // Cek apakah data loan dan reports ada
        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->setCellValue('B2', 'Account Number');
        $sheet->getStyle('B2')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->setCellValue('F2', $loan->no_acc);
        $sheet->setCellValue('B3', 'Deal Number');
        $sheet->getStyle('B3')->getFont()->setBold(true); // Set bold untuk Deal Number
        $sheet->setCellValue('F3', $loan->deb_name);
        $sheet->setCellValue('B4', 'Issuer Name');
        $sheet->getStyle('B4')->getFont()->setBold(true); // Set bold untuk Issuer Name
        $sheet->setCellValue('F4', number_format($loan->org_bal, 2));
        $sheet->setCellValue('B5', 'Face Value');
        $sheet->getStyle('B5')->getFont()->setBold(true); // Set bold untuk Face Value
        $sheet->setCellValue('F5', date('Y-m-d', strtotime($loan->org_date)));
        $sheet->setCellValue('B6', 'Settlement Date');
        $sheet->getStyle('B6')->getFont()->setBold(true); // Set bold untuk Settlement Date
        $sheet->setCellValue('F6', $loan->TERM);
        $sheet->setCellValue('B7', 'Tenor (TTM)');
        $sheet->getStyle('B7')->getFont()->setBold(true); // Set bold untuk Tenor (TTM)
        $sheet->setCellValue('F7', $loan->no_acc);
        $sheet->setCellValue('B8', 'Maturity Date');
        $sheet->getStyle('B8')->getFont()->setBold(true); // Set bold untuk Maturity Date
        $sheet->setCellValue('F8', $loan->deb_name);
        $sheet->setCellValue('B9', 'Coupon Rate');
        $sheet->getStyle('B9')->getFont()->setBold(true); // Set bold untuk Coupon Rate
        $sheet->setCellValue('F9', $loan->deb_name);
        $sheet->setCellValue('B10', 'Price');
        $sheet->getStyle('B10')->getFont()->setBold(true); // Set bold untuk Price
        $sheet->setCellValue('F10', $loan->deb_name);
        $sheet->setCellValue('B11', 'Fair Value');
        $sheet->getStyle('B11')->getFont()->setBold(true); // Set bold untuk Fair Value
        $sheet->setCellValue('F11', $loan->deb_name);
        $sheet->setCellValue('J7', 'At Discount	');
        $sheet->getStyle('J7')->getFont()->setBold(true); // Set bold untuk At Discount
        $sheet->setCellValue('L7', $loan->no_acc);
        $sheet->setCellValue('J8', 'Outstanding Amount Initial At Premium');
        $sheet->getStyle('J8')->getFont()->setBold(true); // Set bold untuk Outstanding Amount Initial At Premium
        $sheet->setCellValue('L8', $loan->deb_name);
        $sheet->setCellValue('J9', 'EIR Calculated Convertion');
        $sheet->getStyle('J9')->getFont()->setBold(true); // Set bold untuk EIR Calculated Convertion
        $sheet->setCellValue('L9', number_format($loan->org_bal, 2));
        $sheet->setCellValue('J10', 'EIR Calculated At Prmium');
        $sheet->getStyle('J10')->getFont()->setBold(true); // Set bold untuk EIR Calculated At Prmium
        $sheet->setCellValue('L10', date('Y-m-d', strtotime($loan->org_date)));


        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Report Amortised Initial At Premium - Treasury Bonds');
        $sheet->mergeCells('A10:K10'); // Menggabungkan sel untuk judul tabel
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
            'Effective Interest Base On Effective Yield',
            'Accrual Coupon',
            'Amortised At Premium',
            'Outstanding Amount Initial At Premium',
            'Cummulative Amortized At Premium',
            'Unamortized At Premium',
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

        $row = 13;
        $cumulativeTimeGap = 0;
        foreach ($reports as $report) {
            $cumulativeTimeGap += floatval($report->timegap);

            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($report->tglangsuran)));
            $sheet->setCellValue('C' . $row, $report->bunga);
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt, 2));
            $sheet->setCellValue('E' . $row, number_format($report->pokok, 2));
            $sheet->setCellValue('F' . $row, number_format(0, 2));
            $sheet->setCellValue('G' . $row, number_format($report->balance, 2));
            $sheet->setCellValue('H' . $row, number_format($report->bungaeir, 2));
            $sheet->setCellValue('I' . $row, $report->timegap);
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('0.000000000000000'); // Format 15 desimal
            $sheet->setCellValue('J' . $row, number_format($report->outsamtconv, 2));
            $sheet->setCellValue('K' . $row, number_format($cumulativeTimeGap, 15));
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('0.000000000000000'); // Format 15 desimal


            // Mengatur font menjadi bold untuk setiap baris data
            $sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
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
        $sheet->getStyle('A13:K'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A12:J12')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A13:K' . ($row - 1))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan nama file
        $filename = "accrual_interest_report_Effective_$no_acc.xlsx";

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
    $loan = report_securities::getLoanDetails(trim($no_acc), trim($id_pt));
    $reports = report_securities::getReportsByNoAcc(trim($no_acc), trim($id_pt));

    // Cek apakah data loan dan reports ada
    if (!$loan || $reports->isEmpty()) {
        return response()->json(['message' => 'No data found for the given account number.'], 404);
    }

    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);


    // Set informasi pinjaman
    $sheet->setCellValue('A3', 'Account Number');
        $sheet->getStyle('A3')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->setCellValue('B3', $loan->no_acc);
        $sheet->setCellValue('A4', 'Debitor Name');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Debitor Name
        $sheet->setCellValue('B4', $loan->deb_name);
        $sheet->setCellValue('A5', 'Original Amount');
        $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk Original Amount
        $sheet->setCellValue('B5', number_format($loan->org_bal, 2));
        $sheet->setCellValue('A6', 'Term');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bold untuk Term
        $sheet->setCellValue('B6', date('Y-m-d', strtotime($loan->org_date)));
        $sheet->setCellValue('A7', 'Interest Rate');
        $sheet->getStyle('A7')->getFont()->setBold(true); // Set bold untuk Interest Rate
        $sheet->setCellValue('B7', $loan->TERM);
        $sheet->setCellValue('D3', 'Outstanding Amount');
        $sheet->getStyle('D3')->getFont()->setBold(true); // Set bold untuk Outstanding Amount
        $sheet->setCellValue('E3', $loan->no_acc);
        $sheet->setCellValue('D4', 'EIR Conversion Calculated');
        $sheet->getStyle('D4')->getFont()->setBold(true); // Set bold untuk EIR Conversion Calculated
        $sheet->setCellValue('E4', $loan->deb_name);
        $sheet->setCellValue('D5', 'Original Loan Date');
        $sheet->getStyle('D5')->getFont()->setBold(true); // Set bold untuk Original Loan Date
        $sheet->setCellValue('E5', number_format($loan->org_bal, 2));
        $sheet->setCellValue('D6', 'Maturity Loan Date');
        $sheet->getStyle('D6')->getFont()->setBold(true); // Set bold untuk Maturity Loan Date
        $sheet->setCellValue('E6', date('Y-m-d', strtotime($loan->org_date)));
        $sheet->setCellValue('D7', 'Payment Amount');
        $sheet->getStyle('D7')->getFont()->setBold(true); // Set bold untuk Payment Amount
        $sheet->setCellValue('E7', $loan->TERM);

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
    $filename = "accrual_interest_report_$no_acc.pdf";

    // Set pengaturan untuk PDF
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);


    // Siapkan direktori untuk menyimpan file sementara
    $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');

    // Simpan file PDF
    $writer->save($temp_file);

    // Kembalikan response PDF
    return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
}
}
