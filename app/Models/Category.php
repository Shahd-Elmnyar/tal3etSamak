<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Add the fillable property
    protected $fillable = [
        'name',
        'slug',
        'content',
        'img',
        'active',
        'parent_id'
    ];

    protected $casts = [
        'content' => 'array',
        'name' => 'array',
    ];

    // Define the self-referential relationship
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category', 'category_id', 'product_id')
            ->withTimestamps();
    }
}
