<?php

namespace App\Http\Controllers\Api\Notification;

use Exception;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Events\ProductOfferUpdated;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AppController;
use App\Http\Resources\NotificationResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationController extends AppController
{
    public function getNotifications(): JsonResponse
    {
        try {
            $notifications = Notification::all(); // Retrieve all notifications

            return $this->successResponse(
                'home.home_success',
                NotificationResource::collection($notifications)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('home.error_retrieving_notifications');
        }
    }

    public function notifyOffer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id', // Validate input
        ]);

        try {
            $productId = $validated['product_id'];
            $product = Product::findOrFail($productId);

            // Update the product offer status
            $product->is_offer = true; // Or set based on input
            $product->save();

            $locale = Auth::user()->lang ?? config('app.fallback_locale');
            // Extract product name based on the preferred language (e.g., English or Arabic)
            $productName = $product->name[$locale] ?? 'Product Name'; // Fallback to 'Product Name' if locale is not available

            // Create a beautifully formatted notification message
            $message = sprintf(
                'Product "%s" is now on offer! Original Price: $%s, Offer Price: $%s',
                $productName,
                number_format($product->price, 2),
                number_format($product->offer_price, 2)
            );

            // Create a notification record
            Notification::create([
                'product_id' => $product->id,
                'type' => 'ProductOfferUpdated',
                'data' => $message, // Save the formatted message
            ]);

            // Trigger the event
            event(new ProductOfferUpdated($product));

            return $this->successResponse('home.Notification_sent');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (Exception $e) {
            Log::error('HomeController error: ' . $e->getMessage());
            return $this->genericErrorResponse(__('auth.error_occurred'), ['error' => $e->getMessage()]);
        }
    }
}
