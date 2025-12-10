<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SdmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sdms = [
            [
                'name' => 'SDM 1',
                'code' => 'SDM001',
                'description' => 'SDM/HRD Unit 1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SDM 2',
                'code' => 'SDM002',
                'description' => 'SDM/HRD Unit 2',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($sdms as $sdm) {
            DB::table('sdms')->insertOrIgnore($sdm);
        }
    }
}

