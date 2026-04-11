<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'tagline',
        'price',
        'description',
        'category_id',
        'status_key',
        'is_featured',
        'sort_order',
        'start_at',
        'end_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'integer',
        'category_id' => 'integer',
        'is_featured' => 'integer',
        'sort_order' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag', 'product_id', 'product_tag_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order')->orderBy('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')
            ->where('is_primary', 1)
            ->orderByDesc('id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('updated_at')->orderByDesc('id');
    }
}

