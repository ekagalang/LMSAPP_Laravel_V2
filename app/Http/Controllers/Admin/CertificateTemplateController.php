<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = CertificateTemplate::latest()->paginate(10);
        return view('admin.certificate-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.certificate-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'background_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'layout_data' => 'nullable|json'
        ]);

        $path = $request->file('background_image')->store('certificate_backgrounds', 'public');

        CertificateTemplate::create([
            'name' => $request->name,
            'background_image_path' => $path,
            'layout_data' => $request->input('layout_data', '[]'), // Simpan layout, default array kosong
        ]);

        return redirect()->route('admin.certificate-templates.index')->with('success', 'Template created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CertificateTemplate $certificateTemplate)
    {
        // Biasanya tidak digunakan untuk template, bisa redirect ke edit
        return redirect()->route('admin.certificate-templates.edit', $certificateTemplate);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CertificateTemplate $certificateTemplate)
    {
        return view('admin.certificate-templates.edit', compact('certificateTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'layout_data' => 'nullable|json'
        ]);

        $data = [
            'name' => $request->name,
            'layout_data' => $request->input('layout_data', '[]'),
        ];

        if ($request->hasFile('background_image')) {
            // Hapus gambar lama
            Storage::disk('public')->delete($certificateTemplate->background_image_path);
            // Simpan gambar baru
            $data['background_image_path'] = $request->file('background_image')->store('certificate_backgrounds', 'public');
        }

        $certificateTemplate->update($data);

        return redirect()->route('admin.certificate-templates.index')->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CertificateTemplate $certificateTemplate)
    {
        // Hapus gambar dari storage
        Storage::disk('public')->delete($certificateTemplate->background_image_path);
        
        $certificateTemplate->delete();

        return redirect()->route('admin.certificate-templates.index')->with('success', 'Template deleted successfully.');
    }
}
