<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Handle the image upload from Summernote editor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->file('image')) {
            // Simpan gambar ke public storage
            $path = $request->file('image')->store('images/posts', 'public');
            
            // Kembalikan URL gambar untuk disisipkan ke editor
            return response()->json(['url' => Storage::url($path)]);
        }

        return response()->json(['error' => 'Upload failed.'], 400);
    }
}
