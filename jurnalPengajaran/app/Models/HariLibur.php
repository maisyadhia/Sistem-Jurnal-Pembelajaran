<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    // 🔥 WAJIB: Beri tahu Laravel nama tabel aslinya (karena tidak pakai 's')
    protected $table = 'hari_libur';

    protected $fillable = [
        'tanggal',
        'keterangan',
    ];
}