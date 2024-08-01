<?php

namespace App\Http\Controllers\Api\Payment;

use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\AppController;

class StripePaymentController extends AppController
{
    public function stripePost(Request $request)
    {
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            // Use a test token instead of card details
            $testToken = 'tok_visa'; // This is a predefined test token

            // Use the token to create a charge
            $response = $stripe->charges->create([
                'amount' => $request->amount,
                'currency' => 'egp',
                'source' => $testToken,
                'description' => $request->description,
            ]);

            return $this->successResponse('home.payment_success', $response->status);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Stripe Payment Error: ' . $e->getMessage());

            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }
}
