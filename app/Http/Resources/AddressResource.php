<?php

namespace App\Http\Resources;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Resources\CityResource;
use App\Http\Resources\MainResource;


class AddressResource extends MainResource
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
            'content' => $this->content,
            'building' => $this->building,
            'floor' => $this->floor,
            'apartment' => $this->apartment,
            'type' => $this->type,
            'active' => $this->active,
            'information' => $this->information,
            'city_of_residence' => $this->city_of_residence,
            'geo_address' => $this->geo_address,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'city' => new CityResource($this->whenLoaded('city')),
            'user_id' => $this->user_id,
        ];
    }
}
