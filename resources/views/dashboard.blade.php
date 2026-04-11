@php
    use App\Models\Supplier;
    use App\Models\CltLayup;
    use App\Models\CltLayer;
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl text-gray-900 leading-tight" style="font-family: 'Merriweather', serif; font-weight: 700;">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#E6F3EE] rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-industry text-[#447A60] text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-medium uppercase tracking-widest">Suppliers</p>
                            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ Supplier::count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#FEF3C7] rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-layer-group text-[#D97706] text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-medium uppercase tracking-widest">CLT Layups</p>
                            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ CltLayup::count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#EDE9FE] rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-bars-staggered text-[#7C3AED] text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-medium uppercase tracking-widest">Total Layers</p>
                            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ CltLayer::count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#DBEAFE] rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-check-double text-[#2563EB] text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-medium uppercase tracking-widest">Active Layups</p>
                            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ CltLayup::where('status', 'Active')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-[15px] font-bold text-gray-900" style="font-family: 'Merriweather', serif;">Recent Suppliers</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-white">
                                <tr>
                                    <th class="py-3 pl-6 pr-3 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Name</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Location</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Layups</th>
                                    <th class="py-3 pl-3 pr-6 text-left text-xs font-bold text-gray-400 tracking-widest uppercase">Last Audit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach(Supplier::withCount('cltLayups')->latest()->take(5)->get() as $s)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="whitespace-nowrap py-3.5 pl-6 pr-3 text-sm font-medium text-gray-900">{{ $s->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-3.5 text-sm text-gray-500">{{ $s->location ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3.5">
                                        <span class="inline-flex items-center justify-center rounded bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">{{ $s->clt_layups_count }}</span>
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 pl-3 pr-6 text-sm text-gray-400">{{ $s->last_audit_date ? Carbon::parse($s->last_audit_date)->format('M d, Y') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-200 rounded-xl">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-[15px] font-bold text-gray-900" style="font-family: 'Merriweather', serif;">Quick Actions</h3>
                    </div>
                    <div class="p-6 flex flex-col gap-3">
                        <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-[#447A60] hover:bg-[#F0F7F4] transition-colors group">
                            <div class="w-9 h-9 bg-[#E6F3EE] rounded-lg flex items-center justify-center shrink-0 group-hover:bg-[#447A60] transition-colors">
                                <i class="fa-solid fa-industry text-[#447A60] text-sm group-hover:text-white transition-colors"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Manage Suppliers</p>
                                <p class="text-[11px] text-gray-500">View, add, edit suppliers</p>
                            </div>
                        </a>
                        <a href="{{ route('suppliers.exportAll') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-[#447A60] hover:bg-[#F0F7F4] transition-colors group">
                            <div class="w-9 h-9 bg-[#FEF3C7] rounded-lg flex items-center justify-center shrink-0 group-hover:bg-[#D97706] transition-colors">
                                <i class="fa-solid fa-file-export text-[#D97706] text-sm group-hover:text-white transition-colors"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Export All Data</p>
                                <p class="text-[11px] text-gray-500">Download all suppliers as JSON</p>
                            </div>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-[#447A60] hover:bg-[#F0F7F4] transition-colors group">
                            <div class="w-9 h-9 bg-[#EDE9FE] rounded-lg flex items-center justify-center shrink-0 group-hover:bg-[#7C3AED] transition-colors">
                                <i class="fa-solid fa-gear text-[#7C3AED] text-sm group-hover:text-white transition-colors"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Account Settings</p>
                                <p class="text-[11px] text-gray-500">Profile and preferences</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
