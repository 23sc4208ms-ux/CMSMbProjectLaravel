<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('students', 'email')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_email_unique');
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'email')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('student_id');
        });
    }
};
