<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create()
    {
        $countries = Country::get();
        $data['countries'] = $countries;

        $shipingCharge = Shipping::select('shipping_charges.*','countries.name')
                                ->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['shippingCharge'] = $shipingCharge;

        return view('admin.shipping.create',$data);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {

            $count = Shipping::where('country_id', $request->country)->count();

            if ($count > 0) {
                session()->flash('error', 'Shipping already added');
                return response()->json([
                    'status' => true,
                    'errors' => $validator->errors()
                ]);
            }

            $shipping = new Shipping;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success', 'Shipping created successfully');

            return response()->json([
                'status' => true,
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

        $shipingCharge = Shipping::find($id);

        $countries = Country::get();
        $data['countries'] = $countries;
        $data['shippingCharge'] = $shipingCharge;

        return view('admin.shipping.edit',$data);
    
    }

    public function update($id, Request $request)
    {

        $shipingCharge = Shipping::find($id);

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'numeric|required'
        ]);

        if ($validator->passes()) {

            $count = Shipping::where('country_id', $request->country)->count();

            if ($count > 0) {
                session()->flash('error', 'Shipping already added');
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ]);
            }


            $shipingCharge->country_id = $request->country;
            $shipingCharge->amount = $request->amount;
            $shipingCharge->save();
    
            session()->flash('success', 'Updated shipping succesfully');
    
            return response()->json([
                'status' => true
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
        $shipingCharge = Shipping::find($id);
        if (empty($shipingCharge)) {
            session()->flash('error', 'Shipping not found');
            return response()->json([
                'status' => false,
            ]);
        }

        $shipingCharge->delete();

        session()->flash('success', 'Shipping deleted successfully');

        return response()->json([
            'status' => true
        ]);
    }

}
