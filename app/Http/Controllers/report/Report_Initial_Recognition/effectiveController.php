<?php

namespace App\Http\Controllers\report\Report_Initial_Recognition;

use App\Http\Controllers\Controller;
use App\Models\InitialRecognitionEffective;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\DB;

class effectiveController extends Controller
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

        $loans = InitialRecognitionEffective::getInitialRecognition($branch, $tahun, $bulan);

        // dd($loans);
        
        return view('report.initial_recognition.effective.master', compact('loans', 'bulan', 'tahun', 'user', 'isSuperAdmin'));
    }

    public function exportPdf(Request $request, $id_pt)
    {
        $user = Auth::user();
        
        $bulan = $request->input('bulan', date('n')); 
        $tahun = $request->input('tahun', date('Y'));

        $loans = InitialRecognitionEffective::getInitialRecognition($id_pt, $tahun, $bulan);

        if (empty($loans)) {
            return response()->json([
                'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
                'details' => [
                    'branch' => $id_pt,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]
            ], 404);
        }

        // Create new spreadsheet
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
        $sheet->setCellValue('A1', 'REPORT INITIAL RECOGNITION NEW LOAN BY ENTITY - CONTRACTUAL EFFECTIVE');
        $sheet->mergeCells('A1:W1');
        
        // Set headers
        $headers = [
            'No.', 'Entity Number', 'Account Number', 'Debitor Name', 'GL Account',
            'Loan Type', 'GL Group', 'Original Date', 'Term (Months)', 'Interest Rate',
            'Maturity Date', 'Payment Amount', 'Original Balance', 'Current Balance',
            'Carrying Amount', 'EIR Amortised Cost Exposure', 'EIR Amortised Cost Calculated',
            'EIR Calculated Convertion', 'EIR Calculated Transaction Cost',
            'EIR Calculated UpFront Fee', 'Outstanding Amount',
            'Outstanding Amount Initial Transaction Cost', 'Outstanding Amount Initial UpFront Fee'
        ];

        foreach (range('A', 'W') as $index => $column) {
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
            $sheet->setCellValue('O' . $row, $loan->baleir);
            $sheet->setCellValue('P' . $row, $loan->eirex * 100);
            $sheet->setCellValue('Q' . $row, $loan->eircalc * 100);
            $sheet->setCellValue('R' . $row, $loan->eircalc_conv * 100);
            $sheet->setCellValue('S' . $row, $loan->eircalc_cost * 100);
            $sheet->setCellValue('T' . $row, $loan->eircalc_fee * 100);
            $sheet->setCellValue('U' . $row, $loan->outsamtconv);
            $sheet->setCellValue('V' . $row, $loan->outsamtcost);
            $sheet->setCellValue('W' . $row, $loan->outsamtfee);
            $row++;
        }

        // Set filename
        $filename = "ReportInitialRecognitionEffective_{$id_pt}_{$bulan}_{$tahun}.xlsx";

        // Create writer and save Excel file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        // Return Excel response
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
