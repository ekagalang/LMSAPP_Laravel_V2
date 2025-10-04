<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Advanced Certificate Template Editor') }}: {{ $certificateTemplate->name }}
            </h2>
            <div class="flex space-x-2">
                <button id="preview-btn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    <i class="fas fa-eye mr-2"></i>Preview
                </button>
                <button id="save-btn" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                    <i class="fas fa-save mr-2"></i>Save Template
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4">
            <div id="certificate-editor" class="flex flex-col lg:flex-row gap-6 h-screen">
                
                <!-- Left Sidebar: Tools & Properties -->
                <div class="lg:w-80 bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                    <!-- Tabs -->
                    <div class="flex border-b">
                        <button class="tab-btn active flex-1 px-3 py-3 text-sm font-medium" data-tab="tools">
                            <i class="fas fa-tools mr-1"></i>Tools
                        </button>
                        <button class="tab-btn flex-1 px-3 py-3 text-sm font-medium" data-tab="properties">
                            <i class="fas fa-cog mr-1"></i>Properties
                        </button>
                        <button class="tab-btn flex-1 px-3 py-3 text-sm font-medium" data-tab="pages">
                            <i class="fas fa-file mr-1"></i>Pages
                        </button>
                        <button class="tab-btn flex-1 px-3 py-3 text-sm font-medium" data-tab="templates">
                            <i class="fas fa-magic mr-1"></i>Templates
                        </button>
                    </div>

                    <!-- Tools Tab -->
                    <div id="tools-tab" class="tab-content flex-1 overflow-y-auto p-4">
                        <div class="space-y-4">
                            <!-- Template Info -->
                            <div class="bg-gray-50 p-3 rounded">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template Name</label>
                                <input type="text" id="template-name" value="{{ $certificateTemplate->name }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Element Tools -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Add Elements</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <button class="tool-btn" data-tool="text" data-content="Sample Text">
                                        <i class="fas fa-font"></i>
                                        <span>Text</span>
                                    </button>
                                    <button class="tool-btn" data-tool="text" data-content="@{{name}}">
                                        <i class="fas fa-user"></i>
                                        <span>Name</span>
                                    </button>
                                    <button class="tool-btn" data-tool="text" data-content="@{{course_title}}">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>Course</span>
                                    </button>
                                    <button class="tool-btn" data-tool="text" data-content="@{{completion_date}}">
                                        <i class="fas fa-calendar"></i>
                                        <span>Date</span>
                                    </button>
                                    <button class="tool-btn" data-tool="text" data-content="@{{instructor_name}}">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        <span>Instructor</span>
                                    </button>
                                    <button class="tool-btn" data-tool="image">
                                        <i class="fas fa-image"></i>
                                        <span>Image</span>
                                    </button>
                                    <button class="tool-btn" data-tool="line">
                                        <i class="fas fa-minus"></i>
                                        <span>Line</span>
                                    </button>
                                    <button class="tool-btn" data-tool="shape">
                                        <i class="fas fa-square"></i>
                                        <span>Shape</span>
                                    </button>
                                </div>
                            </div>


                            <!-- Background Controls -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Background</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Upload New Background</label>
                                        <div class="relative">
                                            <input type="file" id="background-upload" accept="image/*" class="hidden">
                                            <button type="button" onclick="document.getElementById('background-upload').click()" 
                                                    class="w-full p-3 border-2 border-dashed border-gray-300 rounded text-center hover:border-blue-400 transition">
                                                <i class="fas fa-cloud-upload-alt text-gray-400 text-lg mb-1"></i>
                                                <div class="text-xs text-gray-500">Click to upload</div>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Background Color</label>
                                        <input type="color" id="background-color" class="w-full h-10 border rounded cursor-pointer">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Background Size</label>
                                        <select id="background-size" class="w-full px-2 py-1 border rounded text-sm">
                                            <option value="cover">Cover</option>
                                            <option value="contain">Contain</option>
                                            <option value="stretch">Stretch</option>
                                            <option value="repeat">Repeat</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Properties Tab -->
                    <div id="properties-tab" class="tab-content flex-1 overflow-y-auto p-4 hidden">
                        <div id="element-properties" class="space-y-4">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-mouse-pointer text-3xl mb-2"></i>
                                <p>Select an element to edit properties</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pages Tab -->
                    <div id="pages-tab" class="tab-content flex-1 overflow-y-auto p-4 hidden">
                        <div class="space-y-4">
                            <button id="add-page-btn" class="w-full p-3 border-2 border-dashed border-gray-300 rounded text-center hover:border-blue-400 transition">
                                <i class="fas fa-plus text-gray-400 mb-1"></i>
                                <div class="text-sm text-gray-500">Add New Page</div>
                            </button>
                            <div id="pages-list" class="space-y-2">
                                <!-- Pages will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Templates Tab -->
                    <div id="templates-tab" class="tab-content flex-1 overflow-y-auto p-4 hidden">
                        <div class="space-y-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                    <div class="text-sm">
                                        <div class="font-medium text-yellow-800">Warning</div>
                                        <div class="text-yellow-700">Loading a template will replace your current work!</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Quick Start Templates</h3>
                                <div class="space-y-2">
                                    <button class="template-preset w-full text-left p-3 bg-blue-50 hover:bg-blue-100 rounded border-2 border-blue-200 hover:border-blue-400 transition" 
                                            data-template="classic">
                                        <div class="flex items-center">
                                            <i class="fas fa-award text-blue-500 mr-3 text-lg"></i>
                                            <div>
                                                <div class="font-medium text-blue-700">Classic Certificate</div>
                                                <div class="text-xs text-blue-600">Traditional design with elegant borders</div>
                                            </div>
                                        </div>
                                    </button>
                                    
                                    <button class="template-preset w-full text-left p-3 bg-green-50 hover:bg-green-100 rounded border-2 border-green-200 hover:border-green-400 transition" 
                                            data-template="modern">
                                        <div class="flex items-center">
                                            <i class="fas fa-medal text-green-500 mr-3 text-lg"></i>
                                            <div>
                                                <div class="font-medium text-green-700">Modern Design</div>
                                                <div class="text-xs text-green-600">Clean and contemporary layout</div>
                                            </div>
                                        </div>
                                    </button>
                                    
                                    <button class="template-preset w-full text-left p-3 bg-purple-50 hover:bg-purple-100 rounded border-2 border-purple-200 hover:border-purple-400 transition" 
                                            data-template="elegant">
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-purple-500 mr-3 text-lg"></i>
                                            <div>
                                                <div class="font-medium text-purple-700">Elegant Style</div>
                                                <div class="text-xs text-purple-600">Sophisticated and professional</div>
                                            </div>
                                        </div>
                                    </button>
                                    
                                    <button class="template-preset w-full text-left p-3 bg-gray-50 hover:bg-gray-100 rounded border-2 border-gray-200 hover:border-gray-400 transition" 
                                            data-template="blank">
                                        <div class="flex items-center">
                                            <i class="fas fa-plus text-gray-500 mr-3 text-lg"></i>
                                            <div>
                                                <div class="font-medium text-gray-700">Blank Template</div>
                                                <div class="text-xs text-gray-600">Start from scratch</div>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Canvas Area -->
                <div class="flex-1 bg-gray-100 rounded-lg overflow-hidden flex flex-col">
                    <!-- Canvas Toolbar -->
                    <div class="bg-white border-b px-4 py-3 flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <button id="zoom-out" class="p-2 hover:bg-gray-100 rounded">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <span id="zoom-level" class="text-sm font-medium">100%</span>
                                <button id="zoom-in" class="p-2 hover:bg-gray-100 rounded">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button id="zoom-fit" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                                    Fit to Screen
                                </button>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button id="grid-toggle" class="p-2 hover:bg-gray-100 rounded" title="Toggle Grid">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button id="snap-toggle" class="p-2 hover:bg-gray-100 rounded" title="Toggle Snap">
                                    <i class="fas fa-magnet"></i>
                                </button>
                                <button id="ruler-toggle" class="p-2 hover:bg-gray-100 rounded" title="Toggle Rulers">
                                    <i class="fas fa-ruler"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <select id="page-selector" class="px-3 py-1 border rounded text-sm">
                                <option value="0">Page 1</option>
                            </select>
                            <button id="undo-btn" class="p-2 hover:bg-gray-100 rounded" title="Undo">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button id="redo-btn" class="p-2 hover:bg-gray-100 rounded" title="Redo">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Canvas Container -->
                    <div class="flex-1 p-6 overflow-auto" id="canvas-container">
                        <div id="canvas-wrapper" class="mx-auto">
                            <div id="certificate-canvas" class="relative bg-white shadow-lg mx-auto" 
                                 style="width: 794px; height: 1123px; transform-origin: top center;"
                                 data-width="794" data-height="1123">
                                <!-- Canvas content will be rendered here -->
                                <div id="grid-overlay" class="absolute inset-0 pointer-events-none opacity-20 hidden">
                                    <!-- Grid lines will be generated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Layers & History -->
                <div class="lg:w-64 bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                    <div class="flex border-b">
                        <button class="tab-btn active flex-1 px-4 py-3 text-sm font-medium" data-tab="layers">
                            <i class="fas fa-layer-group mr-2"></i>Layers
                        </button>
                        <button class="tab-btn flex-1 px-4 py-3 text-sm font-medium" data-tab="history">
                            <i class="fas fa-history mr-2"></i>History
                        </button>
                    </div>

                    <!-- Layers Tab -->
                    <div id="layers-tab" class="tab-content flex-1 overflow-y-auto p-4">
                        <div id="layers-list" class="space-y-1">
                            <!-- Layers will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div id="history-tab" class="tab-content flex-1 overflow-y-auto p-4 hidden">
                        <div id="history-list" class="space-y-1">
                            <!-- History will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-6xl w-full max-h-full overflow-auto">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Certificate Preview</h3>
                    <button id="close-preview" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div id="preview-content" class="mx-auto" style="max-width: 794px;">
                        <!-- Preview will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="save-form" method="POST" action="{{ route('admin.certificate-templates.update', $certificateTemplate) }}" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="name" id="save-name">
        <input type="hidden" name="layout_data" id="save-layout-data">
        <input type="file" name="backgrounds[]" id="save-backgrounds" multiple style="display: none;">
    </form>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/certificate-templates.css') }}">
    <style>
        .tab-btn.active {
            background-color: #f3f4f6;
            border-bottom: 2px solid #3b82f6;
            color: #3b82f6;
        }

        .canvas-element {
            position: absolute;
            cursor: move;
            user-select: none;
        }

        .canvas-element.selected {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        .canvas-element .resize-handle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border: 1px solid white;
            border-radius: 50%;
        }

        .resize-handle.nw { top: -4px; left: -4px; cursor: nw-resize; }
        .resize-handle.ne { top: -4px; right: -4px; cursor: ne-resize; }
        .resize-handle.sw { bottom: -4px; left: -4px; cursor: sw-resize; }
        .resize-handle.se { bottom: -4px; right: -4px; cursor: se-resize; }

        .layer-item {
            @apply flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer;
        }

        .layer-item.active {
            @apply bg-blue-50 border-l-4 border-blue-500;
        }

        #grid-overlay {
            background-image: 
                linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('js/advanced-certificate-editor.js') }}"></script>
    <script>
        // Initialize editor with existing data
        document.addEventListener('DOMContentLoaded', function() {
            const existingData = @json($certificateTemplate->layout_data);
            if (window.AdvancedCertificateEditor) {
                window.AdvancedCertificateEditor.init(existingData);
            }
        });
    </script>
    @endpush
</x-app-layout>