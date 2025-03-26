<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        Language::create(['name' => 'PHP', 'updated_at' => now(), 'created_at' => now()]);
        Language::create(['name' => 'JavaScript', 'updated_at' => now(), 'created_at' => now()]);
    }
}
