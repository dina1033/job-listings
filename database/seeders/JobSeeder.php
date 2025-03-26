<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Language;
use App\Models\Location;
use App\Models\Category;
use App\Models\JobAttributeValue;
use App\Models\Attribute;

class JobSeeder extends Seeder
{
    public function run()
    {
        $php = Language::where('name', 'PHP')->first();
        $js = Language::where('name', 'JavaScript')->first();
        $ny = Location::where('city', 'New York')->first();
        $supplies = Category::where('name', 'supplies')->first();

        $experience = Attribute::where('name', 'years_experience')->first();

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

        JobAttributeValue::create([
            'job_id' => $job->id,
            'attribute_id' => $experience->id,
            'value' => '5',
            'updated_at' => now(),
            'created_at' => now()
        ]);
    }
}
