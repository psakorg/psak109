<?php

namespace App\Http\Controllers\upload\simple_interest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class COAControllerCorporateloan extends Controller
{
    public function index(Request $request)
    {

        $user = Auth::user();
        $id_pt = $user->id_pt;
        if (!$id_pt) {
            abort(404, 'Invalid ID');
        }
    
        // Validate if the authenticated user has access to this `id_pt`'
        if ($id_pt != Auth::user()->id_pt) {
            abort(403, 'Unauthorized');
        }

        $interface = $request->input('interface');
        $coa = $request->input('coa');
        $group = $request->input('group');
        $perPage = $request->query('per_page', 50);

        $loans = DB::table('public.tblcoaloancorporate')
        ->where('no_branch', $id_pt)
        ->when($interface, function($query, $interface) {
            return $query->where('interface', $interface);
        })
        ->when($coa, function($query, $coa) {
            return $query->where('coa', $coa);
        })
        ->when($group, function($query, $group) {
            return $query->where('GROUP', $group);
        })
        ->orderBy('id', 'asc')
        ->paginate($perPage);

        //dd($loans);
        //dd($interface, $coa, $group);
        //dd($request->all());
        $isSuperAdmin = $user->role === 'superadmin';

        return view('upload.simple_interest.layouts.coa', compact('loans', 'interface','coa','group','isSuperAdmin'));
    }
    public function exportExcel(Request $request, $id_pt)
    {
        // Ambil data loan dan reports
        
        $user = Auth::user();
        $id_pt = $user->id_pt;
        if (!$id_pt) {
            abort(404, 'Invalid ID');
        }
    
        // Validate if the authenticated user has access to this `id_pt`'
        if ($id_pt != Auth::user()->id_pt) {
            abort(403, 'Unauthorized');
        }

        $interface = $request->input('interface');
        $coa = $request->input('coa');
        $group = $request->input('group');

        $loans = DB::table('public.tblcoaloancorporate')
        ->where('no_branch', $id_pt)
        ->when($interface, function($query, $interface) {
            return $query->where('interface', $interface);
        })
        ->when($coa, function($query, $coa) {
            return $query->where('coa', $coa);
        })
        ->when($group, function($query, $group) {
            return $query->where('GROUP', $group);
        })
        ->orderBy('id', 'asc')
        ->get();

        $loanFirst = $loans->first();


        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set informasi pinjaman
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        
    // Prepare info rows
    $infoRows = [
    ['Entity Number', ': ' . ($loanFirst->no_branch ?? '-')],
    ['Interface', ': ' . ($interface ?? '-')], // Check if interface is set
    ['CoA', ': ' . ($coa ?? '-')], // Check if CoA is set
    ['Group', ': ' . ($group ?? '-')], // Check if Group is set
    ];

    // If there are no loans, set default values
    if (!$loanFirst) {
    $infoRows = [
        ['Entity Number', ': -'],
        ['Interface', ': -'],
        ['CoA', ': -'],
        ['Group', ': -'],
    ];
    }

        $currentRow = 2;
        foreach ($infoRows as $info) {
        $sheet->setCellValue('A' . $currentRow, $info[0]);
        $sheet->setCellValue('B' . $currentRow, $info[1]);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        }

        // Set judul tabel laporan
        $sheet->setCellValue('A8', 'Daftar CoA Simple Interest');
        $sheet->mergeCells('A8:G8'); // Menggabungkan sel untuk judul tabel
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A8')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A8')->getFill()->getStartColor()->setARGB('FF006600'); // Warna latar belakang
        $sheet->getStyle('A8')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getRowDimension(9)->setRowHeight(5);

        // Set judul kolom tabel
        $headers = [
            'No',
            'Interface',
            'CoA',
            'Group',
            'Post',
            'Description',
            'Event'
        ];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '10', $header);
            $sheet->getStyle($columnIndex . '10')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '10')->getAlignment()->setVertical(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '10')->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($columnIndex . '10')->getFill()->getStartColor()->setARGB('FF4F81BD'); // Warna latar belakang header
            $sheet->getStyle($columnIndex . '10')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
            $columnIndex++;
        }

    // Mengisi data laporan ke dalam tabel
    $row = 11; // Mulai dari baris 13 untuk data laporan
    $nourut = 0;
    foreach ($loans as $loan) {
    $nourut++;
    // Mengisi data ke dalam kolom
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A' . $row, $nourut);
    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('B' . $row, $loan->interface);
    $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('C' . $row, $loan->coa);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('D' . $row, $loan->GROUP);
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('E' . $row, $loan->mut); // Sama dengan kolom D
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('F' . $row, $loan->keterangan);
    $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('G' . $row, $loan->EVENT);

            // Menambahkan warna latar belakang alternatif pada baris data
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->getStartColor()->setARGB('FFEFEFEF'); // Warna latar belakang untuk baris genap
            }

            $row++;
        }
      foreach (range('A', 'G') as $columnID) {
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

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(24);
        $sheet->getColumnDimension('E')->setWidth(24);
        $sheet->getColumnDimension('F')->setWidth(26);
        $sheet->getColumnDimension('G')->setWidth(26);

        // Set border untuk header tabel
        $sheet->getStyle('A10:G10')->applyFromArray($styleArray);

        // Set border untuk semua data laporan
        $sheet->getStyle('A11:G' . ($row - 1))->applyFromArray($styleArray);

        // Mengatur lebar kolom agar lebih rapi
        // foreach (range('A', 'G') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        // Siapkan nama file
        $filename = "DaftarCoASimpleInterest_{$id_pt}_{$interface}_{$coa}_{$group}.xlsx";

        // Buat writer dan simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($temp_file);

        // Kembalikan response Excel
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
