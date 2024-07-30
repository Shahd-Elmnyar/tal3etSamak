<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity',
        'price',
        'total',
        'state',
        'cart_id',
        'product_id',
        'addition_quantity',
        'size_id',
        'total_addition_price',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
