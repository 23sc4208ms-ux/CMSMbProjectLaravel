<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'action',
        'subject_type',
        'subject_id',
        'description',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
