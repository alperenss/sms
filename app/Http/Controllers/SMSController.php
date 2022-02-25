<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\SMS\SingleSMSRequest;
use App\Traits\SMS;
use App\Jobs\SendMessageJob;
use App\Models\SentMessage;

class SMSController extends Controller
{
    use SMS;

    /**
     * Send single sms function
     *
     * @param SingleSMSRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSingleSMS(SingleSMSRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            //Send sms
            $sendSMS = $this->sendSMS($user, $request->number, $request->message, $request->ip());
            //Check sms status
            if (!$sendSMS['status']) {
                throw new \Exception($sendSMS['error']['message'], 400);
            }
            //Return success response
            return response()->json(['status' => true, 'values' => ['message' => 'Message sent successfully']], 200);
        } catch (\Exception $e) {
            Log::error('SMSController/sendSingleSMS: '.$e->getMessage());
            return response()->json(['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 400);
        }
    }

    /**
     * Send multiple sms
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMultipleSMS(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            //Get message array from request
            $messages = $request->messages;
            //Check message list
            if (gettype($messages) != 'array') {
                throw new \Exception('Message list not found');
            }

            //Check message count, if message count equals or bigger than 500 we send with queue
            if (count($messages) > 500) {
                foreach ($messages as $message) {
                    if (isset($message->name) && isset($message->message)) {
                        SendMessageJob::dispatch($user, $message->number, $message->message, $request->ip());
                    }
                }
            } else {
                foreach ($messages as $message) {
                    if (isset($message->name) && isset($message->message)) {
                        $sendSMS = $this->sendSMS($user, $message->number, $message->message, $request->ip());
                    }
                }
            }
            //Return success response
            return response()->json(['status' => true, 'messages' => ['message sent']], 200);

        } catch (\Exception $e) {
            Log::error('SMSController/sendMultipleSMS: '.$e->getMessage());
            return response()->json(['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 400);
        }
    }

    /**
     * Show all user messages
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            //Get user messages
            $messages = SentMessage::where('user_id', $user->id);

            //Check filter
            if (isset($request->start_date)) {
                $messages = $messages->where('send_time', '>', $request->start_date);
            }

            if (isset($request->end_date)) {
                $messages = $messages->where('send_time', '<', $request->end_date);
            }

            $messages = $messages->get();
            //Return success response
            return response()->json(['status' => true, 'values' => $messages], 200);

        } catch (\Exception $e) {
            Log::error('SMSController/index: '.$e->getMessage());
            return response()->json(['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 400);
        }
    }

    /**
     * Show single message and message detail
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $user = auth()->user();

            $message = SentMessage::where('user_id', $user->id)->where('id', $id)->with('smsDetail')->first();

            if (!$message) {
                return response()->json(['status' => false, 'error' => ['code' => 404, 'message' => 'Message not found']], 404);
            }

            return response()->json(['status' => true, 'values' => $message], 200);

        } catch (\Exception $e) {
            Log::error('SMSController/detail: '.$e->getMessage());
            return response()->json(['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 400);
        }
    }
}
