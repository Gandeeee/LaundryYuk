<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\LandingPage;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\DriverIndex;
use App\Livewire\Customer\Dashboard as CustomerDashboard;
use App\Livewire\Customer\OrderCreate;
use App\Livewire\Admin\ReportIndex;
use App\Http\Controllers\Admin\InvoiceController;
use App\Livewire\Customer\OrderHistory;
use App\Livewire\Customer\OrderDetail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================
// PUBLIC
// ========================
Route::get('/', LandingPage::class)->name('home');

// ========================
// GUEST
// ========================
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// ========================
// LOGOUT
// ========================
Route::get('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ========================
// AUTH
// ========================
Route::middleware('auth')->group(function () {

    // -------- INVOICE --------
    Route::get('/invoice/{id}/print', [InvoiceController::class, 'print'])
        ->name('invoice.print');

    // -------- ADMIN --------
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/drivers', DriverIndex::class)->name('admin.drivers');
    Route::get('/admin/reports', ReportIndex::class)->name('admin.reports');
    Route::get('/admin/orders', App\Livewire\Admin\OrderIndex::class)->name('admin.orders');

    // -------- CUSTOMER --------
    Route::get('/customer/dashboard', CustomerDashboard::class)
        ->name('customer.dashboard');

    // 🔥 DETAIL ORDER (HARUS DI ATAS)
    Route::get('/customer/order/{order}', OrderDetail::class)
        ->name('customer.order.detail');

    Route::get('/customer/order', OrderCreate::class)
        ->name('customer.order.create');

    Route::get('/customer/history', OrderHistory::class)
        ->name('customer.history');
});
