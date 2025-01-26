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
        return self::join('securities.tblmaster_tmpbid as master', 'securities.tblobalsecurities.no_acc', '=', DB::raw("master.no_acc")) // Join ke tblmaster_tmpcorporate dengan alias 'master'
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

public static function spcashflowtreasurybond($id_pt, $perPage = 1000, $no_acc)
{
    $query = DB::table('securities.tblcfobalsecurities as a')
        ->join('securities.tblmaster_tmpbid as m', 'a.no_acc', '=', 'm.no_acc')
        ->join('securities.tblobalsecurities as tbo', function($join) {
            $join->on(DB::raw('tbo.no_acc::numeric'), '=', 'a.no_acc')
                 ->on(DB::raw('tbo.no_branch::numeric'), '=', 'm.no_branch');
        })
        ->select([
            DB::raw('DISTINCT a.no_acc'),
            'a.month_to',
            'm.id_pt',
            DB::raw("TO_CHAR(a.transac_dt, 'DD/MM/YYYY') as TglAngsuranConv"),
            'a.transac_dt',
            'tbo.issuer_name',
            'm.org_bal',
            DB::raw("TO_CHAR(m.settle_dt, 'DD/MM/YYYY') as settle_dt"),
            DB::raw("TO_CHAR(tbo.org_date, 'DD/MM/YYYY') as org_date"),
            'm.tenor',
            DB::raw("TO_CHAR(tbo.mtr_date, 'DD/MM/YYYY') as mtr_date"),
            'm.bond_id', 'm.coupon_rate', 'a.pmtamt', 'm.atdiscount', 'm.atpremium', 
            'm.brokerage', 'a.face_value', 'a.interest_eir', 'a.fair_value',
            'a.price', 'a.amortized', 'a.accr_conv', 'a.outsamt_conv', 'a.timegap',
            'a.accr_disc', 'a.outsamt_disc', 'a.amortise_disc', 'a.accr_prem',
            'a.outsamt_prem', 'a.amortise_prem', 'a.accr_brok', 'a.outsamt_brok',
            'a.amortise_brok', 'a.principal', 'a.principal_in','a.principal_out', 'a.interest', 'a.haribunga', 'tbo.yield', 'tbo.eirex',
            'tbo.eircalc', 'tbo.eircalc_conv', 'tbo.eircalc_disc', 'tbo.eircalc_prem',
            'tbo.eircalc_brok', 'tbo.ibase',
            DB::raw('CASE WHEN (m.bond_grp = 4) OR (m.bond_grp = 2) THEN 
                m.face_value
            ELSE
                (m.face_value - coalesce(m.atdiscount,0) + coalesce(m.atpremium,0) + coalesce(m.brokerage,0) 
                + coalesce((SELECT SUM(amortise_disc) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to),0) 
                + coalesce((SELECT SUM(amortise_prem) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to),0) 
                + coalesce((SELECT SUM(amortise_brok) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to),0))
            END as CARRYING_AMOUNT'),
            DB::raw('(SELECT SUM(amortized) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_amortitized'),
            DB::raw('(SELECT SUM(timegap) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_timegap'),
            DB::raw('(SELECT SUM(amortise_disc) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_amortisedisc'),
            DB::raw('(SELECT SUM(amortise_prem) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_amortiseprem'),
            DB::raw('(SELECT SUM(amortise_brok) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_amortisebrok'),
            DB::raw('(SELECT SUM(interest) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_interest'),
            DB::raw('(SELECT SUM(interest_eir) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_interest_eir'),
            DB::raw('(SELECT SUM(haribunga) FROM securities.tblcfobalsecurities where no_acc = a.no_acc and month_to <= a.month_to) as cum_haribunga'),
            DB::raw('CASE WHEN a.month_to = 0 THEN -a.FACE_VALUE ELSE a.pmtamt END as EXPECTED_CASH_FLOW'),
            DB::raw('CASE WHEN a.month_to = 0 THEN -OUTSAMT_CONV ELSE a.pmtamt END as OUTCONV'),
            DB::raw('CASE WHEN a.month_to = 0 THEN -OUTSAMT_DISC ELSE a.pmtamt END as OUTDISC'),
            DB::raw('CASE WHEN a.month_to = 0 THEN -OUTSAMT_PREM ELSE a.pmtamt END as OUTPREM'),
            DB::raw('CASE WHEN a.month_to = 0 THEN -OUTSAMT_BROK ELSE a.pmtamt END as OUTBROK')
        ]);

    if ($no_acc) {
        $query->where('a.no_acc', $no_acc)
              ->where('m.no_branch', $id_pt);
    } else {
        $query->where('m.no_branch', $id_pt);
    }

    $query->orderBy('a.month_to');

    return $query->paginate($perPage);
}


//     // Method untuk mengambil semua data dengan paginasi
    public static function fetchAll($id_pt, $perPage = 1000)
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


    public static function fetchInitialRecognition($id_pt, $perPage, $tahun, $bulan)
    {
        return DB::table('securities.tbldatasecurities as a')
            ->join('securities.tblobalsecurities as b', 'a.no_acc', '=', 'b.no_acc')
            ->join('securities.tblcfobalsecurities as c', 'a.no_acc', '=', 'c.no_acc')
            ->join('securities.tblpricesecurities as d', 'a.no_acc', '=', 'd.no_acc')
            ->select([
                'a.no_branch',
                'a.no_acc',
                'b.bond_id',
                'a.status',
                'b.issuer_name',
                'a.bond_jns',
                'b.bond_type',
                'b.org_bal',
                'b.org_date',
                'b.tenor',
                'b.mtr_date',
                'b.coupon_rate',
                'b.yield',
                'b.eirex',
                'b.eircalc',
                'b.face_value',
                'b.fair_value',
                'b.atdiscount',
                'b.atpremium',
                'b.brokerage',
                'b.eircalc_conv',
                'b.eircalc_disc',
                'b.eircalc_prem',
                'b.eircalc_brok',
                'd.price',
                'd.price_date',
                'b.ibase',
                'a.id_pt'
            ])
            ->where('a.no_branch', $id_pt)
            ->where('c.month_to', 0)
            ->whereYear('b.org_date', $tahun)
            ->whereMonth('b.org_date', $bulan)
            ->where('a.id_pt', $id_pt)
            ->orderBy('a.no_acc')
            ->paginate($perPage);
    }
}
