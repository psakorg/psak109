<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Import Facade DB

class modul extends Model
{
    use HasFactory;

    protected $table = 'tbl_modul'; // Nama tabel
    protected $primaryKey = 'modul_id'; // Primary Key
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_modul', // Kolom yang dapat diisi
    ];

    /**
     * Contoh metode menggunakan Facade DB.
     * Mengambil semua modul tanpa menggunakan Eloquent.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllModulsUsingDB()
    {
        return DB::table('tbl_modul')->get(); // Menggunakan query builder
    }
}
