<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('suppliers.index') }}" class="hover:text-gray-900 transition-colors">Suppliers</a>
            <span class="text-gray-400">/</span>
            <span class="font-medium text-gray-700">{{ $supplier->name }}</span>
        </div>
        
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white px-8 py-6 rounded-xl border border-gray-100 shadow-sm">
            <div>
                <div class="flex items-center gap-4">
                    <h1 class="text-[32px] text-gray-900 tracking-tight font-bold" style="font-family: 'Merriweather', serif;">
                        {{ $supplier->name }}
                    </h1>
                    <span class="inline-flex items-center rounded-full bg-[#447A60] px-3 py-1 text-xs font-semibold text-white">
                        Active Partner
                    </span>
                </div>
                <p class="text-[14px] text-gray-500 mt-2 font-mono tracking-widest">ID: SUP-{{ date('Y', strtotime($supplier->created_at ?? now())) }}-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            <div class="flex items-center">
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Edit Supplier
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mt-6 bg-white overflow-hidden rounded-xl border border-gray-100 shadow-sm divive-y sm:divide-y-0 sm:divide-x divide-gray-100">
            <!-- Box 1 -->
            <div class="px-6 py-5">
                <dt class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Primary Contact</dt>
                <dd class="flex items-center text-[15px] font-medium text-gray-700">
                    <svg class="mr-2 h-5 w-5 text-[#447A60]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    engineering@nordic.ca
                </dd>
            </div>
            <div class="px-6 py-5">
                <dt class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Location</dt>
                <dd class="flex items-center text-[15px] font-medium text-gray-700">
                    <svg class="mr-2 h-5 w-5 text-[#447A60]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Montreal, QC, Canada
                </dd>
            </div>
            <div class="px-6 py-5">
                <dt class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Material Certifications</dt>
                <dd class="flex items-center text-[15px] font-medium text-gray-700">
                    <svg class="mr-2 h-5 w-5 text-[#447A60]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    SPF No. 1/2, D. Fir-L
                </dd>
            </div>
            <div class="px-6 py-5">
                <dt class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Last Audit Date</dt>
                <dd class="flex items-center text-[15px] font-medium text-gray-700">
                    <svg class="mr-2 h-5 w-5 text-[#447A60]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Oct 12, 2023
                </dd>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-10" x-data="{ 
            showLayupModal: false, 
            isEdit: false, 
            layupName: '', 
            formAction: '{{ route('suppliers.layups.store', $supplier) }}',
            init() {
                // Reopen modal if validation errors exist
                @if($errors->any())
                    this.showLayupModal = true;
                    this.layupName = '{{ old('name') }}';
                    @if(old('_method') == 'PUT')
                        this.isEdit = true;
                        this.formAction = '{{ old('action_url') ?? route('suppliers.layups.index', $supplier) }}';
                    @else
                        this.isEdit = false;
                        this.formAction = '{{ route('suppliers.layups.store', $supplier) }}';
                    @endif
                @endif
            }
        }">
        
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 mt-4">
            <h2 class="text-2xl font-bold text-gray-900" style="font-family: 'Merriweather', serif;">Associated Layups</h2>
            
            <div class="flex items-center gap-3">
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </button>
                <button type="button" @click="isEdit = false; layupName = ''; formAction = '{{ route('suppliers.layups.store', $supplier) }}'; showLayupModal = true;" class="inline-flex items-center justify-center rounded-md bg-[#447A60] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#36614D] transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Layup
                </button>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Layup ID</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Name</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Thickness</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Ply Count</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Species/Grade</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Revision</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Status</th>
                            <th scope="col" class="py-4 pl-3 pr-6 text-right text-xs font-bold text-gray-400 tracking-widest uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white group border-b border-gray-100">
                        @forelse($layups as $layup)
                            @php
                                $statusDummy = ['Active', 'Draft', 'Archived'][$loop->index % 3];
                                $colorStatus = $statusDummy === 'Active' ? 'text-green-700 bg-green-50 border border-green-200 dot-green-500' :
                                              ($statusDummy === 'Draft' ? 'text-yellow-700 bg-yellow-50 border border-yellow-200 dot-yellow-500' : 
                                              'text-gray-700 bg-gray-100 border border-gray-200 dot-gray-500');
                                
                                $plyCount = $layup->clt_layers_count ?? 0;
                                $thickness = $layup->clt_layers_sum_thickness ?? 0;
                            @endphp
                            <tr onclick="window.location='{{ route('layups.layers.index', $layup) }}'" class="hover:bg-gray-50 transition-colors relative group/row cursor-pointer">
                                {{-- LAYUP DUMMY DATA --}}
                                <td class="whitespace-nowrap py-[22px] pl-6 pr-3 text-sm text-gray-500 font-mono tracking-widest">
                                    L-{{ str_pad($layup->id, 3, '0', STR_PAD_LEFT) }}-A
                                </td>
                                <td class="whitespace-nowrap px-3 py-[22px] text-[15px] text-gray-900 font-medium">
                                    {{ $layup->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-[22px] text-[14px] text-gray-500">
                                    {{ rtrim(rtrim(number_format((float)$thickness, 2), '0'), '.') }}mm
                                </td>
                                <td class="whitespace-nowrap px-3 py-[22px]">
                                    <span class="inline-flex items-center justify-center rounded bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600 tracking-wide">{{ $plyCount }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-[22px] text-[14px] text-gray-500">
                                    Spruce / Mixed
                                </td>
                                <td class="whitespace-nowrap px-3 py-[22px] text-[14px] text-gray-400">
                                    Rev {{ ($loop->index % 4) + 1 }} (Oct 10)
                                </td>
                                <td class="whitespace-nowrap px-3 py-[22px]">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ explode(' dot-', $colorStatus)[0] }}">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ explode(' dot-', $colorStatus)[1] }}" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                        {{ $statusDummy }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-[22px] pl-3 pr-6 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                        <button type="button" @click.stop="isEdit = true; layupName = '{{ addslashes($layup->name) }}'; formAction = '{{ route('suppliers.layups.update', ['supplier' => $supplier->id, 'layup' => $layup->id]) }}'; showLayupModal = true;" class="text-blue-600 hover:text-blue-900 font-semibold focus:outline-none">Edit</button>
                                        <form action="{{ route('suppliers.layups.destroy', ['supplier' => $supplier->id, 'layup' => $layup->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this layup?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">No layups registered for this supplier yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <x-pagination :paginator="$layups" />
        </div>

        <div x-show="showLayupModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="showLayupModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showLayupModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showLayupModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form :action="formAction" method="POST">
                            @csrf
                            <input type="hidden" name="action_url" :value="formAction">
                            <input type="hidden" name="_method" value="PUT" x-bind:disabled="!isEdit">
                            
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#E6F3EE] sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-[#447A60]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title" x-text="isEdit ? 'Edit Layup' : 'Add New Layup'"></h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500" x-text="isEdit ? 'Update the details for this layup.' : 'Enter the name for the new timber layup.'"></p>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Layup Name</label>
                                            <div class="mt-2">
                                                <input type="text" name="name" id="name" x-model="layupName" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-[#447A60] sm:text-sm sm:leading-6" placeholder="e.g. Standard 3-Ply Wall" required autofocus>
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
                                    <span x-text="isEdit ? 'Save Changes' : 'Create Layup'"></span>
                                </button>
                                <button type="button" @click="showLayupModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
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