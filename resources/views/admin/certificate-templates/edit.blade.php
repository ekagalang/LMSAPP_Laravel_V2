<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Certificate Template') }}: {{ $certificateTemplate->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="certificateEditor()" x-init="initFromData({{ json_encode($certificateTemplate->layout_data) }})">
                <form id="template-form" @submit.prevent="submitForm" method="POST" action="{{ route('admin.certificate-templates.update', $certificateTemplate) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-medium">Template Details</h3>
                                </div>
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('admin.certificate-templates.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Cancel</a>
                                    <x-primary-button type="submit" class="ml-4">
                                        {{ __('Update Template') }}
                                    </x-primary-button>
                                </div>
                            </div>
                            <div class="mt-6">
                                <x-input-label for="name" :value="__('Template Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $certificateTemplate->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <input type="hidden" name="layout_data" :value="JSON.stringify(getSanitizedPages())">
                            
                            <div class="hidden">
                                <template x-for="(page, index) in pages" :key="index">
                                    <input :id="'background_image_' + index" @change="handleBackgroundUpload($event, index)" type="file" name="backgrounds[]" accept="image/*">
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col lg:flex-row gap-8">
                        <!-- Kolom Kiri: Toolbox & Properties -->
                        <div class="lg:w-1/4 space-y-8">
                            <!-- Toolbox -->
                            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Toolbox</h3>
                                <p class="text-sm text-gray-500 mb-4">Click an element to add it.</p>
                                <div class="space-y-3">
                                    <button type="button" @click="addElement('@{{name}}')" class="w-full text-left toolbox-element px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">Nama Peserta</button>
                                    <button type="button" @click="addElement('@{{course}}')" class="w-full text-left toolbox-element px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">Nama Course</button>
                                    <button type="button" @click="addElement('@{{date}}')" class="w-full text-left toolbox-element px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">Tanggal Selesai</button>
                                    <button type="button" @click="addElement('@{{score}}')" class="w-full text-left toolbox-element px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">Nilai Akhir</button>
                                    <button type="button" @click="addElement('@{{certificate_code}}')" class="w-full text-left toolbox-element px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">Kode Sertifikat</button>
                                    <button type="button" @click="addElement('@{{course_summary}}')" class="w-full text-left toolbox-element px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">Rangkuman Materi</button>
                                </div>
                            </div>

                            <!-- Properties Panel -->
                            <div x-show="selectedElement" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Properties</h3>
                                <div x-if="selectedElement" class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium">Font Size (px)</label>
                                        <input type="number" x-model.number="selectedElement.fontSize" min="8" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Color</label>
                                        <input type="color" x-model="selectedElement.color" class="mt-1 block w-full">
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" x-model="selectedElement.isBold" class="rounded border-gray-300">
                                        <label class="ml-2 block text-sm">Bold</label>
                                    </div>
                                    <button type="button" @click="removeElement" class="w-full text-white bg-red-600 hover:bg-red-700 rounded-md py-2 text-sm font-semibold">
                                        Remove Element
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Canvas Area -->
                        <div class="lg:w-3/4">
                            <!-- Page Navigation -->
                            <div class="flex items-center border-b border-gray-200 dark:border-gray-700 mb-4">
                                <template x-for="(page, index) in pages" :key="index">
                                    <div class="relative">
                                        <button type="button" @click="setActivePage(index)" class="px-4 py-2 text-sm font-medium" :class="{ 'border-b-2 border-blue-500 text-blue-600': activePageIndex === index, 'text-gray-500 hover:text-gray-700': activePageIndex !== index }">
                                            Page <span x-text="index + 1"></span>
                                        </button>
                                        <template x-if="pages.length > 1">
                                            <button @click.stop="removePage(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 flex items-center justify-center">
                                                ×
                                            </button>
                                        </template>
                                    </div>
                                </template>
                                <button type="button" @click="addPage" class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 border border-blue-300 rounded ml-2">+ Add Page</button>
                            </div>

                            <div x-ref="canvas" class="relative w-full aspect-[1.414/1] bg-gray-200 dark:bg-gray-900 rounded-lg shadow-inner overflow-hidden" @click="deselectElement">
                                <template x-if="pages.length > 0 && pages[activePageIndex]">
                                    <div class="w-full h-full">
                                        <template x-if="pages[activePageIndex].backgroundUrl">
                                            <img :src="pages[activePageIndex].backgroundUrl" class="absolute w-full h-full object-cover">
                                        </template>

                                        <div x-show="!pages[activePageIndex].backgroundUrl" class="absolute inset-0 flex flex-col items-center justify-center p-4">
                                            <label :for="'background_image_' + activePageIndex" class="cursor-pointer bg-white dark:bg-gray-700 p-4 rounded-lg shadow text-center hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <h4 class="font-bold text-gray-700 dark:text-gray-300">Upload Background</h4>
                                                <p class="text-sm text-gray-500">Click to select an image for Page <span x-text="activePageIndex + 1"></span></p>
                                            </label>
                                        </div>

                                        <template x-for="(element, elementIndex) in pages[activePageIndex].elements" :key="element.id">
                                            <div class="resizable-draggable absolute p-1 box-border select-none"
                                                :data-page-index="activePageIndex" :data-element-index="elementIndex"
                                                @click.stop="selectElement(elementIndex)"
                                                :class="{ 
                                                    'border-2 border-blue-500 shadow-lg cursor-move': selectedElement === element, 
                                                    'border border-transparent hover:border-gray-400 cursor-move': selectedElement !== element 
                                                }"
                                                :style="{ 
                                                    left: element.x + 'px', top: element.y + 'px', 
                                                    width: element.width + 'px', height: element.height + 'px',
                                                    fontSize: element.fontSize + 'px', color: element.color, 
                                                    fontWeight: element.isBold ? 'bold' : 'normal',
                                                    zIndex: selectedElement === element ? 20 : 10
                                                }">
                                                <span x-text="element.content" class="block w-full h-full overflow-hidden"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
    function certificateEditor() {
        return {
            pages: [],
            activePageIndex: 0,
            selectedElement: null,
            nextElementId: 1,

            initFromData(existingData) {
                if (existingData && existingData.length > 0) {
                    this.pages = existingData.map(pageData => ({
                        ...pageData,
                        backgroundUrl: pageData.background_image_path ? `{{ Storage::url('') }}${pageData.background_image_path}` : null,
                    }));
                    let maxId = 0;
                    this.pages.forEach(p => p.elements.forEach(el => { if (el.id > maxId) maxId = el.id; }));
                    this.nextElementId = maxId + 1;
                } else {
                    this.addPage();
                }
                this.activePageIndex = 0;
                this.$nextTick(() => this.reinitInteract());
                this.$watch('pages', () => { this.$nextTick(() => this.reinitInteract()); }, { deep: true });
                this.$watch('selectedElement', () => { this.$nextTick(() => this.reinitInteract()); });
            },

            addPage() {
                this.pages.push({ backgroundUrl: null, elements: [] });
                this.activePageIndex = this.pages.length - 1;
            },

            removePage(index) {
                if (this.pages.length <= 1) { alert('You must have at least one page.'); return; }
                if (!confirm('Are you sure you want to delete this page?')) return;
                this.pages.splice(index, 1);
                this.activePageIndex = Math.max(0, this.activePageIndex - 1);
                this.selectedElement = null;
            },

            setActivePage(index) {
                this.activePageIndex = index;
                this.selectedElement = null;
            },

            deselectElement(event) {
                if (event.target === this.$refs.canvas || event.target.tagName === 'IMG') {
                    this.selectedElement = null;
                }
            },
            
            reinitInteract() {
                interact('.resizable-draggable').unset();
                interact('.resizable-draggable')
                    .draggable({
                        listeners: {
                            move(event) {
                                const target = event.target;
                                const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                                const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
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
                                    element.x += (parseFloat(target.getAttribute('data-x')) || 0);
                                    element.y += (parseFloat(target.getAttribute('data-y')) || 0);
                                }
                                
                                target.style.transform = '';
                                target.removeAttribute('data-x');
                                target.removeAttribute('data-y');
                            }
                        }
                    })
                    .resizable({
                        edges: { left: true, right: true, bottom: true, top: true },
                        listeners: {
                            move: (event) => {
                                const target = event.target;
                                const pageIndex = parseInt(target.getAttribute('data-page-index'));
                                const elementIndex = parseInt(target.getAttribute('data-element-index'));
                                const element = this.pages[pageIndex].elements[elementIndex];
                                
                                if (element) {
                                    element.width = event.rect.width;
                                    element.height = event.rect.height;
                                    element.x += event.deltaRect.left;
                                    element.y += event.deltaRect.top;
                                }
                            }
                        }
                    });
            },

            addElement(content) {
                if (!this.pages[this.activePageIndex]?.backgroundUrl) {
                    alert('Please upload a background image for the active page first.');
                    return;
                }
                this.pages[this.activePageIndex].elements.push({
                    id: this.nextElementId++, content,
                    x: 50, y: 50, width: 200, height: 50,
                    fontSize: 24, color: '#000000', isBold: false
                });
            },
            
            handleBackgroundUpload(e, pageIndex) {
                const file = e.target.files[0];
                if (file) {
                    this.pages[pageIndex].backgroundUrl = URL.createObjectURL(file);
                }
            },

            selectElement(elementIndex) {
                this.selectedElement = this.pages[this.activePageIndex].elements[elementIndex];
            },

            removeElement() {
                if (!this.selectedElement) return;
                let elements = this.pages[this.activePageIndex].elements;
                this.pages[this.activePageIndex].elements = elements.filter(el => el.id !== this.selectedElement.id);
                this.selectedElement = null;
            },

            getSanitizedPages() {
                return this.pages.map(page => ({ 
                    elements: page.elements,
                    background_image_path: page.background_image_path || null
                }));
            },

            submitForm() {
                document.getElementById('template-form').submit();
            }
        }
    }
    </script>
    
    <style>
        .toolbox-element { transition: all 0.2s ease; }
        .resizable-draggable:hover { outline: 1px dashed #3b82f6; }
    </style>
    @endpush
</x-app-layout>
