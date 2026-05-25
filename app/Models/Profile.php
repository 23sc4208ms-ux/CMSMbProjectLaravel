<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'student_id',
        'bio',
        'phone',
        'avatar_path',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
