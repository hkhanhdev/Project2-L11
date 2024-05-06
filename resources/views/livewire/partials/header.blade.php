<?php

use Livewire\Volt\Component;

new class extends Component {
    //
    public function toCart()
    {
        return redirect()->route("cart");
    }

    public function getCartInfo()
    {
//        case when no authenticated user
        if (auth()->user() == null) {
            return null;
        }else { //case when authenticated user
            $cus_id = auth()->user()->id;
            // Check if an order exists for the customer
            $order = \App\Models\Orders::where('customer_id', $cus_id)
                ->where('status', 'in cart')
                ->first();
            if ($order) {
                $cart_details = \App\Models\CartItems::where('cart_id',$order->cart_id)->get();
                return $cart_details;
            } else {
                return null;
            }
        }
    }

    public function with(): array
    {
        return [
            'cart_info' => $this->getCartInfo()
        ];
    }
}; ?>


<div class="navbar w-9/12 rounded-lg mt-2">
    <div class="navbar-start">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <li><a href="/">Home</a></li>
                <li>
                    <a href="/contact">Contact</a>
                </li>
                <li><a href="/all_products">Our products</a></li>
            </ul>
        </div>
        <a class="text-2xl motion-safe:animate-bounce font-bold px-3">Supplements</a>
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            <li><a href="/">Home</a></li>
            <li>
                <a href="/contact">Contact</a>
            </li>
            <li><a href="/all_products">Our products</a></li>
        </ul>
    </div>
    <div class="navbar-end">
        @auth
            <a
                href="{{ route('dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Dashboard
            </a>
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span class="badge badge-sm indicator-item">{{$cart_info ? $cart_info->count() : 0}}</span>
                    </div>
                </div>
                <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow">
                    <div class="card-body">
                        <span class="font-bold text-lg">{{$cart_info ? $cart_info->count() : 0}} Items</span>
                        <span class="text-info">Total: ${{$cart_info ? $cart_info->sum('subtotal') : 0}}</span>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-block" wire:click="toCart"><span wire:loading.class="loading loading-spinner"></span>View cart</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Avt" src="https://www.svgrepo.com/show/526700/user-circle.svg" />
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li>
                        <a class="justify-between" href="{{route('client_profile')}}">
                            Profile

                        </a>
                    </li>
                    <li><a href="{{route('logout')}}">Logout</a></li>
                </ul>
            </div>
        @else
            <a
                href="{{ route('login') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white bg-primary hover:scale-105 duration-300"
            >
                Log in
            </a>

{{--            @if (Route::has('register'))--}}
            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white bg-accent ml-2 hover:scale-105 "
            >
                Register
            </a>
{{--            @endif--}}
        @endauth
        <div class="dropdown ml-2">
            <div tabindex="0" role="button" class="btn">
                Theme
                <svg width="12px" height="12px" class="h-2 w-2 fill-current opacity-60 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2048 2048"><path d="M1799 349l242 241-1017 1017L7 590l242-241 775 775 775-775z"></path></svg>
            </div>
            <ul tabindex="0" class="dropdown-content z-[1] p-2 shadow-2xl bg-base-300 rounded-box w-52">
                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Dark" value="business"/></li>
                <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Light" value="nord"/></li>
            </ul>
        </div>
    </div>
</div>

