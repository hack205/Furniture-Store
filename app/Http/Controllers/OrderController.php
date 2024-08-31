<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function printForm($id)
    {
        $order = Order::with(['customer', 'items'])->findOrFail($id);

        return view('printer.order', compact('order'));
    }
}
