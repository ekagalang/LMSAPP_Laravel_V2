<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->course->title }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        /* Custom styles untuk PDF printing */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Custom font untuk template */
        .certificate-font {
            font-family: {{ $certificate->certificateTemplate->styles['font_family'] ?? 'Times New Roman' }}, serif;
        }

        /* Background image jika ada */
        @if($certificate->certificateTemplate->background_image)
        .certificate-bg {
            background-image: url('{{ $certificate->certificateTemplate->background_image }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        @endif
    </style>

    <script>
        // Konfigurasi warna dinamis untuk Tailwind
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cert-primary': '{{ $certificate->certificateTemplate->styles["primary_color"] ?? "#2c5282" }}',
                        'cert-secondary': '{{ $certificate->certificateTemplate->styles["secondary_color"] ?? "#4a5568" }}',
                        'cert-accent': '{{ $certificate->certificateTemplate->styles["accent_color"] ?? "#e53e3e" }}',
                        'cert-bg': '{{ $certificate->certificateTemplate->styles["background_color"] ?? "#ffffff" }}'
                    }
                }
            }
        }
    </script>
</head>
<body class="m-0 p-0 certificate-font bg-cert-bg w-full h-screen certificate-bg">
    <div class="relative w-full h-full flex flex-col justify-center items-center text-center p-12 box-border">

        <!-- Header -->
        <div class="mb-10">
            <div class="text-5xl font-bold text-cert-primary mb-3 uppercase tracking-widest">
                Sertifikat Penyelesaian
            </div>
            <div class="text-2xl text-cert-secondary mb-10">
                Certificate of Completion
            </div>
        </div>

        <!-- Body -->
        <div class="mb-10 leading-relaxed">
            <div class="text-xl text-gray-800 mb-5">
                Dengan ini menyatakan bahwa
            </div>

            <div class="text-4xl font-bold text-cert-primary my-8 underline">
                {{ $certificate->user->name }}
            </div>

            <div class="text-xl text-gray-800 mb-5">
                Telah berhasil menyelesaikan kursus
            </div>

            <div class="text-3xl font-bold text-cert-accent my-5 italic">
                "{{ $certificate->course->title }}"
            </div>

            @if($certificate->course->description)
            <div class="text-lg text-gray-600 my-5 max-w-4xl">
                {{ Str::limit(strip_tags($certificate->course->description), 200) }}
            </div>
            @endif

            <div class="text-lg text-gray-800 my-5">
                Diselesaikan pada tanggal {{ $certificate->issued_at->format('d F Y') }}
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-end w-full mt-16">
            <!-- Signature Section -->
            <div class="text-center min-w-[200px]">
                <div class="border-t-2 border-gray-800 mb-3 w-48 mx-auto"></div>
                <div class="text-sm text-gray-600 font-bold">
                    @if($certificate->course->instructors->first())
                        {{ $certificate->course->instructors->first()->name }}
                        <br>Instruktur
                    @else
                        Instruktur
                    @endif
                </div>
            </div>

            <!-- Certificate Info -->
            <div class="text-right text-xs text-gray-600 leading-tight">
                <div class="font-bold">Kode Sertifikat:</div>
                <div class="font-mono font-bold text-gray-800">
                    {{ $certificate->certificate_code }}
                </div>
                <div class="mt-3">
                    <span class="font-bold">Tanggal Terbit:</span>
                    {{ $certificate->issued_at->format('d F Y') }}
                </div>
                <div class="mt-1">
                    <span class="font-bold">Verifikasi:</span>
                    <br>
                    <span class="text-xs break-all">
                        {{ route('certificates.verify', $certificate->certificate_code) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
