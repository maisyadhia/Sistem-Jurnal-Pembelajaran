<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnreportedClass extends Model
{
    use HasFactory;

    protected $table = 'unreported_classes';
    
    protected $fillable = [
        'code',
        'subject',
        'teacher',
        'schedule',
        'date',
        'reported',
    ];

    protected $casts = [
        'date' => 'date',
        'reported' => 'boolean',
    ];
}