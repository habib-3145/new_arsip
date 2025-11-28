<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ijazah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'siswa_id',
        'nisn',
        'tanggal',
        'kelas',
        'foto'
    ];

    // relasi ke tabel siswas
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Shourtcut untuk memanggil data jurusan

    public function getJurusanAttribute()
    {
        return $this->siswa?->jurusan;
    }
}
