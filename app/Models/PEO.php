<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PEO extends Model
{
    use HasFactory;
    
    protected $table = 'peos';

    protected $fillable = ['code', 'description'];
}
