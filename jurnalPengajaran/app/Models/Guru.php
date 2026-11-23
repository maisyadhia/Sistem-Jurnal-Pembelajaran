<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';

    protected $fillable = [
        'kode_guru',
        'nik',
        'nama_guru',
        'no_hp',
        'password',
        'role',
        'is_aktif',
    ];

    protected $hidden = [
        'password',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}