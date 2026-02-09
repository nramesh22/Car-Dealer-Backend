<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'type',
        'path',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'type' => 'image',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
