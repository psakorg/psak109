<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UploadSimpleInterest extends Model
{
    // Nama tabel utama yang digunakan dalam model ini
    protected $table = 'tblmaster_tmpcorporate';

    // Nama tabel untuk detail pinjaman korporat
    protected $tblCorporateLoanCabangDetail = 'tblcorporateloancabangdetail';

    /**
     * Mengambil semua data dari tabel tblmaster_tmpcorporate
     *
     * @return array
     */
    public function fetchTblmaster($id_pt)
    {
        return DB::table($this->table)->where('id_pt', $id_pt)->get()->toArray();
    }

    /**
     * Memeriksa duplikasi nomor akun (no_acc) di tabel tblmaster_tmpcorporate
     *
     * @param string $noAcc
     * @return bool
     */
    public function checkDuplicateTblmaster($noAcc)
    {
        return DB::table($this->table)->where('no_acc', $noAcc)->exists();
    }

    /**
     * Mengambil data berdasarkan nomor akun (no_acc) dari tabel tblmaster_tmpcorporate
     *
     * @param string $noAcc
     * @return object|null
     */
    public function getByNoAcc($noAcc)
    {
        return DB::table($this->table)->where('no_acc', $noAcc)->first();
    }

    /**
     * Memperbarui data berdasarkan nomor akun (no_acc) di tabel tblmaster_tmpcorporate
     *
     * @param string $noAcc
     * @param array $data
     * @return int
     */
    public function updateTblmaster($noAcc, $data)
    {
        return DB::table($this->table)->where('no_acc', $noAcc)->update($data);
    }

    /**
     * Menyisipkan beberapa data ke dalam tabel tblmaster_tmpcorporate
     *
     * @param array $data
     * @return int
     */
    public function insertTransactionBatch($data)
    {
        return DB::table($this->table)->insert($data);
    }

    /**
     * Mengambil semua data dari tabel tblcorporateloancabangdetail
     *
     * @return array
     */
    public function fetchTblCorporateLoanCabangDetail($id_pt)
    {
        return DB::table($this->tblCorporateLoanCabangDetail)->where('id_pt', $id_pt)->get()->toArray();
    }

    /**
     * Memeriksa duplikasi ID transaksi (idtrx) di tabel tblcorporateloancabangdetail
     *
     * @param string $idtrx
     * @return bool
     */
    public function checkDuplicateTblCorporateLoanCabangDetail($idtrx)
    {
        return DB::table($this->tblCorporateLoanCabangDetail)->where('idtrx', $idtrx)->exists();
    }

    /**
     * Menyisipkan beberapa data ke dalam tabel tblcorporateloancabangdetail
     *
     * @param array $data
     * @return int
     */
    public function insertTransactionBatchCorporate($data)
    {
        return DB::table($this->tblCorporateLoanCabangDetail)->insert($data);
    }

    protected $fillable = [
        'no_acc',
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
        'group',
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
        'gol'
    ];

    // Atau gunakan
    protected $guarded = [];
}
