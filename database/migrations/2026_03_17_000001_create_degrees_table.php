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
        Schema::create('degrees', function (Blueprint $table) {
            $table->id();
            $table->string('code', 120)->unique();
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'degree_id')) {
                $table->foreign('degree_id')
                    ->references('id')
                    ->on('degrees')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'degree_id')) {
                $table->dropForeign(['degree_id']);
            }
        });

        Schema::dropIfExists('degrees');
    }
};
