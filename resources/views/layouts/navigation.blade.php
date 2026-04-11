<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-[72px]">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center pr-8 border-r border-gray-100 h-10">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 decoration-none">
                        <div class="w-10 h-10 bg-[#3b7e5c] rounded items-center justify-center flex text-white relative">
                            <i class="fa-solid fa-layer-group text-lg"></i>
                        </div>
                        <div class="flex flex-col uppercase tracking-wide leading-tight">
                            <span class="text-[15px] font-bold text-gray-900 tracking-normal m-0 p-0 leading-none">CLT Layup</span>
                            <span class="text-[10px] font-medium text-gray-500 tracking-widest mt-0.5 leading-none">MANAGER</span>
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-8 sm:flex h-full">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-[#3b7e5c] text-[#3b7e5c] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('suppliers.*') ? 'border-[#3b7e5c] text-[#3b7e5c] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Suppliers
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('suppliers.layups.*') ? 'border-[#3b7e5c] text-[#3b7e5c] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Layups
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('layups.layers.*') ? 'border-[#3b7e5c] text-[#3b7e5c] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Layers
                    </a>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('profile.*') ? 'border-[#3b7e5c] text-[#3b7e5c] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Settings
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-6">
                <button class="text-gray-400 hover:text-gray-500 relative flex items-center justify-center pt-1">
                    <span class="absolute top-[2px] right-[-2px] block h-1.5 w-1.5 rounded-full bg-red-500"></span>
                    <i class="fa-regular fa-bell text-xl"></i>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 focus:outline-none transition ease-in-out duration-150 py-2">
                            <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-user text-sm"></i>
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
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out w-10 h-10">
                    <i :class="{'hidden': open, 'inline-block': ! open }" class="fa-solid fa-bars text-xl inline-block"></i>
                    <i :class="{'hidden': ! open, 'inline-block': open }" class="fa-solid fa-xmark text-xl hidden"></i>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                Suppliers
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.layups.*')">
                Layups
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('layups.layers.*')">
                Layers
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
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
