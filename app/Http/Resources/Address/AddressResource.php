<?php

namespace App\Http\Resources\Address;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Resources\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'name' => is_array($this->name) ? ($this->name[$locale] ?? $this->name['en']) : $this->name,
            'content' => $this->content,
            'type' => $this->type,
            'building' => $this->building,
            'floor' => $this->floor,
            'apartment' => $this->apartment,
            'information' => $this->information,
            'city_of_residence' => $this->city_of_residence,
            'city'=> new CityResource($this->whenLoaded('city')),
            'user_id' => $this->user_id,
        ];
    }
}
