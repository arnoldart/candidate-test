<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Supplier</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Update the supplier name and metadata.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="p-6">
                    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Supplier Name')" />
                            <x-text-input id="name" name="name" type="text" value="{{ old('name', $supplier->name) }}" class="mt-2 block w-full" placeholder="e.g. Nordic Timber Co." />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <a href="{{ route('suppliers.index') }}" class="inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200 dark:hover:bg-gray-900">Cancel</a>
                            <x-primary-button class="rounded-full px-6 py-3">Update Supplier</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
