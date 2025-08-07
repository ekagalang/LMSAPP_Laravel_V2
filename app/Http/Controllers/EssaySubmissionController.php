<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\EssaySubmission;
use App\Models\EssayAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EssaySubmissionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Content $content)
    {
        if ($content->type !== 'essay') {
            return back()->with('error', 'Invalid content type.');
        }

        $user = Auth::user();
        $questions = $content->essayQuestions;
        
        if ($questions->count() > 0) {
            // NEW SYSTEM: Multiple questions
            $rules = [];
            foreach ($questions as $question) {
                $rules["answer_{$question->id}"] = 'required|string';
            }
            $request->validate($rules);

            DB::transaction(function () use ($request, $content, $user, $questions) {
                $submission = EssaySubmission::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'content_id' => $content->id,
                    ],
                    [
                        'status' => 'submitted',
                        'graded_at' => null,
                    ]
                );

                // Hapus jawaban lama
                $submission->answers()->delete();

                // Simpan jawaban baru
                foreach ($questions as $question) {
                    EssayAnswer::create([
                        'submission_id' => $submission->id,
                        'question_id' => $question->id,
                        'answer' => $request->input("answer_{$question->id}"),
                    ]);
                }
            });
        } else {
            // OLD SYSTEM: Backward compatibility
            $request->validate([
                'essay_content' => 'required|string',
            ]);

            $submission = EssaySubmission::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'content_id' => $content->id,
                ],
                [
                    'status' => 'submitted',
                    'graded_at' => null,
                ]
            );

            $submission->answers()->delete();
            EssayAnswer::create([
                'submission_id' => $submission->id,
                'question_id' => null,
                'answer' => $request->input('essay_content'),
            ]);
        }

        // Mark as completed
        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);

        // Check lesson completion
        $lesson = $content->lesson;
        if ($lesson && method_exists($user, 'hasCompletedAllContentsInLesson')) {
            $allContentsCompleted = $user->hasCompletedAllContentsInLesson($lesson);
            if ($allContentsCompleted) {
                $user->lessons()->syncWithoutDetaching([
                    $lesson->id => ['status' => 'completed']
                ]);
            }
        }
        
        return redirect()->route('contents.show', $content)
                        ->with('success', 'Essay submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function showResult(EssaySubmission $submission)
    {
        if (Auth::id() !== $submission->user_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        $submission->load([
            'content.lesson.course',
            'content.essayQuestions' => function ($query) {
                $query->orderBy('order');
            },
            'answers.question',
            'user'
        ]);

        return view('essays.result', compact('submission'));
    }
}