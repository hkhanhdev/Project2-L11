<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Mary\Traits\Toast;

new #[Layout('components.layouts.guest')]
#[Title("Register")]
class extends Component
{
    use Toast;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected $messages = [

    ];
    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class,'ends_with:gmail.com,email.com'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

//        Auth::login($user);
        $this->success("Registration successfully! Please sign in to use our services.",position: 'toast-top toast-end');
        $this->redirect(route('login', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col justify-center items-center min-h-screen">
    <img src="https://www.svgrepo.com/show/156221/medicines.svg" alt="" class="size-32 mb-10">
    <x-ui-card title="Register" subtitle="Registering your personal account to process further" separator progress-indicator class="w-1/2 lg:px-10 sm:px-6 md:px-6">
        <x-ui-form wire:submit="register">
            <x-ui-input label="Name" wire:model="name" clearable />
            <x-ui-input label="Email" wire:model="email" clearable />
            <x-ui-input label="Password" wire:model="password" type="password"/>
            <x-ui-input label="Confirm password" wire:model="password_confirmation" type="password"/>
            <x-slot:actions>
                <a class="underline text-sm text-gray-600 hover:text-gray-900 py-3" href="{{ route('login') }}" wire:navigate>
                    {{ __('Already registered?') }}
                </a>
                <x-ui-button label="Register" class="btn-primary" type="submit" spinner="register" />
            </x-slot:actions>
        </x-ui-form>
    </x-ui-card>
</div>
