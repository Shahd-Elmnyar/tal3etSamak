<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class MenuResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function transformData(array $data): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name, // Already localized
            'slug' => $this->slug,
            'content' => $this->content, // Already localized
            'img' => url('uploads/' . $this->img),
            'active' => $this->active,
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
