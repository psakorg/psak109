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

        // Set informasi pinjaman
        //$sheet->setCellValue('A2', 'Entitiy Name');
        //$sheet->getStyle('A2')->getFont()->setBold(true);
        //$entitiyName = 'PT. PACIFIC MULTI FINANCE';
        //$sheet->setCellValue('B2', $entitiyName); 

        $sheet->setCellValue('A2', 'Account Number');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        //$sheet->setCellValue('B3', ':');
        $sheet->setCellValue('B2', $loan->no_acc);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B2')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A3', 'Debitor Name');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        //$sheet->setCellValue('B3', ':');
        $sheet->setCellValue('B3', $loan->deb_name);
        $sheet->getStyle('B3')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A4', 'Original Amount');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        //$sheet->setCellValue('B5', ':');
        $sheet->setCellValue('B4', number_format($loan->org_bal ?? 0,0));
        $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('A5', 'Term');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        //$sheet->setCellValue('B6', ':');
        $sheet->setCellValue('B5', $loan->term . ' Months');
        $sheet->getStyle('B5')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A6', 'Interest Rate');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bol untuk  Interest Rate
        $sheet->setCellValue('B6', number_format($loan->rate*100,5). "%" ?? 0);
        $sheet->getStyle('B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
     
        $sheet->setCellValue('E2', 'Outstanding Amount');
        $sheet->getStyle('E2')->getFont()->setBold(true); // Set bold untuk  Outstanding Amount
        $sheet->setCellValue('F2', number_format($loan->nbal ?? 0));
        $sheet->getStyle('F2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E3', 'EIR Conversion Calculated');
        $sheet->getStyle('E3')->getFont()->setBold(true); // Set bold untuk EIR Conversion Calculated
        $sheet->setCellValue('F3', number_format($loan->eirex*100,14). "%" ?? 0);
        $sheet->getStyle('F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E4', 'Original Loan Date');
        $sheet->getStyle('E4')->getFont()->setBold(true); // Set bold untuk Original Loan Date
        $sheet->setCellValue('F4', date('d-M-Y', strtotime($loan->org_date)));
        $sheet->getStyle('F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E5', 'Maturity Date');
        $sheet->getStyle('E5')->getFont()->setBold(true); // Set bold untuk Maturity Date
        $sheet->setCellValue('F5', date('d-M-Y', strtotime($loan->mtr_date)));
        $sheet->getStyle('F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Set judul tabel laporan
        $sheet->setCellValue('A8', 'Accrual Interest Report - Report Details');
        $sheet->mergeCells('A8:K8'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A8')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A8')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Set judul kolom tabel
        $headers = ['Month', 'Transaction Date', 'Days Interest', 'Payment Amount', 'Withdrawal', 'Reimbursement', 'Accrued Interest', 'Interest Payment', 'Time Gap', 'Outstanding Amount', 'Cummulative Time Gap'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '10', $header);
            $sheet->getStyle($columnIndex . '10')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '10')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '10')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Mengisi data laporan ke dalam tabel
        $row = 11; // Mulai dari baris 13 untuk data laporan
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
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('C' . $row, number_format($report->haribunga ?? 0));
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt ?? 0));
            $sheet->setCellValue('E' . $row, number_format($report->penarikan ?? 0));
            $sheet->setCellValue('F' . $row, number_format($report->pengembalian ?? 0));
            $sheet->setCellValue('G' . $row, number_format($report->accrconv ?? 0));
            $sheet->setCellValue('H' . $row, number_format($report->bunga ?? 0));
            $sheet->setCellValue('I' . $row, number_format($report->timegap ?? 0));
            $sheet->setCellValue('J' . $row, number_format($report->outsamtconv ?? 0));
            $sheet->setCellValue('K' . $row, number_format($cumtimegap ?? 0));

            // Mengatur font menjadi bold untuk setiap baris data
            //$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('D' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

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
        $sheet->getStyle('A8:K8')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A10:K' . ($row))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        //TOTAL EXCEL
        $sheet->setCellValue('A' . $row, "TOTAL:");
        $sheet->mergeCells('A' . $row . ':B' . $row); // Merge cells A to J for the Total row
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $row, number_format($cumharibunga));
        $sheet->setCellValue('D' . $row, number_format($cumpmtamt));
        $sheet->setCellValue('E' . $row, number_format($cumpenarikan));
        $sheet->setCellValue('F' . $row, number_format($cumpengembalian));
        $sheet->setCellValue('G' . $row, number_format($cumaccrconv));
        $sheet->setCellValue('H' . $row, number_format($cumbunga));
        $sheet->setCellValue('I' . $row, number_format($cumtimegap));

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

        $entitiyName = 'PT. PACIFIC MULTI FINANCE';
        $teksnoacc = ": $loan->no_acc";
        $teksdebname = ": $loan->deb_name";
        $teksorgbal = number_format($loan->org_bal, 2);
        $teksorgbal = ": $teksorgbal";
        $teksorgdate = date('d/M/Y', strtotime($loan->org_date));
        $teksorgdate = ": $teksorgdate";
        $teksterm = ": $loan->term";
        $teksmtrdate = date('d/M/Y', strtotime($loan->mtr_date));
        $teksmtrdate = ": $teksmtrdate";

        $infoRows = [
            ['No. Account', $teksnoacc],
            ['Debtor Name', $teksdebname],
            ['Original Balance', $teksorgbal],
            ['Original Date', $teksorgdate],
            ['Term', $teksterm . ' Months'],
            ['Maturity Date', $teksmtrdate]
        ];

        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $currentRow, $info[0]);
            $sheet->setCellValue('B' . $currentRow, $info[1]);
        //    $sheet->setCellValue('C' . $currentRow, $info[2]);
            $sheet->getRowDimension($currentRow)->setRowHeight(15);
            $currentRow++;
        }

        $sheet->setCellValue('A10', 'Accrual Interest Report - Report Details');
        $sheet->mergeCells('A10:K10');
        $sheet->getStyle('A10:K10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10:K10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10:K10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10:K10')->getFill()->getStartColor()->setARGB('FF006600');
        $sheet->getStyle('A10:K10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

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
            'Cummulative Time Gap'
        ];

        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '12', $header);
            $sheet->getStyle($columnIndex . '12')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '12')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '12')->getFill()->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($columnIndex . '12')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        $row = 13;
        $totalTimeGap = 0;
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
            $totalTimeGap += $report->timegap;

            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($report->tglangsuran)));
            $sheet->setCellValue('C' . $row, number_format($report->haribunga ?? 0));
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt ?? 0));
            $sheet->setCellValue('E' . $row, number_format($report->penarikan ?? 0));
            $sheet->setCellValue('F' . $row, number_format($report->pengembalian ?? 0));
            $sheet->setCellValue('G' . $row, number_format($report->accrconv ?? 0));
            $sheet->setCellValue('H' . $row, number_format($report->bunga ?? 0));
            $sheet->setCellValue('I' . $row, number_format($report->timegap ?? 0));
            $sheet->setCellValue('J' . $row, number_format($report->outsamtconv ?? 0));
            $sheet->setCellValue('K' . $row, number_format($cumtimegap ?? 0));

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        //TOTAL EXCEL
        $sheet->setCellValue('A' . $row, "TOTAL:");
        $sheet->mergeCells('A' . $row . ':C' . $row); // Merge cells A to J for the Total row
        $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $row, number_format($cumharibunga));
        $sheet->setCellValue('D' . $row, number_format($cumpmtamt));
        $sheet->setCellValue('E' . $row, number_format($cumpenarikan));
        $sheet->setCellValue('F' . $row, number_format($cumpengembalian));
        $sheet->setCellValue('G' . $row, number_format($cumaccrconv));
        $sheet->setCellValue('H' . $row, number_format($cumbunga));
        $sheet->setCellValue('I' . $row, number_format($cumtimegap));
        
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
