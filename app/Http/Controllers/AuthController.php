<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    
    public function login()
    {   
        return view('front.account.login');
    }

    public function register()
    {
        return view('front.account.register');
    }

    public function processRegister(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed',
        ]);

        if ($validator->passes()) {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'Registed successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Register successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }

    }

    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password],$request->get('remember'))) {

                if (session()->has('url.intended')) {
                    return redirect()->route('front.checkout');
                }

                return redirect()->route('profil.account');

            } else {

                return redirect()->route('login')->withInput($request->only('email'))->with('error', 'Email or password is incorrect');

            }

        } else {

            return redirect()->route('login')->withErrors($validator)->withInput($request->only('email'));

        }

    }

    public function profilAccount()
    {
        $user = User::where('id', Auth::user()->id)->first();
        $countries = Country::orderBy('name','ASC')->get();
        $customerAdress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        return view('front.account.profile', [
            'user' => $user,
            'countries' => $countries,
            'customerAddress' => $customerAdress
        ]);
    }

    public function updateProfileAccount(Request $request)
    {

        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId . ',id',
            'phone' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            $message = 'Profile updated successfully';
 
            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    
    }

    public function updateAddress(Request $request)
    {

        $customerAdress = CustomerAddress::where('user_id', Auth::user()->id)->first();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|required|unique:customer_adresses,email,' . $customerAdress->id . ',id',
            'mobile' => 'required|numeric',
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required', 
        ]);

        if ($validator->passes()) {

            if (!empty($customerAdress)) {
                $customerAdress = $customerAdress;
                $customerAdress->first_name = $request->first_name;
                $customerAdress->last_name = $request->last_name;
                $customerAdress->email = $request->email;
                $customerAdress->mobile = $request->mobile;
                $customerAdress->country_id = $request->country;
                $customerAdress->address = $request->address;
                $customerAdress->apartement = $request->aparatment;
                $customerAdress->city = $request->city;
                $customerAdress->state = $request->state;
                $customerAdress->zip = $request->zip;
                $customerAdress->save();
            } else {
                $customerAdress = new CustomerAddress;
                $customerAdress->user_id = $request->user_id;
                $customerAdress->first_name = $request->first_name;
                $customerAdress->last_name = $request->last_name;
                $customerAdress->email = $request->email;
                $customerAdress->mobile = $request->mobile;
                $customerAdress->country_id = $request->country;
                $customerAdress->address = $request->address;
                $customerAdress->apartement = $request->aparatment;
                $customerAdress->city = $request->city;
                $customerAdress->state = $request->state;
                $customerAdress->zip = $request->zip;
                $customerAdress->save();
            }


            $message = 'Updated address successfully';

            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('succes', 'You have been logout');
    }

    public function orders()
    {

        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
                        ->orderBy('created_at','DESC')
                        ->get();

        return view('front.account.order', ['orders' => $orders]);
    }

    public function ordersDetail($id)
    {

        $user = Auth::user();

        $orders = Order::where('user_id',$user->id)
                        ->where('id',$id)
                        ->first();
        
        $ordersItem = OrderItem::where('order_id',$id)->get();

        $ordersItemsCount = OrderItem::where('order_id',$id)->count();

        return view('front.account.orders-detail', [
            'orders' => $orders, 
            'ordersItem' => $ordersItem, 
            'ordersItemsCount' => $ordersItemsCount
        ]);
    }

    public function wishlist()
    {

        $wishlist = Wishlist::where('user_id', Auth::user()->id)
                            ->with('product')
                            ->get();
        
        return view('front.account.wishlist', ['wishlist' => $wishlist]);
    
    }

    public function removeWishlist(Request $request)
    {
        $wishlist = Wishlist::where('user_id', Auth::user()->id)
                            ->where('product_id', $request->id)
                            ->first();

        if ($wishlist == null) {
            session()->flash('error', 'Product alreay removed');
            return response()->json([
                'status' => true
            ]);
        } else {
            Wishlist::where('user_id', Auth::user()->id)
                    ->where('product_id', $request->id)
                    ->delete();
            session()->flash('success', 'Product successfully removed');
            return response()->json([
                'status' => true,
            ]);
        }
        
    }

    public function userChangePassword()
    {
        return view('front.account.change-password');
    }

    public function proccessChangePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'password_confirmation' => 'required|same:new_password'
        ]);

        if ($validator->passes()) {

            $user = User::select('id','password')->where('id',Auth::user()->id)->first();

            if (!Hash::check($request->old_password, $user->password)) {
                session()->flash('error', 'Password must be the same as the old');
                return response()->json([
                    'status' => true
                ]);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            $message = 'Success change password';

            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);


        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function showForgotPassword()
    {
        return view('front.account.forgot-password');
    }

    public function proccessForgotPassword(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('forgot-password')->withInput()->withErrors($validator);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        $user = User::where('email', $request->email)->first();

        $formData = [
            'token' => $token,
            'user' => $user,
            'mailSubject' =>  'You have requested to reset password'
        ];

        Mail::to($request->email)->send(new ResetPasswordMail($formData));

        return redirect()->route('forgot-password')->with('success', 'Please check your inbox');

    }

    public function resetPassword($token)
    {
        $tokenExist = DB::table('password_reset_tokens')->where('token',$token)->first();

        if ($tokenExist == null) {
            return redirect()->route('forgot-password')->with('error', 'Invalid request');
        }

        $tokenAge = now()->diffInMinutes($tokenExist->created_at);
        if ($tokenAge > 60) {
            return redirect()->route('forgot-password')->with('error', 'Your token was expired, try again');
        }

        return view('front.account.reset-password', ['token' => $token]);
    }

    public function proccessResetPassword(Request $request)
    {
        
        $token  = $request->token;

        $tokenObject = DB::table('password_reset_tokens')->where('token',$token)->first();

        if ($tokenObject == null) {
            return redirect()->route('forgot-password')->with('error', 'Invalid request');
        }

        $user = User::where('email',$tokenObject->email)->first();

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:4',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return redirect()->route('reset-password',$token)->withErrors($validator);
        }

        User::where('id',$user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('login')->with('success', 'Successfully reset password, please try logging in');

    }

}
