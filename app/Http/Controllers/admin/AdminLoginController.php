<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->passes()){

            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))){

                $admin = Auth::guard('admin')->user();

                if($admin->role == 2){
                    return redirect()->route('admin.dashboard');
                } else {

                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not admin');
                }

                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('admin.login')->with('error', 'Email or Password is incorrect')->onlyInput('email');
            }

        } else {
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }

    }

    public function showForgotPasswordPage()
    {
        return view('admin.forgot-password');
    }
    
    public function proccessForgotPassword(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.forgot-password')->withInput()->withErrors($validator);
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

        return redirect()->route('admin.forgot-password')->with('success', 'Please check your inbox');

    }

}
