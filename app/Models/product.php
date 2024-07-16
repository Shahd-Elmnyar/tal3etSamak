<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Add the fillable property
    protected $fillable = [
        'name',
        'description',
        'img',
        'price',
        'offer_price',
        'discount_type',
        'discount',
        'offer',
        'sale',
        'active',
        'category_id',
        'size_id'
    ];
    protected $casts = [
        'description' => 'array',
        'name' => 'array',
    ];

    // Define the relationship with the Category model
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Define the relationship with the Size model
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    // Define the relationship with the Order model
    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Define the relationship with the CartItem model
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
