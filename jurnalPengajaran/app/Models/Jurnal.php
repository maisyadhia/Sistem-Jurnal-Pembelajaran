<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    protected $table = 'jurnals';
    
    protected $fillable = [
        'teacher_id',
        'date',
        'class',
        'subject',
        'time',
        'topic',
        'next_target',
        'rpp_completed',
        'absent_students',
    ];

    protected $casts = [
        'date' => 'date',
        'rpp_completed' => 'boolean',
        'absent_students' => 'boolean',
    ];
}