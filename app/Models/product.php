<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'is_offer',
        'is_sale',
        'active',
    ];
    protected $casts = [
        'description' => 'array',
        'name' => 'array',
    ];

    // Define the relationship with the Category model
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id')
            ->withTimestamps();
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
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    // Define the relationship with the Size model
    public function sizes()
    {
        return $this->hasMany(Size::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
    public function additions()
    {
        return $this->belongsToMany(Addition::class, 'product_addition', 'product_id', 'addition_id')
            ->withTimestamps();
    }

    public static function getTotalQuantities()
    {
        return self::select('products.*', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'products.offer_price', 'products.discount_type', 'products.discount', 'products.is_offer', 'products.is_sale', 'products.active', 'products.start', 'products.skip', 'products.created_at', 'products.updated_at')
            ->orderByDesc('total_quantity')
            ->limit(2)
            ->with(['additions', 'images', 'sizes', 'categories'])
            ->get();
    }
}
