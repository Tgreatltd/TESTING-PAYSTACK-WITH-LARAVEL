<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Exceptions\PaymentVerificationFailedException;
use Unicodeveloper\Paystack\Facades\Paystack;

class PayController extends Controller
{
    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        $data =$request->validate([
            'email'=> 'required|string|email|unique:users',
            'amount'=> 'required|string',
            'trans_id'=> 'required|string',
            'ref_id'=> 'required|string',
            'status'=> 'required|string',
        ]);

        Payment::create($data);

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
    public function handleGatewayCallback(Request $request)
    {
        try {
            $paymentDetails = Paystack::getPaymentData();
    
            // Log the payment details for debugging
            Log::info('Paystack Payment Details:', $paymentDetails);
    
            // Update the database based on payment status
            $this->updatePaymentStatus($paymentDetails);
    
            // Handle different payment statuses
            if ($paymentDetails->status === 'success') {
                // Payment was successful
                return redirect()->route('payment.success');
            } elseif ($paymentDetails->status === 'failed') {
                // Payment was denied or failed
                return redirect()->route('payment.failure')->withMessage(['msg' => 'Payment failed. Please try again.', 'type' => 'error']);
            } else {
                // Handle other statuses as needed
                return redirect()->route('payment.failure')->withMessage(['msg' => 'Unexpected payment status. Please contact support.', 'type' => 'error']);
            }
        } catch (PaymentVerificationFailedException $e) {
            Log::error('Paystack Payment Verification Failed: ' . $e->getMessage());
            return redirect()->route('getform')->withMessage(['msg' => 'Payment verification failed. Please contact support.', 'type' => 'error']);
        } catch (\Exception $e) {
            Log::error('Paystack Callback Error: ' . $e->getMessage());
            return redirect()->route('index')->withMessage(['msg' => 'An unexpected error occurred. Please contact support.', 'type' => 'error']);
        }
    }

    private function updatePaymentStatus($paymentDetails)
    {
        Payment::updateOrCreate(
            ['trans_id' => $paymentDetails->data->id],
            [
                'status' => $paymentDetails->status,
                'amount' => $paymentDetails->data->amount / 100, // Convert amount back to Naira
                // Add other fields as needed
            ]
        );
    }
}
