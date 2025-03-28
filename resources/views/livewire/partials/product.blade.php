<?php

use Livewire\Volt\Component;

new class extends Component {
    //
    use \Mary\Traits\Toast;
    public $prd;
    public $prd_details;
    public $details_id;
    public $prd_id;
    public $quantity = null;
    public $price = null;
    public $minPrice; // Minimum price across all variants
    public $maxPrice; // Maximum price across all variants
    public $count = 1;

    public $filters = [
        'size' => '',
        'flavor' => '',
        'servings' => '',
        // Add more fields here as needed (e.g., 'color' => '', 'brand' => '')
    ];
    public $availableOptions = [
        'size' => [],
        'flavor' => [],
        'servings' => [],
    ];

    // Field labels for display
    public $labels = [
        'size' => 'Size',
        'flavor' => 'Flavor',
        'servings' => 'Servings',
    ];

    public function reloadOptions() {
        $this->availableOptions= ['size' => [], 'flavor' => [], 'servings' => []];
        $this->filters = ['size' => '', 'flavor' => '', 'servings' => ''];
        $this->quantity = null;
        $this->price = null;
        $this->count = 1;

    }
    public function increment()
    {
        if ($this->price && $this->quantity && $this->count < $this->quantity) {
            $this->count++;
        }
    }

    public function decrement()
    {
        if ($this->price && $this->quantity && $this->count > 1) {
            $this->count--;
        }
    }
    public function processCart($id)
    {
        if (!\Illuminate\Support\Facades\Auth::user()) {
            $this->error("Please login as an authenticated user to process further!");
        }else {
//            dd($id,$quantity,$total);
            $cart_id = $this->getCartID();
            $totalPrice = $this->price ? $this->count * $this->price : 0;
            $this->addToCart($cart_id,$id,$this->details_id,$this->count,$totalPrice);
        }
    }
    protected function addToCart($cart_id,$product_id,$details_id,$quantity,$total)
    {
        // Check if the product already exists in the cart
        $existingCartItem = \App\Models\CartItems::where('order_id', $cart_id)
            ->where('product_id', $product_id)
            ->first();

        if ($existingCartItem) {
            // Product already exists in the cart, update quantity and subtotal
            $existingCartItem->cart_quantity += $quantity;
            $existingCartItem->subtotal += $total;

            // Check if the update was successful
            if ($existingCartItem->save()) {
                $this->dispatch("resetCart");
                $this->success("Quantity updated in cart successfully!");
            } else {
                $this->error("Failed to update quantity in cart.");
            }
        } else {
            // Product does not exist in the cart, insert a new cart item
            $cartItem = \App\Models\CartItems::create([
                'order_id' => $cart_id,
                'product_id' => $product_id,
                'product_details_id' => $details_id,
                'cart_quantity' => $quantity,
                'subtotal' => $total,
            ]);

            // Optionally, you can check if the insertion was successful
            if ($cartItem) {
                $this->dispatch("resetCart");
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
            return $order->id;
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
        return $order->id;
    }

    public function get_product($prd_id)
    {
        $product = \App\Models\Products::query()->where('id',$prd_id)->with(['brand','cate'])->first();
//        dd($product->quantity);
        return $product;
    }
    public function updateOptions(bool $isInit = false)
    {
        $filteredDetails = $this->prd_details;

        // Apply filters for each selected value
        foreach ($this->filters as $key => $value) {
            if ($value) {
                $filteredDetails = $filteredDetails->filter(function ($detail) use ($key, $value) {
                    // Handle servings as string since it's an integer in DB
                    $detailValue = $key === 'servings' ? (string) $detail->$key : $detail->$key;
                    return $detailValue === $value;
                });
            }
        }

        // Update available options for each field
        foreach ($this->availableOptions as $key => &$options) {
            $options = $filteredDetails->pluck($key)
                ->unique()
                ->map(function ($item) use ($key) {
                    return ['name' => $key === 'servings' ? (string) $item : $item];
                })
                ->values()
                ->toArray();
        }
        if (!$isInit) {
            if ($filteredDetails->count() == 1) { //When the only product left
                // Update price based on the filtered result
                $selectedProduct = $filteredDetails->first(); // Get the first matching product
                $this->price = $selectedProduct ? $selectedProduct->price : null; // Set price or null if no match
                $this->quantity = $selectedProduct ? $selectedProduct->quantity : null;
                $this->details_id = $selectedProduct->id;
//                dd($this->details_id);
            }
//            dd($this->price,$this->quantity);
        }

    }

    public function with():array
    {
        $product = $this->get_product($this->prd_id);
        $this->prd_details = $product->details;
        // Calculate initial min and max prices
        $this->minPrice = $this->prd_details->min('price');
        $this->maxPrice = $this->prd_details->max('price');

        $this->updateOptions(isInit: true);
        return [
            'product' => $product
        ];
    }
}; ?>

<div class="shadow-xl flex w-9/12">
{{--    <div class="flex flex-col p-10">--}}
{{--        <div class="px-4 py-10 rounded-md shadow-sm  relative flex justify-center w-96 bg-white">--}}
{{--            <img src="{{$product->cate->img_url}}" alt="Product" class="rounded object-cover" />--}}
{{--        </div>--}}
{{--        <div class="mt-6 flex flex-wrap flex-col justify-center mx-auto items-center" x-data="{ count: 1, price: {{$price}}, maxQuantity: {{$quantity??null}} }">--}}
{{--            <h2 class="text-2xl font-extrabold">{{$product->name}}</h2>--}}
{{--            <div class="flex gap-4 mt-2 justify-center">--}}
{{--                <p class="text-2xl font-bold" x-text="'$'+(count*price).toFixed(2)"></p>--}}
{{--            </div>--}}
{{--            <div class="my-2 ">--}}
{{--                <button x-on:click="count = count > 1 ? count - 1 : count" class="w-5 btn btn-error rounded-md">-</button>--}}
{{--                <span x-model="count" x-text="count"></span>--}}
{{--                <button x-on:click="count = count < maxQuantity ? count + 1 : count" class="w-5 btn btn-success rounded-md">+</button>--}}
{{--            </div>--}}
{{--            <div class="flex space-x-2 mb-2 justify-center items-center">--}}
{{--            </div>--}}
{{--            <button class="btn btn-wide btn-lg btn-primary hover:scale-105 text-primary-content" wire:click="processCart({{$product->id}},count,count*price)">Add to cart</button>--}}
{{--        </div>--}}

{{--    </div>--}}
    <div class="flex flex-col p-10">
        <div class="px-4 py-10 rounded-md shadow-sm relative flex justify-center w-96 bg-white">
            <img src="{{ $product->cate->img_url }}" alt="Product" class="rounded object-cover" />
        </div>
        <div class="mt-6 flex flex-wrap flex-col justify-center mx-auto items-center">
            <h2 class="text-2xl font-extrabold">{{ $product->name }}</h2>
            <div class="flex gap-4 mt-2 justify-center">
                <p class="text-2xl font-bold transition-opacity duration-300 ease-in-out"
                   >
                    @if($price != null)
                        ${{ number_format($count * $price, 2) }}
                    @else
                        ${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}
                    @endif
                </p>
            </div>
            <div class="my-2">
                <button
                    wire:click="decrement"
                    class="w-5 btn btn-error rounded-md"
                    @if(!$price || !$quantity) disabled @endif
                >-</button>
                <span class="px-4 transition-opacity duration-300 ease-in-out"
                      wire:transition>{{ $count }}</span>
                <button
                    wire:click="increment"
                    class="w-5 btn btn-success rounded-md"
                    @if(!$price || !$quantity || $count >= $quantity) disabled @endif
                >+</button>
            </div>
            <button
                wire:click="processCart({{ $product->id }})"
                class="btn btn-wide btn-lg btn-primary hover:scale-105 text-primary-content"
                @if(!$price || !$quantity) disabled @endif
            >
                Add to cart
            </button>
        </div>
    </div>
    <div class="divider divider-horizontal"></div>
    <div class="card-body">
        <div class="px-6">
            <h3 class="text-lg font-bold ">Details Information:</h3>
        </div>
        <div class="overflow-x-auto p-5">
            <table class="table table-zebra">
                <tbody>
                <tr>
                    <th>Product ID</th>
                    <td>#00{{$product->id}}</td>
                </tr>
                <tr>
                    <th>Brand</th>
                    <td>{{$product->brand->name}}</td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>{{$product->cate->name}}</td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td class="transition-opacity duration-300 ease-in-out" >
                        @if($price != null)
                            ${{ number_format($price, 2) }}
                        @else
                            ${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>Rate</th>
                    <td>
                        4.0/5
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="grid sm:grid-rows-2 mt-5 gap-5">
                <div class="grid md:grid-cols-3 gap-12" wire:key="dynamic-selects">
                    @foreach($filters as $key => $value)
                        <div>
                            <fieldset>
                                <legend class="fieldset-legend">{{ $labels[$key] }}</legend>
                                <select
                                    class="select select-primary"
                                    wire:model.defer="filters.{{ $key }}"
                                    wire:change="updateOptions"
                                >
                                    <option value="">Pick your {{ strtolower($labels[$key]) }}</option>
                                    @foreach($availableOptions[$key] as $option)
                                        <option value="{{ $option['name'] }}">{{ $option['name'] }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    @endforeach
                </div>
                <x-ui-button label="Reset options" wire:click="reloadOptions" icon-right="c-arrow-path" spinner class="btn-warning" />
            </div>

        </div>
        <div class="shadow-sm p-6">
            <h3 class="text-lg font-bold ">Reviews(50)</h3>
            <div class="grid md:grid-cols-2 gap-12 mt-6">
                <div>
                    <div class="flex items-start">
                        <img src="https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/5404890a-6524-4910-a6cf-f6c74c2a69d7/dh852mv-d7802065-f983-4d9f-843e-755a4fe7b8cd.jpg/v1/fill/w_894,h_894,q_70,strp/jane_doe_by_yinzersteel_dh852mv-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9MTAyNCIsInBhdGgiOiJcL2ZcLzU0MDQ4OTBhLTY1MjQtNDkxMC1hNmNmLWY2Yzc0YzJhNjlkN1wvZGg4NTJtdi1kNzgwMjA2NS1mOTgzLTRkOWYtODQzZS03NTVhNGZlN2I4Y2QuanBnIiwid2lkdGgiOiI8PTEwMjQifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6aW1hZ2Uub3BlcmF0aW9ucyJdfQ.Ae1QCCfZST5ZpMX2wo_IyupDqLZWHnCyuWmBVhdQHho" class="w-12 h-12 rounded-full border-2 border-white" />
                        <div class="ml-3">
                            <h4 class="text-sm font-bold ">Jane Doe</h4>
                            <div class="flex space-x-1 mt-1">
                                <svg class="w-4 fill-primary" viewBox="0 0 14 13" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                </svg>
                                <svg class="w-4 fill-primary" viewBox="0 0 14 13" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                                </svg>
                                <svg class="w-4 fill-primary" viewBox="0 0 14 13" fill="none"
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
                                <p class="text-xs !ml-2 font-semibold ">2 minutes ago</p>
                            </div>
                            <p class="text-sm mt-4 ">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua.</p>
                        </div>
                    </div>
{{--                    <button type="button" class="w-full mt-10 px-4 py-2.5 bg-transparent hover:bg-gray-50 border border-[#333]  font-bold rounded">Read all reviews</button>--}}
                </div>
                <div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <p class="text-sm  font-bold">5.0</p>
                            <svg class="w-5 fill-primary ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                {{--fill-[#333]--}}
                                <path
                                    d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                            </svg>
                            <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                <div class="w-2/3 h-full rounded bg-primary"></div>
                            </div>
                            <p class="text-sm  font-bold ml-3">69%</p>
                        </div>
                        <div class="flex items-center">
                            <p class="text-sm font-bold">4.0</p>
                            <svg class="w-5 fill-primary ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                            </svg>
                            <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                <div class="w-1/3 h-full rounded bg-primary"></div>
                            </div>
                            <p class="text-sm  font-bold ml-3">33%</p>
                        </div>
                        <div class="flex items-center">
                            <p class="text-sm  font-bold">3.0</p>
                            <svg class="w-5 fill-primary ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                            </svg>
                            <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                <div class="w-1/6 h-full rounded bg-primary"></div>
                            </div>
                            <p class="text-sm  font-bold ml-3">16%</p>
                        </div>
                        <div class="flex items-center">
                            <p class="text-sm  font-bold">2.0</p>
                            <svg class="w-5 fill-primary ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                            </svg>
                            <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                <div class="w-1/12 h-full rounded bg-primary"></div>
                            </div>
                            <p class="text-sm  font-bold ml-3">8%</p>
                        </div>
                        <div class="flex items-center">
                            <p class="text-sm  font-bold">1.0</p>
                            <svg class="w-5 fill-primary ml-1" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7 0L9.4687 3.60213L13.6574 4.83688L10.9944 8.29787L11.1145 12.6631L7 11.2L2.8855 12.6631L3.00556 8.29787L0.342604 4.83688L4.5313 3.60213L7 0Z" />
                            </svg>
                            <div class="bg-gray-400 rounded w-full h-2 ml-3">
                                <div class="w-[6%] h-full rounded bg-primary"></div>
                            </div>
                            <p class="text-sm  font-bold ml-3">6%</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <button type="button" class="w-full mt-10 px-4 py-2.5 bg-transparent hover:bg-gray-50 border border-[#333]  font-bold rounded">Read all reviews</button>
            </div>
        </div>
    </div>
</div>
