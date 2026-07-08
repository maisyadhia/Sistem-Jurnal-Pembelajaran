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
        'dob',
        'class',
        'parent_name',
        'parent_phone',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}