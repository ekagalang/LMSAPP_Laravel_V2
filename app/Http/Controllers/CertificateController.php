<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
    {
        /**
         * Memicu pembuatan sertifikat (misalnya, saat kursus selesai).
         * Ini adalah metode statis yang bisa dipanggil dari mana saja.
         */
        public static function generateForUser(Course $course, User $user)
        {
            // Cek apakah sertifikat sudah ada untuk menghindari duplikat
            $existingCertificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingCertificate) {
                return $existingCertificate;
            }

            // Pastikan kursus memiliki template yang sudah di-set
            if (!$course->certificate_template_id) {
                return null; // Tidak ada template, tidak ada sertifikat
            }

            // Buat entri sertifikat baru di database
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'template_id' => $course->certificate_template_id,
                'certificate_code' => 'CERT-' . strtoupper(Str::random(12)),
                'issued_at' => now(),
            ]);

            // Siapkan data dinamis untuk menggantikan placeholder
            $replacements = [
                '{{name}}' => $user->name,
                '{{course}}' => $course->title,
                '{{date}}' => $certificate->issued_at->format('d F Y'),
                '{{score}}' => '95.00', // TODO: Ganti dengan skor asli dari gradebook
                '{{certificate_code}}' => $certificate->certificate_code,
                '{{course_summary}}' => strip_tags($course->description),
            ];

            // Render view menjadi string HTML
            $html = view('certificates.render', compact('certificate', 'replacements'))->render();

            // Generate PDF menggunakan Browsershot dan simpan ke storage
            $pdfPath = 'certificates/' . $certificate->certificate_code . '.pdf';
            
            // Pastikan direktori ada
            Storage::disk('public')->makeDirectory('certificates');

            // Konfigurasi path node & npm jika diperlukan (biasanya untuk server produksi)
            // Anda bisa set ini di file .env jika path-nya berbeda
            // NODE_BINARY_PATH=/usr/bin/node
            // NPM_BINARY_PATH=/usr/bin/npm
            Browsershot::html($html)
                ->setNodeBinary(env('NODE_BINARY_PATH', '/usr/bin/node'))
                ->setNpmBinary(env('NPM_BINARY_PATH', '/usr/bin/npm'))
                ->format('A4')
                ->save(Storage::disk('public')->path($pdfPath));

            return $certificate;
        }

        /**
         * Menampilkan halaman verifikasi publik untuk sertifikat.
         */
        public function show($code)
        {
            $certificate = Certificate::where('certificate_code', $code)->with('user', 'course')->firstOrFail();
            return view('certificates.show', compact('certificate'));
        }
    }
    