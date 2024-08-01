<?php

namespace App\Http\Controllers\Api\Order;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\Api\AppController;
use App\Http\Resources\OrderDetailResource;

class OrderController extends AppController
{
public function index()
    {
        $orders = Order::where('user_id', $this->user->id)->paginate(6);

        return $this->successResponse(
            'home.home_success',
            ['orders' => OrderResource::collection($orders),
            'pagination'=> $this->getPaginationData($orders),]
        );
    }
    public function show($id)
    {
        $orderDetail = OrderDetail::with('order.orderItems')->where('order_id', $id)->first();

        if (!$orderDetail) {
            return $this->notFoundResponse('home.order_not_found', 404);
        }

        return $this->successResponse(
            'home.home_success',
            ['order_detail' => new OrderDetailResource($orderDetail)]
        );
    }
}
