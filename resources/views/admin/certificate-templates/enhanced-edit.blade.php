<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Certificate Template') }}: {{ $certificateTemplate->name }} - Enhanced Editor
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4">
            <div x-data="enhancedCertificateEditor()" x-init="initFromData({{ json_encode($certificateTemplate->layout_data) }})">
                <form id="template-form" @submit.prevent="submitForm" method="POST" action="{{ route('admin.certificate-templates.update', $certificateTemplate) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Top Toolbar -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-6 p-4">
                        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <x-input-label for="name" :value="__('Template Name')" />
                                    <x-text-input id="name" type="text" name="name" :value="old('name', $certificateTemplate->name)" required class="mt-1 w-64" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>
                                
                                <!-- Zoom Controls -->
                                <div class="flex items-center space-x-2">
                                    <button type="button" @click="zoomOut" class="p-2 bg-gray-100 hover:bg-gray-200 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span x-text="Math.round(zoom * 100) + '%'" class="text-sm font-medium w-12 text-center"></span>
                                    <button type="button" @click="zoomIn" class="p-2 bg-gray-100 hover:bg-gray-200 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click="resetZoom" class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs">Reset</button>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- Grid Toggle -->
                                <button type="button" @click="showGrid = !showGrid" class="p-2 rounded" :class="showGrid ? 'bg-blue-100 text-blue-600' : 'bg-gray-100'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </button>

                                <!-- Snap to Grid Toggle -->
                                <button type="button" @click="snapToGrid = !snapToGrid" class="p-2 rounded" :class="snapToGrid ? 'bg-blue-100 text-blue-600' : 'bg-gray-100'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </button>

                                <!-- Preview Button -->
                                <button type="button" @click="openPreview" class="p-2 bg-green-100 text-green-600 rounded hover:bg-green-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                
                                <div class="border-l border-gray-200 pl-4 flex space-x-2">
                                    <a href="{{ route('admin.certificate-templates.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md">Cancel</a>
                                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md">Update Template</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Properties Panel (moved above canvas) -->
                    <div x-show="selectedElement" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Element Properties</h3>
                        
                        <div x-show="selectedElement" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <!-- Position -->
                            <div>
                                <label class="text-xs font-medium text-gray-600">X Position</label>
                                <input type="number" x-model.number="selectedElement.x" min="0" class="w-full text-sm border border-gray-300 rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Y Position</label>
                                <input type="number" x-model.number="selectedElement.y" min="0" class="w-full text-sm border border-gray-300 rounded px-2 py-1">
                            </div>

                            <!-- Size -->
                            <div>
                                <label class="text-xs font-medium text-gray-600">Width</label>
                                <input type="number" x-model.number="selectedElement.width" min="10" class="w-full text-sm border border-gray-300 rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Height</label>
                                <input type="number" x-model.number="selectedElement.height" min="10" class="w-full text-sm border border-gray-300 rounded px-2 py-1">
                            </div>

                            <!-- Typography -->
                            <div>
                                <label class="text-xs font-medium text-gray-600">Font Size</label>
                                <div class="flex items-center space-x-2">
                                    <input type="range" x-model.number="selectedElement.fontSize" min="8" max="100" class="flex-1">
                                    <span class="text-xs text-gray-500 w-12" x-text="selectedElement.fontSize + 'px'"></span>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-600">Font Family</label>
                                <select x-model="selectedElement.fontFamily" class="w-full text-sm border border-gray-300 rounded px-2 py-1">
                                    <option value="Arial">Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-600">Text Color</label>
                                <input type="color" x-model="selectedElement.color" class="w-full h-8 border border-gray-300 rounded">
                            </div>

                            <!-- Text Style -->
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Text Style</label>
                                <div class="flex space-x-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="selectedElement.isBold" class="mr-1">
                                        <span class="text-xs">B</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="selectedElement.isItalic" class="mr-1">
                                        <span class="text-xs">I</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="selectedElement.isUnderline" class="mr-1">
                                        <span class="text-xs">U</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Text Alignment -->
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Text Alignment</label>
                                <div class="grid grid-cols-3 gap-1">
                                    <button type="button" 
                                            @click="selectedElement.textAlign = 'left'" 
                                            class="flex items-center justify-center p-1 border rounded text-xs transition-all duration-200"
                                            :class="selectedElement.textAlign === 'left' || !selectedElement.textAlign ? 'bg-blue-100 border-blue-300 text-blue-700' : 'border-gray-300 hover:border-gray-400'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            @click="selectedElement.textAlign = 'center'" 
                                            class="flex items-center justify-center p-1 border rounded text-xs transition-all duration-200"
                                            :class="selectedElement.textAlign === 'center' ? 'bg-blue-100 border-blue-300 text-blue-700' : 'border-gray-300 hover:border-gray-400'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M8 12h8M6 18h12"></path>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            @click="selectedElement.textAlign = 'right'" 
                                            class="flex items-center justify-center p-1 border rounded text-xs transition-all duration-200"
                                            :class="selectedElement.textAlign === 'right' ? 'bg-blue-100 border-blue-300 text-blue-700' : 'border-gray-300 hover:border-gray-400'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M12 12h8M4 18h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Layer Controls -->
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Layer</label>
                                <div class="grid grid-cols-2 gap-1">
                                    <button type="button" @click="bringToFront" class="p-1 text-xs bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded">Front</button>
                                    <button type="button" @click="sendToBack" class="p-1 text-xs bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded">Back</button>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Actions</label>
                                <div class="grid grid-cols-2 gap-1">
                                    <button type="button" @click="duplicateElement" class="p-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Copy</button>
                                    <button type="button" @click="removeElement" class="p-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Background Controls -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Background Settings - Page <span x-text="activePageIndex + 1"></span></h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Current Background -->
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Current Background</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    <template x-if="pages[activePageIndex]?.backgroundUrl">
                                        <div>
                                            <img :src="pages[activePageIndex].backgroundUrl" class="w-full h-24 object-cover rounded mb-2">
                                            <button type="button" @click="removeBackground()" class="text-xs text-red-600 hover:text-red-800">Remove Background</button>
                                        </div>
                                    </template>
                                    <template x-if="!pages[activePageIndex]?.backgroundUrl">
                                        <div class="text-gray-500">
                                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-xs">No background</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Upload New Background -->
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Upload New Background</label>
                                <label :for="'bg_upload_' + activePageIndex" class="cursor-pointer border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-gray-400 block text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-xs text-gray-600">Click to upload</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF (Max 5MB)</p>
                                </label>
                                <input :id="'bg_upload_' + activePageIndex" type="file" @change="changeBackground($event)" accept="image/*" class="hidden">
                            </div>

                            <!-- Background Settings -->
                            <div x-show="pages[activePageIndex]?.backgroundUrl">
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Background Size</label>
                                <select x-model="pages[activePageIndex].backgroundSize" class="w-full text-sm border border-gray-300 rounded px-2 py-1 mb-2">
                                    <option value="cover">Cover (Fill)</option>
                                    <option value="contain">Contain (Fit)</option>
                                    <option value="100% 100%">Stretch</option>
                                    <option value="auto">Original Size</option>
                                </select>
                                
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Background Position</label>
                                <select x-model="pages[activePageIndex].backgroundPosition" class="w-full text-sm border border-gray-300 rounded px-2 py-1">
                                    <option value="center">Center</option>
                                    <option value="top">Top</option>
                                    <option value="bottom">Bottom</option>
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                    <option value="top left">Top Left</option>
                                    <option value="top right">Top Right</option>
                                    <option value="bottom left">Bottom Left</option>
                                    <option value="bottom right">Bottom Right</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <!-- Left Sidebar -->
                        <div class="w-80 space-y-6">
                            <!-- Page Navigation -->
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-semibold text-gray-900">Pages</h3>
                                    <button type="button" @click="addPage" class="text-xs px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Add</button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(page, index) in pages" :key="index">
                                        <div class="flex items-center justify-between p-2 rounded border" :class="activePageIndex === index ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                            <button type="button" @click="setActivePage(index)" class="flex items-center space-x-2 flex-1">
                                                <div class="w-8 h-6 bg-gray-200 rounded border flex items-center justify-center">
                                                    <span x-text="index + 1" class="text-xs"></span>
                                                </div>
                                                <span x-text="'Page ' + (index + 1)" class="text-sm"></span>
                                            </button>
                                            <button type="button" x-show="pages.length > 1" @click="removePage(index)" class="text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Elements Toolbox -->
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                                <h3 class="font-semibold text-gray-900 mb-3">Elements</h3>
                                
                                <!-- Text Elements -->
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Dynamic Text</h4>
                                    <div class="grid grid-cols-1 gap-2">
                                        <button type="button" @click="addElement('@{{name}}')" class="text-left p-2 text-sm bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded">👤 Participant Name</button>
                                        <button type="button" @click="addElement('@{{course}}')" class="text-left p-2 text-sm bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded">📚 Course Name</button>
                                        <button type="button" @click="addElement('@{{date}}')" class="text-left p-2 text-sm bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded">📅 Completion Date</button>
                                        <button type="button" @click="addElement('@{{score}}')" class="text-left p-2 text-sm bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded">⭐ Final Score</button>
                                        <button type="button" @click="addElement('@{{certificate_code}}')" class="text-left p-2 text-sm bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded">🔖 Certificate Code</button>
                                        <button type="button" @click="addElement('@{{course_summary}}')" class="text-left p-2 text-sm bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded">📝 Course Summary</button>
                                    </div>
                                </div>

                                <!-- Static Text -->
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Static Text</h4>
                                    <div class="flex">
                                        <input type="text" x-model="customText" placeholder="Enter custom text..." class="flex-1 text-sm border border-gray-300 rounded-l px-2 py-1">
                                        <button type="button" @click="addCustomText" class="px-3 py-1 bg-green-600 text-white text-sm rounded-r">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Canvas Area -->
                        <div class="flex-1">
                            <div class="bg-white rounded-lg shadow-sm p-6" :style="{ minHeight: 'calc(100vh - 200px)' }">
                                
                                <!-- Canvas Container -->
                                <div class="relative overflow-auto border border-gray-300 rounded-lg landscape-canvas" :style="{ height: '70vh', minWidth: '100%' }">
                                    <div class="relative mx-auto bg-gray-100 canvas-wrapper" 
                                         :style="{ 
                                             transform: `scale(${zoom})`,
                                             transformOrigin: 'top center'
                                         }">
                                        <div x-ref="canvasContainer" 
                                             class="relative bg-white" 
                                             style="width: 1123px; height: 794px;"
                                         @click="deselectElement">
                                        
                                        <!-- Grid overlay -->
                                        <div x-show="showGrid" 
                                             class="absolute inset-0 pointer-events-none"
                                             :style="{
                                                 backgroundImage: `
                                                     linear-gradient(to right, rgba(0,0,0,0.1) 1px, transparent 1px),
                                                     linear-gradient(to bottom, rgba(0,0,0,0.1) 1px, transparent 1px)
                                                 `,
                                                 backgroundSize: '20px 20px'
                                             }"></div>
                                        
                                        <!-- Background Image -->
                                        <template x-if="pages[activePageIndex] && pages[activePageIndex].backgroundUrl">
                                            <img :src="pages[activePageIndex].backgroundUrl" 
                                                 class="absolute inset-0 w-full h-full pointer-events-none"
                                                 :style="{
                                                     objectFit: pages[activePageIndex].backgroundSize === 'cover' ? 'cover' : 
                                                               pages[activePageIndex].backgroundSize === 'contain' ? 'contain' : 
                                                               pages[activePageIndex].backgroundSize === '100% 100%' ? 'fill' : 'none',
                                                     objectPosition: pages[activePageIndex].backgroundPosition || 'center'
                                                 }">
                                        </template>

                                        <!-- Background Upload Area -->
                                        <div x-show="!pages[activePageIndex]?.backgroundUrl" 
                                             class="absolute inset-0 flex items-center justify-center">
                                            <label :for="'background_image_' + activePageIndex" 
                                                   class="cursor-pointer bg-white p-8 rounded-lg shadow-lg border-2 border-dashed border-gray-300 hover:border-gray-400 text-center">
                                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <h4 class="text-lg font-semibold text-gray-700 mb-2">Upload Background</h4>
                                                <p class="text-gray-500">Click to replace background for Page <span x-text="activePageIndex + 1"></span></p>
                                                <p class="text-sm text-gray-400 mt-2">Recommended: 1123×794px (A4 Landscape)</p>
                                            </label>
                                        </div>

                                        <!-- Elements -->
                                        <template x-for="(element, elementIndex) in (pages[activePageIndex]?.elements || [])" :key="element.id">
                                            <div class="resizable-draggable absolute cursor-move select-none element-container"
                                                 :data-page-index="activePageIndex" 
                                                 :data-element-index="elementIndex"
                                                 @click.stop="selectElement(elementIndex)"
                                                 :class="{ 
                                                     'element-selected': selectedElement === element,
                                                     'element-hover': selectedElement !== element,
                                                     'text-align-left': element.textAlign === 'left' || !element.textAlign,
                                                     'text-align-center': element.textAlign === 'center',
                                                     'text-align-right': element.textAlign === 'right'
                                                 }"
                                                 :style="{ 
                                                     left: element.x + 'px', 
                                                     top: element.y + 'px', 
                                                     width: element.width + 'px', 
                                                     height: element.height + 'px',
                                                     zIndex: element.zIndex || 10
                                                 }">
                                                
                                                <!-- Text Content -->
                                                <div x-text="element.content" 
                                                     class="w-full h-full overflow-hidden p-2 element-text"
                                                     :style="{ 
                                                         fontSize: element.fontSize + 'px', 
                                                         color: element.color,
                                                         fontFamily: element.fontFamily || 'Arial',
                                                         fontWeight: element.isBold ? 'bold' : 'normal',
                                                         fontStyle: element.isItalic ? 'italic' : 'normal',
                                                         textDecoration: element.isUnderline ? 'underline' : 'none',
                                                         textAlign: element.textAlign || 'left',
                                                         lineHeight: '1.4',
                                                         display: 'flex',
                                                         alignItems: 'center'
                                                     }"></div>

                                                <!-- Resize Handles -->
                                                <template x-if="selectedElement === element">
                                                    <div class="resize-handles">
                                                        <!-- Corner handles -->
                                                        <div class="resize-handle resize-handle-nw"></div>
                                                        <div class="resize-handle resize-handle-ne"></div>
                                                        <div class="resize-handle resize-handle-sw"></div>
                                                        <div class="resize-handle resize-handle-se"></div>
                                                        
                                                        <!-- Edge handles -->
                                                        <div class="resize-handle resize-handle-n"></div>
                                                        <div class="resize-handle resize-handle-s"></div>
                                                        <div class="resize-handle resize-handle-w"></div>
                                                        <div class="resize-handle resize-handle-e"></div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Bar -->
                                <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                                    <div class="flex items-center space-x-4">
                                        <span>Page <span x-text="activePageIndex + 1"></span> of <span x-text="pages.length"></span></span>
                                        <span x-show="selectedElement">Selected: <span x-text="selectedElement.content"></span></span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span x-text="Math.round(zoom * 100) + '% zoom'"></span>
                                        <span x-show="snapToGrid" class="text-green-600">• Snap to Grid</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" name="layout_data" :value="JSON.stringify(getSanitizedPages())">
                    <div class="hidden">
                        <template x-for="(page, index) in pages" :key="index">
                            <input :id="'background_image_' + index" @change="handleBackgroundUpload($event, index)" type="file" name="backgrounds[]" accept="image/*">
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
    function enhancedCertificateEditor() {
        return {
            pages: [],
            activePageIndex: 0,
            selectedElement: null,
            nextElementId: 1,
            customText: '',
            zoom: 1.0,
            showGrid: true,
            snapToGrid: true,
            gridSize: 10,

            initFromData(existingData) {
                if (existingData && existingData.length > 0) {
                    this.pages = existingData.map(pageData => ({
                        ...pageData,
                        backgroundUrl: pageData.background_image_path ? `{{ Storage::url('') }}${pageData.background_image_path}` : null,
                        backgroundSize: pageData.backgroundSize || 'cover',
                        backgroundPosition: pageData.backgroundPosition || 'center',
                        elements: (pageData.elements || []).map(el => ({
                            ...el,
                            fontFamily: el.fontFamily || 'Arial',
                            isItalic: el.isItalic || false,
                            isUnderline: el.isUnderline || false,
                            textAlign: el.textAlign || 'left',
                            zIndex: el.zIndex || 10
                        }))
                    }));
                    
                    let maxId = 0;
                    this.pages.forEach(p => p.elements.forEach(el => { if (el.id > maxId) maxId = el.id; }));
                    this.nextElementId = maxId + 1;
                } else {
                    this.addPage();
                }
                
                this.activePageIndex = 0;
                
                // Setup watchers for reactivity
                this.$watch('pages', () => { 
                    this.$nextTick(() => {
                        setTimeout(() => this.reinitInteract(), 100);
                    }); 
                }, { deep: true });
                
                this.$watch('selectedElement', () => { 
                    this.$nextTick(() => {
                        setTimeout(() => this.reinitInteract(), 50);
                    }); 
                });
                
                this.$watch('activePageIndex', () => {
                    this.$nextTick(() => {
                        setTimeout(() => this.reinitInteract(), 50);
                    });
                });
                
                // Initial setup
                this.$nextTick(() => {
                    setTimeout(() => this.reinitInteract(), 200);
                });
                
                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Delete' && this.selectedElement) {
                        this.removeElement();
                    }
                    if (e.ctrlKey && e.key === 'd' && this.selectedElement) {
                        e.preventDefault();
                        this.duplicateElement();
                    }
                    if (e.key === 'Escape') {
                        this.selectedElement = null;
                    }
                });
            },

            // Page Management
            addPage() {
                this.pages.push({ 
                    backgroundUrl: null, 
                    background_image_path: null,
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    elements: [] 
                });
                this.activePageIndex = this.pages.length - 1;
            },

            removePage(index) {
                if (this.pages.length <= 1) { 
                    alert('You must have at least one page.'); 
                    return; 
                }
                if (!confirm('Are you sure you want to delete this page?')) return;
                
                this.pages.splice(index, 1);
                this.activePageIndex = Math.max(0, Math.min(this.activePageIndex, this.pages.length - 1));
                this.selectedElement = null;
            },

            setActivePage(index) {
                this.activePageIndex = index;
                this.selectedElement = null;
            },

            // Zoom Controls
            zoomIn() {
                this.zoom = Math.min(2, this.zoom + 0.1);
            },

            zoomOut() {
                this.zoom = Math.max(0.2, this.zoom - 0.1);
            },

            resetZoom() {
                this.zoom = 1.0;
            },

            // Element Management
            addElement(content) {
                if (!this.pages[this.activePageIndex]?.backgroundUrl) {
                    alert('Please upload a background image for the active page first.');
                    return;
                }

                const newElement = {
                    id: this.nextElementId++,
                    content,
                    x: 50,
                    y: 50,
                    width: 200,
                    height: 40,
                    fontSize: 24,
                    fontFamily: 'Arial',
                    color: '#000000',
                    isBold: false,
                    isItalic: false,
                    isUnderline: false,
                    textAlign: 'left',
                    zIndex: 10
                };

                this.pages[this.activePageIndex].elements.push(newElement);
                this.selectedElement = newElement;
            },

            addCustomText() {
                if (!this.customText.trim()) return;
                this.addElement(this.customText);
                this.customText = '';
            },

            selectElement(elementIndex) {
                this.selectedElement = this.pages[this.activePageIndex].elements[elementIndex];
            },

            deselectElement(event) {
                if (event.target === this.$refs.canvasContainer || event.target.closest('.resizable-draggable') === null) {
                    this.selectedElement = null;
                }
            },

            removeElement() {
                if (!this.selectedElement) return;
                
                const elements = this.pages[this.activePageIndex].elements;
                this.pages[this.activePageIndex].elements = elements.filter(el => el.id !== this.selectedElement.id);
                this.selectedElement = null;
            },

            duplicateElement() {
                if (!this.selectedElement) return;
                
                const newElement = {
                    ...JSON.parse(JSON.stringify(this.selectedElement)),
                    id: this.nextElementId++,
                    x: this.selectedElement.x + 20,
                    y: this.selectedElement.y + 20
                };
                
                this.pages[this.activePageIndex].elements.push(newElement);
                this.selectedElement = newElement;
            },

            // Layer Management
            bringToFront() {
                if (!this.selectedElement) return;
                const maxZ = Math.max(...this.pages[this.activePageIndex].elements.map(el => el.zIndex || 10));
                this.selectedElement.zIndex = maxZ + 1;
            },

            sendToBack() {
                if (!this.selectedElement) return;
                const minZ = Math.min(...this.pages[this.activePageIndex].elements.map(el => el.zIndex || 10));
                this.selectedElement.zIndex = Math.max(1, minZ - 1);
            },

            // Preview
            openPreview() {
                const previewWindow = window.open('', '_blank', 'width=800,height=600');
                const previewHTML = this.generatePreviewHTML();
                previewWindow.document.write(previewHTML);
            },

            generatePreviewHTML() {
                let html = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Certificate Preview</title>
                        <style>
                            body { margin: 0; padding: 20px; background: #f0f0f0; font-family: Arial, sans-serif; }
                            .page { width: 794px; height: 1123px; margin: 0 auto 20px; position: relative; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                            .element { position: absolute; }
                        </style>
                    </head>
                    <body>
                `;
                
                this.pages.forEach((page, pageIndex) => {
                    html += `<div class="page">`;
                    if (page.backgroundUrl) {
                        html += `<img src="${page.backgroundUrl}" style="width: 100%; height: 100%; object-fit: cover; position: absolute;">`;
                    }
                    
                    page.elements.forEach(element => {
                        html += `
                            <div class="element" style="
                                left: ${element.x}px; 
                                top: ${element.y}px; 
                                width: ${element.width}px; 
                                height: ${element.height}px;
                                font-size: ${element.fontSize}px;
                                color: ${element.color};
                                font-family: ${element.fontFamily || 'Arial'};
                                font-weight: ${element.isBold ? 'bold' : 'normal'};
                                font-style: ${element.isItalic ? 'italic' : 'normal'};
                                text-decoration: ${element.isUnderline ? 'underline' : 'none'};
                                text-align: ${element.textAlign || 'left'};
                                line-height: 1.2;
                                z-index: ${element.zIndex || 10};
                            ">${element.content}</div>
                        `;
                    });
                    
                    html += `</div>`;
                });
                
                html += `</body></html>`;
                return html;
            },

            // Snap to Grid
            snapToGridFn(value) {
                if (!this.snapToGrid) return value;
                return Math.round(value / this.gridSize) * this.gridSize;
            },

            // File Upload
            handleBackgroundUpload(e, pageIndex) {
                const file = e.target.files[0];
                if (file) {
                    this.pages[pageIndex].backgroundUrl = URL.createObjectURL(file);
                }
            },
            
            // Background Management
            changeBackground(event) {
                const file = event.target.files[0];
                if (file) {
                    // Validate file size (5MB limit)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size must be less than 5MB');
                        return;
                    }
                    
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select a valid image file');
                        return;
                    }
                    
                    // Create object URL for preview
                    this.pages[this.activePageIndex].backgroundUrl = URL.createObjectURL(file);
                    
                    // Set default background properties if not set
                    if (!this.pages[this.activePageIndex].backgroundSize) {
                        this.pages[this.activePageIndex].backgroundSize = 'cover';
                    }
                    if (!this.pages[this.activePageIndex].backgroundPosition) {
                        this.pages[this.activePageIndex].backgroundPosition = 'center';
                    }
                    
                    // Trigger file input for form submission
                    const hiddenInput = document.getElementById(`background_image_${this.activePageIndex}`);
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    hiddenInput.files = dt.files;
                }
                
                // Reset input value to allow selecting same file again
                event.target.value = '';
            },
            
            removeBackground() {
                if (confirm('Are you sure you want to remove the background for this page?')) {
                    this.pages[this.activePageIndex].backgroundUrl = null;
                    this.pages[this.activePageIndex].background_image_path = null;
                    
                    // Clear the hidden file input
                    const hiddenInput = document.getElementById(`background_image_${this.activePageIndex}`);
                    hiddenInput.value = '';
                }
            },

            // InteractJS Integration
            reinitInteract() {
                interact('.resizable-draggable').unset();
                
                interact('.resizable-draggable')
                    .draggable({
                        allowFrom: '.element-container',
                        ignoreFrom: '.resize-handle',
                        listeners: {
                            start: (event) => {
                                // Ensure element is selected when dragging starts
                                const target = event.target;
                                const elementIndex = parseInt(target.getAttribute('data-element-index'));
                                this.selectElement(elementIndex);
                            },
                            move: (event) => {
                                const target = event.target;
                                let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                                let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                                
                                if (this.snapToGrid) {
                                    x = this.snapToGridFn(x);
                                    y = this.snapToGridFn(y);
                                }
                                
                                target.style.transform = `translate(${x}px, ${y}px)`;
                                target.setAttribute('data-x', x);
                                target.setAttribute('data-y', y);
                            },
                            end: (event) => {
                                const target = event.target;
                                const pageIndex = parseInt(target.getAttribute('data-page-index'));
                                const elementIndex = parseInt(target.getAttribute('data-element-index'));
                                const element = this.pages[pageIndex].elements[elementIndex];
                                
                                if (element) {
                                    element.x += parseFloat(target.getAttribute('data-x')) || 0;
                                    element.y += parseFloat(target.getAttribute('data-y')) || 0;
                                    
                                    // Ensure position stays within canvas bounds
                                    element.x = Math.max(0, Math.min(element.x, 1123 - element.width));
                                    element.y = Math.max(0, Math.min(element.y, 794 - element.height));
                                }
                                
                                target.style.transform = '';
                                target.removeAttribute('data-x');
                                target.removeAttribute('data-y');
                            }
                        }
                    })
                    .resizable({
                        edges: { left: true, right: true, bottom: true, top: true },
                        margin: 8,
                        listeners: {
                            start: (event) => {
                                // Ensure element is selected when resizing starts
                                const target = event.target;
                                const elementIndex = parseInt(target.getAttribute('data-element-index'));
                                this.selectElement(elementIndex);
                            },
                            move: (event) => {
                                const target = event.target;
                                const pageIndex = parseInt(target.getAttribute('data-page-index'));
                                const elementIndex = parseInt(target.getAttribute('data-element-index'));
                                const element = this.pages[pageIndex].elements[elementIndex];
                                
                                if (element) {
                                    let width = event.rect.width;
                                    let height = event.rect.height;
                                    let x = element.x + event.deltaRect.left;
                                    let y = element.y + event.deltaRect.top;
                                    
                                    // Apply minimum sizes
                                    width = Math.max(20, width);
                                    height = Math.max(20, height);
                                    
                                    // Apply snap to grid if enabled
                                    if (this.snapToGrid) {
                                        width = this.snapToGridFn(width);
                                        height = this.snapToGridFn(height);
                                        x = this.snapToGridFn(x);
                                        y = this.snapToGridFn(y);
                                    }
                                    
                                    // Keep within canvas bounds
                                    x = Math.max(0, Math.min(x, 1123 - width));
                                    y = Math.max(0, Math.min(y, 794 - height));
                                    
                                    // Update element properties
                                    element.width = width;
                                    element.height = height;
                                    element.x = x;
                                    element.y = y;
                                }
                            }
                        }
                    });
            },

            // Form Submission
            getSanitizedPages() {
                return this.pages.map(page => ({
                    elements: page.elements,
                    background_image_path: page.background_image_path,
                    backgroundSize: page.backgroundSize || 'cover',
                    backgroundPosition: page.backgroundPosition || 'center'
                }));
            },

            submitForm() {
                document.getElementById('template-form').submit();
            }
        }
    }
    </script>
    
    <style>
    /* Element Container Styling */
    .element-container {
        transition: all 0.1s ease;
        border: 1px solid transparent;
        border-radius: 2px;
    }
    
    .element-hover:hover {
        border-color: #94a3b8;
        background-color: rgba(148, 163, 184, 0.05);
    }
    
    .element-selected {
        border-color: #3b82f6 !important;
        background-color: rgba(59, 130, 246, 0.05) !important;
        box-shadow: 0 0 0 1px #3b82f6;
    }
    
    .element-text {
        pointer-events: none;
        word-break: break-word;
    }
    
    /* Make sure the element container is draggable but text is not */
    .element-container {
        pointer-events: auto;
    }
    
    .element-container .element-text {
        pointer-events: none;
        user-select: none;
    }

    /* Resize Handles */
    .resize-handles {
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        pointer-events: none;
        z-index: 1001;
    }

    .resize-handle {
        position: absolute;
        background: #3b82f6;
        border: 2px solid white;
        border-radius: 3px;
        pointer-events: auto;
        z-index: 1002;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        transition: all 0.1s ease;
    }
    
    .resize-handle:hover {
        background: #2563eb;
        transform: scale(1.1);
    }

    /* Corner handles */
    .resize-handle-nw {
        top: -6px;
        left: -6px;
        width: 12px;
        height: 12px;
        cursor: nw-resize;
    }

    .resize-handle-ne {
        top: -6px;
        right: -6px;
        width: 12px;
        height: 12px;
        cursor: ne-resize;
    }

    .resize-handle-sw {
        bottom: -6px;
        left: -6px;
        width: 12px;
        height: 12px;
        cursor: sw-resize;
    }

    .resize-handle-se {
        bottom: -6px;
        right: -6px;
        width: 12px;
        height: 12px;
        cursor: se-resize;
    }

    /* Edge handles */
    .resize-handle-n {
        top: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 12px;
        height: 12px;
        cursor: n-resize;
    }

    .resize-handle-s {
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 12px;
        height: 12px;
        cursor: s-resize;
    }

    .resize-handle-w {
        top: 50%;
        left: -6px;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        cursor: w-resize;
    }

    .resize-handle-e {
        top: 50%;
        right: -6px;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        cursor: e-resize;
    }

    /* Text alignment visual helpers */
    .text-align-left .element-text {
        justify-content: flex-start;
        text-align: left;
    }

    .text-align-center .element-text {
        justify-content: center;
        text-align: center;
    }

    .text-align-right .element-text {
        justify-content: flex-end;
        text-align: right;
    }

    /* Custom scrollbar */
    .overflow-auto::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Canvas styling for landscape */
    .landscape-canvas {
        min-width: 100%;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    
    /* Canvas wrapper styling */
    .canvas-wrapper {
        padding: 40px;
        display: inline-block;
        min-width: 100%;
    }
    </style>
    @endpush
</x-app-layout>