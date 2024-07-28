<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ImageResource extends MainResource
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
            'name' => url('uploads/' . $this->name),
        ];
    }
}
