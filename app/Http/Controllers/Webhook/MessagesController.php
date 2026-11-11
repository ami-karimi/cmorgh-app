<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBankMessages;
use App\Services\BankMessageService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessagesController extends Controller
{
    public function __construct(protected BankMessageService $bankMessageService) {}

    public function get(Request $request)
    {
        try {
            $data = $request->validate(['message' => 'required|string|max:10000']);
            $message = $this->bankMessageService->storeFromRawMessage($data['message']);
            // afterResponse also works with the existing sync queue without delaying SMS acknowledgement.
            ProcessBankMessages::dispatchAfterResponse();
            return response()->json(['success' => true, 'message' => 'پیام با موفقیت ذخیره شد', 'data' => ['id' => $message->id]], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'پیام نامعتبر است.', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            if ($e->getCode() === 409) {
                return response()->json(['success' => false, 'message' => 'این پیام قبلاً ثبت شده است.'], 409);
            }
            report($e);
            return response()->json(['success' => false, 'message' => 'ذخیره پیام انجام نشد.'], 500);
        }
    }
}
