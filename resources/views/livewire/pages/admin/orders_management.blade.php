<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\{Layout, Title};

new
#[Layout('components.layouts.admin')]
#[Title('Orders Management')]
class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    public string $search = '';
    public bool $updateOrder = false;
    public $status = 'in cart';
    public $active_cart_id = '';

    public function updateStatus()
    {
        $order = \App\Models\Orders::find($this->active_cart_id);
        $order->status = $this->status;
        $order->save();
        $this->updateOrder = false;
        $this->success("Order status updated!");
//        dd($this->status,$this->active_cart_id);
    }
    public function openUpdateModal($current_status,$active_cart_id)
    {
//        dd($current_status);
        $this->status = $current_status;
        $this->active_cart_id = $active_cart_id;
        $this->updateOrder = true;
    }

    public function orders()
    {
        $orders = \App\Models\Orders::where("cart_id","LIKE","%$this->search%")->paginate(10);
        return $orders;
    }

    public function with(): array
    {
        return [
            'orders' => $this->orders(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-ui-header title="Orders Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-ui-input placeholder="Search by order ID" wire:model.live.debounce="search" clearable icon="o-magnifying-glass"/>
        </x-slot:middle>
        <x-slot:actions>
{{--            <x-ui-button label="Filters" @click="$wire.filter_drawer = true" responsive icon="o-funnel"/>--}}
            <x-ui-button icon="o-plus" class="btn-primary" label="Add" @click="$wire.add_drawer = true"/>
        </x-slot:actions>
    </x-ui-header>
    <x-ui-card>
        <div class="overflow-x-auto">
            <table class="table">
                <!-- head -->
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Customer name</th>
                    <th>Customer email</th>
                    <th>Customer phone</th>
                    <th>Customer address</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <!-- order1 -->
                @foreach($orders as $order)
                    <tr class="bg-base-200">
                        <td>{{$order->cart_id}}</td>
                        <th>Item ID</th>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <td>{{$order->customer->name}}</td>
                        <td>{{$order->customer->email}}</td>
                        <td>{{$order->customer->phone}}</td>
                        <td>{{$order->customer->address}}</td>
                        <td>{{$order->items->sum('subtotal')}}</td>
                        @if($order->status == 'pending')
                            <td><span class="badge badge-warning">Pending</span></td>
                        @elseif($order->status == 'delivering')
                            <td><span class="badge badge-info">Delivering</span></td>
                        @elseif($order->status == 'delivered')
                            <td><span class="badge bg-green-400">Delivered</span></td>
                        @elseif($order->status == 'in cart')
                            <td><span class="badge badge-success">In Cart</span></td>
                        @else
                            <td><span class="badge badge-error">Canceled</span></td>
                        @endif
{{--                        {{dd($order->status)}}--}}
                        <td>
                            <button class="btn btn-sm btn-info" wire:click="openUpdateModal('{{$order->status}}','{{$order->cart_id}}')" >
                                Edit
                            </button>
                        </td>
                    </tr>
                    @foreach($order->items as $item)
                        <tr>
                            <th class="bg-base-200"></th>
                            <td >{{$item->item_id}}</td>
                            <td >{{$item->product_id}}</td>
                            <td >{{$item->cart_quantity}}</td>
                            <td >${{$item->subtotal}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach



                @endforeach

                </tbody>
            </table>
        </div>

    </x-ui-card>

    <x-ui-modal wire:model="updateOrder" title="Update order status" subtitle="" >
        <select class="select select-info w-full max-w-xs" wire:model="status">
            <option disabled selected>Status</option>
            <option value="in cart" {{ $status == 'in cart' ? 'disabled' : '' }}>In Cart</option>
            <option value="pending" {{ $status == 'pending' ? 'disabled' : '' }}>Pending</option>
            <option value="delivering" {{ $status == 'delivering' ? 'disabled' : '' }}>Delivering</option>
            <option value="delivered" {{ $status == 'delivered' ? 'disabled' : '' }}>Delivered</option>
            <option value="canceled" {{ $status == 'canceled' ? 'disabled' : '' }}>Canceled</option>
        </select>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.updateOrder = false" />
            <x-ui-button label="Update" class="btn-primary" wire:click="updateStatus"/>
        </x-slot:actions>
    </x-ui-modal>
</div>
