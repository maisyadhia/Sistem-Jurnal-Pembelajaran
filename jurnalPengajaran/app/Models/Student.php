<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    
    protected $fillable = [
        'nisn',
        'name',
        'gender',
        'dob',
        'class',
        'tahun_ajaran',
        'status',
        'tanggal_keluar',
        'keterangan',
        'parent_name',
        'parent_phone',
    ];

    protected $casts = [
        'dob' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeTahunAjaran($query, $tahun)
    {
        return $query->where('tahun_ajaran', $tahun);
    }

    public function classHistories()
    {
        return $this->hasMany(StudentClassHistory::class);
    }
}