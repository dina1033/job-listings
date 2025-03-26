<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attribute;
use App\Models\Language;
use App\Models\Location;
use App\Models\Job;
use App\Models\Category;
use App\Models\JobAttributeValue;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
// DatabaseSeeder.php
public function run()
{
    // Create attributes
    $experience = Attribute::create([
        'name' => 'years_experience',
        'type' => 'number',
        'updated_at' => now(),
        'created_at' => now()
    ]);
    
    $education = Attribute::create([
        'name' => 'education_level',
        'type' => 'select',
        'options' => ['high_school', 'bachelors', 'masters', 'phd'],
        'updated_at' => now(),
        'created_at' => now()
    ]);
    
    // Create languages
    $php = Language::create(['name' => 'PHP','updated_at' => now(),'created_at' => now()]);
    $js = Language::create(['name' => 'JavaScript','updated_at' => now(),'created_at' => now()]);
    
    $supplies = Category::create(['name' => 'supplies','updated_at' => now(),'created_at' => now()]);
    $toys = Category::create(['name' => 'toys','updated_at' => now(),'created_at' => now()]);
    

    // Create locations
    $ny = Location::create(['city' => 'New York', 'state' => 'NY', 'country' => 'USA','updated_at' => now(),'created_at' => now()]);
    $sf = Location::create(['city' => 'San Francisco', 'state' => 'CA', 'country' => 'USA','updated_at' => now(),'created_at' => now()]);
    
    // Create jobs with relationships
    $job = Job::create([
        'title' => 'Senior Laravel Developer',
        'description' => 'Looking for an experienced Laravel developer...',
        'company_name' => 'Tech Corp',
        'salary_min' => 80000,
        'salary_max' => 120000,
        'is_remote' => true,
        'job_type' => 'full-time',
        'status' => 'published',
        'published_at' => now(),
        'updated_at' => now(),
        'created_at' => now()
    ]);
    
    $job->languages()->attach([$php->id, $js->id]);
    $job->locations()->attach([$ny->id]);
    $job->categories()->attach([$supplies->id]);
    
    // Add EAV attributes
    JobAttributeValue::create([
        'job_id' => $job->id,
        'attribute_id' => $experience->id,
        'value' => '5',
        'updated_at' => now(),
        'created_at' => now()
    ]);
}
}
