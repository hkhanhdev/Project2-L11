<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout,Title};
new #[Layout('components.layouts.admin')]
#[Title('Admin Profile')]
class extends Component {
    //
}; ?>

<div>
    <x-ui-header title="Profile" separator progress-indicator/>
    <div class="mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-ui-card>
            <livewire:partials.update-profile-information-form />
        </x-ui-card>

        <x-ui-card>
            <livewire:partials.update-password-form />
        </x-ui-card>

        <x-ui-card>
            <livewire:partials.delete-user-form />
        </x-ui-card>
    </div>
</div>
