<?php

namespace App\Http\Controllers\report\securities;

use App\Models\report_securities;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection; 
use Illuminate\Http\Request;
use Carbon\Carbon;


// use Dompdf\Dompdf;
// use Dompdf\Options;
// use Mpdf\Tag\Dd;

class amortisedinitialbrokeragefeeController extends Controller
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
        $reports = report_securities::fetchAll($id_pt, $perPage);

        return view('report.securities.report_amortised_initial_brokerage_fee.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt, Request $request)
{
    $no_acc = trim($no_acc);

    // $reports = report_securities::getLoanDetails($no_acc, $id_pt);
    // $master = report_securities::getMasterDataByNoAcc($no_acc, $id_pt);
    // // dd($master);
    // $reports = report_securities::getReportsByNoAcc($no_acc, $id_pt);


    // // Debugging: Jika salah satu data tidak ditemukan, tampilkan pesan atau log
    // if (!$reports) {
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

    return view('report.securities.report_amortised_initial_brokerage_fee.view', compact('reports'));
}

    public function exportExcel($no_acc,$id_pt)
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
        $sheet->setCellValue('D7', ': ' . $firstReport->tenor);
        $sheet->setCellValue('B8', 'Maturity Date');
        $sheet->setCellValue('D8', ': ' . $firstReport->mtr_date);
        $sheet->setCellValue('B9', 'Coupon Rate');
        $sheet->setCellValue('D9', ': ' . number_format($firstReport->coupon_rate*100,5) . '%');
        $sheet->setCellValue('B10', 'Price');
        $sheet->setCellValue('D10', ': ' . number_format($firstReport->price*100,5) . '%');
        $sheet->setCellValue('B11', 'Fair Value');
        $sheet->setCellValue('D11', ': ' . number_format($firstReport->fair_value,0));

        $sheet->setCellValue('I8', 'Brokerage Fee');
        $sheet->setCellValue('K8', ': ' . number_format($firstReport->brokerage, 0));
        $sheet->setCellValue('I9', 'Oustanding Amount Initial Brokerage Fee');
        $sheet->setCellValue('K9', ': ' . number_format($firstReport->outbrok, 0));
        $sheet->setCellValue('I10', 'EIR Calculated Conversion');
        $sheet->setCellValue('K10', ': ' . number_format($firstReport->eircalc_conv*100, 14) . '%');
        $sheet->setCellValue('I11', 'EIR Calculated Brokerage Fee');
        $sheet->setCellValue('K11', ': ' . number_format($firstReport->eircalc_brok*100, 14) . '%');

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

        $sheet->getStyle('B2:B12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('I2:I12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('D2:D12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('K8:K11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set judul tabel laporan
        $sheet->setCellValue('B13', 'Report Amortised Initial Brokerage Fee - Treasury Bonds');
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
            'Effective Interest Based on Effective Yield',
            'Accrual Coupon',
            'Amortised Brokerage Fee',
            'Outstanding Amount Initial Brokerage Fee',
            'Cummulative Amortized Brokerage Fee',
            'Unamortized Brokerage Fee'
        ];
        $columnIndex = 'B';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '15', $header);
            $sheet->getStyle($columnIndex . '15')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '15')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '15')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '15')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '15')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }
        $sheet->getStyle('B15:K15')->getAlignment()->setWrapText(true);

        $unamort = $reports->first()->brokerage;
        $row = 16;
        foreach ($reports as $report) {
            $amortized = (float)$report->brokerage;
            if ($report->month_to > 0) {
                $unamort -= $amortized;
            }
            $sheet->setCellValue('B' . $row, $report->month_to);
            $sheet->setCellValue('C' . $row, $report->tglangsuranconv);
            $sheet->setCellValue('D' . $row, number_format($report->haribunga, 0));
            $sheet->setCellValue('E' . $row, number_format($report->outbrok, 0));
            $sheet->setCellValue('F' . $row, number_format($report->interest, 0));
            $sheet->setCellValue('G' . $row, number_format($report->accr_brok, 0));
            $sheet->setCellValue('H' . $row, number_format($report->amortise_brok, 0));
            $sheet->setCellValue('I' . $row, number_format($report->outsamt_brok, 0));
            $sheet->setCellValue('J' . $row, number_format($report->cum_amortisebrok, 0));
            $sheet->setCellValue('K' . $row, number_format($unamort, 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('B' . $row . ':K' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('B' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('B' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        $sheet->getStyle('B16' . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E16' . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue('B' . $row, "TOTAL");
        $sheet->mergeCells('B' . $row . ':C' . $row); 
        $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, number_format($reports->sum('haribunga')));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($reports->sum('outbrok')));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($reports->sum('interest')));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($reports->sum('accr_brok')));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($reports->sum('amortise_brok')));

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $sheet->getStyle('B15:K15')->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(22);
        $sheet->getColumnDimension('K')->setWidth(24);

        // Set border untuk semua data laporan
        $sheet->getStyle('B16:K' . ($row))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        // foreach (range('A', 'K') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        // Siapkan nama file
        $filename = "securities_amortised_initial_brokerage_fee_$no_acc.xlsx";

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
        $sheet->setCellValue('D7', ': ' . $firstReport->tenor);
        $sheet->setCellValue('B8', 'Maturity Date');
        $sheet->setCellValue('D8', ': ' . $firstReport->mtr_date);
        $sheet->setCellValue('B9', 'Coupon Rate');
        $sheet->setCellValue('D9', ': ' . number_format($firstReport->coupon_rate*100,5) . '%');
        $sheet->setCellValue('B10', 'Price');
        $sheet->setCellValue('D10', ': ' . number_format($firstReport->price*100,5) . '%');
        $sheet->setCellValue('B11', 'Fair Value');
        $sheet->setCellValue('D11', ': ' . number_format($firstReport->fair_value,0));

        $sheet->setCellValue('I8', 'Brokerage Fee');
        $sheet->setCellValue('K8', ': ' . number_format($firstReport->brokerage, 0));
        $sheet->setCellValue('I9', 'Oustanding Amount Initial Brokerage Fee');
        $sheet->setCellValue('K9', ': ' . number_format($firstReport->outbrok, 0));
        $sheet->setCellValue('I10', 'EIR Calculated Conversion');
        $sheet->setCellValue('K10', ': ' . number_format($firstReport->eircalc_conv*100, 14) . '%');
        $sheet->setCellValue('I11', 'EIR Calculated Brokerage Fee');
        $sheet->setCellValue('K11', ': ' . number_format($firstReport->eircalc_brok*100, 14) . '%');

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

        $sheet->getStyle('B2:B12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('I2:I12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('D2:D12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('K8:K11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set judul tabel laporan
        $sheet->setCellValue('B13', 'Report Amortised Initial Brokerage Fee - Treasury Bonds');
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
            'Effective Interest Based on Effective Yield',
            'Accrual Coupon',
            'Amortised Brokerage Fee',
            'Outstanding Amount Initial Brokerage Fee',
            'Cummulative Amortized Brokerage Fee',
            'Unamortized Brokerage Fee'
        ];
        $columnIndex = 'B';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '15', $header);
            $sheet->getStyle($columnIndex . '15')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '15')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '15')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '15')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '15')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }
        $sheet->getStyle('B15:K15')->getAlignment()->setWrapText(true);

        $unamort = $reports->first()->brokerage;
        $row = 16;
        foreach ($reports as $report) {
            $amortized = (float)$report->brokerage;
            if ($report->month_to > 0) {
                $unamort -= $amortized;
            }
            $sheet->setCellValue('B' . $row, $report->month_to);
            $sheet->setCellValue('C' . $row, $report->tglangsuranconv);
            $sheet->setCellValue('D' . $row, number_format($report->haribunga, 0));
            $sheet->setCellValue('E' . $row, number_format($report->outbrok, 0));
            $sheet->setCellValue('F' . $row, number_format($report->interest, 0));
            $sheet->setCellValue('G' . $row, number_format($report->accr_brok, 0));
            $sheet->setCellValue('H' . $row, number_format($report->amortise_brok, 0));
            $sheet->setCellValue('I' . $row, number_format($report->outsamt_brok, 0));
            $sheet->setCellValue('J' . $row, number_format($report->cum_amortisebrok, 0));
            $sheet->setCellValue('K' . $row, number_format($unamort, 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('B' . $row . ':K' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('B' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('B' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        $sheet->getStyle('B16' . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E16' . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue('B' . $row, "TOTAL");
        $sheet->mergeCells('B' . $row . ':C' . $row); 
        $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('D' . $row, number_format($reports->sum('haribunga')));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($reports->sum('outbrok')));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($reports->sum('interest')));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($reports->sum('accr_brok')));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($reports->sum('amortise_brok')));

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $sheet->getStyle('B15:K15')->applyFromArray($styleArray);

        $sheet->mergeCells('I2:J2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I3:J3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I4:J4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I5:J5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I6:J6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I7:J7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I8:J8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I9:J9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I10:J10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('I11:J11'); // Menggabungkan sel untuk judul tabel

        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(24);
        $sheet->getColumnDimension('J')->setWidth(24);
        $sheet->getColumnDimension('K')->setWidth(26);

        // Set border untuk semua data laporan
        $sheet->getStyle('B16:K' . ($row))->applyFromArray($styleArray);

    // Siapkan nama file
    $filename = "amortised_initial_brokerage_fee_$no_acc.pdf";

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
