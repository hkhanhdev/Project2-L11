<?php

use Livewire\Volt\Component;

new class extends Component {
    //
    public $display;
    public $route;
    public $icon_name;
    public function with():array
    {
//        $this->display = $this->full_location['display'];
//        $this->route = $this->full_location['route'];
        return [
            "route" => $this->route,
            'display' => $this->display,
            'icon_name' => $this->icon_name
        ];
    }
}; ?>

<div class=" breadcrumbs">
    <ul>
        <li>
            <span class="inline-flex gap-2 items-center">
                <x-ui-icon name="o-home"></x-ui-icon>
                <a href="/">Home</a>
            </span>
        </li>
        <li>
          <span class="inline-flex gap-2 items-center">
                <x-ui-icon name="{{$icon_name}}"></x-ui-icon>
                <a href="/{{$route}}">{{$display}}</a>
            </span>
        </li>
    </ul>
</div>
