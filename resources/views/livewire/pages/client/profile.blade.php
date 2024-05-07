<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout("components.layouts.guest")]
#[Title("Profile")]
class extends Component {
    //
    public $full_location = ['display'=>'Profile','route'=>'profile','icon_name'=>"o-user"];
//    public string $group = 'group2';
    public bool $logoutModal = false;
    public function getUserInfo()
    {
        dd(auth()->user()->orders->where('status','!=','in cart'));
        return auth()->user();
    }

    public function with():array
    {
        return [
            'user_info' => auth()->user(),
//            'user_info' => $this->getUserInfo(),
            'orders_info' => auth()->user()->orders->where('status','!=','in cart')
        ];
    }

}; ?>

<div class="flex flex-col items-center">
    <x-ui-modal wire:model="logoutModal" title="Are you sure?" separator>
        <div>Log out? This action can't be undo</div>
        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.logoutModal = false" />
            <x-ui-button label="Confirm" class="btn-primary" @click="window.location.href = '/logout'"/>
        </x-slot:actions>
    </x-ui-modal>

    <livewire:partials.header/>
    <x-gap/>
    <livewire:partials.bread-crumb display="{{$full_location['display']}}" route="{{$full_location['route']}}" icon_name="{{$full_location['icon_name']}}"/>
    <div class="card bg-base-100 shadow-xl w-9/12">
        <div class="flex w-full" x-data="{ order: true,edit:false }">
            <div class="card bg-base-100 p-10">
                <div class="flex flex-col items-center">
                    <img src="https://th.bing.com/th/id/OIP.kcaJsnMsMsFRdU6d1m2v6AHaHa?w=194&h=194&c=7&r=0&o=5&pid=1.7" class="w-32 h-32 bg-gray-300 rounded-full mb-4 shrink-0">
                    <h1 class="text-xl font-bold">{{$user_info->name}}</h1>
                    <p class="">{{$user_info->email}}</p>
                    <div class="mt-6 flex gap-4 justify-center flex-col" >
{{--                        <x-ui-button label="Order History" icon="o-clipboard-document-list" class="btn-primary"/>--}}
                        <button class="flex gap-1 items-center bg-primary hover:scale-105 duration-300 py-2 px-2 rounded" @click="window.location.href = '/cart'">
                            <x-ui-icon name="o-shopping-cart"></x-ui-icon>
                            Shopping Cart</button>
                        <button class="flex gap-1 items-center bg-primary hover:scale-105 duration-300 py-2 px-2 rounded" @click="order = true,edit=false">
                            <x-ui-icon name="o-clipboard-document-list"></x-ui-icon>
                            Order History</button>
                        <button class="flex gap-1 items-center bg-primary hover:scale-105 duration-300 py-2 px-2 rounded" @click="order = false,edit=true">
                            <x-ui-icon name="o-pencil-square"></x-ui-icon>
                            Edit Profile</button>
                        <button class="flex gap-1 items-center bg-red-500 hover:scale-105 duration-300 py-2 px-7 rounded" @click="$wire.logoutModal = true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                            </svg>
                            Log out</button>
                    </div>
                </div>
                <hr class="my-6 border-t border-gray-300">
                <div class="flex flex-col">
                    <span class="uppercase font-bold tracking-wider mb-2">More Infomation</span>
                    <ul>
                        <li class="mb-2">Joined for</li>
                    </ul>
                </div>
            </div>
            <div class="divider divider-horizontal"></div>
            <div class="flex card w-10/12" x-show="order" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-10 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-10 transform scale-90">

                <div class="px-3">
                    <div class="flex flex-col">
                        <span class="font-semibold text-2xl">Orders History</span>
                        <span class="">Check recent orders</span>
                    </div>

                    <hr class="my-3 border-t border-gray-300">
                    <div class="overflow-y-auto h-2/3 flex flex-col gap-4 p-4">
                        @foreach($orders_info as $order)
                            <div class="card card-side bg-gray-200 shadow-sm rounded-full">
                                <div class="card-body">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold">ID:##{{$order->cart_id}}</span>
                                        <span class="font-semibold">Number of items:{{$order->items?$order->items->count() : 0}}</span>
                                        <span class="font-semibold">Total:${{$order->items?$order->items->sum('subtotal') : 0}}</span>
                                        @if($order->status == 'pending')
                                            <span class="font-semibold">Status:<span class="badge badge-warning">Pending</span></span>
                                        @elseif($order->status == 'delivering')
                                            <span class="font-semibold">Status:<span class="badge badge-warning">Delivering</span></span>
                                        @elseif($order->status == 'delivered')
                                            <span class="font-semibold">Status:<span class="badge badge-warning">Delivered</span></span>
                                        @else
                                            <span class="font-semibold">Status:<span class="badge badge-warning">Canceled</span></span>
                                        @endif
                                        <button class="btn btn-primary">View Details</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <div class="flex card w-10/12 p-3" x-show="edit" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90">
                <div class="flex flex-col">
                    <span class="font-semibold text-2xl">Edit profile</span>
                    <span class="">Update your details information</span>
                </div>

                <hr class="my-3 border-t border-gray-300">
                <div class="card card-side bg-base-100 shadow-sm">

                    <div class="card-body ">
                        <span>Order Details</span>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-gap/>
    <x-footer/>
</div>
