<?php

namespace App\Http\Controllers;

use App\Models\mapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Lob;


class MappingAdminController extends Controller
{
    public function index()
    {
        // Pastikan hanya super admin yang dapat mengakses
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        // Ambil data dari tabel tbl_mapping dengan join ke tbl_lob
        $mappings = Mapping::select('m.user_id', 'm.lob_id', 'm.company_type', 'l.description')
        ->from('tbl_mapping as m') // Menggunakan alias di sini
        ->join('tbl_lob as l', 'm.lob_id', '=', 'l.lob_id') // Menggunakan alias di sini
        ->get();


        $userIds = $mappings->pluck('user_id')->unique(); // Ambil unique user_id
        $users = User::whereIn('user_id', $userIds)->get()->keyBy('user_id'); // Ambil data pengguna

        return view('mapping.master', compact('mappings','users'));
    }

    public function show($userId)
    {
        // Pastikan hanya super admin yang dapat mengakses
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        // Ambil data berdasarkan user_id dengan join ke tabel modul
        $mappingDetails = Mapping::select('mappings.*', 'modul.nama_modul')
            ->from('tbl_mapping as mappings') // Alias untuk tabel mapping
            ->join('tbl_modul as modul', 'mappings.modul_id', '=', 'modul.modul_id') // Join dengan tabel modul
            ->where('mappings.user_id', $userId)
            ->get();

        $user = User::find($userId);

        return view('mapping.master_view', compact('mappingDetails', 'user'));
    }
}
