<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
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
                    <i class="fa-regular fa-copy mr-2 text-gray-500"></i>
                    Duplicate
                </button>
                <button type="submit" form="sync-form" class="inline-flex items-center justify-center rounded-md bg-[#447A60] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#36614D] transition-colors focus:outline-none focus:ring-2 focus:ring-[#447A60] focus:ring-offset-2">
                    <i class="fa-solid fa-floppy-disk mr-2 text-white"></i>
                    Save Changes
                </button>
            </div>
        </div>
    </x-slot>

    <form id="sync-form" action="{{ route('layups.layers.sync', $layup) }}" method="POST" class="max-w-7xl mx-auto pb-12 pt-6" x-data="{ 
        showLayerModal: false, 
        isEdit: false,
        editIndex: null,
        thickness: '40', 
        width: '1200', 
        angle: '0', 
        layers: {{ json_encode($layers->map(fn($l) => ['thickness' => rtrim(rtrim($l->thickness, '0'), '.'), 'width' => rtrim(rtrim($l->width, '0'), '.'), 'angle' => rtrim(rtrim($l->angle, '0'), '.')])->toArray()) }},
        init() {
            this.$nextTick(() => {
                new Sortable(document.getElementById('layers-tbody'), {
                    animation: 150,
                    handle: '.cursor-grab',
                    ghostClass: 'bg-green-50',
                    onEnd: (evt) => {
                        const item = this.layers.splice(evt.oldIndex, 1)[0];
                        this.layers.splice(evt.newIndex, 0, item);
                    }
                });
            });
        },
        saveLayer() {
            if (this.isEdit && this.editIndex !== null) {
                this.layers[this.editIndex].thickness = parseFloat(this.thickness) || 0;
                this.layers[this.editIndex].width = parseFloat(this.width) || 0;
                this.layers[this.editIndex].angle = this.angle.toString();
            } else {
                this.layers.push({
                    thickness: parseFloat(this.thickness) || 0,
                    width: parseFloat(this.width) || 0,
                    angle: this.angle.toString()
                });
            }
            this.showLayerModal = false;
        },
        deleteLayer(index) {
            this.layers.splice(index, 1);
        },
        get totalThickness() {
            return this.layers.reduce((sum, layer) => sum + parseFloat(layer.thickness || 0), 0);
        },
        get totalLayers() {
            return this.layers.length;
        }
    }">
        @csrf
        
        <template x-for="(layer, index) in layers">
            <div>
                <input type="hidden" :name="'layers['+index+'][thickness]'" :value="layer.thickness">
                <input type="hidden" :name="'layers['+index+'][width]'" :value="layer.width">
                <input type="hidden" :name="'layers['+index+'][angle]'" :value="layer.angle">
            </div>
        </template>

        <div class="bg-white px-8 py-6 rounded-xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
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
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total Thickness</span>
                    <span class="block text-lg font-bold text-[#447A60] leading-none"><span x-text="totalThickness.toFixed(2)"></span>mm</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total Layers</span>
                    <span class="block text-lg font-bold text-[#447A60] leading-none"><span x-text="totalLayers"></span> Layers</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-7 flex flex-col min-w-0">
                <div class="flex justify-between items-center mb-4 px-1">
                    <h2 class="text-xl font-bold text-gray-900" style="font-family: 'Merriweather', serif;">Layer Composition</h2>
                    <button type="button" @click="isEdit = false; editIndex=null; thickness='40'; width='1200'; angle='0'; showLayerModal=true;" class="inline-flex items-center text-sm font-semibold text-[#447A60] hover:text-[#36614D] transition-colors focus:outline-none">
                        <i class="fa-solid fa-plus w-4 h-4 mr-1 text-[13px]"></i>
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
                            <tbody id="layers-tbody" class="divide-y divide-gray-100 bg-white">
                                <template x-if="layers.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500">
                                            No layers have been compositioned yet. Click "Add Layer" to begin constructing your layup.
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(layer, index) in layers" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="py-3.5 pl-6 whitespace-nowrap">
                                            <div class="flex items-center justify-center w-6 h-6 rounded bg-gray-100 text-gray-400 cursor-grab hover:bg-gray-200">
                                                <i class="fa-solid fa-grip-vertical text-gray-400 text-xs text-[10px]"></i>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-sm font-medium text-gray-700 font-mono" x-text="layer.thickness + 'mm'"></td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-500 font-mono" x-text="layer.width + 'mm'"></td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <template x-if="layer.angle.toString() === '0'">
                                                <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-600">
                                                    <i class="fa-solid fa-arrow-up mr-1.5 text-gray-500 text-[10px]"></i>
                                                    0°
                                                </span>
                                            </template>
                                            <template x-if="layer.angle.toString() !== '0'">
                                                <span class="inline-flex items-center rounded-full bg-orange-50 border border-orange-200 px-2 py-0.5 text-xs font-semibold text-[#B36B39]">
                                                    <i class="fa-solid fa-rotate-right mr-1.5 text-[#B36B39] text-[10px]"></i>
                                                    90°
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center text-xs font-medium text-gray-700">
                                                <span class="mr-1.5 h-2 w-2 rounded-full" :class="layer.angle.toString() === '0' ? 'bg-[#447A60]' : 'bg-[#B36B39]'"></span> 
                                                <span x-text="layer.angle.toString() === '0' ? 'C24' : 'C16'"></span>
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="isEdit = true; editIndex = index; thickness=layer.thickness; width=layer.width; angle=layer.angle.toString(); showLayerModal=true;" class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-colors focus:outline-none" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" @click="if(confirm('Remove this layer?')) deleteLayer(index)" class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-colors focus:outline-none" title="Delete">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="bg-gray-50 border-t border-gray-200 px-5 py-3 flex justify-between items-center mt-auto">
                        <span class="text-xs text-gray-500 font-medium">Showing <span x-text="totalLayers"></span> layers</span>
                        <span class="text-xs text-gray-700">Calculated Sum: <strong class="font-mono text-gray-900"><span x-text="totalThickness.toFixed(2)"></span> mm</strong></span>
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
                        <template x-if="layers.length === 0">
                            <div class="h-48 border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                                Canvas Empty
                            </div>
                        </template>
                        <template x-for="(layer, index) in layers" :key="index">
                            <div class="rounded-md border text-xs font-bold text-gray-800 flex items-center justify-center tracking-wider transition-all relative" 
                                 :class="layer.angle.toString() === '0' ? 'bg-[#E3CAA5] border-[#CBB38D] w-full' : 'bg-[#C49B74] border-[#A6815B] w-11/12 mx-auto'" 
                                 :style="'height: ' + Math.max(30, (parseFloat(layer.thickness) / Math.max(1, totalThickness)) * 200) + 'px; transition: height 0.3s ease;'">
                                
                                <span x-text="'L' + (index + 1) + ' (' + layer.thickness + 'mm)'" class="z-10"></span>
                                
                                <template x-if="layer.angle.toString() === '0'">
                                    <i class="fa-solid fa-arrow-up absolute right-3 text-gray-500 opacity-60 text-[10px]"></i>
                                </template>
                                <template x-if="layer.angle.toString() !== '0'">
                                    <i class="fa-solid fa-rotate-right absolute right-3 text-gray-700 opacity-50 text-[11px]"></i>
                                </template>
                            </div>
                        </template>
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
                        <div class="block" @keydown.enter.prevent="saveLayer()">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="modal-title" x-text="isEdit ? 'Edit Layer' : 'Add New Layer'"></h3>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="col-span-2">
                                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Angle (Deg)</label>
                                            <select x-model="angle" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#447A60] sm:text-sm">
                                                <option value="0">0° (Longitudinal)</option>
                                                <option value="90">90° (Transverse)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Thickness (mm)</label>
                                            <input type="number" step="0.01" x-model="thickness" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#447A60] sm:text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Width (mm)</label>
                                            <input type="number" step="0.01" x-model="width" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#447A60] sm:text-sm" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button" @click="saveLayer()" class="inline-flex w-full justify-center rounded-md bg-[#447A60] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#36614D] sm:ml-3 sm:w-auto transition-colors">
                                    <span x-text="isEdit ? 'Save Changes' : 'Draft Layer'"></span>
                                </button>
                                <button type="button" @click="showLayerModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
