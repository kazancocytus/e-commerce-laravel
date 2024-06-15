<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');

        $query = Discount::orderBy('created_at', 'DESC');


        if(!empty($keyword)){
            $query = $query->where('code', 'like', '%'. $keyword .'%');
        }

        $discount = $query->paginate(10);

        return view('admin.discount.list', ['discount' => $discount]);
    }

    public function create()
    {
        return view('admin.discount.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'max_uses' => 'numeric|nullable',
            'max_uses_user' => 'numeric|nullable',
            'discount_amount' => 'required|numeric',
            'min_amount' => 'numeric|nullable',
        ]);

        if ($validator->passes()) {

            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($startsAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not be less than current time']
                    ]);
                }
            }

            if (!empty($request->starts_at) && !empty($request->expired_at)) {
                $expiredAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expired_at);
                $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($expiredAt->gt($startsAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expired_at' => 'Expired date not be can greater then Start at']
                    ]);
                }
            }

            $discount = new Discount();
            $discount->code = $request->code;
            $discount->name = $request->name;
            $discount->description = $request->description;
            $discount->max_uses = $request->max_uses;
            $discount->max_uses_user = $request->max_uses_user;
            $discount->type = $request->type;
            $discount->discount_amount = $request->discount_amount;
            $discount->min_amount = $request->min_amount;
            $discount->status = $request->status;
            $discount->starts_at = $request->starts_at;
            $discount->expired_at = $request->expired_at;
            $discount->save();

            session()->flash('success', 'Coupon updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Coupon updated successfully'
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
        $discount = Discount::find($id);
        return view('admin.discount.edit',['discount' => $discount]);
    }

    public function update(Request $request)
    {

        $discount = Discount::find($request->id);

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'max_uses' => 'numeric|nullable',
            'max_uses_user' => 'numeric|nullable',
            'discount_amount' => 'required|numeric',
            'min_amount' => 'numeric|nullable',
        ]);

        if ($validator->passes()) {

            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($startsAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not be less than current time']
                    ]);
                }
            }

            if (!empty($request->starts_at) && !empty($request->expired_at)) {
                $expiredAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expired_at);
                $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($expiredAt->gt($startsAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expired_at' => 'Expired date not be can greater then Start at']
                    ]);
                }
            }

            $discount->code = $request->code;
            $discount->name = $request->name;
            $discount->description = $request->description;
            $discount->max_uses = $request->max_uses;
            $discount->max_uses_user = $request->max_uses_user;
            $discount->type = $request->type;
            $discount->discount_amount = $request->discount_amount;
            $discount->min_amount = $request->min_amount;
            $discount->status = $request->status;
            $discount->starts_at = $request->starts_at;
            $discount->expired_at = $request->expired_at;
            $discount->save();

            session()->flash('success', 'Coupon created successfully');

            return response()->json([
                'status' => true,
                'message' => 'Coupon created successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    } 

    public function delete($id)
    {
        $discount = Discount::find($id);
        if (empty($discount)) {
            session()->flash('error', 'Discount not found');
            return redirect()->route('discount');
            return response()->json([
                'status' => false
            ]);
        }

        $discount->delete();

        session()->flash('success', 'Delete discount successfully');

        return response()->json([
            'status' => true,
            'message' => 'Delete discount successfully'
        ]);
    }
}
