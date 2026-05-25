<?php

namespace Database\Seeders;

use App\Models\Degree;
use Illuminate\Database\Seeder;

class DegreeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $degrees = [
            ['code' => 'BSIT'],
            ['code' => 'BSHM'],
            ['code' => 'BSA'],
        ];

        Degree::upsert($degrees, ['code']);
    }
}
