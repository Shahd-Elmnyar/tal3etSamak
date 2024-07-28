<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\SizeResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\AdditionResource;
use App\Http\Resources\CategoryResource;

class ProductResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function transformData(array $data): array
    {
        // Check if the resource is null
        if (is_null($this->resource)) {
            return [];
        }

        // Customize the data transformation
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
            'image' => $this->when($this->relationLoaded('images'), function () {
                return ImageResource::collection($this->images);
            }),
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'offer' => $this->offer,
            'sale' => $this->sale,
            'active' => $this->active,
            'order_count' => $this->total_quantity,
            'category' => $this->when($this->relationLoaded('categories'), function () {
                return CategoryResource::collection($this->categories);
            }),
            'size' => $this->when($this->relationLoaded('sizes'), function () {
                return SizeResource::collection($this->sizes);
            }),
            'is_favorite' => $this->isFavoriteByUser(request()->user()),
            'additions' => $this->when($this->relationLoaded('additions'), function () {
                return AdditionResource::collection($this->additions);
            }),
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
