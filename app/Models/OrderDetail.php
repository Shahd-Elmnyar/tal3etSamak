<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    // Add the fillable property
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'address_type',
        'order_type',
        'payment_id',
        'order_id',
        'user_id',
        'address_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Payment model
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
