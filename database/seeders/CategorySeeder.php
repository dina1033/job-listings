<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create(['name' => 'supplies', 'updated_at' => now(), 'created_at' => now()]);
        Category::create(['name' => 'toys', 'updated_at' => now(), 'created_at' => now()]);
    }
}
