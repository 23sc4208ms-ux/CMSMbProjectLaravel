<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::query()
            ->latest('created_at')
            ->latest('id')
            ->paginate(20);

        return view('activity-logs.index', compact('logs'));
    }
}
