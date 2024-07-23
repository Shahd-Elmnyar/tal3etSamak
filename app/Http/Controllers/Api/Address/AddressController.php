<?php

namespace App\Http\Controllers\Api\Address;

use App\Models\Address;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Api\MainController;
use App\Http\Requests\Address\AddressRequest;
use App\Http\Resources\Address\AddressResource;


class AddressController  extends MainController
{
    public function store(AddressRequest $request)
    {
        $user = $this->checkAuthorization($request);
        try {
            $addressData = $request->validated();
            $addressData['user_id'] = $user->id; // Ensure user_id is set
            $address = Address::create($addressData);
            $addressData = new AddressResource($address);
            return $this->successResponse(__('home.address_created'), $addressData);
        } catch (QueryException $e) {
            return $this->genericErrorResponse(__('auth.database_error', ['error' => $e->getMessage()]));
        } catch (\Exception $e) {
            return $this->genericErrorResponse(__('auth.error_occurred', ['error' => $e->getMessage()]));
        }
    }
}
