<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'image' => new ImageResource($this->whenLoaded('image')),
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'offer' => $this->offer,
            'sale' => $this->sale,
            'active' => $this->active,
            'order_count' => $this->total_quantity , // Check for null
            'category' => new CategoryResource($this->whenLoaded('category')),
            'size' => new SizeResource($this->whenLoaded('size')),
            'is_favorite' => $this->isFavoriteByUser($request->user()),
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
