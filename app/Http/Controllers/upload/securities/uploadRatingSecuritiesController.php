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
use Illuminate\Support\Str;

class uploadRatingSecuritiesController extends Controller
{
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id_pt;
        $perPage = $request->input('per_page', 5);

        $securities = DB::table('securities.tblratingsecurities')
        ->where('no_branch', $id_pt)
        ->paginate($perPage);
        
        return view('upload.securities.layouts.tblratingsecurities', compact('securities'), [
        'title' => 'Laravel - PHPSpreadsheet',
        'securities' => $securities 
    ]);
    }
    protected function validateData($data)
    {
        Log::info('Memulai validasi data:', ['data' => $data]);
        
        // Validasi field wajib
        $required_fields = [
            'no_acc', 'no_branch', 'bond_id', 'appraisal_name', 'rating',
            'rating_date', 'economi_sector', 'sources_rating'
        ];

        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                Log::warning("Field '$field' tidak ditemukan", $data);
                return false;
            }
            
            // Izinkan nilai 0 atau "0"
            if ($data[$field] === null || $data[$field] === '') {
                Log::warning("Field '$field' kosong", ['value' => $data[$field]]);
                return false;
            }
        }

        // Validasi format tanggal
        $dateFields = ['rating_date'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field]) && !strtotime($data[$field])) {
                Log::error("Format tanggal tidak valid untuk field '$field'", [
                    'nilai' => $data[$field]
                ]);
                return false;
            }
        }

        Log::info('Validasi berhasil');
        return true;
    }

    protected function formatData($row)
    {
        try {
            // Fungsi helper untuk format tanggal
            $formatDate = function($date) {
                if (empty($date) || $date == "''" || $date == "''") {
                    return '1900-01-01 00:00:00';
                }
                
                try {
                    // Coba parse format dd/mm/yyyy atau dd-mm-yyyy
                    if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $date, $matches)) {
                        return sprintf('%s-%s-%s 00:00:00', $matches[3], $matches[2], $matches[1]);
                    }
                    
                    // Coba parse format yyyy-mm-dd
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                        return $date . ' 00:00:00';
                    }

                    $dateTime = new DateTime($date);
                    return $dateTime->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    Log::error('Date parsing error:', ['date' => $date, 'error' => $e->getMessage()]);
                    return null;
                }
            };

            // Hapus spasi dari nilai numerik dan ganti koma dengan titik
            $cleanRow = array_map(function($value) {
                if (is_string($value)) {
                    return trim(str_replace([',', ' '], ['', ''], $value));
                }
                return $value;
            }, $row);

            return [
                'no_acc' => (int)$cleanRow[0],
                'no_branch' => (string)$cleanRow[1],
                'bond_id' => (string)$cleanRow[2],
                'appraisal_name' => (string)$cleanRow[3],
                'rating' => (string)$cleanRow[4],
                'rating_date' =>  $formatDate(trim($cleanRow[5])),
                'economi_sector' =>(string)$cleanRow[6],
                'sources_rating' => (string)$cleanRow[7],
            ];
        } catch (\Exception $e) {
            Log::error('Data formatting error: ' . $e->getMessage());
            Log::error('Row data: ' . json_encode($row));
            return null;
        }
    }


    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'uploadFile' => 'required|file|mimes:xlsx,csv,txt',
            ]);

            $user = Auth::user();
            $id_pt = $user->id_pt ?? 'pt001';

            $file = $request->file('uploadFile');
            // dd($file);
            Log::info('File uploaded:', [
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]);

            // Deteksi format file berdasarkan ekstensi
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Konfigurasi khusus untuk CSV
            if ($extension === 'csv') {
                $reader = new Csv();
                $reader->setInputEncoding('UTF-8');
                $reader->setDelimiter(',');
            } else {
                $reader = new Xlsx();
            }

            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            $rows = array_slice($rows, 1);
            // dd($rows);

            $successCount = 0;
            $errors = [];
            $duplicates = [];
            $allData = [];

            foreach ($rows as $index => $row) {
                DB::beginTransaction();
                try {

                    $convertNumericDate = function($numericDate) {
                        if (empty($numericDate)) return null;
                        // Assuming format YYYYMMDD
                        $year = substr($numericDate, 0, 4);
                        $month = substr($numericDate, 4, 2);
                        $day = substr($numericDate, 6, 2);
                        return date('Y-m-d H:i:s', strtotime("$year-$month-$day"));
                    };
                    

                    $no_acc = (int) trim($row[0]);

                    //Check for existing no_acc for this id_pt
                    $existingRecord = DB::table('tblmaster_tmp')
                        ->where('no_acc',trim((int)$row[0]))
                        ->where('id_pt', $id_pt)
                        ->first();

                    if ($existingRecord) {
                        $duplicates[] = "Baris " . ($index + 1) . ": No ACC '" . trim((int)$row[0]) . "' sudah ada untuk PT ini";
                        continue;
                    }

                    // Ensure it's stored as a string if it's too large for an integer
                    if (!empty($no_acc) && is_numeric($no_acc)) {
                    $no_acc = (int)$no_acc; 
                    } else{
                    $no_acc = null; // Set to null if invalid
                    }


                    $data = [
                        'no_acc' => trim((int)$row[0]),
                        'no_branch' => trim((string)$row[1]),
                        'bond_id' => trim((string)$row[2]),
                        'appraisal_name' => trim((string)$row[3]),
                        'rating' => trim((string)$row[4]),
                        'rating_date' => trim($row[5]),
                        'economi_sector' => trim((string)$row[6]),
                        'sources_rating' => trim((string)$row[7]),
                    ];


                    $allData[] = $data;

                    Log::info("Checking date at row " . ($index + 1) . ": " . $row[5]);
                    // if (!strtotime($row[5])) {
                    //     dd("Invalid date format at row " . ($index + 1), $data);
                    // }

                    $ratingDate = trim($row[5]);

                    // Check if it's already a DateTime object
                    if ($ratingDate instanceof DateTime) {
                        $ratingDate = $ratingDate->format('Y-m-d H:i:s'); // Ensure it's a string
                    } elseif (!empty($ratingDate) && !strtotime($ratingDate)) {
                        Log::error("Invalid date format at row " . ($index + 1) . ": " . $ratingDate);
                    continue;
                    }
                    $data['rating_date'] = $ratingDate;

                     // Validate data
                     if (!$this->validateData($data)) {
                        $errors[] = "Row " . ($index + 1) . ": Invalid data.";
                        DB::rollBack();
                        continue;
                    }

                    // // Hapus karakter $ dan konversi nilai kosong menjadi 0 untuk field numerik
                    // foreach ($data as $key => $value) {
                    //     if (is_string($value)) {
                    //         $data[$key] = trim(str_replace('$', '', $value));
                    //     }
                    //     if ($value === '' || $value === null) {
                    //         if (in_array($key)) {
                    //             $data[$key] = 0.0;
                    //         } elseif (in_array($key, ['tenor'])) {
                    //             $data[$key] = 0;
                    //         }
                    //     }
                    // }

                    Log::info('Attempting to insert row ' . ($index + 1), ['data' => $data]);
                    DB::table('securities.tblratingsecurities')->insert($data);
                    DB::commit();
                    $successCount++;
                   

                } catch (\Exception $e) {
                    DB::rollBack();
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                    Log::error('Error pada baris ' . ($index + 1), [
                        'error' => $e->getMessage(),
                        'data' => $row,
                        'formatted_data' => $data ?? null
                    ]);
                }
            }

            // dd($rows);
            dd($allData, $errors);
            //dd($errors);
            //dd("Value at row " . ($index + 1) . ": " . $row[5]);
            

            // Prepare response message
            if ($successCount > 0 || !empty($duplicates)) {
                $message = [];
                if ($successCount > 0) {
                    $message[] = "Berhasil import $successCount data";
                }
                if (!empty($duplicates)) {
                    $message[] = "Data duplikat: " . implode("; ", $duplicates);
                }
                if (!empty($errors)) {
                    $message[] = "Error: " . implode("; ", $errors);
                }
                return redirect()->back()->with('warning', implode(". ", $message));
            }

            throw new \Exception('Tidak ada data yang berhasil diimport. ' . 
                (!empty($duplicates) ? "Data duplikat: " . implode("; ", $duplicates) : "") .
                (!empty($errors) ? "Error: " . implode("; ", $errors) : ""));
            
        } catch (\Exception $e) {
            Log::error('Import gagal: ' . $e->getMessage());
            Log::error("Transaction rolled back at row " . ($index + 1) . ": " . $e->getMessage());
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    protected function generateImportMessage($valid, $duplicates, $invalid, $existing)
    {
        $messages = [];
        $hasSuccess = $valid > 0; // Menandai apakah ada catatan yang berhasil diimporr

        if ($valid > 0) {
            $messages[] = "$valid catatan berhasil diimpor";
        }
        if ($duplicates > 0) {
            $messages[] = "$duplicates catatan duplikat ";
        }
        if ($invalid > 0) {
            $messages[] = "$invalid catatan tidak valid ";
        }
        if ($existing > 0) {
            $messages[] = "$existing catatan sudah ada ";
        }

        // Kembalikan array dengan pesan dan status sukses
        return [
            'success' => $hasSuccess,
            'message' => empty($messages) ? 'Tidak ada catatan yang diimpor' : implode(', ', $messages),
        ];
    }


    public function executeStoredProcedure(Request $request)
    {
        try {
            $request->validate([
                'bulan' => 'required|integer',
                'tahun' => 'required|integer'
            ]);

            DB::beginTransaction();
            
            $user = Auth::user();
            $id_pt = $user->id_pt;

            // Get all records from upload table for the current PT
            $uploadData = DB::table('tblmaster_tmp')
                ->where('id_pt', $id_pt)
                ->get();

            if ($uploadData->isEmpty()) {
                return redirect()->back()->withErrors(['message' => 'Tidak ada data yang tersedia untuk diproses.']);
            }

            // Move all records to tmp table first
            // foreach ($uploadData as $record) {
            //     DB::table('tblmaster_tmp')->insert((array)$record);
            // }

            // // Delete moved records from upload table
            // DB::table('tblmaster_tmp_upload')
            //     ->where('id_pt', $id_pt)
            //     ->delete();

            // Now process each record with the stored procedure
            $processedCount = 0;
            $errors = [];

            foreach ($uploadData as $record) {
                try {
                    $result = DB::select("SELECT * FROM public.ndcalculateeffectivetrigger_final(?, ?, ?, ?)", [
                        $request->bulan,
                        $request->tahun,
                        $record->no_acc,
                        $id_pt
                    ]);
                    $processedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error processing account {$record->no_acc}: {$e->getMessage()}";
                    Log::error('Error processing account:', [
                        'no_acc' => $record->no_acc,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            // Prepare response message
            $message = "Berhasil memproses $processedCount data";
            if (!empty($errors)) {
                $message .= ". Beberapa error terjadi: " . implode("; ", $errors);
            }

            return redirect()->back()->with('swal', [
                'title' => 'Berhasil!',
                'text' => $message,
                'icon' => 'success'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error executing procedure:', [
                'error' => $e->getMessage(),
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'id_pt' => $id_pt ?? null
            ]);
            
            return redirect()->back()->with('swal', [
                'title' => 'Error!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function clear(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $id_pt = $user->id_pt;
            
            if (!$id_pt) {
                Log::warning('ID PT tidak ditemukan untuk user:', ['user_id' => $user->id]);
                return redirect()->back()->with('error', 'ID PT tidak ditemukan');
            }

            Log::info('Attempting to clear data for PT ID: ' . $id_pt);
            
            // Riyadi remark - Tidak boleh hapus data tblmaster_tmpcorporate
            //$deleted = DB::table('tblmaster_tmp')
            //->where('id_pt', $id_pt)
            //->delete();
            //
            //if ($deleted > 0) {
            //    DB::commit();
            //    Log::info('Successfully deleted ' . $deleted . ' records');
            //    return redirect()->back()->with('success', 'Berhasil menghapus ' . $deleted . ' data');
            //}
            //
            //DB::rollBack();
            //Log::warning('No data found to delete for PT ID: ' . $id_pt);
            //return redirect()->back()->with('error', 'Tidak ada data yang dapat dihapus untuk PT ini');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Clear data error: ' . $e->getMessage(), [
                'id_pt' => $id_pt ?? null,
                'error' => $e
            ]);
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    private function prepareData($row)
    {
        try {
            \Log::info('Raw CSV row:', $row);

            $id_pt = Auth::user()->id_pt ?? 'pt001';

            $data = [
                'no_acc' => $this->cleanValue($row[0]),
                'no_branch' => $this->cleanValue($row[1]),
                'deb_name' => $this->cleanValue($row[2]),
                'status' => $this->cleanValue($row[3]),
                'ln_type' => $this->cleanValue($row[4]),
                'org_date' => $this->formatDate($row[5]),
                'org_date_dt' => $this->formatDate($row[6]),
                'term' => intval($this->cleanValue($row[7]) ?? 0),
                'mtr_date' => $this->formatDate($row[8]),
                'mtr_date_dt' => $this->formatDate($row[9]),
                'org_bal' => $this->cleanNumber($row[10]) ?? 0,
                'rate' => $this->cleanNumber($row[11]) ?? 0,
                'cbal' => $this->cleanNumber($row[12]) ?? 0,
                'prbal' => $this->cleanNumber($row[13]) ?? 0,
                'bilpmt' => $this->cleanNumber($row[14]) ?? 0,
                'pmtamt' => $this->cleanNumber($row[15]) ?? 0,
                'frstd_dt' => $this->formatDate($row[16]),
                'lnstd_dt' => $this->formatDate($row[17]),
                'ln_grp' => $this->cleanValue($row[18]) ?: '0',
                'bsf1a' => $this->cleanValue($row[19]) ?: '0',
                'bsf1b' => $this->cleanValue($row[20]) ?: '0',
                'brest' => $this->cleanValue($row[21]) ?: '0',
                'fnstd_dt' => $this->formatDate($row[22]),
                'rstdt' => $this->formatDate($row[23]),
                'rstdt_dt' => $this->formatDate($row[24]),
                'trial014' => '1',
                'id_pt' => $id_pt,
                'freldt' => $this->cleanValue($row[26]),
                'freldt_dt' => $this->formatDate($row[27]),
                'resdt' => $this->cleanValue($row[28]),
                'resdt_dt' => $this->formatDate($row[29]),
                'restdt' => $this->cleanValue($row[30]),
                'restdt_dt' => $this->formatDate($row[31]),
                'prov' => $this->cleanNumber($row[32]) ?? 0,
                'trxcost' => $this->cleanNumber($row[33]) ?? 0,
                'gol' => (int)$this->cleanValue($row[34]),
                'GROUP' => $this->cleanValue($row[22]) ?: '0'
            ];

            // Tambahkan logging untuk debug
            \Log::info('Raw date values:', [
                'freldt_dt_raw' => $row[27],
                'resdt_dt_raw' => $row[29],
                'restdt_dt_raw' => $row[31]
            ]);

            \Log::info('Prepared data:', $data);
            return $data;

        } catch (\Exception $e) {
            \Log::error('Error in prepareData:', [
                'message' => $e->getMessage(),
                'row' => $row
            ]);
            throw $e;
        }
    }

    private function formatDate($dateValue) {
        try {
            if (empty($dateValue)) {
                return null;
            }

            // Debug log
            \Log::info('Format date input:', ['value' => $dateValue]);

            // Jika nilai adalah angka Excel (serial date)
            if (is_numeric($dateValue)) {
                return Date::excelToDateTimeObject($dateValue);
            }

            // Jika format tanggal adalah string (yyyy-mm-dd atau dd/mm/yyyy)
            if (is_string($dateValue)) {
                $dateValue = trim($dateValue);
                
                // Coba parse format dd/mm/yyyy
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateValue)) {
                    $parts = explode('/', $dateValue);
                    return new DateTime("{$parts[2]}-{$parts[1]}-{$parts[0]}");
                }
                
                // Coba parse format yyyy-mm-dd
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
                    return new DateTime($dateValue);
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error formatting date:', [
                'input' => $dateValue,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function cleanValue($value) {
        if ($value === null || $value === '' || $value === "''" || $value === '""') {
            return null;
        }
        // Hapus kutip dan spasi di awal/akhir
        $value = trim($value, "' ");
        // Hapus karakter non-printable
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
        return $value;
    }

    private function cleanNumber($value) {
        if ($value === null || $value === '' || $value === "''" || $value === '""') {
            return null;
        }
        
        // Hapus karakter khusus dan spasi
        $value = trim($value);
        $value = str_replace([',', ' '], ['', ''], $value);
        
        // Pastikan hanya angka, titik desimal dan minus yang tersisa
        $value = preg_replace('/[^0-9.\-]/', '', $value);
        
        return is_numeric($value) ? floatval($value) : null;
    }
}
