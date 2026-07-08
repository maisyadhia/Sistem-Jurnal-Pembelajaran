<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    
    protected $fillable = [
        'student_id',
        'subject',
        'teacher',
        'topic',
        'next_topic',
        'date_time',
        'icon',
        'color',
        'is_past',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'is_past' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}