<?php

namespace App\Imports;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuizImport implements ToCollection, WithHeadingRow
{
    protected $lessonId;
    protected $userId;
    protected $errors = [];
    protected $successCount = 0;

    public function __construct($lessonId, $userId)
    {
        $this->lessonId = $lessonId;
        $this->userId = $userId;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        // Group rows by quiz title
        $quizGroups = $rows->groupBy('quiz_title');

        foreach ($quizGroups as $quizTitle => $quizRows) {
            try {
                DB::beginTransaction();

                // Get quiz details from first row
                $firstRow = $quizRows->first();

                // Validate quiz data
                $validator = Validator::make($firstRow->toArray(), [
                    'quiz_title' => 'required|string|max:255',
                    'quiz_description' => 'nullable|string',
                    'passing_percentage' => 'required|integer|min:0|max:100',
                    'time_limit' => 'nullable|integer|min:1|max:1440',
                    'show_answers_after_attempt' => 'nullable|in:yes,no,1,0',
                    'enable_leaderboard' => 'nullable|in:yes,no,1,0',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Quiz '{$quizTitle}': " . implode(', ', $validator->errors()->all());
                    DB::rollBack();
                    continue;
                }

                // Create quiz
                $quiz = Quiz::create([
                    'lesson_id' => $this->lessonId,
                    'user_id' => $this->userId,
                    'title' => $firstRow['quiz_title'],
                    'description' => $firstRow['quiz_description'] ?? null,
                    'passing_percentage' => $firstRow['passing_percentage'],
                    'time_limit' => $firstRow['time_limit'] ?? null,
                    'show_answers_after_attempt' => $this->parseBoolean($firstRow['show_answers_after_attempt'] ?? 'no'),
                    'enable_leaderboard' => $this->parseBoolean($firstRow['enable_leaderboard'] ?? 'no'),
                    'status' => $firstRow['status'] ?? 'draft',
                ]);

                // Process questions for this quiz
                foreach ($quizRows as $row) {
                    // Validate question data
                    $questionValidator = Validator::make($row->toArray(), [
                        'question_text' => 'required|string',
                        'question_type' => 'required|in:multiple_choice,true_false',
                        'marks' => 'required|integer|min:1',
                        'option_1' => 'required_if:question_type,multiple_choice',
                        'option_2' => 'required_if:question_type,multiple_choice',
                        'correct_answer' => 'required',
                    ]);

                    if ($questionValidator->fails()) {
                        throw new \Exception("Question validation failed: " . implode(', ', $questionValidator->errors()->all()));
                    }

                    // Create question
                    $question = Question::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $row['question_text'],
                        'type' => $row['question_type'],
                        'marks' => $row['marks'],
                    ]);

                    // Create options based on question type
                    if ($row['question_type'] === 'multiple_choice') {
                        $this->createMultipleChoiceOptions($question, $row);
                    } else {
                        $this->createTrueFalseOptions($question, $row);
                    }
                }

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Quiz '{$quizTitle}': " . $e->getMessage();
            }
        }
    }

    /**
     * Create options for multiple choice questions
     */
    protected function createMultipleChoiceOptions(Question $question, $row)
    {
        $options = [];

        // Collect all options from the row
        for ($i = 1; $i <= 10; $i++) {
            $optionKey = 'option_' . $i;
            if (isset($row[$optionKey]) && !empty($row[$optionKey])) {
                $options[] = $row[$optionKey];
            }
        }

        // Create option records
        foreach ($options as $index => $optionText) {
            $isCorrect = false;

            // Check if this is the correct answer
            // Support multiple formats: "option 1", "option_1", "1", etc.
            $correctAnswer = strtolower(trim($row['correct_answer']));
            $optionNumber = $index + 1;

            if (
                $correctAnswer === "option {$optionNumber}" ||
                $correctAnswer === "option_{$optionNumber}" ||
                $correctAnswer === (string)$optionNumber ||
                $correctAnswer === strtolower(trim($optionText))
            ) {
                $isCorrect = true;
            }

            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => $isCorrect,
            ]);
        }
    }

    /**
     * Create options for true/false questions
     */
    protected function createTrueFalseOptions(Question $question, $row)
    {
        $correctAnswer = strtolower(trim($row['correct_answer']));

        // Normalize the correct answer
        $isTrue = in_array($correctAnswer, ['true', 'benar', '1', 'yes']);

        Option::create([
            'question_id' => $question->id,
            'option_text' => 'True',
            'is_correct' => $isTrue,
        ]);

        Option::create([
            'question_id' => $question->id,
            'option_text' => 'False',
            'is_correct' => !$isTrue,
        ]);
    }

    /**
     * Parse boolean values from various formats
     */
    protected function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['yes', '1', 'true', 'ya']);
    }

    /**
     * Get import errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get success count
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
