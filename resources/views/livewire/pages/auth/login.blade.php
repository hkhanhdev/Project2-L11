<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Mary\Traits\Toast;

new #[Layout('components.layouts.guest')]
#[Title("Authentication")]
class extends Component
{
    use Toast;

    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if($this->form->isAdmin) {
            $this->success("Logged in! Now you could start managing your store.",position: 'toast-top toast-end');
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

        }else {
            $this->success("Logged in! Now you could use our services.",position: 'toast-top toast-end');
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);
        }
    }
}; ?>

{{--            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />--}}
{{--            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />--}}

<div class="flex flex-col justify-center items-center min-h-screen">
    <img src="https://www.svgrepo.com/show/156221/medicines.svg" alt="" class="size-32 mb-10">
    <x-ui-card title="Login" subtitle="Welcome, please sign in to see our magic stuffs" separator progress-indicator class="w-1/2 lg:px-10 sm:px-6 md:px-6">
        <x-ui-form wire:submit="login">
            <x-ui-input label="Email" wire:model="form.email" clearable />

            <x-ui-input label="Password" wire:model="form.password"  clearable type="password"/>
            <x-slot:actions>
                <div class="flex justify-between size-full">
                    <div class="form-control ">
                        <label class="label cursor-pointer">
                            <input type="checkbox" class="checkbox checkbox-primary"  wire:model="form.isAdmin"/>
                            <span class="ml-2 label-text">Login as an administrator account</span>
                        </label>
                    </div>
                    <div>
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 py-3 px-3" href="{{ route('register') }}" wire:navigate>
                            {{ __('Register your account here!') }}
                        </a>
                        <x-ui-button label="Login" class="btn-primary" type="submit" spinner="login" />
                    </div>
                </div>
            </x-slot:actions>
        </x-ui-form>

    </x-ui-card>
</div>
