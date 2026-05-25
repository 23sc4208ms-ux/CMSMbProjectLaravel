<?php
require 'bootstrap/app.php';

use Illuminate\Database\Capsule\Manager as DB;

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Query degrees
echo "=== DEGREES TABLE ===\n";
$degrees = DB::table('degrees')->orderBy('id')->get();
foreach ($degrees as $degree) {
    echo "ID: {$degree->id}, Code: {$degree->code}, Name: {$degree->name}\n";
}

echo "\n=== STUDENTS TABLE ===\n";
$students = DB::table('students')
    ->leftJoin('degrees', 'students.degree_id', '=', 'degrees.id')
    ->select('students.id', 'students.first_name', 'students.last_name', 'students.degree_id', 'degrees.code', 'degrees.name')
    ->orderBy('students.id')
    ->get();

foreach ($students as $student) {
    $degree_info = $student->degree_id ? "ID: {$student->degree_id}, Code: {$student->code}, Name: {$student->name}" : "No degree assigned";
    echo "Student ID: {$student->id}, Name: {$student->first_name} {$student->last_name}, Degree: {$degree_info}\n";
}

echo "\n=== CHECKING FOR DEGREE ID 14 ===\n";
$degree14 = DB::table('degrees')->where('id', 14)->first();
if ($degree14) {
    echo "Found: ID: {$degree14->id}, Code: {$degree14->code}, Name: {$degree14->name}\n";
    
    $students_with_14 = DB::table('students')->where('degree_id', 14)->get();
    echo "Students with degree_id 14: " . count($students_with_14) . "\n";
    foreach ($students_with_14 as $s) {
        echo "  - {$s->first_name} {$s->last_name}\n";
    }
} else {
    echo "No degree found with ID 14\n";
}
