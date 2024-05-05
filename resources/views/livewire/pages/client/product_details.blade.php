<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout("components.layouts.guest")]
#[Title("Product Details")]
class extends Component {
    //
    public $full_location = ['display'=>'Products','route'=>'all_products','icon_name'=>"o-shopping-bag"];
    public $prd_id;

    public function mount($prd_id)
    {
        $this->prd_id = $prd_id;
    }
}; ?>

<div class="flex flex-col items-center">
    <livewire:partials.header/>
    <x-gap/>
    <livewire:partials.bread-crumb display="{{$full_location['display']}}" route="{{$full_location['route']}}" icon_name="{{$full_location['icon_name']}}"/>
    <x-gap/>
    <livewire:partials.product :prd_id="$prd_id"/>
    <x-gap/>
    <x-footer/>
</div>

