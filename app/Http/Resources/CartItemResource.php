<?php

namespace App\Http\Resources;

class CartItemResource extends MainResource
{
    protected function transformData(array $data): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'cart_id' => $this->cart_id,
            'size_id' => $this->size_id,
            'addition_quantity' => $this->addition_quantity,
            'total_addition_price' => $this->total_addition_price,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }

}
