<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\EssaySubmission;
use App\Models\EssayAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Autosave draft answer (called via AJAX)
     */
    public function autosave(Request $request, Content $content)
    {
        if ($content->type !== 'essay') {
            return response()->json(['error' => 'Invalid content type'], 400);
        }

        $user = Auth::user();

        // Validate input
        $request->validate([
            'question_id' => 'required|exists:essay_questions,id',
            'answer' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $content, $user) {
                // Get or create submission as draft
                $submission = EssaySubmission::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'content_id' => $content->id,
                    ],
                    [
                        'status' => 'draft',
                    ]
                );

                // Update or create the answer for this question
                EssayAnswer::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'question_id' => $request->question_id,
                    ],
                    [
                        'answer' => $request->answer ?? '',
                    ]
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Draft saved',
                'saved_at' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Autosave error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'content_id' => $content->id,
                'question_id' => $request->question_id
            ]);

            return response()->json([
                'error' => 'Failed to save draft'
            ], 500);
        }
    }

    /**
     * Get draft answers for the current user
     */
    public function getDrafts(Content $content)
    {
        if ($content->type !== 'essay') {
            return response()->json(['error' => 'Invalid content type'], 400);
        }

        $user = Auth::user();

        $submission = EssaySubmission::where('user_id', $user->id)
            ->where('content_id', $content->id)
            ->with('answers')
            ->first();

        if (!$submission) {
            return response()->json(['drafts' => []]);
        }

        $drafts = [];
        foreach ($submission->answers as $answer) {
            $drafts[$answer->question_id] = $answer->answer;
        }

        return response()->json([
            'drafts' => $drafts,
            'status' => $submission->status
        ]);
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
