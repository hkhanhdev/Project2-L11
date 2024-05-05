<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\{Layout, Title};

new
#[Layout('components.layouts.admin')]
#[Title('Categories Management')]
class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    protected $rules = [
        'cate_id' => [],
        'name' => ["required", "string", 'max:100',"unique:categories"]
    ];
    public string $search = '';
    public bool $add_drawer = false;
    public bool $edit = false;

    public $cate_id;
    public $name;

    public function saveEdit(): void
    {
        $validated = $this->validate($this->rules);
        $cate = \App\Models\Categories::where("id", $validated['cate_id'])->update(
            [
                "name" => $validated['name'],
            ]
        );
        if ($cate > 0) {
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
        \App\Models\Categories::create(['name' => $validated['name']]);
        $this->reset();
        $this->success('Category added!', position: 'toast-bottom', timeout: 4000);

    }

    public function openEditModal($cate)
    {
//        $this->reset();
        $this->cate_id = $cate['id'] ?? null;
        $this->name = $cate['name'] ?? null;
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
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-96 text-primary']
        ];
    }

    public function cates()
    {
        $cates = \App\Models\Categories::where("name","LIKE","%$this->search%")->paginate(8);
        return $cates;
    }

    public function with(): array
    {
        return [
            'categories' => $this->cates(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-ui-header title="Categories Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-ui-input placeholder="Category name..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"/>
        </x-slot:middle>
        <x-slot:actions>
            <x-ui-button icon="o-plus" class="btn-primary" label="Add" wire:click="openAddModal()"/>
        </x-slot:actions>
    </x-ui-header>

    {{--        ADD DRAWER--}}
    <x-ui-drawer wire:model="add_drawer" title="Add new category" right separator with-close-button class="lg:w-1/3">
        <x-ui-form wire:submit="saveAdd">
            <x-ui-input label="Category name" wire:model="name"/>
            <x-slot:actions>
                <x-ui-button label="Cancel" icon="o-x-mark" @click="$wire.add_drawer = false" spinner/>
                <x-ui-button label="Save" icon="o-check" class="btn-primary" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-drawer>
    <x-ui-card>
        <x-ui-table :headers="$headers" :rows="$categories" with-pagination class="table-lg">

            @scope('cell_status', $product)
                @if($product['status'] == 'Available' && $product['quantity'] != 0)
                <x-ui-badge value="{{$product['status']}}" class="bg-green-400" />
                @elseif($product['quantity'] == 0)
                <x-ui-badge value="Out of stock" class="badge-error" />
                @else
                <x-ui-badge value="Hidden" class="badge-warning" />
                @endif
            @endscope

            @scope('actions', $cate)
            <x-ui-button icon="o-pencil-square" spinner class="btn-sm btn-warning"
                         @click="$wire.edit = true,$wire.openEditModal({{$cate}})"/>
            @endscope
        </x-ui-table>
    </x-ui-card>

    {{--    edit modal--}}
    <x-ui-modal wire:model="edit" title="Edit panel" subtitle="Change category infomation">
        <x-ui-form wire:submit="saveEdit">
            <x-ui-input label="Category ID" disabled wire:model="cate_id"/>
            <x-ui-input label="Category name" wire:model="name"/>
            <x-slot:actions>
                <x-ui-button label="Cancel" @click="$wire.edit = false"/>
                <x-ui-button label="Save" class="btn-success" type="submit" spinner="save"/>
            </x-slot:actions>
        </x-ui-form>
    </x-ui-modal>
</div>
