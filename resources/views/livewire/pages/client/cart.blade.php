<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout("components.layouts.guest")]
#[Title("Shopping Cart")]
class extends Component {
    //
    use \Mary\Traits\Toast;
    public $full_location = ['display'=>'Cart','route'=>'cart','icon_name'=>"o-shopping-cart"];
    public bool $deleteModal = false;
    public bool $checkoutForm = false;
    public $item_to_del;
    public $active_cart_id;
    public $final_price;
    public $address;
    public $phone;
    public $active_user;

    protected $rules = [
        'address' => ['required','string','min:8'],
        'phone' => ['required','regex:/^(?:\+84|0)?[1-9]\d{8,9}$/']
    ];

    public function placeOrder($user_id,$cart_id)
    {
//        dd($user_id,$cart_id);
        $validated = $this->validate($this->rules);
        $this->updateOrderInfo($validated,$user_id,$cart_id);

    }

    protected function updateOrderInfo($validated_data,$user_id,$cart_id)
    {
//        update customer info first, phone and address field
        $customer = \App\Models\User::find($user_id);
        $customer->phone = $validated_data['phone'];
        $customer->address = $validated_data['address'];
        $customer->save();
//        update order status
        $order = \App\Models\Orders::find($cart_id);
        $order->status = 'pending';
        $order->save();
        $this->dispatch("resetCart");
        $this->checkoutForm = false;
        $this->success("Your order has been made!","Waiting for confirmation",position: 'toast-top toast-end');
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
                $this->dispatch("resetCart");
                $this->success("Quantity updated in cart successfully!");
            } else {
                $this->error("Failed to update quantity in cart.");
            }
        }elseif ($mode == 'decrement' && $item_to_update->cart_quantity > 1) {
            $item_to_update->cart_quantity -= 1;
            $item_to_update->subtotal = $based_price*($item_to_update->cart_quantity);
            // Check if the update was successful
            if ($item_to_update->save()) {
                $this->dispatch("resetCart");
                $this->success("Quantity updated in cart successfully!");
            } else {
                $this->error("Failed to update quantity in cart.");
            }
        }else {
            $this->deleteModal = true;
            $this->item_to_del = $item_id;
        }
    }
    public function loadCOF()
    {
        $order = \App\Models\Orders::with('items')->find($this->active_cart_id);
        $totalPrice = $order->items->sum('subtotal');
        $this->final_price = $totalPrice;
        $user = auth()->user();
        $this->active_user =$user->id;
        $this->address = $user->address;
        $this->phone = $user->phone;
//        this line of code would open the right drawer(checkoutForm), put it at the end because i want it to open when everything is loaded
        $this->checkoutForm = true;
    }
    public function deleteItem($id)
    {
//        dd($id);
        $item = \App\Models\CartItems::find($id);
        if ($item) {
            $item->delete();
            // The user with ID 1 has been deleted
            $this->dispatch("resetCart");
            $this->success("Item $id deleted successfully!",position: "toast-top toast-end");
            $this->reset();
        } else {
            $this->dispatch("resetCart");
            $this->error("Cannot delete that item. Please try again!",position: "toast-top toast-end");
        }
    }

    public function getCartItems()
    {
        $cart_id = $this->getCartID();
        $cart_items = \App\Models\CartItems::query()->where("cart_id",$cart_id)->get();
//        dd($cart_items);
        return $cart_items;
    }
    protected function getCartID(): int
    {
        // Get the authenticated user's ID
        $customer_id = auth()->user()->id;

        // Check if an order exists for the customer
        $order = \App\Models\Orders::where('customer_id', $customer_id)
            ->where('status', 'in cart')
            ->first();

        // If an order exists, return its cart_id
        if ($order) {
            $this->active_cart_id = $order->cart_id;
            return $order->cart_id;
        } else {
            // No order exists, create a new one
            return $this->createOrder($customer_id);
        }
    }

    protected function createOrder($cus_id): int
    {
        // Create a new order
        $order = \App\Models\Orders::create([
            'customer_id' => $cus_id
        ]);
        $this->active_cart_id = $order->cart_id;
        // Return the newly created order's cart_id
        return $order->cart_id;
    }
    public function with():array
    {
        return [
            "items" => $this->getCartItems()
        ];
    }
}; ?>

<div class="flex flex-col items-center h-screen">
    <x-ui-modal wire:model="deleteModal" title="Are you sure?" subtitle="Remove this product from cart">
        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.deleteModal = false" />
            <x-ui-button label="Delete" class="btn-error" wire:click="deleteItem({{$item_to_del}})"/>
        </x-slot:actions>
    </x-ui-modal>

    <livewire:partials.header/>
    <x-gap/>
    <livewire:partials.bread-crumb display="{{$full_location['display']}}" route="{{$full_location['route']}}" icon_name="{{$full_location['icon_name']}}"/>
    <x-gap/>
    <div class="card card-side shadow-xl w-10/12">
        <div class="flex flex-col p-5 w-full">
            <div class="self-start ml-14 flex items-center">
                <span class="font-bold text-3xl">Shopping Cart</span>
                <div class="badge badge-primary badge-lg rounded-full h-10 ml-5 w-10">{{$items->count()}}</div>
            </div>
            <div class="divider"></div>
            <div class="overflow-y-auto h-96">
                @if($items->count() == 0)
{{--                    <span class="text-4xl text-error">Your cart is empty!</span>--}}
            </div>
            <button class="btn btn-disabled" tabindex="-1" role="button" aria-disabled="true">Proceed to checkout</button>
                @else
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
            <button class="btn btn-wide w-full btn-primary" wire:click="loadCOF()" ><span wire:loading.class="loading loading-spinner loading-md"></span>Proceed to checkout</button>
                @endif

        </div>
{{--        <div class="card-body shadow-lg">--}}

{{--        </div>--}}
    </div>
    <x-gap/>
    <x-footer/>
    <x-ui-drawer
        wire:model="checkoutForm"
        title="Checkout"
        subtitle="Complete this form and double check your infomation"
        separator
        with-close-button
        class="w-11/12 lg:w-1/3"
        right
    >
        <div class="self-start mb-2 mt-2">
            <span class="font-semibold text-2xl">Order summary</span>
        </div>
        <div class="w-11/12 h-44 flex flex-col justify-between mb-10">
            <div class="flex justify-between">
                <span>Number of items</span>
                <span class="text-lg font-semibold text-semibold ">{{$items->count()}}</span>
            </div>
            <div class="flex-grow border-t border-gray-300"></div>
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span class="text-lg font-semibold text-semibold ">${{$final_price}}</span>
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
                <span class="text-lg font-semibold text-semibold ">${{$final_price}}</span>
            </div>
        </div>
        <label class="input flex items-center gap-2">
            <x-ui-icon name="m-globe-americas"/>
            <input type="text" class="grow border-0" placeholder="Enter your location" wire:model="address"/>
        </label>
        @error('address')
        <div class="text-red-600 font-semibold">{{ $message }}</div>
        @enderror
        <label class="input flex items-center gap-2 focus:border-0">
            <x-ui-icon name="o-phone"/>
            <input type="text" class="grow border-0" placeholder="Enter your phone number" wire:model="phone"/>
        </label>
        @error('phone')
        <div class="text-red-600 font-semibold">{{ $message }}</div>
        @enderror
        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.checkoutForm = false" />
            <x-ui-button label="Place order" class="btn-outline btn-primary" icon="o-check" wire:click="placeOrder({{$active_user ?? ''}},{{$active_cart_id}})"/>
        </x-slot:actions>
    </x-ui-drawer>
</div>
