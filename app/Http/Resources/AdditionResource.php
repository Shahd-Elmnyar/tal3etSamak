<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class AdditionResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function transformData(array $data): array
    {
        // $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'content' => $this->content,
            'img' => url('uploads/' . $this->img),
            'active' => $this->active,
            'price' => $this->price,
        ];
    }
}
