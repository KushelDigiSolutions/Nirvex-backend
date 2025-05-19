<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;


class InvoiceController extends Controller
{
    public function generatePDF($id)
    {
        $order = Order::with('users')->findOrFail($id);
        $orderItems = OrderItem::with('variant')->where('order_id', $id)->get();
        $vendor = User::findOrFail($order->vendor_id);
        $address = Address::findOrFail($order->address_id);
        return view('admin.invoice.index', compact('order','orderItems','vendor','address'));
        // return $pdf->download('invoice.pdf');
    }
}
