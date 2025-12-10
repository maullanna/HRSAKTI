<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      $sections = [
         [
            'name' => 'Section IT & Sarpras',
            'code' => 'SEC001',
            'description' => 'Section IT & Sarana Prasarana',
            'is_active' => true,
         ],
         [
            'name' => 'Section Prodi TPMO',
            'code' => 'SEC002',
            'description' => 'Section Program Studi TPMO',
            'is_active' => true,
         ],
         [
            'name' => 'Section Prodi TOPKR4',
            'code' => 'SEC003',
            'description' => 'Section Program Studi TOPKR4',
            'is_active' => true,
         ],
         [
            'name' => 'Section BAAK',
            'code' => 'SEC004',
            'description' => 'Section BAAK',
            'is_active' => true,
         ],
         [
            'name' => 'Section Teaching Factory',
            'code' => 'SEC005',
            'description' => 'Section Teaching Factory',
            'is_active' => true,
         ],
         [
            'name' => 'Section Administrasi',
            'code' => 'SEC006',
            'description' => 'Section Administrasi',
            'is_active' => true,
         ],
      ];

      foreach ($sections as $section) {
         // Check if section already exists
         $existing = Section::where('name', $section['name'])->first();
         if (!$existing) {
            Section::create($section);
         }
      }
   }
}
