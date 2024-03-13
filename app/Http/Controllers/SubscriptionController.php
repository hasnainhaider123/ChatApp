<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe;
use Charge;
class SubscriptionController extends Controller
{
    public function paySubscription(Request $request)
    {
        $stripe = new \Stripe\StripeClient('sk_test_51KGwr3EhNBd9PttKGUAeAWJFipO7TVXG6RkEJOgAoXogyvN422hTsRcagaSDSsbZcm93OkfrHD72HqteMEgy8nzU00frSoaq9H');

        $user = Auth::user();
        try {
//             $token =$stripe->tokens->create([
//              'card' => [
//                'number' => '4242424242424242',
//                'exp_month' => '5',
//                'exp_year' => '2024',
//                'cvc' => '314',
//              ],
//            ]);
            $token =$stripe->customers->createSource($user->stripe_customer_id, ['source' => 'tok_visa']);

            $charge =  $stripe->charges->create([
                'amount' => $request->amount, // amount in cents
                'currency' => 'usd',
                'description' => 'Payment for subscription',
                'source' => $token['id'],
                'customer' => $user->stripe_customer_id, // Customer ID from your database
            ]);
            if($charge['status'] == 'succeeded') {


             UserSubscription::create
             ([
                 'user_id' =>Auth::id(),
                 'subscription_type' => $request->subscription_type,
                 'payment_method' => $request->payment_method,
                 'payment_status' => 1,
              ]);
                return response()->json(['success' => true, 'message' => 'Payment successful.']);

            } else {
                return response()->json(['success' => false, 'message' => 'Payment Unsuccessful.']);
            }
            // Process further as needed
        } catch (Stripe\Exception\CardException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
