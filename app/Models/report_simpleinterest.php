<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class report_simpleinterest extends Model
{
    // Mengganti tabel utama menjadi tblOBALCorporateLoan
    protected $table = 'public.tblobalcorporateloan';

    // Jika primary key bukan 'id', spesifikkan di sini
    protected $primaryKey = 'id';

    // Jika tidak menggunakan timestamps (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang bisa diakses
    protected $fillable = [
        'no_acc', 'deb_name', 'org_bal', 'org_date', 'ln_type', 'mtr_date'
    ];

    // Method untuk mendapatkan semua pinjaman korporat
    public static function getCorporateLoans()
    {
        return self::select('no_acc','no_branch', 'deb_name', 'org_bal', 'org_date','interest','eircalc_conv','eircalc_cost','eircalc','eircalc_fee','eirex', 'ln_type', 'mtr_date','id_pt');
    }
// Method untuk mendapatkan detail pinjaman berdasarkan nomor akun
public static function getLoanDetails($no_acc, $id_pt)
{
    return self::join('public.tblmaster_tmpcorporate as master', 'public.tblobalcorporateloan.no_acc','=', DB::raw("master.no_acc::varchar")) // Join ke tblmaster_tmpcorporate dengan alias 'master'
        ->where('public.tblobalcorporateloan.no_acc', $no_acc)
        ->where('public.tblobalcorporateloan.id_pt', $id_pt)
        ->select('public.tblobalcorporateloan.*', 'master.*') // Memilih semua kolom dari tblobalcorporateloan dan kolom term dari master
        ->first();
}


    // Method untuk mendapatkan laporan berdasarkan nomor akunn
    // Mengubah dari tabel 'report' ke tabel 'tblCFOBALCorporateLoan'
    public static function getReportsByNoAcc($no_acc, $id_pt)
    {
        return DB::table('public.tblcfobalcorporateloan')
            ->where('no_acc', $no_acc)
            ->where('id_pt', $id_pt)
            ->orderBy('bulanke', 'asc')
            ->orderBy('haribunga', 'desc')
            ->orderBy('tglangsuran', 'asc')
            ->get();
    }
    public static function getMasterDataByNoAcc($no_acc, $id_pt)
    {
        return DB::table('public.tblmaster_tmpcorporate')
            ->where('no_acc', $no_acc)
            ->where('id_pt', $id_pt)
            ->select('*')
            ->first();
    }

    public static function fetchAll($id_pt, $perPage = 10)
{
    return DB::table('public.tblobalcorporateloan as simpleinterest')
        ->leftJoin('public.tblmaster_tmpcorporate as master', 'simpleinterest.no_acc', '=', DB::raw("master.no_acc::varchar"))
        ->where('simpleinterest.id_pt', $id_pt) // Menambahkan kondisi id_pt
        ->select(
                'simpleinterest.no_branch',
                'simpleinterest.no_acc',
                'simpleinterest.deb_name',
                'simpleinterest.ln_type',
                'simpleinterest.org_bal',
                'simpleinterest.org_date',
                'simpleinterest.mtr_date',
                'simpleinterest.eirex',
                'simpleinterest.eircalc',
                'simpleinterest.nbal',
                'simpleinterest.oldbal',
                'simpleinterest.principal',
                'simpleinterest.interest',
                'simpleinterest.adjsmnt',
                'simpleinterest.eircalc_conv',
                'simpleinterest.eircalc_cost',
                'simpleinterest.eircalc_fee',
                'master.rate',
                'master.cbal',
                'master.prebal',
                'master.bilprn',
                'master.pmtamt',
                'master.lrebd',
                'master.nrebd',
                'master.ln_grp',
                'master.bilint',
                'master.bisifa',
                'master.birest',
                'master.freldt',
                'master.resdt',
                'master.restdt',
                'master.prov',
                'master.trxcost',
                'master.gol',
                'master.term',
                'master.id_pt',
                'simpleinterest.id_pt'

            )

            ->paginate($perPage);
            // Log data yang diambil
    // Log::info('Data fetched from tblobalcorporateloan and tblmaster_tmpcorporate', ['data' => $result]);

    }

    public static function getLoanDetailsbyidpt($id_pt)
    {
        return self::where('id_pt', $id_pt)
            ->get();
    }

    public static function getLoanjoinByIdPt($id_pt)
{
    return DB::table('public.tblmaster_tmpcorporate as master')
        ->join('public.tblobaleffective as simpleinterest',  DB::raw("master.no_acc::varchar"), '=', 'simpleinterest.no_acc')
        ->join('public.tblpsaklbucorporateloan as corporateloan', 'master.no_acc', '=', 'corporateloan.no_acc')
        ->where('master.id_pt', $id_pt)
        ->select(
           'master.no_branch',
            'simpleinterest.no_acc',
            'master.deb_name',
            'simpleinterest.deb_name as effective_deb_name', // Mengambil deb_name dari effective
            'master.ln_type',
            'master.org_bal',
            'simpleinterest.org_bal as effective_org_bal', // Mengambil org_bal dari effective
            'master.org_date_dt',
            'master.term as master_term',
            'simpleinterest.term as effective_term',
            'simpleinterest.mtr_date',
            'simpleinterest.eirex',
            'simpleinterest.eircalc',
            'simpleinterest.nbal',
            'simpleinterest.oldbal',
            'simpleinterest.principal',
            'simpleinterest.interest',
            'simpleinterest.adjsmnt',
            'simpleinterest.eircalc_conv',
            'simpleinterest.eircalc_cost',
            'simpleinterest.eircalc_fee',
            'master.id_pt',
            'master.GROUP',
            'master.rate',
            'master.pmtamt',
            'corporateloan.eircalc_conv',
            'corporateloan.eircalc_cost',
            'corporateloan.eircalc_fee'
        )
        ->get();
}
}

