<?php

namespace App\Http\Controllers\dashboard;

// use App\Models\report_securities;
use App\Models\report_simpleinterest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\model_pt;

class simple_interestDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil id_pt pengguna yang sedang login
        $id_pt = Auth::user()->id_pt;

        // Mengambil semua pinjaman korporat berdasarkan id_pt pengguna yang sedang login dan urutkan
        $loans = Report_simpleinterest::getCorporateLoans()
                    ->orderBy('no_acc') // Pindahkan ini sebelum memanggil get()
                    ->get();

        // $totalOutstanding = $loans->sum('org_bal');
        // Ambil nama_pt dari model_pt berdasarkan id_pt pengguna yang sedang login
        $nama_pt = model_pt::where('id_pt', $id_pt)->value('nama_pt');

        // Ambil data default untuk tampilan awal
        $defaultLoan = $loans->first(); // Ambil pinjaman pertama

        $defaultNoAcc = trim($defaultLoan->no_acc ?? ''); // Gunakan null coalescing operator
         $defaultIdPt = $defaultLoan->id_pt ?? ''; // Gunakan null coalescing operator

        // Panggil dengan dua argumen
        $master = report_simpleinterest::getMasterDataByNoAcc($defaultNoAcc, $defaultIdPt);

        // $loanWithNoacc = report_effective::getLoanDetails($defaultNoAcc, $defaultIdPt);
        $loanWithNoacc = report_simpleinterest::getLoanDetails($defaultNoAcc,$defaultIdPt);

        // $cfobal = Report_securities::getReportsByNoAcc($defaultNoAcc, $id_pt)
        $cfobal = report_simpleinterest::getReportsByNoAcc($defaultNoAcc,$defaultIdPt);

        // ->where('month_to', '!=', 0);
        // // dd($cfobal);
        // Persiapkan data untuk chart
        $labels = [];
        $data = [];
        // Mengurutkan data berdasarkan tanggal
        $cfobal = $cfobal->sortBy(function($item) {
            return \Carbon\Carbon::parse($item->tglangsuran); // Pastikan tglangsuran adalah objek tanggal yang valid
        });

        foreach ($cfobal as $item) {
           // Format bulan (misalnya: "Januari", "Februari", dst.)
            $formattedDate = \Carbon\Carbon::parse($item->tglangsuran)->format('F Y'); // Menggunakan format bulan dan tahun

            // Hanya tambahkan ke $labels jika belum ada
            if (!in_array($formattedDate, $labels)) {
                $labels[] = $formattedDate; // Menambahkan format tanggal jika belum ada
            }
           // Hanya tambahkan ke $data jika $item->amortized tidak sama dengan 0
            if ($item->amortized != 0) {
                $data[] = $item->amortized; // Ganti dengan nama kolom yang sesuai
            }
        }


        // Log jika data tidak ditemukan
        if (!$master) {
            Log::warning('Master data not found', ['no_acc' => $defaultNoAcc, 'id_pt' => $defaultIdPt]);
        }

        if (!$loanWithNoacc) {
            Log::warning('Loan details not found', ['no_acc' => $defaultNoAcc, 'id_pt' => $defaultIdPt]);
        }
        return view('admin.dashboard.simpleinterest.master', compact('loans','master', 'loanWithNoacc', 'defaultNoAcc','nama_pt','labels','data'));
        // return view('admin.dashboard.securities.master', compact('loans', 'totalOutstanding', 'defaultNoAcc', 'master', 'loanWithNoacc','labels','data'));
    }

    public function getDebiturInfo($no_acc)
    {
        // Mengambil id_pt pengguna yang sedang login
        $id_pt = Auth::user()->id_pt;

        Log::info('Fetching Debitur Info', ['no_acc' => $no_acc, 'id_pt' => $id_pt]);

        $master = report_simpleinterest::getMasterDataByNoAcc($no_acc, $id_pt);
        $loanWithNoacc = report_simpleinterest::getLoanDetails($no_acc, $id_pt);
        $cfobal = report_simpleinterest::getReportsByNoAcc($no_acc, $id_pt)
        ->where('bulanke', '!=', 0);; // Ambil data amortisasi
        dd($master);
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
