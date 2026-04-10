<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-[32px] text-gray-900 tracking-tight" style="font-family: 'Merriweather', serif;">Suppliers</h1>
                <p class="mt-1 text-sm text-gray-500">Manage timber suppliers and material sourcing.</p>
            </div>
            
            <div class="flex items-center">
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-md bg-[#447A60] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#36614D] transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Supplier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-2">
            <div class="w-full sm:max-w-xs">
                <label for="search" class="sr-only">Search suppliers by name...</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" id="search" class="block w-full rounded-md border-gray-300 pl-10 focus:border-[#447A60] focus:ring-[#447A60] sm:text-sm text-gray-500 placeholder-gray-400 py-2.5" placeholder="Search suppliers by name...">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </button>
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
                                    ['bg' => 'bg-[#EBF1FA]', 'text' => 'text-[#4A72B2]'], // NT
                                    ['bg' => 'bg-[#E6F3EE]', 'text' => 'text-[#3E8D61]'], // AC
                                    ['bg' => 'bg-[#FDF0E6]', 'text' => 'text-[#CC6A33]'], // MW
                                    ['bg' => 'bg-[#F4EBFA]', 'text' => 'text-[#8A51B2]'], // TS
                                    ['bg' => 'bg-[#E6F5F3]', 'text' => 'text-[#3B9282]']  // EL
                                ];
                                $initials = strtoupper(substr($supplier->name, 0, 2));
                                $colorStyle = $colors[$loop->index % count($colors)];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors relative group/row">
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
                                    {{ $supplier->layups_count ?? 0 }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-[15px] text-gray-600">
                                    {{ optional($supplier->created_at)->format('M d, Y') ?? 'Oct 24, 2023' }}
                                </td>
                                <td class="whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Delete this supplier?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
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
            
            <div class="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-100">
                <div class="text-sm text-gray-500">
                    Showing 1 to {{ count($suppliers) }} of {{ count($suppliers) }} results
                </div>
                <div class="flex items-center gap-2">
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
