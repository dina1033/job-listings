<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AttributeSeeder::class,
            LanguageSeeder::class,
            CategorySeeder::class,
            LocationSeeder::class,
            JobSeeder::class,
        ]);
    }
}
