<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataMaster extends Model
{
    use HasFactory;

    protected $table = 'data_masters';
    
    protected $fillable = [
        'name',
        'identifier',
        'initials',
        'category',
        'status',
        'statusColor',
        'color',
    ];
}