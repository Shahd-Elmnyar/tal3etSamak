<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        // Check if $this->name and $this->content are strings before decoding
        $name = is_string($this->name) ? json_decode($this->name, true) : $this->name;
        $content = is_string($this->content) ? json_decode($this->content, true) : $this->content;

        return [
            'id' => $this->id,
            'name' => is_array($name) ? ($name[$locale] ?? $name['en']) : $this->name,
            'slug' => $this->slug,
            'content' => is_array($content) ? ($content[$locale] ?? $content['en']) : $this->content,
            'img' => url('uploads/' . $this->img),
            'active' => $this->active,
            'price' => $this->price,
        ];
    }
}
