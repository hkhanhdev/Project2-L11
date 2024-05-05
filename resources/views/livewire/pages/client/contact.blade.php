<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
new #[Layout("components.layouts.guest")]
#[Title("Contact")]
class extends Component {
    use \Mary\Traits\Toast;
    public $where;
    public $full_location = ['display'=>'Contact','route'=>'contact','icon_name'=>"o-phone"];

    public function sendMessage()
    {
        $this->success("Sent contact information successfully","We'll contact you ASAP!",timeout: 4000, position: 'toast-bottom');
    }
    public function Home()
    {
        $this->warning("Navigating to home",timeout: 1000, position: 'toast-top toast-end');
        return $this->redirectIntended(navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col justify-center items-center sm:py-12">
    <livewire:partials.bread-crumb display="{{$full_location['display']}}" route="{{$full_location['route']}}" icon_name="{{$full_location['icon_name']}}"/>
    <x-gap/>
    <div class="relative sm:max-w-xl sm:mx-auto">
        <div class="text-white relative px-4 bg-secondary shadow-lg sm:rounded-3xl sm:p-20">
            <button wire:click="Home">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 hover:scale-150 duration-200">
                    <path fill-rule="evenodd" d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>
            </button>
            <div class="text-center pb-6">
                <h1 class="text-3xl">Contact Us!</h1>

                <p>Fill up the form below to send us a message.</p>
            </div>
            <form>
                <input
                    class="shadow mb-4 appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" placeholder="Name" name="name">
                <input
                    class="shadow mb-4 appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="email" placeholder="Email" name="email">

                <input
                    class="shadow mb-4 appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" placeholder="Subject" name="_subject">

                <textarea
                    class="shadow mb-4 min-h-0 appearance-none border rounded h-64 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" placeholder="Type your message here..." name="message" style="height: 121px;"></textarea>

                <div class="flex justify-end">
                    <x-ui-button label="Send" class="bg-accent" wire:click="sendMessage" spinner="sendMessage"/>
                </div>

            </form>
        </div>
    </div>
</div>


