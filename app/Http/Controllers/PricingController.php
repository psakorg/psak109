<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modul; // Import model Modul

class PricingController extends Controller
{
    /**
     * Tampilkan halaman konfigurasi akuntansi dengan modul-modul yang sesuai.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // Ambil semua modul dari database menggunakan Eloquent
        $moduls = Modul::all();

        // Tampilkan view dengan data modul
        return view('pricing.price', compact('moduls')); // Mengirim $moduls ke view configurations.index
    }

    /**

     * Simpan konfigurasi yang dipilih oleh user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        // Proses input yang diterima dari form
        $data = $request->all();

        // Melakukan validasi
        $request->validate([
            'company_type' => 'required|string',
            'bisnis_type' => 'required|string',
            // Lakukan validasi lainnya jika perlu
        ]);

        // Simpan konfigurasi sesuai dengan modul yang dipilih user
        foreach ($request->except(['company_type', 'bisnis_type']) as $key => $value) {
            // Key format: module_{modul_id}_method atau module_{modul_id}_journal_method
            // Lakukan penyimpanan konfigurasi sesuai dengan kebutuhan
        }

        // Redirect ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Konfigurasi berhasil disimpan!');
    }
}
