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

        try {
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

                    $submission->answers()->delete();

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

            // ✅ FIX: Mark as completed but DON'T do complex lesson logic here
            $user->completedContents()->syncWithoutDetaching([
                $content->id => ['completed' => true, 'completed_at' => now()]
            ]);

            // ✅ FIX: Simple redirect - let the view handle navigation
            return redirect()->route('contents.show', $content)
                ->with('success', 'Essay submitted successfully!');
        } catch (\Exception $e) {
            Log::error('Essay submission error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'content_id' => $content->id
            ]);

            return back()->withInput()->with('error', 'Failed to submit essay. Please try again.');
        }
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
