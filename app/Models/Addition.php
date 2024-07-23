<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addition extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'content',
        'img',
        'active',
    ];

    protected $casts = [
        'content' => 'array',
        'name' => 'array',
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_addition', 'product_id', 'addition_id')
        ->withTimestamps();
    }
}
