<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CartResource;
use App\Http\Resources\OrderResource;
use App\Http\Requests\CheckoutRequest;
use App\Http\Controllers\Api\AppController;

class CheckoutController extends AppController
{
    public function checkout(CheckoutRequest $request)
    {
        Log::info('Checkout request received', $request->all());
        $cart = Cart::where('user_id', $this->user->id)->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $order = null;

        DB::transaction(function () use ($request, $cart, &$order) {
            // Create the order
            $order = Order::create([
                'total' => $cart->cartItems->sum(fn ($item) => $item->quantity * $item->price),
                'status' => 'pending',
                'type' => $request->order_type,
                'user_id' => auth()->id(),
                'payment_id' => $request->payment_id,
            ]);

            // Create the order details
            OrderDetail::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $this->user->email,
                'address' => $request->address,
                'address_type' => $request->address_type,
                'order_type' => $request->order_type,
                'user_id' => $this->user->id,
                'order_id' => $order->id,
                'payment_id' => $request->payment_id,
            ]);

            foreach ($cart->cartItems as $item) {
                $orderItem = OrderItem::create([
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                    'product_id' => $item->product_id,
                    'order_id' => $order->id,
                    'size_id' => $item->size_id,
                ]);
            }

            // Clear the cart
            $cart->cartItems()->delete();
            $cart->delete();
        });

        return $this->successResponse(
            'home.order_success',[
            'order' => new CartResource($order),
            ]
        );
    }
    public function updatePaymentMethod(Request $request, $orderId)
    {
        // Log the request data for debugging
        // Log::info('Update payment method request received');
        // dd($request->all());

        $request->validate([
            'payment_method' => 'required|in:cash,card',
        ]);

        $order = Order::findOrFail($orderId);


            // Retrieve the payment ID from the payments table
            $payment = Payment::where('method', $request->payment_method)->first();
            if (!$payment) {
                return response()->json(['message' => 'Payment method not found'], 404);
            }
            $order->payment_id = $payment->id;
            $order->save();


        return $this->successResponse(
            'home.payment_updated',[
            'order' => new CartResource($order),]
        );
    }
}
