<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Report_effective extends Model
{
    // Mengganti tabel utama menjadi tblOBALCorporateLoan
    protected $table = 'public.tblobaleffective';

    // Jika primary key bukan 'id', spesifikkan di sini
    protected $primaryKey = 'id';

    // Jika tidak menggunakan timestamps (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang bisa diakses
    protected $fillable = [
        'no_branch', 'no_acc', 'deb_name', 'ln_type', 'org_bal',
        'org_date', 'term', 'mtr_date', 'eirex', 'eircalc',
        'nbal', 'oldbal', 'principal', 'interest', 'adjsmnt',
        'eircalc_conv', 'eircalc_cost', 'eircalc_fee', 'id_pt'
    ];

    // Method untuk mendapatkan semua pinjaman korporat
    public static function getCorporateLoans()
    {
        return self::select('no_acc', 'id_pt','no_branch', 'deb_name', 'org_bal', 'org_date', 'term', 'interest', 'eircalc_conv', 'eircalc_fee', 'ln_type', 'mtr_date', 'eirex', 'eircalc');
    }

    // Method untuk mendapatkan detail pinjaman berdasarkan nomor akun
    public static function getLoanDetails($no_acc, $id_pt)
    {
        return self::where('no_acc', $no_acc)
            ->where('id_pt', $id_pt)
            ->first();
    }

    // Method untuk mendapatkan laporan berdasarkan nomor akun
    public static function getReportsByNoAcc($no_acc, $id_pt)
    {
        return DB::table('public.tblcfobaleffective')
        ->join('tbl_pt', 'tblcfobaleffective.id_pt', '=', 'tbl_pt.id_pt')
            ->where('tblcfobaleffective.no_acc', $no_acc)
            ->where('tblcfobaleffective.id_pt', $id_pt)
            ->select('tblcfobaleffective.*', 'tbl_pt.*')  // Specify the columns you need from both tables
            ->get();
    }

    // Method untuk mengambil pinjaman dari tblmaster_tmp berdasarkan no_acc
    public static function getMasterDataByNoAcc($no_acc, $id_pt)
    {
        return DB::table('public.tblmaster_tmp')
            ->where('no_acc', $no_acc)
            ->where('id_pt', $id_pt)
            ->select(
                'no_branch',
                'deb_name',
                'status',
                'ln_type',
                'org_date',
                'org_date_dt',
                'term',
                'mtr_date',
                'mtr_date_dt',
                'org_bal',
                'rate',
                'cbal',
                'prebal',
                'bilprn',
                'pmtamt',
                'lrebd',
                'lrebd_dt',
                'nrebd',
                'nrebd_dt',
                'ln_grp',
                'GROUP',
                'bilint',
                'bisifa',
                'birest',
                'freldt',
                'freldt_dt',
                'resdt',
                'resdt_dt',
                'restdt',
                'restdt_dt',
                'prov',
                'trxcost',
                'gol',
                'id_pt'// Pastikan ini ada
            )
            ->first();
    }

    // Method untuk mengambil data lengkap berdasarkan no_acc
    public static function getobalDataByNoAcc($no_acc, $id_pt)
    {
        return self::where('no_acc', $no_acc)
            ->where('id_pt', $id_pt)
            ->select('*')
            ->get();
    }

    // Method untuk mengambil semua data dengan paginasi
    public static function fetchAll($id_pt, $perPage = 10)
{
    return DB::table('public.tblobaleffective as effective')
        ->leftJoin('public.tblmaster_tmp as master', 'effective.no_acc', '=', DB::raw("master.no_acc::varchar"))
        ->where('effective.id_pt', $id_pt) // Menambahkan kondisi id_pt
        ->select(
                'effective.no_branch',
                'effective.no_acc',
                'effective.deb_name',
                'effective.ln_type',
                'effective.org_bal',
                'effective.org_date',
                'effective.term',
                'effective.mtr_date',
                'effective.eirex',
                'effective.eircalc',
                'effective.nbal',
                'effective.oldbal',
                'effective.principal',
                'effective.interest',
                'effective.adjsmnt',
                'effective.eircalc_conv',
                'effective.eircalc_cost',
                'effective.eircalc_fee',
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
                'master.id_pt',
                'effective.id_pt'

            )

            ->paginate($perPage);
            // Log data yang diambil
    Log::info('Data fetched from tblobaleffective and tblmaster_tmp', ['data' => $result]);

    }

    //pemanggilan untuk id pt nya saja
    public static function getMasterByIdPt($id_pt)
    {
        return DB::table('public.tblmaster_tmp')
            ->where('id_pt', $id_pt)
            ->select(
                'no_branch',
                'deb_name',
                'status',
                'ln_type',
                'org_date',
                'org_date_dt',
                'term',
                'mtr_date',
                'mtr_date_dt',
                'org_bal',
                'rate',
                'cbal',
                'prebal',
                'bilprn',
                'pmtamt',
                'lrebd',
                'lrebd_dt',
                'nrebd',
                'nrebd_dt',
                'ln_grp',
                'GROUP',
                'bilint',
                'bisifa',
                'birest',
                'freldt',
                'freldt_dt',
                'resdt',
                'resdt_dt',
                'restdt',
                'restdt_dt',
                'prov',
                'trxcost',
                'gol',
                'id_pt'
            )
            ->get();
    }

    public static function getLoanDetailsbyidpt($id_pt)
    {
        return self::where('id_pt', $id_pt)
            ->get();
    }

    public static function getLoanjoinByIdPt($id_pt)
{
    return DB::table('public.tblmaster_tmp as master')
        ->join('public.tblobaleffective as effective',  DB::raw("master.no_acc::varchar"), '=', 'effective.no_acc')
        ->where('master.id_pt', $id_pt)
        ->select(
           'master.no_branch',
            'effective.no_acc',
            'master.deb_name',
            'effective.deb_name as effective_deb_name', // Mengambil deb_name dari eeffective
            'master.ln_type',
            'master.org_bal',
            'effective.org_bal as effective_org_bal', // Mengambil org_bal dari effective
            'master.org_date_dt',
            'master.term as master_term',
            'effective.term as effective_term',
            'effective.mtr_date',
            'effective.eirex',
            'effective.eircalc',
            'effective.nbal',
            'effective.oldbal',
            'effective.principal',
            'effective.interest',
            'effective.adjsmnt',
            'effective.eircalc_conv',
            'effective.eircalc_cost',
            'effective.eircalc_fee',
            'master.id_pt',
            'master.GROUP',
            'master.rate',
            'master.pmtamt'
        )
        ->get();
}
}

