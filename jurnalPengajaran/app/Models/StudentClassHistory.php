<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassHistory extends Model
{
    use HasFactory;

    protected $table = 'student_class_histories';

    protected $fillable = [
        'student_id',
        'tahun_ajaran',
        'kelas',
        'status',
        'keterangan',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}