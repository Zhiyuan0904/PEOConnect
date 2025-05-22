<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'questions', // ✅ important! must allow 'questions' field
    ];

    protected $casts = [
        'questions' => 'array', // ✅ automatically cast questions JSON to array
    ];
}
