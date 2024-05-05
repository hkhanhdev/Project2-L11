<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\{Layout, Title};

new
#[Layout('components.layouts.admin')]
#[Title('Brands Management')]
class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    protected $rules = [
        'brand_id' => [],
        'name' => ["required", "string", 'max:100','unique:brands'],
    ];
    public string $search = '';
    public bool $add_drawer = false;
    public bool $edit = false;

    public $brand_id;
    public $name;

    public function saveEdit(): void
    {
        $validated = $this->validate($this->rules);
        $brand = \App\Models\Brands::where("id", $validated['brand_id'])->update(
            [
                "name" => $validated['name'],
            ]
        );
        if ($brand > 0) {
            // Update successful
            $this->reset();
            $this->success('Updated', position: 'toast-bottom', timeout: 4000);
        } else {
            // No rows were updated
            $this->reset();
            $this->error('Something is wrong.Please try again!', position: 'toast-bottom', timeout: 4000);
        }
    }

    public function saveAdd(): void
    {
        $validated = $this->validate($this->rules);
        \App\Models\Brands::create(
            [
                "name" => $validated['name']
            ]
        );
        $this->reset();
        $this->success('Product added!', position: 'toast-bottom', timeout: 4000);

    }

    public function openEditModal($brand)
    {
        $this->brand_id = $brand['id'] ?? null;
        $this->name = $brand['name'] ?? null;
    }

    public function openAddModal()
    {
        $this->reset();
        $this->add_drawer = true;
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1 text-primary'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-96 text-primary'],
        ];
    }

    public function brands()
    {
        $brands = \App\Models\Brands::where("name","LIKE","%$this->search%")->paginate(10);
        return $brands;
    }

    public function with(): array
    {
        return [
            'brands' => $this->brands(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-ui-header title="Brands Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-ui-input placeholder="Brand name..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"/>
        </x-slot:middle>
        <x-slot:actions>
            <x-ui-button icon="o-plus" class="btn-primary" label="Add" wire:click="openAddModal()"/>
        </x-slot:actions>
    </x-ui-header>

    {{--        ADD DRAWER--}}
    <x-ui-drawer wire:model="add_drawer" title="Add brand" right separator with-close-button class="lg:w-1/3">
        <x-ui-form wire:submit="saveAdd">
            <x-ui-input label="Name" wire:model="name"/>
            <x-slot:actions>
                <x-ui-button label="Cancel" icon="o-x-mark" @click="$wire.add_drawer = false" spinner/>
                <x-ui-button label="Save" icon="o-check" class="btn-primary" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-drawer>
    <x-ui-card>
        <x-ui-table :headers="$headers" :rows="$brands" with-pagination class="table-lg">
            @scope('actions', $brand)
            <x-ui-button icon="o-pencil-square" spinner class="btn-sm btn-warning"
                         @click="$wire.edit = true,$wire.openEditModal({{$brand}})"/>
            @endscope
        </x-ui-table>
    </x-ui-card>

    {{--    edit modal--}}
    <x-ui-modal wire:model="edit" title="Edit panel" subtitle="Change brand infomation">
        <x-ui-form wire:submit="saveEdit">
            <x-ui-input label="Brand ID" disabled wire:model="brand_id"/>
            <x-ui-input label="Name" wire:model="name"/>
            <x-slot:actions>
                <x-ui-button label="Cancel" @click="$wire.edit = false"/>
                <x-ui-button label="Save" class="btn-success" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-modal>
</div>
