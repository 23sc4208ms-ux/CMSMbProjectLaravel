<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'student_id',
        'title',
        'content',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
