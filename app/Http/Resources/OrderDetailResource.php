<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function transformData(array $data): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'address_type' => $this->address_type,
            'order_type' => $this->order_type,
            'payment_id' => $this->payment_id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'address_id' => $this->address_id,
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
