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
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\DB;


use Dompdf\Dompdf;
use Dompdf\Options;

class evaluationTreasuryController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    public function index(Request $request)
    {
        $user = Auth::user();

        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('m');
        $hari = $request->input('hari') ?? date('d');

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Get the collection from your custom method
        $loans = report_securities::callEvaluationTreasuryBonds(
            intval($tahun),
            intval($bulan),
            intval($hari),
            $user->id_pt
        );

        $selectedDate = "$tahun-$bulan-$hari";
        $loans = $loans->filter(function ($loan) use ($selectedDate) {
            return date('Y-m-d', strtotime($loan->transac_dt)) == $selectedDate;
        });

        $loans = $loans->sortBy('no_acc');

        $count = $loans->count();

        // Convert collection to paginator
        $loans = new \Illuminate\Pagination\LengthAwarePaginator(
            $loans->forPage($page, $perPage),
            $loans->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('report.securities.report_evaluation_treasury_bond.master', 
            compact('loans', 'tahun', 'bulan', 'hari', 'user', 'page', 'perPage', 'count'));
    }

    public function executeStoredProcedure(Request $request)
    {
        try {
            // Validate the hidden inputs just to be safe
            $request->validate([
                'tahun' => 'required|integer',
                'bulan' => 'required|integer|min:1|max:12',
                'tanggal' => 'required|integer|min:1|max:31',
            ]);

            $user = Auth::user();
            $id_pt = $user->id_pt;

            DB::transaction(function () use ($request, $id_pt) {
                DB::select("CALL securities.spcreatemastersecurities_daily(?, ?, ?, ?)", [
                    $request->tahun,
                    $request->bulan,
                    $request->tanggal,
                    $id_pt
                ]);

                // Execute first stored procedure
                DB::select("CALL securities.spevaluationtreasury_bonds(?, ?, ?, ?)", [
                    $request->tahun,
                    $request->bulan,
                    $request->tanggal,
                    $id_pt
                ]);
                
                // Execute second stored procedure
                DB::select("CALL securities.spoutbaltreasury_daily_bonds(?, ?, ?, ?)", [
                    $id_pt,
                    $request->tahun,
                    $request->bulan,
                    $request->tanggal
                ]);
            });

            return redirect()->back()->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Stored procedures berhasil dijalankan',
                'icon' => 'success'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error executing stored procedures: ' . $e->getMessage());
            return redirect()->back()->with('swal', [
                'title' => 'Error!',
                'text' => 'Gagal menjalankan stored procedures: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // Method untuk menampilkan detail pinjaman berdasarkan nomor akunn
    public function view($no_acc, $id_pt)
    {

        $no_acc = trim($no_acc);
        $loan = report_effective::getLoanDetails($no_acc, $id_pt);
        $master=report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);
        if (!$loan) {
            abort(404, 'Loan not found');
        }

        // dd($loan, $master, $reports);

        return view('report.securities.report_evaluation_treasury_bond.view', compact('loan', 'reports','master'));
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        
        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('m');
        $hari = $request->input('hari') ?? date('d');

        // Get the collection from your custom method
        $loans = report_securities::callEvaluationTreasuryBonds(
            intval($tahun),
            intval($bulan),
            intval($hari),
            $user->id_pt
        );

        // Convert collection to array and filter by date
        $selectedDate = "$tahun-$bulan-$hari";
        $loans = $loans->filter(function ($loan) use ($selectedDate) {
            return date('Y-m-d', strtotime($loan->transac_dt)) == $selectedDate;
        });

        $loans = $loans->sortBy('no_acc');
        
        if ($loans->isEmpty()) {
            return response()->json(['message' => 'No data found.'], 404);
        }

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set page orientation and margins
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        // Write header information
        $sheet->setCellValue('B1', 'Branch Number');
        $sheet->setCellValue('D1', ': ' . ($loans->first()->no_branch ?? ''));
        $sheet->setCellValue('B2', 'Branch Name');
        $sheet->setCellValue('D2', ': ' . ($loans->first()->entity_name ?? ''));
        $sheet->setCellValue('B3', 'GL Group');
        $sheet->setCellValue('D3', ': 320 - Bond Perusahaan - Fixed Rate - Nominal - Available for Sale');
        $sheet->setCellValue('B4', 'Date Of Report');
        $sheet->setCellValue('D4', ': ' . date('d-m-Y', strtotime("$tahun-$bulan-$hari")));
        $sheet->mergeCells('B1:C1');
        $sheet->mergeCells('B2:C2');
        $sheet->mergeCells('B3:C3');
        $sheet->mergeCells('B4:C4');
        $sheet->mergeCells('D3:G3');

        // Add title
        $sheet->setCellValue('B6', 'Report Evaluation (Market to Market) - Treasury Bonds');
        $sheet->mergeCells('B6:R6');
        $sheet->getStyle('B6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('B6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('B6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(7)->setRowHeight(5);

        // Set headers for the table
        $headers = [
            'No.',
            'Branch Number',
            'Account Number',
            'Bond Id',
            'Issuer Name',
            'GL Account',
            'Bond Type',
            'GL Group',
            'Evaluation Date',
            'Maturity Date',
            'Yield (YTM)',
            'Face Value',
            'Price',
            'Mark to Market (MTM)',
            'Carrying Amount',
            'Unreleazed Gain / (Losses)',
            'Cummulative Unreleazed Gain / (Losses)'
        ];

        // Write table headers
        foreach (range('B', 'R') as $key => $column) {
            $sheet->setCellValue($column . '8', $headers[$key]);
            $sheet->getStyle($column . '8')->getFont()->setBold(true);
            $sheet->getStyle($column . '8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($column . '8')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FF4472C4')); // Blue color as in image
            $sheet->getStyle($column . '8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        }

        $sheet->getStyle('B8:R8')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B8:R8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B8:R8')->getAlignment()->setWrapText(true);

        // Write data
        $row = 9;
        $total_face_value = 0;
        $total_mtm = 0;
        $total_carrying = 0;
        $total_unreleazed = 0;
        $total_cumulative = 0;
        $counter = 1; // Tambahkan counter manual

        foreach ($loans as $loan) {  // Hapus $key dari foreach
            $sheet->setCellValue('B' . $row, $counter); // Gunakan counter sebagai nomor urut
            $sheet->setCellValue('C' . $row, $loan->no_branch);
            $sheet->setCellValue('D' . $row, $loan->no_acc);
            $sheet->setCellValue('E' . $row, $loan->bond_id);
            $sheet->setCellValue('F' . $row, $loan->issuer_name);
            $sheet->setCellValue('G' . $row, $loan->coa);
            $sheet->setCellValue('H' . $row, $loan->bond_type);
            $sheet->setCellValue('I' . $row, $loan->gl_group);
            $sheet->setCellValue('J' . $row, date('d/m/Y', strtotime($loan->transac_dt)));
            $sheet->setCellValue('K' . $row, date('d/m/Y', strtotime($loan->mtr_date_dt)));
            $sheet->setCellValue('L' . $row, number_format($loan->yield*100 ?? 0, 5) . '%');
            $sheet->setCellValue('M' . $row, number_format((float)str_replace(['$', ','], '', $loan->face_value), 0));
            $sheet->setCellValue('N' . $row, number_format($loan->price ?? 0, 5));
            $sheet->setCellValue('O' . $row, number_format($loan->mtm_price, 0));
            $sheet->setCellValue('P' . $row, number_format($loan->carrying_amount, 0));
            $sheet->setCellValue('Q' . $row, number_format($loan->gain_losses, 0));
            $sheet->setCellValue('R' . $row, number_format($loan->cum_gain_losses, 0));

            $sheet->getStyle('B' . $row . ':L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M' . $row . ':R' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            if ($row % 2 == 0) {
                $sheet->getStyle('B' . $row . ':R' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('B' . $row . ':R' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            // Update totals
            $total_face_value += (float)str_replace(['$', ','], '', $loan->face_value);
            $total_mtm += $loan->mtm_price;
            $total_carrying += $loan->carrying_amount;
            $total_unreleazed += $loan->gain_losses;
            $total_cumulative += $loan->cum_gain_losses;

            $row++;
            $counter++; // Increment counter
        }

        // Add totals row
        $totalRow = $row;
        $sheet->setCellValue('B' . $totalRow, 'Total');
        $sheet->mergeCells('B' . $totalRow . ':L' . $totalRow);
        $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M' . $totalRow, number_format($total_face_value, 0));
        $sheet->setCellValue('N' . $totalRow, '');
        $sheet->setCellValue('O' . $totalRow, number_format($total_mtm, 0));
        $sheet->setCellValue('P' . $totalRow, number_format($total_carrying, 0));
        $sheet->setCellValue('Q' . $totalRow, number_format($total_unreleazed, 0));
        $sheet->setCellValue('R' . $totalRow, number_format($total_cumulative, 0));

        // Style the totals row
        //$sheet->getStyle('B' . $totalRow . ':R' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('M' . $totalRow . ':R' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // // Auto size columns
        // foreach (range('A', 'Q') as $column) {
        //     $sheet->getColumnDimension($column)->setAutoSize(true);
        // }

        // Add borders to the table
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $sheet->getStyle('B8:R' . $totalRow)->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(6);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(14);
        $sheet->getColumnDimension('K')->setWidth(14);
        $sheet->getColumnDimension('L')->setWidth(14);
        $sheet->getColumnDimension('M')->setWidth(18);
        $sheet->getColumnDimension('N')->setWidth(14);
        $sheet->getColumnDimension('O')->setWidth(18);
        $sheet->getColumnDimension('P')->setWidth(18);
        $sheet->getColumnDimension('Q')->setWidth(18);
        $sheet->getColumnDimension('R')->setWidth(18);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'ReportEvaluationTreasuryBonds_' . date('Ymd') . '.xlsx';
        
        // Save to temp file and send response
        $temp_file = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }



    // Method untuk mengekspor data ke PDF
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('m');
        $hari = $request->input('hari') ?? date('d');

        // Get the collection from your custom method
        $loans = report_securities::callEvaluationTreasuryBonds(
            intval($tahun),
            intval($bulan),
            intval($hari),
            $user->id_pt
        );

        // Convert collection to array and filter by date
        $selectedDate = "$tahun-$bulan-$hari";
        $loans = $loans->filter(function ($loan) use ($selectedDate) {
            return date('Y-m-d', strtotime($loan->transac_dt)) == $selectedDate;
        });

        $loans = $loans->sortBy('no_acc');
        
        if ($loans->isEmpty()) {
            return response()->json(['message' => 'No data found.'], 404);
        }

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set page orientation and margins
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        // Write header information
        $sheet->setCellValue('B1', 'Branch Number');
        $sheet->setCellValue('D1', ': ' . ($loans->first()->no_branch ?? ''));
        $sheet->setCellValue('B2', 'Branch Name');
        $sheet->setCellValue('D2', ': ' . ($loans->first()->entity_name ?? ''));
        $sheet->setCellValue('B3', 'GL Group');
        $sheet->setCellValue('D3', ': 320 - Bond Perusahaan - Fixed Rate - Nominal - Available for Sale');
        $sheet->setCellValue('B4', 'Date Of Report');
        $sheet->setCellValue('D4', ': ' . date('d-m-Y', strtotime("$tahun-$bulan-$hari")));
        $sheet->mergeCells('B1:C1');
        $sheet->mergeCells('B2:C2');
        $sheet->mergeCells('B3:C3');
        $sheet->mergeCells('B4:C4');
        $sheet->mergeCells('D3:G3');

        // Add title
        $sheet->setCellValue('B6', 'Report Evaluation (Market to Market) - Treasury Bonds');
        $sheet->mergeCells('B6:R6');
        $sheet->getStyle('B6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('B6')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('B6')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(7)->setRowHeight(5);

        // Set headers for the table
        $headers = [
            'No.',
            'Branch Number',
            'Account Number',
            'Bond Id',
            'Issuer Name',
            'GL Account',
            'Bond Type',
            'GL Group',
            'Evaluation Date',
            'Maturity Date',
            'Yield (YTM)',
            'Face Value',
            'Price',
            'Mark to Market (MTM)',
            'Carrying Amount',
            'Unreleazed Gain / (Losses)',
            'Cummulative Unreleazed Gain / (Losses)'
        ];

        // Write table headers
        foreach (range('B', 'R') as $key => $column) {
            $sheet->setCellValue($column . '8', $headers[$key]);
            $sheet->getStyle($column . '8')->getFont()->setBold(true);
            $sheet->getStyle($column . '8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($column . '8')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FF4472C4')); // Blue color as in image
            $sheet->getStyle($column . '8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        }

        $sheet->getStyle('B8:R8')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B8:R8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B8:R8')->getAlignment()->setWrapText(true);

        // Write data
        $row = 9;
        $total_face_value = 0;
        $total_mtm = 0;
        $total_carrying = 0;
        $total_unreleazed = 0;
        $total_cumulative = 0;
        $counter = 1; // Tambahkan counter manual

        foreach ($loans as $loan) {  // Hapus $key dari foreach
            $sheet->setCellValue('B' . $row, $counter); // Gunakan counter sebagai nomor urut
            $sheet->setCellValue('C' . $row, $loan->no_branch);
            $sheet->setCellValue('D' . $row, $loan->no_acc);
            $sheet->setCellValue('E' . $row, $loan->bond_id);
            $sheet->setCellValue('F' . $row, $loan->issuer_name);
            $sheet->setCellValue('G' . $row, $loan->coa);
            $sheet->setCellValue('H' . $row, $loan->bond_type);
            $sheet->setCellValue('I' . $row, $loan->gl_group);
            $sheet->setCellValue('J' . $row, date('d/m/Y', strtotime($loan->transac_dt)));
            $sheet->setCellValue('K' . $row, date('d/m/Y', strtotime($loan->mtr_date_dt)));
            $sheet->setCellValue('L' . $row, number_format($loan->yield*100 ?? 0, 5) . '%');
            $sheet->setCellValue('M' . $row, number_format((float)str_replace(['$', ','], '', $loan->face_value), 0));
            $sheet->setCellValue('N' . $row, number_format($loan->price ?? 0, 5));
            $sheet->setCellValue('O' . $row, number_format($loan->mtm_price, 0));
            $sheet->setCellValue('P' . $row, number_format($loan->carrying_amount, 0));
            $sheet->setCellValue('Q' . $row, number_format($loan->gain_losses, 0));
            $sheet->setCellValue('R' . $row, number_format($loan->cum_gain_losses, 0));

            $sheet->getStyle('B' . $row . ':L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M' . $row . ':R' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            if ($row % 2 == 0) {
                $sheet->getStyle('B' . $row . ':R' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('B' . $row . ':R' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            // Update totals
            $total_face_value += (float)str_replace(['$', ','], '', $loan->face_value);
            $total_mtm += $loan->mtm_price;
            $total_carrying += $loan->carrying_amount;
            $total_unreleazed += $loan->gain_losses;
            $total_cumulative += $loan->cum_gain_losses;

            $row++;
            $counter++; // Increment counter
        }

        // Add totals row
        $totalRow = $row;
        $sheet->setCellValue('B' . $totalRow, 'Total');
        $sheet->mergeCells('B' . $totalRow . ':L' . $totalRow);
        $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M' . $totalRow, number_format($total_face_value, 0));
        $sheet->setCellValue('N' . $totalRow, '');
        $sheet->setCellValue('O' . $totalRow, number_format($total_mtm, 0));
        $sheet->setCellValue('P' . $totalRow, number_format($total_carrying, 0));
        $sheet->setCellValue('Q' . $totalRow, number_format($total_unreleazed, 0));
        $sheet->setCellValue('R' . $totalRow, number_format($total_cumulative, 0));

        // Style the totals row
        //$sheet->getStyle('B' . $totalRow . ':R' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('M' . $totalRow . ':R' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // // Auto size columns
        // foreach (range('A', 'Q') as $column) {
        //     $sheet->getColumnDimension($column)->setAutoSize(true);
        // }

        // Add borders to the table
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $sheet->getStyle('B8:R' . $totalRow)->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(6);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(14);
        $sheet->getColumnDimension('K')->setWidth(14);
        $sheet->getColumnDimension('L')->setWidth(14);
        $sheet->getColumnDimension('M')->setWidth(18);
        $sheet->getColumnDimension('N')->setWidth(14);
        $sheet->getColumnDimension('O')->setWidth(18);
        $sheet->getColumnDimension('P')->setWidth(18);
        $sheet->getColumnDimension('Q')->setWidth(18);
        $sheet->getColumnDimension('R')->setWidth(18);

        // Create PDF file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $filename = 'ReportEvaluationTreasuryBonds_' . date('Ymd') . '.pdf';
        
        // Save to temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'pdf');
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
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
