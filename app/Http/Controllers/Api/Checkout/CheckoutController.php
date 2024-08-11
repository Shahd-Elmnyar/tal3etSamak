<?php

namespace App\Http\Controllers\Api\Checkout;

use Exception;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Voucher;
use App\Models\Shipping;
use App\Models\OrderItem;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CartResource;
use App\Http\Requests\CheckoutRequest;
use App\Http\Controllers\Api\AppController;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CheckoutController extends AppController
{
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        Log::info('Checkout request received', $request->all());
        $cart = Cart::where('user_id', auth()->id())->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return $this->validationErrorResponse('home.cart_empty');
        }

        try {
            $order = DB::transaction(function () use ($request, $cart) {
                return $this->createOrder($request, $cart);
            });

            return $this->successResponse(
                __('home.order_success'),
                ['order' => new CartResource($order)]
            );
        } catch (Exception $e) {
            Log::error('CheckoutController error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }


    public function applyVoucher(Request $request, int $orderId): JsonResponse
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string',
        ]);

        try {
            $voucher = Voucher::where('code', $validated['voucher_code'])->firstOrFail();
            $order = Order::findOrFail($orderId);
            $orderDetail = OrderDetail::where('order_id', $orderId)->firstOrFail();

            $this->validateVoucher($voucher);

            $shippingFee = $this->calculateShippingFee($order, $orderDetail);

            $totalAfterDiscount = $this->applyDiscount($order, $voucher);
            $totalAfterShipping = $totalAfterDiscount + $shippingFee;

            $order->update(['total' => $totalAfterShipping]);

            $voucher->increment('times_used');

            return $this->successResponse(
                null,
                [
                    'order' => $order->id,
                    'total' => $totalAfterShipping,
                    'voucher_discount' => $totalAfterDiscount,
                    'shipping_fee' => $shippingFee,
                ]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('home.voucher_not_found'));
        }catch(ValidationException $e) {

            return $this->validationErrorResponse($e->getMessage());
        } catch (Exception $e) {
            Log::error('CheckoutController error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    public function updatePaymentMethod(Request $request, int $orderId): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card',
        ]);

        try {
            $order = Order::findOrFail($orderId);
            $payment = Payment::where('method', $validated['payment_method'])->firstOrFail();

            $order->update(['payment_id' => $payment->id]);

            return $this->successResponse(
                __('home.payment_updated'),
                ['order' => new CartResource($order)]
            );

        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('home.payment_not_found'));
        } catch (Exception $e) {
            Log::error('CheckoutController error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    private function createOrder(CheckoutRequest $request, Cart $cart): Order
    {
        $order = Order::create([
            'total' => $cart->cartItems->sum(fn($item) => $item->quantity * $item->price),
            'status' => 'pending',
            'type' => $request->order_type,
            'user_id' => auth()->id(),
        ]);

        OrderDetail::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => auth()->user()->email,
            'address' => $request->address,
            'address_type' => $request->address_type,
            'order_type' => $request->order_type,
            'user_id' => auth()->id(),
            'order_id' => $order->id,
        ]);

        foreach ($cart->cartItems as $item) {
            OrderItem::create([
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->quantity * $item->price,
                'product_id' => $item->product_id,
                'order_id' => $order->id,
                'size_id' => $item->size_id,
            ]);
        }

        return $order;
    }

    private function validateVoucher(Voucher $voucher): void
    {
        $currentDate = Carbon::now();

        if ($voucher->start_date && $voucher->start_date > $currentDate) {
            throw new ValidationException(__('home.voucher_not_valid'));
        }

        if ($voucher->end_date && $voucher->end_date < $currentDate) {
            throw new ValidationException(__('home.voucher_expired'));
        }

        if ($voucher->user_limit && $voucher->times_used >= $voucher->user_limit) {
            throw new ValidationException(__('home.voucher_limit_reached'));
        }
    }

    private function calculateShippingFee(Order $order, OrderDetail $orderDetail): float
    {
        if ($order->type === 'delivery') {
            $shipping = Shipping::where('name', $orderDetail->address)->first();
            if (!$shipping) {
                throw new \Exception(__('home.shipping_not_found'));
            }
            return $shipping->price;
        }

        return 0;
    }


    private function applyDiscount(Order $order, Voucher $voucher): float
    {
        $orderTotal = $order->total;

        $discount = $voucher->discount_type === 'percentage'
            ? ($orderTotal * $voucher->discount) / 100
            : $voucher->discount;

        return min($discount, $voucher->max_discount ?? $discount);
    }
}
