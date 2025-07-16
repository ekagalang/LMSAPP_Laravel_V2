<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Progres Lengkap Kursus</title>
    <style>
        /* Styling untuk laporan PDF */
        @page {
            margin: 25px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #1a202c;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }
        .content {
            margin-top: 20px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            border: 1px solid #007bff;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #bce0ff;
        }
        .info-table .label {
            font-weight: bold;
            background-color: #e7f5ff;
            width: 120px;
        }
        .progress-summary {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f0f8ff;
            border-radius: 8px;
        }
        .progress-summary h2 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #0056b3;
        }
        .progress-summary .percentage {
            font-size: 40px;
            font-weight: bold;
            color: #1a202c;
        }
        .details-section h2 {
            font-size: 18px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #1a202c;
        }
        .lesson-block {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .lesson-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #e9ecef;
            padding: 8px;
            border-radius: 5px;
        }
        .content-table {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
        }
        .content-table th, .content-table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
        }
        .content-table th {
            background-color: #f8f9fa;
        }
        .status {
            font-weight: bold;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif; /* Font yang mendukung simbol ceklis/silang */
        }
        .status-completed {
            color: #28a745;
        }
        .status-not-completed {
            color: #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9px;
            color: #777;
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            height: 50px;
        }
        .participant-block {
            page-break-after: always; /* Setiap peserta akan mulai di halaman baru */
        }
        .participant-block:last-child {
            page-break-after: auto; /* Peserta terakhir tidak perlu page break setelahnya */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Progres Lengkap</h1>
            <p>Kursus: {{ $course->title ?? 'N/A' }}</p>
            <p>Tanggal Cetak: {{ $date ?? 'N/A' }}</p>
        </div>

        <div class="content">
            @forelse($participantsProgress ?? [] as $participantData)
                <div class="participant-block">
                    <!-- Participant Info -->
                    <table class="info-table">
                        <tr>
                            <td class="label">Nama Peserta</td>
                            {{-- FIX: Mengakses data sebagai array atau object --}}
                            <td>{{ $participantData['name'] ?? ($participantData->name ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Email</td>
                            {{-- FIX: Mengakses data sebagai array atau object --}}
                            <td>{{ $participantData['email'] ?? ($participantData->email ?? 'N/A') }}</td>
                        </tr>
                    </table>

                    <!-- Progress Summary -->
                    <div class="progress-summary">
                        <h2>Total Progres Penyelesaian</h2>
                        {{-- FIX: Mengakses data sebagai array atau object --}}
                        <div class="percentage">{{ $participantData['progressPercentage'] ?? ($participantData->progressPercentage ?? 0) }}%</div>
                    </div>

                    <!-- Detailed Progress -->
                    <div class="details-section">
                        <h2>Rincian Progres per Materi</h2>
                        {{-- FIX: Mengakses data sebagai array atau object --}}
                        @php $lessons = $participantData['lessons'] ?? ($participantData->lessons ?? []); @endphp
                        @forelse($lessons as $lesson)
                            <div class="lesson-block">
                                <div class="lesson-title">{{ $lesson->title }}</div>
                                @if(!empty($lesson->contents))
                                    <table class="content-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">No.</th>
                                                <th style="width: 75%;">Judul Materi</th>
                                                <th style="width: 20%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lesson->contents as $index => $content)
                                                <tr>
                                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                                    <td>{{ $content->title }}</td>
                                                    @if($content->is_completed)
                                                        <td class="status status-completed">✔ Selesai</td>
                                                    @else
                                                        <td class="status status-not-completed">✖ Belum Selesai</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p style="padding-left: 10px; color: #777;">Tidak ada materi dalam pelajaran ini.</p>
                                @endif
                            </div>
                        @empty
                            <p>Tidak ada pelajaran dalam kursus ini.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <p style="text-align: center; font-size: 16px;">Tidak ada peserta yang terdaftar di kursus ini untuk dilaporkan.</p>
            @endforelse
        </div>

        <div class="footer">
            Laporan ini dibuat secara otomatis oleh sistem LMS.
        </div>
    </div>
</body>
</html>