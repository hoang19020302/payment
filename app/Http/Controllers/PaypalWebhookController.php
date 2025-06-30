<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PayPalService;

class PaypalWebhookController extends Controller
{
    public function handleWebhook(Request $request, PayPalService $paypal)
    {
        try {
            $payload = $request->all();
            Log::info('📩 PayPal Webhook Received: ' . json_encode($payload, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));

            // ✅ Xác thực chữ ký
            if (!$paypal->verifyWebhookSignature($request->headers->all(), $payload)) {
                Log::warning('❌ Invalid webhook signature');
                return response('Invalid signature', 400);
            }

            if (($payload['event_type'] ?? '') === 'CHECKOUT.ORDER.APPROVED') {
                $resource = $payload['resource'] ?? [];
                $purchaseUnit = $resource['purchase_units'][0] ?? [];
                $customData = json_decode($purchaseUnit['custom_id'] ?? '{}', true);

                $orderId = $customData['order_id'] ?? null;
                $vendorEmail = $customData['vendor_email'] ?? null;
                $amount = $purchaseUnit['amount']['value'] ?? '0.00';

                Log::info('✅ Đơn PayPal approved', [
                    'paypal_order_id' => $resource['id'] ?? null,
                    'app_order_id'    => $orderId,
                    'vendor_email'    => $vendorEmail,
                    'amount'          => $amount,
                ]);

                // 💸 Payout
                if ($vendorEmail) {
                    $vat = 0.10;
                    $vatAmount = round(floatval($amount) * $vat, 2);
                    $netAmount = round(floatval($amount) - $vatAmount, 2);

                    $payout = $paypal->createPayout([
                        'sender_batch_header' => [
                            'sender_batch_id' => uniqid(),
                            'email_subject'   => 'Bạn vừa nhận được thanh toán từ hệ thống',
                        ],
                        'items' => [[
                            'recipient_type' => 'EMAIL',
                            'amount' => [
                                'value'    => number_format($netAmount, 2, '.', ''),
                                'currency' => 'USD'
                            ],
                            'receiver' => $vendorEmail,
                            'note'     => 'Thanh toán đơn hàng #' . ($orderId ?? 'N/A')
                        ]]
                    ]);

                    Log::info('✅ Đã payout PayPal', [
                        'original_amount' => $amount,
                        'vat'             => $vatAmount,
                        'net_amount'      => $netAmount,
                        'vendor_email'    => $vendorEmail,
                        'payout_response' => $payout
                    ]);

                }
                return response('Webhook processed', 200);
            }
            return response('Webhook received', 200);
        } catch (\Exception $e) {
            Log::error('❌ Error processing webhook: ' . $e->getMessage());
            return response('Error processing webhook', 500);
        }        
    }
}
