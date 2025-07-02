<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'target_role',
        'date_field',
        'start_date',
        'end_date',
        'scheduled_active_date',
        'scheduled_end_date', 
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'scheduled_active_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationship: A distribution belongs to a Survey
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
