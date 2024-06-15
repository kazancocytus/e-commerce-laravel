<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Page;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

function getCategories() 
{
    return Category::orderBy('name', 'ASC')
                    ->with('subCategories')
                    ->where('status',1)
                    ->where('showHome', 'Yes')
                    ->get();
}

function getProductImage($productId) 
{
    return ProductImage::where('product_id',$productId)->first();
}

function orderEmail($orderId, $userType="customer")
{
    $orders = Order::where('id',$orderId)->with('items')->first();

    if ($userType ==  'customer') {
        $subject = 'Thanks for your order';
        $email = $orders->email;
    } else {
        $subject = 'You have received an order';
        $email = env('ADMIN_EMAIL');
    }

    $mailData = [
        'subject' => $subject,
        'orders' => $orders,
        'userType' => $userType
    ];

    Mail::to($email)->send(new OrderEmail($mailData));
}

function getCountry($id)
{
    return Country::where('id', $id)->first();
}

function getAllPage()
{
    return Page::all();
}

?>