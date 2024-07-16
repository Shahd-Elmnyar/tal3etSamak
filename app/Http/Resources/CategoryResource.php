<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'slug' => $this->slug,
            'content' => $this->content[$locale] ?? $this->content['en'],
            'img' => $this->img,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            ];
    }
}
