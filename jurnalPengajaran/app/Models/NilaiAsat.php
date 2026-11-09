<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAsat extends Model
{
    use HasFactory;

    protected $table = 'nilai_asat';

    protected $fillable = [
        'student_id',
        'tahun_ajaran',
        'kelas',
        'nilai_total',
        'nilai_bind',
        'nilai_mat',
        'nilai_ipa',
        'catatan',
    ];

    protected $casts = [
        'nilai_total' => 'decimal:2',
        'nilai_bind' => 'decimal:2',
        'nilai_mat' => 'decimal:2',
        'nilai_ipa' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeTahunAjaran($query, $tahun)
    {
        return $query->where('tahun_ajaran', $tahun);
    }
}