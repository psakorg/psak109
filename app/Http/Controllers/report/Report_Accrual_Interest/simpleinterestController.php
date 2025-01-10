<?php

namespace App\Http\Controllers\report\Report_Accrual_Interest;

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
        // dd($loans);
        return view('report.accrual_interest.simple_interest.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt)
    {
        // dd($no_acc, $id_pt); // Debugging parameterrr
        $no_acc = trim($no_acc);
        $loan = report_simpleinterest::getLoanDetails($no_acc, $id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc, $id_pt);
        // dd($loan, $reports);
        if (!$loan) {
            abort(404, 'Loan not found');
        }

        return view('report.accrual_interest.simple_interest.view', compact('loan', 'reports'));

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

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(30);
        $entityName = "PT. PACIFIC MULTI FINANCE";
        $infoRows = [
            ['Entity Name', ':', $entityName],
            ['Account Number', ':', "'" . $loan->no_acc],
            ['Debitor Name', ':', $loan->deb_name],
            ['Original Amount', ':', number_format($loan->org_bal, 2)],
            ['Term', ':', $loan->term . ' Month'],
            ['Interest Rate', ':', number_format($loan->rate*100, 5) . '%'],
        ];


        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $currentRow, $info[0]);
            $sheet->setCellValue('B' . $currentRow, $info[1]);
            $sheet->setCellValue('C' . $currentRow, $info[2]);
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $currentRow++;
        }
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(5);
        $sheet->getColumnDimension('K')->setWidth(30);
        $infoRows = [
        ['Outstanding Amount', ':', number_format($loan->nbal, 2)],
        ['EIR Conversion Calculated', ':', number_format($loan->eircalc_conv * 100, 14) . '%'],
        ['Original Loan Date', ':',date('d-m-Y', strtotime($loan->org_date))],
        ['Maturity Loan Date', ':', date('d-m-Y', strtotime($loan->mtr_date))],
        ];
        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('I' . $currentRow, $info[0]);
            $sheet->setCellValue('J' . $currentRow, $info[1]);
            $sheet->setCellValue('K' . $currentRow, $info[2]);
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $currentRow++;
        }

        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Accrual Interest Simple Interest Report - Report Details');
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
            'Withdrawal', 
            'Reimbursement', 
            'Accrued Interest', 
            'Interest Payment', 
            'Time Gap', 
            'Outstanding Amount', 
            'Cummulative Time Gap'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '11', $header);
            $sheet->getStyle($columnIndex . '11')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '11')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '11')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '11')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 12; // Mulai dari baris 13 untuk data laporan
        $cumharibunga = 0;
        $cumpmtamt = 0;
        $cumpenarikan = 0;
        $cumpengembalian = 0;
        $cumaccrconv = 0;
        $cumbunga = 0;
        $cumoutsamtconv = 0;
        $cumtimegap = 0;
        foreach ($reports as $report) {
            $cumharibunga += $report->haribunga;
            $cumpmtamt += $report->pmtamt;
            $cumpenarikan += $report->penarikan;
            $cumpengembalian += $report->pengembalian;
            $cumaccrconv += $report->accrconv;
            $cumbunga += $report->bunga;
            $cumoutsamtconv += $report->outsamtconv;
            $cumtimegap += $report->timegap;
            //$timegap = $report->timegap;
            //$cumulativeTimeGap += floatval($report->timegap);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($report->tglangsuran)));
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('C' . $row, number_format($report->haribunga ?? 0));
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt ?? 0));
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('E' . $row, number_format($report->penarikan ?? 0));
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('F' . $row, number_format($report->pengembalian ?? 0));
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('G' . $row, number_format($report->accrconv ?? 0));
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, number_format($report->bunga ?? 0));
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('I' . $row, number_format($report->timegap ?? 0));
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('J' . $row, number_format($report->outsamtconv ?? 0));
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('K' . $row, number_format($cumtimegap ?? 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            //if ($row % 2 == 0) {
            //     $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            //     $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            // }

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('C' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        //TOTAL ACCRUAL INTEREST
        $sheet->setCellValue('A' . $row, "TOTAL");
        $sheet->mergeCells('A' . $row . ':B' . $row); 
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $row, number_format($cumharibunga ?? 0));
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, number_format($cumpmtamt ?? 0));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($cumpenarikan ?? 0));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($cumpengembalian ?? 0));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($cumaccrconv ?? 0));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($cumbunga ?? 0));
        $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('I' . $row, number_format($cumtimegap ?? 0));
        $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('J' . $row, null);
        $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $row, null);

        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
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
        $sheet->getStyle('A10:K10')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A11:K' . ($row))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Siapkan nama file
        $filename = "ReportCalculatedAccrualInterestCorporateLoan_$no_acc.xlsx";

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

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(30);
        $entityName = "PT. PACIFIC MULTI FINANCE";
        $infoRows = [
            ['Entity Name', ':', $entityName],
            ['Account Number', ':', "'" . $loan->no_acc],
            ['Debitor Name', ':', $loan->deb_name],
            ['Original Amount', ':', number_format($loan->org_bal, 2)],
            ['Term', ':', $loan->term . ' Month'],
            ['Interest Rate', ':', number_format($loan->rate*100, 5) . '%'],
        ];


        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $currentRow, $info[0]);
            $sheet->setCellValue('B' . $currentRow, $info[1]);
            $sheet->setCellValue('C' . $currentRow, $info[2]);
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $currentRow++;
        }
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(5);
        $sheet->getColumnDimension('K')->setWidth(30);
        $infoRows = [
        ['Outstanding Amount', ':', number_format($loan->nbal, 2)],
        ['EIR Conversion Calculated', ':', number_format($loan->eircalc_conv * 100, 14) . '%'],
        ['Original Loan Date', ':',date('d-m-Y', strtotime($loan->org_date))],
        ['Maturity Loan Date', ':', date('d-m-Y', strtotime($loan->mtr_date))],
        ];
        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('I' . $currentRow, $info[0]);
            $sheet->setCellValue('J' . $currentRow, $info[1]);
            $sheet->setCellValue('K' . $currentRow, $info[2]);
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $currentRow++;
        }

        // Set judul tabel laporan
        $sheet->setCellValue('A10', 'Accrual Interest Simple Interest Report - Report Details');
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
            'Withdrawal', 
            'Reimbursement', 
            'Accrued Interest', 
            'Interest Payment', 
            'Time Gap', 
            'Outstanding Amount', 
            'Cummulative Time Gap'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '11', $header);
            $sheet->getStyle($columnIndex . '11')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '11')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '11')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '11')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 12; // Mulai dari baris 13 untuk data laporan
        $cumharibunga = 0;
        $cumpmtamt = 0;
        $cumpenarikan = 0;
        $cumpengembalian = 0;
        $cumaccrconv = 0;
        $cumbunga = 0;
        $cumoutsamtconv = 0;
        $cumtimegap = 0;
        foreach ($reports as $report) {
            $cumharibunga += $report->haribunga;
            $cumpmtamt += $report->pmtamt;
            $cumpenarikan += $report->penarikan;
            $cumpengembalian += $report->pengembalian;
            $cumaccrconv += $report->accrconv;
            $cumbunga += $report->bunga;
            $cumoutsamtconv += $report->outsamtconv;
            $cumtimegap += $report->timegap;
            //$timegap = $report->timegap;
            //$cumulativeTimeGap += floatval($report->timegap);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($report->tglangsuran)));
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('C' . $row, number_format($report->haribunga ?? 0));
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt ?? 0));
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('E' . $row, number_format($report->penarikan ?? 0));
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('F' . $row, number_format($report->pengembalian ?? 0));
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('G' . $row, number_format($report->accrconv ?? 0));
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, number_format($report->bunga ?? 0));
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('I' . $row, number_format($report->timegap ?? 0));
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('J' . $row, number_format($report->outsamtconv ?? 0));
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('K' . $row, number_format($cumtimegap ?? 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            //if ($row % 2 == 0) {
            //     $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            //     $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            // }

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('C' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        //TOTAL ACCRUAL INTEREST
        $sheet->setCellValue('A' . $row, "TOTAL");
        $sheet->mergeCells('A' . $row . ':B' . $row); 
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $row, number_format($cumharibunga ?? 0));
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, number_format($cumpmtamt ?? 0));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($cumpenarikan ?? 0));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($cumpengembalian ?? 0));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, number_format($cumaccrconv ?? 0));
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, number_format($cumbunga ?? 0));
        $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('I' . $row, number_format($cumtimegap ?? 0));
        $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('J' . $row, null);
        $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $row, null);

        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
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
        $sheet->getStyle('A10:K10')->applyFromArray($styleArray);
        
        // Set border untuk semua data laporan
        $sheet->getStyle('A11:K' . ($row))->applyFromArray($styleArray);

        
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "ReportCalculatedAccrualInterestCorporateLoan_$no_acc.pdf";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');
        $writer->save($temp_file);

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
        $loan = report_simpleinterest::getLoanDetails($no_acc, $id_pt);
        $reports = report_simpleinterest::getReportsByNoAcc($no_acc, $id_pt);

        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        $csvData = [];
        $csvData[] = ['Month', 'Payment Date', 'Payment Amount', 'Accrued Interest', 'Interest Payment', 'Time Gap', 'Outstanding Amount', 'Cummulative Time Gap'];

        $totalTimeGap = 0;
        foreach ($reports as $report) {
            $totalTimeGap += $report->timegap;
            $csvData[] = [
                $report->bulanke,
                date('Y-m-d', strtotime($report->tglangsuran)),
                number_format($report->pmtamt, 2),
                number_format($report->accrconv ?? 0, 2),
                number_format($report->bunga, 2),
                number_format($report->timegap, 2),
                number_format($report->outsamtconv, 2),
                number_format($totalTimeGap, 2)
            ];
        }

        $filename = "accrual_interest_report_$no_acc.csv";
        $handle = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->streamDownload(function() use ($handle) {
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
    
}
