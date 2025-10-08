<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Certificate Templates') }}
        </h2>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg" style="min-height: calc(100vh - 200px);">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-end mb-4 space-x-2">
                        <div class="relative group">
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Create New Template') }}
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 border border-gray-200">
                                <div class="py-1">
                                    <a href="{{ route('admin.certificate-templates.create') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100">
                                        <div class="font-medium">Basic Editor</div>
                                        <div class="text-xs text-gray-500">Simple drag-and-drop interface</div>
                                    </a>
                                    <a href="{{ route('admin.certificate-templates.create-enhanced') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100">
                                        <div class="font-medium">Enhanced Editor ✨</div>
                                        <div class="text-xs text-gray-500">Advanced tools, grid, zoom, and more controls</div>
                                    </a>
                                    <a href="{{ route('admin.certificate-templates.create-advanced') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                        <div class="font-medium">Advanced Editor 🚀</div>
                                        <div class="text-xs text-gray-500">Professional editor with layers, history, and templates</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="relative shadow-md sm:rounded-lg" style="overflow: visible;">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ __('Created At') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $template)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $template->name }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $template->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="relative group inline-block">
                                                <button class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                                    Edit
                                                    <svg class="w-3 h-3 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                
                                                <!-- Dropdown Menu -->
                                                <div class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 border border-gray-200">
                                                    <div class="py-1">
                                                        <a href="{{ route('admin.certificate-templates.edit', $template) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Basic Editor
                                                        </a>
                                                        <a href="{{ route('admin.certificate-templates.edit-enhanced', $template) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Enhanced Editor ✨
                                                        </a>
                                                        <a href="{{ route('admin.certificate-templates.edit-advanced', $template) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Advanced Editor 🚀
                                                        </a>
                                                        <hr class="my-1">
                                                        <a href="{{ route('admin.certificate-templates.preview', $template) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-eye mr-2"></i>Preview
                                                        </a>
                                                        <hr class="my-1">
                                                        <form action="{{ route('admin.certificate-templates.duplicate', $template) }}" method="POST" class="block">
                                                            @csrf
                                                            <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="return confirm('Are you sure you want to duplicate this template?');">
                                                                <i class="fas fa-copy mr-2"></i>Duplicate
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <form action="{{ route('admin.certificate-templates.destroy', $template) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this template?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4 text-center" colspan="3">
                                            No templates found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
