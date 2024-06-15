<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangePassword()
    {
        return view('admin.change-password');
    }

    public function proccessChangePassword(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:4',
            'password_confirmation' => 'required|same:new_password'
        ]);

        if ($validator->passes()) {

            $admin = User::select('id','password')->where('id', Auth::guard('admin')->user()->id)->first();
            
            if (!Hash::check($request->old_password, $admin->password)) {
                $errors = 'Password must be the same as the old';
                session()->flash('error', $errors);
                return response()->json([
                    'status' => true,
                    'errors' => $errors
                ]);
            }

            $admin->password = Hash::make($request->new_password);
            $admin->save();

            $message = 'Change password for admin successfully';

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
}
