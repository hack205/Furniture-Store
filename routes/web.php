<?php
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.home');
});


Route::get('/print-form/{id}', [\App\Http\Controllers\OrderController::class, 'printForm'])->name('print.form');
Route::get('print/orderpayments/{id}', [OrderController::class, 'printOrderPayments'])->name('print.orderpayments');
