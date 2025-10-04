<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        $templates = CertificateTemplate::latest()->paginate(10);
        return view('admin.certificate-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.certificate-templates.create');
    }

    public function createEnhanced()
    {
        return view('admin.certificate-templates.enhanced-create');
    }

    public function createAdvanced()
    {
        return view('admin.certificate-templates.advanced-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout_data' => 'required|json',
            'backgrounds' => 'nullable|array',
            'backgrounds.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $layoutData = json_decode($request->layout_data, true);
            $backgroundFiles = $request->file('backgrounds', []);

            // Proses setiap halaman dan simpan background image
            foreach ($layoutData as $index => &$page) {
                // Handle base64 background images from advanced editor
                if (isset($page['backgroundImage']) && str_starts_with($page['backgroundImage'], 'data:image/')) {
                    // Convert base64 to file and save
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $page['backgroundImage']));
                    $fileName = 'background_' . time() . '_' . $index . '.png';
                    $path = 'certificate_backgrounds/' . $fileName;
                    Storage::disk('public')->put($path, $imageData);
                    $page['background_image_path'] = $path;
                    unset($page['backgroundImage']); // Remove base64 data
                }
                // Handle traditional file uploads
                elseif (isset($backgroundFiles[$index]) && $backgroundFiles[$index]) {
                    $path = $backgroundFiles[$index]->store('certificate_backgrounds', 'public');
                    $page['background_image_path'] = $path;
                }
                // Set default background color if no background image (for advanced editor)
                elseif (!isset($page['background_image_path']) && !isset($page['backgroundColor'])) {
                    $page['backgroundColor'] = '#ffffff';
                }
            }

            // Simpan template
            $template = CertificateTemplate::create([
                'name' => $request->name,
                'layout_data' => $layoutData,
            ]);

            // Handle AJAX requests from advanced editor
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template created successfully.',
                    'template' => $template,
                    'redirect_url' => route('admin.certificate-templates.index')
                ]);
            }

            return redirect()->route('admin.certificate-templates.index')
                ->with('success', 'Template created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating certificate template: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create template. Please try again.',
                    'error' => $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => 'Failed to create template. Please try again.'])->withInput();
        }
    }

    public function show(CertificateTemplate $certificateTemplate)
    {
        return redirect()->route('admin.certificate-templates.edit', $certificateTemplate);
    }

    public function edit(CertificateTemplate $certificateTemplate)
    {
        return view('admin.certificate-templates.edit', compact('certificateTemplate'));
    }

    public function editEnhanced(CertificateTemplate $certificateTemplate)
    {
        return view('admin.certificate-templates.enhanced-edit', compact('certificateTemplate'));
    }

    public function editAdvanced(CertificateTemplate $certificateTemplate)
    {
        return view('admin.certificate-templates.advanced-edit', compact('certificateTemplate'));
    }

    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout_data' => 'required|json',
            'backgrounds' => 'nullable|array',
            'backgrounds.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Increased to 5MB
        ]);

        try {
            $newLayoutData = json_decode($request->layout_data, true);
            $oldLayoutData = $certificateTemplate->layout_data;
            $backgroundFiles = $request->file('backgrounds', []);

            // Kumpulkan path gambar lama untuk kemungkinan penghapusan
            $oldImagePaths = collect($oldLayoutData)->pluck('background_image_path')->filter();

            // Proses setiap halaman
            foreach ($newLayoutData as $index => &$page) {
                // Handle base64 background images from advanced editor
                if (isset($page['backgroundImage']) && str_starts_with($page['backgroundImage'], 'data:image/')) {
                    // Convert base64 to file and save
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $page['backgroundImage']));
                    $fileName = 'background_' . time() . '_' . $index . '.png';
                    $path = 'certificate_backgrounds/' . $fileName;
                    Storage::disk('public')->put($path, $imageData);
                    $page['background_image_path'] = $path;
                    unset($page['backgroundImage']); // Remove base64 data
                }
                // Jika ada file baru untuk halaman ini
                elseif (isset($backgroundFiles[$index]) && $backgroundFiles[$index]) {
                    // Upload file baru
                    $path = $backgroundFiles[$index]->store('certificate_backgrounds', 'public');
                    $page['background_image_path'] = $path;
                } 
                // Jika tidak ada file baru, pertahankan path lama jika ada
                elseif (isset($oldLayoutData[$index]['background_image_path'])) {
                    $page['background_image_path'] = $oldLayoutData[$index]['background_image_path'];
                }
                // Advanced editor allows templates without background images
                elseif (!isset($page['background_image_path']) && !isset($page['backgroundColor'])) {
                    $page['backgroundColor'] = '#ffffff'; // Default white background
                }
            }

            // Hapus gambar yang tidak digunakan lagi
            $newImagePaths = collect($newLayoutData)->pluck('background_image_path')->filter();
            $imagesToDelete = $oldImagePaths->diff($newImagePaths);

            foreach ($imagesToDelete as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // Update template
            $certificateTemplate->update([
                'name' => $request->name,
                'layout_data' => $newLayoutData,
            ]);

            // Handle AJAX requests from advanced editor
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template updated successfully.',
                    'template' => $certificateTemplate
                ]);
            }

            return redirect()->route('admin.certificate-templates.index')
                ->with('success', 'Template updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating certificate template: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update template. Please try again.',
                    'error' => $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => 'Failed to update template. Please try again.'])->withInput();
        }
    }

    public function destroy(CertificateTemplate $certificateTemplate)
    {
        try {
            // Hapus semua gambar latar yang terkait
            foreach ($certificateTemplate->layout_data as $page) {
                if (isset($page['background_image_path']) && Storage::disk('public')->exists($page['background_image_path'])) {
                    Storage::disk('public')->delete($page['background_image_path']);
                }
            }
            
            $certificateTemplate->delete();

            return redirect()->route('admin.certificate-templates.index')
                ->with('success', 'Template deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting certificate template: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete template. Please try again.']);
        }
    }

    public function preview(CertificateTemplate $certificateTemplate)
    {
        return view('admin.certificate-templates.preview', compact('certificateTemplate'));
    }

    public function generatePreview(Request $request, CertificateTemplate $certificateTemplate)
    {
        $sampleData = [
            'name' => $request->get('name', 'John Doe'),
            'course_title' => $request->get('course_title', 'Sample Course Title'),
            'completion_date' => $request->get('completion_date', now()->format('F d, Y')),
            'instructor_name' => $request->get('instructor_name', 'Dr. Jane Smith'),
            'organization' => $request->get('organization', 'Learning Organization'),
            'grade' => $request->get('grade', 'A+'),
        ];

        return response()->json([
            'success' => true,
            'preview_data' => $sampleData,
            'template' => $certificateTemplate
        ]);
    }

    public function duplicate(CertificateTemplate $certificateTemplate)
    {
        try {
            // Copy layout data
            $layoutData = $certificateTemplate->layout_data;
            $newBackgroundFiles = [];
            
            // Process each page and duplicate background images
            foreach ($layoutData as $index => &$page) {
                if (isset($page['background_image_path']) && Storage::disk('public')->exists($page['background_image_path'])) {
                    // Generate new filename
                    $originalPath = $page['background_image_path'];
                    $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                    $newFileName = 'background_' . time() . '_' . $index . '_copy.' . $extension;
                    $newPath = 'certificate_backgrounds/' . $newFileName;
                    
                    // Copy the file
                    Storage::disk('public')->copy($originalPath, $newPath);
                    $page['background_image_path'] = $newPath;
                }
            }
            
            // Create new template
            $duplicatedTemplate = CertificateTemplate::create([
                'name' => $certificateTemplate->name . ' (Copy)',
                'layout_data' => $layoutData,
            ]);

            return redirect()->route('admin.certificate-templates.index')
                ->with('success', 'Template duplicated successfully as "' . $duplicatedTemplate->name . '".');
                
        } catch (\Exception $e) {
            Log::error('Error duplicating certificate template: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to duplicate template. Please try again.']);
        }
    }
}