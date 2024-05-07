<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\{Layout, Title};
use Illuminate\Validation\Rule;

new
#[Layout('components.layouts.admin')]
#[Title('Users Management')]
class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    protected $edit_rules = [
        'role' => [],
        'address' => ['required','string'],
        'name' => ['required', 'string', 'max:80']
    ];

    protected function setRules($user_id)
    {
        $this->edit_rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255',Rule::unique('users', 'email')->ignore($user_id)];
        $this->edit_rules['phone'] = ['required','regex:/^(?:\+84|0)?[1-9]\d{8,9}$/',Rule::unique('users', 'phone')->ignore($user_id)];
    }
    public string $search = '';
    public bool $edit = false;

    public $user_id;
    public $name;
    public $email;
    public $password;
    public $address;
    public $phone;
    public $role = '0';

    public function saveEdit(): void
    {
        $this->setRules($this->user_id);
        $validated = $this->validate($this->edit_rules);
        $user = \App\Models\User::find($this->user_id);
        $user->update($validated);
        $this->success('Updated', position: 'toast-bottom', timeout: 4000);
    }

    public function closeModal()
    {
        $this->reset();
    }
    public function openEditModal($user)
    {
        $this->user_id = $user['id'] ?? '';
        $this->name = $user['name'] ?? '';
        $this->email = $user['email'] ?? '';
        $this->address = $user['address'] ?? '';
        $this->phone = $user['phone'] ?? '';
        $this->edit = true;
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1 text-primary'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-48 text-primary'],
            ['key' => 'email', 'label' => 'Email', 'class' => 'w-64 text-primary'],
            ['key' => 'role', 'label' => 'Privilege', 'class' => 'w-36 text-primary'],
            ['key' => 'address', 'label' => 'Address', 'class' => 'w-48 text-primary'],
            ['key' => 'phone', 'label' => 'Phone', 'class' => 'w-20 text-primary']
        ];
    }

    public function users()
    {
        $users = \App\Models\User::where("name","LIKE","%$this->search%")->paginate(10);
        return $users;
    }

    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-ui-header title="Users Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-ui-input placeholder="User name..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"/>
        </x-slot:middle>

    </x-ui-header>

    <x-ui-card>
        <x-ui-table :headers="$headers" :rows="$users" with-pagination class="table-md">

            @scope('cell_role', $user)
                @if($user['role'] == '0')
                <x-ui-badge value="User" class="bg-green-400" />
                @else
                <x-ui-badge value="Admin" class="badge-error" />
                @endif
            @endscope
            @scope('actions', $user)
            <div>
                <x-ui-button icon="o-pencil-square" spinner class="btn-sm btn-warning"
                             @click="$wire.openEditModal({{$user}})"/>
            </div>
            @endscope
        </x-ui-table>
    </x-ui-card>

    {{--    edit modal--}}
    <x-ui-modal wire:model="edit" title="Edit user panel" subtitle="Change user infomation" persistent>
        <x-ui-form wire:submit="saveEdit">
            <x-ui-input label="UserID" disabled value="{{$user_id ?? ''}}"/>
            <x-ui-input label="Username" wire:model="name"/>
            <x-ui-input label="Address" wire:model="address"/>
            <x-ui-input label="Email" wire:model="email"/>
            <x-ui-input label="Phone" wire:model="phone"/>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Role</span>
                </div>
                <select class="select select-bordered select-info" wire:model="role">
                    <option value="0" selected>User</option>
                    <option value="1">Admin</option>
                </select>
            </label>

            <x-slot:actions>
                <x-ui-button label="Cancel" @click="$wire.closeModal()" spinner="closeModal"/>
                <x-ui-button label="Save" class="btn-success" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-modal>
</div>
