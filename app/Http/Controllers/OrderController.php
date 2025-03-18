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

    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_status' => 'required|integer|min:0|max:9', 
        ]);

        $order = Order::find($request->order_id);
        $order->order_status = $request->order_status;
        $order->save();

    $notificationType = $this->getNotificationType($request->order_status);
    $message = "Your order #{$order->order_id} status has been updated to: " . $this->getStatusText($request->order_status);

    createUserNotification($order->user_id, $notificationType, $message);

        return response()->json(['message' => 'Order status updated successfully!']);
    }

    private function getNotificationType($status)
{
    $mapping = [
        1 => 2,  
        2 => 4,  
        3 => 4,  
        4 => 5,  
        5 => 6,  
        6 => 11, 
        7 => 7,  
        8 => 7,  
        9 => 7,  
    ];

    return $mapping[$status] ?? null;
}

private function getStatusText($status)
{
    $statusTexts = [
        0 => "Created",
        1 => "Payment Done",
        2 => "Order Accepted",
        3 => "Order Preparing",
        4 => "Order Shipped",
        5 => "Order Delivered",
        6 => "Order Completed",
        7 => "Order Rejected",
        8 => "Order Returned",
        9 => "Order Cancelled",
    ];

    return $statusTexts[$status] ?? "Unknown Status";
}

public function getSellers(Request $request)
{
    $query = User::where('user_type', 2)
                 ->where('pincode', $request->pincode)
                 ->with('addresses');

// dd($query);

    if ($request->has('first_name') && !empty($request->first_name)) {
        $query->where('first_name', 'like', '%' . $request->first_name . '%');
    } else {
        $query->where('pincode', $request->pincode)->limit(5);
    }

    $sellers = $query->get();

    return response()->json($sellers);
}

public function getSellers190325(Request $request)
{
    $query = User::where('user_type', 3)->where('pincode', $request->pincode);

    if ($request->has('name') && !empty($request->name)) {
        $query->where('first_name', 'like', '%' . $request->name . '%');
    } else {
        $query->where('pincode', $request->pincode)->limit(5);
    }

    $sellers = $query->get();

    return response()->json($sellers);
}

public function getSellers130225(Request $request)
{
    $orderId = $request->query('order_id');
    $pincode = $request->query('pincode');
    $order = Order::where('id', $orderId)->first();

    if (!$order) {
        return response()->json(['error' => 'Order not found'], 404);
    }

    $productId = $order->product_id;  
    $sellers = Order::where('id', $orderId)
                     ->whereHas('products', function ($query) use ($productId) {
                         $query->where('product_id', $productId);
                     })
                     ->take(5)
                     ->get(['id', 'name', 'address', 'photo']);

    return response()->json(['sellers' => $sellers]);
}



}
