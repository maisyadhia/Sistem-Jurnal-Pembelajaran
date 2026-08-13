<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapelMaster extends Model
{
    use HasFactory;

    protected $table = 'mapel_master';
    
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
    ];
}