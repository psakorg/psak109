<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modul; // Import model Modul
use App\Models\mapping; // Import model Mapping
use App\Models\lob; // Import model Lob untuk business type
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MappingController extends Controller
{
    /**
     * Tampilkan halaman konfigurasi akuntansi dengan modul-modul dan business type yang sesuai.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // Ambil semua modul dari database menggunakan Eloquent
        $moduls = Modul::all();

        // Ambil semua business types dari tabel tbl_lob
        $businessTypes = Lob::all();

        // Tampilkan view dengan data modul dan business types
        return view('pricing.price', compact('moduls', 'businessTypes')); // Mengirim $moduls dan $businessTypes ke view configurations.index
    }

    /**
     * Simpan konfigurasi yang dipilih oleh user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
{
    Log::info('Save method called');
    // Validasi input
    $request->validate([
        'company_type' => 'required|string|max:255',
        'bisnis_type' => 'required|exists:tbl_lob,lob_id', // Validasi bisnis_type
    ]);

    // Debug untuk melihat semua input


    // Ambil user_id dari Auth
    $userId = Auth::user()->user_id;

    // Array untuk menyimpan data yang akan disimpan
    $moduls = [
        ['modul_id' => 'M0001', 'nama_modul' => 'Interest Deferred Restructuring'],
        ['modul_id' => 'M0002', 'nama_modul' => 'Expenses Off market'],
        ['modul_id' => 'M0003', 'nama_modul' => 'Amortized Cost'],
        ['modul_id' => 'M0004', 'nama_modul' => 'Amortized Fee'],
        ['modul_id' => 'M0005', 'nama_modul' => 'Calculated Accrual Interest'],
        ['modul_id' => 'M0006', 'nama_modul' => 'Expected Cash Flow'],
        ['modul_id' => 'M0007', 'nama_modul' => 'Outstanding Balance'],
        ['modul_id' => 'M0008', 'nama_modul' => 'Opening Balance'],
    ];

    // Loop melalui modul dan simpan data yang dipilih
    foreach ($moduls as $modul) {
        $modulId = $modul['modul_id'];
        $effective = $request->input('module_' . $modulId) == '1' ? '1' : '0';
        $simpleInterest = $request->input('module_' . $modulId) == '0' ? '1' : '0';

        // Hanya simpan jika salah satu dari effective atau simple interest dipilih
        if ($effective == '1' || $simpleInterest == '1') {
            try {
                Mapping::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'modul_id' => $modulId,
                    ],
                    [
                        'periode' => null, // Isi periode dengan null
                        'effective' => $effective,
                        'simple_interest' => $simpleInterest,
                        'lob_id' => $request->input('bisnis_type'), // lob_id baru
                        'company_type' => $request->input('company_type'), // Menyimpan company_type
                        'updated_at' => now(), // Update timestamp
                    ]
                );
            } catch (\Exception $e) {
                // Log error dan kembalikan pesan kesalahan
                Log::error('Failed to save mapping for user_id: ' . $userId . ' and modul_id: ' . $modulId . ' - Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal menyimpan data untuk modul ' . $modul['nama_modul'] . ': ' . $e->getMessage());
            }
        }
    }

    return redirect()->back()->with('success', 'Data berhasil disimpan.');
}
}
