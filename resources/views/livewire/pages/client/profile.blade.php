<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout("components.layouts.guest")]
#[Title("Profile")]
class extends Component {
    //
    public $full_location = ['display'=>'Profile','route'=>'profile','icon_name'=>"o-user"];
    public string $group = 'group2';
}; ?>

<div class="flex flex-col items-center">
    <livewire:partials.header/>
    <x-gap/>
    <livewire:partials.bread-crumb display="{{$full_location['display']}}" route="{{$full_location['route']}}" icon_name="{{$full_location['icon_name']}}"/>
    <div class="card bg-base-100 shadow-xl">
        <div class="flex w-full">
            <div class="card bg-base-100 p-10">
                <div class="flex flex-col items-center">
                    <img src="https://th.bing.com/th/id/OIP.kcaJsnMsMsFRdU6d1m2v6AHaHa?w=194&h=194&c=7&r=0&o=5&pid=1.7" class="w-32 h-32 bg-gray-300 rounded-full mb-4 shrink-0">
                    <h1 class="text-xl font-bold">User Name</h1>
                    <p class="">Email</p>
                    <div class="mt-6 flex gap-4 justify-center flex-col">
                        <x-ui-button label="Cart" icon="" class="btn-primary"/>
                        <button class="flex gap-1 items-center bg-red-500 hover:scale-110 hover:bg-red-700 duration-300 text-white py-2 px-4 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                            </svg>
                            Log out</button>
                    </div>
                </div>
                <hr class="my-6 border-t border-gray-300">
                <div class="flex flex-col">
                    <span class="text-gray-700 uppercase font-bold tracking-wider mb-2">More Infomation</span>
                    <ul>
                        <li class="mb-2">JavaScript</li>

                    </ul>
                </div>
            </div>
            <div class="divider divider-horizontal "></div>
            <div class="flex card">
                <div class="flex card">
                    {{--                            title--}}
                    <div class="flex flex-col">
                        <span class="font-semibold text-2xl">Orders History</span>
                        <span class="">Check recent purchases </span>
                    </div>

                    <hr class="my-3 border-t border-gray-300">

                    <div class="collapse bg-base-200">
                        <input type="radio" name="my-accordion-1" checked="checked" />
                        <div class="collapse-title text-xl font-medium">
                            Click to open this one and close others
                        </div>
                        <div class="collapse-content">
                            <p>hello</p>
                        </div>
                    </div>
                    <div class="collapse bg-base-200">
                        <input type="radio" name="my-accordion-1" />
                        <div class="collapse-title text-xl font-medium">
                            Click to open this one and close others
                        </div>
                        <div class="collapse-content">
                            <p>hello</p>
                        </div>
                    </div>
                    <div class="collapse bg-base-200">
                        <input type="radio" name="my-accordion-1" />
                        <div class="collapse-title text-xl font-medium">
                            Click to open this one and close others
                        </div>
                        <div class="collapse-content">
                            <p>hello</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
