<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuKeluarga extends Model
{

    use HasFactory;

    protected $fillable = [
        'no_kk',
        'alamat',
        'rt',
        'rw',
        'kode_pos',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
    ];

    protected $table = 'kartu_keluargas';
}
