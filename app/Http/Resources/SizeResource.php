<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class SizeResource extends MainResource
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
            'name' => $this->name,
        ];
    }
}
