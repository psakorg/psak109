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

class calculatedaccrualcouponController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    // Tampilkan data dengan pagination
    public function index(Request $request)
    {
        // Mengambil id_pt pengguna yang sedang login
        $id_pt = Auth::user()->id_pt;

        // Ambil jumlah item per halaman dari query string, default 10


        $perPage = $request->input('per_page', 1000);
        // Ambil data pinjaman hanya untuk id_pt yang sesuai, dengan pagination
        $loans = report_securities::fetchAll($id_pt, $perPage);

        return view('report.securities.report_calculated_accrual_coupon.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt , Request $request)
{
    $no_acc = trim($no_acc);

    // $loan = report_securities::getLoanDetails($no_acc, $id_pt);
    // $master = report_securities::getMasterDataByNoAcc($no_acc, $id_pt);
    // // dd($master);
    // $reports = report_securities::getReportsByNoAcc($no_acc, $id_pt);


    // // Debugging: Jika salah satu data tidak ditemukan, tampilkan pesan atau log
    // if (!$loan) {
    //     return response()->json(['error' => 'Data loan tidak ditemukan'], 404);
    // }
    // if (!$master) {
    //     return response()->json(['error' => 'Data master tidak ditemukan'], 404);
    // }
    // if ($reports->isEmpty()) {
    //     return response()->json(['error' => 'Data laporan tidak ditemukan'], 404);
    // }

    $perPage = $request->input('per_page', 1000);

    $reports = report_securities::spcashflowtreasurybond($id_pt, $perPage, $no_acc);

    if (!$reports) {
        abort(404, 'report detail not found');
    }

    return view('report.securities.report_calculated_accrual_coupon.view', compact('reports'));
}
 // Method untuk mengekspor data ke Excel
    public function exportExcel($no_acc, $id_pt)
    {
        // Ambil data loan dan reports

        $reports = report_securities::spcashflowtreasurybond($id_pt, 1000, $no_acc);
        if ($reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet();
         $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
         $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
         $sheet->getPageMargins()->setTop(0.5);
         $sheet->getPageMargins()->setRight(0.5);
         $sheet->getPageMargins()->setLeft(0.5);
         $sheet->getPageMargins()->setBottom(0.5);

        $firstReport = $reports->first();
        if (!$firstReport) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Set informasi pinjaman
        $sheet->setCellValue('B2', 'Account Number');
        $sheet->setCellValue('D2', ': ' . $firstReport->no_acc);
        $sheet->setCellValue('B3', 'Deal Number');
        $sheet->setCellValue('D3', ': ' . $firstReport->bond_id);
        $sheet->setCellValue('B4', 'Issuer Name');
        $sheet->setCellValue('D4', ': ' . $firstReport->issuer_name);
        $sheet->setCellValue('B5', 'Face Value');
        $sheet->setCellValue('D5', ': ' . number_format($firstReport->face_value,0));
        $sheet->setCellValue('B6', 'Settlement Date');
        $sheet->setCellValue('D6', ': ' . $firstReport->settle_dt);
        $sheet->setCellValue('B7', 'Tenor (TTM)');
        $sheet->setCellValue('D7', ': ' . $firstReport->tenor . ' Year');
        $sheet->setCellValue('B8', 'Maturity Date');
        $sheet->setCellValue('D8', ': ' . $firstReport->mtr_date);
        $sheet->setCellValue('B9', 'Coupon Rate');
        $sheet->setCellValue('D9', ': ' . number_format($firstReport->coupon_rate*100,5) . '%');
        $sheet->setCellValue('B10', 'Price');
        $sheet->setCellValue('D10', ': ' . number_format($firstReport->price*100,5) . '%');
        $sheet->setCellValue('B11', 'Fair Value');
        $sheet->setCellValue('D11', ': ' . number_format($firstReport->fair_value,0));
        $sheet->setCellValue('H10', 'Outstanding Amount');
        $sheet->setCellValue('J10', ': ' . number_format($firstReport->outsamt_conv, 0));
        $sheet->setCellValue('H11', 'EIR Calculated Conversion');
        $sheet->setCellValue('J11', ': ' . number_format($firstReport->eircalc_conv*100, 14) . '%');

        $sheet->mergeCells('B2:C2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B3:C3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B4:C4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B5:C5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B6:C6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B7:C7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B8:C8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B9:C9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B10:C10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B11:C11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D2:E2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D3:E3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D4:E4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D5:E5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D6:E6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D7:E7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D8:E8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D9:E9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D10:E10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D11:E11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('H10:I10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('H11:I11'); // Menggabungkan sel untuk judul tabel

        $sheet->getStyle('B2:C11')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('H2:I11')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('D2:E12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('J8:J11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set judul tabel laporan
        $sheet->setCellValue('B13', 'Report Calculated Accrual Coupon - Treasury Bonds');
        $sheet->mergeCells('B13:K13'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('B13')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B13')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('B13')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('B13')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(14)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Transaction Date',
            'Days Interest',
            'Payment Amount',
            'Accrual Coupon',
            'Coupon Payment',
            'Time Gap',
            'Outstanding Amount',
            'Cummulative Time Gap'
        ];
        $columnIndex = 'B';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '15', $header);
            $sheet->getStyle($columnIndex . '15')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '15')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '15')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '15')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }
        $sheet->getStyle('B15:J15')->getAlignment()->setWrapText(true);

        $unamort = $reports->first()->atpremium * (-1);
        $row = 16;
        foreach ($reports as $report) {
            if ($report->month_to > 0) {
                $unamort += $report->amortise_prem;
            }
            $sheet->setCellValue('B' . $row, $report->month_to);
            $sheet->setCellValue('C' . $row, $report->tglangsuranconv);
            $sheet->setCellValue('D' . $row, number_format($report->haribunga, 0));
            $sheet->setCellValue('E' . $row, number_format($report->outconv, 0));
            $sheet->setCellValue('F' . $row, number_format($report->accr_conv, 0));
            $sheet->setCellValue('G' . $row, number_format($report->interest, 0));
            $sheet->setCellValue('H' . $row, number_format($report->timegap, 0));
            $sheet->setCellValue('I' . $row, number_format($report->outsamt_conv, 0));
            $sheet->setCellValue('J' . $row, number_format($report->cum_timegap, 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('B' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('B' . $row . ':J' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        $sheet->getStyle('E17' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue('B' . $row, "TOTAL");
        $sheet->mergeCells('B' . $row . ':C' . $row); 
        $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, number_format($reports->sum('haribunga')));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($reports->sum('outconv')));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($reports->sum('accr_conv')));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($reports->sum('interest')));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($reports->sum('timegap')));

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $sheet->getStyle('B15:J15')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B15:J15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B15:J15')->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(9);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(22);

        // Set border untuk semua data laporan
        $sheet->getStyle('B16:J' . ($row))->applyFromArray($styleArray);

        // Siapkan nama file
        $filename = "securities_calculated_accrual_coupon_$no_acc.xlsx";

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

        $reports = report_securities::spcashflowtreasurybond($id_pt, 1000, $no_acc);
        if ($reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet();
         $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
         $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
         $sheet->getPageMargins()->setTop(0.5);
         $sheet->getPageMargins()->setRight(0.5);
         $sheet->getPageMargins()->setLeft(0.5);
         $sheet->getPageMargins()->setBottom(0.5);

        $firstReport = $reports->first();
        if (!$firstReport) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Set informasi pinjaman
        $sheet->setCellValue('B2', 'Account Number');
        $sheet->setCellValue('D2', ': ' . $firstReport->no_acc);
        $sheet->setCellValue('B3', 'Deal Number');
        $sheet->setCellValue('D3', ': ' . $firstReport->bond_id);
        $sheet->setCellValue('B4', 'Issuer Name');
        $sheet->setCellValue('D4', ': ' . $firstReport->issuer_name);
        $sheet->setCellValue('B5', 'Face Value');
        $sheet->setCellValue('D5', ': ' . number_format($firstReport->face_value,0));
        $sheet->setCellValue('B6', 'Settlement Date');
        $sheet->setCellValue('D6', ': ' . $firstReport->settle_dt);
        $sheet->setCellValue('B7', 'Tenor (TTM)');
        $sheet->setCellValue('D7', ': ' . $firstReport->tenor . ' Year');
        $sheet->setCellValue('B8', 'Maturity Date');
        $sheet->setCellValue('D8', ': ' . $firstReport->mtr_date);
        $sheet->setCellValue('B9', 'Coupon Rate');
        $sheet->setCellValue('D9', ': ' . number_format($firstReport->coupon_rate*100,5) . '%');
        $sheet->setCellValue('B10', 'Price');
        $sheet->setCellValue('D10', ': ' . number_format($firstReport->price*100,5) . '%');
        $sheet->setCellValue('B11', 'Fair Value');
        $sheet->setCellValue('D11', ': ' . number_format($firstReport->fair_value,0));
        $sheet->setCellValue('H10', 'Outstanding Amount');
        $sheet->setCellValue('J10', ': ' . number_format($firstReport->outsamt_conv, 0));
        $sheet->setCellValue('H11', 'EIR Calculated Conversion');
        $sheet->setCellValue('J11', ': ' . number_format($firstReport->eircalc_conv*100, 14) . '%');

        $sheet->mergeCells('B2:C2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B3:C3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B4:C4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B5:C5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B6:C6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B7:C7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B8:C8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B9:C9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B10:C10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('B11:C11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D2:E2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D3:E3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D4:E4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D5:E5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D6:E6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D7:E7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D8:E8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D9:E9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D10:E10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('D11:E11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('H10:I10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('H11:I11'); // Menggabungkan sel untuk judul tabel

        $sheet->getStyle('B2:C11')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('H2:I11')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('D2:E12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('J8:J11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set judul tabel laporan
        $sheet->setCellValue('B13', 'Report Calculated Accrual Coupon - Treasury Bonds');
        $sheet->mergeCells('B13:J13'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('B13')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B13')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('B13')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('B13')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(14)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Transaction Date',
            'Days Interest',
            'Payment Amount',
            'Accrual Coupon',
            'Coupon Payment',
            'Time Gap',
            'Outstanding Amount',
            'Cummulative Time Gap'
        ];
        $columnIndex = 'B';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '15', $header);
            $sheet->getStyle($columnIndex . '15')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '15')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '15')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '15')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }
        $sheet->getStyle('B15:J15')->getAlignment()->setWrapText(true);

        $unamort = $reports->first()->atpremium * (-1);
        $row = 16;
        foreach ($reports as $report) {
            if ($report->month_to > 0) {
                $unamort += $report->amortise_prem;
            }
            $sheet->setCellValue('B' . $row, $report->month_to);
            $sheet->setCellValue('C' . $row, $report->tglangsuranconv);
            $sheet->setCellValue('D' . $row, number_format($report->haribunga, 0));
            $sheet->setCellValue('E' . $row, number_format($report->outconv, 0));
            $sheet->setCellValue('F' . $row, number_format($report->accr_conv, 0));
            $sheet->setCellValue('G' . $row, number_format($report->interest, 0));
            $sheet->setCellValue('H' . $row, number_format($report->timegap, 0));
            $sheet->setCellValue('I' . $row, number_format($report->outsamt_conv, 0));
            $sheet->setCellValue('J' . $row, number_format($report->cum_timegap, 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('B' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('B' . $row . ':J' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        $sheet->getStyle('E17' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue('B' . $row, "TOTAL");
        $sheet->mergeCells('B' . $row . ':C' . $row); 
        $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, number_format($reports->sum('haribunga')));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($reports->sum('outconv')));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($reports->sum('accr_conv')));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($reports->sum('interest')));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($reports->sum('timegap')));

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $sheet->getStyle('B15:J15')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B15:J15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B15:J15')->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(9);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(22);

        // Set border untuk semua data laporan
        $sheet->getStyle('B16:J' . ($row))->applyFromArray($styleArray);

    // Siapkan nama file
    $filename = "securities_calculated_accrual_coupon_$no_acc.pdf";

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
