<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
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
    public function genericErrorResponse($message=null)
    {
        if ($message) {
            return response()->json([
                'code' => 'error',
                'errors' => __($message),
            ], 500);
        }
        return response()->json([
            'code' => 'error',
            'errors' => __('auth.error_occurred'),
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
        // Attempt to process the error message
        if (is_array($message)) {

            // Check if 'errors' key exists in the array
            if (isset($message['errors'])) {
                $errors = $message['errors'];
            } else {
                throw new \Exception('The array does not contain an "errors" key.');
            }
        } else {
            // Handle the non-array message
            $errors = __($message);
        }
        return response()->json([
            'code' => 'error',
            'message' => 'validation_error',
            'errors' => $errors,
        ], 422);
    }


    public function successResponse($message=null, $data = null)
    {
        if ($message ) {
            $responseData = [
                'code' => 'success',
                'message' => __($message),
            ];
        }else{
            $responseData = [
                'code' => 'success',
                'message' => __('home.home_success'),
            ];
        }
        if ($data !== null) {
            $responseData['data'] = $data;
        }

        return response()->json($responseData, 200);
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
}
