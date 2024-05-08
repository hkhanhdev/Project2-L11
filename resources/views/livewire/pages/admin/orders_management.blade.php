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
    public $active_cart_id ;
    public string $product_query = '';
    public $name;
    public $email;
    public $phone;
    public $address;
    protected $rules = [
        'name' => ['max:50','string'],
        'email' => ['max:50','string'],
        'phone' => ['max:50','string'],
        'address' => ['max:100','string'],
    ];


    public function confirmOrder()
    {
        $current_cart_id = $this->getCartID();
        $order = \App\Models\Orders::find($current_cart_id);
        $order->update(['name'=>$this->name,'email'=>$this->email,'phone'=>$this->phone,'address'=>$this->address,'status'=>'delivered']);
        $this->success("Order $order->cart_id made!",position: 'toast-bottom');
    }
    public function processingCart($product_id,$cart_quan,$subtotal)
    {
//        step1 check if customer cart exist(if not create one with only seller id field)
//        step2 insert items to newly created cart(CartItems)
        $current_cart_id = $this->getCartID();
        $this->active_cart_id = $current_cart_id;
        $this->addToCart($current_cart_id,$product_id,$cart_quan,$subtotal);


    }

    protected function addToCart($cart_id,$product_id,$quantity,$total)
    {
        // Check if the product already exists in the cart
        $existingCartItem = \App\Models\CartItems::where('cart_id', $cart_id)
            ->where('product_id', $product_id)
            ->first();

        if ($existingCartItem) {
            // Product already exists in the cart, update quantity and subtotal
            $existingCartItem->cart_quantity += $quantity;
            $existingCartItem->subtotal += $total;

            // Check if the update was successful
            if ($existingCartItem->save()) {
                $this->success("Quantity updated in cart successfully!");
            } else {
                $this->error("Failed to update quantity in cart.");
            }
        } else {
            // Product does not exist in the cart, insert a new cart item
            $cartItem = \App\Models\CartItems::create([
                'cart_id' => $cart_id,
                'product_id' => $product_id,
                'cart_quantity' => $quantity,
                'subtotal' => $total,
            ]);
            // Optionally, you can check if the insertion was successful
            if ($cartItem) {
                $this->success("Item added to cart successfully!");
            } else {
                $this->error("Failed to add item to cart.");
            }
        }
    }

    protected function getCartID():int
    {
        $seller_id = auth()->user()->id;
        $order = \App\Models\Orders::query()->where(['status'=>'in cart','seller_id' => $seller_id])->get('cart_id')->first();

        if($order == null) {
            $new_order = \App\Models\Orders::create(['seller_id'=>$seller_id]);
            return $new_order->cart_id;
        }else {
            return $order->cart_id;
        }
    }

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

    public function search_product()
    {
        $product = \App\Models\Products::find($this->product_query);
        if ($product) {
            // Record found
            return $product;
        } else {
            // No record found
            return null;
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

    protected function getCartItems()
    {
        $cart_id = $this->getCartID();
        $cart_items = \App\Models\CartItems::query()->where("cart_id",$cart_id)->get();
        return $cart_items;
    }
    public function updateItem($item_id,$based_price,$mode)
    {
        // Check if the product already exists in the cart
        $item_to_update = \App\Models\CartItems::where('item_id', $item_id)
            ->first();

        if ($mode == 'increment' && $item_to_update->cart_quantity >= 1) {
            // Product already exists in the cart, update quantity and subtotal
            $item_to_update->cart_quantity += 1;
            $item_to_update->subtotal = $based_price*($item_to_update->cart_quantity);

            // Check if the update was successful
            if ($item_to_update->save()) {
                $this->success("Quantity updated in cart successfully!");
            } else {
                $this->error("Failed to update quantity in cart.");
            }
        }elseif ($mode == 'decrement' && $item_to_update->cart_quantity > 1) {
            $item_to_update->cart_quantity -= 1;
            $item_to_update->subtotal = $based_price*($item_to_update->cart_quantity);
            // Check if the update was successful
            if ($item_to_update->save()) {
                $this->success("Quantity updated in cart successfully!");
            } else {
                $this->error("Failed to update quantity in cart.");
            }
        }else {
            $this->deleteItem($item_to_update->item_id);
        }
    }
    public function deleteItem($id)
    {
        $item = \App\Models\CartItems::find($id);
        if ($item) {
            $item->delete();
            $this->success("Item $id deleted successfully!",position: "toast-top toast-end");
        } else {
            $this->error("Cannot delete that item. Please try again!",position: "toast-top toast-end");
        }
    }
    public function orders()
    {
        $orders = \App\Models\Orders::where("cart_id","LIKE","%$this->search%")->paginate(3);
        return $orders;
    }

    public function with(): array
    {
        return [
            'orders' => $this->orders(),
            'product' => $this->search_product(),
            'items' => $this->getCartItems()
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
                        <td>{{$order->name??$order->customer->name??''}}</td>
                        <td>{{$order->email??$order->customer->email??''}}</td>
                        <td>{{$order->phone??$order->customer->phone??''}}</td>
                        <td>{{$order->address??$order->customer->address??''}}</td>
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
        class="w-11/12 lg:w-10/12"
    >
        <x-ui-steps wire:model="step" class="border my-5 p-5">
            <x-ui-step step="1" text="Add items">
                <x-ui-input placeholder="Search by product ID" wire:model.live.debounce="product_query" clearable icon="o-magnifying-glass"/>
                <div class="mockup-browser border border-base-300 mt-3">
                    <div class="mockup-browser-toolbar">
                        <div class="input border border-base-300 w-96">https://supplements.com/product_id={{$product_query?? ''}}</div>
                    </div>
                    <div class="flex justify-center px-4 py-10 border-t border-base-300">
                        @if(is_null($product))
                            <div class="flex flex-col gap-4 w-96">
                                <div class="skeleton h-32 w-full"></div>
                                <div class="skeleton h-4 w-28"></div>
                                <div class="skeleton h-4 w-full"></div>
                                <div class="skeleton h-4 w-full"></div>
                            </div>
                        @else
                            <div class="flex flex-col ">
                                <div class="px-4 py-10 rounded-xl relative flex justify-center w-96 bg-white">
                                    <img src="https://th.bing.com/th/id/OIP.-lcAsgnii8chGPixD71CRQHaHa?rs=1&pid=ImgDetMain" alt="Product" class="rounded object-cover" />
                                </div>
                                <div class="mt-6 flex flex-wrap flex-col justify-center mx-auto items-center" x-data="{ count: 1, price: {{$product->price}}, maxQuantity: {{$product->quantity}} }">
                                    <h2 class="text-2xl font-extrabold text-[#333]">{{$product->name}}</h2>
                                    <div class="flex gap-4 mt-2 justify-center">
                                        <p class="text-[#333] text-2xl font-bold" x-text="'$'+(count*price).toFixed(2)"></p>
                                    </div>
                                    <div class="my-2">
                                        <button x-on:click="count = count > 1 ? count - 1 : count" class="w-10 bg-red-400 rounded-md">-</button>
                                        <span x-model="count" x-text="count"></span>
                                        <button x-on:click="count = count < maxQuantity ? count + 1 : count" class="w-10 bg-blue-400 rounded-md">+</button>
                                    </div>
                                    <div class="flex space-x-2 mb-2 justify-center items-center">
                                    </div>
                                    <button class="btn btn-wide btn-lg btn-primary hover:scale-105 text-primary-content" wire:click="processingCart({{$product->id}},count,count*price)">Add to cart</button>
                                </div>
                            </div>
                            <div class="divider divider-horizontal"></div>
                            <div class="card-body">
                                <div class="px-6">
                                    <h3 class="text-lg font-bold text-[#333]">Product information:</h3>
                                </div>
                                <div class="overflow-x-auto p-5">
                                    <table class="table table-zebra">
                                        <tbody>
                                        <!-- row 1 -->
                                        <tr>
                                            <th>Product ID</th>
                                            <td>#00{{$product->id}}</td>
                                        </tr>
                                        <tr>
                                            <th>Brand</th>
                                            <td>{{$product->brand->name}}</td>
                                        </tr>
                                        <!-- row 2 -->
                                        <tr>
                                            <th>Category</th>
                                            <td>{{$product->cate->name}}</td>
                                        </tr>
                                        <!-- row 3 -->
                                        <tr>
                                            <th>Size</th>
                                            <td>{{$product->size}}</td>
                                        </tr>
                                        <tr>
                                            <th>Flavor</th>
                                            <td>{{$product->flavor}}</td>
                                        </tr>
                                        <tr>
                                            <th>Servings</th>
                                            <td>{{$product->servings}}</td>
                                        </tr>
                                        <tr>
                                            <th>Price</th>
                                            <td>${{$product->price}}</td>
                                        </tr>
                                        <tr>
                                            <th>Rate</th>
                                            <td>
                                                4.5/5
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="shadow-sm p-6">
                                    <h3 class="text-lg font-bold text-[#333]">Reviews(50)</h3>
                                    <div class="grid md:grid-cols-2 gap-12 mt-6">
                                        <div>
                                            <div class="space-y-3">
                                                <div class="flex items-center">
                                                    <p class="text-sm text-[#333] font-bold">5.0</p>
                                                    <svg class="w-5 fill-[#333] ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                    </svg>
                                                    <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                                        <div class="w-2/3 h-full rounded bg-[#333]"></div>
                                                    </div>
                                                    <p class="text-sm text-[#333] font-bold ml-3">69%</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <p class="text-sm text-[#333] font-bold">4.0</p>
                                                    <svg class="w-5 fill-[#333] ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                    </svg>
                                                    <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                                        <div class="w-1/3 h-full rounded bg-[#333]"></div>
                                                    </div>
                                                    <p class="text-sm text-[#333] font-bold ml-3">33%</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <p class="text-sm text-[#333] font-bold">3.0</p>
                                                    <svg class="w-5 fill-[#333] ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                    </svg>
                                                    <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                                        <div class="w-1/6 h-full rounded bg-[#333]"></div>
                                                    </div>
                                                    <p class="text-sm text-[#333] font-bold ml-3">16%</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <p class="text-sm text-[#333] font-bold">2.0</p>
                                                    <svg class="w-5 fill-[#333] ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                    </svg>
                                                    <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                                        <div class="w-1/12 h-full rounded bg-[#333]"></div>
                                                    </div>
                                                    <p class="text-sm text-[#333] font-bold ml-3">8%</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <p class="text-sm text-[#333] font-bold">1.0</p>
                                                    <svg class="w-5 fill-[#333] ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                    </svg>
                                                    <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                                        <div class="w-[6%] h-full rounded bg-[#333]"></div>
                                                    </div>
                                                    <p class="text-sm text-[#333] font-bold ml-3">6%</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="flex items-start">
                                                <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg" class="w-12 h-12 rounded-full border-2 border-white" />
                                                <div class="ml-3">
                                                    <h4 class="text-sm font-bold text-[#333]">Jane Doe</h4>
                                                    <div class="flex space-x-1 mt-1">
                                                        <svg class="w-4 fill-[#333]" viewBox="0 0 14 13" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                        </svg>
                                                        <svg class="w-4 fill-[#333]" viewBox="0 0 14 13" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                        </svg>
                                                        <svg class="w-4 fill-[#333]" viewBox="0 0 14 13" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                        </svg>
                                                        <svg class="w-4 fill-[#CED5D8]" viewBox="0 0 14 13" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                        </svg>
                                                        <svg class="w-4 fill-[#CED5D8]" viewBox="0 0 14 13" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                                        </svg>
                                                        <p class="text-xs !ml-2 font-semibold text-[#333]">2 seconds ago</p>
                                                    </div>
                                                    <p class="text-sm mt-4 text-[#333]">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua.</p>
                                                </div>
                                            </div>
                                            <button type="button" class="w-full mt-10 px-4 py-2.5 bg-transparent hover:bg-gray-50 border border-[#333] text-[#333] font-bold rounded">Read all reviews</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui-step>
            <x-ui-step step="2" text="Cart">
                <div class="overflow-y-auto h-96">
                    <table class="table table-lg">
                        <!-- head -->
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Flavor</th>
                            <th>Size</th>
                            <th>Servings</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr x-data="{ count: {{$item->cart_quantity}},price:{{$item->product->price}}}">
                                <td>##{{$item->item_id}}</td>
                                <td><img class="h-16 w-16 mr-4" src="https://via.placeholder.com/150" alt="Product image"></td>
                                <td class="w-48">{{$item->product->name}}</td>
                                <td>{{$item->product->brand->name}}</td>
                                <td>{{$item->product->cate->name}}</td>
                                <td>{{$item->product->price}}</td>
                                <td>{{$item->product->flavor}}</td>
                                <td>{{$item->product->size}}</td>
                                <td>{{$item->product->servings}}</td>
                                <td>
                                    <div class="flex">
                                        <button class="btn btn-outline btn-error btn-xs" x-on:click="count = count > 1 ? count-1 : count"
                                                wire:click="updateItem({{$item->item_id}},price,'decrement')" wire:loading.class="loading loading-spinner btn-disabled"
                                        >-</button>
                                        <span x-model="count" x-text="{{$item->cart_quantity}}" class="mx-2"></span>
                                        <button class="btn btn-outline btn-success btn-xs" x-on:click="count++"
                                                wire:click="updateItem({{$item->item_id}},price,'increment')" wire:loading.class="loading loading-spinner btn-disabled"
                                        >+</button>
                                    </div>
                                </td>
                                <td>${{$item->subtotal}}</td>
                                <td></td>
                                <th>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hover:scale-125 duration-300" wire:click="deleteItem({{$item->item_id}})" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </th>
                            </tr>
                        @empty
                            <tr>
                                <td class="col-span-2">Your cart is empty!</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui-step>
            <x-ui-step step="3" text="Checkout" data-content="✓" class="">
                <div class="self-start mb-2 mt-2">
                    <span class="font-semibold text-2xl">Order summary</span>
                </div>
                <div class="flex flex-col justify-between mb-10">
                    <div class="flex justify-between">
                        <span>Number of items</span>
                        <span class="text-lg font-semibold text-semibold ">{{$items->count()}}</span>
                    </div>
                    <div class="flex-grow border-t border-gray-300"></div>
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="text-lg font-semibold text-semibold ">${{$items->sum('subtotal')}}</span>
                    </div>
                    <div class="flex-grow border-t border-gray-300"></div>
                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span class="text-lg font-semibold text-semibold line-through">$10</span>
                    </div>
                    <div class="flex-grow border-t border-gray-300"></div>
                    <div class="flex justify-between">
                        <span>Tax estimate</span>
                        <span class="text-lg font-semibold text-semibold line-through">$2</span>
                    </div>
                    <div class="flex-grow border-t border-gray-300"></div>
                    <div class="flex justify-between">
                        <span class="text-lg font-semibold text-semibold underline">Total</span>
                        <span class="text-lg font-semibold text-semibold ">${{$items->sum('subtotal')}}</span>
                    </div>
                </div>
                <label class="input flex items-center gap-2">
                    <x-ui-icon name="o-user"/>
                    <input type="text" class="grow border-0" placeholder="Enter customer's name" wire:model="name"/>
                </label>
                @error('name')
                <div class="text-red-600 font-semibold">{{ $message }}</div>
                @enderror
                <label class="input flex items-center gap-2">
                    <x-ui-icon name="o-envelope"/>
                    <input type="text" class="grow border-0" placeholder="Enter customer's email" wire:model="email"/>
                </label>
                @error('email')
                <div class="text-red-600 font-semibold">{{ $message }}</div>
                @enderror
                <label class="input flex items-center gap-2">
                    <x-ui-icon name="m-globe-americas"/>
                    <input type="text" class="grow border-0" placeholder="Enter customer's location" wire:model="address"/>
                </label>
                @error('address')
                <div class="text-red-600 font-semibold">{{ $message }}</div>
                @enderror
                <label class="input flex items-center gap-2 focus:border-0">
                    <x-ui-icon name="o-phone"/>
                    <input type="text" class="grow border-0" placeholder="Enter customer's phone number" wire:model="phone"/>
                </label>
                @error('phone')
                <div class="text-red-600 font-semibold">{{ $message }}</div>
                @enderror
            </x-ui-step>
        </x-ui-steps>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.addDrawer = false" />
            <x-ui-button label="Previous" wire:click="prev" spinner="prev"/>
            <x-ui-button label="Next" wire:click="next" spinner="next"/>
            <x-ui-button label="Confirm" class="btn-primary {{ $step!=3 ? 'btn-disabled' : '' }}" icon="o-check" wire:click="confirmOrder"/>
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
