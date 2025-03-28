<?php

use Livewire\Volt\Component;

new class extends Component {
    //
    public $listeners = ["resetCart" => '$refresh'];
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
                $cart_details = \App\Models\CartItems::where('order_id',$order->id)->get();
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
            @role("1")
                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-md px-3 py-2 ring-1 ring-transparent transition focus:outline-none focus-visible:ring-[#FF2D20]"
                >
                    Administrator panel
                </a>
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            <img alt="Avt" src="https://www.svgrepo.com/show/108160/admin-with-cogwheels.svg" />
                        </div>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a href="{{route('logout')}}">Logout</a></li>
                    </ul>
                </div>
            @endrole

            @role("0")
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
                        <img alt="Avt" src="https://th.bing.com/th/id/OIP.kcaJsnMsMsFRdU6d1m2v6AHaHa?w=194&h=194&c=7&r=0&o=5&pid=1.7" />
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
            @endrole

        @else
            <a
                href="{{ route('login') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white bg-primary hover:scale-105 duration-300"
            >
                Log in
            </a>

            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white bg-accent ml-2 hover:scale-105 "
            >
                Register
            </a>
        @endauth
            <label class="swap swap-rotate ml-3">
                <!-- this hidden checkbox controls the state -->
                <input type="checkbox" class="theme-controller" value="business" />
                <!-- sun icon -->
                <svg class="swap-off fill-current w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>
                <!-- moon icon -->
                <svg class="swap-on fill-current w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
            </label>
    </div>
</div>

