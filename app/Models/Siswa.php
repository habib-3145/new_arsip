<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'jurusan',   // <── Tambahkan field jurusan
        'email',
        'user_id',   // <── perbaikan, sebelumnya tertulis 'user'
    ];

    // relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke tabel ijazahs (1 siswa memiliki 1 ijazah)
    public function ijazahs()
    {
        return $this->hasMany(Ijazah::class);
    }
}
