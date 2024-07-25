<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;


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


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id')
            ->withTimestamps();
    }


    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

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


    public function userAdditions()
    {
        return $this->belongsToMany(Addition::class, 'user_product_addition')
        ->withPivot('quantity')
        ->withTimestamps();
    }



    public function getTotalAdditionPrice()
    {

        $total = $this->userAdditions->sum(function ($addition) {
            return $addition->pivot->quantity * $addition->price;
        });

        return $total;
    }




    public function scopeFilter($query, array $filters)
    {

        $query->when($filters['search'] ?? false, function ($query, $search) {

            $query->where(fn($query)=>$query
                ->where('name','like','%'.$search.'%')
                ->orWhere('description','like','%'.$search.'%')
            );

        });

        $query->when($filters['price_min'] ?? false, function ($query, $price_min) {

            $query->where('price', '>=', $price_min);
        });

        $query->when($filters['price_max'] ?? false, function ($query, $price_max) {

            $query->where('price', '<=', $price_max);
        });
    }

}
