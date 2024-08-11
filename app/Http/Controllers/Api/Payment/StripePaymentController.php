<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\Cart;
use App\Models\Order;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\AppController;

class StripePaymentController extends AppController
{
    public function stripePost(Request $request, $orderId)
    {
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            $testToken = 'tok_visa';


            $response = $stripe->charges->create([
                'amount' => $request->amount,
                'currency' => 'egp',
                'source' => $testToken,
                'description' => $request->description,
            ]);

            $cart = Cart::where('user_id', $this->user->id)->first();
            
            $cart->cartItems()->delete();
            $cart->delete();
            $order = Order::findOrFail($orderId);
            $order->status = 'processing';
            $order->save();

            return $this->successResponse('home.payment_success', $response->status);
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
