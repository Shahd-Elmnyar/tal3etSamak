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

        // Decode the JSON string into an array
        $name = json_decode($this->name, true);
        $content = json_decode($this->content, true);

        return [
            'id' => $this->id,
            'name' => is_array($name) ? ($name[$locale] ?? $name['en']) : $this->name,
            'slug' => $this->slug,
            'content' => is_array($content) ? ($content[$locale] ?? $content['en']) : $this->content,
            'img' => $this->img,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
        ];
    }
}
