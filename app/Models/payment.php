<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    // Add the fillable property
    protected $fillable = [
        'method'
    ];

    // Define the relationship with the Order model
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
