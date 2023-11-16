<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Facades\Paystack;

class PayController extends Controller
{
    /**
     * Redirect the User to Paystack Payment Page
    //  * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        // try{
        //     return Paystack::getAuthorizationUrl()->redirectNow();
        // }catch(\Exception $e) {
        //     return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        // }   
        
        $paymentDetails = [
            'amount'       => $request->input('amount') * 100, // Paystack expects amount in kobo
            'email'        => $request->input('email'),
            'callback_url' => route('payments.callback'),
        ];

        try {
            $paymentResponse = Paystack::getAuthorizationUrl($paymentDetails);
    
            // Access the authorization URL from the response object
            $authorizationUrl = $paymentResponse->data->authorization_url;
    
            // Redirect to the authorization URL
            return redirect()->to($authorizationUrl);
        } catch (\Exception $e) {
            // Handle exceptions, log the error, or show an error message
            return back()->with('error', 'Failed to initiate payment: ' . $e->getMessage());
        }
        
    }

    /**
     * Obtain Paystack payment information
    //  * @return void
     */
    public function handleGatewayCallback(Request $request)
    {
    
       

       

// $payment= new Payment();
// $payment->email=$paymentDetails['data']['customer']['email'];
// $payment->status=$paymentDetails['data']['status'];
// $payment->amount=$paymentDetails['data']['amount'];
// $payment->trans_id=$paymentDetails['data']['id'];
// $payment->ref_id=$paymentDetails['data']['reference'];

// if ($payment->save()) {
//    return view('success');
// }
// Log::error('Failed to save payment details to the database');

// Handle Paystack callback and save payment details to the database
$paymentData = $request->all(); // Adjust this based on Paystack callback data structure

Payment::create([
    'amount'         => $paymentData['amount'] / 100, // Convert amount back to Naira
    'status'         => $paymentData['status'],
    'email'         => $paymentData['email'],
    'trans_id' => $paymentData['transaction_id'],
    'ref_id' => $paymentData['transaction_id'],
]);

return view('payment.success'); // Create a success view



}
}
