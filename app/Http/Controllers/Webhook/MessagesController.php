<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\BankMessageService;
use App\Jobs\ProcessBankMessages;

class MessagesController extends Controller
{
    protected $bankMessageService;

    public function __construct(BankMessageService $bankMessageService)
    {
        $this->bankMessageService = $bankMessageService;
    }

    /**
     * دریافت و ذخیره‌سازی پیام بانکی
     */
    public function get(Request $request)
    {
        try {
            // ۱. پارس کردن پیام خام
            $parsedData = $this->bankMessageService->parseRawMessage($request->message);

            $bankMessage = $this->bankMessageService->storeFromArray($parsedData);


            return response()->json([
                'success' => true,
                'message' => 'پیام با موفقیت ذخیره شد',
                'data' => $bankMessage
            ], 201);

        } catch (\Exception $e) {
            Log::error("خطا در ذخیره‌سازی پیام بانکی: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
