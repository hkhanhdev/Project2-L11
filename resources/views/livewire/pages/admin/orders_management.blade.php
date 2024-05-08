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

    public int $step = 1;
    public string $search = '';
    public bool $addDrawer = false;
    public bool $updateOrder = false;
    public $status = 'in cart';
    public $active_cart_id = '';
    public string $search_product = '';

    public function next()
    {
        $this->step++;
    }
    public function prev()
    {
        if ($this->step == 1) {
            $this->warning("Cannot going back to previous step!",position: 'toast-bottom');
        }else{
            $this->step--;
        }
    }
    public function updateStatus()
    {
        $order = \App\Models\Orders::find($this->active_cart_id);
        $order->seller_id = auth()->user()->id;
        $order->status = $this->status;
        $order->save();
        $this->updateOrder = false;
        $this->success("Order status updated!",position: 'toast-bottom toast-end');
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
            <x-ui-button icon="o-plus" class="btn-primary" label="Add" @click="$wire.addDrawer = true"/>
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
                    <th>Seller</th>
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
                @foreach($orders as $order)
                    <tr class="bg-base-200">
                        <td>{{$order->cart_id}}</td>
                        <th>Item ID</th>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <td>{{$order->seller?$order->seller->name: ''}}</td>
                        <td>{{$order->name??$order->customer->name}}</td>
                        <td>{{$order->email??$order->customer->email}}</td>
                        <td>{{$order->phone??$order->customer->phone}}</td>
                        <td>{{$order->address??$order->customer->address}}</td>
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
{{--add drawer--}}
    <x-ui-drawer
        wire:model="addDrawer"
        title="Offline order"
        subtitle="Add new order for offline customers"
        separator
        with-close-button
        class="w-11/12 lg:w-1/2"
    >
        <x-ui-steps wire:model="step" class="border my-5 p-5">
            <x-ui-step step="1" text="Add items">
                <x-ui-input placeholder="Search by product ID" wire:model.live.debounce="search_product" clearable icon="o-magnifying-glass"/>
            </x-ui-step>
            <x-ui-step step="2" text="Payment">
                Payment step
            </x-ui-step>
            <x-ui-step step="3" text="Receive Product" class="bg-orange-500/20">
                Receive Product
            </x-ui-step>
        </x-ui-steps>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.addDrawer = false" />
            <x-ui-button label="Previous" wire:click="prev" spinner="prev"/>
            <x-ui-button label="Next" wire:click="next" spinner="next"/>
            <x-ui-button label="Confirm" class="btn-primary {{ $step!=3 ? 'btn-disabled' : '' }}" icon="o-check" />
        </x-slot:actions>
    </x-ui-drawer>
{{--    edit modal--}}
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
            <x-ui-button label="Cancel" @click="$wire.updateOrder=false" />
            <x-ui-button label="Update" class="btn-primary" wire:click="updateStatus"/>
        </x-slot:actions>
    </x-ui-modal>
</div>
