<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $fillable = [
        'title', 'description', 'company_name', 
        'salary_min', 'salary_max', 'is_remote',
        'job_type', 'status', 'published_at'
    ];
    
    protected $casts = [
        'is_remote' => 'boolean',
        'published_at' => 'datetime',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];
    
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }
    
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }
    
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class,'job_category','job_id','category_id');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class);
    }
    
    public function attributeValues(): HasMany
    {
        return $this->hasMany(JobAttributeValue::class);
    }
    
    // Scopes for filtering
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
