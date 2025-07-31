<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->course->title }}</title>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 0;
            size: A4 landscape;
        }

        body {
            font-family: {{ $certificate->certificateTemplate->styles['font_family'] ?? 'Times New Roman' }}, serif;
            background-color: {{ $certificate->certificateTemplate->styles['background_color'] ?? '#ffffff' }};
            width: 297mm;
            height: 210mm;
            position: relative;
            @if($certificate->certificateTemplate->background_image)
            background-image: url('{{ $certificate->certificateTemplate->background_image }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            @endif
        }

        /* Layout Classes - Tailwind-like */
        .w-full { width: 100%; }
        .h-full { height: 100%; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .items-end { align-items: flex-end; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .relative { position: relative; }

        /* Spacing Classes */
        .p-12 { padding: 48px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-5 { margin-bottom: 20px; }
        .mb-8 { margin-bottom: 32px; }
        .mb-10 { margin-bottom: 40px; }
        .my-5 { margin-top: 20px; margin-bottom: 20px; }
        .my-8 { margin-top: 32px; margin-bottom: 32px; }
        .mt-3 { margin-top: 12px; }
        .mt-16 { margin-top: 64px; }

        /* Typography Classes */
        .text-xs { font-size: 12px; }
        .text-sm { font-size: 14px; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 20px; }
        .text-2xl { font-size: 24px; }
        .text-3xl { font-size: 28px; }
        .text-4xl { font-size: 36px; }
        .text-5xl { font-size: 48px; }
        .font-bold { font-weight: bold; }
        .italic { font-style: italic; }
        .uppercase { text-transform: uppercase; }
        .underline { text-decoration: underline; }
        .leading-relaxed { line-height: 1.6; }
        .leading-tight { line-height: 1.4; }
        .tracking-widest { letter-spacing: 0.1em; }

        /* Color Classes */
        .text-cert-primary { color: {{ $certificate->certificateTemplate->styles['primary_color'] ?? '#2c5282' }}; }
        .text-cert-secondary { color: {{ $certificate->certificateTemplate->styles['secondary_color'] ?? '#4a5568' }}; }
        .text-cert-accent { color: {{ $certificate->certificateTemplate->styles['accent_color'] ?? '#e53e3e' }}; }
        .text-gray-800 { color: #2d3748; }
        .text-gray-600 { color: #718096; }

        /* Border Classes */
        .border-t-2 { border-top: 2px solid; }
        .border-gray-800 { border-color: #2d3748; }

        /* Width Classes */
        .w-48 { width: 192px; }
        .min-w-200 { min-width: 200px; }
        .max-w-4xl { max-width: 896px; }

        /* Font Classes */
        .font-mono { font-family: 'Courier New', monospace; }

        /* Custom Certificate Classes */
        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #2d3748;
            width: 192px;
            margin: 0 auto 12px;
        }

        .break-all {
            word-break: break-all;
        }

        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">

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
            <div class="text-center min-w-200">
                <div class="signature-line"></div>
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
                <div class="mt-3">
                    <span class="font-bold">Verifikasi:</span><br>
                    <span class="text-xs break-all">
                        {{ route('certificates.verify', $certificate->certificate_code) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
