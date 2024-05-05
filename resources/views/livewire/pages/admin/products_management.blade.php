<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\{Layout, Title};

new
#[Layout('components.layouts.admin')]
#[Title('Products Management')]
class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    protected $rules = [
        'prd_id' => ["required"],
        'prd_name' => ["required", "string", 'max:100'],
        'prd_cate' => ["required", "string", 'max:50'],
        'prd_brand' => ["required", "string", 'max:50'],
        'prd_size' => ["required", "string", 'max:50'],
        'prd_servings' => ["required", "int"],
        'prd_flavor' => ["required", "string", 'max:50'],
        'prd_price' => ["required", 'numeric'],
        'prd_quantity' => ["required", 'numeric'],
    ];
    public string $search = '';
//    public bool $filter_drawer = false;
    public bool $add_drawer = false;
    public bool $edit = false;

    public $prd_id;
    public $prd_name;
    public $prd_brand;
    public $prd_cate;
    public $prd_size;
    public $prd_servings;
    public $prd_flavor;
    public $prd_price;
    public $prd_quantity;

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom', timeout: 4000);
    }

    public function saveEdit(): void
    {
        $validated = $this->validate($this->rules);
        if (\App\Models\Categories::where("name", $validated['prd_cate'])->doesntExist()) {
            \App\Models\Categories::create(['name' => $validated['prd_cate']]);
        }
        if (\App\Models\Brands::where("name", $validated['prd_brand'])->doesntExist()) {
            \App\Models\Brands::create(['name' => $validated['prd_brand']]);
        }
        $cate_id = \App\Models\Categories::where("name", $validated['prd_cate'])->value('id');
        $brand_id = \App\Models\Brands::where("name", $validated['prd_brand'])->value('id');
        $product = \App\Models\Products::where("id", $validated['prd_id'])->update(
            [
                "name" => $validated['prd_name'],
                "brand_id" => $brand_id,
                "cate_id" => $cate_id,
                "size" => $validated['prd_size'],
                "flavor" => $validated['prd_flavor'],
                "servings" => $validated['prd_servings'],
                "price" => floatval($validated['prd_price']),
                "quantity" => $validated['prd_quantity'],
            ]
        );
        if ($product > 0) {
            // Update successful
            $this->reset();
            $this->success('Updated', position: 'toast-bottom', timeout: 4000);
        } else {
            // No rows were updated
            $this->reset();
            $this->error('Something is wrong.Please try again!', position: 'toast-bottom', timeout: 4000);
        }
    }

    public function saveAdd(): void
    {
        $validated = $this->validate($this->rules);
        if (\App\Models\Categories::where("name", $validated['prd_cate'])->doesntExist()) {
            \App\Models\Categories::create(['name' => $validated['prd_cate']]);
        }
        if (\App\Models\Brands::where("name", $validated['prd_brand'])->doesntExist()) {
            \App\Models\Brands::create(['name' => $validated['prd_brand']]);
        }
        $cate_id = \App\Models\Categories::where("name", $validated['prd_cate'])->value('id');
        $brand_id = \App\Models\Brands::where("name", $validated['prd_brand'])->value('id');
        \App\Models\Products::create(
            [
                "name" => $validated['prd_name'],
                "brand_id" => $brand_id,
                "cate_id" => $cate_id,
                "size" => $validated['prd_size'],
                "flavor" => $validated['prd_flavor'],
                "servings" => $validated['prd_servings'],
                "price" => floatval($validated['prd_price']),
                "quantity" => $validated['prd_quantity'],
            ]
        );
        $this->reset();
        $this->success('Product added!', position: 'toast-bottom', timeout: 4000);

    }

    // Delete action
    public function delete($id): void
    {
        $product = \App\Models\Products::where("id",$id)->update(
            [
                "status" => 'Hidden'
            ]
        );
        if ($product > 0) {
            // Update successful
            $this->reset();
            $this->warning('Cannot delete associated product,change product status to hidden instead', position: 'toast-bottom', timeout: 6000);
        } else {
            // No rows were updated
            $this->reset();
            $this->error('Something is wrong.Please try again!', position: 'toast-bottom', timeout: 4000);
        }
    }

    public function openEditModal($product)
    {
        $this->prd_id = $product['id'] ?? null;
        $this->prd_name = $product['name'] ?? null;
        $this->prd_brand = $product['brand']['name'] ?? null;
        $this->prd_cate = $product['cate']['name'] ?? null;
        $this->prd_size = $product['size'] ?? null;
        $this->prd_servings = $product['servings'] ?? null;
        $this->prd_flavor = $product['flavor'] ?? null;
        $this->prd_price = $product['price'] ?? null;
        $this->prd_quantity = $product['quantity'] ?? null;
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1 text-primary'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-96 text-primary'],
            ['key' => 'brand.name', 'label' => 'Brand', 'class' => 'w-48 text-primary'],
            ['key' => 'cate.name', 'label' => 'Category', 'class' => 'w-48 text-primary'],
            ['key' => 'size', 'label' => 'Size', 'class' => 'w-20 text-primary'],
            ['key' => 'flavor', 'label' => 'Flavor', 'class' => 'w-48 text-primary'],
            ['key' => 'servings', 'label' => 'Servings', 'class' => 'w-20 text-primary'],
            ['key' => 'price', 'label' => 'Price', 'class' => 'text-primary'],
            ['key' => 'quantity', 'label' => 'Quantity', 'class' => 'text-primary'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-36 text-primary'],
            ['key' => 'any', 'label' => ''],
        ];
    }

    public function products()
    {
//        $products = \App\Models\Products::paginate(10);
        $products = \App\Models\Products::where("name","LIKE","%$this->search%")->paginate(10);
        return $products;
    }

    public function with(): array
    {
        return [
            'products' => $this->products(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-ui-header title="Products Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-ui-input placeholder="Product name..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"/>
        </x-slot:middle>
        <x-slot:actions>
{{--            <x-ui-button label="Filters" @click="$wire.filter_drawer = true" responsive icon="o-funnel"/>--}}
            <x-ui-button icon="o-plus" class="btn-primary" label="Add" @click="$wire.add_drawer = true"/>
        </x-slot:actions>
    </x-ui-header>
    <!-- FILTER DRAWER -->
{{--    <x-ui-drawer wire:model="filter_drawer" title="Filters" right separator with-close-button class="lg:w-1/3">--}}
{{--        <x-ui-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass"--}}
{{--                    @keydown.enter="$wire.drawer = false"/>--}}

{{--        <x-slot:actions>--}}
{{--            <x-ui-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>--}}
{{--            <x-ui-button label="Done" icon="o-check" class="btn-primary" @click="$wire.filter_drawer = false"/>--}}
{{--        </x-slot:actions>--}}
{{--    </x-ui-drawer>--}}

    {{--        ADD DRAWER--}}
    <x-ui-drawer wire:model="add_drawer" title="Add Product" right separator with-close-button class="lg:w-1/3">
        <x-ui-form wire:submit="saveAdd">
            <x-ui-input label="Name" wire:model="prd_name"/>
            <x-ui-input label="Brand" wire:model="prd_brand"/>
            <x-ui-input label="Category" wire:model="prd_cate"/>
            <x-ui-input label="Size" wire:model="prd_size"/>
            <x-ui-input label="Flavor" wire:model="prd_flavor"/>
            <x-ui-input label="Price" wire:model="prd_price"/>
            <x-ui-input label="Servings" wire:model="prd_servings"/>
            <x-ui-input label="Quantity" wire:model="prd_quantity"/>
            <x-slot:actions>
                <x-ui-button label="Cancel" icon="o-x-mark" @click="$wire.add_drawer = false" spinner/>
                <x-ui-button label="Save" icon="o-check" class="btn-primary" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-drawer>
    <x-ui-card>
        <x-ui-table :headers="$headers" :rows="$products" with-pagination class="table-md">

            @scope('cell_status', $product)
                @if($product['status'] == 'Available' && $product['quantity'] != 0)
                <x-ui-badge value="{{$product['status']}}" class="bg-green-400" />
                @elseif($product['quantity'] == 0)
                <x-ui-badge value="Out of stock" class="badge-error" />
                @else
                <x-ui-badge value="Hidden" class="badge-warning" />
                @endif
            @endscope
            @scope('cell_any', $product)
            <x-ui-button icon="o-pencil-square" spinner class="btn-sm btn-warning"
                         @click="$wire.edit = true,$wire.openEditModal({{$product}})"/>
            @endscope

            @scope('actions', $product)

            <x-ui-button icon="o-trash" wire:click="delete({{ $product->id }})" spinner
                         class="btn-sm hover:btn-error hover:text-white"/>
            @endscope
        </x-ui-table>
    </x-ui-card>

    {{--    edit modal--}}
    <x-ui-modal wire:model="edit" title="Edit panel" subtitle="Change product infomation">
        <x-ui-form wire:submit="saveEdit">
            <x-ui-input label="Product ID" disabled wire:model="prd_id"/>
            <x-ui-input label="Name" wire:model="prd_name"/>
            <x-ui-input label="Brand" wire:model="prd_brand"/>
            <x-ui-input label="Category" wire:model="prd_cate"/>
            <x-ui-input label="Size" wire:model="prd_size"/>
            <x-ui-input label="Flavor" wire:model="prd_flavor"/>
            <x-ui-input label="Price" wire:model="prd_price"/>
            <x-ui-input label="Servings" wire:model="prd_servings"/>
            <x-ui-input label="Quantity" wire:model="prd_quantity"/>
            <x-slot:actions>
                <x-ui-button label="Cancel" @click="$wire.edit = false"/>
                <x-ui-button label="Save" class="btn-success" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-modal>
</div>
