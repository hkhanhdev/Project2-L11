<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;

new #[Layout("components.layouts.guest")]
#[Title("Profile")]
class extends Component {
    //
    use \Mary\Traits\Toast;
    public $full_location = ['display'=>'Profile','route'=>'profile','icon_name'=>"o-user"];

    public bool $logoutModal = false;
    public bool $editModal = false;
    public $current_user_id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $createdAt;

    protected $rules = [];

    public function validateCredentials()
    {
        $this->editModal = false;
        $this->setRules();
        $validated = $this->validate($this->rules);
        $user = \App\Models\User::find($this->current_user_id);
        $user->update($validated);
        sleep(1);
        $this->redirect(route("client_profile"));
        $this->success("Updated!",position: 'toast-top toast-end');
    }
    protected function setRules()
    {
        $this->rules = [
            'name' => ['required', 'string', 'max:50','min:1'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',Rule::unique('users', 'email')->ignore($this->current_user_id)],
            'address' => ['string', 'max:255'],
            'phone' =>['required','regex:/^(?:\+84|0)?[1-9]\d{8,9}$/',Rule::unique('users', 'phone')->ignore($this->current_user_id)],
        ];
    }
    public function getUserInfo()
    {
        $current_user = auth()->user();
        $this->createdAt = $this->getTime($current_user->created_at);
        $this->current_user_id = $current_user->id;
        $this->name = $current_user->name;
        $this->email = $current_user->email;
        $this->phone = $current_user->phone;
        $this->address = $current_user->address;
        return $current_user;
    }

    protected function getTime($created_time)
    {
        $createdAt = \Carbon\Carbon::parse($created_time);
        $now = \Carbon\Carbon::now();
        $timeDifference = $createdAt->diffForHumans($now);
        return $timeDifference;
    }
    public function with():array
    {
        return [
//            'user_info' => auth()->user(),
            'user_info' => $this->getUserInfo(),
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
        <div class="flex w-full" x-data="{ order: false,edit:true }">
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
                        <button class="flex gap-1 items-center bg-error hover:scale-105 duration-300 py-2 px-7 rounded" @click="$wire.logoutModal = true">
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
                        <li class="mb-2">Joined {{$createdAt}}</li>
                        @if($user_info->role == '0')
                            <li class="mb-2">Role:User/Customer</li>
                        @else
                            <li class="mb-2">Role:Administrator</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="divider divider-horizontal"></div>
            <div class="flex card w-10/12" x-show="order" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">

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
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">
                <div class="flex flex-col">
                    <span class="font-semibold text-2xl">Edit profile</span>
                    <span class="">Update your details information</span>
                </div>

                <hr class="my-2 border-t border-gray-300">
                <div class="card card-side">
                    <x-ui-modal wire:model="editModal" title="Are you sure?" subtitle="We'll update your profile" separator>
                        <div>Make sure your provided credentials are correct!</div>

                        <x-slot:actions>
                            <x-ui-button label="Cancel" @click="$wire.editModal = false" />
                            <x-ui-button label="Confirm" class="btn-primary" wire:click="validateCredentials" spinner/>
                        </x-slot:actions>
                    </x-ui-modal>

                    <div class="card-body gap-6">
                        <x-ui-input label="Username" icon="o-user" wire:model="name"/>
                        <x-ui-input label="Email" icon="o-envelope" wire:model="email"/>
                        <x-ui-input label="Phone"  icon="o-phone" wire:model="phone"/>
                        <x-ui-input label="Address"  icon="o-map-pin" wire:model="address"/>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary" @click="$wire.editModal = true">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-gap/>
    <x-footer/>
</div>
