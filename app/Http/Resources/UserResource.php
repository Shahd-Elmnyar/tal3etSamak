<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        return [
            'id' => $this->id,
            'name' =>  $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'img'=> $this->img ? url($this->img): null,
            'date_of_birth' => $this->date_of_birth ? Carbon::parse($this->date_of_birth)->format('Y-m-d') : null,
            'gender' => $this->gender,
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
