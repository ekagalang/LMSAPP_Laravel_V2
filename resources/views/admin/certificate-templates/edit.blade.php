<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Certificate Template') }}: {{ $certificateTemplate->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Komponen Editor Utama dengan Alpine.js --}}
            <div x-data="certificateEditor('{{ json_encode($certificateTemplate->layout_data) }}', '{{ Storage::url($certificateTemplate->background_image_path) }}')">
                <form id="template-form" @submit.prevent="submitForm" method="POST" action="{{ route('admin.certificate-templates.update', $certificateTemplate) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            {{-- Form Header --}}
                            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-medium">Template Details</h3>
                                    <p class="text-sm text-gray-500">Update the name and background for your template.</p>
                                </div>
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('admin.certificate-templates.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Cancel</a>
                                    <x-primary-button type="submit" class="ml-4">
                                        {{ __('Update Template') }}
                                    </x-primary-button>
                                </div>
                            </div>

                            {{-- Form Fields --}}
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="name" :value="__('Template Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $certificateTemplate->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="background_image" :value="__('New Background Image (Optional)')" />
                                    <input @change="handleBackgroundUpload" id="background_image" class="block mt-1 w-full" type="file" name="background_image">
                                    <x-input-error :messages="$errors->get('background_image')" class="mt-2" />
                                </div>
                            </div>
                            <input type="hidden" name="layout_data" x-model="JSON.stringify(elements)">
                        </div>
                    </div>

                    {{-- Certificate Editor --}}
                    <div class="mt-8 flex flex-col lg:flex-row gap-8">
                        <!-- Kolom Kiri: Toolbox & Properties -->
                        <div class="lg:w-1/4 space-y-8">
                            <!-- Toolbox -->
                            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Toolbox</h3>
                                <p class="text-sm text-gray-500 mb-4">Click an element to add it.</p>
                                <div class="space-y-3">
                                    <button type="button" @click="addElement('@{{name}}')" class="w-full text-left toolbox-element">Nama Peserta</button>
                                    <button type="button" @click="addElement('@{{course}}')" class="w-full text-left toolbox-element">Nama Course</button>
                                    <button type="button" @click="addElement('@{{date}}')" class="w-full text-left toolbox-element">Tanggal Selesai</button>
                                    <button type="button" @click="addElement('@{{score}}')" class="w-full text-left toolbox-element">Nilai Akhir</button>
                                    <button type="button" @click="addElement('@{{certificate_code}}')" class="w-full text-left toolbox-element">Kode Sertifikat</button>
                                    <button type="button" @click="addElement('@{{course_summary}}')" class="w-full text-left toolbox-element">Rangkuman Materi</button>
                                </div>
                            </div>

                            <!-- Properties Panel -->
                            <div x-show="selectedElement" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Properties</h3>
                                <div x-if="selectedElement" class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium">Font Size (px)</label>
                                        <input type="number" x-model.number="selectedElement.fontSize" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
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
                            <div x-ref="canvas" class="relative w-full aspect-[1.414/1] bg-gray-200 dark:bg-gray-900 rounded-lg shadow-inner overflow-hidden">
                                <template x-if="backgroundUrl">
                                    <img :src="backgroundUrl" alt="Background Preview" class="absolute w-full h-full object-cover">
                                </template>
                                <div x-show="!backgroundUrl" class="absolute inset-0 flex items-center justify-center">
                                    <p class="text-gray-400">Upload a background image to begin</p>
                                </div>
                                
                                <template x-for="(element, index) in elements" :key="element.id">
                                    <div class="resizable-draggable absolute p-1 box-border cursor-move select-none"
                                        :data-index="index"
                                        @click.stop="selectElement(index)"
                                        :class="{ 'border-2 border-blue-500': selectedElement && selectedElement.id === element.id, 'border border-transparent': !selectedElement || selectedElement.id !== element.id }"
                                        :style="{ 
                                            left: element.x + 'px', top: element.y + 'px', 
                                            width: element.width + 'px', height: element.height + 'px',
                                            fontSize: element.fontSize + 'px', color: element.color, 
                                            fontWeight: element.isBold ? 'bold' : 'normal' 
                                        }">
                                        <span x-text="element.content"></span>
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
    function certificateEditor(existingElements, existingBackground) {
        return {
            backgroundUrl: existingBackground || null,
            elements: JSON.parse(existingElements || '[]'),
            selectedElement: null,
            nextElementId: 1,

            init() {
                // Set nextElementId based on existing elements to avoid duplicate IDs
                if (this.elements.length > 0) {
                    this.nextElementId = Math.max(...this.elements.map(el => el.id)) + 1;
                }

                this.$watch('elements', () => {
                    this.$nextTick(() => this.reinitInteract());
                });
                this.$refs.canvas.addEventListener('click', (e) => {
                    if (e.target === this.$refs.canvas || e.target.tagName === 'IMG') {
                        this.selectedElement = null;
                    }
                });
                // Initialise interact for elements loaded from DB
                this.$nextTick(() => this.reinitInteract());
            },
            
            reinitInteract() {
                interact('.resizable-draggable')
                    .draggable({
                        inertia: true,
                        modifiers: [interact.modifiers.restrictRect({ restriction: 'parent', endOnly: true })],
                        listeners: {
                            move: (event) => {
                                const index = event.target.getAttribute('data-index');
                                if (this.elements[index]) {
                                    this.elements[index].x += event.dx;
                                    this.elements[index].y += event.dy;
                                }
                            }
                        }
                    })
                    .resizable({
                        edges: { left: true, right: true, bottom: true, top: true },
                        listeners: {
                            move: (event) => {
                                const index = event.target.getAttribute('data-index');
                                if (this.elements[index]) {
                                    this.elements[index].width = event.rect.width;
                                    this.elements[index].height = event.rect.height;
                                }
                            }
                        }
                    });
            },

            addElement(content) {
                if (!this.backgroundUrl) {
                    alert('Please upload a background image first.');
                    return;
                }
                this.elements.push({
                    id: this.nextElementId++,
                    content: content,
                    x: 20, y: 20, width: 200, height: 50,
                    fontSize: 24, color: '#000000', isBold: false
                });
            },
            
            handleBackgroundUpload(e) {
                const file = e.target.files[0];
                if (file) {
                    this.backgroundUrl = URL.createObjectURL(file);
                }
            },

            selectElement(index) {
                this.selectedElement = this.elements[index];
            },

            removeElement() {
                if (!this.selectedElement) return;
                this.elements = this.elements.filter(el => el.id !== this.selectedElement.id);
                this.selectedElement = null;
            },

            submitForm() {
                document.getElementById('template-form').submit();
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
