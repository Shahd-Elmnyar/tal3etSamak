<?php

namespace App\Http\Controllers\Api\Contact;

use Exception;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\AppController;
use Illuminate\Validation\ValidationException;

class ContactController extends AppController
{
    public function store(Request $request){
        try{
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
        ]);
        Message::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $this->user->id
        ]);

        return $this->successResponse('home.contact_success');
        }catch (ValidationException $e){
            Log::error('Validation error: ', ['errors' => $e->errors()]);
            return $this->validationErrorResponse(['errors' => $e->errors()]);
        }catch(Exception $e){
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
