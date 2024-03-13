<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Exception;
use Twilio\Rest\Client;
use Validator;
use Stripe;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
//            'image' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
        ]);
       if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $customer = Stripe\Customer::create([
            'email' => $request->email,
        ]);

        $input=$request->all();
        $input['stripe_customer_id'] = $customer->id;
        $input['password'] = bcrypt($request->password);
        $user_id = User::create($input)->id;
        $response =[
            'status' =>true,
            'message' => "Data registered successfully"
        ];
        return response()->json($response);
    }

    public function confirmEmail($id)
    {
        User::where('id',$id)->update(['status'=>1]);
        return view('main.success-mail');
    }

      public function getLoginUserDataByID($id)
    {
        $user = User::with('subscription')->find($id);
        $response =[
            'data'=>$user,
            'status' =>true,
            'message' => "data get successfully"
        ];
        return response()->json($response);
    }

    public function userProfile() {
        $user = User::with('subscription')->find(Auth::id());

        $response =[
            'data'=>$user,
            'status' =>true,
            'message' => "data get successfully"
        ];
        return response()->json($response);
    }

    public function getAllVerifiedUser()
    {
        $users=User::where('status',1)->get();
        return respose()->json($users);
    }

}
