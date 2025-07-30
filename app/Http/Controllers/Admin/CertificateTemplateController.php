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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout_data' => 'required|json',
            'backgrounds' => 'required|array',
            'backgrounds.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $layoutData = json_decode($request->layout_data, true);
            $backgroundFiles = $request->file('backgrounds');

            // Pastikan jumlah gambar sesuai dengan jumlah halaman
            if (count($layoutData) !== count($backgroundFiles)) {
                return back()->withErrors(['backgrounds' => 'The number of background images does not match the number of pages.'])->withInput();
            }

            // Proses setiap halaman dan simpan background image
            foreach ($layoutData as $index => &$page) {
                if (isset($backgroundFiles[$index])) {
                    $path = $backgroundFiles[$index]->store('certificate_backgrounds', 'public');
                    $page['background_image_path'] = $path;
                } else {
                    return back()->withErrors(['backgrounds' => "Background image for page " . ($index + 1) . " is required."])->withInput();
                }
            }

            // Simpan template
            CertificateTemplate::create([
                'name' => $request->name,
                'layout_data' => $layoutData,
            ]);

            return redirect()->route('admin.certificate-templates.index')
                ->with('success', 'Template created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating certificate template: ' . $e->getMessage());
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

    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout_data' => 'required|json',
            'backgrounds' => 'nullable|array',
            'backgrounds.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $newLayoutData = json_decode($request->layout_data, true);
            $oldLayoutData = $certificateTemplate->layout_data;
            $backgroundFiles = $request->file('backgrounds', []);

            // Kumpulkan path gambar lama untuk kemungkinan penghapusan
            $oldImagePaths = collect($oldLayoutData)->pluck('background_image_path')->filter();

            // Proses setiap halaman
            foreach ($newLayoutData as $index => &$page) {
                // Jika ada file baru untuk halaman ini
                if (isset($backgroundFiles[$index]) && $backgroundFiles[$index]) {
                    // Upload file baru
                    $path = $backgroundFiles[$index]->store('certificate_backgrounds', 'public');
                    $page['background_image_path'] = $path;
                } 
                // Jika tidak ada file baru, pertahankan path lama jika ada
                elseif (isset($oldLayoutData[$index]['background_image_path'])) {
                    $page['background_image_path'] = $oldLayoutData[$index]['background_image_path'];
                }
                // Jika halaman baru tanpa background, berikan error
                elseif (!isset($page['background_image_path'])) {
                    return back()->withErrors(['backgrounds' => "Background image for page " . ($index + 1) . " is required."])->withInput();
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

            return redirect()->route('admin.certificate-templates.index')
                ->with('success', 'Template updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating certificate template: ' . $e->getMessage());
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
}