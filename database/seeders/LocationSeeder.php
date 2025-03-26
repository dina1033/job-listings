<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run()
    {
        Location::create(['city' => 'New York', 'state' => 'NY', 'country' => 'USA', 'updated_at' => now(), 'created_at' => now()]);
        Location::create(['city' => 'San Francisco', 'state' => 'CA', 'country' => 'USA', 'updated_at' => now(), 'created_at' => now()]);
    }
}
