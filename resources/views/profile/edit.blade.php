<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl text-gray-900 leading-tight" style="font-family: 'Merriweather', serif; font-weight: 700;">
            Settings
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="space-y-6">
            <div class="p-6 sm:p-8 bg-white shadow-sm ring-1 ring-gray-200 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow-sm ring-1 ring-gray-200 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow-sm ring-1 ring-gray-200 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
