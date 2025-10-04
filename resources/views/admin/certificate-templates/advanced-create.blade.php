<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Certificate Template - Advanced Editor') }}
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
                                <input type="text" id="template-name" placeholder="Enter template name..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Quick Start Templates -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Quick Start Templates</h3>
                                <div class="space-y-2">
                                    <button class="template-preset w-full text-left p-3 bg-blue-50 hover:bg-blue-100 rounded border-2 border-blue-200 hover:border-blue-400 transition" 
                                            data-template="classic">
                                        <div class="flex items-center">
                                            <i class="fas fa-award text-blue-500 mr-3 text-xl"></i>
                                            <div>
                                                <div class="font-medium text-blue-700">Classic Certificate</div>
                                                <div class="text-xs text-blue-600">Traditional design with elegant borders</div>
                                            </div>
                                        </div>
                                    </button>
                                    
                                    <button class="template-preset w-full text-left p-3 bg-green-50 hover:bg-green-100 rounded border-2 border-green-200 hover:border-green-400 transition" 
                                            data-template="modern">
                                        <div class="flex items-center">
                                            <i class="fas fa-medal text-green-500 mr-3 text-xl"></i>
                                            <div>
                                                <div class="font-medium text-green-700">Modern Design</div>
                                                <div class="text-xs text-green-600">Clean and contemporary layout</div>
                                            </div>
                                        </div>
                                    </button>
                                    
                                    <button class="template-preset w-full text-left p-3 bg-purple-50 hover:bg-purple-100 rounded border-2 border-purple-200 hover:border-purple-400 transition" 
                                            data-template="elegant">
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-purple-500 mr-3 text-xl"></i>
                                            <div>
                                                <div class="font-medium text-purple-700">Elegant Style</div>
                                                <div class="text-xs text-purple-600">Sophisticated and professional</div>
                                            </div>
                                        </div>
                                    </button>
                                    
                                    <button class="template-preset w-full text-left p-3 bg-gray-50 hover:bg-gray-100 rounded border-2 border-gray-200 hover:border-gray-400 transition" 
                                            data-template="blank">
                                        <div class="flex items-center">
                                            <i class="fas fa-plus text-gray-500 mr-3 text-xl"></i>
                                            <div>
                                                <div class="font-medium text-gray-700">Blank Template</div>
                                                <div class="text-xs text-gray-600">Start from scratch</div>
                                            </div>
                                        </div>
                                    </button>
                                </div>
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
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Upload Background</label>
                                        <div class="relative">
                                            <input type="file" id="background-upload" accept="image/*" class="hidden">
                                            <button type="button" onclick="document.getElementById('background-upload').click()" 
                                                    class="w-full p-3 border-2 border-dashed border-gray-300 rounded text-center hover:border-blue-400 transition">
                                                <i class="fas fa-cloud-upload-alt text-gray-400 text-lg mb-1"></i>
                                                <div class="text-xs text-gray-500">Click to upload image</div>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Background Color</label>
                                        <input type="color" id="background-color" value="#ffffff" class="w-full h-10 border rounded cursor-pointer">
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
                                <button id="snap-toggle" class="p-2 hover:bg-gray-100 rounded active" title="Toggle Snap">
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
                            <button id="undo-btn" class="p-2 hover:bg-gray-100 rounded" title="Undo" disabled>
                                <i class="fas fa-undo"></i>
                            </button>
                            <button id="redo-btn" class="p-2 hover:bg-gray-100 rounded" title="Redo" disabled>
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Canvas Container -->
                    <div class="flex-1 p-6 overflow-auto" id="canvas-container">
                        <div id="canvas-wrapper" class="mx-auto">
                            <div id="certificate-canvas" class="relative bg-white shadow-lg mx-auto" 
                                 style="width: 794px; height: 1123px; transform-origin: top center;">
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
                            <div class="text-center text-gray-500 py-4">
                                <i class="fas fa-layer-group text-2xl mb-2"></i>
                                <p class="text-sm">No elements yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div id="history-tab" class="tab-content flex-1 overflow-y-auto p-4 hidden">
                        <div id="history-list" class="space-y-1">
                            <div class="text-center text-gray-500 py-4">
                                <i class="fas fa-history text-2xl mb-2"></i>
                                <p class="text-sm">History will appear here</p>
                            </div>
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

    <!-- Template Selection Modal -->
    <div id="template-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold">Choose a Starting Template</h3>
                    <p class="text-gray-600 mt-1">Select a template to get started, or create from scratch</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Template Options -->
                        <div class="template-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-400 transition" data-template="classic">
                            <div class="aspect-w-16 aspect-h-9 bg-blue-50 rounded mb-3 flex items-center justify-center">
                                <i class="fas fa-award text-blue-500 text-4xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800">Classic Certificate</h4>
                            <p class="text-sm text-gray-600">Traditional design with elegant borders and formal layout</p>
                        </div>
                        
                        <div class="template-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-green-400 transition" data-template="modern">
                            <div class="aspect-w-16 aspect-h-9 bg-green-50 rounded mb-3 flex items-center justify-center">
                                <i class="fas fa-medal text-green-500 text-4xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800">Modern Design</h4>
                            <p class="text-sm text-gray-600">Clean and contemporary layout with minimal elements</p>
                        </div>
                        
                        <div class="template-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-purple-400 transition" data-template="elegant">
                            <div class="aspect-w-16 aspect-h-9 bg-purple-50 rounded mb-3 flex items-center justify-center">
                                <i class="fas fa-star text-purple-500 text-4xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800">Elegant Style</h4>
                            <p class="text-sm text-gray-600">Sophisticated and professional with refined typography</p>
                        </div>
                        
                        <div class="template-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-gray-400 transition" data-template="blank">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-50 rounded mb-3 flex items-center justify-center">
                                <i class="fas fa-plus text-gray-400 text-4xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800">Blank Template</h4>
                            <p class="text-sm text-gray-600">Start from scratch with a blank canvas</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.certificate-templates.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button id="start-editing" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition" disabled>
                            Start Editing
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="save-form" method="POST" action="{{ route('admin.certificate-templates.store') }}" class="hidden">
        @csrf
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

        .template-option.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
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
        document.addEventListener('DOMContentLoaded', function() {
            let selectedTemplate = null;
            
            // Template selection handling
            document.querySelectorAll('.template-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selection from all options
                    document.querySelectorAll('.template-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Add selection to clicked option
                    this.classList.add('selected');
                    selectedTemplate = this.dataset.template;
                    
                    // Enable start button
                    document.getElementById('start-editing').disabled = false;
                });
            });
            
            // Start editing button
            document.getElementById('start-editing').addEventListener('click', function() {
                if (selectedTemplate) {
                    // Hide template selection modal
                    document.getElementById('template-modal').classList.add('hidden');
                    
                    // Initialize editor with selected template
                    if (window.AdvancedCertificateEditor) {
                        const templateData = getTemplateData(selectedTemplate);
                        window.editor = new window.AdvancedCertificateEditor();
                        window.editor.init(templateData);
                    }
                }
            });
            
            // Template presets in sidebar
            document.querySelectorAll('.template-preset').forEach(preset => {
                preset.addEventListener('click', function() {
                    const template = this.dataset.template;
                    if (window.editor && template) {
                        const templateData = getTemplateData(template);
                        window.editor.loadTemplate(templateData);
                    }
                });
            });
            
            function getTemplateData(templateType) {
                const templates = {
                    blank: [{
                        id: Date.now(),
                        name: 'Page 1',
                        width: 794,
                        height: 1123,
                        backgroundColor: '#ffffff',
                        backgroundSize: 'cover',
                        elements: []
                    }],
                    classic: [{
                        id: Date.now(),
                        name: 'Page 1',
                        width: 794,
                        height: 1123,
                        backgroundColor: '#f8f9fa',
                        backgroundSize: 'cover',
                        elements: [
                            {
                                id: 1,
                                type: 'text',
                                content: 'CERTIFICATE OF COMPLETION',
                                x: 197,
                                y: 150,
                                width: 400,
                                height: 50,
                                fontSize: 28,
                                fontFamily: 'Times New Roman',
                                color: '#2c3e50',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 1
                            },
                            {
                                id: 2,
                                type: 'text',
                                content: 'This is to certify that',
                                x: 247,
                                y: 250,
                                width: 300,
                                height: 30,
                                fontSize: 18,
                                fontFamily: 'Times New Roman',
                                color: '#5a6c7d',
                                textAlign: 'center',
                                zIndex: 2
                            },
                            {
                                id: 3,
                                type: 'text',
                                content: '@{{name}}',
                                x: 197,
                                y: 320,
                                width: 400,
                                height: 60,
                                fontSize: 36,
                                fontFamily: 'Times New Roman',
                                color: '#2c3e50',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 3
                            },
                            {
                                id: 4,
                                type: 'text',
                                content: 'has successfully completed the course',
                                x: 197,
                                y: 420,
                                width: 400,
                                height: 30,
                                fontSize: 18,
                                fontFamily: 'Times New Roman',
                                color: '#5a6c7d',
                                textAlign: 'center',
                                zIndex: 4
                            },
                            {
                                id: 5,
                                type: 'text',
                                content: '@{{course_title}}',
                                x: 197,
                                y: 480,
                                width: 400,
                                height: 40,
                                fontSize: 24,
                                fontFamily: 'Times New Roman',
                                color: '#2c3e50',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 5
                            },
                            {
                                id: 6,
                                type: 'text',
                                content: 'Date: @{{completion_date}}',
                                x: 100,
                                y: 650,
                                width: 200,
                                height: 30,
                                fontSize: 16,
                                fontFamily: 'Times New Roman',
                                color: '#5a6c7d',
                                textAlign: 'left',
                                zIndex: 6
                            },
                            {
                                id: 7,
                                type: 'text',
                                content: 'Instructor: @{{instructor_name}}',
                                x: 494,
                                y: 650,
                                width: 200,
                                height: 30,
                                fontSize: 16,
                                fontFamily: 'Times New Roman',
                                color: '#5a6c7d',
                                textAlign: 'right',
                                zIndex: 7
                            }
                        ]
                    }],
                    modern: [{
                        id: Date.now(),
                        name: 'Page 1',
                        width: 794,
                        height: 1123,
                        backgroundColor: '#ffffff',
                        backgroundSize: 'cover',
                        elements: [
                            {
                                id: 1,
                                type: 'text',
                                content: 'CERTIFICATE',
                                x: 197,
                                y: 200,
                                width: 400,
                                height: 60,
                                fontSize: 48,
                                fontFamily: 'Helvetica',
                                color: '#3498db',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 1
                            },
                            {
                                id: 2,
                                type: 'text',
                                content: '@{{name}}',
                                x: 197,
                                y: 350,
                                width: 400,
                                height: 50,
                                fontSize: 32,
                                fontFamily: 'Helvetica',
                                color: '#2c3e50',
                                textAlign: 'center',
                                zIndex: 2
                            },
                            {
                                id: 3,
                                type: 'text',
                                content: 'Successfully completed',
                                x: 197,
                                y: 450,
                                width: 400,
                                height: 30,
                                fontSize: 20,
                                fontFamily: 'Helvetica',
                                color: '#7f8c8d',
                                textAlign: 'center',
                                zIndex: 3
                            },
                            {
                                id: 4,
                                type: 'text',
                                content: '@{{course_title}}',
                                x: 197,
                                y: 520,
                                width: 400,
                                height: 40,
                                fontSize: 28,
                                fontFamily: 'Helvetica',
                                color: '#2c3e50',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 4
                            },
                            {
                                id: 5,
                                type: 'text',
                                content: '@{{completion_date}}',
                                x: 197,
                                y: 650,
                                width: 400,
                                height: 30,
                                fontSize: 18,
                                fontFamily: 'Helvetica',
                                color: '#95a5a6',
                                textAlign: 'center',
                                zIndex: 5
                            }
                        ]
                    }],
                    elegant: [{
                        id: Date.now(),
                        name: 'Page 1',
                        width: 794,
                        height: 1123,
                        backgroundColor: '#fdfbf7',
                        backgroundSize: 'cover',
                        elements: [
                            {
                                id: 1,
                                type: 'text',
                                content: 'Certificate of Achievement',
                                x: 197,
                                y: 180,
                                width: 400,
                                height: 50,
                                fontSize: 32,
                                fontFamily: 'Georgia',
                                color: '#8b4513',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 1
                            },
                            {
                                id: 2,
                                type: 'text',
                                content: 'This certifies that',
                                x: 247,
                                y: 280,
                                width: 300,
                                height: 30,
                                fontSize: 20,
                                fontFamily: 'Georgia',
                                color: '#8b4513',
                                isItalic: true,
                                textAlign: 'center',
                                zIndex: 2
                            },
                            {
                                id: 3,
                                type: 'text',
                                content: '@{{name}}',
                                x: 197,
                                y: 350,
                                width: 400,
                                height: 60,
                                fontSize: 40,
                                fontFamily: 'Georgia',
                                color: '#2c3e50',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 3
                            },
                            {
                                id: 4,
                                type: 'text',
                                content: 'has demonstrated excellence in',
                                x: 197,
                                y: 450,
                                width: 400,
                                height: 30,
                                fontSize: 18,
                                fontFamily: 'Georgia',
                                color: '#8b4513',
                                isItalic: true,
                                textAlign: 'center',
                                zIndex: 4
                            },
                            {
                                id: 5,
                                type: 'text',
                                content: '@{{course_title}}',
                                x: 197,
                                y: 520,
                                width: 400,
                                height: 40,
                                fontSize: 26,
                                fontFamily: 'Georgia',
                                color: '#2c3e50',
                                isBold: true,
                                textAlign: 'center',
                                zIndex: 5
                            },
                            {
                                id: 6,
                                type: 'text',
                                content: 'Awarded this @{{completion_date}}',
                                x: 197,
                                y: 650,
                                width: 400,
                                height: 30,
                                fontSize: 16,
                                fontFamily: 'Georgia',
                                color: '#8b4513',
                                textAlign: 'center',
                                zIndex: 6
                            },
                            {
                                id: 7,
                                type: 'text',
                                content: '@{{instructor_name}}',
                                x: 497,
                                y: 750,
                                width: 200,
                                height: 30,
                                fontSize: 18,
                                fontFamily: 'Georgia',
                                color: '#2c3e50',
                                textAlign: 'center',
                                zIndex: 7
                            },
                            {
                                id: 8,
                                type: 'text',
                                content: 'Instructor',
                                x: 497,
                                y: 780,
                                width: 200,
                                height: 20,
                                fontSize: 14,
                                fontFamily: 'Georgia',
                                color: '#8b4513',
                                textAlign: 'center',
                                zIndex: 8
                            }
                        ]
                    }]
                };
                
                return templates[templateType] || templates.blank;
            }
        });
    </script>
    @endpush
</x-app-layout>