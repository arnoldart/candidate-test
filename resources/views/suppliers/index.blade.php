<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" x-data>
            <div>
                <h1 class="text-[32px] text-gray-900 tracking-tight" style="font-family: 'Merriweather', serif;">Suppliers</h1>
                <p class="mt-1 text-sm text-gray-500">Manage timber suppliers and material sourcing.</p>
            </div>
            
            <div class="flex items-center">
                <button type="button" @click="$dispatch('open-supplier-modal', { isEdit: false })" class="inline-flex items-center justify-center rounded-md bg-[#447A60] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#36614D] transition-colors">
                    <i class="fa-solid fa-plus mr-2 opacity-90 text-[15px]"></i>
                    Add Supplier
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6"
        x-data="{
            showModal: false,
            isEdit: false,
            supplierName: '',
            formAction: '{{ route('suppliers.store') }}',
            init() {
                window.addEventListener('open-supplier-modal', (e) => {
                    let data = e.detail;
                    if (data.isEdit) {
                        this.isEdit = true;
                        this.supplierName = data.name;
                        this.formAction = '/suppliers/' + data.id;
                    } else {
                        this.isEdit = false;
                        this.supplierName = '';
                        this.formAction = '{{ route('suppliers.store') }}';
                    }
                    this.showModal = true;
                });
                
                @if($errors->any())
                    this.showModal = true;
                    this.supplierName = '{{ old('name') }}';
                    @if(old('_method') == 'PUT')
                        this.isEdit = true;
                        this.formAction = '{{ old('action_url') ?? route('suppliers.index') }}';
                    @else
                        this.isEdit = false;
                        this.formAction = '{{ route('suppliers.store') }}';
                    @endif
                @endif
            }
        }">
         
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-2">
            <form action="{{ route('suppliers.index') }}" method="GET" class="w-full sm:max-w-xs">
                <label for="search" class="sr-only">Search suppliers by name...</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="search" name="search" id="search" value="{{ request('search') }}" class="block w-full rounded-md border-gray-300 pl-10 focus:border-[#447A60] focus:ring-[#447A60] sm:text-sm text-gray-500 placeholder-gray-400 py-2.5" placeholder="Search suppliers by name..." onchange="this.form.submit()">
                </div>
            </form>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-filter mr-2 text-gray-500 opacity-80 text-[13px]"></i>
                    Filter
                </button>
                {{-- <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-cloud-arrow-down mr-2 text-gray-500 opacity-80 text-[13px]"></i>
                    Export
                </button> --}}
                <a href="{{ route('suppliers.exportAll') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-cloud-arrow-down mr-2 text-gray-500 opacity-80 text-[13px]"></i>
                    Export All
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold text-gray-500 tracking-widest uppercase">Name</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 tracking-widest uppercase">Total Layups</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 tracking-widest uppercase">Created At</th>
                            <th scope="col" class="py-4 pl-3 pr-6 text-right text-xs font-bold text-gray-500 tracking-widest uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white group">
                        @forelse($suppliers as $supplier)
                            @php
                                $colors = [
                                    ['bg' => 'bg-[#EBF1FA]', 'text' => 'text-[#4A72B2]'],
                                    ['bg' => 'bg-[#E6F3EE]', 'text' => 'text-[#3E8D61]'], 
                                    ['bg' => 'bg-[#FDF0E6]', 'text' => 'text-[#CC6A33]'], 
                                    ['bg' => 'bg-[#F4EBFA]', 'text' => 'text-[#8A51B2]'], 
                                    ['bg' => 'bg-[#E6F5F3]', 'text' => 'text-[#3B9282]']  
                                ];
                                $initials = strtoupper(substr($supplier->name, 0, 2));
                                $colorStyle = $colors[$loop->index % count($colors)];
                            @endphp
                            <tr @click="window.location='{{ route('suppliers.layups.index', $supplier) }}'" class="hover:bg-gray-50 transition-colors relative group/row cursor-pointer">
                                <td class="whitespace-nowrap py-5 pl-6 pr-3">
                                    <div class="flex items-center">
                                        <div class="h-11 w-11 shrink-0 flex items-center justify-center rounded-full {{ $colorStyle['bg'] }} {{ $colorStyle['text'] }} font-semibold text-sm">
                                            {{ $initials }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-bold text-gray-900 text-[15px]">{{ $supplier->name }}</div>
                                            <div class="text-[13px] text-gray-500 mt-0.5">ID: SUP-{{ date('Y', strtotime($supplier->created_at ?? now())) }}-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-[15px] text-gray-600">
                                    {{ $supplier->clt_layups_count ?? 0 }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-[15px] text-gray-600">
                                    {{ optional($supplier->created_at)->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('suppliers.export', $supplier) }}" class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-[#447A60] hover:border-[#447A60] hover:bg-[#F0F7F4] transition-colors focus:outline-none" title="Export">
                                            <i class="fa-solid fa-file-export"></i>
                                        </a>
                                        <button type="button" @click.stop="$dispatch('open-supplier-modal', { isEdit: true, id: {{ $supplier->id }}, name: '{{ addslashes($supplier->name) }}' })" class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-colors focus:outline-none" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Delete this supplier?');" @click.stop>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-colors focus:outline-none" title="Delete">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No suppliers registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <x-pagination :paginator="$suppliers" />
        </div>

        <div x-show="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form :action="formAction" method="POST">
                            @csrf
                            <input type="hidden" name="action_url" :value="formAction">
                            <input type="hidden" name="_method" value="PUT" x-bind:disabled="!isEdit">
                            
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#E6F3EE] sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fa-solid fa-industry text-[18px] text-[#447A60]"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title" x-text="isEdit ? 'Edit Supplier' : 'Add New Supplier'"></h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500" x-text="isEdit ? 'Update the details for this supplier.' : 'Please enter the details for the new timber supplier.'"></p>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Supplier Name</label>
                                            <div class="mt-2">
                                                <input type="text" name="name" id="name" x-model="supplierName" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-[#447A60] sm:text-sm sm:leading-6" placeholder="e.g. Nordic Timber Co." required autofocus>
                                            </div>
                                            @error('name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-[#447A60] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#36614D] sm:ml-3 sm:w-auto transition-colors">
                                    <span x-text="isEdit ? 'Save Changes' : 'Create Supplier'"></span>
                                </button>
                                <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
