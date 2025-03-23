<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\{Layout,Title};
new #[Layout('components.layouts.guest')]
#[Title('Supplements')]
class extends Component {

    public function with(): array
    {
        return [

        ];
    }
}; ?>

<div class="flex items-center flex-col">
    <livewire:partials.header/>
    <x-gap/>
    <livewire:partials.hero/>
    <x-gap/>
    <span class="text-4xl font-semibold">Our Products</span>
    <livewire:partials.products/>

{{--    <x-gap/>--}}
{{--    <livewire:partials.brands-carousel/>--}}
{{--    <x-gap/>--}}
{{--    <livewire:partials.cates-carousel/>--}}
    <x-gap/>
    <span class="text-4xl font-semibold">GALLERY</span>
    <livewire:partials.img-carousel/>
    <x-gap/>
    <livewire:partials.gallery/>
    <x-gap/>
    <x-footer/>
</div>

