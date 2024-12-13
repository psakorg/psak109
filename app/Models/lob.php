<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lob extends Model
{
    use HasFactory;

    // Nama tabel yang terhubung dengan model ini
    protected $table = 'tbl_lob';

    // Kolom primary key pada tabel
    protected $primaryKey = 'lob_id';

    // Jika primary key bukan auto-increment, bisa didefinisikan seperti ini, tapi karena tabel menggunakan identity, kita biarkan default
    public $incrementing = true;

    // Jika tidak menggunakan timestamp (created_at, updated_at), bisa disable dengan properti berikut
    public $timestamps = false;

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'ln_type',
        'description',
    ];
}
