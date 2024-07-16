<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // app/Http/Resources/UserResource.php

    public function toArray($request): array
    {
        $locale = app()->getLocale();
        return [
            'id' => $this->id,
            'name' => $this->name[$locale] ?? $this->name['en'], // Default to English if locale is not found
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

}
