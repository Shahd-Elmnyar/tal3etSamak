<?php

namespace App\Http\Controllers\Api\Notification;

use Exception;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Events\ProductOfferUpdated;
use Illuminate\Support\Facades\Log;
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
            $notifications = Notification::all();
            return $this->successResponse(
                'null',
                NotificationResource::collection($notifications)
            );
        } catch (\Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse('home.notification_failed');
        }
    }


public function notifyOffer(Request $request, Notification $notificationService): JsonResponse
{
    $validated = $request->validate([
        'product_id' => 'required|integer|exists:products,id',
    ]);

    try {
        $productId = $validated['product_id'];
        $product = $this->getProductById($productId);


        $product->is_offer = true;
        $product->save();

        $locale = Auth::user()->lang ?? config('app.fallback_locale');

        $productName = $this->getProductNameByLocale($product, $locale);


        $message = $this->formatOfferMessage($productName, $product->price, $product->offer_price);


        Notification::create([
                'product_id' => $product->id,
                'type' => 'ProductOfferUpdated',
                'data' => $message
        ]);


        event(new ProductOfferUpdated($product));

        return $this->successResponse('home.Notification_sent');
    } catch (ModelNotFoundException $e) {
        return $this->notFoundResponse('home.product_not_found');
    } catch (ValidationException $e) {
        return $this->validationErrorResponse(['errors' => $e->errors()]);
    } catch (Exception $e) {
        Log::error('General error : ' . $e->getMessage());
        return $this->genericErrorResponse();
    }
}

private function getProductNameByLocale(Product $product, string $locale): string
{
    return $product->name[$locale] ?? __('Product Name');
}

private function formatOfferMessage(string $productName, float $originalPrice, float $offerPrice): string
{
    return sprintf(
        __('Product "%s" is now on offer! Original Price: $%s, Offer Price: $%s'),
        $productName,
        number_format($originalPrice, 2),
        number_format($offerPrice, 2)
    );
}

}
