<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\PaymentIntent;
use Stripe\Transfer;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        $endpoint_secret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;

                // 🔍 Lấy metadata
                $orderId = $session->metadata->order_id ?? null;
                $vendorAccount = $session->metadata->vendor_stripe_account_id ?? null;

                Stripe::setApiKey(config('services.stripe.secret'));

                // ⚙️ Gọi lại PaymentIntent để lấy số tiền
                $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
                $amountReceived = $paymentIntent->amount_received; // đơn vị: cent
                $currency = $paymentIntent->currency;

                // ✅ Tính VAT
                $vatRate = 0.10; // 10%
                $vatAmount = intval(round($amountReceived * $vatRate));
                $netAmount = $amountReceived - $vatAmount;

                Log::info('Thanh toán hoàn tất', [
                    'session_id'     => $session->id,
                    'order_id'       => $orderId,
                    'vendor_account' => $vendorAccount,
                    'total_amount'   => $amountReceived,
                    'vat_amount'     => $vatAmount,
                    'net_amount'     => $netAmount,
                ]);

                // 💸 Chuyển tiền cho vendor (sau khi trừ VAT)
                if ($vendorAccount) {
                    $transfer = Transfer::create([
                        'amount' => $netAmount,
                        'currency' => $currency,
                        'destination' => $vendorAccount,
                        'transfer_group' => 'ORDER_' . $orderId,
                    ]);
                    Log::info('✅ Đã chuyển tiền cho người bán (đã trừ VAT)', [
                        'transfer_id' => $transfer->id,
                    ]);
                }
            }
            return response('Webhook received', 200);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook error', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        }
    }
}
