<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserImportController extends Controller
{
    /**
     * Menampilkan halaman form untuk mengunggah file.
     */
    public function show()
    {
        $this->authorize('create', User::class); 
        $courses = Course::orderBy('title')->get();
        return view('admin.users.import', compact('courses'));
    }

    /**
     * Memproses file yang diunggah dan membuat pengguna baru.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'user_file' => 'required|mimes:xlsx,xls,csv',
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $file = $request->file('user_file');

        try {
            $data = Excel::toArray(new \stdClass(), $file)[0];
            $importedCount = 0;
            $errors = [];

            foreach (array_slice($data, 1) as $rowIndex => $row) {
                $name = $row[0] ?? null;
                $email = $row[1] ?? null;
                $password = $row[2] ?? null;

                $validator = Validator::make(
                    ['name' => $name, 'email' => $email, 'password' => $password],
                    [
                        'name' => 'required|string|max:255',
                        'email' => 'required|string|email|max:255|unique:users',
                        'password' => 'required|string|min:8',
                    ]
                );

                if ($validator->fails()) {
                    $errors[] = "Baris " . ($rowIndex + 2) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);

                $user->assignRole('participant');
                $course->enrolledUsers()->syncWithoutDetaching($user->id);
                $importedCount++;
            }

            $message = "Berhasil mengimpor {$importedCount} pengguna.";
            if (!empty($errors)) {
                $message .= " Gagal mengimpor " . count($errors) . " baris.";
                return redirect()->back()->with('success', $message)->with('import_errors', $errors);
            }

            return redirect()->route('admin.users.index')->with('success', $message);

        } catch (Exception $e) {
            Log::error('Bulk User Import Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file. Pastikan format file sudah benar.');
        }
    }

    /**
     * [BARU] Menghasilkan dan mengunduh file template CSV.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Menulis baris header
            fputcsv($file, ['name', 'email', 'password']);
            // Menulis baris contoh untuk panduan
            fputcsv($file, ['John Doe', 'john.doe@example.com', 'password123']);
            fputcsv($file, ['Jane Smith', 'jane.smith@example.com', 'securepass']);
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
