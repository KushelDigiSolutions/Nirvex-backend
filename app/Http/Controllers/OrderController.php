<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomerDetails;
use App\Models\User;
use Illuminate\Support\Number;
use App\Models\Address;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalOrders = Order::with(['users', 'items', 'addresses'])
        ->select('orders.id', 'orders.user_id', 'orders.grand_total', 'orders.created_at', 'orders.order_status')
        ->orderBy('orders.created_at', 'desc')
        ->get()
        ->map(function ($order, $index) {
            $customerName = $order->users->first_name . ' ' . ($order->users->last_name ?? '');
            $pincode = $order->users->pincode ?? ($order->addresses->pincode ?? 'Unknown');

            $seller = \App\Models\User::where('user_type', 1)
                ->where('id', $order->user_id)
                ->first();
            $sellerName = $seller ? $seller->first_name . ' ' . $seller->last_name : 'Unknown';

            return [
                'so_no' => $index + 1, 
                'order_id' => $order->id,
                'customer' => $customerName,
                'pincode' => $pincode,
                'seller' => $sellerName,
                'count' => $order->items->count(),
                'total_amount' => Number::currency($order->grand_total, 'INR', locale: 'en-IN'),
                'ordered' => $order->created_at->format('Y-m-d H:i:s'),
                'status' => $order->order_status,
                'view' => '<a href="' . route('orders.show', $order->id) . '" class="btn btn-sm btn-primary">View</a>',
            ];
        });

        //  echo '<pre>'; print_r($totalOrders); die;

        return view('admin.orders.index', compact('totalOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('admin.orders.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderId = decrypt($id);
        $orders = Order::with('users')->where('id', $orderId)->first();
        $orderItems = OrderItem::with('product')->where('order_id', $orderId)->get();
        $address = Address::find($orders->address_id);
        if ($address) {
            $address->address_type_label = $address->address_type == 0 ? 'Home' : 'Office';
        }
        return view('admin.orders.show',compact('orders','orderItems','address'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
