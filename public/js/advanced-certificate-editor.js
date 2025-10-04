class AdvancedCertificateEditor {
    constructor() {
        this.pages = [this.createDefaultPage()];
        this.currentPageIndex = 0;
        this.selectedElement = null;
        this.zoom = 1;
        this.gridEnabled = false;
        this.snapEnabled = true;
        this.history = [];
        this.historyIndex = -1;
        this.dragStartPos = null;
        this.resizeHandle = null;
        this.elements = [];
        this.nextElementId = 1;
        
        this.init();
    }

    createDefaultPage() {
        return {
            id: Date.now(),
            name: 'Page 1',
            width: 794,  // A4 width in pixels at 96 DPI
            height: 1123, // A4 height in pixels at 96 DPI
            backgroundImage: null,
            backgroundColor: '#ffffff',
            backgroundSize: 'cover',
            elements: []
        };
    }

    init(existingData = null) {
        if (existingData && Array.isArray(existingData) && existingData.length > 0) {
            this.pages = existingData.map((page, index) => ({
                ...page,
                id: page.id || Date.now() + index,
                name: page.name || `Page ${index + 1}`,
                elements: page.elements || []
            }));
        }

        this.setupEventListeners();
        this.renderCanvas();
        this.updatePagesList();
        this.updateLayersList();
        this.saveState();
    }

    setupEventListeners() {
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-btn').dataset.tab;
                this.switchTab(btn.closest('.flex').parentElement, tab);
            });
        });

        // Tool buttons
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tool = btn.dataset.tool;
                const content = btn.dataset.content;
                this.addElement(tool, content);
            });
        });

        // Canvas interactions
        const canvas = document.getElementById('certificate-canvas');
        canvas.addEventListener('click', (e) => this.handleCanvasClick(e));
        canvas.addEventListener('mousedown', (e) => this.handleMouseDown(e));
        canvas.addEventListener('mousemove', (e) => this.handleMouseMove(e));
        canvas.addEventListener('mouseup', (e) => this.handleMouseUp(e));

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyDown(e));

        // Background upload
        document.getElementById('background-upload').addEventListener('change', (e) => {
            this.handleBackgroundUpload(e);
        });

        // Background color
        document.getElementById('background-color').addEventListener('change', (e) => {
            this.updateBackgroundColor(e.target.value);
        });

        // Background size
        document.getElementById('background-size').addEventListener('change', (e) => {
            this.updateBackgroundSize(e.target.value);
        });

        // Zoom controls
        document.getElementById('zoom-in').addEventListener('click', () => this.zoomIn());
        document.getElementById('zoom-out').addEventListener('click', () => this.zoomOut());
        document.getElementById('zoom-fit').addEventListener('click', () => this.fitToScreen());

        // Grid and snap
        document.getElementById('grid-toggle').addEventListener('click', () => this.toggleGrid());
        document.getElementById('snap-toggle').addEventListener('click', () => this.toggleSnap());

        // Undo/Redo
        document.getElementById('undo-btn').addEventListener('click', () => this.undo());
        document.getElementById('redo-btn').addEventListener('click', () => this.redo());

        // Page management
        document.getElementById('add-page-btn').addEventListener('click', () => this.addPage());
        document.getElementById('page-selector').addEventListener('change', (e) => {
            this.switchPage(parseInt(e.target.value));
        });

        // Preview and save
        document.getElementById('preview-btn').addEventListener('click', () => this.showPreview());
        document.getElementById('close-preview').addEventListener('click', () => this.hidePreview());
        document.getElementById('save-btn').addEventListener('click', () => this.saveTemplate());
    }

    switchTab(container, tabName) {
        // Update tab buttons
        container.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        container.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // Update tab content
        container.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        container.querySelector(`#${tabName}-tab`).classList.remove('hidden');
    }

    addElement(type, content = '') {
        const page = this.pages[this.currentPageIndex];
        const element = {
            id: this.nextElementId++,
            type: type,
            content: content || 'New Element',
            x: 100,
            y: 100,
            width: type === 'text' ? 200 : 100,
            height: type === 'text' ? 40 : 100,
            fontSize: 16,
            fontFamily: 'Arial',
            color: '#000000',
            backgroundColor: 'transparent',
            isBold: false,
            isItalic: false,
            isUnderline: false,
            textAlign: 'left',
            rotation: 0,
            opacity: 1,
            zIndex: page.elements.length
        };

        page.elements.push(element);
        this.renderCanvas();
        this.updateLayersList();
        this.selectElement(element);
        this.saveState();
    }

    selectElement(element) {
        this.selectedElement = element;
        
        // Update visual selection
        document.querySelectorAll('.canvas-element').forEach(el => {
            el.classList.remove('selected');
        });
        
        const elementDiv = document.querySelector(`[data-element-id="${element.id}"]`);
        if (elementDiv) {
            elementDiv.classList.add('selected');
            this.addResizeHandles(elementDiv);
        }

        this.updatePropertiesPanel(element);
        this.updateLayersSelection(element);
    }

    addResizeHandles(elementDiv) {
        // Remove existing handles
        elementDiv.querySelectorAll('.resize-handle').forEach(handle => handle.remove());

        // Add resize handles for all element types
        const handles = ['nw', 'ne', 'sw', 'se'];
        handles.forEach(pos => {
            const handle = document.createElement('div');
            handle.className = `resize-handle ${pos}`;
            handle.dataset.handle = pos;
            elementDiv.appendChild(handle);
        });
    }

    updatePropertiesPanel(element) {
        const propertiesDiv = document.getElementById('element-properties');
        
        const properties = `
            <div class="space-y-4">
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-medium text-gray-700 mb-2">${element.type.charAt(0).toUpperCase() + element.type.slice(1)} Properties</h4>
                </div>
                
                ${element.type === 'text' ? this.getTextProperties(element) : ''}
                ${this.getCommonProperties(element)}
                
                <div class="pt-3 border-t">
                    <button onclick="editor.deleteElement(${element.id})" class="w-full px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                        <i class="fas fa-trash mr-2"></i>Delete Element
                    </button>
                </div>
            </div>
        `;
        
        propertiesDiv.innerHTML = properties;
        this.bindPropertyControls();
    }

    getTextProperties(element) {
        return `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Text Content</label>
                <textarea id="element-content" class="w-full px-3 py-2 border rounded" rows="2">${element.content}</textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Font Size</label>
                    <div class="flex items-center space-x-2">
                        <input type="range" id="element-font-size" min="8" max="100" value="${element.fontSize}" class="flex-1">
                        <span class="text-sm text-gray-500 w-12">${element.fontSize}px</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Font Family</label>
                    <select id="element-font-family" class="w-full px-2 py-1 border rounded text-sm">
                        <option value="Arial" ${element.fontFamily === 'Arial' ? 'selected' : ''}>Arial</option>
                        <option value="Times New Roman" ${element.fontFamily === 'Times New Roman' ? 'selected' : ''}>Times New Roman</option>
                        <option value="Helvetica" ${element.fontFamily === 'Helvetica' ? 'selected' : ''}>Helvetica</option>
                        <option value="Georgia" ${element.fontFamily === 'Georgia' ? 'selected' : ''}>Georgia</option>
                        <option value="Verdana" ${element.fontFamily === 'Verdana' ? 'selected' : ''}>Verdana</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
                    <input type="color" id="element-color" value="${element.color}" class="w-full h-8 border rounded">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Style</label>
                    <div class="flex space-x-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="element-bold" ${element.isBold ? 'checked' : ''} class="mr-1">
                            <span class="text-sm font-bold">B</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="element-italic" ${element.isItalic ? 'checked' : ''} class="mr-1">
                            <span class="text-sm italic">I</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="element-underline" ${element.isUnderline ? 'checked' : ''} class="mr-1">
                            <span class="text-sm underline">U</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Text Alignment</label>
                <div class="grid grid-cols-3 gap-1">
                    <button type="button" onclick="editor.updateElementProperty('textAlign', 'left')" 
                            class="px-2 py-1 border rounded text-sm ${element.textAlign === 'left' ? 'bg-blue-100 border-blue-300' : 'hover:bg-gray-50'}">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <button type="button" onclick="editor.updateElementProperty('textAlign', 'center')" 
                            class="px-2 py-1 border rounded text-sm ${element.textAlign === 'center' ? 'bg-blue-100 border-blue-300' : 'hover:bg-gray-50'}">
                        <i class="fas fa-align-center"></i>
                    </button>
                    <button type="button" onclick="editor.updateElementProperty('textAlign', 'right')" 
                            class="px-2 py-1 border rounded text-sm ${element.textAlign === 'right' ? 'bg-blue-100 border-blue-300' : 'hover:bg-gray-50'}">
                        <i class="fas fa-align-right"></i>
                    </button>
                </div>
            </div>
        `;
    }

    getCommonProperties(element) {
        return `
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">X Position</label>
                    <input type="number" id="element-x" value="${element.x}" class="w-full px-2 py-1 border rounded text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Y Position</label>
                    <input type="number" id="element-y" value="${element.y}" class="w-full px-2 py-1 border rounded text-sm">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Width</label>
                    <input type="number" id="element-width" value="${element.width}" class="w-full px-2 py-1 border rounded text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Height</label>
                    <input type="number" id="element-height" value="${element.height}" class="w-full px-2 py-1 border rounded text-sm">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rotation</label>
                    <div class="flex items-center space-x-2">
                        <input type="range" id="element-rotation" min="-180" max="180" value="${element.rotation}" class="flex-1">
                        <span class="text-sm text-gray-500 w-10">${element.rotation}°</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opacity</label>
                    <div class="flex items-center space-x-2">
                        <input type="range" id="element-opacity" min="0" max="1" step="0.1" value="${element.opacity}" class="flex-1">
                        <span class="text-sm text-gray-500 w-10">${Math.round(element.opacity * 100)}%</span>
                    </div>
                </div>
            </div>
        `;
    }

    bindPropertyControls() {
        const controls = [
            'element-content', 'element-font-size', 'element-font-family', 'element-color',
            'element-bold', 'element-italic', 'element-underline',
            'element-x', 'element-y', 'element-width', 'element-height',
            'element-rotation', 'element-opacity'
        ];

        controls.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', (e) => {
                    this.handlePropertyChange(id, e.target);
                });
            }
        });
    }

    handlePropertyChange(controlId, target) {
        if (!this.selectedElement) return;

        const property = controlId.replace('element-', '').replace('-', '_');
        let value = target.value;

        // Convert specific values
        if (['x', 'y', 'width', 'height', 'fontSize', 'rotation'].includes(property)) {
            value = parseInt(value) || 0;
        } else if (property === 'opacity') {
            value = parseFloat(value) || 0;
        } else if (['bold', 'italic', 'underline'].includes(property)) {
            value = target.checked;
            property = 'is' + property.charAt(0).toUpperCase() + property.slice(1);
        }

        this.updateElementProperty(property, value);
    }

    updateElementProperty(property, value) {
        if (!this.selectedElement) return;

        this.selectedElement[property] = value;
        this.renderCanvas();
        
        // Update property display if applicable
        if (property === 'fontSize') {
            const span = document.querySelector('#element-properties .text-sm.text-gray-500.w-12');
            if (span) span.textContent = value + 'px';
        } else if (property === 'rotation') {
            const span = document.querySelector('#element-properties .text-sm.text-gray-500.w-10');
            if (span) span.textContent = value + '°';
        } else if (property === 'opacity') {
            const span = document.querySelector('#element-properties .text-sm.text-gray-500.w-10');
            if (span) span.textContent = Math.round(value * 100) + '%';
        }

        this.saveState();
    }

    renderCanvas() {
        const canvas = document.getElementById('certificate-canvas');
        const page = this.pages[this.currentPageIndex];
        
        // Update canvas size and background
        canvas.style.width = page.width + 'px';
        canvas.style.height = page.height + 'px';
        canvas.style.backgroundColor = page.backgroundColor;
        
        if (page.backgroundImage) {
            canvas.style.backgroundImage = `url(${page.backgroundImage})`;
            canvas.style.backgroundSize = page.backgroundSize || 'cover';
            canvas.style.backgroundPosition = 'center';
            canvas.style.backgroundRepeat = 'no-repeat';
        } else {
            canvas.style.backgroundImage = 'none';
        }

        // Clear existing elements
        canvas.querySelectorAll('.canvas-element').forEach(el => el.remove());

        // Render elements
        page.elements.forEach(element => {
            this.renderElement(element);
        });

        // Update zoom
        canvas.style.transform = `scale(${this.zoom})`;
        document.getElementById('zoom-level').textContent = Math.round(this.zoom * 100) + '%';
    }

    renderElement(element) {
        const canvas = document.getElementById('certificate-canvas');
        const elementDiv = document.createElement('div');
        
        elementDiv.className = 'canvas-element';
        elementDiv.dataset.elementId = element.id;
        elementDiv.style.cssText = `
            left: ${element.x}px;
            top: ${element.y}px;
            width: ${element.width}px;
            height: ${element.height}px;
            transform: rotate(${element.rotation}deg);
            opacity: ${element.opacity};
            z-index: ${element.zIndex};
        `;

        if (element.type === 'text') {
            elementDiv.innerHTML = `
                <div style="
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: ${element.textAlign === 'center' ? 'center' : element.textAlign === 'right' ? 'flex-end' : 'flex-start'};
                    font-family: ${element.fontFamily};
                    font-size: ${element.fontSize}px;
                    color: ${element.color};
                    font-weight: ${element.isBold ? 'bold' : 'normal'};
                    font-style: ${element.isItalic ? 'italic' : 'normal'};
                    text-decoration: ${element.isUnderline ? 'underline' : 'none'};
                    word-wrap: break-word;
                    overflow: hidden;
                    padding: 2px;
                ">
                    ${element.content}
                </div>
            `;
        } else if (element.type === 'image') {
            elementDiv.innerHTML = `
                <div style="
                    width: 100%;
                    height: 100%;
                    background-color: #f3f4f6;
                    border: 2px dashed #d1d5db;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #6b7280;
                ">
                    <i class="fas fa-image text-2xl"></i>
                </div>
            `;
        }

        canvas.appendChild(elementDiv);
    }

    handleCanvasClick(e) {
        e.stopPropagation();
        
        const element = e.target.closest('.canvas-element');
        if (element) {
            const elementId = parseInt(element.dataset.elementId);
            const selectedElement = this.pages[this.currentPageIndex].elements.find(el => el.id === elementId);
            if (selectedElement) {
                this.selectElement(selectedElement);
            }
        } else {
            this.deselectElement();
        }
    }

    handleMouseDown(e) {
        const element = e.target.closest('.canvas-element');
        const resizeHandle = e.target.closest('.resize-handle');
        
        if (resizeHandle && this.selectedElement) {
            this.resizeHandle = resizeHandle.dataset.handle;
            this.dragStartPos = { x: e.clientX, y: e.clientY };
            e.preventDefault();
        } else if (element && this.selectedElement) {
            const rect = document.getElementById('certificate-canvas').getBoundingClientRect();
            const mouseX = (e.clientX - rect.left) / this.zoom;
            const mouseY = (e.clientY - rect.top) / this.zoom;
            
            this.dragStartPos = { 
                x: mouseX - this.selectedElement.x, 
                y: mouseY - this.selectedElement.y 
            };
            e.preventDefault();
        }
    }

    handleMouseMove(e) {
        if (!this.dragStartPos || !this.selectedElement) return;

        const rect = document.getElementById('certificate-canvas').getBoundingClientRect();
        const x = (e.clientX - rect.left) / this.zoom;
        const y = (e.clientY - rect.top) / this.zoom;

        if (this.resizeHandle) {
            // Handle resizing
            const deltaX = (e.clientX - this.dragStartPos.x) / this.zoom;
            const deltaY = (e.clientY - this.dragStartPos.y) / this.zoom;
            
            switch (this.resizeHandle) {
                case 'se':
                    this.selectedElement.width = Math.max(10, this.selectedElement.width + deltaX);
                    this.selectedElement.height = Math.max(10, this.selectedElement.height + deltaY);
                    break;
                case 'sw':
                    this.selectedElement.width = Math.max(10, this.selectedElement.width - deltaX);
                    this.selectedElement.height = Math.max(10, this.selectedElement.height + deltaY);
                    this.selectedElement.x = Math.max(0, this.selectedElement.x + deltaX);
                    break;
                case 'ne':
                    this.selectedElement.width = Math.max(10, this.selectedElement.width + deltaX);
                    this.selectedElement.height = Math.max(10, this.selectedElement.height - deltaY);
                    this.selectedElement.y = Math.max(0, this.selectedElement.y + deltaY);
                    break;
                case 'nw':
                    this.selectedElement.width = Math.max(10, this.selectedElement.width - deltaX);
                    this.selectedElement.height = Math.max(10, this.selectedElement.height - deltaY);
                    this.selectedElement.x = Math.max(0, this.selectedElement.x + deltaX);
                    this.selectedElement.y = Math.max(0, this.selectedElement.y + deltaY);
                    break;
            }
            
            this.dragStartPos = { x: e.clientX, y: e.clientY };
        } else {
            // Handle dragging
            const rect = document.getElementById('certificate-canvas').getBoundingClientRect();
            const mouseX = (e.clientX - rect.left) / this.zoom;
            const mouseY = (e.clientY - rect.top) / this.zoom;
            
            this.selectedElement.x = Math.max(0, mouseX - this.dragStartPos.x);
            this.selectedElement.y = Math.max(0, mouseY - this.dragStartPos.y);
            
            if (this.snapEnabled) {
                this.selectedElement.x = Math.round(this.selectedElement.x / 10) * 10;
                this.selectedElement.y = Math.round(this.selectedElement.y / 10) * 10;
            }
        }

        this.renderCanvas();
        this.selectElement(this.selectedElement);
    }

    handleMouseUp(e) {
        if (this.dragStartPos) {
            this.saveState();
        }
        this.dragStartPos = null;
        this.resizeHandle = null;
    }

    deselectElement() {
        this.selectedElement = null;
        document.querySelectorAll('.canvas-element').forEach(el => {
            el.classList.remove('selected');
            el.querySelectorAll('.resize-handle').forEach(handle => handle.remove());
        });
        
        const propertiesDiv = document.getElementById('element-properties');
        propertiesDiv.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-mouse-pointer text-3xl mb-2"></i>
                <p>Select an element to edit properties</p>
            </div>
        `;
    }

    deleteElement(elementId) {
        const page = this.pages[this.currentPageIndex];
        const elementIndex = page.elements.findIndex(el => el.id === elementId);
        
        if (elementIndex !== -1) {
            page.elements.splice(elementIndex, 1);
            this.renderCanvas();
            this.updateLayersList();
            this.deselectElement();
            this.saveState();
        }
    }

    // Zoom controls
    zoomIn() {
        const currentZoom = this.zoom;
        if (currentZoom < 0.5) {
            this.zoom = Math.min(3, currentZoom + 0.05);
        } else if (currentZoom < 1) {
            this.zoom = Math.min(3, currentZoom + 0.1);
        } else {
            this.zoom = Math.min(3, currentZoom + 0.25);
        }
        this.renderCanvas();
    }

    zoomOut() {
        const currentZoom = this.zoom;
        if (currentZoom <= 0.5) {
            this.zoom = Math.max(0.1, currentZoom - 0.05);
        } else if (currentZoom <= 1) {
            this.zoom = Math.max(0.1, currentZoom - 0.1);
        } else {
            this.zoom = Math.max(0.1, currentZoom - 0.25);
        }
        this.renderCanvas();
    }

    fitToScreen() {
        const container = document.getElementById('canvas-container');
        const canvas = document.getElementById('certificate-canvas');
        
        if (!container || !canvas) return;
        
        const containerRect = container.getBoundingClientRect();
        const canvasWidth = 794; // A4 width
        const canvasHeight = 1123; // A4 height
        
        const padding = 40; // Leave some padding
        const scaleX = (containerRect.width - padding) / canvasWidth;
        const scaleY = (containerRect.height - padding) / canvasHeight;
        
        this.zoom = Math.min(scaleX, scaleY, 1);
        this.renderCanvas();
    }

    // Grid and snap controls
    toggleGrid() {
        this.gridEnabled = !this.gridEnabled;
        const gridOverlay = document.getElementById('grid-overlay');
        gridOverlay.classList.toggle('hidden', !this.gridEnabled);
        
        const btn = document.getElementById('grid-toggle');
        btn.classList.toggle('bg-blue-100', this.gridEnabled);
    }

    toggleSnap() {
        this.snapEnabled = !this.snapEnabled;
        const btn = document.getElementById('snap-toggle');
        btn.classList.toggle('bg-blue-100', this.snapEnabled);
    }

    // Background controls
    handleBackgroundUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            this.pages[this.currentPageIndex].backgroundImage = e.target.result;
            this.renderCanvas();
            this.saveState();
        };
        reader.readAsDataURL(file);
    }

    updateBackgroundColor(color) {
        this.pages[this.currentPageIndex].backgroundColor = color;
        this.renderCanvas();
        this.saveState();
    }

    updateBackgroundSize(size) {
        this.pages[this.currentPageIndex].backgroundSize = size;
        this.renderCanvas();
        this.saveState();
    }

    // Page management
    addPage() {
        const newPage = this.createDefaultPage();
        newPage.name = `Page ${this.pages.length + 1}`;
        this.pages.push(newPage);
        this.updatePagesList();
        this.switchPage(this.pages.length - 1);
        this.saveState();
    }

    deletePage(pageIndex) {
        if (this.pages.length <= 1) {
            alert('Cannot delete the last remaining page.');
            return;
        }
        
        if (confirm('Are you sure you want to delete this page?')) {
            this.pages.splice(pageIndex, 1);
            
            // Update page names
            this.pages.forEach((page, index) => {
                page.name = `Page ${index + 1}`;
            });
            
            // Adjust current page index if necessary
            if (this.currentPageIndex >= this.pages.length) {
                this.currentPageIndex = this.pages.length - 1;
            } else if (this.currentPageIndex >= pageIndex) {
                this.currentPageIndex = Math.max(0, this.currentPageIndex - 1);
            }
            
            this.updatePagesList();
            this.renderCanvas();
            this.updateLayersList();
            this.saveState();
        }
    }

    duplicatePage(pageIndex) {
        const originalPage = this.pages[pageIndex];
        const duplicatedPage = JSON.parse(JSON.stringify(originalPage));
        
        // Generate new IDs for elements
        duplicatedPage.elements.forEach(element => {
            element.id = this.nextElementId++;
        });
        
        duplicatedPage.id = Date.now();
        duplicatedPage.name = `${originalPage.name} Copy`;
        
        this.pages.splice(pageIndex + 1, 0, duplicatedPage);
        
        // Update page names
        this.pages.forEach((page, index) => {
            if (!page.name.includes('Copy')) {
                page.name = `Page ${index + 1}`;
            }
        });
        
        this.updatePagesList();
        this.switchPage(pageIndex + 1);
        this.saveState();
    }

    switchPage(pageIndex) {
        if (pageIndex >= 0 && pageIndex < this.pages.length) {
            this.currentPageIndex = pageIndex;
            this.deselectElement();
            this.renderCanvas();
            this.updateLayersList();
            
            const selector = document.getElementById('page-selector');
            selector.value = pageIndex;
        }
    }

    updatePagesList() {
        const pagesList = document.getElementById('pages-list');
        const pageSelector = document.getElementById('page-selector');
        
        // Update pages list
        pagesList.innerHTML = this.pages.map((page, index) => `
            <div class="layer-item ${index === this.currentPageIndex ? 'active' : ''}" onclick="editor.switchPage(${index})">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-file-alt text-gray-400"></i>
                    <span class="text-sm">${page.name}</span>
                </div>
                <div class="flex items-center space-x-1">
                    <button onclick="editor.duplicatePage(${index})" class="p-1 hover:bg-gray-200 rounded" title="Duplicate">
                        <i class="fas fa-copy text-xs"></i>
                    </button>
                    <button onclick="editor.deletePage(${index})" class="p-1 hover:bg-gray-200 rounded" title="Delete" ${this.pages.length === 1 ? 'disabled' : ''}>
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        `).join('');

        // Update page selector
        pageSelector.innerHTML = this.pages.map((page, index) => 
            `<option value="${index}">${page.name}</option>`
        ).join('');
    }

    updateLayersList() {
        const layersList = document.getElementById('layers-list');
        const page = this.pages[this.currentPageIndex];
        
        if (page.elements.length === 0) {
            layersList.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-layer-group text-2xl mb-2"></i>
                    <p class="text-sm">No elements yet</p>
                </div>
            `;
            return;
        }

        layersList.innerHTML = page.elements
            .sort((a, b) => b.zIndex - a.zIndex)
            .map(element => `
                <div class="layer-item ${this.selectedElement && this.selectedElement.id === element.id ? 'active' : ''}" 
                     onclick="editor.selectElementById(${element.id})">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-${element.type === 'text' ? 'font' : element.type} text-gray-400"></i>
                        <span class="text-sm truncate">${element.content.substring(0, 20)}${element.content.length > 20 ? '...' : ''}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button onclick="editor.moveElementLayer(${element.id}, 'up')" class="p-1 hover:bg-gray-200 rounded" title="Move Up">
                            <i class="fas fa-arrow-up text-xs"></i>
                        </button>
                        <button onclick="editor.moveElementLayer(${element.id}, 'down')" class="p-1 hover:bg-gray-200 rounded" title="Move Down">
                            <i class="fas fa-arrow-down text-xs"></i>
                        </button>
                    </div>
                </div>
            `).join('');
    }

    selectElementById(elementId) {
        const element = this.pages[this.currentPageIndex].elements.find(el => el.id === elementId);
        if (element) {
            this.selectElement(element);
        }
    }

    moveElementLayer(elementId, direction) {
        const page = this.pages[this.currentPageIndex];
        const element = page.elements.find(el => el.id === elementId);
        
        if (!element) return;
        
        if (direction === 'up') {
            element.zIndex = Math.min(page.elements.length - 1, element.zIndex + 1);
        } else if (direction === 'down') {
            element.zIndex = Math.max(0, element.zIndex - 1);
        }
        
        // Normalize z-indices
        page.elements.sort((a, b) => a.zIndex - b.zIndex);
        page.elements.forEach((el, index) => {
            el.zIndex = index;
        });
        
        this.renderCanvas();
        this.updateLayersList();
        this.saveState();
    }

    updateLayersSelection(element) {
        document.querySelectorAll('.layer-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`#layers-list .layer-item[onclick*="${element.id}"]`)?.classList.add('active');
    }

    // History management
    saveState() {
        const state = JSON.parse(JSON.stringify(this.pages));
        
        // Remove future history if we're not at the end
        if (this.historyIndex < this.history.length - 1) {
            this.history = this.history.slice(0, this.historyIndex + 1);
        }
        
        this.history.push(state);
        this.historyIndex++;
        
        // Limit history size
        if (this.history.length > 50) {
            this.history.shift();
            this.historyIndex--;
        }
        
        this.updateHistoryButtons();
    }

    undo() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.pages = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
            this.renderCanvas();
            this.updateLayersList();
            this.updatePagesList();
            this.deselectElement();
            this.updateHistoryButtons();
        }
    }

    redo() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.pages = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
            this.renderCanvas();
            this.updateLayersList();
            this.updatePagesList();
            this.deselectElement();
            this.updateHistoryButtons();
        }
    }

    updateHistoryButtons() {
        document.getElementById('undo-btn').disabled = this.historyIndex <= 0;
        document.getElementById('redo-btn').disabled = this.historyIndex >= this.history.length - 1;
    }

    // Keyboard shortcuts
    handleKeyDown(e) {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'z':
                    e.preventDefault();
                    if (e.shiftKey) {
                        this.redo();
                    } else {
                        this.undo();
                    }
                    break;
                case 's':
                    e.preventDefault();
                    this.saveTemplate();
                    break;
            }
        } else if (e.key === 'Delete' && this.selectedElement) {
            this.deleteElement(this.selectedElement.id);
        }
    }

    // Preview
    showPreview() {
        const modal = document.getElementById('preview-modal');
        const content = document.getElementById('preview-content');
        
        content.innerHTML = this.pages.map((page, index) => `
            <div class="mb-8 ${index === this.pages.length - 1 ? '' : 'page-break-after'}">
                <div class="relative bg-white shadow-lg mx-auto" style="width: 794px; height: 1123px; ${page.backgroundImage ? `background-image: url(${page.backgroundImage}); background-size: ${page.backgroundSize}; background-position: center; background-repeat: no-repeat;` : `background-color: ${page.backgroundColor};`}">
                    ${page.elements.map(element => this.renderPreviewElement(element)).join('')}
                </div>
                <div class="text-center mt-2 text-sm text-gray-500">Page ${index + 1}</div>
            </div>
        `).join('');
        
        modal.classList.remove('hidden');
    }

    renderPreviewElement(element) {
        if (element.type === 'text') {
            return `
                <div style="
                    position: absolute;
                    left: ${element.x}px;
                    top: ${element.y}px;
                    width: ${element.width}px;
                    height: ${element.height}px;
                    transform: rotate(${element.rotation}deg);
                    opacity: ${element.opacity};
                    z-index: ${element.zIndex};
                    display: flex;
                    align-items: center;
                    justify-content: ${element.textAlign === 'center' ? 'center' : element.textAlign === 'right' ? 'flex-end' : 'flex-start'};
                    font-family: ${element.fontFamily};
                    font-size: ${element.fontSize}px;
                    color: ${element.color};
                    font-weight: ${element.isBold ? 'bold' : 'normal'};
                    font-style: ${element.isItalic ? 'italic' : 'normal'};
                    text-decoration: ${element.isUnderline ? 'underline' : 'none'};
                    word-wrap: break-word;
                    overflow: hidden;
                    padding: 2px;
                ">
                    ${element.content}
                </div>
            `;
        }
        return '';
    }

    hidePreview() {
        document.getElementById('preview-modal').classList.add('hidden');
    }

    // Load template
    loadTemplate(templateData) {
        if (templateData && Array.isArray(templateData) && templateData.length > 0) {
            this.pages = templateData.map((page, index) => ({
                ...page,
                id: page.id || Date.now() + index,
                name: page.name || `Page ${index + 1}`,
                elements: page.elements || []
            }));
            
            this.currentPageIndex = 0;
            this.selectedElement = null;
            this.renderCanvas();
            this.updatePagesList();
            this.updateLayersList();
            this.saveState();
        }
    }

    // Save template
    async saveTemplate() {
        const templateName = document.getElementById('template-name').value;
        if (!templateName.trim()) {
            alert('Please enter a template name');
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'PUT');
        formData.append('name', templateName);
        formData.append('layout_data', JSON.stringify(this.pages));

        // Add background images if any
        const backgroundPromises = this.pages.map(async (page, index) => {
            if (page.backgroundImage && page.backgroundImage.startsWith('data:')) {
                const response = await fetch(page.backgroundImage);
                const blob = await response.blob();
                const file = new File([blob], `background_${index}.png`, { type: 'image/png' });
                formData.append('backgrounds[]', file);
            }
        });

        await Promise.all(backgroundPromises);

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                alert('Template saved successfully!');
                // Optionally redirect
                // window.location.href = '/admin/certificate-templates';
            } else {
                const errorData = await response.json();
                alert('Error saving template: ' + (errorData.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Save error:', error);
            alert('Error saving template. Please try again.');
        }
    }
}

// Initialize the editor
window.AdvancedCertificateEditor = AdvancedCertificateEditor;
let editor;

document.addEventListener('DOMContentLoaded', function() {
    editor = new AdvancedCertificateEditor();
});