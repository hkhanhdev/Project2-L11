<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\{Layout,Title};
new #[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class extends Component {

    public function productCount()
    {
        return \App\Models\Products::count();
    }
    public function with(): array
    {
        return [
            'prdCount' => $this->productCount()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-ui-header title="Dashboard" separator progress-indicator/>
    <div class="w-full shadow stats">
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-ui-icon name="o-currency-dollar" class="w-10 h-10"/>
            </div>
            <div class="stat-title">Revenue</div>
            <div class="stat-value">310M</div>
            <div class="stat-desc">Jan 1st - Feb 1st</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-ui-icon name="o-user-group" class="w-10 h-10"/>
            </div>
            <div class="stat-title">Users</div>
            <div class="stat-value">4,200</div>
            <div class="stat-desc text-success">↗︎Stonk</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-ui-icon name="o-shopping-bag" class="w-10 h-10"/>
            </div>
            <div class="stat-title">Products</div>
            <div class="stat-value">{{$prdCount}}</div>
            <div class="stat-desc text-success">↘︎ 90 (14%)</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-ui-icon name="o-truck" class="w-10 h-10"/>
            </div>
            <div class="stat-title">Orders</div>
            <div class="stat-value">1,200</div>
            <div class="stat-desc text-error">↘︎ 90 (14%)</div>
        </div>
    </div>
</div>

