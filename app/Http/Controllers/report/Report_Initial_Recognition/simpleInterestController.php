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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection; 

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
        
        $namaBulan = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        
        $bulan = $request->input('bulan', date('n')); 
        $tahun = $request->input('tahun', date('Y'));

        $bulanNama = $namaBulan[$bulan];

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
        $bulanAngka =  $request->input('bulan', date('n'));
        $loanFirst = $loans[0];

                // Buat spreadsheet baru
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                
                // // Set page orientation and size
                // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                // $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                // $sheet->getPageMargins()->setTop(0.5);
                // $sheet->getPageMargins()->setRight(0.5);
                // $sheet->getPageMargins()->setLeft(0.5);
                // $sheet->getPageMargins()->setBottom(0.5);
        
                if (is_array($loans) && count($loans) > 0) {
                    $loanFirst = $loans[0];
        
            // Set informasi pinjaman
            $sheet->setCellValue('A2', 'Entity Number');
            $sheet->getStyle('A2')->getFont()->setBold(true); 
            $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('B2', $loanFirst->no_branch);
            $sheet->setCellValue('A3', 'Entitiy Name');
            $sheet->getStyle('A3')->getFont()->setBold(true);
            $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('B3', $loanFirst->jdname);
            $sheet->setCellValue('A4', 'Date Of Report');
            $sheet->getStyle('A4')->getFont()->setBold(true);
            $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('B4', $bulanNama . ' - ' . $tahun);
            }else{
            $sheet->setCellValue('B2', 'Tidak ada data');
            $sheet->setCellValue('B3', 'Tidak ada data');
            }
        
               // Set title
               $sheet->setCellValue('A6', 'REPORT INITIAL RECOGNITION NEW LOAN BY ENTITY - CONTRACTUAL SIMPLE INTEREST');
               $sheet->mergeCells('A6:V6'); // Menggabungkan sel untuk judul tabel
               $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
               $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
               $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
               $sheet->getStyle('A6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
               $sheet->getStyle('A6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
                
                // Set headers
                $headers = [
                    'No.', 'Entity Number', 'Account Number', 'Debitor Name', 'GL Account',
                    'Loan Type', 'GL Group', 'Original Date', 'Term (Months)', 'Interest Rate',
                    'Maturity Date','Original Balance', 'Current Balance', 'Carrying Amount', 'EIR Amortised Cost Exposure', 'EIR Amortised Cost Calculated', 'EIR Calculated Convertion', 'EIR Calculated Transaction Cost', 'EIR Calculated UpFront Fee', 'Outstanding Amount', 'Outstanding Amount Initial Transaction Cost', 'Outstanding Amount Initial UpFront Fee'
                ];
        
                $columnIndex = 'A';
                foreach ($headers as $header) {
                $sheet->setCellValue($columnIndex . '8', $header);
                $sheet->getStyle($columnIndex . '8')->getFont()->setBold(true);
                $sheet->getStyle($columnIndex . '8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($columnIndex . '8')->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle($columnIndex . '8')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
                $sheet->getStyle($columnIndex . '8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
                $columnIndex++;
            }
                // Add data
                $row = 9;
                $nourut = 0;
                foreach ($loans as $loan) {
                    $nourut += 1;
                    $sheet->getStyle('A' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('A' . $row, $nourut);
                    $sheet->getStyle('B' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('B' . $row, $loan->no_branch);
                    $sheet->getStyle('C' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('C' . $row, $loan->no_acc);
                    $sheet->getStyle('D' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('D' . $row, $loan->deb_name);
                    $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('E' . $row, $loan->coa);
                    $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('F' . $row, $loan->ln_type);
                    $sheet->getStyle('G' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('G' . $row, $loan->glgroup);
                    $sheet->getStyle('H' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('H' . $row, $loan->orgdtconv);
                    $sheet->getStyle('I' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('I' . $row, $loan->term);
                    $sheet->getStyle('J' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('J' . $row, number_format($loan->rate * 100, 5). '%');
                    $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('K' . $row, $loan->mtrdtconv);
                    $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('L' . $row, number_format($loan->org_bal));
                    $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('M' . $row, number_format($loan->oldbal));
                    $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('N' . $row, number_format($loan->baleir));
                    $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('O' . $row, number_format($loan->eirex * 100, 14).'%');
                    $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('P' . $row, number_format($loan->eircalc * 100, 14).'%');
                    $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('Q' . $row, number_format($loan->eircalc_conv * 100, 14).'%');
                    $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('R' . $row, number_format($loan->eircalc_cost * 100, 14).'%');
                    $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('S' . $row, number_format($loan->eircalc_fee * 100, 14).'%');
                    $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('T' . $row, number_format($loan->outsamtconv));
                    $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('U' . $row, number_format($loan->outsamtcost));
                    $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('V' . $row, number_format($loan->outsamtfee));
        
                    $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);
        
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                        $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF');
                }
                $row++;
                }
                $loansCollection = new Collection($loans);
                $averageRate = $loansCollection->avg('rate');
                $averageEirex = $loansCollection->avg('eirex'); 
                $averageEircalc = $loansCollection->avg('eircalc'); 
                $averageEircalcConv = $loansCollection->avg('eircalc_conv'); 
                $averageRateEircalcCost = $loansCollection->avg('eircalc_cost'); 
                $averageEircalcFee = $loansCollection->avg('eircalc_fee');
                $totalPmtamt = $loansCollection->sum('pmtamt');
                $totalOrgBal = $loansCollection->sum('org_bal');
                $totalOldbal = $loansCollection->sum('oldbal');
                $totalOutsamtconv = $loansCollection->sum('outsamtconv');
                $totalOutsamtcost = $loansCollection->sum('outsamtcost');
                $totalOutsamtfee = $loansCollection->sum('outsamtfee');
        
                $sheet->setCellValue('A' . $row, "TOTAL:");
                $sheet->mergeCells('A' . $row . ':I' . $row); 
                $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('J' . $row, number_format($averageRate * 100, 5) . '%');
                $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('K' . $row, null);
                $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('L' . $row, number_format($totalOrgBal));
                $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('M' . $row,  number_format($totalOldbal));
                $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('N' . $row, null);
                $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('O' . $row, number_format($averageEirex*100, 14).'%');
                $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('P' . $row, number_format($averageEircalc*100, 14).'%');
                $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('Q' . $row, number_format($averageEircalcConv*100, 14).'%');
                $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('R' . $row, number_format($averageRateEircalcCost*100, 14).'%');
                $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('S' . $row, number_format($averageEircalcFee*100, 14).'%');
                $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('T' . $row, number_format($totalOutsamtconv));
                $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('U' . $row, number_format($totalOutsamtcost));
                $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('V' . $row, number_format($totalOutsamtfee));
        
                $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);
        
               // Menambahkan warna latar belakang alternatif pada baris data
           
                $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris
        
        
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
            $sheet->getStyle('A6:V6')->applyFromArray($styleArray);
        
            // Set border untuk semua data laporan
            $sheet->getStyle('A8:V' . $row)->applyFromArray($styleArray);
        
            // Mengatur lebar kolom agar lebih rapi
            foreach (range('A', 'V') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }


        $filename = "ReportInitialRecognitionCorporateLoan_{$id_pt}_{$bulanNama}_{$tahun}.xlsx";
        
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf(Request $request, $id_pt)
    {
        $user = Auth::user();

        $namaBulan = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        
        $bulan = $request->input('bulan', date('n')); 
        $tahun = $request->input('tahun', date('Y'));

        $bulanNama = $namaBulan[$bulan];

        $loans = InitialRecognitionSimpleInterest::getInitialRecognition($id_pt, $tahun, $bulan);

        if (empty($loans)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang sesuai dengan kriteria yang dipilih',
                'details' => [
                    'branch' => $id_pt,
                    'bulan' => $bulanNama,
                    'tahun' => $tahun
                ]
            ], 404);
        }

        $bulanAngka =  $request->input('bulan', date('n'));
        $loanFirst = $loans[0];

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        
        // // Set page orientation and size
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // $sheet->getPageMargins()->setTop(0.5);
        // $sheet->getPageMargins()->setRight(0.5);
        // $sheet->getPageMargins()->setLeft(0.5);
        // $sheet->getPageMargins()->setBottom(0.5);

        if (is_array($loans) && count($loans) > 0) {
            $loanFirst = $loans[0];

    // Set informasi pinjaman
    $sheet->setCellValue('A2', 'Entity Number');
    $sheet->getStyle('A2')->getFont()->setBold(true); 
    $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('B2', $loanFirst->no_branch);
    $sheet->setCellValue('A3', 'Entitiy Name');
    $sheet->getStyle('A3')->getFont()->setBold(true);
    $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('B3', $loanFirst->jdname);
    $sheet->setCellValue('A4', 'Date Of Report');
    $sheet->getStyle('A4')->getFont()->setBold(true);
    $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('B4', $bulanNama . ' - ' . $tahun);
    }else{
    $sheet->setCellValue('B2', 'Tidak ada data');
    $sheet->setCellValue('B3', 'Tidak ada data');
    }

       // Set title
       $sheet->setCellValue('A6', 'REPORT INITIAL RECOGNITION NEW LOAN BY ENTITY - CONTRACTUAL SIMPLE INTEREST');
       $sheet->mergeCells('A6:V6'); // Menggabungkan sel untuk judul tabel
       $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
       $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
       $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
       $sheet->getStyle('A6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
       $sheet->getStyle('A6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        
        // Set headers
        $headers = [
            'No.', 'Entity Number', 'Account Number', 'Debitor Name', 'GL Account',
            'Loan Type', 'GL Group', 'Original Date', 'Term (Months)', 'Interest Rate',
            'Maturity Date','Original Balance', 'Current Balance', 'Carrying Amount', 'EIR Amortised Cost Exposure', 'EIR Amortised Cost Calculated', 'EIR Calculated Convertion', 'EIR Calculated Transaction Cost', 'EIR Calculated UpFront Fee', 'Outstanding Amount', 'Outstanding Amount Initial Transaction Cost', 'Outstanding Amount Initial UpFront Fee'
        ];

        $columnIndex = 'A';
        foreach ($headers as $header) {
        $sheet->setCellValue($columnIndex . '8', $header);
        $sheet->getStyle($columnIndex . '8')->getFont()->setBold(true);
        $sheet->getStyle($columnIndex . '8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($columnIndex . '8')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($columnIndex . '8')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
        $sheet->getStyle($columnIndex . '8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $columnIndex++;
    }
        // Add data
        $row = 9;
        $nourut = 0;
        foreach ($loans as $loan) {
            $nourut += 1;
            $sheet->getStyle('A' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A' . $row, $nourut);
            $sheet->getStyle('B' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, $loan->no_branch);
            $sheet->getStyle('C' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('C' . $row, $loan->no_acc);
            $sheet->getStyle('D' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('D' . $row, $loan->deb_name);
            $sheet->getStyle('E' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('E' . $row, $loan->coa);
            $sheet->getStyle('F' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('F' . $row, $loan->ln_type);
            $sheet->getStyle('G' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('G' . $row, $loan->glgroup);
            $sheet->getStyle('H' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H' . $row, $loan->orgdtconv);
            $sheet->getStyle('I' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('I' . $row, $loan->term);
            $sheet->getStyle('J' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('J' . $row, number_format($loan->rate * 100, 5). '%');
            $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('K' . $row, $loan->mtrdtconv);
            $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('L' . $row, number_format($loan->org_bal));
            $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('M' . $row, number_format($loan->oldbal));
            $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('N' . $row, number_format($loan->baleir));
            $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('O' . $row, number_format($loan->eirex * 100, 14).'%');
            $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('P' . $row, number_format($loan->eircalc * 100, 14).'%');
            $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('Q' . $row, number_format($loan->eircalc_conv * 100, 14).'%');
            $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('R' . $row, number_format($loan->eircalc_cost * 100, 14).'%');
            $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('S' . $row, number_format($loan->eircalc_fee * 100, 14).'%');
            $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('T' . $row, number_format($loan->outsamtconv));
            $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('U' . $row, number_format($loan->outsamtcost));
            $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('V' . $row, number_format($loan->outsamtfee));

            $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF');
        }
        $row++;
        }
        $loansCollection = new Collection($loans);
        $averageRate = $loansCollection->avg('rate');
        $averageEirex = $loansCollection->avg('eirex'); 
        $averageEircalc = $loansCollection->avg('eircalc'); 
        $averageEircalcConv = $loansCollection->avg('eircalc_conv'); 
        $averageRateEircalcCost = $loansCollection->avg('eircalc_cost'); 
        $averageEircalcFee = $loansCollection->avg('eircalc_fee');
        $totalPmtamt = $loansCollection->sum('pmtamt');
        $totalOrgBal = $loansCollection->sum('org_bal');
        $totalOldbal = $loansCollection->sum('oldbal');
        $totalOutsamtconv = $loansCollection->sum('outsamtconv');
        $totalOutsamtcost = $loansCollection->sum('outsamtcost');
        $totalOutsamtfee = $loansCollection->sum('outsamtfee');

        $sheet->setCellValue('A' . $row, "TOTAL:");
        $sheet->mergeCells('A' . $row . ':I' . $row); 
        $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('J' . $row, number_format($averageRate * 100, 5) . '%');
        $sheet->getStyle('K' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $row, null);
        $sheet->getStyle('L' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('L' . $row, number_format($totalOrgBal));
        $sheet->getStyle('M' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('M' . $row,  number_format($totalOldbal));
        $sheet->getStyle('N' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('N' . $row, null);
        $sheet->getStyle('O' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('O' . $row, number_format($averageEirex*100, 14).'%');
        $sheet->getStyle('P' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('P' . $row, number_format($averageEircalc*100, 14).'%');
        $sheet->getStyle('Q' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('Q' . $row, number_format($averageEircalcConv*100, 14).'%');
        $sheet->getStyle('R' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('R' . $row, number_format($averageRateEircalcCost*100, 14).'%');
        $sheet->getStyle('S' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('S' . $row, number_format($averageEircalcFee*100, 14).'%');
        $sheet->getStyle('T' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('T' . $row, number_format($totalOutsamtconv));
        $sheet->getStyle('U' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('U' . $row, number_format($totalOutsamtcost));
        $sheet->getStyle('V' . $row, $nourut)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('V' . $row, number_format($totalOutsamtfee));

        $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);

       // Menambahkan warna latar belakang alternatif pada baris data
   
        $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row . ':V' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris


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
    $sheet->getStyle('A6:V6')->applyFromArray($styleArray);

    // Set border untuk semua data laporan
    $sheet->getStyle('A8:V' . $row)->applyFromArray($styleArray);

    // Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'V') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

        // Set pengaturan untuk PDF
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        
        // Siapkan direktori untuk menyimpan file sementara
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');

        // Simpan file PDF
        $writer->save($temp_file);

        $filename = "ReportInitialRecognitionCorporateLoan_{$id_pt}_{$bulanNama}_{$tahun}.pdf";

        // Kembalikan response PDF
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
