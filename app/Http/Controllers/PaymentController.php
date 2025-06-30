<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use App\Services\PayPalService;

class PaymentController extends Controller
{
    public function showPaymentPage()
    {
        return view('payment');
    }

    public function stripeCheckout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $orderId = $request->input('order_id', 123); // fallback 123 nếu không truyền
        $amount = $request->input('amount') * 100; // Chuyển sang cent
        $vendorStripeAccountId = 'acct_1ReUaFRYoAL7Qddu'; // fallback nếu không truyền

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Giày sneaker mới'],
                    'unit_amount' => $amount,                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
            'metadata' => [
                'order_id' => $orderId,
                'vendor_stripe_account_id' => $vendorStripeAccountId,
            ],
        ]);

        return redirect($session->url);
    }

    public function paypalCheckout(Request $request, PayPalService $paypal)
    {
        $amount = $request->input('amount'); // fallback 20 nếu không truyền
        $orderId = $request->input('order_id'); // fallback 123 nếu không truyền
        $vendorEmail = $request->input('vendor_email', 'sb-vihw044180697@business.example.com'); // fallback nếu không truyền

        $response = $paypal->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "USD",
                    "value" => number_format($amount, 2, '.', ''), // Đảm bảo định dạng đúng
                ],
                'custom_id' => json_encode([
                    'order_id' => $orderId,
                    'vendor_email' => $vendorEmail
                ])
            ]],
            "application_context" => [
                "return_url" => route('paypal.success'),
                "cancel_url" => route('paypal.cancel')
            ]
        ]);
        Log::info('PayPal create order response: ' . json_encode($response, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));

        if (isset($response['id']) && $response['status'] === 'CREATED') {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->back()->with('error', 'Không tạo được đơn PayPal');
    }

    public function paypalSuccess(Request $request)
    {
        $orderId = $request->query('token');

        if ($orderId) {
            return redirect()->route('payment.success')->with('success', 'Thanh toán thành công');
        }

        return redirect()->route('payment.failed')->with('error', 'Thanh toán không thành công');
    }
    public function paypalCancel()
    {
        return redirect()->route('payment.cancel')->with('error', 'Bạn đã hủy thanh toán.');
    }

    public function vnpayCheckout(Request $request)
    {
        // Lấy thông tin config: 
        $vnp_TmnCode = config('services.vnpay.vnp_TmnCode'); // Mã website của bạn tại VNPAY 
        $vnp_HashSecret = config('services.vnpay.vnp_HashSecret'); // Chuỗi bí mật
        $vnp_Url = config('services.vnpay.vnp_Url'); // URL thanh toán của VNPAY
        $vnp_ReturnUrl = config('services.vnpay.vnp_Returnurl'); // URL nhận kết quả trả về

        // Lấy thông tin từ đơn hàng phục vụ thanh toán 
        $order = (object)[
            "code" => 'ORDER' . rand(100000, 999999),  // Mã đơn hàng
            "total" => $request->input('amount', 10000), // Số tiền cần thanh toán (VND)
            "bankCode" => 'NCB',   // Mã ngân hàng
            "type" => "billpayment", // Loại đơn hàng
            "info" => "Thanh toán đơn hàng" // Thông tin đơn hàng
        ];
        $vnp_IpAddr = $request->ip();
        if ($vnp_IpAddr === '127.0.0.1') {
            $vnp_IpAddr = '14.248.71.116'; // Hoặc thay bằng IP thật
        }
        
        // Thông tin đơn hàng, thanh toán
        $vnp_TxnRef = $order->code;
        $vnp_OrderInfo = $order->info;
        $vnp_OrderType =  $order->type;
        $vnp_Amount = $order->total * 100; 
        $vnp_Locale = 'vn';
        $vnp_BankCode = $order->bankCode;  // Mã ngân hàng

        // Tạo input data để gửi sang VNPay server
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );
        // Kiểm tra nếu mã ngân hàng đã được thiết lập và không rỗng
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        // Kiểm tra nếu thông tin tỉnh/thành phố hóa đơn đã được thiết lập và không rỗng
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State; // Gán thông tin tỉnh/thành phố hóa đơn vào mảng dữ liệu input
        }

        // Sắp xếp mảng dữ liệu input theo thứ tự bảng chữ cái của key
        ksort($inputData);
        
        $query = ""; // Biến lưu trữ chuỗi truy vấn (query string)
        $i = 0; // Biến đếm để kiểm tra lần đầu tiên
        $hashdata = ""; // Biến lưu trữ dữ liệu để tạo mã băm (hash data)

        // Duyệt qua từng phần tử trong mảng dữ liệu input
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                // Nếu không phải lần đầu tiên, thêm ký tự '&' trước mỗi cặp key=value
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                // Nếu là lần đầu tiên, không thêm ký tự '&'
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1; // Đánh dấu đã qua lần đầu tiên
            }
            // Xây dựng chuỗi truy vấn
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
            
        // Gán chuỗi truy vấn vào URL của VNPay
        $vnp_Url = $vnp_Url . "?" . $query;

        // Kiểm tra nếu chuỗi bí mật hash secret đã được thiết lập
        if (isset($vnp_HashSecret)) {
            // Tạo mã băm bảo mật (Secure Hash) bằng cách sử dụng thuật toán SHA-512 với hash secret
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            // Thêm mã băm bảo mật vào URL để đảm bảo tính toàn vẹn của dữ liệu
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        return redirect($vnp_Url);
    }

     public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->all();

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, config('services.vnpay.vnp_HashSecret'));

        if ($secureHash === $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // Thanh toán thành công
                return view('payment-success', compact('inputData'));
            } else {
                // Thanh toán không thành công
                return view('payment-failed');
            }
        } else {
            // Dữ liệu không hợp lệ
            return view('payment-failed');
        }
    }

    public function success()
    {
        return view('payment-success');
    }

    public function cancel()
    {
        return view('payment-cancel');
    }

    public function failed()
    {
        return view('payment-failed');
    }
}
