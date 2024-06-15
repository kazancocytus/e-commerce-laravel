<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shipping;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class CartController extends Controller
{

    public function addToCart(Request $request)
    {

        $product = Product::with('product_image')
                            ->find($request->id);

        if ($product == null ) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if (Cart::count() > 0) {

            // Check if product is already in the cart
            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $list) {
                if ($list->id == $product->id) {
                    $productAlreadyExist = true;
                } 
            }

            if ($productAlreadyExist == false) {
            
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_image)) ? $product->product_image->first() : '']);
                
                $status = true;
                $message = $product->title . ' ' . 'added in cart';

            } else {
                $status = false;
                $message = $product->title . ' ' . 'already added in cart';
            } 

        } else {

            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_image)) ? $product->product_image->first() : '']);
            $status = true;
            $message = $product->title . ' ' . 'added in cart';

        }


        return response()->json([
            'status' => $status,
            'message' => $message
        ]);


    }

    public function cart()
    {

        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;

        return view('front.cart',$data);

    }

    public function updateCart(Request $request)
    {

        $rowId = $request->rowId;
        $qty = intval($request->qty);

        $itemInfo = Cart::get($rowId);
        
        $product = Product::find($itemInfo->id);

        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash('success', $message);
            } else {
                $message = 'Request qty(' . $qty . ') is not available stock';
                $status = false;
                session()->flash('error',$message);
            }
        } else {
            Cart::update($rowId,$qty);
            $message = 'Cart updated successfully';
            $status = true;
            session()->flash('success', $message);
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteCart(Request $request)
    {
        $rowId = $request->rowId;

        $itemInfo = Cart::get($rowId);
        if ($itemInfo == null) {
            $errorMessage = 'Item not found in cart';
            session()->flash('error', $errorMessage);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }

        Cart::remove($request->rowId);

        $message = 'Item removed from cart successfully';
        session()->flash('success',$message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);

    }

    public function checkout()
    {

        $discount = 0;

        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        if (Auth::check() == false) {

            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }

            return redirect()->route('login');
        }

        $customerAdress = CustomerAddress::where('user_id', Auth::user()->id)->first();

        session()->forget('url.intended');

        $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2,'.','');
        
        // Apply discount coupon
        if (session()->has('code')) {
            $code = session()->get('code');

            if ($code->type == 'percent') {
                $discount = ($code->discount_amount/100)*$subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        // Calculate shipping
        if ($customerAdress != '') {
            $userCountry = $customerAdress->country_id;
            $shippingInfo = Shipping::where('country_id', $userCountry)->first();
    
            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }
    
            $totalShippingCharge = $shippingInfo ? $totalQty*$shippingInfo->amount : 0;
            $grandTotal = ($subTotal-$discount)+$totalShippingCharge;
        } else {
            $grandTotal = $subTotal-$discount;
            $totalShippingCharge = 0;
        }


        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAdress,
            'totalShippingCharge' => $totalShippingCharge,
            'discount' => $discount,
            'grandTotal' => $grandTotal
        ]);
    }

    public function processCheckout(Request $request)
    {
        Log::info('Process chkecout started');

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:15',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();

        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'country_id' => $request->country,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'mobile' => $request->mobile,
            ],
        );

        if ($request->payment_method == 'cod') {

            $discounCodeId = null;
            $promoCode = '';
            $shippingCharge = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2,'.','');

            if (session()->has('code')) {
                $code = session()->get('code');
    
                if ($code->type == 'percent') {
                    $discount = ($code->discount_amount/100)*$subTotal;
                } else {
                    $discount = $code->discount_amount;
                }

                $discounCodeId = $code->id;
                $promoCode = $code->code;
            }
            
            $shippingInfo = Shipping::where('country_id', $request->country)->first();
            
            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty = $item->qty;
            }

            if ($shippingInfo != null) {
                
                $shippingCharge = $shippingInfo ? $totalQty*$shippingInfo->amount : 0;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;

            } else {
                
                $shippingInfo = Shipping::where('country_id','rest_of_world')->first();
                $shippingCharge = $shippingInfo ? $totalQty*$shippingInfo->amount : 0;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;
            
            }


            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shippingCharge;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code_id = $discounCodeId;
            $order->coupon_code = $promoCode;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->user_id = $user->id;

            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->apartement = $request->apartement;
            $order->state = $request->state;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->notes = $request->notes;
            $order->country_id = $request->country;
            $order->save();

            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price*$item->qty;
                $orderItem->save();

                // Update product
                $productData = Product::find($item->id);
                if ($productData->track_qty == 'Yes') {
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty-$item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }
            }

            // Send email
            orderEmail($order->id, 'customer');

            session()->flash('success', 'You have successfully placed your order');

            Cart::destroy();

            return response()->json([
                'message' => 'Order saved successfully',
                'orderId' => $order->id,
                'status' => true,
            ]);
            

        }

    }

    public function thankyou($id)
    {
        return view('front.thanks', [
            'id' => $id
        ]);
    }

    public function getOrderSummery(Request $request)
    {

        $subTotal = Cart::subtotal(2,'.','');
        $discount = 0;
        $discountString = '';

        // Apply discount coupon
        if (session()->has('code')) {
            $code = session()->get('code');

            if ($code->type == 'percent') {
                $discount = ($code->discount_amount/100)*$subTotal;
            } else {
                $discount = $code->discount_amount;
            }
            
            $discountString =   '<div class="mt-4" id="discount-response">
                                    <strong>' .  session()->get('code')->code . '</strong>
                                    <a href="" class="btn btn-danger btn-sm" id="remove-discount"><i class="fa fa-times"></i></a>
                                </div>';
        }
        

        $totalQty = 0;
        
        foreach (Cart::content() as $item) {
            $totalQty = $item->qty;
        }

        if ($request->country_id > 0) {

            $shippingInfo = Shipping::where('country_id', $request->country_id)->first();

            if ($shippingInfo == null) {
                $grandTotal = $subTotal-$discount;
                $shippingCharge = 0;
                return response()->json([
                    'status' => false,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => $discount,
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            }

            if ($shippingInfo != null) {
                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => $discount,
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            } else {
                $shippingInfo = Shipping::where('country_id','rest_of_world')->first();

                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => $discount,
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'grandTotal' => number_format(($subTotal-$discount),2),
                'discount' => $discount,
                'discountString' => $discountString,
                'shippingCharge' => number_format(0,2),
            ]);
        }

    }

    public function applyDiscount(Request $request)
    {   
        
        $code = Discount::where('code', $request->code)->first();

        if ($code == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Invalid coupon code'
            ]);
        }

        $now = Carbon::now();

        if ($code->starts_at != '') {
            $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$code->starts_at);

            if ($now->gt($startsAt)) {
                return response()->json([
                    'status' => false,
                    'errors' => "Coupon not yet released",
                ]);
            }
        }

        if ($code->expired_at != '') {
            $expiredAt = Carbon::createFromFormat('Y-m-d H:i:s',$code->expired_at);

            if ($expiredAt->lt($now)) {
                return response()->json([
                    'status' => false,
                    'errors' => 'Coupon code has expired'
                ]);
            }
        }

        $couponUsed = Order::where('coupon_code_id', $code->id)->count();

        // Max uses check

        if ($code->max_uses > 0) {
            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    'status' => false,
                    'errors' => 'Coupon has reached the maximum usage limit'
                ]);
            }
        }


        // Max uses user 

        if ($code->max_uses_user > 0) {
            $couponUsedUser = Order::where('coupon_code_id', $code->id)
                                    ->where('user_id', Auth::user()->id)
                                    ->count();
    
            if ($couponUsedUser >= $code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'errors' => 'You alreday use this coupon code'
                ]);
            }   
        }

        $subTotal = Cart::subtotal(2,'.','');

        if ($code->min_amount > 0) {
            if ($subTotal < $code->min_amount) {
                return response()->json([
                    'status' => false,
                    'errors' => 'Your min must be greater than' . ' ' . '$' . $code->min_amount . '' 
                ]);
            }
        }


        session()->put('code',$code);

        return $this->getOrderSummery($request);

    }

    public function removeDiscount(Request $request)
    {
        session()->forget('code');
        return $this->getOrderSummery($request);
    }

}
