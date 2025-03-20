<?php

use Livewire\Volt\Component;

new class extends Component {
    use \Livewire\WithPagination, \Livewire\WithoutUrlPagination,\Mary\Traits\Toast;

    protected $listeners = ['apply_search','apply_reset'];

    public $filtered = [];

    public function apply_reset()
    {
        $this->reset();
    }
    public function get_products($query)
    {
        $products_query = \App\Models\Products::query();
        // Apply filters if the $filters array is not null
        if ($query !== null) {
            foreach ($query as $key => $value) {
                if ($key == 'name') {
                    $products_query->where($key, 'like', '%' . $value . '%');
                }else {
                    $products_query->where($key, 'like',$value);
                }
            }
        }
        return $products_query->paginate(10);
    }
    public function apply_search($query)
    {
        $this->reset();
        $this->filtered = $query;
    }
    public function with():array
    {
        return [
            "products"=>$this->get_products($this->filtered)
        ];
    }

}; ?>

<div class="flex justify-center font-sans w-10/12" id="products">
    <div class="flex flex-col items-center">
        <div class="mt-10 grid grid-rows-2 grid-flow-col gap-6">

            @forelse($products as $product)
{{--                @dd($products->product_detail)--}}
                <a href="/product_details/{{$product->id}}">
                    <div
                        class="w-72 bg-secondary text-secondary-content shadow-md rounded-xl duration-300 hover:scale-x-105 hover:shadow-xl" wire:loading.class="loading loading-bars loading-xs ">
                        <img
                            src="{{$product->cate->img_url}}"
                            alt="Product" class="h-72 w-72 object-cover rounded-t-xl"/>
                        <div class="px-4 py-3 w-72 ">
                            <p class="text-xl cursor-auto text-secondary-content">#00{{$product->id}}</p>

                            <p class="text-lg font-bold truncate block capitalize text-secondary-content">{{$product->name}}</p>

                            <span class="uppercase font-semibold"><span class="mr-1 uppercase text-secondary-content">BY </span>{{$product->brand->name}}</span>
                            <br>
                            {{--                            <span class="mr-3 uppercase text-primary-content">{{$product->cate->name}}</span>--}}

                            <div class="flex items-center ">
                                @if($product->details->min('price') == $product->details->max('price'))
                                    <p class="text-lg font-semibold cursor-auto my-3">${{$product->details->min('price')}}</p>
                                @else
                                    <p class="text-lg font-semibold cursor-auto my-3">${{$product->details->min('price') . " - " .$product->details->max('price')}}</p>
                                @endif
{{--                                <del>--}}
{{--                                    <p class="text-lg cursor-auto ml-30 text-red-500">$299</p>--}}
{{--                                </del>--}}
{{--                                <div class="ml-auto hover:scale-125 duration-300 lg:tooltip tooltip-success" data-tip="Add to cart" wire:click="addToCart({{$product}})">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"--}}
{{--                                         fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">--}}
{{--                                        <path fill-rule="evenodd"--}}
{{--                                              d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5z"/>--}}
{{--                                        <path--}}
{{--                                            d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/>--}}
{{--                                    </svg>--}}
{{--                                </div>--}}
{{--                                <button class="btn btn-outline ml-10">View Details</button>--}}
                                    <div class="ml-auto hover:scale-110 duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="25"
                                                                                           fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5z" />
                                            <path
                                                d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z" />
                                        </svg></div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-warning">We're updating our products....</p>
            @endforelse

        </div>
        @if(!empty($products))
            <div class="mt-5">{{$products->links()}}</div>
        @endif
    </div>

{{--<div>--}}
{{--    --}}{{--    newest product card--}}
{{--    <x-ui-card title="Name" subtitle="Category" class="bg-primary hover:scale-105 duration-200">--}}
{{--        <span class="text-primary-content">Brand</span>--}}
{{--        <x-slot:figure>--}}
{{--            <img src="https://picsum.photos/300/200" class=""/>--}}
{{--        </x-slot:figure>--}}
{{--        <x-slot:menu>--}}
{{--            <span>###ID</span>--}}
{{--        </x-slot:menu>--}}
{{--        <div class="flex">--}}
{{--            <span>Price</span>--}}
{{--            <span>Price</span>--}}
{{--            <div class="ml-auto hover:scale-110 duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="25"--}}
{{--                                                                   fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">--}}
{{--                    <path fill-rule="evenodd"--}}
{{--                          d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5z" />--}}
{{--                    <path--}}
{{--                        d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z" />--}}
{{--                </svg></div>--}}
{{--        </div>--}}
{{--    </x-ui-card>--}}
{{--</div>--}}

</div>

