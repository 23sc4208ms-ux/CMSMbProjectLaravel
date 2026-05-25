<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivityLog;

$logs = ActivityLog::latest()->take(20)->get();

foreach ($logs as $log) {
    echo "[{$log->created_at}] {$log->action} - {$log->description} (subject_type={$log->subject_type}, subject_id={$log->subject_id})\n";
}
