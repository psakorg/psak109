<?php

namespace App\Http\Controllers\report\Report_Accrual_Interest;

use App\Models\report_effective;
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
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;

use Dompdf\Dompdf;
use Dompdf\Options;

class effectiveController extends Controller
{
    // Method untuk menampilkan semua data pinjaman korporat
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
        // Ambil jumlah item per halaman dari query string, default 10
        $perPage = $request->input('per_page', 10);
        // Ambil data dengan pagination
        $loans = report_effective::fetchAll($id_pt, $perPage);
         // Tambahkan debug untuk loans
    //  dd($loans);
        return view('report.accrual_interest.effective.master', compact('loans'));
    }
    // Method untuk menampilkan detail pinjaman berdasarkan nomor akun
    public function view($no_acc, $id_pt)
{
    $no_acc = trim($no_acc);

    $loan = report_effective::getLoanDetails($no_acc, $id_pt);
    $master = report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
    $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);


    // Debugging: Jika salah satu data tidak ditemukan, tampilkan pesan atau log
    if (!$loan) {
        return response()->json(['error' => 'Data loan tidak ditemukan'], 404);
    }
    if (!$master) {
        return response()->json(['error' => 'Data master tidak ditemukan'], 404);
    }
    if ($reports->isEmpty()) {
        return response()->json(['error' => 'Data laporan tidak ditemukan'], 404);
    }
    
    // dd($reports);

    return view('report.accrual_interest.effective.view', compact('loan', 'reports', 'master'));
}

    public function exportExcel($no_acc,$id_pt)
    {
        // Ambil data loan dan reports

        $loan = report_effective::getLoanDetails($no_acc,$id_pt);
        $reports = report_effective::getReportsByNoAcc($no_acc,$id_pt);

        // Cek apakah data loan dan reports ada
        if (!$loan || $reports->isEmpty()) {
            return response()->json(['message' => 'No data found for the given account number.'], 404);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        // Set informasi pinjaman
        $sheet->setCellValue('A3', 'No. Account');
        $sheet->getStyle('A3')->getFont()->setBold(true); // Set bold untuk No. Account
        $sheet->setCellValue('B3', $loan->no_acc);
        $sheet->setCellValue('A4', 'Debtor Name');
        $sheet->getStyle('A4')->getFont()->setBold(true); // Set bold untuk Debtor Name
        $sheet->setCellValue('B4', $loan->deb_name);
        $sheet->setCellValue('A5', 'Original Balance');
        $sheet->getStyle('A5')->getFont()->setBold(true); // Set bold untuk Original Balance
        $sheet->setCellValue('B5', number_format($loan->org_bal, 2));
        $sheet->setCellValue('A6', 'Original Date');
        $sheet->getStyle('A6')->getFont()->setBold(true); // Set bold untuk Original Date
        $sheet->setCellValue('B6', date('Y-m-d', strtotime($loan->org_date)));
        $sheet->setCellValue('A7', 'Term');
        $sheet->getStyle('A7')->getFont()->setBold(true); // Set bold untuk Term
        $sheet->setCellValue('B7', $loan->TERM);
        $sheet->setCellValue('A8', 'Maturity Date');
        $sheet->getStyle('A8')->getFont()->setBold(true); // Set bold untuk Maturity Date
        $sheet->setCellValue('B8', date('Y-m-d', strtotime($loan->mtr_date)));


        // Set judul tabel laporan 2 baris
        $sheet->setCellValue('A10', 'Accrual Interest Report');
        $sheet->setCellValue('A11', 'Report Details');

        // Merge cells untuk kedua baris judul
        $sheet->mergeCells('A10:H10');
        $sheet->mergeCells('A11:H11');

        // Style untuk baris judul pertama
        $sheet->getStyle('A10:H10')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF006600',
                ],
            ],
        ]);

        // Style untuk baris judul kedua
        $sheet->getStyle('A11:H11')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => Color::COLOR_WHITE]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF006600',
                ],
            ],
        ]);

        // Set warna text putih untuk kedua baris
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A11')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Atur tinggi baris untuk judul
        $sheet->getRowDimension('10')->setRowHeight(25);
        $sheet->getRowDimension('11')->setRowHeight(25);

        // Beri jarak sebelum header tabel
        $sheet->getRowDimension('12')->setRowHeight(10);

        // Header tabel sekarang dimulai dari baris 13
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
            $sheet->setCellValue($columnIndex . '13', $header);
            $sheet->getStyle($columnIndex . '13')->getFont()->setBold(false);
            $sheet->getStyle($columnIndex . '13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '13')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '13')->getFill()->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($columnIndex . '13')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

        // Sesuaikan index baris untuk data
        $dataStartRow = 14; // Data mulai dari baris 14

        $row = 13;
        $cumulativeTimeGap = 0;
        foreach ($reports as $report) {
            $cumulativeTimeGap += floatval($report->timegap);

            $sheet->setCellValue('A' . $row, $report->bulanke);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($report->tglangsuran)));
            $sheet->setCellValue('C' . $row, $report->bunga);
            $sheet->setCellValue('D' . $row, number_format($report->pmtamt, 2));
            $sheet->setCellValue('E' . $row, number_format($report->pokok, 2));
            $sheet->setCellValue('F' . $row, number_format(0, 2));
            $sheet->setCellValue('G' . $row, number_format($report->balance, 2));
            $sheet->setCellValue('H' . $row, number_format($report->bungaeir, 2));
            $sheet->setCellValue('I' . $row, $report->timegap);
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('0.000000000000000'); // Format 15 desimal
            $sheet->setCellValue('J' . $row, number_format($report->outsamtconv, 2));
            $sheet->setCellValue('K' . $row, number_format($cumulativeTimeGap, 15));
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('0.000000000000000'); // Format 15 desimal


            // Mengatur font menjadi bold untuk setiap baris data
            $sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(false);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

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
        $sheet->getStyle('A13:K'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A12:J12')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A13:K' . ($row - 1))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan nama file
        $filename = "accrual_interest_report_Effective_$no_acc.xlsx";

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
    $loan = report_effective::getLoanDetails($no_acc,$id_pt);
    $reports = report_effective::getReportsByNoAcc($no_acc, $id_pt);

    // dd($reports);

    // Cek apakah data loan dan reports ada
    if (!$loan || $reports->isEmpty()) {
        return response()->json(['message' => 'No data found for the given account number.'], 404);
    }

    // dd($loan);

    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Mengatur orientasi halaman dan margin
    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    $sheet->getPageMargins()->setTop(0.5);
    $sheet->getPageMargins()->setRight(0.5);
    $sheet->getPageMargins()->setLeft(0.5);
    $sheet->getPageMargins()->setBottom(0.5);

    // Set lebar kolom untuk informasi header
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(5);  // Untuk tanda ':'
    $sheet->getColumnDimension('C')->setWidth(30);

    // Styling untuk header informasi
    $headerInfoStyle = [
        'font' => [
            'bold' => true,
            'size' => 11
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'bottom' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
        ],
    ];

    // Set informasi dengan format yang lebih rapi
    $infoRows = [
        ['No. Account', ':', $loan->no_acc],
        ['Debtor Name', ':', $loan->deb_name],
        ['Original Balance', ':', 'Rp ' . number_format($loan->org_bal, 2)],
        ['Original Date', ':', date('d/m/Y', strtotime($loan->org_date))],
        ['Term', ':', $loan->term . ' Months'],
        ['Maturity Date', ':', date('d/m/Y', strtotime($loan->mtr_date))],
    ];

    $currentRow = 3;
    foreach ($infoRows as $info) {
        // Label (Kolom A)
        $sheet->setCellValue('A' . $currentRow, $info[0]);
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Separator ":" (Kolom B)
        $sheet->setCellValue('B' . $currentRow, $info[1]);
        $sheet->getStyle('B' . $currentRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Value (Kolom C)
        $sheet->setCellValue('C' . $currentRow, $info[2]);
        $sheet->getStyle('C' . $currentRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Mengatur tinggi baris
        $sheet->getRowDimension($currentRow)->setRowHeight(25);
        
        // Menerapkan border bottom untuk setiap baris
        $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $currentRow++;
    }

    // Memberikan sedikit jarak sebelum judul tabel
    $sheet->getRowDimension($currentRow)->setRowHeight(20);

    // Set tinggi baris untuk header
    $sheet->getRowDimension('1')->setRowHeight(30);
    $sheet->getRowDimension('3')->setRowHeight(20);
    $sheet->getRowDimension('10')->setRowHeight(20);

    // Informasi header dengan styling yang lebih baik
    $headerStyle = [
        'font' => ['bold' => true],
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ];

    // Set lebar kolom
    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(20);

    // Set judul tabel laporan (tanpa border)
    $sheet->setCellValue('A10', 'Accrual Interest Report - Report Details');
    $sheet->mergeCells('A10:H10');
    $sheet->getStyle('A10:H10')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A10:H10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A10:H10')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A10:H10')->getFill()->getStartColor()->setARGB('FF006600');
    $sheet->getStyle('A10:H10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

    // Set header tabel (baris 12) dengan border
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
        $sheet->getStyle($columnIndex . '12')->getFont()->setBold(false);
        $sheet->getStyle($columnIndex . '12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($columnIndex . '12')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($columnIndex . '12')->getFill()->getStartColor()->setARGB('FF4F81BD');
        $sheet->getStyle($columnIndex . '12')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $columnIndex++;
    }

    // Border khusus untuk header tabel
    $sheet->getStyle('A12:H12')->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
        ],
    ]);

    // Mengisi data laporan ke dalam tabel
    $row = 13; // Mulai dari baris 13 untuk data laporan
    $totalTimeGap = 0;
    $totalPaymentAmount = 0;
    $totalAccruedInterest = 0;
    $totalInterestPayment = 0;
    $totalTimeGapSum = 0;
    $totalOutstandingAmount = 0;

    foreach ($reports as $report) {
        $totalTimeGap += ($report->timegap);
        
        // Menambah total untuk setiap kolom
        $totalPaymentAmount += $report->pmtamt;
        $totalAccruedInterest += $report->accrconv;
        $totalInterestPayment += $report->bunga;
        $totalTimeGapSum += $report->timegap;
        $totalOutstandingAmount += $report->outsamtconv;
        
        $sheet->setCellValue('A' . $row, $report->bulanke);
        $sheet->setCellValue('B' . $row, date('Y-m-d', strtotime($report->tglangsuran)));
        $sheet->setCellValue('C' . $row, 'Rp ' . number_format($report->pmtamt, 2));
        $sheet->setCellValue('D' . $row, 'Rp ' . number_format($report->accrconv ?? 0, 2));
        $sheet->setCellValue('E' . $row, 'Rp ' . number_format($report->bunga, 2));
        $sheet->setCellValue('F' . $row, number_format($report->timegap, 2));
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('G' . $row, 'Rp ' . number_format($report->outsamtconv, 2));
        $sheet->setCellValue('H' . $row, number_format($totalTimeGap, 2));
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('0.00');

        // Mengatur alignment untuk setiap kolom
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Mengatur font menjadi bold untuk setiap baris data
        $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(false);

        // Menambahkan warna latar belakang alternatif pada baris data
        if ($row % 2 == 0) {
            $sheet->getStyle('A' . $row . ':H' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle('A' . $row . ':H' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF');
        }

        $row++;
    }

    // Row total
    $totalRow = $row;
    $sheet->setCellValue('A' . $totalRow, 'TOTAL');
    $sheet->mergeCells('A' . $totalRow . ':B' . $totalRow);
    $sheet->setCellValue('C' . $totalRow, 'Rp ' . number_format($totalPaymentAmount, 2));
    $sheet->setCellValue('D' . $totalRow, 'Rp ' . number_format($totalAccruedInterest, 2));
    $sheet->setCellValue('E' . $totalRow, 'Rp ' . number_format($totalInterestPayment, 2));
    $sheet->setCellValue('F' . $totalRow, number_format($totalTimeGapSum, 2));
    $sheet->setCellValue('G' . $totalRow, 'Rp ' . number_format($totalOutstandingAmount, 2));
    $sheet->setCellValue('H' . $totalRow, number_format($totalTimeGap, 2));

    // Styling untuk row total (tanpa bold)
    $sheet->getStyle('A' . $totalRow . ':H' . $totalRow)->applyFromArray([
        'font' => [
            'bold' => false
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'argb' => 'FFD3D3D3'
            ]
        ]
    ]);

    // Alignment untuk row total
    $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('C' . $totalRow . ':H' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    // Menambahkan border untuk row total
    $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
        ],
    ]);

    // Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'H') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Styling untuk data tabel (mulai dari baris 13)
    $sheet->getStyle('A13:H' . ($row-1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Mengatur lebar kolom agar lebih rapi
    foreach (range('A', 'H') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Styling untuk data tabel (hanya sampai kolom H)
    $sheet->getStyle('A11:H' . ($row-1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Set tinggi baris untuk data
    for ($i = 11; $i < $row; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(20);
    }

    // Mengatur wrap text untuk header kolom yang panjang (hanya sampai kolom H)
    $sheet->getStyle('A10:H10')->getAlignment()->setWrapText(true);

    // Menambahkan sedikit padding (hanya sampai kolom H)
    $sheet->getStyle('A11:H' . $row)->getAlignment()->setIndent(1);

    // Mengatur lebar kolom agar sesuai dengan isi
    foreach (range('A', 'H') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Siapkan nama file
    $filename = "accrual_interest_report_$no_acc.pdf";

    // Set pengaturan untuk PDF
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);

    // Siapkan direktori untuk menyimpan file sementara
    $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_pdf');

    // Simpan file PDF
    $writer->save($temp_file);

    // Kembalikan response PDF
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
