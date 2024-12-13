<?php

namespace App\Http\Controllers;

use App\Models\report_effective;
use App\Models\model_pt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil id_pt pengguna yang sedang login melalui relasi dengan model_pt
$id_pt = Auth::user()->id_pt;
    // Debug: Menampilkan id_pt
    // dd(Auth::user()); // Hentikan eksekusi dan tampilkan nilai id_pt
        // Mengambil semua pinjaman korporat berdasarkan id_pt pengguna yang sedang login dan urutkan
        $loans = Report_effective::getCorporateLoans()
                    ->where('id_pt', $id_pt)
                    ->orderBy('no_acc') // Pindahkan ini sebelum memanggil get()
                    ->get();

        $totalOutstanding = $loans->sum('org_bal');

         // Ambil nama_pt dari model_pt berdasarkan id_pt pengguna yang sedang login
                 $nama_pt = model_pt::where('id_pt', $id_pt)->value('nama_pt');

// dd($id_pt,$nama_pt);

        // Ambil data default untuk tampilan awal
        $defaultLoan = $loans->first(); // Ambil pinjaman pertama
        $defaultNoAcc = $defaultLoan->no_acc ?? ''; // Gunakan null coalescing operator
        $defaultIdPt = $defaultLoan->id_pt ?? ''; // Gunakan null coalescing operator

        // Panggil dengan dua argumen
        $master = report_effective::getMasterDataByNoAcc($defaultNoAcc, $defaultIdPt);


        $loanWithNoacc = report_effective::getLoanDetails($defaultNoAcc, $defaultIdPt);
        $cfobal = report_effective::getReportsByNoAcc($defaultNoAcc, $id_pt)
        ->where('bulanke', '!=', 0);

        // Persiapkan data untuk chart
        $labels = [];
        $data = [];
        // Mengurutkan data berdasarkan tanggal
        $cfobal = $cfobal->sortBy(function($item) {
            return \Carbon\Carbon::parse($item->tglangsuran); // Pastikan tglangsuran adalah objek tanggal yang valid
        });

        foreach ($cfobal as $item) {
            // Format bulan (misalnya: "Januari", "Februari", dst.)
            $labels[] = \Carbon\Carbon::parse($item->tglangsuran)->format('F Y'); // Menggunakan format bulan dan tahun
            $data[] = $item->amortized; // Ganti dengan nama kolom yang sesuai
        }

        // Log jika data tidak ditemukan
        if (!$master) {
            Log::warning('Master data not found', ['no_acc' => $defaultNoAcc, 'id_pt' => $defaultIdPt]);
        }

        if (!$loanWithNoacc) {
            Log::warning('Loan details not found', ['no_acc' => $defaultNoAcc, 'id_pt' => $defaultIdPt]);
        }

        return view('admin.dashboard', compact('loans', 'totalOutstanding', 'defaultNoAcc', 'master', 'loanWithNoacc','labels','data','nama_pt'));
    }

    public function getDebiturInfo($no_acc)
    {
        // Mengambil id_pt pengguna yang sedang login
        $id_pt = Auth::user()->id_pt;

        Log::info('Fetching Debitur Info', ['no_acc' => $no_acc, 'id_pt' => $id_pt]);

        $master = report_effective::getMasterDataByNoAcc($no_acc, $id_pt);
        $loanWithNoacc = report_effective::getLoanDetails($no_acc, $id_pt);
        $cfobal = report_effective::getReportsByNoAcc($no_acc, $id_pt)
        ->where('bulanke', '!=', 0);; // Ambil data amortisasi

        // Persiapkan data untuk chart
        $labels = [];
        $data = [];

        // Mengurutkan data berdasarkan tanggal
        $cfobal = $cfobal->sortBy(function($item) {
            return \Carbon\Carbon::parse($item->tglangsuran); // Pastikan tglangsuran adalah objek tanggal yang valid
        });

        foreach ($cfobal as $item) {
            // Format bulan (misalnya: "Januari", "Februari", dst.)
            $labels[] = \Carbon\Carbon::parse($item->tglangsuran)->format('F Y'); // Menggunakan format bulan dan tahun
            $data[] = $item->amortized; // Ganti dengan nama kolom yang sesuai
        }

        if ($master && $loanWithNoacc) {
            Log::info('Data Found', ['master' => $master, 'loan' => $loanWithNoacc]);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'deb_name' => $master->deb_name,
                    'rate' => $master->rate,
                    'eirex' => $loanWithNoacc->eirex,
                    'eircalc' => $loanWithNoacc->eircalc,
                    'labels' => $labels, // Menambahkan labels ke response
                    'data' => $data // Menambahkan data ke response
                ]
            ]);
        } else {
            Log::warning('Data Not Found', ['no_acc' => $no_acc, 'id_pt' => $id_pt]);
            return response()->json(['status' => 'error', 'message' => 'Data not found', 'no_acc' => $no_acc]);
        }
    }
}
