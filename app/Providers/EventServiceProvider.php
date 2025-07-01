<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Event::listen('*', function (string $eventName, array $data) {
            // Nếu là closure event thì bỏ qua
            if (!is_string($eventName)) {
                return;
            }

            if (!Str::startsWith($eventName, 'App\\Events\\')) {
                return; // Bỏ qua event không thuộc App\Events
            }

            $allowedEvents = [
                // App\Events\MessageSent::class,
                // App\Events\OrderCreated::class,
            ];

            if (!empty($allowedEvents) && !in_array($eventName, $allowedEvents)) {
                return;
            }

            $webhookUrl = config('reverb.servers.webhook.url');

            if (empty($webhookUrl)) {
                Log::warning('Webhook URL is not configured.');
                return;
            }

            // Gửi webhook
            try {
                dispatch(function () use ($webhookUrl, $eventName, $data) {
                    $response = Http::retry(3, 1000)->post($webhookUrl, [
                        'event' => $eventName,
                        'payload' => $data,
                    ]);

                    Log::info('Dispatching webhook', [
                        'event' => $eventName,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                })->onQueue('webhooks')->onConnection('beanstalkd');

                Log::info('Webhook job dispatched', [
                    'event' => $eventName,
                ]);
            } catch (\Exception $e) {
                Log::error('Webhook send failed: ' . $e->getMessage(), [
                    'event' => $eventName,
                    'payload' => $data,
                ]);
            }
        });
    }
}
