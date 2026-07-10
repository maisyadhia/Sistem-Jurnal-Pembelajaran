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
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}