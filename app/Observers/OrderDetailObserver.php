<?php

namespace App\Observers;

use App\Models\OrderDetail;

class OrderDetailObserver
{
    public function created(OrderDetail $orderDetail)
    {
        // Handle the event when an order detail is created
    }

    public function updated(OrderDetail $orderDetail)
    {
        // Handle the event when an order detail is updated
    }

    public function deleted(OrderDetail $orderDetail)
    {
        // Handle the event when an order detail is deleted
    }
}
