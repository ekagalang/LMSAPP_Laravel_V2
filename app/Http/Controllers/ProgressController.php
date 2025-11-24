<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Content;
use App\Models\Lesson;
use App\Models\Course; // <-- TAMBAHKAN USE STATEMENT
use App\Models\Certificate; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Support\Facades\Log; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN USE STATEMENT
use Barryvdh\DomPDF\Facade\Pdf;

class ProgressController extends Controller
{
    public function myScores(Course $course)
    {
        $user = Auth::user();

        // Authorization: only users allowed to attempt quizzes (participants) can view their scores
        if (!$user->can('attempt quizzes')) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk peserta.');
        }

        // Ensure participant is enrolled in the course
        if (!$course->enrolledUsers()->where('user_id', $user->id)->exists()) {
            abort(403, 'Anda tidak terdaftar pada kursus ini.');
        }

        // QUIZ: Ambil semua attempt peserta untuk kuis dalam kursus ini
        $quizAttempts = $user->quizAttempts()
            ->whereHas('quiz.lesson.course', function ($q) use ($course) {
                $q->where('id', $course->id);
            })
            ->with(['quiz' => function ($q) {
                // ✅ FIX: Ganti total_marks dan pass_marks dengan passing_percentage
                // Kolom total_marks dan pass_marks sudah dihapus di migration 2025_10_03_133115
                $q->select('id', 'title', 'passing_percentage', 'lesson_id')->with('questions');
            }, 'quiz.lesson.course'])
            ->orderByDesc('completed_at')
            ->get();

        // Kelompokkan per kuis dan tampilkan SEMUA attempt (dengan ringkasan attempt terbaru untuk perhitungan rata-rata)
        $quizSummaries = $quizAttempts
            ->groupBy('quiz_id')
            ->map(function ($attempts) {
                $attemptsSorted = $attempts->sortByDesc('completed_at');
                $quiz = $attemptsSorted->first()->quiz;

                $attemptItems = $attemptsSorted->map(function ($attempt) use ($quiz) {
                    // ✅ FIX: Hitung total_marks dari sum marks di questions
                    // Karena kolom total_marks sudah tidak ada, kita hitung dari questions
                    $totalMarks = (int) ($quiz->questions->sum('marks'));
                    $totalMarks = max(1, $totalMarks); // Minimal 1 untuk avoid division by zero

                    $percentage = round(((int)$attempt->score / $totalMarks) * 100, 2);

                    return [
                        'attempt_id' => $attempt->id,
                        'score' => (int) $attempt->score,
                        'total' => (int) $totalMarks,
                        'percentage' => $percentage,
                        'passed' => (bool) $attempt->passed,
                        'completed_at' => $attempt->completed_at,
                    ];
                })->values();

                $latest = $attemptItems->first();

                return [
                    'quiz_id' => $quiz->id,
                    'title' => $quiz->title,
                    'latest_percentage' => $latest['percentage'] ?? 0,
                    'attempts' => $attemptItems,
                ];
            })
            ->values();

        $quizAverage = round(($quizSummaries->avg('latest_percentage') ?? 0), 2);

        // ESSAY: Ambil semua submission dalam kursus ini
        $essaySubmissions = $user->essaySubmissions()
            ->whereHas('content.lesson.course', function ($q) use ($course) {
                $q->where('id', $course->id);
            })
            ->with(['content' => function ($q) {
                $q->select('id', 'title', 'lesson_id', 'type', 'scoring_enabled', 'grading_mode');
            }, 'answers'])
            ->get();

        $essaySummaries = $essaySubmissions->map(function ($submission) {
            $scoringEnabled = (bool) ($submission->content->scoring_enabled ?? true);

            if ($scoringEnabled) {
                $score = (int) ($submission->total_score ?? 0);
                $max = (int) ($submission->max_total_score ?? 0);
                $percentage = $max > 0 ? round(($score / $max) * 100, 2) : null;
            } else {
                $score = null;
                $max = null;
                $percentage = null;
            }

            return [
                'submission_id' => $submission->id,
                'title' => $submission->content->title,
                'scoring_enabled' => $scoringEnabled,
                'score' => $score,
                'total' => $max,
                'percentage' => $percentage,
                'graded' => (bool) $submission->is_fully_graded,
                'graded_at' => $submission->graded_at,
            ];
        });

