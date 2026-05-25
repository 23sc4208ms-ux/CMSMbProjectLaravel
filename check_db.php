<?php
// Load Laravel
$app = require __DIR__ . '/bootstrap/app.php';

// Get the kernel and bootstrap
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the database instance
$db = app('db');

// Query degrees
echo "=== DEGREES TABLE ===\n";
$degrees = $db->table('degrees')->orderBy('id')->get();
if (count($degrees) > 0) {
    echo "Found " . count($degrees) . " degrees:\n";
    foreach ($degrees as $degree) {
        echo "  ID: {$degree->id}, Code: {$degree->code}, Name: {$degree->name}\n";
    }
} else {
    echo "No degrees found\n";
}

echo "\n=== STUDENTS TABLE ===\n";
$students = $db->table('students')
    ->leftJoin('degrees', 'students.degree_id', '=', 'degrees.id')
    ->select(
        'students.id',
        'students.first_name',
        'students.last_name',
        'students.degree_id',
        $db->raw("COALESCE(degrees.code, 'N/A') as code"),
        $db->raw("COALESCE(degrees.name, 'N/A') as degree_name")
    )
    ->orderBy('students.id')
    ->get();

if (count($students) > 0) {
    echo "Found " . count($students) . " students:\n";
    foreach ($students as $student) {
        $degree_info = $student->degree_id ? "ID: {$student->degree_id}, Code: {$student->code}, Name: {$student->degree_name}" : "None";
        echo "  Student ID: {$student->id}, Name: {$student->first_name} {$student->last_name}, Degree: {$degree_info}\n";
    }
} else {
    echo "No students found\n";
}

echo "\n=== CHECKING FOR DEGREE ID 14 ===\n";
$degree14 = $db->table('degrees')->where('id', 14)->first();
if ($degree14) {
    echo "Found degree with ID 14:\n";
    echo "  Code: {$degree14->code}, Name: {$degree14->name}\n";
    
    $students_with_14 = $db->table('students')->where('degree_id', 14)->get();
    echo "Students with degree_id 14: " . count($students_with_14) . "\n";
    foreach ($students_with_14 as $s) {
        echo "  - {$s->first_name} {$s->last_name} (Student ID: {$s->student_id})\n";
    }
} else {
    echo "No degree found with ID 14\n";
}
