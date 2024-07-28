<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class UserResource extends MainResource
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
            'name' => is_array($this->name) ? ($this->name[$locale] ?? $this->name['en']) : $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
