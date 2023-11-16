<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
// use Unicodeveloper\Paystack\Paystack;
use Paystack;

class PayController extends Controller
{
    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }   
        
        
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        // dd($paymentDetails);
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
        
    //    ['amount','email','status','trans_id','ref_id'];
   

$payment= new Payment();
$payment->email=$paymentDetails['data']['customer']['email'];
$payment->status=$paymentDetails['data']['status'];
$payment->amount=$paymentDetails['data']['amount'];
$payment->trans_id=$paymentDetails['data']['id'];
$payment->ref_id=$paymentDetails['data']['reference'];

if ($payment->save()) {
   return view('success');
}
Log::error('Failed to save payment details to the database');
}
}
