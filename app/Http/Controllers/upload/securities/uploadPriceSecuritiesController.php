<?php

namespace App\Http\Controllers\upload\securities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Carbon\Carbon;


class uploadPriceSecuritiesController extends Controller
{
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
        $perPage = $request->input('per_page', 10);

        $existIntbldatasecurities = DB::table('securities.tblpricesecurities')
        ->exists();

        $existIntbldatasecurities = DB::table('securities.tbldatasecurities')
        ->exists();

        $existIntblmastersecurities = DB::table('securities.tblmaster_tmpbid')
        ->exists();

        $priceSecurities = DB::table('securities.tblpricesecurities')
        ->where('no_branch', $id_pt)
        ->paginate($perPage);

        // Initialize an array to hold messages
        $messages = [];

        // Logic to determine messages based on existence checks
        if ($existIntbldatasecurities) {
            $messages[] = '<span style="color: red;">already exists in tblpricesecurities</span>';
        } elseif (!$existIntbldatasecurities) {
            $messages[] = '<span style="color: red;">not exist in table tbldatasecurities</span>';
        } elseif (!$existIntblmastersecurities) {
            $messages[] = '<span style="color: red;">not exist in table tblmaster_tmpbid</span>';
        } else {
            // If it does not exist in any of the tables, insert into tblprice
            DB::table('securities.tblpricesecurities')
                ->where('no_branch', $id_pt)
                ->paginate($perPage);
            // $messages[] = '<span style="color: green;">Inserted into tblprice successfully</span>';
        }

        return view('upload.securities.layouts.tblpricesecurities', array_merge(compact('messages', 'priceSecurities'), [
            'title' => 'Laravel - PHPSpreadsheet',
            'securities' => $priceSecurities 
        ]));
    }

    // protected function validateData($data)
    // {
    //     Log::info('Memulai validasi data:', ['data' => $data]);
        
    //     // Validasi field wajib
    //     $required_fields = [
    //         'no_acc', 'no_branch', 'deb_name', 'status', 'ln_type',
    //         'org_date', 'term', 'mtr_date', 'org_bal', 'rate', 'cbal',
    //         'prebal', 'bilprn', 'pmtamt', 'lrebd', 'nrebd', 'ln_grp',
    //         'GROUP', 'bilint', 'bisifa', 'birest', 'freldt', 'resdt',
    //         'restdt', 'prov', 'trxcost', 'gol'
    //     ];

    //     foreach ($required_fields as $field) {
    //         if (!isset($data[$field])) {
    //             Log::warning("Field '$field' tidak ditemukan", $data);
    //             return false;
    //         }
            
    //         // Izinkan nilai 0 atau "0"
    //         if ($data[$field] === null || $data[$field] === '') {
    //             Log::warning("Field '$field' kosong", ['value' => $data[$field]]);
    //             return false;
    //         }
    //     }

    //     // Validasi format tanggal
    //     $dateFields = ['org_date_dt', 'mtr_date_dt', 'lrebd_dt', 'nrebd_dt', 'freldt_dt', 'resdt_dt', 'restdt_dt'];
    //     foreach ($dateFields as $field) {
    //         if (!empty($data[$field]) && !strtotime($data[$field])) {
    //             Log::error("Format tanggal tidak valid untuk field '$field'", [
    //                 'nilai' => $data[$field]
    //             ]);
    //             return false;
    //         }
    //     }

    //     Log::info('Validasi berhasil');
    //     return true;
    // }

    // protected function formatData($row)
    // {
    //     try {
    //         // Fungsi helper untuk format tanggal
    //         $formatDate = function($date) {
    //             if (empty($date) || $date == "''" || $date == "''") {
    //                 return '1900-01-01';
    //             }
                
    //             try {
    //                 // Coba parse format dd/mm/yyyy atau dd-mm-yyyy
    //                 if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $date, $matches)) {
    //                     return sprintf('%s-%s-%s 00:00:00', $matches[3], $matches[2], $matches[1]);
    //                 }
                    
    //                 // Coba parse format yyyy-mm-dd
    //                 if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    //                     return $date . ' 00:00:00';
    //                 }

    //                 $dateTime = new DateTime($date);
    //                 return $dateTime->format('Y-m-d H:i:s');
    //             } catch (\Exception $e) {
    //                 Log::error('Date parsing error:', ['date' => $date, 'error' => $e->getMessage()]);
    //                 return null;
    //             }
    //         };

    //         // Hapus spasi dari nilai numerik dan ganti koma dengan titik
    //         $cleanRow = array_map(function($value) {
    //             if (is_string($value)) {
    //                 return trim(str_replace([',', ' '], ['', ''], $value));
    //             }
    //             return $value;
    //         }, $row);


    //         return [
    //             'no_acc' => (float)$cleanRow[0],
    //             'no_branch' => (float)$cleanRow[1],
    //             'deb_name' => (string)$cleanRow[2],
    //             'status' => (string)$cleanRow[3],
    //             'ln_type' => (string)$cleanRow[4],
    //             'org_date' => (float)$cleanRow[5],
    //             'org_date_dt' => !empty($cleanRow[6]) ? date('Y-m-d H:i:s', strtotime($cleanRow[6])) : null,
    //             'term' => (float)$cleanRow[7],
    //             'mtr_date' => (float)$cleanRow[8],
    //             'mtr_date_dt' => !empty($cleanRow[9]) ? date('Y-m-d H:i:s', strtotime($cleanRow[9])) : null,
    //             'org_bal' => (float)$cleanRow[10],
    //             'rate' => (float)$cleanRow[11],
    //             'cbal' => (float)$cleanRow[12],
    //             'prebal' => (float)$cleanRow[13],
    //             'bilprn' => (float)$cleanRow[14],
    //             'pmtamt' => (float)$cleanRow[15],
    //             'lrebd' => (float)$cleanRow[16],
    //             'lrebd_dt' => !empty($cleanRow[17]) ? date('Y-m-d H:i:s', strtotime($cleanRow[17])) : null,
    //             'nrebd' => (float)$cleanRow[18],
    //             'nrebd_dt' => !empty($cleanRow[19]) ? date('Y-m-d H:i:s', strtotime($cleanRow[19])) : null,
    //             'ln_grp' => (float)$cleanRow[20],
    //             'GROUP' => trim($cleanRow[21], "'"),
    //             'bilint' => (float)$cleanRow[22],
    //             'bisifa' => (float)$cleanRow[23],
    //             'birest' => (string)$cleanRow[24],
    //             'freldt' => (float)$cleanRow[25],
    //             'freldt_dt' => !empty($cleanRow[26]) && $cleanRow[26] != '1900-01-01' ? date('Y-m-d H:i:s', strtotime($cleanRow[26])) : null,
    //             'resdt' => (float)$cleanRow[27],
    //             'resdt_dt' => !empty($cleanRow[28]) && $cleanRow[28] != '1900-01-01' ? date('Y-m-d H:i:s', strtotime($cleanRow[28])) : null,
    //             'restdt' => (float)$cleanRow[29],
    //             'restdt_dt' => !empty($cleanRow[30]) && $cleanRow[30] != '1900-01-01' ? date('Y-m-d H:i:s', strtotime($cleanRow[30])) : null,
    //             'prov' => (float)$cleanRow[31],
    //             'trxcost' => (float)$cleanRow[32],
    //             'gol' => (int)$cleanRow[33]
    //         ];
    //     } catch (\Exception $e) {
    //         Log::error('Data formatting error: ' . $e->getMessage());
    //         Log::error('Row data: ' . json_encode($row));
    //         return null;
    //     }
    // }
}