        $essayAverage = round(($essaySummaries->filter(fn ($e) => $e['percentage'] !== null)->avg('percentage') ?? 0), 2);

        return view('progress.my-scores', [
            'course' => $course,
            'quizSummaries' => $quizSummaries,
            'essaySummaries' => $essaySummaries,
            'quizAverage' => $quizAverage,
            'essayAverage' => $essayAverage,
        ]);
    }
    public function markContentAsCompleted(Content $content)
    {
        $user = Auth::user();
        $lesson = $content->lesson;
        $course = $lesson->course;

        // Tandai konten saat ini sebagai selesai
        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);

        // Cek apakah lesson (materi) sekarang sudah selesai
        if ($user->hasCompletedAllContentsInLesson($lesson)) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['status' => 'completed']]);
        }

        // =================================================================
        // PERUBAHAN UTAMA: Menonaktifkan pemanggilan fungsi pengecekan sertifikat otomatis
        // Logika ini akan dipindahkan ke tombol manual di dashboard peserta.
        // $this->checkAndGenerateCertificate($course, $user);
        // =================================================================

        // Cari konten berikutnya dalam pelajaran yang sama berdasarkan urutan
        $nextContent = $lesson->contents()
            ->where('order', '>', $content->order)
            ->orderBy('order', 'asc')
            ->first();

        // Jika ada konten berikutnya, arahkan ke sana
        if ($nextContent) {
            return redirect()->route('contents.show', ['content' => $nextContent->id])
                ->with('success', 'Lanjut ke konten berikutnya!');
        }

        // Jika tidak ada konten berikutnya (konten terakhir dalam pelajaran),
        // cek apakah semua pelajaran di kursus ini sudah selesai.
        $allLessonsCompleted = true;
        foreach ($course->lessons as $courseLesson) {
            if (!$user->hasCompletedLesson($courseLesson)) {
                $allLessonsCompleted = false;
                break;
            }
        }

        if ($allLessonsCompleted) {
            // Selaras dengan alur completeAndContinue: arahkan ke dashboard saat kursus selesai
            return redirect()->route('dashboard')->with('success', 'Selamat! Anda telah menyelesaikan seluruh kursus ini.');
        }

        // Jika ini adalah konten terakhir dari pelajaran, tapi masih ada pelajaran lain,
        // kembalikan ke halaman kursus.
        return redirect()->route('courses.show', $course->id)->with('success', 'Selamat! Anda telah menyelesaikan pelajaran ini.');
    }

    public function markLessonAsCompleted(Lesson $lesson)
    {
        $user = Auth::user();

        // Tandai semua konten dalam pelajaran ini sebagai selesai juga
        $contentIds = $lesson->contents->pluck('id')->toArray();
        if (!empty($contentIds)) {
            $user->contents()->syncWithoutDetaching(
                array_fill_keys($contentIds, ['completed' => true, 'completed_at' => now()])
            );
        }

        $user->lessons()->syncWithoutDetaching([$lesson->id => ['status' => 'completed']]);

        return redirect()->back()->with('success', 'Pelajaran berhasil ditandai selesai!');
    }

    // =================================================================
    // FUNGSI BARU: Untuk memeriksa dan men-generate sertifikat
    // =================================================================
    private function checkAndGenerateCertificate(Course $course, User $user)
    {
        Log::info("Checking certificate eligibility for user {$user->id} in course {$course->id}");

        // Cek apakah course punya template sertifikat
        if (!$course->certificate_template_id) {
            Log::info("No certificate template set for course {$course->id}");
            return;
        }

        // Cek progress user
        $progress = $user->courseProgress($course);
        Log::info("User {$user->id} progress: {$progress}%");

        // Syarat untuk sertifikat: progress 100%
        if ($progress >= 100) {
            // Cek apakah sertifikat sudah ada
            $existingCertificate = Certificate::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$existingCertificate) {
                Log::info("Generating new certificate for user {$user->id} in course {$course->id}");
                $this->generateCertificate($course, $user);
            } else {
                Log::info("Certificate already exists for user {$user->id} in course {$course->id}");
            }
        } else {
            Log::info("Certificate conditions not met - Progress: {$progress}%");
        }
    }

    private function generateCertificate(Course $course, User $user)
    {
        $template = $course->certificateTemplate;
        if (!$template) {
            Log::warning("Certificate generation skipped for user {$user->id} in course {$course->id}: No template found.");
            return;
        }

        Log::info("Generating certificate for user {$user->id} in course {$course->id}");

        try {
            // Generate unique certificate code
            $certificateCode = Certificate::generateCertificateCode();

            // Create certificate record first
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_template_id' => $template->id,
                'certificate_code' => $certificateCode,
                'issued_at' => now(),
            ]);

            // Generate PDF using the enhanced certificate render view
            $pdf = Pdf::loadView('certificates.template-render', compact('certificate'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'dpi' => 96, // Match screen DPI for consistent sizing
                    'defaultFont' => 'times',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'enable_local_file_access' => true,
                    'chroot' => public_path(),
                ]);

            // Create certificates directory if it doesn't exist
            $certificatesDir = 'certificates';
            if (!Storage::disk('public')->exists($certificatesDir)) {
                Storage::disk('public')->makeDirectory($certificatesDir);
            }

            // Save PDF file
            $fileName = $certificateCode . '.pdf';
            $filePath = $certificatesDir . '/' . $fileName;

            Storage::disk('public')->put($filePath, $pdf->output());

            // Update certificate record with file path
            $certificate->update(['path' => $filePath]);

            Log::info("Certificate generated successfully for user {$user->id} in course {$course->id}, file: {$filePath}");

            // Optionally, you can trigger a notification here
            // $user->notify(new CertificateGeneratedNotification($certificate));

            return $certificate;
        } catch (\Exception $e) {
            Log::error("Certificate generation failed for user {$user->id} in course {$course->id}: " . $e->getMessage());

            // Clean up certificate record if PDF generation failed
            if (isset($certificate)) {
                $certificate->delete();
            }

            return null;
        }
    }

    public function exportCourseProgressPdf(Course $course)
    {
        try {
            $participants = $course->enrolledUsers()->orderBy('name')->get();
            $lessons = $course->lessons()->with(['contents' => function ($query) {
                $query->orderBy('order');
            }])->orderBy('order')->get();

            $participantsProgress = [];

            foreach ($participants as $participant) {
                $progressData = $participant->getProgressForCourse($course);

                // ===== QUIZ SCORES (WORKING) =====
                $quizScores = $participant->quizAttempts()
                    ->whereHas('quiz.lesson.course', function ($query) use ($course) {
                        $query->where('id', $course->id);
                    })
                    ->where('passed', true)
                    ->with('quiz.questions')
                    ->get()
                    ->map(function ($attempt) {
                        $totalQuestions = $attempt->quiz->questions->count();
                        return [
                            'quiz_title' => $attempt->quiz->title,
                            'score' => $attempt->score,
                            'max_score' => $totalQuestions,
                            'percentage' => $totalQuestions > 0 ? round(($attempt->score / $totalQuestions) * 100, 2) : 0
                        ];
                    });

                // ===== ESSAY SCORES DEBUG =====
                \Log::info('=== ESSAY DEBUG START ===', [
                    'participant_id' => $participant->id,
                    'participant_name' => $participant->name,
                    'course_id' => $course->id
                ]);

                // Step 1: Check essay submissions
                $essaySubmissions = $participant->essaySubmissions()
                    ->whereHas('content.lesson.course', function ($query) use ($course) {
                        $query->where('id', $course->id);
                    })
                    ->with(['content', 'answers'])
                    ->get();

                \Log::info('Essay Submissions Found', [
                    'count' => $essaySubmissions->count(),
                    'submissions' => $essaySubmissions->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'content_id' => $sub->content_id,
                            'content_title' => $sub->content->title ?? 'No title',
                            'answers_count' => $sub->answers->count(),
                            'answers_with_score' => $sub->answers->whereNotNull('score')->count(),
                            'answers_detail' => $sub->answers->map(function ($ans) {
                                return [
                                    'id' => $ans->id,
                                    'score' => $ans->score,
                                    'question_id' => $ans->question_id,
                                    'has_answer' => !empty($ans->answer)
                                ];
                            })
                        ];
                    })
                ]);

                // Step 2: Check essay content in course
                $essayContents = $course->lessons()
                    ->with('contents')
                    ->get()
                    ->flatMap(function ($lesson) {
                        return $lesson->contents->where('type', 'essay');
                    });

                \Log::info('Essay Contents in Course', [
                    'count' => $essayContents->count(),
                    'contents' => $essayContents->map(function ($content) {
                        return [
                            'id' => $content->id,
                            'title' => $content->title,
                            'questions_count' => $content->essayQuestions()->count()
                        ];
                    })
                ]);

                // Step 3: Build essay scores
                $essayScores = collect();

                foreach ($essaySubmissions as $submission) {
                    $answersWithScores = $submission->answers()->whereNotNull('score')->get();

                    \Log::info('Processing Submission', [
                        'submission_id' => $submission->id,
                        'content_title' => $submission->content->title,
                        'total_answers' => $submission->answers->count(),
                        'graded_answers' => $answersWithScores->count(),
                        'answers_detail' => $answersWithScores->map(function ($ans) {
                            return ['score' => $ans->score, 'question_id' => $ans->question_id];
                        })
                    ]);

                    if ($answersWithScores->count() > 0) {
                        $averageScore = $answersWithScores->avg('score');

                        $essayScores->push([
                            'essay_title' => $submission->content->title,
                            'score' => round($averageScore, 2),
                            'percentage' => round($averageScore, 2)
                        ]);

                        \Log::info('Essay Score Added', [
                            'title' => $submission->content->title,
                            'average_score' => $averageScore
                        ]);
                    } else {
                        \Log::info('No graded answers found for submission', [
                            'submission_id' => $submission->id,
                            'content_title' => $submission->content->title
                        ]);
                    }
                }

                \Log::info('Final Essay Scores', [
                    'count' => $essayScores->count(),
                    'scores' => $essayScores->toArray()
                ]);

                \Log::info('=== ESSAY DEBUG END ===');

                $participantsProgress[] = (object)[
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'progress_percentage' => $progressData['progress_percentage'],
                    'completed_count' => $progressData['completed_count'],
                    'total_count' => $progressData['total_count'],
                    'quiz_scores' => $quizScores,
                    'essay_scores' => $essayScores,
                    'quiz_average' => $quizScores->avg('percentage') ?? 0,
                    'essay_average' => $essayScores->avg('percentage') ?? 0,
                ];
            }

            $totalContentCount = 0;
            foreach ($lessons as $lesson) {
                $totalContentCount += $lesson->contents->count();
            }

            $data = [
                'course' => $course,
                'participantsProgress' => collect($participantsProgress),
                'date' => now()->translatedFormat('d F Y'),
                'total_content_count' => $totalContentCount
            ];

            $pdf = Pdf::loadView('reports.progress_pdf', $data);
            $pdf->setPaper('a4', 'portrait');

            $fileName = 'laporan-progres-lengkap-' . Str::slug($course->title) . '.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            \Log::error('PDF Export Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }



    public function debugEssayScores(Course $course, User $participant)
    {
        // Debug essay submissions
        $submissions = $participant->essaySubmissions()
            ->whereHas('content.lesson.course', function ($query) use ($course) {
                $query->where('id', $course->id);
            })
            ->with(['content.essayQuestions', 'essayAnswers'])
            ->get();

        $debug = [];
        foreach ($submissions as $submission) {
            $debug[] = [
                'submission_id' => $submission->id,
                'content_title' => $submission->content->title,
                'total_questions' => $submission->content->essayQuestions->count(),
                'total_answers' => $submission->essayAnswers->count(),
                'graded_answers' => $submission->essayAnswers->whereNotNull('score')->count(),
                'answers_detail' => $submission->essayAnswers->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'score' => $answer->score,
                        'has_score' => !is_null($answer->score)
                    ];
                })
            ];
        }

        return $debug;
    }
}
