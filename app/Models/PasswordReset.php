<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    //public $incrementing = true; // Disable auto-incrementing IDs
    //protected $primaryKey = 'email'; // Use 'email' as the primary key
    public $table = 'password_reset_tokens';
    public $timestamps = true;
    protected $fillable = ['email', 'token'];
}
