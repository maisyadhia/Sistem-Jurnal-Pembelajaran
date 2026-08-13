<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasMaster extends Model
{
    use HasFactory;

    protected $table = 'kelas_master';
    
    protected $fillable = [
        'nama_kelas',
        'wali_kelas',
    ];
}