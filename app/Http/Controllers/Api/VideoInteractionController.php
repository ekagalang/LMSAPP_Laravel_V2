<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoInteraction;
use App\Models\VideoInteractionResponse;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class VideoInteractionController extends Controller
{
    /**
     * Get all interactions for a specific content
     */
    public function getContentInteractions($contentId): JsonResponse
    {
        try {
            $content = Content::findOrFail($contentId);
            
            // Check if user has access to this content
            if (!$this->userHasAccessToContent($content)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Cache key for interactions (without user-specific responses)
            $cacheKey = "video_interactions_content_{$content->id}";
            
            $interactions = Cache::remember($cacheKey, 300, function() use ($content) { // 5 minutes cache
                return $content->videoInteractions()
                    ->select('id', 'content_id', 'type', 'timestamp', 'title', 'description', 'data', 'position', 'is_active')
                    ->where('is_active', true)
                    ->orderBy('timestamp')
                    ->get();
            });
            
            // Load user responses separately (can't cache user-specific data)
            $userResponses = collect();
            if (!$interactions->isEmpty()) {
                $interactionIds = $interactions->pluck('id')->toArray();
                $userResponses = VideoInteractionResponse::whereIn('video_interaction_id', $interactionIds)
                    ->where('user_id', Auth::id())
                    ->select('id', 'video_interaction_id', 'user_id', 'response_data', 'is_correct', 'answered_at')
                    ->get()
                    ->keyBy('video_interaction_id');
            }
            
            $formattedInteractions = $interactions->map(function ($interaction) use ($userResponses) {
                $userResponse = $userResponses->get($interaction->id);
                
                return [
                    'id' => $interaction->id,
                    'type' => $interaction->type,
                    'timestamp' => $interaction->timestamp,
                    'title' => $interaction->title,
                    'description' => $interaction->description,
                    'data' => $interaction->data,
                    'position' => $interaction->position,
                    'user_response' => $userResponse ? [
                        'response_data' => $userResponse->response_data,
                        'is_correct' => $userResponse->is_correct,
                        'answered_at' => $userResponse->answered_at,
                    ] : null,
                ];
            });
            
            return response()->json([
                'interactions' => $formattedInteractions,
                'content_id' => $content->id
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getContentInteractions', [
                'content_id' => $contentId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Content not found'], 404);
        }
    }

    /**
     * Store user response to video interaction
     */
    public function storeResponse(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'video_interaction_id' => 'required|exists:video_interactions,id',
            'response_data' => 'required|array',
            'answered_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $interaction = VideoInteraction::findOrFail($request->video_interaction_id);
            
            // Check if user has access to this content
            if (!$this->userHasAccessToContent($interaction->content)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Check if user already responded to this interaction
            $existingResponse = VideoInteractionResponse::where([
                'video_interaction_id' => $interaction->id,
                'user_id' => Auth::id()
            ])->first();

            if ($existingResponse) {
                return response()->json(['error' => 'Already responded to this interaction'], 409);
            }

            // Evaluate response based on interaction type
            $isCorrect = null;
            $feedback = '';

            if ($interaction->type === 'quiz') {
                $result = $this->evaluateQuizResponse($interaction, $request->response_data);
                $isCorrect = $result['is_correct'];
                $feedback = $result['feedback'];
            } elseif ($interaction->type === 'reflection') {
                $result = $this->evaluateReflectionResponse($interaction, $request->response_data);
                $isCorrect = $result['is_correct'];
                $feedback = $result['feedback'];
            }

            // Store response
            $response = VideoInteractionResponse::create([
                'video_interaction_id' => $interaction->id,
                'user_id' => Auth::id(),
                'response_data' => $request->response_data,
                'is_correct' => $isCorrect,
                'answered_at' => $request->answered_at
            ]);

            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'feedback' => $feedback,
                'response_id' => $response->id
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save response'], 500);
        }
    }

    /**
     * Get user's progress on video interactions for a content
     */
    public function getUserProgress($contentId): JsonResponse
    {
        try {
            $content = Content::findOrFail($contentId);
            
            // Check if user has access to this content
            if (!$this->userHasAccessToContent($content)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $totalInteractions = $content->videoInteractions()->count();
            $userResponses = $content->getUserVideoResponses(Auth::id());
            $correctResponses = $userResponses->where('is_correct', true)->count();
            
            $progress = [
                'total_interactions' => $totalInteractions,
                'answered_interactions' => $userResponses->count(),
                'correct_answers' => $correctResponses,
                'completion_percentage' => $totalInteractions > 0 ? 
                    round(($userResponses->count() / $totalInteractions) * 100, 2) : 100,
                'success_rate' => $userResponses->count() > 0 ? 
                    round(($correctResponses / $userResponses->count()) * 100, 2) : 0,
                'is_completed' => $content->hasUserCompletedVideoInteractions(Auth::id())
            ];

            return response()->json(['progress' => $progress]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Content not found'], 404);
        }
    }

    /**
     * Get content statistics (for instructors)
     */
    public function getContentStats($contentId): JsonResponse
    {
        try {
            $content = Content::findOrFail($contentId);
            
            // Check if user is instructor for this content
            if (!$this->userIsInstructorForContent($content)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $stats = $content->getVideoInteractionStats();
            
            // Add detailed breakdown by interaction
            $interactions = $content->videoInteractions()->with('responses')->get();
            $interactionStats = $interactions->map(function ($interaction) {
                return [
                    'id' => $interaction->id,
                    'type' => $interaction->type,
                    'timestamp' => $interaction->timestamp,
                    'title' => $interaction->title,
                    'total_responses' => $interaction->getTotalResponsesCount(),
                    'correct_responses' => $interaction->getCorrectResponsesCount(),
                    'success_rate' => $interaction->getSuccessRate()
                ];
            });

            return response()->json([
                'stats' => $stats,
                'interaction_details' => $interactionStats
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Content not found'], 404);
        }
    }

    /**
     * Evaluate quiz response
     */
    private function evaluateQuizResponse(VideoInteraction $interaction, array $responseData): array
    {
        $quizData = $interaction->data;
        
        if (!isset($quizData['options']) || !isset($quizData['correct_answer'])) {
            return ['is_correct' => false, 'feedback' => 'Invalid quiz configuration'];
        }

        $selectedOption = $responseData['selected_option'] ?? null;
        $correctAnswer = $quizData['correct_answer'];
        
        $isCorrect = $selectedOption === $correctAnswer;
        
        $feedback = $isCorrect 
            ? ($quizData['correct_feedback'] ?? 'Correct!')
            : ($quizData['incorrect_feedback'] ?? 'Incorrect. The correct answer was: ' . 
               ($quizData['options'][$correctAnswer]['text'] ?? 'N/A'));

        return [
            'is_correct' => $isCorrect,
            'feedback' => $feedback
        ];
    }

    /**
     * Evaluate reflection response
     */
    private function evaluateReflectionResponse(VideoInteraction $interaction, array $responseData): array
    {
        $reflectionData = $interaction->data;
        
        // Check if it's multiple choice reflection
        if (isset($reflectionData['reflection_type']) && $reflectionData['reflection_type'] === 'multiple_choice') {
            if (!isset($reflectionData['reflection_options']) || !isset($reflectionData['reflection_correct_answer'])) {
                return ['is_correct' => null, 'feedback' => $reflectionData['reflection_general_feedback'] ?? 'Thank you for your reflection'];
            }

            $selectedOption = $responseData['selected_option'] ?? null;
            $correctAnswer = $reflectionData['reflection_correct_answer'];
            
            // Check if has scoring enabled
            if (isset($reflectionData['reflection_has_scoring']) && $reflectionData['reflection_has_scoring']) {
                $isCorrect = $selectedOption === $correctAnswer;
                $feedback = $isCorrect 
                    ? ($reflectionData['reflection_correct_feedback'] ?? 'Great reflection!')
                    : ($reflectionData['reflection_general_feedback'] ?? 'Thank you for your reflection');
            } else {
                // No scoring, just give general feedback
                $isCorrect = null;
                $feedback = $reflectionData['reflection_general_feedback'] ?? 'Thank you for your reflection';
            }
        } else {
            // Text reflection - no scoring, just feedback
            $isCorrect = null;
            $feedback = $reflectionData['reflection_general_feedback'] ?? 'Thank you for your reflection';
        }

        return [
            'is_correct' => $isCorrect,
            'feedback' => $feedback
        ];
    }

    /**
     * Check if user has access to content
     */
    private function userHasAccessToContent(Content $content): bool
    {
        $user = Auth::user();
        
        // Check if user is enrolled in the course
        $course = $content->lesson->course;
        
        return $course->participants()->where('user_id', $user->id)->exists() ||
               $course->instructors()->where('user_id', $user->id)->exists() ||
               $course->eventOrganizers()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user is instructor for content
     */
    private function userIsInstructorForContent(Content $content): bool
    {
        $user = Auth::user();
        $course = $content->lesson->course;
        
        return $course->instructors()->where('user_id', $user->id)->exists() ||
               $course->eventOrganizers()->where('user_id', $user->id)->exists();
    }
}
