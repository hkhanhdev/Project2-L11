<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\{Layout,Title};
new #[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class extends Component {


    public $chartTypes = [['value'=>'pie','label'=>'Pie'],['value'=>'bar','label'=>'Bar'],['value'=>'line','label'=>'Line']];
    protected function revenue()
    {

    }
    protected function productCount()
    {
        return \App\Models\Products::count();
    }
    protected function orderCount()
    {
        return \App\Models\Orders::count();
    }

    protected function userCount()
    {
        return User::count();
    }
    public array $myChart = [
        'type' => 'bar',
        'data' => [
            'labels' => ['Jan', 'Feb', 'Mar','April', 'May', 'June','July','August' ,'Sep', 'Oct','Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Revenue by month',
                    'data' => [12, 19, 3],
                ]
            ]
        ]

    ];

    protected function getReByMonth()
    {
        // Initialize an array to hold revenue values for each month
        $revenueValues = [];
        $revenueByMonth = \App\Models\Orders::getTotalRevenueByMY();
        // Fill the revenue values array with 0 for each month initially
        for ($i = 1; $i <= 12; $i++) {
            $revenueValues[$i] = 0;
        }

        // Map the revenue values to their corresponding months
        foreach ($revenueByMonth as $revenue) {
            // Extract the year and month from the result
            $year = $revenue->year;
            $month = $revenue->month;

            // Calculate the index of the month in the revenueValues array
            // For example, January would be at index 1, February at index 2, and so on
            $index = ($year - 2024) * 12 + $month;

            // Insert the revenue value into the corresponding month's index
            $revenueValues[$index] = $revenue->revenue;
        }
        return array_values($revenueValues);
    }
    public function with(): array
    {
        $this->myChart['data']['datasets'][0]['data'] = $this->getReByMonth();
        return [
            'totalRevenue' => \App\Models\Orders::getTotalRevenue(),
            'prdCount' => $this->productCount(),
            'orderCount' => $this->orderCount(),
            'userCount' => $this->userCount(),
            'chartTypes' => collect($this->chartTypes)
        ];
    }
}; ?>

@section("chart_js")
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection
<div>
    <!-- HEADER -->
    <x-ui-header title="Dashboard" separator progress-indicator/>
    <div class="w-full shadow stats">
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-ui-icon name="o-currency-dollar" class="w-10 h-10"/>
            </div>
            <div class="stat-title">Revenue</div>
            <div class="stat-value">{{$totalRevenue}}</div>
            <div class="stat-desc">Jan 1st - Feb 1st</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-ui-icon name="o-user-group" class="w-10 h-10"/>
            </div>
            <div class="stat-title">Users</div>
            <div class="stat-value">{{$userCount}}</div>
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
            <div class="stat-value">{{$orderCount}}</div>
            <div class="stat-desc text-error">↘︎ 90 (14%)</div>
        </div>
    </div>

    <div class="flex mt-10">
        <x-ui-chart wire:model="myChart" class="size-2/3"/>
    </div>

</div>

