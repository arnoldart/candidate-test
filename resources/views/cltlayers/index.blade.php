<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition-colors">Home</a>
                <span class="text-gray-400">›</span>
                <a href="{{ route('suppliers.index') }}" class="hover:text-gray-900 transition-colors">Suppliers</a>
                <span class="text-gray-400">›</span>
                <a href="{{ route('suppliers.layups.index', $layup->supplier_id) }}" class="hover:text-gray-900 transition-colors">Layups</a>
                <span class="text-gray-400">›</span>
                <span class="font-medium text-gray-900">{{ $layup->name }}</span>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#447A60] focus:ring-offset-2">
                    <svg class="mr-2.5 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                    Duplicate
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-md bg-[#447A60] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#36614D] transition-colors focus:outline-none focus:ring-2 focus:ring-[#447A60] focus:ring-offset-2">
                    <svg class="mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    Save Changes
                </button>
            </div>
        </div>

        <div class="bg-white px-8 py-6 rounded-xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-4">
                    <h1 class="text-[28px] text-gray-900 tracking-tight font-bold" style="font-family: 'Merriweather', serif;">
                        Layup Specification: {{ $layup->name }}
                    </h1>
                    <span class="inline-flex items-center rounded-full bg-[#E6F3EE] px-3 py-1 text-xs font-semibold text-[#36614D] border border-[#C5E1D4]">
                        Active
                    </span>
                </div>
                <p class="text-[14px] text-gray-500 mt-1">Standard structural specification for {{ strtolower($layup->name) }}.</p>
            </div>
            
            <div class="flex gap-10 md:pl-10 md:border-l md:border-gray-100">
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Created By</span>
                    <span class="block text-sm font-semibold text-gray-900">Eng. Dept A</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Last Modified</span>
                    <span class="block text-sm font-semibold text-gray-900">{{ $layup->updated_at->format('M d, Y') }}</span>
                </div>
                @php
                    $totalThickness = collect($layers->items())->sum('thickness');
                    $totalLayers = $layers->total();
                @endphp
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total Thickness</span>
                    <span class="block text-lg font-bold text-[#447A60] leading-none">{{ $totalThickness }}mm</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total Layers</span>
                    <span class="block text-lg font-bold text-[#447A60] leading-none">{{ $totalLayers }} Layers</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12" x-data="{ showLayerModal: false, isEdit: false, layerOrder: '', thickness: '', width: '', angle: '', formAction: '{{ route('layups.layers.store', $layup) }}' }">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-6">
            
            <div class="lg:col-span-7 flex flex-col min-w-0">
                
                <div class="flex justify-between items-center mb-4 px-1">
                    <h2 class="text-xl font-bold text-gray-900" style="font-family: 'Merriweather', serif;">Layer Composition</h2>
                    <button type="button" @click="isEdit = false; layerOrder='1'; thickness='40'; width='1200'; angle='0'; formAction='{{ route('layups.layers.store', $layup) }}'; showLayerModal=true;" class="inline-flex items-center text-sm font-semibold text-[#447A60] hover:text-[#36614D] transition-colors focus:outline-none">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add Layer
                    </button>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex-grow flex flex-col">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th scope="col" class="py-3 pl-5 text-left text-[11px] font-bold text-gray-400 tracking-widest uppercase w-12">Order</th>
                                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-gray-400 tracking-widest uppercase">Thickness</th>
                                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-gray-400 tracking-widest uppercase">Width</th>
                                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-gray-400 tracking-widest uppercase">Angle</th>
                                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-gray-400 tracking-widest uppercase">Grade</th>
                                    <th scope="col" class="px-5 py-3 text-right text-[11px] font-bold text-gray-400 tracking-widest uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @if($layers->isEmpty())
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500">
                                            No layers have been compositioned yet. Click "Add Layer" to begin constructing your layup.
                                        </td>
                                    </tr>
                                @else
                                    @foreach($layers as $layer)
                                        <tr class="hover:bg-gray-50 transition-colors group">
                                            <td class="py-3.5 pl-6 whitespace-nowrap">
                                                <div class="flex items-center justify-center w-6 h-6 rounded bg-gray-100 text-gray-400 cursor-grab hover:bg-gray-200">
                                                    <span class="text-xs font-bold">{{ $layer->layer_order }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5 whitespace-nowrap text-sm font-medium text-gray-700 font-mono">{{ rtrim(rtrim($layer->thickness, '0'), '.') }}mm</td>
                                            <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-500 font-mono">{{ rtrim(rtrim($layer->width, '0'), '.') }}mm</td>
                                            <td class="px-4 py-3.5 whitespace-nowrap">
                                                @if($layer->angle == 0)
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-600">
                                                        <svg class="mr-1 h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                                        0°
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-orange-50 border border-orange-200 px-2 py-0.5 text-xs font-semibold text-[#B36B39]">
                                                        <svg class="mr-1 h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                        90°
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3.5 whitespace-nowrap">
                                                <span class="inline-flex items-center text-xs font-medium text-gray-700">
                                                    <span class="mr-1.5 h-2 w-2 rounded-full {{ $layer->angle == 0 ? 'bg-[#447A60]' : 'bg-[#B36B39]' }}"></span> 
                                                    {{ $layer->angle == 0 ? 'C24' : 'C16' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button type="button" @click="isEdit = true; layerOrder='{{ $layer->layer_order }}'; thickness='{{ rtrim(rtrim($layer->thickness, '0'), '.') }}'; width='{{ rtrim(rtrim($layer->width, '0'), '.') }}'; angle='{{ rtrim(rtrim($layer->angle, '0'), '.') }}'; formAction='{{ route('layups.layers.update', ['layup' => $layup->id, 'layer' => $layer->id]) }}'; showLayerModal=true;" class="text-gray-400 hover:text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                                                    <form action="{{ route('layups.layers.destroy', ['layup' => $layup->id, 'layer' => $layer->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this layer?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="bg-gray-50 border-t border-gray-200 px-5 py-3 flex justify-between items-center mt-auto">
                        <span class="text-xs text-gray-500 font-medium">Showing {{ $totalLayers }} layers</span>
                        <span class="text-xs text-gray-700">Calculated Sum: <strong class="font-mono text-gray-900">{{ number_format((float)$totalThickness, 2) }} mm</strong></span>
                    </div>
                </div>

                <div class="mt-6 bg-[#FAFAFA] border border-gray-200 rounded-xl p-5 flex items-start gap-3">
                    <div class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full border border-red-200 text-red-600 flex items-center justify-center font-serif italic text-sm mb-auto">i</div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 leading-none mb-1.5">Engineering Note</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">Ensure bonding pressure is adjusted for varying layer grades (C24/C16 mix). Verify alignment of 90° transverse layers.</p>
                    </div>
                </div>

            </div>
            
            <div class="lg:col-span-5 flex flex-col min-w-0">
                <div class="flex justify-between items-center mb-4 px-1">
                    <h2 class="text-xl font-bold text-gray-900" style="font-family: 'Merriweather', serif;">Structure Visualizer</h2>
                    <div class="flex items-center gap-3 text-[10px] uppercase font-bold tracking-widest text-gray-500">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-sm bg-[#E3CAA5] mr-1.5 border border-[#CBB38D]"></span> Longitudinal (0°)</span>
                        <span class="flex items-center"><span class="w-3 h-3 rounded-sm bg-[#C49B74] mr-1.5 border border-[#A6815B]"></span> Transverse (90°)</span>
                    </div>
                </div>

                <div class="bg-white border text-center border-gray-200 rounded-xl shadow-sm flex-grow relative overflow-hidden flex flex-col justify-center py-16 px-8 min-h-[400px]">
                    
                    <div class="absolute left-10 top-12 bottom-12 w-px border-l-2 border-dashed border-gray-200 flex flex-col justify-between pt-1 pb-1">
                        <div class="-ml-[13px] bg-white px-2 py-1 text-[9px] font-bold tracking-widest text-gray-400 uppercase leading-tight text-center">TOP<br>(OUTSIDE)</div>
                        <div class="-ml-[16px] bg-white px-2 py-1 text-[9px] font-bold tracking-widest text-gray-400 uppercase leading-tight text-center">BOTTOM<br>(INSIDE)</div>
                    </div>

                    <div class="w-64 max-w-full mx-auto shadow-xl rounded-lg bg-white p-6 relative z-10 space-y-1 transform transition-all hover:scale-105 duration-500">
                        @if($layers->isEmpty())
                            <div class="h-48 border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                                Canvas Empty
                            </div>
                        @else
                            @foreach($layers as $layer)
                                <div class="w-full rounded-md border text-xs font-bold text-gray-800 flex items-center justify-center tracking-wider transition-colors {{ $layer->angle == 0 ? 'bg-[#E3CAA5] border-[#CBB38D]' : 'bg-[#C49B74] border-[#A6815B]' }}" style="height: {{ max(30, ($layer->thickness / max(1, $totalThickness)) * 200) }}px;">
                                    L{{ $layer->layer_order }} ({{ rtrim(rtrim($layer->thickness, '0'), '.') }}mm)
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="absolute left-0 right-0 bottom-6 text-center text-[11px] text-gray-400 mx-10 leading-tight">
                        <span class="block mb-0.5 text-gray-500">Cross-Laminated Structural Assembly</span>
                        <em class="italic opacity-80">Note: 2D orientation is for schematic purposes. All layers bonded with industrial-grade adhesives.</em>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showLayerModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="showLayerModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showLayerModal = false"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showLayerModal" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                        <form :action="formAction" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" x-bind:disabled="!isEdit">
                            
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="modal-title" x-text="isEdit ? 'Edit Layer' : 'Add New Layer'"></h3>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Order Index</label>
                                            <input type="number" name="layer_order" x-model="layerOrder" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#447A60] sm:text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Angle (Deg)</label>
                                            <select name="angle" x-model="angle" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#447A60] sm:text-sm">
                                                <option value="0">0° (Longitudinal)</option>
                                                <option value="90">90° (Transverse)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Thickness (mm)</label>
                                            <input type="number" step="0.01" name="thickness" x-model="thickness" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#447A60] sm:text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Width (mm)</label>
                                            <input type="number" step="0.01" name="width" x-model="width" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#447A60] sm:text-sm" required>
                                        </div>
                                    </div>
                                    
                                    @if($errors->any())
                                        <div class="text-sm text-red-600 mt-2">Please check your inputs, validation failed.</div>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-[#447A60] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#36614D] sm:ml-3 sm:w-auto transition-colors">
                                    <span x-text="isEdit ? 'Save Changes' : 'Create Layer'"></span>
                                </button>
                                <button type="button" @click="showLayerModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
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
