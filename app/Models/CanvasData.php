<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanvasData extends Model
{
    use HasFactory;
    protected $table = 'canvas_data';

    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array',
    ];
}
