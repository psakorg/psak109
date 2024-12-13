<?php

namespace App\Http\Controllers\report\Report_Amortised_Cost;

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

use Dompdf\Dompdf;
use Dompdf\Options;

class effectiveController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
        // Ambil jumlah item per halaman dari query string, default 10
        $perPage = $request->input('per_page', 10);
        // Ambil data dengan pagination
        $loans = report_effective::fetchAll($id_pt, $perPage);
         // Tambahkan debug untuk loans
    //  dd($loans);
        return view('report.amortised_cost.effective.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt)
    {

        $no_acc = trim($no_acc);
        $loan = report_effective::getLoanDetails($no_acc, $id_pt);
        $master=report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);
        if (!$loan) {
            abort(404, 'Loan not found');
        }


        return view('report.amortised_cost.effective.view', compact('loan', 'reports','master'));
    }

    public function exportExcel($no_acc, $id_pt)
    {
        // Ambil data loan dan reports
        $loan = report_effective::getLoanDetails(trim($no_acc), trim($id_pt));
        $reports = report_effective::getReportsByNoAcc(trim($no_acc), trim($id_pt));
        $master = report_effective::getMasterDataByNoAcc(trim($no_acc), trim($id_pt));

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
$accountNumber = ' ' . (string) $loan->no_acc; // Menambahkan spasi di depan
$sheet->setCellValue('B3', $accountNumber);

$sheet->setCellValue('A4', 'Debitor Name');
$sheet->getStyle('A4')->getFont()->setBold(true);
$sheet->setCellValue('B4', $loan->deb_name);

$sheet->setCellValue('A5', 'Original Amount');
$sheet->getStyle('A5')->getFont()->setBold(true);
$sheet->setCellValue('B5', number_format($loan->org_bal, 2));

$sheet->setCellValue('A6', 'Original Loan Date');
$sheet->getStyle('A6')->getFont()->setBold(true);
$sheet->setCellValue('B6', date('Y-m-d', strtotime($loan->org_date)));

$sheet->setCellValue('A7', 'Term');
$sheet->getStyle('A7')->getFont()->setBold(true);
$sheet->setCellValue('B7', $master->term);

$sheet->setCellValue('A8', 'Maturity Loan Date');
$sheet->getStyle('A8')->getFont()->setBold(true);
$sheet->setCellValue('B8', date('Y-m-d', strtotime($loan->mtr_date)));

$sheet->setCellValue('A9', 'Interest Rate');
$sheet->getStyle('A9')->getFont()->setBold(true);
$sheet->setCellValue('B9', number_format($master->rate * 100, 2) . ' %'); // Mengisi dengan format persen

// Set informasi biaya tambahan
$sheet->setCellValue('D3', 'UpFront Fee');
$sheet->getStyle('D3')->getFont()->setBold(true);
$upfrontFee = round(-($loan->org_bal * 0.01), 0);
$sheet->setCellValue('E3', number_format($upfrontFee, 2));

$sheet->setCellValue('D4', 'Transaction Cost');
$sheet->getStyle('D4')->getFont()->setBold(true);
$trxcost = preg_replace('/[^\d.]/', '', $master->trxcost); // Mengambil nilai dari database
$trxcostFloat = (float)$trxcost;
$sheet->setCellValue('E4', number_format($trxcostFloat, 2));

$sheet->setCellValue('D5', 'Carrying Amount');
$sheet->getStyle('D5')->getFont()->setBold(true);
$carryingAmount = $loan->org_bal + $upfrontFee;
$sheet->setCellValue('E5', number_format($carryingAmount, 2));

$sheet->setCellValue('D6', 'EIR Exposure');
$sheet->getStyle('D6')->getFont()->setBold(true);
$sheet->setCellValue('E6', number_format($loan->eirex * 100, 14) . ' %'); // Menghitung EIR Exposure

$sheet->setCellValue('D7', 'EIR Calculated');
$sheet->getStyle('D7')->getFont()->setBold(true);
$sheet->setCellValue('E7', number_format($loan->eircalc * 100, 14) . ' %'); // Menghitung EIR Calculated

$sheet->setCellValue('D8', 'Payment Amount');
$sheet->getStyle('D8')->getFont()->setBold(true);
$sheet->setCellValue('E8', number_format($master->pmtamt ?? 0, 2));



        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Accrual Interest Report - Report Details');
        $sheet->mergeCells('A10:J10'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Payment Date',
            'Payment Amount',
            'Interest Recognition',
            'Interest Payment',
            'Amortised',
            'Carrying Amount',
            'Cumulative Amortized',
            'Unamortized'
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
        $unamortized = $upfrontFee ?? 0; // Menggunakan upfrontFee untuk unamortized pada baris pertama

        foreach ($reports as $report) {
            $amortized = $report->amortized ?? 0; // Ambil nilai amortized dari laporan
            $cumulativeAmortized += $amortized; // Tambahkan amortized ke total kumulatif
            $carryingAmount = $report->baleir ?? 0; // Ambil nilai carrying amount dari laporan

            // Hitung nilai unamortized
            if ($row == 13) { // Untuk baris pertama
                $unamortized = $upfrontFee; // Gunakan nilai upfrontFee
            } else {
                $unamortized += $amortized; // Tambahkan amortized ke unamortized
            }

            // Mengisi data ke dalam kolom
            $sheet->setCellValue('A' . $row, $report->bulanke ?? 'Data tidak ditemukan');
            $sheet->setCellValue('B' . $row, isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate');
            $sheet->setCellValue('C' . $row, number_format($report->pmtamt ?? 0, 2));
            $sheet->setCellValue('D' . $row, number_format($report->bungaeir ?? 0, 2));
            $sheet->setCellValue('E' . $row, number_format($report->bunga ?? 0, 2));
            $sheet->setCellValue('F' . $row, number_format($amortized, 2));
            $sheet->setCellValue('G' . $row, number_format($carryingAmount, 2));
            $sheet->setCellValue('H' . $row, number_format($cumulativeAmortized, 2));
            $sheet->setCellValue('I' . $row, number_format($unamortized, 2));

            $row++; // Pindah ke baris berikutnya
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
    $loan = report_effective::getLoanDetails($no_acc, $id_pt);
    $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);

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
    $sheet->getStyle('A3')->getFont()->setBold(true);
    $sheet->setCellValue('B3', $loan->no_acc);
    $sheet->setCellValue('A4', 'Debitor Name');
    $sheet->getStyle('A4')->getFont()->setBold(true);
    $sheet->setCellValue('B4', $loan->deb_name);
    $sheet->setCellValue('A5', 'Original Amount');
    $sheet->getStyle('A5')->getFont()->setBold(true);
    $sheet->setCellValue('B5', number_format($loan->org_bal, 2));
    $sheet->setCellValue('A6', 'Original Loan Date');
    $sheet->getStyle('A6')->getFont()->setBold(true);
    $sheet->setCellValue('B6', date('Y-m-d', strtotime($loan->org_date)));
    $sheet->setCellValue('A7', 'Term');
    $sheet->getStyle('A7')->getFont()->setBold(true);
    $sheet->setCellValue('B7', $loan->TERM);
    $sheet->setCellValue('A8', 'Maturity Loan Date');
    $sheet->getStyle('A8')->getFont()->setBold(true);
    $sheet->setCellValue('B8', date('Y-m-d', strtotime($loan->mtr_date)));
    $sheet->setCellValue('A9', 'Interest Rate');
    $sheet->getStyle('A9')->getFont()->setBold(true);
    $sheet->setCellValue('B9', date('Y-m-d', strtotime($loan->mtr_date)));
    $sheet->setCellValue('D3', 'UpFront Fee');
    $sheet->getStyle('D3')->getFont()->setBold(true);
    $sheet->setCellValue('E3', $loan->no_acc);
    $sheet->setCellValue('D4', 'Transaction Cost');
    $sheet->getStyle('D4')->getFont()->setBold(true);
    $sheet->setCellValue('E4', $loan->deb_name);
    $sheet->setCellValue('D5', 'Carrying Amount');
    $sheet->getStyle('D5')->getFont()->setBold(true);
    $sheet->setCellValue('E5', number_format($loan->org_bal, 2));
    $sheet->setCellValue('D6', 'EIR Exposure');
    $sheet->getStyle('D6')->getFont()->setBold(true);
    $sheet->setCellValue('E6', date('Y-m-d', strtotime($loan->org_date)));
    $sheet->setCellValue('D7', 'EIR Calculated');
    $sheet->getStyle('D7')->getFont()->setBold(true);
    $sheet->setCellValue('E7', $loan->TERM);
    $sheet->setCellValue('D8', 'Payment Amount');
    $sheet->getStyle('D8')->getFont()->setBold(true);
    $sheet->setCellValue('E8', date('Y-m-d', strtotime($loan->mtr_date)));

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
