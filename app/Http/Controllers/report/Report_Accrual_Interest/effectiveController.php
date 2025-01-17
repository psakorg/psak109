<?php

namespace App\Http\Controllers\report\Report_Accrual_Interest;

use App\Models\report_effective;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class effectiveController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
        $perPage = $request->input('per_page', 10);
        $loans = report_effective::fetchAll($id_pt, $perPage);
        return view('report.accrual_interest.effective.master', compact('loans'));
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt)
    {
        $no_acc = trim($no_acc);
        $loan = report_effective::getLoanDetails($no_acc, $id_pt);
        $master = report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);

        if (!$loan) {
            return response()->json(['error' => 'Data loan tidak ditemukan'], 404);
        }
        if (!$master) {
            return response()->json(['error' => 'Data master tidak ditemukan'], 404);
        }
        if ($reports->isEmpty()) {
            return response()->json(['error' => 'Data laporan tidak ditemukan'], 404);
        }

        //dd($reports, $loan);

        return view('report.accrual_interest.effective.view', compact('loan', 'reports', 'master'));
    }

    // Method untuk mengekspor data ke Excel
    public function exportExcel($no_acc, $id_pt)
    {
        $loan = report_effective::getLoanDetails($no_acc, $id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);
        $master = report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
        $entityName = DB::table('public.tblobaleffective')
        ->join('public.tbl_pt', 'tblobaleffective.id_pt', '=', 'tbl_pt.id_pt')
        ->where('tblobaleffective.no_branch', $id_pt)
        ->select('tbl_pt.nama_pt')
        ->first();

        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $infoRows = [
            ['Entity Name', ': ' . $entityName->nama_pt],
            ['Account Number', ': ' . $loan->no_acc],
            ['Debitor Name', ': ' . $loan->deb_name],
            ['Original Amount', ': ' . number_format($loan->org_bal, 2)],
            ['Term', ': ' . $master->term . ' Month'],
            ['Interest Rate', ': ' . number_format($master->rate*100, 5) . '%'],
        ];

        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $currentRow, $info[0]);
            $sheet->setCellValue('C' . $currentRow, $info[1]);
            $sheet->getRowDimension($currentRow)->setRowHeight(15);
            $currentRow++;
        }

        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');
        $sheet->mergeCells('A5:B5');
        $sheet->mergeCells('A6:B6');
        $sheet->mergeCells('A7:B7');
        $sheet->mergeCells('A8:B8');
        $sheet->mergeCells('C3:D3');
        $sheet->mergeCells('C4:D4');
        $sheet->mergeCells('C5:D5');
        $sheet->mergeCells('C6:D6');
        $sheet->mergeCells('C7:D7');
        $sheet->mergeCells('C8:D8');

        $infoRows = [
        ['Outstanding Amount', ': ' . number_format($loan->org_bal, 2)],
        ['EIR Conversion Calculated', ': ' . number_format($loan->eircalc_conv * 100, 14) . '%'],
        ['Original Loan Date', ': ' . date('d/m/Y', strtotime($master->org_date_dt))],
        ['Maturity Loan Date', ': ' . date('d/m/Y', strtotime($master->mtr_date_dt))],
        ['Payment Amount', ': ' . number_format($master->pmtamt, 2)],
        ];
        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('F' . $currentRow, $info[0]);
            $sheet->setCellValue('H' . $currentRow, $info[1]);
            $sheet->getRowDimension($currentRow)->setRowHeight(15);
            $currentRow++;
        }
        $sheet->mergeCells('F3:G3');
        $sheet->mergeCells('F4:G4');
        $sheet->mergeCells('F5:G5');
        $sheet->mergeCells('F6:G6');
        $sheet->mergeCells('F7:G7');

        $sheet->setCellValue('A10', 'Accrual Interest Effective Report - Report Details');
        $sheet->mergeCells('A10:H10');
        $sheet->getStyle('A10:H10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10:H10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10:H10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10:H10')->getFill()->getStartColor()->setARGB('FF006600');
        $sheet->getStyle('A10:H10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(11)->setRowHeight(5);

        $headers = [
            'Month',
            'Payment Date',
            'Payment Amount',
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
        $cumulativeTimeGap = 0;
        $totalPaymentAmount = 0;
        $totalAccruedInterest = 0;
        $totalInterestPayment = 0;
        $totalOutstandingAmount = 0;

        foreach ($reports as $report) {
            $accruedInterest = $report->accrconv ?? 0;
            $interestPayment = $report->bunga ?? 0;
            $timegap = $accruedInterest - $interestPayment;
            $cumulativeTimeGap += floatval($timegap);
            $totalTimeGap += $timegap;
            $totalPaymentAmount += $report->pmtamt;
            $totalAccruedInterest += $report->accrconv;
            $totalInterestPayment += $report->bunga;
            $totalOutstandingAmount += $report->outsamtconv;

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($report->tglangsuran)));
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('C' . $row, number_format($report->pmtamt, 0));
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('D' . $row, number_format($report->accrconv ?? 0, 0));
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('E' . $row, number_format($report->bunga, 0));
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('F' . $row, number_format($report->timegap, 0));
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('G' . $row, number_format($report->outsamtconv, 0));
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, number_format($cumulativeTimeGap, 0));

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('C' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }
        //TOTAL ACCRUAL INEREST
        $sheet->setCellValue('A' . $row, "TOTAL");
        $sheet->mergeCells('A' . $row . ':B' . $row); // Merge cells A to J for the Total row
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $row, number_format($totalPaymentAmount ?? 0));
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, number_format($totalAccruedInterest ?? 0));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($totalInterestPayment ?? 0));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($totalTimeGap ?? 0));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, null);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, null);

        // foreach (range('A', 'H') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

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
        $sheet->getStyle('A12:H12')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A13:H' . $row)->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(22);
        $sheet->getColumnDimension('H')->setWidth(24);

        $filename = "ReportCalculatedAccrualInterestEffective_$no_acc.xlsx";
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    // Method untuk mengekspor data ke PDF
    public function exportPdf($no_acc, $id_pt)
    {
        $loan = report_effective::getLoanDetails($no_acc, $id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);
        $master = report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
        $entityName = DB::table('public.tblobaleffective')
        ->join('public.tbl_pt', 'tblobaleffective.id_pt', '=', 'tbl_pt.id_pt')
        ->where('tblobaleffective.no_branch', $id_pt)
        ->select('tbl_pt.nama_pt')
        ->first();

        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $infoRows = [
            ['Entity Name', ': ' . $entityName->nama_pt],
            ['Account Number', ': ' . $loan->no_acc],
            ['Debitor Name', ': ' . $loan->deb_name],
            ['Original Amount', ': ' . number_format($loan->org_bal, 2)],
            ['Term', ': ' . $master->term . ' Month'],
            ['Interest Rate', ': ' . number_format($master->rate*100, 5) . '%'],
        ];

        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $currentRow, $info[0]);
            $sheet->setCellValue('C' . $currentRow, $info[1]);
            $sheet->getRowDimension($currentRow)->setRowHeight(15);
            $currentRow++;
        }

        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');
        $sheet->mergeCells('A5:B5');
        $sheet->mergeCells('A6:B6');
        $sheet->mergeCells('A7:B7');
        $sheet->mergeCells('A8:B8');
        $sheet->mergeCells('C3:D3');
        $sheet->mergeCells('C4:D4');
        $sheet->mergeCells('C5:D5');
        $sheet->mergeCells('C6:D6');
        $sheet->mergeCells('C7:D7');
        $sheet->mergeCells('C8:D8');

        $infoRows = [
        ['Outstanding Amount', ': ' . number_format($loan->org_bal, 2)],
        ['EIR Conversion Calculated', ': ' . number_format($loan->eircalc_conv * 100, 14) . '%'],
        ['Original Loan Date', ': ' . date('d/m/Y', strtotime($master->org_date_dt))],
        ['Maturity Loan Date', ': ' . date('d/m/Y', strtotime($master->mtr_date_dt))],
        ['Payment Amount', ': ' . number_format($master->pmtamt, 2)],
        ];
        $currentRow = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('F' . $currentRow, $info[0]);
            $sheet->setCellValue('H' . $currentRow, $info[1]);
            $sheet->getRowDimension($currentRow)->setRowHeight(15);
            $currentRow++;
        }
        $sheet->mergeCells('F3:G3');
        $sheet->mergeCells('F4:G4');
        $sheet->mergeCells('F5:G5');
        $sheet->mergeCells('F6:G6');
        $sheet->mergeCells('F7:G7');

        $sheet->setCellValue('A10', 'Accrual Interest Effective Report - Report Details');
        $sheet->mergeCells('A10:H10');
        $sheet->getStyle('A10:H10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10:H10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10:H10')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A10:H10')->getFill()->getStartColor()->setARGB('FF006600');
        $sheet->getStyle('A10:H10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(11)->setRowHeight(5);

        $headers = [
            'Month',
            'Payment Date',
            'Payment Amount',
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
            $sheet->getStyle($columnIndex . '12')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '12')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '12')->getFill()->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($columnIndex . '12')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        $row = 13;
        $totalTimeGap = 0;
        $cumulativeTimeGap = 0;
        $totalPaymentAmount = 0;
        $totalAccruedInterest = 0;
        $totalInterestPayment = 0;
        $totalOutstandingAmount = 0;

        foreach ($reports as $report) {
            $accruedInterest = $report->accrconv ?? 0;
            $interestPayment = $report->bunga ?? 0;
            $timegap = $accruedInterest - $interestPayment;
            $cumulativeTimeGap += floatval($timegap);
            $totalTimeGap += $timegap;
            $totalPaymentAmount += $report->pmtamt;
            $totalAccruedInterest += $report->accrconv;
            $totalInterestPayment += $report->bunga;
            $totalOutstandingAmount += $report->outsamtconv;

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($report->tglangsuran)));
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('C' . $row, number_format($report->pmtamt, 0));
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('D' . $row, number_format($report->accrconv ?? 0, 0));
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('E' . $row, number_format($report->bunga, 0));
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('F' . $row, number_format($report->timegap, 0));
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('G' . $row, number_format($report->outsamtconv, 0));
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('H' . $row, number_format($cumulativeTimeGap, 0));

            // Mengatur angka menjadi rata kanan
            $sheet->getStyle('C' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }
        //TOTAL ACCRUAL INEREST
        $sheet->setCellValue('A' . $row, "TOTAL");
        $sheet->mergeCells('A' . $row . ':B' . $row); // Merge cells A to J for the Total row
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $row, number_format($totalPaymentAmount ?? 0));
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, number_format($totalAccruedInterest ?? 0));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $row, number_format($totalInterestPayment ?? 0));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $row, number_format($totalTimeGap ?? 0));
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $row, null);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('H' . $row, null);

        // foreach (range('A', 'H') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

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
        $sheet->getStyle('A12:H12')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A13:H' . $row)->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(22);
        $sheet->getColumnDimension('H')->setWidth(24);

        $filename = "ReportCalculatedAccrualInterestEffective_$no_acc.pdf";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');
        $writer->save($temp_file);

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
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memeriksa data']);
        }
    }

    
}
