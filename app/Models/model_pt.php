<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_pt extends Model
{
    use HasFactory;

    protected $table = 'tbl_pt';

    protected $primaryKey = 'id_pt';
    public $incrementing = false; // Jangan auto increment
    public $timestamps = false; // Nonaktifkan timestamps

    protected $fillable = [
        'nama_pt',
        'alamat_pt',
        'company_type',
    ];

    /**
     * Relasi ke model User.
     * Setiap Pt dapat memiliki banyak User.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'nama_pt', 'nama_pt');
    }
}
