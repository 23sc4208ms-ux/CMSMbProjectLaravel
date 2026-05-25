<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'code',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'course_students', 'course_id', 'student_id');
    }
}
