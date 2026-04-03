<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Burgers'],
            ['name' => 'Pizza'],
            ['name' => 'Sides'],
            ['name' => 'Drinks'],
            ['name' => 'Desserts'],
        ];

        DB::table('categories')->insert($categories);
    }
}
