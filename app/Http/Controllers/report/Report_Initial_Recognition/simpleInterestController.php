<?php

namespace App\Http\Controllers\report\Report_Initial_Recognition;

use App\Http\Controllers\Controller;
use App\Models\InitialRecognitionSimpleInterest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class simpleInterestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('https://psak.pramatech.id');
        }

        $isSuperAdmin = $user->role === 'superadmin';
        
        $branch = $request->input('branch', $user->id_pt);

        if($branch != $user->id_pt){
            $branch = $user->id_pt;
        }

        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('m');

        // $result1 = InitialRecognitionEffective::getInitialRecognition('999', '2024', '5');;
        // dd($id_pt);

        $loans = InitialRecognitionSimpleInterest::getInitialRecognition($branch, $tahun, $bulan);

        //dd($loans);
        
        return view('report.initial_recognition.simple_interest.master', compact('loans', 'bulan', 'tahun', 'user', 'isSuperAdmin'));
    }

    public function exportExcel(Request $request, $id_pt)
    {
        $user = Auth::user();
        
        $bulan = $request->input('bulan', date('n')); 
        $tahun = $request->input('tahun', date('Y'));

        $loans = InitialRecognitionSimpleInterest::getInitialRecognition($id_pt, $tahun, $bulan);

        if (empty($loans)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
                'details' => [
                    'branch' => $id_pt,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]
            ], 404);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set page orientation and size
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        // Set title
        $sheet->setCellValue('A1', 'REPORT INITIAL RECOGNITION NEW LOAN BY ENTITY - CORPORATE LOAN');
        $sheet->mergeCells('A1:W1');
        
        // Set headers (similar to effective but adjust if needed)
        $headers = [
            'No.', 'Entity Number', 'Account Number', 'Debitor Name', 'GL Account',
            'Loan Type', 'GL Group', 'Original Date', 'Term (Months)', 'Interest Rate',
            'Maturity Date', 'Payment Amount', 'Original Balance', 'Current Balance',
            'Outstanding Amount', 'Outstanding Amount Initial Transaction Cost', 
            'Outstanding Amount Initial UpFront Fee'
        ];

        foreach (range('A', 'Q') as $index => $column) {
            $sheet->setCellValue($column . '2', $headers[$index]);
        }

        // Add data
        $row = 3;
        foreach ($loans as $index => $loan) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $loan->no_branch);
            $sheet->setCellValue('C' . $row, $loan->no_acc);
            $sheet->setCellValue('D' . $row, $loan->deb_name);
            $sheet->setCellValue('E' . $row, $loan->coa);
            $sheet->setCellValue('F' . $row, $loan->ln_type);
            $sheet->setCellValue('G' . $row, $loan->glgroup);
            $sheet->setCellValue('H' . $row, $loan->orgdtconv);
            $sheet->setCellValue('I' . $row, $loan->term);
            $sheet->setCellValue('J' . $row, $loan->rate * 100);
            $sheet->setCellValue('K' . $row, $loan->mtrdtconv);
            $sheet->setCellValue('L' . $row, $loan->pmtamt);
            $sheet->setCellValue('M' . $row, $loan->org_bal);
            $sheet->setCellValue('N' . $row, $loan->oldbal);
            $sheet->setCellValue('O' . $row, $loan->outsamtconv);
            $sheet->setCellValue('P' . $row, $loan->outsamtcost);
            $sheet->setCellValue('Q' . $row, $loan->outsamtfee);
            $row++;
        }

        $filename = "ReportInitialRecognitionCorporateLoan_{$id_pt}_{$bulan}_{$tahun}.xlsx";
        
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf(Request $request, $id_pt)
    {
        $user = Auth::user();
        
        $bulan = $request->input('bulan', date('n')); 
        $tahun = $request->input('tahun', date('Y'));

        $loans = InitialRecognitionSimpleInterest::getInitialRecognition($id_pt, $tahun, $bulan);

        if (empty($loans)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
                'details' => [
                    'branch' => $id_pt,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]
            ], 404);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set page orientation and size
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        // Style for title
        $titleStyle = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        // Style for headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];

        // Style for data cells
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        // Set title
        $sheet->setCellValue('A1', 'REPORT INITIAL RECOGNITION NEW LOAN BY ENTITY - CORPORATE LOAN');
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle('A1:Q1')->applyFromArray($titleStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Set headers
        $headers = [
            'No.', 'Entity Number', 'Account Number', 'Debitor Name', 'GL Account',
            'Loan Type', 'GL Group', 'Original Date', 'Term (Months)', 'Interest Rate',
            'Maturity Date', 'Payment Amount', 'Original Balance', 'Current Balance',
            'Outstanding Amount', 'Outstanding Amount Initial Transaction Cost', 
            'Outstanding Amount Initial UpFront Fee'
        ];

        foreach (range('A', 'Q') as $index => $column) {
            $sheet->setCellValue($column . '2', $headers[$index]);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A2:Q2')->applyFromArray($headerStyle);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Add data
        $row = 3;
        foreach ($loans as $index => $loan) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $loan->no_branch);
            $sheet->setCellValue('C' . $row, $loan->no_acc);
            $sheet->setCellValue('D' . $row, $loan->deb_name);
            $sheet->setCellValue('E' . $row, $loan->coa);
            $sheet->setCellValue('F' . $row, $loan->ln_type);
            $sheet->setCellValue('G' . $row, $loan->glgroup);
            $sheet->setCellValue('H' . $row, $loan->orgdtconv);
            $sheet->setCellValue('I' . $row, $loan->term);
            $sheet->setCellValue('J' . $row, $loan->rate * 100);
            $sheet->setCellValue('K' . $row, $loan->mtrdtconv);
            $sheet->setCellValue('L' . $row, $loan->pmtamt);
            $sheet->setCellValue('M' . $row, $loan->org_bal);
            $sheet->setCellValue('N' . $row, $loan->oldbal);
            $sheet->setCellValue('O' . $row, $loan->outsamtconv);
            $sheet->setCellValue('P' . $row, $loan->outsamtcost);
            $sheet->setCellValue('Q' . $row, $loan->outsamtfee);
            $row++;
        }
        $lastRow = $row - 1;
        $sheet->getStyle('A3:Q'.$lastRow)->applyFromArray($dataStyle);

        // Format numeric columns
        $numericColumns = ['I', 'J', 'L', 'M', 'N', 'O', 'P', 'Q'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle($col.'3:'.$col.$lastRow)->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        // Set pengaturan untuk PDF
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        
        // Siapkan direktori untuk menyimpan file sementara
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');

        // Simpan file PDF
        $writer->save($temp_file);

        $filename = "ReportInitialRecognitionCorporateLoan_{$id_pt}_{$bulan}_{$tahun}.pdf";

        // Kembalikan response PDF
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
