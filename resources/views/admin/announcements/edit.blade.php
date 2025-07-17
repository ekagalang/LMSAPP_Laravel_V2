<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Pengumuman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        {{-- Memanggil form fields dari partial --}}
                        @include('admin.announcements.partials.form-fields', ['announcement' => $announcement])
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.announcements.index') }}" class="text-sm text-gray-600 hover:underline mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Perbarui') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
