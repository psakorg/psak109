<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class report_securities extends Model
{
    // Mengganti tabel utama menjadi tblOBALCorporateLoan
    protected $table = 'securities.tblobalsecurities';

    // Jika primary key bukan 'id', spesifikkan di sini
    protected $primaryKey = 'id';

    // Jika tidak menggunakan timestamps (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang bisa diakses
    protected $fillable = [
        'no_acc', 'no_branch', 'bond_id', 'issuer_name', 'bond_type',
        'org_bal', 'org_date', 'tenor', 'mtr_date', 'coupon_rate',
        'yield', 'eirex', 'eircalc', 'face_value', 'fair_value',
        'atdiscount', 'atpremium', 'brokerage', 'eircalc_conv',
        'eircalc_disc', 'eircalc_prem', 'eircalc_brok', 'ibase','id_pt'
    ];

    // Method untuk mendapatkan semua pinjaman korporat
    public static function getCorporateLoans()
    {
        return self::select(
            'no_acc', 'no_branch', 'bond_id', 'issuer_name',
            'bond_type', 'org_bal', 'org_date', 'tenor',
            'mtr_date', 'coupon_rate', 'yield', 'eirex',
            'eircalc', 'face_value', 'fair_value',
            'atdiscount', 'atpremium', 'brokerage',
            'eircalc_conv', 'eircalc_disc', 'eircalc_prem',
            'eircalc_brok', 'ibase','id_pt'
        );
    }

    // Method untuk mendapatkan detail pinjaman berdasarkan nomor akun
//     public static function getLoanDetails($no_acc, $id_pt)
//     {
//         return self::where('no_acc', $no_acc)
//             ->where('id_pt', $id_pt)
//             ->first();
//     }
public static function getLoanDetails($no_acc,$id_pt)
    {
        return self::join('securities.tblmaster_tmpbid as master as master', 'securities.tblobalsecurities.no_acc', '=', DB::raw("master.no_acc")) // Join ke tblmaster_tmpcorporate dengan alias 'master'
        ->where('securities.tblobalsecurities.no_acc', $no_acc)
        ->where('securities.tblobalsecurities.id_pt', $id_pt)
        ->select('securities.tblobalsecurities.*', 'master.price') // Memilih semua kolom dari tblobalcorporateloan dan kolom term dari master
        ->first();    }
//     // Method untuk mendapatkan laporan berdasarkan nomor akun
//     public static function getReportsByNoAcc($no_acc, $id_pt)

    public static function getReportsByNoAcc($no_acc,$id_pt)
    {
        return DB::table('securities.tblcfobalsecurities')
            ->where('no_acc', $no_acc)
            ->where('id_pt', $id_pt)
            ->select('*')
            ->orderBy('month_to')
            ->get();
    }
//Method untuk mengambil pinjaman dari tblmaster_tmp berdasarkan no_acc
    public static function getMasterDataByNoAcc($no_acc,$id_pt)
    {
        return DB::table('securities.tblmaster_tmpbid')
            ->where('no_acc', $no_acc)
            ->where('id_pt', $id_pt)
            ->select('*')
            ->first();
    }
//     // Method untuk mengambil pinjaman dari tblmaster_tmp berdasarkan no_acc
//     public static function getMasterDataByNoAcc($no_acc, $id_pt)
//     {
//         return DB::table('public.tblmaster_tmp')
//             ->where('no_acc', $no_acc)
//             ->where('id_pt', $id_pt)
//             ->select(
//                 'no_branch',
//                 'deb_name',
//                 'status',
//                 'ln_type',
//                 'org_date',
//                 'org_date_dt',
//                 'term',
//                 'mtr_date',
//                 'mtr_date_dt',
//                 'org_bal',
//                 'rate',
//                 'cbal',
//                 'prebal',
//                 'bilprn',
//                 'pmtamt',
//                 'lrebd',
//                 'lrebd_dt',
//                 'nrebd',
//                 'nrebd_dt',
//                 'ln_grp',
//                 'GROUP',
//                 'bilint',
//                 'bisifa',
//                 'birest',
//                 'freldt',
//                 'freldt_dt',
//                 'resdt',
//                 'resdt_dt',
//                 'restdt',
//                 'restdt_dt',
//                 'prov',
//                 'trxcost',
//                 'gol',
//                 'id_pt'// Pastikan ini ada
//             )
//             ->first();
//     }

//     // Method untuk mengambil data lengkap berdasarkan no_acc
//     public static function getEffectiveDataByNoAcc($no_acc)
//     {
//         return self::where('no_acc', $no_acc)->get();
//     }

//     // Method untuk mengambil semua data dengan paginasi
    public static function fetchAll($id_pt, $perPage = 10)
    {
        return DB::table('securities.tblobalsecurities as effective')
            ->leftJoin('securities.tblmaster_tmpbid as master', 'effective.no_acc', '=', DB::raw("master.no_acc"))
            ->where('effective.id_pt', $id_pt) // Menambahkan kondisi id_pt
            ->select(
                'effective.no_branch',
                'effective.no_acc',
                'effective.bond_id',
                'effective.issuer_name',
                'effective.bond_type',
                'effective.org_date',
                'effective.org_bal',
                'effective.tenor',
                'effective.mtr_date',
                'effective.coupon_rate',
                'effective.yield',
                'effective.eirex',
                'effective.eircalc',
                'effective.face_value',
                'effective.fair_value',
                'effective.atdiscount',
                'effective.atpremium',
                'effective.brokerage',
                'effective.eircalc_conv',
                'effective.eircalc_disc',
                'effective.eircalc_prem',
                'effective.eircalc_brok',
                'effective.ibase',
                'effective.id_pt',
                'master.status',
                'master.org_date_dt',
                'master.mtr_date_dt',
                'master.price',
                'master.prebal',
                'master.lrebd_dt',
                'master.nrebd_dt',
                'master.pmtamt',
                'master.bond_grp',
                'master.gl_group',
                'master.trade_dt',
                'master.settle_dt',
                'master.eval_dt',
                'master.clasification',

            )
            ->paginate($perPage);
            // Log data yang diambil
    Log::info('Data fetched from tblobaleffective and tblmaster_tmp', ['data' => $result]);
    }
}
