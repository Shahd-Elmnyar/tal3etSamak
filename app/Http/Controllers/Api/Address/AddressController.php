<?php

namespace App\Http\Controllers\Api\Address;

use App\Models\Address;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Api\AppController;
use App\Http\Requests\Address\AddressRequest;
use App\Http\Resources\Address\AddressResource;

class AddressController extends AppController
{
    public function store(AddressRequest $request)
    {
        try {
            $addressData = $request->validated();
            $addressData['user_id'] = $this->user->id; // Ensure user_id is set
            $address = Address::create($addressData);
            $addressData = new AddressResource($address);
            return $this->successResponse('home.address_created', $addressData);
        } catch (QueryException $e) {
            return $this->genericErrorResponse('auth.database_error', ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }
}
