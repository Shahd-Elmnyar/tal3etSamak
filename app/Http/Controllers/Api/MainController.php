<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MainController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function notFoundResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'errors' =>__($message),
        ], 404);
    }
    public function genericErrorResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'errors' => __($message) ,
        ], 500);
    }
    public function checkAuthorization($request){
        $user = $request->user();

        if (!$user) {
            return $this->unauthorizedResponse('auth.user_not_found');
        }
        return $user;
    }
    public function unauthorizedResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'message' => __($message),
        ], 401);
    }
    public function validationErrorResponse($message)
    {
        return response()->json([
            'code' => 'error',
            'message' => 'validation_error',
            'errors' => __($message),
        ], 403);
    }
    public function successResponse($message, $data = null)
    {
        $responseData = [
            'code' => 'success',
            'message' => __($message),
        ];

        if ($data !== null) {
            $responseData['data'] = $data;
        }

        return response()->json($responseData, 200);
    }
    public function emptyResponse($data,$message){
        if ($data->isEmpty()) {
            return $this->notFoundResponse(__($message));
        }
    }
    public function getPaginationData($products)
    {
        if ($products instanceof LengthAwarePaginator) {
            return [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ];
        }
        return [];
    }
    public function getProductById($productId)
    {
        return Product::findOrFail($productId);
    }

}
