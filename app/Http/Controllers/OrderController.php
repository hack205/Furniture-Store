<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function printForm($id)
    {
        $order = Order::with(['customer'])->findOrFail($id);

        return view('printer.order', compact('order'));
    }
    public function printOrderPayments($id)
    {
        $order = Order::with(['customer', 'payments'])->findOrFail($id);

        return view('printer.orderpayments', compact('order'));
    }
}
