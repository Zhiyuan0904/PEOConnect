<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'user_id',
        'responses',
    ];

    protected $casts = [
        'responses' => 'array', // Laravel automatically decode JSON
    ];

    // New: Each response belongs to a Survey
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    // New: Each response belongs to a User (student)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
