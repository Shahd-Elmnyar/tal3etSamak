<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Add the fillable property
    protected $fillable = [
        'quantity',
        'price',
        'total',
        'state',
        'product_id',
        'order_id',
        'size_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
