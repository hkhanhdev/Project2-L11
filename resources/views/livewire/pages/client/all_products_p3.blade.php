<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
new #[Layout("components.layouts.guest")]
#[Title("All Products")]
class extends Component {
    //
    public $full_location = ['display'=>'Products','route'=>'all_products','icon_name'=>"o-shopping-bag"];
    public $filtered_query = [];
    public $selectedName ;
    public $selectedBrand;
    public $selectedCate;

    public function reset_search(): void
    {
        $this->reset();
        $this->dispatch("apply_reset");
    }
    public function search()
    {
        $this->filtered_query = array_filter([
            'name' => $this->selectedName,
            'brand_id' => $this->selectedBrand,
            'cate_id' => $this->selectedCate
        ]);
//        dd($this->filtered_query);
        $this->dispatch("apply_search",$this->filtered_query);
    }
    public function brands()
    {
        return \App\Models\Brands::all();
    }

    public function cates()
    {
        return \App\Models\Categories::all();
    }
    public function with():array
    {
        return [
          "brands" => $this->brands(),
          "cates" => $this->cates()
        ];
    }
}; ?>

<div class="flex flex-col items-center">
    <livewire:partials.header/>
    <x-gap/>
    <livewire:partials.bread-crumb display="{{$full_location['display']}}" route="{{$full_location['route']}}" icon_name="{{$full_location['icon_name']}}"/>
    <x-gap/>
    <div class="flex justify-end w-10/12 gap-20">
        <x-ui-button label="Reset" icon-right="o-arrow-path" class="btn btn-warning mt-7" wire:click="reset_search()" spinner="reset_search"/>
        <div class="flex gap-6">
            {{--    search--}}
{{--            <x-ui-button label="Hello" icon-right="o-x-circle" class="btn btn-warning mt-7"/>--}}
            <x-ui-select
                label="Brand"
                :options="$brands"
                placeholder="Select a brand"
                placeholder-value="0" {{-- Set a value for placeholder. Default is `null` --}}
                wire:model.live="selectedBrand" />
            <x-ui-select
                label="Category"
                :options="$cates"
                placeholder="Select a category"
                placeholder-value="0" {{-- Set a value for placeholder. Default is `null` --}}
                wire:model="selectedCate" />
        </div>
        <x-ui-input label="Search products by name" wire:model.live="selectedName" placeholder="Product Name..." clearable/>
        <x-ui-button label="Apply" icon-right="s-magnifying-glass" class="btn btn-info mt-7" spinner="search" wire:click="search"/>
    </div>
{{--    <x-gap/>--}}
    <livewire:partials.products/>

    <x-gap/>
    <x-footer/>
</div>
