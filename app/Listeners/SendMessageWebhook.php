<?php

namespace App\Listeners;

use App\Events\MessageSent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessageWebhook
{
    // public function handle(MessageSent $event)
    // {
    //     $response = Http::post(config('reverb.servers.webhook.url'), [
    //         'event' => 'message_sent',
    //         'payload' => [
    //             'message' => $event->message['content'],
    //             'sender_id' => $event->message['sender_id'],
    //             'receiver_id' => $event->message['receiver_id'],
    //             'sent_at' => now(),
    //         ],
    //     ]);

    //     Log::info('Webhook sent', [
    //         'status' => $response->status(),
    //         'body' => $response->body(),
    //     ]);
    // }
}
