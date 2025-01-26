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

class amortisedcostController extends Controller
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

        return view('report.securities.report_amortised.master', compact('loans'));
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

    return view('report.securities.report_amortised.view', compact('reports'));
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
        $sheet->setCellValue('A2', 'Account Number');
        $sheet->setCellValue('C2', ': ' . $firstReport->no_acc);
        $sheet->setCellValue('A3', 'Deal Number');
        $sheet->setCellValue('C3', ': ' . $firstReport->bond_id);
        $sheet->setCellValue('A4', 'Issuer Name');
        $sheet->setCellValue('C4', ': ' . $firstReport->issuer_name);
        $sheet->setCellValue('A5', 'Face Value');
        $sheet->setCellValue('C5', ': ' . number_format($firstReport->face_value,0));
        $sheet->setCellValue('A6', 'Settlement Date');
        $sheet->setCellValue('C6', ': ' . $firstReport->settle_dt);
        $sheet->setCellValue('A7', 'Tenor (TTM)');
        $sheet->setCellValue('C7', ': ' . $firstReport->tenor . ' Year');
        $sheet->setCellValue('A8', 'Maturity Date');
        $sheet->setCellValue('C8', ': ' . $firstReport->mtr_date);
        $sheet->setCellValue('A9', 'Coupon Rate');
        $sheet->setCellValue('C9', ': ' . number_format($firstReport->coupon_rate*100,5) . '%');
        $sheet->setCellValue('A10', 'Yield (YTM)');
        $sheet->setCellValue('C10', ': ' . number_format($firstReport->yield*100,5) . '%');
        $sheet->setCellValue('A11', 'Price');
        $sheet->setCellValue('C11', ': ' . number_format($firstReport->price*100,5) . '%');
        $sheet->setCellValue('A12', 'Fair Value');
        $sheet->setCellValue('C12', ': ' . number_format($firstReport->fair_value,0));

        $sheet->setCellValue('J7', 'At Discount	');
        $sheet->setCellValue('K7', ': -' . number_format($firstReport->atdiscount,0));
        $sheet->setCellValue('J8', 'At Premium');
        $sheet->setCellValue('K8', ': ' . number_format($firstReport->atpremium,0));
        $sheet->setCellValue('J9', 'Brokerage Fee');
        $sheet->setCellValue('K9', ': ' . number_format($firstReport->brokerage, 0));
        $sheet->setCellValue('J10', 'Carrying Amount');
        $sheet->setCellValue('K10', ': ' . number_format($firstReport->carrying_amount, 0));
        $sheet->setCellValue('J11', 'EIR Exposure');
        $sheet->setCellValue('K11', ': ' . number_format($firstReport->eirex*100, 14) . '%');
        $sheet->setCellValue('J12', 'EIR Calculated');
        $sheet->setCellValue('K12', ': ' . number_format($firstReport->eircalc*100, 14) . '%');

        $sheet->mergeCells('A2:B2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A3:B3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A4:B4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A5:B5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A6:B6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A7:B7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A8:B8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A9:B9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A10:B10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A11:B11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A12:B12'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C2:D2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C3:D3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C4:D4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C5:D5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C6:D6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C7:D7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C8:D8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C9:D9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C10:D10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C11:D11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C12:D12'); // Menggabungkan sel untuk judul tabel

        $sheet->getStyle('A2:B12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('I2:J12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('C2:C12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('K7:K12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set judul tabel laporan
        $sheet->setCellValue('A14', 'Report Amortised Cost - Treasury Bonds');
        $sheet->mergeCells('A14:K14'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A14')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A14')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A14')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A14')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(15)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Transaction Date',
            'Days Interest',
            'Payment Amount',
            'Principal Payment',
            'Coupon Recognition',
            'Coupon Payment',
            'Amortized',
            'Carrying Amount',
            'Cummulative Amortized',
            'Unamortized'
        ];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '16', $header);
            $sheet->getStyle($columnIndex . '16')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '16')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '16')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '16')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '16')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }
        $sheet->getStyle('A16:K16')->getAlignment()->setWrapText(true);

        $atdisc = $reports->first()->atdiscount;
        $atpremium = $reports->first()->atpremium;
        if ($atdisc > 0){
            $unamort = $atdisc * (-1);
        } else {
            $unamort = $atpremium;
        }

        $row = 17;

        foreach ($reports as $report) {
            $amortized = (float)$report->amortized;
            if ($report->month_to > 0) {
                $unamort += $amortized;
                // if ($atdisc > 0){
                //     $unamort += $amortized;
                // } else {
                //     $unamort += $amortized;
                // }
            }

        // foreach ($reports as $report) {
        //     if ($report->month_to > 0) {
        //         $unamort += $report->amortized;
        //     }
    
            $sheet->setCellValue('A' . $row, $report->month_to);
            $sheet->setCellValue('B' . $row, $report->tglangsuranconv);
            $sheet->setCellValue('C' . $row, number_format($report->haribunga, 0));
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt, 0));
            $sheet->setCellValue('E' . $row, number_format($report->principal_in, 0));
            $sheet->setCellValue('F' . $row, number_format($report->interest_eir, 0));
            $sheet->setCellValue('G' . $row, number_format($report->interest, 0));
            $sheet->setCellValue('H' . $row, number_format($report->amortized, 0));
            $sheet->setCellValue('I' . $row, number_format($report->fair_value, 0));
            $sheet->setCellValue('J' . $row, number_format($report->cum_amortitized, 0));
            $sheet->setCellValue('K' . $row, number_format($unamort, 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        // $sheet->getStyle('D' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue('A' . $row, "TOTAL");
        $sheet->mergeCells('A' . $row . ':B' . $row); 
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('C' . $row, number_format($reports->sum('haribunga')));
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, number_format($reports->sum('pmtamt')));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($reports->sum('principal_in')));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($reports->sum('interest_eir')));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($reports->sum('interest')));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($reports->sum('amortized')));

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $sheet->getStyle('A16:K16')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A16:K16')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A16:K16')->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(17);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(22);
        $sheet->getColumnDimension('K')->setWidth(24);

        // Set border untuk semua data laporan
        $sheet->getStyle('A16:K' . ($row))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        // foreach (range('A', 'K') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        // Siapkan nama file
        $filename = "securities_amortised_cost_$no_acc.xlsx";

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
        $sheet->setCellValue('A2', 'Account Number');
        $sheet->setCellValue('C2', ': ' . $firstReport->no_acc);
        $sheet->setCellValue('A3', 'Deal Number');
        $sheet->setCellValue('C3', ': ' . $firstReport->bond_id);
        $sheet->setCellValue('A4', 'Issuer Name');
        $sheet->setCellValue('C4', ': ' . $firstReport->issuer_name);
        $sheet->setCellValue('A5', 'Face Value');
        $sheet->setCellValue('C5', ': ' . number_format($firstReport->face_value,0));
        $sheet->setCellValue('A6', 'Settlement Date');
        $sheet->setCellValue('C6', ': ' . $firstReport->settle_dt);
        $sheet->setCellValue('A7', 'Tenor (TTM)');
        $sheet->setCellValue('C7', ': ' . $firstReport->tenor . ' Year');
        $sheet->setCellValue('A8', 'Maturity Date');
        $sheet->setCellValue('C8', ': ' . $firstReport->mtr_date);
        $sheet->setCellValue('A9', 'Coupon Rate');
        $sheet->setCellValue('C9', ': ' . number_format($firstReport->coupon_rate*100,5) . '%');
        $sheet->setCellValue('A10', 'Yield (YTM)');
        $sheet->setCellValue('C10', ': ' . number_format($firstReport->yield*100,5) . '%');
        $sheet->setCellValue('A11', 'Price');
        $sheet->setCellValue('C11', ': ' . number_format($firstReport->price*100,5) . '%');
        $sheet->setCellValue('A12', 'Fair Value');
        $sheet->setCellValue('C12', ': ' . number_format($firstReport->fair_value,0));

        $sheet->setCellValue('J7', 'At Discount	');
        $sheet->setCellValue('K7', ': -' . number_format($firstReport->atdiscount,0));
        $sheet->setCellValue('J8', 'At Premium');
        $sheet->setCellValue('K8', ': ' . number_format($firstReport->atpremium,0));
        $sheet->setCellValue('J9', 'Brokerage Fee');
        $sheet->setCellValue('K9', ': ' . number_format($firstReport->brokerage, 0));
        $sheet->setCellValue('J10', 'Carrying Amount');
        $sheet->setCellValue('K10', ': ' . number_format($firstReport->carrying_amount, 0));
        $sheet->setCellValue('J11', 'EIR Exposure');
        $sheet->setCellValue('K11', ': ' . number_format($firstReport->eirex*100, 14) . '%');
        $sheet->setCellValue('J12', 'EIR Calculated');
        $sheet->setCellValue('K12', ': ' . number_format($firstReport->eircalc*100, 14) . '%');

        $sheet->mergeCells('A2:B2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A3:B3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A4:B4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A5:B5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A6:B6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A7:B7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A8:B8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A9:B9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A10:B10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A11:B11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('A12:B12'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C2:D2'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C3:D3'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C4:D4'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C5:D5'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C6:D6'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C7:D7'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C8:D8'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C9:D9'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C10:D10'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C11:D11'); // Menggabungkan sel untuk judul tabel
        $sheet->mergeCells('C12:D12'); // Menggabungkan sel untuk judul tabel

        $sheet->getStyle('A2:B12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('I2:J12')->getFont()->setBold(true); // Set bold untuk Account Number
        $sheet->getStyle('C2:C12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('K7:K12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set judul tabel laporan
        $sheet->setCellValue('A14', 'Report Amortised Cost - Treasury Bonds');
        $sheet->mergeCells('A14:K14'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A14')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A14')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A14')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A14')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(15)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'Month',
            'Transaction Date',
            'Days Interest',
            'Payment Amount',
            'Principal Payment',
            'Coupon Recognition',
            'Coupon Payment',
            'Amortized',
            'Carrying Amount',
            'Cummulative Amortized',
            'Unamortized'
        ];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '16', $header);
            $sheet->getStyle($columnIndex . '16')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '16')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '16')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '16')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '16')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }
        $sheet->getStyle('A16:K16')->getAlignment()->setWrapText(true);

        $atdisc = $reports->first()->atdiscount;
        $atpremium = $reports->first()->atpremium;
        if ($atdisc > 0){
            $unamort = $atdisc * (-1);
        } else {
            $unamort = $atpremium;
        }

        $row = 17;

        foreach ($reports as $report) {
            $amortized = (float)$report->amortized;
            if ($report->month_to > 0) {
                $unamort += $amortized;
                // if ($atdisc > 0){
                //     $unamort += $amortized;
                // } else {
                //     $unamort += $amortized;
                // }
            }

        // foreach ($reports as $report) {
        //     if ($report->month_to > 0) {
        //         $unamort += $report->amortized;
        //     }
    
            $sheet->setCellValue('A' . $row, $report->month_to);
            $sheet->setCellValue('B' . $row, $report->tglangsuranconv);
            $sheet->setCellValue('C' . $row, number_format($report->haribunga, 0));
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt, 0));
            $sheet->setCellValue('E' . $row, number_format($report->principal_in, 0));
            $sheet->setCellValue('F' . $row, number_format($report->interest_eir, 0));
            $sheet->setCellValue('G' . $row, number_format($report->interest, 0));
            $sheet->setCellValue('H' . $row, number_format($report->amortized, 0));
            $sheet->setCellValue('I' . $row, number_format($report->fair_value, 0));
            $sheet->setCellValue('J' . $row, number_format($report->cum_amortitized, 0));
            $sheet->setCellValue('K' . $row, number_format($unamort, 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
        // $sheet->getStyle('D' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue('A' . $row, "TOTAL");
        $sheet->mergeCells('A' . $row . ':B' . $row); 
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('C' . $row, number_format($reports->sum('haribunga')));
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, number_format($reports->sum('pmtamt')));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($reports->sum('principal_in')));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($reports->sum('interest_eir')));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($reports->sum('interest')));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($reports->sum('amortized')));

        // Mengatur border untuk tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $sheet->getStyle('A16:K16')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A16:K16')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A16:K16')->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(17);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(22);
        $sheet->getColumnDimension('K')->setWidth(24);

        // Set border untuk semua data laporan
        $sheet->getStyle('A16:K' . ($row))->applyFromArray($styleArray);

    // Siapkan nama file
    $filename = "securities_amortised_cost_$no_acc.pdf";

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
