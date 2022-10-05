<?php

use App\Http\Controllers\PaypalPaymentController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route:: get('/pay-with-paypal',       [PaypalPaymentController::class,   'paypal_pay'])->name('pay-with-paypal');

Route:: get('/paypal-payment-success',       [PaypalPaymentController::class,   'paypal_success'])->name('payment.success');

Route:: get('/paypal-payment-cancled',       [PaypalPaymentController::class,   'paypal_cancel'])->name('paypal-payment-cancled');
