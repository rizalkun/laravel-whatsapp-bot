<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Zuwinda;

class WebhookController extends Controller
{
    //
    public function ReceiveWebhook(Request $request, Zuwinda $zuwinda)
    {
        try {
            $secret_key = env('ZUWINDA_WEBHOOK_KEY', 'Your webhook secret key');
            $post_data = file_get_contents('php://input');
            $json = json_decode($post_data);
            $signature = hash_hmac('sha256', json_encode($json->data), $secret_key);
            if ($request->header('X-Zuwinda-Signature') == $signature) {
                $data = $json->data;
                if ($data->event == "MESSAGE_RECEIVED") {
                    if ($data->to == env('ZUWINDA_SENDER_NUMBER', 'Your Sender number')) {
                        if ($data->content == "!halo") {
                            $zuwinda->sendMessage($data->instances_id, $data->from, 'Halo zuwinda 🥳');
                        } else if ($data->content == "!bye") {
                            $zuwinda->sendMessage($data->instances_id, $data->from, 'Selamat tinggal 🥺');
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            error_log($th);
        }
    }
}
