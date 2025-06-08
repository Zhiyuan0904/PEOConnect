<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumContent extends Model
{
    protected $fillable = ['title', 'description', 'peo_ids', 'files', 'created_by'];

    protected $casts = [
        'peo_ids' => 'array',
        'files' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
