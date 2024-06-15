<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {

        $query = Order::orderBy('orders.created_at')
                    ->select('orders.*','users.name','users.email');
        $query = $query->leftJoin('users','users.id','orders.user_id');
        
        $keyword = $request->get('keyword');
        
        if(!empty($keyword)){
            $query = $query->where('users.name', 'like', '%'. $keyword .'%');
            $query = $query->orWhere('users.email', 'like', '%'. $keyword .'%');
            $query = $query->orWhere('orders.id', 'like', '%'. $keyword .'%');
        }

        $orders = $query->paginate(10);

        return view('admin.orders.list', ['orders' => $orders]);
    }

    public function detail($id)
    {

        $orders = Order::select('orders.*','countries.name as countryName')
                        ->where('orders.id',$id)
                        ->leftJoin('countries','countries.id','orders.country_id')
                        ->first();

        $ordersItem = OrderItem::where('order_id',$id)->get();

        return view('admin.orders.detail', ['orders' => $orders, 'ordersItem' => $ordersItem]);
    }

    public function updateStatusOrder(Request $request, $id)
    {

        $orders = Order::find($id);
        $orders->status = $request->status;
        $orders->shipped_date = $request->shipped_date;
        $orders->save();

        $message = 'Updated status successfully';

        session()->flash('success', $message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);

    }

    public function sendInvoice(Request $request, $id)
    {
        
        orderEmail($id, $request->userType);
        
        $message = 'Order email sent successfully';

        session()->flash('success', $message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);

    }
}
