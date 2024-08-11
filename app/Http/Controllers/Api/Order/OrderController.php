<?php

namespace App\Http\Controllers\Api\Order;

use Exception;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\Api\AppController;
use App\Http\Resources\OrderDetailResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends AppController
{
    public function index()
    {
        try {
            $orders = Order::where('user_id', $this->user->id)->paginate(6);

            return $this->successResponse(
                'null',
                [
                    'orders' => OrderResource::collection($orders),
                    'pagination' => $this->getPaginationData($orders),
                ]
            );
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    public function show($id)
    {
        try {
            $orderDetail = OrderDetail::with('order.orderItems')->where('order_id', $id)->first();
            if(!$orderDetail){
                return $this->notFoundResponse('home.order_not_found');
            }

            return $this->successResponse(
                'null',
                ['order_detail' => new OrderDetailResource($orderDetail)]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.order_not_found');
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
