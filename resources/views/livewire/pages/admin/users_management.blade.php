<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\{Layout, Title};

new
#[Layout('components.layouts.admin')]
#[Title('Users Management')]
class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.\App\Models\User::class],
        'password' => ['required', 'string'],
        'role' => [],
        'address' => ['required','string'],
        'phone' =>['required','regex:/^(?:\+84|0)?[1-9]\d{8,9}$/', 'unique:'.\App\Models\User::class],

    ];
    public string $search = '';
    public bool $add_drawer = false;
    public bool $edit = false;

    public $user_id;
    public $name;
    public $email;
    public $password;
    public $address;
    public $phone;
    public $role = '0';

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom', timeout: 4000);
    }

    public function saveEdit(): void
    {
        $validated = $this->validate($this->rules);
        dd($validated);
        $this->success('Updated', position: 'toast-bottom', timeout: 4000);

        $this->error('Something is wrong.Please try again!', position: 'toast-bottom', timeout: 4000);

    }

    public function saveAdd(): void
    {
        $validated = $this->validate($this->rules);
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user = \App\Models\User::create($validated);
        $this->reset();
        $this->success('User added successfully!', position: 'toast-bottom', timeout: 4000);
    }

    // Delete action
    public function delete($id): void
    {

        $this->reset();
        $this->warning('Cannot delete associated product,change product status to hidden instead', position: 'toast-bottom', timeout: 6000);

        $this->reset();
        $this->error('Something is wrong.Please try again!', position: 'toast-bottom', timeout: 4000);

    }

    public function openEditModal($user)
    {
        $this->user_id = $user['id'] ?? '';
        $this->name = $user['name'] ?? '';
        $this->email = $user['email'] ?? '';
        $this->address = $user['address'] ?? '';
        $this->phone = $user['phone'] ?? '';
//        dd($user);
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
            ['key' => 'phone', 'label' => 'Phone', 'class' => 'w-20 text-primary'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-36 text-primary']
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
        <x-slot:actions>
            <x-ui-button icon="o-plus" class="btn-primary" label="Add" @click="$wire.add_drawer = true"/>
        </x-slot:actions>
    </x-ui-header>

    {{--        ADD DRAWER--}}
    <x-ui-drawer wire:model="add_drawer" title="Add new user" right separator with-close-button class="lg:w-1/3">
        <x-ui-form wire:submit="saveAdd">
            <x-ui-input label="Username" wire:model="name"/>
            <x-ui-input label="Email" wire:model="email"/>
            <x-ui-input label="Password" wire:model="password" type="password"/>
            <x-ui-input label="Address" wire:model="address"/>
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
                <x-ui-button label="Cancel" icon="o-x-mark" @click="$wire.add_drawer = false" spinner/>
                <x-ui-button label="Save" icon="o-check" class="btn-primary" type="submit" spinner="saveAdd"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-drawer>
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
            <x-ui-button icon="o-pencil-square" spinner class="btn-sm btn-warning"
                         @click="$wire.openEditModal({{$user}})"/>
            @endscope
        </x-ui-table>
    </x-ui-card>

    {{--    edit modal--}}
    <x-ui-modal wire:model="edit" title="Edit panel" subtitle="Change product infomation">
        <x-ui-form wire:submit="saveEdit">
            <x-ui-input label="UserID" disabled value="{{$user_id ?? ''}}"/>
            <x-ui-input label="Username" wire:model="name"/>
            <x-ui-input label="Address" wire:model="address"/>
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
                <x-ui-button label="Cancel" @click="$wire.edit = false"/>
                <x-ui-button label="Save" class="btn-success" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-modal>
</div>
