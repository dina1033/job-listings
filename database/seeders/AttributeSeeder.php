<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        Attribute::create([
            'name' => 'years_experience',
            'type' => 'number',
            'updated_at' => now(),
            'created_at' => now()
        ]);

        Attribute::create([
            'name' => 'education_level',
            'type' => 'select',
            'options' => ['high_school', 'bachelors', 'masters', 'phd'],
            'updated_at' => now(),
            'created_at' => now()
        ]);
    }
}
