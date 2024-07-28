<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CategoryResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function transformData(array $data): array
    {
        // $data = parent::toArray($request);

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'img' => url('uploads/' . $data['img']),
            'active' => $data['active'],
            'parent' => isset($data['parent']) ? new CategoryResource($this->whenLoaded('parent')) : null,
            'children' => isset($data['children']) ? CategoryResource::collection($this->whenLoaded('children')) : null,
            'products' => isset($data['products']) ? ProductResource::collection($this->whenLoaded('products')) : null,
        ];
    }
}
