<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Add the fillable property
    protected $fillable = [
        'total',
        'status',
        'type',
        'user_id',
        'payment_id'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Payment model
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }


}
