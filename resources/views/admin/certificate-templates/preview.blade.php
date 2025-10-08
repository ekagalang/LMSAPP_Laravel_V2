<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Preview Certificate Template') }}: {{ $certificateTemplate->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.certificate-templates.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Templates
                </a>
                <a href="{{ route('admin.certificate-templates.edit-advanced', $certificateTemplate) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    <i class="fas fa-edit mr-2"></i>Edit Template
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                
                <!-- Control Panel -->
                <div class="border-b bg-gray-50 p-4">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-lg font-semibold text-gray-700">Preview Settings</h3>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center space-x-2">
                                <button id="zoom-out" class="p-2 bg-white border rounded hover:bg-gray-50">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <span id="zoom-level" class="text-sm font-medium px-3 py-1 bg-white border rounded">100%</span>
                                <button id="zoom-in" class="p-2 bg-white border rounded hover:bg-gray-50">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button id="fit-screen" class="px-3 py-2 bg-white border rounded hover:bg-gray-50 text-sm">
                                    Fit to Screen
                                </button>
                            </div>
                            
                            <button id="download-pdf" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                <i class="fas fa-download mr-2"></i>Download PDF
                            </button>
                        </div>
                    </div>
                    
                    <!-- Sample Data Form -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="sample-name" value="John Doe" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                            <input type="text" id="sample-course" value="Advanced Web Development" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="text" id="sample-date" value="{{ now()->format('F d, Y') }}" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instructor</label>
                            <input type="text" id="sample-instructor" value="Dr. Jane Smith" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                            <input type="text" id="sample-grade" value="A+" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        </div>
                        <div class="flex items-end">
                            <button id="update-preview" class="w-full px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm">
                                Update Preview
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Preview Area -->
                <div class="p-6 bg-gray-100">
                    <div id="preview-container" class="mx-auto overflow-auto">
                        <div id="preview-content" class="mx-auto" style="transform-origin: top center;">
                            @foreach($certificateTemplate->layout_data as $pageIndex => $page)
                                <div class="preview-page bg-white shadow-lg mx-auto mb-8 {{ $loop->last ? '' : 'page-break-after' }}" 
                                     style="width: {{ $page['width'] ?? 794 }}px; height: {{ $page['height'] ?? 1123 }}px; 
                                            {{ isset($page['background_image_path']) ? 'background-image: url(' . asset('storage/' . $page['background_image_path']) . '); background-size: ' . ($page['backgroundSize'] ?? 'cover') . '; background-position: center; background-repeat: no-repeat;' : 'background-color: ' . ($page['backgroundColor'] ?? '#ffffff') . ';' }}">
                                    
                                    @if(isset($page['elements']) && is_array($page['elements']))
                                        @foreach($page['elements'] as $element)
                                            @if($element['type'] === 'text')
                                                <div class="absolute" 
                                                     style="left: {{ $element['x'] }}px; 
                                                            top: {{ $element['y'] }}px; 
                                                            width: {{ $element['width'] }}px; 
                                                            height: {{ $element['height'] }}px; 
                                                            transform: rotate({{ $element['rotation'] ?? 0 }}deg); 
                                                            opacity: {{ $element['opacity'] ?? 1 }}; 
                                                            z-index: {{ $element['zIndex'] ?? 1 }};">
                                                    <div style="width: 100%; 
                                                                height: 100%; 
                                                                display: flex; 
                                                                align-items: center; 
                                                                justify-content: {{ $element['textAlign'] === 'center' ? 'center' : ($element['textAlign'] === 'right' ? 'flex-end' : 'flex-start') }}; 
                                                                font-family: {{ $element['fontFamily'] ?? 'Arial' }}; 
                                                                font-size: {{ $element['fontSize'] ?? 16 }}px; 
                                                                color: {{ $element['color'] ?? '#000000' }}; 
                                                                font-weight: {{ isset($element['isBold']) && $element['isBold'] ? 'bold' : 'normal' }}; 
                                                                font-style: {{ isset($element['isItalic']) && $element['isItalic'] ? 'italic' : 'normal' }}; 
                                                                text-decoration: {{ isset($element['isUnderline']) && $element['isUnderline'] ? 'underline' : 'none' }}; 
                                                                word-wrap: break-word; 
                                                                overflow: hidden; 
                                                                padding: 2px;">
                                                        <span class="template-variable" data-original="{{ $element['content'] }}">{{ $element['content'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>

                                @if(!$loop->last)
                                    <div class="text-center my-4 text-sm text-gray-500">Page {{ $pageIndex + 1 }}</div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Template Info -->
                <div class="border-t bg-gray-50 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <strong>Template Name:</strong> {{ $certificateTemplate->name }}
                        </div>
                        <div>
                            <strong>Pages:</strong> {{ count($certificateTemplate->layout_data) }}
                        </div>
                        <div>
                            <strong>Last Modified:</strong> {{ $certificateTemplate->updated_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .preview-page {
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        @media print {
            .page-break-after {
                page-break-after: always;
            }
        }
        
        .template-variable {
            transition: background-color 0.2s;
        }
        
        .template-variable:hover {
            background-color: rgba(59, 130, 246, 0.1);
            outline: 1px dashed #3b82f6;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentZoom = 1;
            const previewContent = document.getElementById('preview-content');
            const zoomLevel = document.getElementById('zoom-level');

            // Variable mappings for template replacement
            const variableMappings = {
                '@{{name}}': 'sample-name',
                '@{{course_title}}': 'sample-course', 
                '@{{completion_date}}': 'sample-date',
                '@{{instructor_name}}': 'sample-instructor',
                '@{{grade}}': 'sample-grade',
                '@{{organization}}': 'sample-organization'
            };

            // Zoom controls
            document.getElementById('zoom-in').addEventListener('click', function() {
                currentZoom = Math.min(3, currentZoom + 0.2);
                updateZoom();
            });

            document.getElementById('zoom-out').addEventListener('click', function() {
                currentZoom = Math.max(0.2, currentZoom - 0.2);
                updateZoom();
            });

            document.getElementById('fit-screen').addEventListener('click', function() {
                const container = document.getElementById('preview-container');
                const content = document.getElementById('preview-content');
                const containerWidth = container.clientWidth - 40; // padding
                const contentWidth = content.querySelector('.preview-page').offsetWidth;
                currentZoom = Math.min(1, containerWidth / contentWidth);
                updateZoom();
            });

            function updateZoom() {
                previewContent.style.transform = `scale(${currentZoom})`;
                zoomLevel.textContent = Math.round(currentZoom * 100) + '%';
            }

            // Update preview with sample data
            document.getElementById('update-preview').addEventListener('click', function() {
                updatePreviewVariables();
            });

            // Auto-update on input change
            Object.values(variableMappings).forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('input', debounce(updatePreviewVariables, 300));
                }
            });

            function updatePreviewVariables() {
                document.querySelectorAll('.template-variable').forEach(element => {
                    const original = element.dataset.original;
                    let newContent = original;

                    // Replace variables with actual values
                    Object.entries(variableMappings).forEach(([variable, inputId]) => {
                        const input = document.getElementById(inputId);
                        if (input && newContent.includes(variable)) {
                            newContent = newContent.replace(new RegExp(escapeRegExp(variable), 'g'), input.value);
                        }
                    });

                    element.textContent = newContent;
                });
            }

            // Download PDF functionality
            document.getElementById('download-pdf').addEventListener('click', async function() {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';

                try {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF({
                        orientation: 'landscape',
                        unit: 'px',
                        format: [794, 1123]
                    });

                    const pages = document.querySelectorAll('.preview-page');
                    
                    for (let i = 0; i < pages.length; i++) {
                        if (i > 0) pdf.addPage();
                        
                        const canvas = await html2canvas(pages[i], {
                            scale: 2,
                            useCORS: true,
                            allowTaint: true
                        });
                        
                        const imgData = canvas.toDataURL('image/jpeg', 0.9);
                        pdf.addImage(imgData, 'JPEG', 0, 0, 794, 1123);
                    }

                    pdf.save(`{{ $certificateTemplate->name }}_preview.pdf`);
                } catch (error) {
                    console.error('Error generating PDF:', error);
                    alert('Error generating PDF. Please try again.');
                } finally {
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-download mr-2"></i>Download PDF';
                }
            });

            // Helper functions
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            // Initialize
            updatePreviewVariables();
            
            // Fit to screen on load
            setTimeout(() => {
                document.getElementById('fit-screen').click();
            }, 100);
        });
    </script>
    @endpush
</x-app-layout>