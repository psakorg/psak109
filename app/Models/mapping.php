<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mapping extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak menggunakan konvensi penamaan
    protected $table = 'tbl_mapping';

    // Tentukan primary key jika tidak menggunakan 'id'
    protected $primaryKey = 'id';

    // Jika primary key bukan auto-increment
    public $incrementing = false;

    // Jika tipe primary key bukan integer
    protected $keyType = 'bigint';

    // Tentukan kolom yang dapat diisi massal
    protected $fillable = [
        'user_id',
        'modul_id',
        'periode',
        'effective',
        'simple_interest',
        'lob_id',
        'company_type',
    ];

    // Definisikan relasi jika ada
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id'); // Sesuaikan dengan kolom user_id pada tabel users
    }

    // Anda bisa menambahkan relasi lain sesuai kebutuhan
}
