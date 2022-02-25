<?php

namespace App\Traits;

use App\Models\SentMessage;
use App\Models\SentMessageDetail;
use App\Models\User;
use Carbon\Carbon;

trait SMS
{
    /**
     * This function make request and add database for sms operations
     *
     * @param User $user
     * @param $number
     * @param $message
     * @param $ip
     * @return array|bool[]
     */
    public function sendSMS(User $user, $number, $message, $ip)
    {
        try {
            /*
            http request is sent to the provider company
            we return example http response from provider company
            */
            $provider_response = ['status' => true, 'message' => 'message sent successfully'];

            if (!$provider_response['status']) {
                $sent_message = SentMessage::create([
                    'user_id' => $user->id,
                    'number' => $number,
                    'message' => $message,
                    'success' => '0',
                    'send_time' => Carbon::now()
                ]);

                $detail = SentMessageDetail::create([
                    'sent_message_id' => $sent_message->id,
                    'title' => 'single sms error',
                    'detail' => $provider_response['message'],
                    'ip' => $ip,
                    'time' => Carbon::now()
                ]);

                throw new \Exception($provider_response['message'], 400);
            }

            $sent_message = SentMessage::create([
                'user_id' => $user->id,
                'number' => $number,
                'message' => $message,
                'success' => '1',
                'send_time' => Carbon::now()
            ]);

            $detail = SentMessageDetail::create([
                'sent_message_id' => $sent_message->id,
                'title' => 'Message sent',
                'detail' => $provider_response['message'],
                'ip' => $ip,
                'time' => Carbon::now()
            ]);

            return ['status' => true];

        } catch (\Exception $e) {
            return ['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]];
        }
    }
}
