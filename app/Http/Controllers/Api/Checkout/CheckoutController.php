<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Voucher;
use App\Models\CartItem;
use App\Models\Shipping;
use App\Models\OrderItem;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            return $this->validationErrorResponse('home.cart_empty');
        }

        $order = null;

        DB::transaction(function () use ($request, $cart, &$order) {
            // Create the order
            $order = Order::create([
                'total' => $cart->cartItems->sum(fn ($item) => $item->quantity * $item->price),
                'status' => 'pending',
                'type' => $request->order_type,
                'user_id' => auth()->id(),
            ]);

            //  Create the order details
            OrderDetail::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $this->user->email,
                'address' => $request->address,
                'address_type' => $request->address_type,
                'order_type' => $request->order_type,
                'user_id' => $this->user->id,
                'order_id' => $order->id,
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
        });

        return $this->successResponse(
            'home.order_success',[
            'order' => new CartResource($order),
            ]
        );
    }
    public function applyVoucher(Request $request, $orderId)
    {
        // Validate the request
        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $voucherCode = $request->voucher_code;
        $order = Order::findOrFail($orderId);
        $orderDetail = OrderDetail::where('order_id', $orderId)->first();
        $shipping = Shipping::find($order->shipping_id); // Assuming `shipping_id` is saved in Order

        // Check if the voucher exists
        $voucher = Voucher::where('code', $voucherCode)->first();
        if (!$voucher) {
            return $this->notFoundResponse('home.voucher_not_found');
        }

        // Check voucher validity
        $currentDate = Carbon::now();
        if ($voucher->start_date && $voucher->start_date > $currentDate) {
            return $this->notFoundResponse('home.voucher_not_valid');
        }
        if ($voucher->end_date && $voucher->end_date < $currentDate) {
            return $this->notFoundResponse('home.voucher_expired');
        }
        if ($voucher->user_limit && $voucher->times_used >= $voucher->user_limit) {
            return $this->notFoundResponse('home.voucher_limit_reached');
        }

        // Calculate the shipping fee if order type is delivery
        $shippingFee = 0;
        if ($order->type === 'delivery') {
            $shipping = Shipping::where('name', $orderDetail->address)->first();
            if (!$shipping) {
                return $this->notFoundResponse('home.shipping_not_found');
            }
            $shippingFee = $shipping->price;
        }

        // Calculate the total discount
        $orderTotal = $order->total;
        $discount = $voucher->discount_type === 'percentage'
        ? ($orderTotal * $voucher->discount) / 100
            : $voucher->discount;

        // Ensure the discount doesn't exceed the max discount
        if ($voucher->max_discount && $discount > $voucher->max_discount) {
            $discount = $voucher->max_discount;
        }

        // Apply the discount and shipping fee
        $totalAfterDiscount = $orderTotal - $discount;
        $totalAfterShipping = $totalAfterDiscount + $shippingFee;

        // Update the order total
        $order->total = $totalAfterShipping;
        $order->save();

        // Update the voucher usage
        $voucher->times_used = $voucher->times_used ? $voucher->times_used + 1 : 1;
        $voucher->save();

        return $this->successResponse('home.home_success',
            [
                'order'=> $order->id,
                'total' => $totalAfterShipping,
                'voucher_discount' => $discount,
                'shipping_fee' => $shippingFee,
            ]);
    }
    public function updatePaymentMethod(Request $request, $orderId)
    {

        $request->validate([
            'payment_method' => 'required|in:cash,card',
        ]);

        $order = Order::findOrFail($orderId);

            // Retrieve the payment ID from the payments table
            $payment = Payment::where('method', $request->payment_method)->first();
            if (!$payment) {
                return $this->notFoundResponse('home.payment_not_found');
            }
            $order->payment_id = $payment->id;
            $order->save();


        return $this->successResponse(
            'home.payment_updated',[
            'order' => new CartResource($order),]
        );
    }
}
