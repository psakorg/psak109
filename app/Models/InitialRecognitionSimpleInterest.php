<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InitialRecognitionSimpleInterest extends Model
{
    public static function getInitialRecognition($branch, $tahun, $bulan)
    {
        return DB::select("
            SELECT DISTINCT 
                a.id,
                a.no_branch,
                a.no_acc,
                a.deb_name,
                c.LN_TYPE,
                TO_CHAR(a.org_date, 'DD/MM/YYYY') as OrgdtConv,
                c.term,
                c.pmtamt,
                c.\"group\" as glgroup,
                c.RATE,
                TO_CHAR(a.mtr_date, 'DD/MM/YYYY') as MtrdtConv,
                a.org_bal,
                a.oldbal,
                b.baleir,
                a.adjsmnt,
                a.eirex,
                a.eircalc,
                a.eircalc_conv,
                a.eircalc_cost,
                a.eircalc_fee,
                b.outsamtconv,
                b.outsamtcost,
                b.outsamtfee,
                EOM(b.TglAngsuran) as TglAngsuranConv,
                m.jdname,
                d.coa
            FROM tblOBALcorporateloan a, \"CABANG-\" m, tblMASTER_tmpcorporate c, tblGROUPCOALoan d, tblCFOBALcorporateloan b
            WHERE a.no_branch::numeric = m.jdbr::numeric
            AND a.no_acc::numeric = c.no_acc::numeric
            AND c.\"group\" = d.\"GROUP\"
            AND a.no_acc = b.no_acc
            AND DATE_PART('year', b.tglangsuran) = ?
            AND DATE_PART('month', b.tglangsuran) = ?
            AND (a.org_date) > '2012-01-01'
            AND b.BULANKE = 0
            AND a.NO_BRANCH::text = ?
        ", [$tahun, $bulan, $branch]);
    }
}