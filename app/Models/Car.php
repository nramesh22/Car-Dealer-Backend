<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'brand',
        'model',
        'year',
        'price',
        'mileage',
        'fuel_type',
        'transmission',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'slug',
        'featured_image_path',
        'video_url',
        'video_path',
    ];

    protected $casts = [
        'year' => 'integer',
        'price' => 'integer',
        'mileage' => 'integer',
    ];

    public function media(): HasMany
    {
        return $this->hasMany(CarMedia::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
