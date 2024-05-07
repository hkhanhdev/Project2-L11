<?php

use Livewire\Volt\Component;

new class extends Component {
    //
    use \Mary\Traits\Toast;
    public $prd_id;


    public function processCart($id,$quantity,$total)
    {
        if (!\Illuminate\Support\Facades\Auth::user()) {
            $this->error("Please login as an authenticated user to process further!");
        }else {
//            dd($id,$quantity,$total);
            $cart_id = $this->getCartID();
            $this->addToCart($cart_id,$id,$quantity,$total);
        }
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
        // Return the newly created order's cart_id
        return $order->cart_id;
    }

    public function get_product($prd_id)
    {
        $product = \App\Models\Products::query()->where('id',$prd_id)->with(['brand','cate'])->first();
//        dd($product->quantity);
        return $product;
    }
    public function with():array
    {
        return [
            'product' => $this->get_product($this->prd_id)
        ];
    }
}; ?>

<div class="shadow-xl flex w-9/12">
    <div class="flex flex-col p-10">
        <div class="px-4 py-10 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.3)] relative flex justify-center w-96 bg-white">
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
            <button class="btn btn-wide btn-lg btn-primary hover:scale-105 text-primary-content" wire:click="processCart({{$product->id}},count,count*price)">Add to cart</button>
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
</div>
