<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'page_type',
        'order_id',
        'parent_id',
        'active',
        'link',
        'img',
        'video',
    ];

    public function scopeFilter($query, array $filters)
    {

        $query->when($filters['search'] ?? false, function ($query, $search) {

            $query->where(
                fn ($query) => $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
            );
        });
    }
}
