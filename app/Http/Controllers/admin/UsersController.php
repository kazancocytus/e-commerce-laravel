<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');

        $query = User::where('role', '!=', 2)
                    ->orderBy('id','DESC');

        if(!empty($keyword)){
            $query = $query->where('name', 'like', '%'. $keyword .'%'); 
        }

        $users = $query->paginate(15);
        return view('admin.users.list', ['users' => $users]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|numeric',
            'password' => 'required|min:4|confirmed'
        ]);

        if ($validator->passes()) {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->password = Hash::make($request->password);
            $user->save();

            $message = 'Created user successfully';

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

    public function edit($id) 
    {
        $users = User::find($id);

        if ($users == null) {
            session()->flash('error', 'User not found');
            return redirect()->route('admin.users');
        }

        return view('admin.users.edit', ['users' => $users]);
    }

    public function update(Request $request) 
    {

        $users = User::find($request->id);

        if ($users == null) {
            session()->flash('error', 'User not found');
            return redirect()->route('admin.users');
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'email|required|unique:users,email,'.$users->id,
            'password' => 'required|min:4|confirmed',
            'phone' => 'nullable|numeric'
        ]);

        if ($validator->passes()) {

            $users->name = $request->name;
            $users->email = $request->email;
            $users->phone = $request->phone;
            $users->status = $request->status;
            $users->password = Hash::make($request->password);
            $users->save();

            $message = 'Updated user successfully';

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

    public function destroy($id)
    {
        $users = User::find($id);

        if ($users == null) {
            session()->flash('error', 'User not found');
            return redirect()->route('admin.users');
        }

        $users->delete();

        session()->flash('success', 'Delete user successfully');

        return response()->json([
            'status' => true,
        ]);

    }
}
