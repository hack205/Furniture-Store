<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});


Route::get('/print-form/{id}', [\App\Http\Controllers\OrderController::class, 'printForm'])->name('print.form');
