<div x-show="showImportModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showImportModal = false"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px]">
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-[17px] font-bold text-gray-900" style="font-family: 'Merriweather', serif;" id="modal-title">Import Layup Data</h3>
                    <button type="button" @click="showImportModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-6">
                    
                    <div class="mt-2 flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-10 hover:bg-gray-50 transition-colors cursor-pointer" x-data="{ isDragging: false }" @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false" @drop.prevent="isDragging = false; handleDrop($event)" :class="{'bg-[#F0F7F4] border-[#447A60]': isDragging}">
                        <div class="text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white border border-gray-100 shadow-sm mb-4">
                                <i class="fa-solid fa-cloud-arrow-up text-[#447A60] text-xl"></i>
                            </div>
                            <div class="mt-2 text-sm leading-6 text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer rounded-md font-semibold text-[#447A60] focus-within:outline-none focus-within:ring-2 focus-within:ring-[#447A60] focus-within:ring-offset-2 hover:text-[#36614D]">
                                    <span>Click to upload</span>
                                    <input id="file-upload" name="file-upload" type="file" accept=".json,.csv" class="sr-only" @change="handleFileSelect">
                                </label>
                                <span class="pl-1">or drag and drop</span>
                            </div>
                            <p class="text-[11px] leading-5 text-gray-400 mt-1" x-text="importState.fileName ? importState.fileName : 'JSON up to 10MB'"></p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="conflict-strategy" class="block text-[11px] font-medium text-gray-500 mb-1.5">Conflict Resolution Strategy</label>
                        <div class="relative">
                            <select id="conflict-strategy" x-model="importState.strategy" class="block w-full appearance-none rounded-md border border-gray-200 py-2.5 pl-3 pr-10 text-sm text-gray-900 focus:border-[#447A60] focus:outline-none focus:ring-[#447A60] shadow-sm">
                                <option value="skip">Skip conflicts (Default)</option>
                                <option value="overwrite">Overwrite existing data</option>
                                <option value="duplicate">Create duplicates</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fa-solid fa-chevron-down text-gray-400 text-[11px]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 border border-gray-200 rounded-lg p-4 flex items-start gap-3 relative hover:border-gray-300 transition-colors">
                        <div class="flex h-6 items-center">
                            <input id="dry-run" x-model="importState.dryRun" name="dry-run" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#447A60] focus:ring-[#447A60] cursor-pointer">
                        </div>
                        <div class="flex-1">
                            <label for="dry-run" class="font-medium text-sm text-gray-900 cursor-pointer select-none">Run as Dry Run</label>
                            <p class="text-gray-500 text-[12px] leading-relaxed mt-0.5">Simulate the import process without saving changes to the database.</p>
                        </div>
                        <div class="absolute right-4 top-4 text-gray-400">
                            <i class="fa-solid fa-flask text-lg" :class="importState.dryRun ? 'text-[#447A60]' : ''"></i>
                        </div>
                    </div>

                    <div class="mt-5 rounded-lg p-4 flex gap-3" x-show="importState.results && importState.results.success && !importState.results.stats?.conflicts_detected" x-transition :class="importState.results?.success ? 'bg-[#F0FDF4] border border-[#DCFCE7]' : 'bg-[#FEF2F2] border border-[#FEE2E2]'">
                        <div class="flex-shrink-0 mt-0.5" x-show="!importState.results?.success">
                            <i class="fa-solid fa-circle-xmark text-[#EF4444]"></i>
                        </div>
                        <div class="flex-shrink-0 mt-0.5" x-show="importState.results?.success">
                            <i class="fa-solid fa-check-circle text-[#22C55E]"></i>
                        </div>
                        <div class="w-full">
                            <h3 class="text-sm font-bold" :class="importState.results?.success ? 'text-[#166534]' : 'text-[#B91C1C]'" x-text="importState.results?.message"></h3>
                            
                            <div class="mt-2 text-[12px] flex flex-col gap-1" :class="importState.results?.success ? 'text-[#166534]' : 'text-[#DC2626]'" x-show="importState.results?.stats && importState.results.success">
                                <div class="grid grid-cols-3 gap-2 mt-1 bg-white/50 p-2 rounded">
                                    <div><span class="font-bold">Created:</span> <span x-text="importState.results?.stats?.created"></span></div>
                                    <div><span class="font-bold">Updated:</span> <span x-text="importState.results?.stats?.updated"></span></div>
                                    <div><span class="font-bold">Skipped:</span> <span x-text="importState.results?.stats?.skipped"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-lg bg-[#FEF2F2] border border-[#FEE2E2] p-4 flex gap-3" x-show="importState.results?.stats?.conflicts_detected > 0" x-transition>
                        <div class="flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-triangle-exclamation text-[#B91C1C] text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-[14px] font-bold text-[#991B1B]" style="font-family: 'Merriweather', serif;">Potential Conflicts Detected</h3>
                            <div class="mt-1.5 text-[13px] text-[#B91C1C]">
                                <p><span x-text="importState.results?.stats?.conflicts_detected"></span> Layups differ significantly from current suppliers in the database. <a href="#" @click.prevent="viewConflicts()" class="font-medium underline hover:text-[#7f1d1d] ml-1">View details</a></p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex items-center justify-end gap-3 bg-gray-50/50">
                    <button type="button" @click="cancelImport()" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors text-center w-full sm:w-auto mt-3 sm:mt-0">
                        Cancel
                    </button>
                    <button type="button" @click="uploadData()" class="inline-flex items-center justify-center rounded-md bg-[#447A60] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#36614D] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#447A60] text-center w-full sm:w-auto" :disabled="importState.isProcessing">
                        <i class="fa-solid" :class="importState.isProcessing ? 'fa-circle-notch fa-spin mr-2' : 'fa-file-import mr-2'"></i>
                        <span x-text="importState.isProcessing ? 'Processing...' : 'Confirm Import'"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
