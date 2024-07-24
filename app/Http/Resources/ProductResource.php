<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\SizeResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\AdditionResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        return [
            'id' => $this->id,
            'name' => $this->name[$locale] ?? $this->name['en'],
            'description' => $this->description[$locale] ?? $this->description['en'],
            'image' => ImageResource::collection($this->whenLoaded('images')),
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'offer' => $this->offer,
            'sale' => $this->sale,
            'active' => $this->active,
            'order_count' => $this->total_quantity, // Check for null
            'category' => CategoryResource::collection($this->whenLoaded('categories')),
            'size' => SizeResource::collection($this->whenLoaded('sizes')),
            'is_favorite' => $this->isFavoriteByUser($request->user()),
            'additions' => AdditionResource::collection($this->whenLoaded('additions')),
        ];

    }

    protected function isFavoriteByUser($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

}
