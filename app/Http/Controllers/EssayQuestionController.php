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
        // Check permission - hanya yang bisa update course
        $course = $content->lesson->course;
        $this->authorize('update', $course);

        $request->validate([
            'question' => 'required|string',
            'max_score' => 'required|integer|min:1|max:1000',
        ]);

        $maxOrder = $content->essayQuestions()->max('order') ?? 0;

        $content->essayQuestions()->create([
            'question' => $request->question,
            'max_score' => $request->max_score,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan!');
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

    public function update(Request $request, $questionId)
    {
        $question = EssayQuestion::findOrFail($questionId);
        
        // Check permission
        $course = $question->content->lesson->course;
        $this->authorize('update', $course);

        $request->validate([
            'question' => 'required|string',
            'max_score' => 'required|integer|min:1|max:1000',
        ]);

        $question->update([
            'question' => $request->question,
            'max_score' => $request->max_score,
        ]);

        return back()->with('success', 'Pertanyaan berhasil diupdate!');
    }
}