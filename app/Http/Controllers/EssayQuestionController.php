<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\EssayQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EssayQuestionController extends Controller
{
    /**
     * Store a newly created essay question.
     */
    public function store(Request $request, Content $content)
    {
        $this->authorize('update', $content->lesson->course);

        $rules = [
            'question' => 'required|string',
        ];

        // 🆕 Max score hanya required jika scoring enabled
        if ($content->scoring_enabled) {
            $rules['max_score'] = 'required|integer|min:1|max:10000';
        }

        $validated = $request->validate($rules);

        $content->essayQuestions()->create([
            'question' => $validated['question'],
            // 🆕 Set max_score berdasarkan scoring_enabled
            'max_score' => $content->scoring_enabled ? $validated['max_score'] : 0,
            'order' => $content->essayQuestions()->max('order') + 1,
        ]);

        return back()->with('success', 'Pertanyaan essay berhasil ditambahkan.');
    }

    /**
     * Delete an essay question.
     */
    public function destroy($questionId)
    {
        $question = EssayQuestion::findOrFail($questionId);
        
        // Check permission
        $course = $question->content->lesson->course;
        $this->authorize('update', $course);

        $question->delete();
        
        return back()->with('success', 'Pertanyaan berhasil dihapus!');
    }

    /**
     * Update question order.
     */
    public function updateOrder(Request $request, Content $content)
    {
        $course = $content->lesson->course;
        $this->authorize('update', $course);

        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:essay_questions,id',
            'questions.*.order' => 'required|integer',
        ]);

        foreach ($request->questions as $questionData) {
            $content->essayQuestions()
                   ->where('id', $questionData['id'])
                   ->update(['order' => $questionData['order']]);
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, EssayQuestion $question)
    {
        $content = $question->content;
        $this->authorize('update', $content->lesson->course);

        $rules = [
            'question' => 'required|string',
        ];

        // 🆕 Max score hanya required jika scoring enabled
        if ($content->scoring_enabled) {
            $rules['max_score'] = 'required|integer|min:1|max:10000';
        }

        $validated = $request->validate($rules);

        $question->update([
            'question' => $validated['question'],
            // 🆕 Set max_score berdasarkan scoring_enabled
            'max_score' => $content->scoring_enabled ? $validated['max_score'] : 0,
        ]);

        return back()->with('success', 'Pertanyaan essay berhasil diperbarui.');
    }
}