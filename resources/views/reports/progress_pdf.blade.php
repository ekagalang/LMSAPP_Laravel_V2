<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Progres Kursus</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 0; }
        h2 { font-size: 14px; text-align: center; margin-top: 5px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .progress-bar-container { border: 1px solid #ccc; border-radius: 5px; height: 20px; width: 100%; background-color: #e9ecef; }
        .progress-bar { background-color: #0d6efd; height: 100%; text-align: center; color: white; line-height: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Laporan Progres Peserta</h1>
        <h2>Kursus: {{ $course->title }}</h2>
        <p style="text-align: center; font-size: 10px;">Dicetak pada: {{ $date }}</p>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Nama Peserta</th>
                    <th style="width: 50%;">Progres Penyelesaian</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($participantsProgress as $participant)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $participant['name'] }}<br>
                            <small style="color: #6c757d;">{{ $participant['email'] }}</small>
                        </td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: {{ $participant['progress_percentage'] }}%;">
                                    {{ $participant['progress_percentage'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">Tidak ada peserta di kursus ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>