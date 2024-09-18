<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CanvasData;

class OrderController extends Controller
{
    public function printForm($id)
    {
        $order = Order::with(['customer'])->findOrFail($id);
        $canvasData = CanvasData::first()->data ?? [];
        return view('printer.order', compact('order', 'canvasData'));
    }
    public function printOrderPayments($id)
    {
        $order = Order::with(['customer', 'payments'])->findOrFail($id);
        return view('printer.orderpayments', compact('order'));
    }
}
