<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-[72px]">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center pr-8 border-r border-gray-100 h-10">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 decoration-none">
                        <div class="w-10 h-10 bg-[#3b7e5c] rounded items-center justify-center flex text-white relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m0-18l-8 8h16l-8-8zm0 18l-4-4h8l-4 4z"/></svg> 
                        </div>
                        <div class="flex flex-col uppercase tracking-wide leading-tight">
                            <span class="text-[15px] font-bold text-gray-900 tracking-normal m-0 p-0 leading-none">CLT Layup</span>
                            <span class="text-[10px] font-medium text-gray-500 tracking-widest mt-0.5 leading-none">MANAGER</span>
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-8 sm:flex h-full">
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                        Overview
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-[#3b7e5c] text-sm font-semibold text-[#3b7e5c] transition duration-150 ease-in-out">
                        Suppliers
                    </a>
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                        Layups
                    </a>
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                        Layers
                    </a>
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150 ease-in-out">
                        Settings
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-6">
                <button class="text-gray-400 hover:text-gray-500 relative">
                    <span class="absolute top-[3px] right-[4px] block h-1.5 w-1.5 rounded-full bg-red-500"></span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 focus:outline-none transition ease-in-out duration-150 py-2">
                            <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="text-left hidden md:block">
                                <div class="text-sm font-semibold text-gray-900 leading-none">{{ Auth::user()->name ?? 'Alex Morgan' }}</div>
                                <div class="text-[11px] text-gray-500 mt-1 leading-none">Engineering Lead</div>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="#" :active="false">
                Overview
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                Suppliers
            </x-responsive-nav-link>
            <x-responsive-nav-link href="#" :active="false">
                Layups
            </x-responsive-nav-link>
            <x-responsive-nav-link href="#" :active="false">
                Layers
            </x-responsive-nav-link>
            <x-responsive-nav-link href="#" :active="false">
                Settings
            </x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name ?? 'Alex Morgan' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email ?? 'alex.morgan@example.com' }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
