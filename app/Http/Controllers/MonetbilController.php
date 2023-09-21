<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\Order;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Str;

class MonetbilController extends Controller
{
    public function payment(Request $request)
    {
        $order = Order::where('id', $request->order_id)->first();
        if (!$order) {
            Toastr::warning(translate('messages.order_not_found'));
            return back();
        }
        $reference = Str::random(5) . $order->id;
        $order->transaction_reference = $reference;
        $order->order_status = 'failed';
        $order->save();
        $config = Helpers::get_business_settings('monetbil');

        $service_key = $config['service_key'];

        $data = array(
            'amount' => $order->order_amount,
            'currency' => 'XAF',
            'payment_ref' => $reference,
            'user' => $order->customer->id,
            'notify_url' => route('monetbil-notification'),
            'return_url' => route('monetbil-callback')
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.monetbil.com/widget/v2.1/' . $service_key);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = json_decode($json, true);

        if (!isset($response['success']) && $response['success'] == false) {
            Toastr::warning(translate('messages.payment_not_found_or_not accepted'));
            return back();
        }
        return redirect()->to($response['payment_url']);
    }

    public function notification(Request $request)
    {
        info('monetbil notification');
        info($request->all());
        $order = Order::where('transaction_reference', $request['payment_ref'])->first();
        if (isset($order) && $request['status'] == 'success') {
            $order->payment_status = 'paid';
            $order->order_status = 'confirmed';
            $order->payment_method = 'monetbil';
            $order->transaction_reference = $request['transaction_id'];
            $order->confirmed = now();
            Helpers::send_order_notification($order);
        }
        else{
            $order->order_status = 'failed';
            $order->failed = now();
        }
        $order->save();
    }

    public function callback(Request $request)
    {
        $order = Order::with(['details'])->where('transaction_reference',$request['transaction_id'])->first();
        if (!$order) {
            return \redirect()->route('payment-fail');
        }
        $maximumAttempts = 5;
        $attempt = 1;
        while ($attempt <= $maximumAttempts) {
            if ($request['status'] == 'success' && $order->payment_status == 'paid') {
                if ($order->callback != null) {
                    return redirect($order->callback . '&status=success');
                } else {
                    return redirect()->route('payment-success');
                }
            } else {
                sleep(2);
                $attempt++;
            }
        }
        if (isset($order) && $order->callback != null) {
            return redirect($order->callback . '&status=fail');
        } else {
            return \redirect()->route('payment-fail');
        }
    }
}
