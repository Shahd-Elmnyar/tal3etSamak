<?php

namespace App\Http\Controllers\Api\Address;

use App\Models\Address;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\AddressResource;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Api\AppController;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\Address\AddressRequest;

class AddressController extends AppController
{
    public function index()
    {
        $address = Address::where('user_id', $this->user->id)->get();
        if (!$address) {
            return $this->successResponse('home.home_success', [
                'address' => []
            ]);
        } else {
            return $this->successResponse('home.home_success', [
                'address' => $address
            ]);
        }
    }
    public function store(AddressRequest $request)
    {
        try {
            $addressData = $request->validated();
            $addressData['user_id'] = $this->user->id; 
            $address = Address::create($addressData);


            $addressData = new AddressResource($address);

            return $this->successResponse('home.address_created', $addressData);
        } catch (QueryException $e) {
            return $this->genericErrorResponse('auth.database_error', ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }
    public function update(UpdateAddressRequest $request, $id)
    {
        try {
            $address = Address::where('user_id', $this->user->id)->findOrFail($id);
            $addressData = [];
            foreach ($request->all() as $key => $value) {
                if ($request->filled($key)) {
                    $addressData[$key] = $value;
                }
            }
            $address->update($addressData);
            return $this->successResponse('home.address_updated', new AddressResource($address));

        } catch (QueryException $e) {

            Log::error('Database Error during Address update: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse('auth.database_error', ['error' => $e->getMessage()]);

        } catch (\Exception $e) {

            Log::error('General Error during Address update: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {

            $address = Address::where('user_id', $this->user->id)->findOrFail($id);
            $address->delete();
            return $this->successResponse('home.address_deleted');

        } catch (QueryException $e) {

            Log::error('Database Error during Address Deletion: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse('auth.database_error', ['error' => $e->getMessage()]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            Log::error('Address Not Found: ', ['error' => $e->getMessage()]);
            return $this->notFoundResponse('home.address_not_found');

        } catch (\Exception $e) {

            Log::error('General Error during Address Deletion: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse('auth.error_occurred',['error' => $e->getMessage()]);
        }
    }
}
