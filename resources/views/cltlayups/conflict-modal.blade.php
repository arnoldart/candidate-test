<div x-show="showConflictModal" class="relative z-[60]" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div x-show="showConflictModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="if(Object.keys(conflictState.resolutions).length === conflictState.conflicts.length) { showConflictModal = false; }"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showConflictModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all w-full max-w-6xl h-[85vh] flex flex-col">
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                    <div>
                        <h3 class="text-[17px] font-bold text-gray-900 flex items-center gap-2" style="font-family: 'Merriweather', serif;">
                            Conflict Resolution: Import <span x-text="importState.fileName ? '[' + importState.fileName + ']' : ''" class="text-[#447A60]"></span>
                            <span class="ml-2 inline-flex items-center rounded-full bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Needs Review</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Please review discrepancies between incoming data and existing records.</p>
                    </div>
                    <button type="button" @click="showConflictModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="flex flex-1 overflow-hidden" x-data="{
                        get currentConflict() { return conflictState.conflicts[conflictState.currentIndex] || null; },
                        get maxLayers() {
                           if (!this.currentConflict) return 0;
                           return Math.max(this.currentConflict.existing_layers.length, this.currentConflict.importing_layers.length);
                        },
                        isDiffering(field, orderIndex) {
                           if (!this.currentConflict) return false;
                           let ex = this.currentConflict.existing_layers.find(l => l.order == orderIndex);
                           let im = this.currentConflict.importing_layers.find(l => l.order == orderIndex);
                           if (!ex || !im) return true;
                           return ex[field] !== im[field];
                        }
                    }">
                    
                    <div class="w-[30%] border-r border-gray-100 bg-[#FAFAFA] flex flex-col overflow-y-auto">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3 text-sm font-bold text-gray-900 border-b border-gray-200 pb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                                    <span>Conflicting Layups (<span x-text="conflictState.conflicts.length"></span>)</span>
                                </div>
                                <div class="flex gap-1 text-gray-400">
                                    <i class="fa-solid fa-chevron-up text-xs cursor-pointer"></i>
                                    <i class="fa-solid fa-chevron-down text-xs cursor-pointer"></i>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <template x-for="(conflict, index) in conflictState.conflicts" :key="conflict.layup_name">
                                    <div @click="conflictState.currentIndex = index" class="p-3 rounded-lg border cursor-pointer hover:shadow-sm transition-colors relative"
                                         :class="conflictState.currentIndex === index 
                                             ? 'bg-[#F0F7F4] border-[#447A60]' 
                                             : (conflictState.resolutions[conflict.layup_name] 
                                                  ? 'bg-white border-gray-200 opacity-60' 
                                                  : 'bg-white border-gray-200')">
                                        
                                        <div class="flex justify-between items-start">
                                            <div class="font-bold text-sm text-gray-900" :class="conflictState.resolutions[conflict.layup_name] ? 'line-through text-gray-500' : ''" x-text="conflict.layup_name"></div>
                                            <div>
                                                <i x-show="conflictState.resolutions[conflict.layup_name]" class="fa-regular fa-circle-check text-[#22C55E]"></i>
                                                <div x-show="!conflictState.resolutions[conflict.layup_name]" class="w-2 h-2 rounded-full bg-red-600 mt-1"></div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1" x-text="conflictState.resolutions[conflict.layup_name] ? 'Resolved' : 'Discrepancies found'"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <div class="mt-auto p-4 border-t border-gray-200 bg-white">
                            <button type="button" @click="cancelImport()" class="w-full inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                                Cancel Import
                            </button>
                        </div>
                    </div>

                    <div class="w-[70%] bg-white flex flex-col h-full bg-[#fcfcfc]" x-show="currentConflict">
                        
                        <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                            <div class="flex items-center gap-3">
                                <h2 class="text-xl font-bold text-gray-900" x-text="currentConflict.layup_name + ' Comparison'"></h2>
                                <span class="inline-flex items-center rounded-md bg-[#F4EDE4] px-2 py-1 text-xs font-semibold text-[#8B7D6B] uppercase tracking-wider">
                                    <span x-text="currentConflict.existing_layers.length"></span> Layers
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500 flex items-center gap-1.5 font-medium">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div> Differences highlighted in Red
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-8 py-6 flex gap-6">
                            
                            <div class="w-1/2 flex flex-col relative rounded-xl border border-gray-200 bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] h-fit">
                                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center rounded-t-xl bg-gray-50">
                                    <div>
                                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                            <i class="fa-solid fa-database text-gray-400"></i> Existing Version
                                        </h3>
                                        <p class="text-[11px] text-gray-500 mt-0.5">Currently stored in database</p>
                                    </div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-gray-300"></div>
                                </div>
                                <div class="p-0">
                                    <table class="min-w-full divide-y divide-gray-100 text-sm md:text-xs lg:text-sm">
                                        <thead class="bg-white">
                                            <tr>
                                                <th scope="col" class="py-3 pl-4 pr-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Order</th>
                                                <th scope="col" class="py-3 px-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Thickness <br><span class="lowercase text-gray-400 font-normal">(mm)</span></th>
                                                <th scope="col" class="py-3 px-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Width <br><span class="lowercase text-gray-400 font-normal">(mm)</span></th>
                                                <th scope="col" class="py-3 px-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Angle <br><span class="lowercase text-gray-400 font-normal">(°)</span></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white">
                                            <template x-for="i in maxLayers">
                                                <tr :class="isDiffering('thickness', i) || isDiffering('width', i) || isDiffering('angle', i) ? 'bg-[#FEF2F2]' : 'hover:bg-gray-50'">
                                                    <td class="whitespace-nowrap py-3 pl-4 pr-3 text-gray-900 font-medium" x-text="i"></td>
                                                    <td class="whitespace-nowrap py-3 px-3" 
                                                        :class="isDiffering('thickness', i) ? 'text-red-600 font-bold' : 'text-gray-600'" 
                                                        x-text="(currentConflict.existing_layers.find(l => l.order == i) || {}).thickness ?? '-'"></td>
                                                    <td class="whitespace-nowrap py-3 px-3" 
                                                        :class="isDiffering('width', i) ? 'text-red-600 font-bold' : 'text-gray-600'" 
                                                        x-text="(currentConflict.existing_layers.find(l => l.order == i) || {}).width ?? '-'"></td>
                                                    <td class="whitespace-nowrap py-3 px-3" 
                                                        :class="isDiffering('angle', i) ? 'text-red-600 font-bold' : 'text-gray-600'" 
                                                        x-text="(currentConflict.existing_layers.find(l => l.order == i) || {}).angle ?? '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-4 mt-4 text-center">
                                    <button type="button" @click="resolveConflict('skip')" class="w-full inline-flex items-center justify-center rounded-lg border-2 border-[#447A60] bg-white px-4 py-3 text-sm font-bold text-[#447A60] shadow-sm hover:bg-[#F0F7F4] transition-colors focus:ring-2 focus:ring-[#447A60] focus:ring-offset-2">
                                        <i class="fa-solid fa-rotate-left mr-2"></i> Keep Existing
                                    </button>
                                </div>
                            </div>

                            <div class="w-1/2 flex flex-col relative rounded-xl border border-gray-200 bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] h-fit">
                                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center rounded-t-xl bg-[#F0F7F4]">
                                    <div>
                                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                            <i class="fa-solid fa-file-arrow-up text-[#447A60]"></i> Importing Version
                                        </h3>
                                        <p class="text-[11px] text-gray-500 mt-0.5">Source: Uploaded File</p>
                                    </div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#447A60]"></div>
                                </div>
                                <div class="p-0">
                                    <table class="min-w-full divide-y divide-gray-100 text-sm md:text-xs lg:text-sm">
                                        <thead class="bg-white">
                                            <tr>
                                                <th scope="col" class="py-3 pl-4 pr-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Order</th>
                                                <th scope="col" class="py-3 px-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Thickness <br><span class="lowercase text-gray-400 font-normal">(mm)</span></th>
                                                <th scope="col" class="py-3 px-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Width <br><span class="lowercase text-gray-400 font-normal">(mm)</span></th>
                                                <th scope="col" class="py-3 px-3 text-left font-bold text-gray-500 uppercase tracking-wider text-[10px]">Angle <br><span class="lowercase text-gray-400 font-normal">(°)</span></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white">
                                            <template x-for="i in maxLayers">
                                                <tr :class="isDiffering('thickness', i) || isDiffering('width', i) || isDiffering('angle', i) ? 'bg-[#FEF2F2]' : 'hover:bg-gray-50'">
                                                    <td class="whitespace-nowrap py-3 pl-4 pr-3 text-gray-900 font-medium" x-text="i"></td>
                                                    <td class="whitespace-nowrap py-3 px-3" 
                                                        :class="isDiffering('thickness', i) ? 'text-red-600 font-bold' : 'text-gray-600'" 
                                                        x-text="(currentConflict.importing_layers.find(l => l.order == i) || {}).thickness ?? '-'"></td>
                                                    <td class="whitespace-nowrap py-3 px-3" 
                                                        :class="isDiffering('width', i) ? 'text-red-600 font-bold' : 'text-gray-600'" 
                                                        x-text="(currentConflict.importing_layers.find(l => l.order == i) || {}).width ?? '-'"></td>
                                                    <td class="whitespace-nowrap py-3 px-3" 
                                                        :class="isDiffering('angle', i) ? 'text-red-600 font-bold' : 'text-gray-600'" 
                                                        x-text="(currentConflict.importing_layers.find(l => l.order == i) || {}).angle ?? '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-4 mt-4 text-center">
                                    <button type="button" @click="resolveConflict('overwrite')" class="w-full inline-flex items-center justify-center rounded-lg bg-[#447A60] px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-[#36614D] transition-colors focus:ring-2 focus:ring-[#447A60] focus:ring-offset-2">
                                        <i class="fa-solid fa-check mr-2"></i> Accept New
                                    </button>
                                </div>
                            </div>

                        </div>

                        <div class="px-8 py-4 border-t border-gray-200 flex justify-between items-center shrink-0 bg-white rounded-br-xl">
                            <button type="button" @click="changeConflictIndex(-1)" :disabled="conflictState.currentIndex === 0" class="inline-flex items-center text-sm font-semibold text-gray-700 hover:text-gray-900 focus:outline-none disabled:opacity-30 transition-colors">
                                <i class="fa-solid fa-arrow-left mr-2"></i> Previous Conflict
                            </button>
                            
                            <div class="text-xs font-bold text-gray-600 uppercase tracking-widest text-center" x-show="Object.keys(conflictState.resolutions).length < conflictState.conflicts.length">
                                <span x-text="conflictState.currentIndex + 1"></span> of <span x-text="conflictState.conflicts.length"></span> Discrepancies
                            </div>

                            <button type="button" x-show="Object.keys(conflictState.resolutions).length === conflictState.conflicts.length" @click="applyAllResolutions()" class="inline-flex items-center justify-center rounded-md bg-[#447A60] px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                                <i class="fa-solid fa-bolt mr-2"></i> Apply Resolutions & Import
                            </button>

                            <button type="button" x-show="Object.keys(conflictState.resolutions).length < conflictState.conflicts.length" @click="changeConflictIndex(1)" :disabled="conflictState.currentIndex === conflictState.conflicts.length - 1" class="inline-flex items-center text-sm font-semibold text-[#447A60] hover:text-[#36614D] focus:outline-none disabled:opacity-30 transition-colors">
                                Next Conflict <i class="fa-solid fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
