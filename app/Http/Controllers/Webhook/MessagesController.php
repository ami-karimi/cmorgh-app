<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\BankMessageService;

class MessagesController extends Controller
{
    public function get(Request  $request){
        try {
            $service = new BankMessageService();
            $bankMessage = $service->storeFromArray($request->message);

            return response()->json([
                'success' => true,
                'message' => 'پیام با موفقیت ذخیره شد',
                'data' => $bankMessage
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
