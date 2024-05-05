<?php
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

//Route::view('/', 'welcome')->name('home');
Volt::route('/', 'pages.client.home')->name('home');

Route::middleware(['auth','verified'])->group(function () {
    Volt::route('/cart', 'pages.client.cart')->name('cart');
    Volt::route('/profile', 'pages.client.profile')->name('client_profile');
    Volt::route('/administration-panel/Dashboard', 'pages.admin.dashboard')->name('dashboard');
    Volt::route('/administration-panel/Products', 'pages.admin.products_management')->name('prd_mng');
    Volt::route('/administration-panel/Brands', 'pages.admin.brands_management')->name('brd_mng');
    Volt::route('/administration-panel/Categories', 'pages.admin.categories_management')->name('cate_mng');
    Volt::route('/administration-panel/Users', 'pages.admin.users_management')->name('usr_mng');
    Volt::route('/administration-panel/Orders', 'pages.admin.orders_management')->name('ord_mng');
    Volt::route('/administration-panel/Profile', 'pages.admin.profile')->name('admin_profile');
});
Volt::route('/all_products', 'pages.client.all-products')->name('all');
Volt::route('/product_details/{prd_id}', 'pages.client.product_details')->name('product');
Volt::route('/contact', 'pages.client.contact')->name('contact');

Route::fallback(function () {
    return view('not-found');
});
Route::get("logout",function (Logout $logout){
    $logout();
    return redirect('/');
})->name('logout');
require __DIR__.'/auth.php';
