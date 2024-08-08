<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'discount_type',
        'discount',
        'max_discount',
        'min_order',
        'times_used',
        'user_limit',
        'user_number',
        'start_date',
        'end_date',
        'active',
    ];
}
