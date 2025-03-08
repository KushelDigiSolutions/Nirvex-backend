<?php

namespace App\Http\Controllers\api;

use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('users', 'orderItems', 'products')->orderby('id','desc')->get();
        if (!$orders) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No Order found.',
                ],
                'data' =>[],
            ], 401);
        }
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Order retrieved successfully.',
            ],
            'data' =>$orders,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orders = Order::with('users', 'orderItems', 'products')->orderby('id','desc')->find($id);
        if (!$orders) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No Order found.',
                ],
                'data' =>[],
            ], 401);
        }
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Order retrieved successfully.',
            ],
            'data' =>$orders,
        ], 200);
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
    try {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_status' => 'required|integer|min:0|max:9', 
        ]);

        $order = Order::find($request->order_id);

        if (!$order) {

            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No Order found.',
                ],
                'data' =>[],
            ], 401);
        }

        $order->order_status = $request->order_status;
        $order->save();

        $notificationType = $this->getNotificationType($request->order_status);
        $message = "Your order #{$order->id} status has been updated to: " . $this->getStatusText($request->order_status);

        createUserNotification($order->user_id, $notificationType, $message);

        return response()->json([
            'isSuccess' => true,
            'errors' => [
            'message' => 'Order status updated successfully!',
            ],
            'order' => [
                'order_id' => $order->id,
                'status' => $this->getStatusText($request->order_status),
                'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'isSuccess' => false,
            'message' => 'Something went wrong. Please try again!',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
