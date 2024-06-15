<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use App\Models\Page;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
      
    public function index()
    {

        $product = Product::where('is_featured','Yes')
                        ->where('status',1)
                        ->paginate(8);
        $data['featuredProduct'] = $product;

        $latestProduct = Product::orderBy('id', 'DESC')
                                ->where('status',1)
                                ->take(1)
                                ->get();
        $data['latestProduct'] = $latestProduct;

        return view('front.home',$data);

    }

    public function addWishlist(Request $request)
    {
        if (Auth::check() == false) {

            session(['url.intended' => url()->previous()]);

            return response()->json([
                'status' => false
            ]);

        }

        $product = Product::where('id',$request->id)->first();

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => '<div class="alert alert-danger>"Product not found</div>'
            ]);
        }

        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ],
        );

        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success"><strong>' . $product->title . '</strong> added successfully to wishlist</div>'
        ]);
    }

    public function showPage($slug)
    {
        $pages = Page::where('slug',$slug)->first();

        if ($pages == null) {
            abort(403);
        }

        return view('front.pages', ['pages' => $pages]);
    }

    public function sendContact(Request $request) 
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        if ($validator->passes()) {

            // Mail subject
            
            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject' => 'You have received a contact'
            ];

            $admin = env('ADMIN_EMAIL');

            // Send email  to admin

            Mail::to($admin)->send(new ContactEmail($mailData));

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

}
