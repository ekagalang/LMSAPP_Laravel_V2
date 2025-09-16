<?php

namespace App\Http\Controllers;

use App\Models\AudioLesson;
use App\Models\AudioExercise;
use App\Models\UserAudioProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AudioLearningController extends Controller
{
    public function index(Request $request)
    {
        $difficulty = $request->get('difficulty', 'all');

        $query = AudioLesson::active()->orderBy('sort_order');

        if ($difficulty !== 'all') {
            $query->byDifficulty($difficulty);
        }

        $lessons = $query->with(['exercises' => function($q) {
            $q->active()->orderBy('sort_order');
        }])->get();

        // Get user progress for all lessons
        $userProgress = [];
        if (Auth::check()) {
            $progressRecords = UserAudioProgress::where('user_id', Auth::id())
                ->whereIn('audio_lesson_id', $lessons->pluck('id'))
                ->get()
                ->groupBy('audio_lesson_id');

            foreach ($progressRecords as $lessonId => $progresses) {
                $userProgress[$lessonId] = [
                    'completed' => $progresses->where('completed', true)->count(),
                    'total' => $progresses->count(),
                    'score' => $progresses->sum('score'),
                    'max_score' => $progresses->sum('max_score')
                ];
            }
        }

        return view('audio-learning.index', compact('lessons', 'userProgress', 'difficulty'));
    }

    public function lesson($id)
    {
        $lesson = AudioLesson::active()
            ->with(['exercises' => function($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->findOrFail($id);

        $userProgress = null;
        if (Auth::check()) {
            $userProgress = UserAudioProgress::firstOrCreate([
                'user_id' => Auth::id(),
                'audio_lesson_id' => $lesson->id,
                'audio_exercise_id' => null
            ], [
                'started_at' => now(),
                'max_score' => $lesson->exercises->sum('points')
            ]);
        }

        return view('audio-learning.lesson', compact('lesson', 'userProgress'));
    }

    public function exercise($lessonId, $exerciseId)
    {
        $lesson = AudioLesson::active()->findOrFail($lessonId);
        $exercise = AudioExercise::active()
            ->where('audio_lesson_id', $lessonId)
            ->findOrFail($exerciseId);

        $userProgress = null;
        if (Auth::check()) {
            $userProgress = UserAudioProgress::firstOrCreate([
                'user_id' => Auth::id(),
                'audio_lesson_id' => $lesson->id,
                'audio_exercise_id' => $exercise->id
            ], [
                'started_at' => now(),
                'max_score' => $exercise->points
            ]);
        }

        return view('audio-learning.exercise', compact('lesson', 'exercise', 'userProgress'));
    }

    public function updateProgress(Request $request, $lessonId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'position' => 'required|integer|min:0',
            'exercise_id' => 'nullable|integer|exists:audio_exercises,id'
        ]);

        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId,
            'audio_exercise_id' => $validated['exercise_id'] ?? null
        ])->first();

        if ($progress) {
            $progress->updatePosition($validated['position']);
        }

        return response()->json(['success' => true]);
    }

    public function submitAnswer(Request $request, $lessonId, $exerciseId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'answer' => 'required',
            'type' => 'required|in:text,speech,multiple_choice'
        ]);

        $exercise = AudioExercise::findOrFail($exerciseId);
        $isCorrect = $exercise->checkAnswer($validated['answer']);
        $points = $isCorrect ? $exercise->points : 0;

        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId,
            'audio_exercise_id' => $exerciseId
        ])->first();

        if ($progress) {
            $progress->recordAnswer($exerciseId, $validated['answer'], $isCorrect, $points);

            if ($isCorrect) {
                $progress->markCompleted($points);
            }
        }

        return response()->json([
            'success' => true,
            'correct' => $isCorrect,
            'points' => $points,
            'correct_answer' => $isCorrect ? null : $exercise->correct_answers[0] ?? null,
            'explanation' => $exercise->explanation ?? null
        ]);
    }

    public function submitSpeech(Request $request, $lessonId, $exerciseId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'transcript' => 'required|string',
            'confidence' => 'nullable|numeric|min:0|max:1'
        ]);

        $exercise = AudioExercise::findOrFail($exerciseId);
        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId,
            'audio_exercise_id' => $exerciseId
        ])->first();

        if ($progress) {
            $progress->recordSpeechAttempt(
                $exerciseId,
                $validated['transcript'],
                $validated['confidence'] ?? 0
            );
        }

        $isCorrect = $exercise->checkAnswer($validated['transcript']);
        $points = $isCorrect ? $exercise->points : 0;

        if ($isCorrect && $progress) {
            $progress->recordAnswer($exerciseId, $validated['transcript'], true, $points);
            $progress->markCompleted($points);
        }

        return response()->json([
            'success' => true,
            'correct' => $isCorrect,
            'points' => $points,
            'confidence' => $validated['confidence'] ?? 0,
            'transcript' => $validated['transcript']
        ]);
    }

    public function getProgress($lessonId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId
        ])->get();

        $totalScore = $progress->sum('score');
        $maxScore = $progress->sum('max_score');
        $completed = $progress->where('completed', true)->count();
        $total = $progress->count();

        return response()->json([
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
            'completed_exercises' => $completed,
            'total_exercises' => $total,
            'progress_percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
        ]);
    }

    /**
     * Show the form for creating a new audio lesson (Admin only)
     */
    public function create()
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        return view('audio-learning.create');
    }

    /**
     * Store a newly created audio lesson (Admin only)
     */
    public function store(Request $request)
    {
        // Increase memory and upload limits for large file uploads
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '300M');
        ini_set('upload_max_filesize', '200M');
        ini_set('max_execution_time', 300);

        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        // Custom validation rules based on content type
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:audio,video,mixed',
            'transcript' => 'nullable|string',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'metadata' => 'nullable|array',
            'available_for_courses' => 'boolean',

            // Exercise data
            'exercises' => 'nullable|array',
            'exercises.*.question_text' => 'required_with:exercises|string',
            'exercises.*.type' => 'required_with:exercises|in:multiple_choice,fill_blank,speech_response,comprehension',
            'exercises.*.options' => 'nullable|array', // Add options validation
            'exercises.*.correct_answers' => 'required_with:exercises|array',
            'exercises.*.points' => 'required_with:exercises|integer|min:1|max:100',
            'exercises.*.explanation' => 'nullable|string',
            'exercises.*.image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240', // 10MB max
            'exercises.*.audio_file' => 'nullable|file|mimes:mp3,wav,m4a,aac|max:20480', // 20MB max
            'exercises.*.document_file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
        ];

        // Add conditional file validation based on content type
        $contentType = $request->input('content_type');

        // Debug logging
        Log::info('AudioLearning Store - Content Type: ' . $contentType);
        Log::info('AudioLearning Store - Has audio file: ' . ($request->hasFile('audio_file') ? 'Yes' : 'No'));
        Log::info('AudioLearning Store - Has video file: ' . ($request->hasFile('video_file') ? 'Yes' : 'No'));

        if ($contentType === 'audio') {
            $rules['audio_file'] = 'required|file|mimes:mp3,wav,m4a,aac|max:51200'; // 50MB max
            $rules['video_file'] = 'nullable|file|max:204800'; // Optional for audio type, relax validation
        } elseif ($contentType === 'video') {
            $rules['audio_file'] = 'nullable|file|mimes:mp3,wav,m4a,aac|max:51200'; // Optional for video type
            $rules['video_file'] = 'required|file|max:204800'; // 200MB max, relax mime validation for now
        } elseif ($contentType === 'mixed') {
            $rules['audio_file'] = 'nullable|file|mimes:mp3,wav,m4a,aac|max:51200'; // At least one required
            $rules['video_file'] = 'nullable|file|max:204800'; // At least one required, relax validation
        }

        // Debug validation rules
        Log::info('AudioLearning Store - Validation Rules: ', $rules);

        $validated = $request->validate($rules);

        // For mixed content type, ensure at least one file is provided
        if ($contentType === 'mixed' && !$request->hasFile('audio_file') && !$request->hasFile('video_file')) {
            return back()->withErrors(['content_type' => 'For mixed content, at least one audio or video file is required.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Handle file uploads
            $audioPath = null;
            $videoPath = null;
            $duration = null;
            $videoMetadata = [];

            // Handle audio file upload
            if ($request->hasFile('audio_file')) {
                $audioFile = $request->file('audio_file');
                $fileName = time() . '_aud_' . $audioFile->getClientOriginalName();
                $audioPath = $audioFile->storeAs('microlearning/audio', $fileName, 'public');

                // Get audio duration if possible
                try {
                    $fullPath = storage_path('app/public/' . $audioPath);
                    if (function_exists('getid3_lib') || class_exists('getID3')) {
                        $getID3 = new \getID3;
                        $fileInfo = $getID3->analyze($fullPath);
                        $duration = $fileInfo['playtime_seconds'] ?? null;
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not get audio duration: ' . $e->getMessage());
                }
            }

            // Handle video file upload
            if ($request->hasFile('video_file')) {
                Log::info('AudioLearning Store - Processing video file upload');
                $videoFile = $request->file('video_file');
                Log::info('AudioLearning Store - Video file info: ', [
                    'name' => $videoFile->getClientOriginalName(),
                    'size' => $videoFile->getSize(),
                    'mime' => $videoFile->getMimeType()
                ]);

                $fileName = time() . '_vid_' . $videoFile->getClientOriginalName();
                $videoPath = $videoFile->storeAs('microlearning/videos', $fileName, 'public');
                Log::info('AudioLearning Store - Video stored at: ' . $videoPath);

                // Store video metadata
                $videoMetadata = [
                    'original_name' => $videoFile->getClientOriginalName(),
                    'size' => $videoFile->getSize(),
                    'mime_type' => $videoFile->getMimeType()
                ];

                // Try to get video duration
                try {
                    $fullPath = storage_path('app/public/' . $videoPath);
                    if (function_exists('getid3_lib') || class_exists('getID3')) {
                        $getID3 = new \getID3;
                        $fileInfo = $getID3->analyze($fullPath);
                        $duration = $fileInfo['playtime_seconds'] ?? null;

                        if (isset($fileInfo['video']['resolution_x']) && isset($fileInfo['video']['resolution_y'])) {
                            $videoMetadata['resolution'] = [
                                'width' => $fileInfo['video']['resolution_x'],
                                'height' => $fileInfo['video']['resolution_y']
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not get video metadata: ' . $e->getMessage());
                }
            }

            // Create microlearning lesson
            Log::info('AudioLearning Store - Creating lesson with data: ', [
                'title' => $validated['title'],
                'content_type' => $validated['content_type'],
                'audio_path' => $audioPath,
                'video_path' => $videoPath,
                'video_metadata' => $videoMetadata
            ]);

            $audioLesson = AudioLesson::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'content_type' => $validated['content_type'],
                'audio_file_path' => $audioPath,
                'video_file_path' => $videoPath,
                'duration_seconds' => $duration,
                'difficulty_level' => $validated['difficulty_level'],
                'transcript' => $validated['transcript'],
                'metadata' => $validated['metadata'] ?? [],
                'video_metadata' => $videoMetadata,
                'is_active' => true,
                'sort_order' => AudioLesson::max('sort_order') + 1,
                'available_for_courses' => $validated['available_for_courses'] ?? false,
            ]);

            Log::info('AudioLearning Store - Lesson created with ID: ' . $audioLesson->id);

            // Create exercises if provided
            if (!empty($validated['exercises'])) {
                foreach ($validated['exercises'] as $index => $exerciseData) {
                    // Handle file uploads for exercise
                    $exerciseFiles = [
                        'image_file_path' => null,
                        'audio_file_path' => null,
                        'document_file_path' => null,
                        'file_metadata' => []
                    ];

                    // Handle image file upload
                    if (isset($exerciseData['image_file']) && $exerciseData['image_file']) {
                        $imageFile = $exerciseData['image_file'];
                        $fileName = time() . '_ex' . ($index + 1) . '_img_' . $imageFile->getClientOriginalName();
                        $exerciseFiles['image_file_path'] = $imageFile->storeAs('microlearning/exercises/images', $fileName, 'public');
                        $exerciseFiles['file_metadata']['image'] = [
                            'original_name' => $imageFile->getClientOriginalName(),
                            'size' => $imageFile->getSize(),
                            'mime_type' => $imageFile->getMimeType()
                        ];
                    }

                    // Handle audio file upload
                    if (isset($exerciseData['audio_file']) && $exerciseData['audio_file']) {
                        $audioFile = $exerciseData['audio_file'];
                        $fileName = time() . '_ex' . ($index + 1) . '_aud_' . $audioFile->getClientOriginalName();
                        $exerciseFiles['audio_file_path'] = $audioFile->storeAs('microlearning/exercises/audio', $fileName, 'public');
                        $exerciseFiles['file_metadata']['audio'] = [
                            'original_name' => $audioFile->getClientOriginalName(),
                            'size' => $audioFile->getSize(),
                            'mime_type' => $audioFile->getMimeType()
                        ];
                    }

                    // Handle document file upload
                    if (isset($exerciseData['document_file']) && $exerciseData['document_file']) {
                        $docFile = $exerciseData['document_file'];
                        $fileName = time() . '_ex' . ($index + 1) . '_doc_' . $docFile->getClientOriginalName();
                        $exerciseFiles['document_file_path'] = $docFile->storeAs('microlearning/exercises/documents', $fileName, 'public');
                        $exerciseFiles['file_metadata']['document'] = [
                            'original_name' => $docFile->getClientOriginalName(),
                            'size' => $docFile->getSize(),
                            'mime_type' => $docFile->getMimeType()
                        ];
                    }

                    AudioExercise::create([
                        'audio_lesson_id' => $audioLesson->id,
                        'title' => 'Exercise ' . ($index + 1),
                        'question' => $exerciseData['question_text'], // Map to correct field name
                        'exercise_type' => $exerciseData['type'], // Map to correct field name
                        'options' => $exerciseData['options'] ?? null, // Add options field
                        'correct_answers' => $exerciseData['correct_answers'],
                        'points' => $exerciseData['points'],
                        'explanation' => $exerciseData['explanation'] ?? null,
                        'image_file_path' => $exerciseFiles['image_file_path'],
                        'audio_file_path' => $exerciseFiles['audio_file_path'],
                        'document_file_path' => $exerciseFiles['document_file_path'],
                        'file_metadata' => $exerciseFiles['file_metadata'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            Log::info('Microlearning Created', [
                'audio_lesson_id' => $audioLesson->id,
                'title' => $audioLesson->title,
                'exercises_count' => count($validated['exercises'] ?? []),
                'created_by' => Auth::id()
            ]);

            return redirect()->route('audio-learning.index')
                ->with('success', 'Microlearning berhasil dibuat! 🎉');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Microlearning Creation Failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat microlearning: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an audio lesson (Admin only)
     */
    public function edit($id)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $audioLesson = AudioLesson::with('exercises')->findOrFail($id);
        return view('audio-learning.edit', compact('audioLesson'));
    }

    /**
     * Update a microlearning lesson (Admin only)
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $audioLesson = AudioLesson::with('exercises')->findOrFail($id);

        // Base validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:audio,video,mixed',
            'transcript' => 'nullable|string',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'metadata' => 'nullable|array',
            'available_for_courses' => 'boolean',
        ];

        // Add conditional file validation for update
        $contentType = $request->input('content_type');

        if ($contentType === 'video') {
            $rules['video_file'] = 'nullable|file|max:204800'; // Relax validation for update
        } else {
            $rules['video_file'] = 'nullable|file|max:204800';
        }

        if ($contentType === 'audio') {
            $rules['audio_file'] = 'nullable|file|mimes:mp3,wav,m4a,aac|max:51200';
        } else {
            $rules['audio_file'] = 'nullable|file|mimes:mp3,wav,m4a,aac|max:51200';
        }

        // Add exercise validation rules
        $rules['exercises'] = 'nullable|array';
        $rules['exercises.*.id'] = 'nullable|integer|exists:audio_exercises,id';
        $rules['exercises.*.question_text'] = 'required_with:exercises|string';
        $rules['exercises.*.type'] = 'required_with:exercises|in:multiple_choice,fill_blank,speech_response,comprehension';
        $rules['exercises.*.options'] = 'nullable|array';
        $rules['exercises.*.correct_answers'] = 'required_with:exercises|array';
        $rules['exercises.*.points'] = 'required_with:exercises|integer|min:1|max:100';
        $rules['exercises.*.explanation'] = 'nullable|string';
        $rules['exercises.*.image_file'] = 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240';
        $rules['exercises.*.audio_file'] = 'nullable|file|mimes:mp3,wav,m4a,aac|max:20480';
        $rules['exercises.*.document_file'] = 'nullable|file|mimes:pdf,doc,docx,txt|max:10240';

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $duration = $audioLesson->duration_seconds;
            $videoMetadata = $audioLesson->video_metadata ?? [];

            // Handle audio file upload
            if ($request->hasFile('audio_file')) {
                // Delete old audio file
                if ($audioLesson->audio_file_path) {
                    Storage::disk('public')->delete($audioLesson->audio_file_path);
                }

                $audioFile = $request->file('audio_file');
                $fileName = time() . '_aud_' . $audioFile->getClientOriginalName();
                $validated['audio_file_path'] = $audioFile->storeAs('microlearning/audio', $fileName, 'public');

                // Get audio duration if possible
                try {
                    $fullPath = storage_path('app/public/' . $validated['audio_file_path']);
                    if (function_exists('getid3_lib') || class_exists('getID3')) {
                        $getID3 = new \getID3;
                        $fileInfo = $getID3->analyze($fullPath);
                        $duration = $fileInfo['playtime_seconds'] ?? $duration;
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not get audio duration: ' . $e->getMessage());
                }
            }

            // Handle video file upload
            if ($request->hasFile('video_file')) {
                // Delete old video file
                if ($audioLesson->video_file_path) {
                    Storage::disk('public')->delete($audioLesson->video_file_path);
                }

                $videoFile = $request->file('video_file');
                $fileName = time() . '_vid_' . $videoFile->getClientOriginalName();
                $validated['video_file_path'] = $videoFile->storeAs('microlearning/videos', $fileName, 'public');

                // Store video metadata
                $videoMetadata = [
                    'original_name' => $videoFile->getClientOriginalName(),
                    'size' => $videoFile->getSize(),
                    'mime_type' => $videoFile->getMimeType()
                ];

                // Try to get video duration and resolution
                try {
                    $fullPath = storage_path('app/public/' . $validated['video_file_path']);
                    if (function_exists('getid3_lib') || class_exists('getID3')) {
                        $getID3 = new \getID3;
                        $fileInfo = $getID3->analyze($fullPath);
                        $duration = $fileInfo['playtime_seconds'] ?? $duration;

                        if (isset($fileInfo['video']['resolution_x']) && isset($fileInfo['video']['resolution_y'])) {
                            $videoMetadata['resolution'] = [
                                'width' => $fileInfo['video']['resolution_x'],
                                'height' => $fileInfo['video']['resolution_y']
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not get video metadata: ' . $e->getMessage());
                }
            }

            // Update lesson data
            $updateData = array_filter([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'content_type' => $validated['content_type'],
                'transcript' => $validated['transcript'],
                'difficulty_level' => $validated['difficulty_level'],
                'metadata' => $validated['metadata'] ?? [],
                'available_for_courses' => $validated['available_for_courses'] ?? false,
                'duration_seconds' => $duration,
                'video_metadata' => $videoMetadata,
            ]);

            // Add file paths if they were uploaded
            if (isset($validated['audio_file_path'])) {
                $updateData['audio_file_path'] = $validated['audio_file_path'];
            }
            if (isset($validated['video_file_path'])) {
                $updateData['video_file_path'] = $validated['video_file_path'];
            }

            $audioLesson->update($updateData);

            // Handle exercises update
            if (!empty($validated['exercises'])) {
                $existingExerciseIds = [];

                foreach ($validated['exercises'] as $index => $exerciseData) {
                    $exerciseFiles = [
                        'image_file_path' => null,
                        'audio_file_path' => null,
                        'document_file_path' => null,
                        'file_metadata' => []
                    ];

                    $exercise = null;

                    // Check if this is an existing exercise
                    if (!empty($exerciseData['id'])) {
                        $exercise = AudioExercise::where('id', $exerciseData['id'])
                            ->where('audio_lesson_id', $audioLesson->id)
                            ->first();

                        if ($exercise) {
                            $existingExerciseIds[] = $exercise->id;
                            // Keep existing file paths unless new files are uploaded
                            $exerciseFiles['image_file_path'] = $exercise->image_file_path;
                            $exerciseFiles['audio_file_path'] = $exercise->audio_file_path;
                            $exerciseFiles['document_file_path'] = $exercise->document_file_path;
                            $exerciseFiles['file_metadata'] = $exercise->file_metadata ?? [];
                        }
                    }

                    // Handle file uploads for exercise
                    if (isset($exerciseData['image_file']) && $exerciseData['image_file']) {
                        // Delete old image if exists
                        if ($exercise && $exercise->image_file_path) {
                            Storage::disk('public')->delete($exercise->image_file_path);
                        }

                        $imageFile = $exerciseData['image_file'];
                        $fileName = time() . '_ex' . ($index + 1) . '_img_' . $imageFile->getClientOriginalName();
                        $exerciseFiles['image_file_path'] = $imageFile->storeAs('microlearning/exercises/images', $fileName, 'public');
                        $exerciseFiles['file_metadata']['image'] = [
                            'original_name' => $imageFile->getClientOriginalName(),
                            'size' => $imageFile->getSize(),
                            'mime_type' => $imageFile->getMimeType()
                        ];
                    }

                    if (isset($exerciseData['audio_file']) && $exerciseData['audio_file']) {
                        // Delete old audio if exists
                        if ($exercise && $exercise->audio_file_path) {
                            Storage::disk('public')->delete($exercise->audio_file_path);
                        }

                        $audioFile = $exerciseData['audio_file'];
                        $fileName = time() . '_ex' . ($index + 1) . '_aud_' . $audioFile->getClientOriginalName();
                        $exerciseFiles['audio_file_path'] = $audioFile->storeAs('microlearning/exercises/audio', $fileName, 'public');
                        $exerciseFiles['file_metadata']['audio'] = [
                            'original_name' => $audioFile->getClientOriginalName(),
                            'size' => $audioFile->getSize(),
                            'mime_type' => $audioFile->getMimeType()
                        ];
                    }

                    if (isset($exerciseData['document_file']) && $exerciseData['document_file']) {
                        // Delete old document if exists
                        if ($exercise && $exercise->document_file_path) {
                            Storage::disk('public')->delete($exercise->document_file_path);
                        }

                        $docFile = $exerciseData['document_file'];
                        $fileName = time() . '_ex' . ($index + 1) . '_doc_' . $docFile->getClientOriginalName();
                        $exerciseFiles['document_file_path'] = $docFile->storeAs('microlearning/exercises/documents', $fileName, 'public');
                        $exerciseFiles['file_metadata']['document'] = [
                            'original_name' => $docFile->getClientOriginalName(),
                            'size' => $docFile->getSize(),
                            'mime_type' => $docFile->getMimeType()
                        ];
                    }

                    $exerciseUpdateData = [
                        'title' => 'Exercise ' . ($index + 1),
                        'question' => $exerciseData['question_text'],
                        'exercise_type' => $exerciseData['type'],
                        'options' => $exerciseData['options'] ?? null,
                        'correct_answers' => $exerciseData['correct_answers'],
                        'points' => $exerciseData['points'],
                        'explanation' => $exerciseData['explanation'] ?? null,
                        'image_file_path' => $exerciseFiles['image_file_path'],
                        'audio_file_path' => $exerciseFiles['audio_file_path'],
                        'document_file_path' => $exerciseFiles['document_file_path'],
                        'file_metadata' => $exerciseFiles['file_metadata'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ];

                    if ($exercise) {
                        // Update existing exercise
                        $exercise->update($exerciseUpdateData);
                    } else {
                        // Create new exercise
                        $exerciseUpdateData['audio_lesson_id'] = $audioLesson->id;
                        AudioExercise::create($exerciseUpdateData);
                    }
                }

                // Delete exercises that were removed
                AudioExercise::where('audio_lesson_id', $audioLesson->id)
                    ->whereNotIn('id', $existingExerciseIds)
                    ->get()
                    ->each(function($exercise) {
                        // Delete associated files
                        if ($exercise->image_file_path) {
                            Storage::disk('public')->delete($exercise->image_file_path);
                        }
                        if ($exercise->audio_file_path) {
                            Storage::disk('public')->delete($exercise->audio_file_path);
                        }
                        if ($exercise->document_file_path) {
                            Storage::disk('public')->delete($exercise->document_file_path);
                        }
                        $exercise->delete();
                    });
            }

            DB::commit();

            Log::info('Microlearning Updated', [
                'audio_lesson_id' => $audioLesson->id,
                'title' => $audioLesson->title,
                'exercises_count' => count($validated['exercises'] ?? []),
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('audio-learning.index')
                ->with('success', 'Microlearning berhasil diperbarui! ✅');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Microlearning Update Failed', [
                'audio_lesson_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal memperbarui microlearning: ' . $e->getMessage());
        }
    }

    /**
     * Delete a microlearning lesson (Admin only)
     */
    public function destroy($id)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $audioLesson = AudioLesson::with('exercises')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete exercise files first
            foreach ($audioLesson->exercises as $exercise) {
                if ($exercise->image_file_path) {
                    Storage::disk('public')->delete($exercise->image_file_path);
                }
                if ($exercise->audio_file_path) {
                    Storage::disk('public')->delete($exercise->audio_file_path);
                }
                if ($exercise->document_file_path) {
                    Storage::disk('public')->delete($exercise->document_file_path);
                }
            }

            // Delete lesson media files
            if ($audioLesson->audio_file_path) {
                Storage::disk('public')->delete($audioLesson->audio_file_path);
            }
            if ($audioLesson->video_file_path) {
                Storage::disk('public')->delete($audioLesson->video_file_path);
            }

            // Delete the lesson (exercises will be deleted via foreign key constraint)
            $audioLesson->delete();

            DB::commit();

            Log::info('Microlearning Deleted', [
                'audio_lesson_id' => $id,
                'title' => $audioLesson->title,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('audio-learning.index')
                ->with('success', 'Microlearning berhasil dihapus! 🗑️');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Microlearning Delete Failed', [
                'audio_lesson_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Gagal menghapus microlearning: ' . $e->getMessage());
        }
    }
}
