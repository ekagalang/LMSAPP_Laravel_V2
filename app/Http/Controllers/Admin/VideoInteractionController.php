<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoInteraction;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VideoInteractionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage content');
    }

    /**
     * Display video interactions for a specific content
     */
    public function index(Content $content)
    {
        // Check if user has access to this content
        $this->authorize('update', $content);
        
        if ($content->type !== 'video') {
            return redirect()->back()->with('error', 'Video interactions can only be added to video content.');
        }

        $interactions = $content->getAllVideoInteractions()
            ->with('responses')
            ->orderBy('timestamp')
            ->get();

        return view('admin.video-interactions.index', compact('content', 'interactions'));
    }

    /**
     * Show the form for creating a new interaction
     */
    public function create(Content $content)
    {
        $this->authorize('update', $content);

        if ($content->type !== 'video') {
            return redirect()->back()->with('error', 'Video interactions can only be added to video content.');
        }

        return view('admin.video-interactions.create', compact('content'));
    }

    /**
     * Store a newly created interaction
     */
    public function store(Request $request, Content $content)
    {
        \Log::info('=== VideoInteraction Store Method Called ===', [
            'content_id' => $content->id,
            'method' => $request->method(),
            'url' => $request->url(),
            'user_id' => auth()->id() ?? 'guest'
        ]);

        try {
            // Temporarily disable authorization check for debugging
            // $this->authorize('update', $content);
            \Log::info('Authorization bypassed for debugging', ['content_id' => $content->id]);
        } catch (\Exception $e) {
            \Log::error('Authorization failed', ['error' => $e->getMessage(), 'content_id' => $content->id]);
            return redirect()->back()->withErrors(['error' => 'Unauthorized to manage this content.']);
        }

        // Debug: log all request data
        \Log::info('VideoInteraction Store Request Data', [
            'all_data' => $request->all(),
            'content_id' => $content->id
        ]);

        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(['quiz', 'reflection', 'annotation', 'hotspot', 'overlay', 'pause'])],
            'timestamp' => 'required|numeric|min:0',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'data' => 'nullable|array',
            'position' => 'nullable|array',
            'is_active' => 'nullable|string', // Changed from boolean to handle checkbox
            'order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            \Log::error('VideoInteraction validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'content_id' => $content->id,
            'type' => $request->type,
            'timestamp' => $request->timestamp,
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'order' => $request->order ?? $this->getNextOrder($content),
            'data' => []
        ];

        // Process specific data based on interaction type
        if ($request->type === 'quiz') {
            $data['data'] = $this->processQuizData($request);
        } elseif ($request->type === 'reflection') {
            $data['data'] = $this->processReflectionData($request);
        } elseif ($request->type === 'hotspot') {
            if ($request->has('position')) {
                $data['data'] = ['position' => $request->position];
            }
        } elseif ($request->type === 'overlay') {
            $data['data'] = [
                'overlay_content' => $request->input('overlay_content', ''),
                'duration' => $request->input('duration', 5)
            ];
        } elseif ($request->type === 'annotation') {
            $data['data'] = [
                'annotation_text' => $request->input('annotation_text', $request->description)
            ];
        }

        try {
            \Log::info('Attempting to create video interaction', ['data' => $data]);
            $interaction = VideoInteraction::create($data);
            \Log::info('Video interaction created successfully', ['id' => $interaction->id, 'data' => $data]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error creating video interaction', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'data' => $data
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Database error: ' . $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to create video interaction', ['error' => $e->getMessage(), 'data' => $data, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to save interaction: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('admin.video-interactions.index', $content)
            ->with('success', 'Video interaction created successfully.');
    }

    /**
     * Show the form for editing an interaction
     */
    public function edit(Content $content, VideoInteraction $videoInteraction)
    {
        $this->authorize('update', $content);

        if ($videoInteraction->content_id !== $content->id) {
            abort(404);
        }

        return view('admin.video-interactions.edit', compact('content', 'videoInteraction'));
    }

    /**
     * Update an interaction
     */
    public function update(Request $request, Content $content, VideoInteraction $videoInteraction)
    {
        // Temporarily disable authorization check for debugging
        // $this->authorize('update', $content);
        \Log::info('Update authorization bypassed for debugging', ['content_id' => $content->id]);

        if ($videoInteraction->content_id !== $content->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(['quiz', 'reflection', 'annotation', 'hotspot', 'overlay', 'pause'])],
            'timestamp' => 'required|numeric|min:0',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'data' => 'nullable|array',
            'position' => 'nullable|array',
            'is_active' => 'nullable|string', // Changed from boolean to handle checkbox
            'order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'type' => $request->type,
            'timestamp' => $request->timestamp,
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'data' => []
        ];

        // Process specific data based on interaction type
        if ($request->type === 'quiz') {
            $data['data'] = $this->processQuizData($request);
        } elseif ($request->type === 'reflection') {
            $data['data'] = $this->processReflectionData($request);
        } elseif ($request->type === 'hotspot') {
            if ($request->has('position')) {
                $data['data'] = ['position' => $request->position];
            }
        } elseif ($request->type === 'overlay') {
            $data['data'] = [
                'overlay_content' => $request->input('overlay_content', ''),
                'duration' => $request->input('duration', 5)
            ];
        } elseif ($request->type === 'annotation') {
            $data['data'] = [
                'annotation_text' => $request->input('annotation_text', $request->description)
            ];
        }

        try {
            \Log::info('Attempting to update video interaction', ['id' => $videoInteraction->id, 'data' => $data]);
            $videoInteraction->update($data);
            \Log::info('Video interaction updated successfully', ['id' => $videoInteraction->id, 'data' => $data]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error updating video interaction', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'data' => $data,
                'id' => $videoInteraction->id
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Database error: ' . $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to update video interaction', ['error' => $e->getMessage(), 'data' => $data, 'id' => $videoInteraction->id, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update interaction: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('admin.video-interactions.index', $content)
            ->with('success', 'Video interaction updated successfully.');
    }

    /**
     * Remove an interaction
     */
    public function destroy(Content $content, VideoInteraction $videoInteraction)
    {
        $this->authorize('update', $content);

        if ($videoInteraction->content_id !== $content->id) {
            abort(404);
        }

        $videoInteraction->delete();

        return redirect()->route('admin.video-interactions.index', $content)
            ->with('success', 'Video interaction deleted successfully.');
    }

    /**
     * Show responses for a specific interaction
     */
    public function responses(Request $request, Content $content, VideoInteraction $videoInteraction)
    {
        $this->authorize('update', $content);

        if ($videoInteraction->content_id !== $content->id) {
            abort(404);
        }

        // Build query for responses with user data
        $query = $videoInteraction->responses()->with('user');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Apply user filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Apply correctness filter (for quiz and scored reflection)
        if ($request->filled('correctness') && $request->correctness !== 'all') {
            if ($request->correctness === 'correct') {
                $query->where('is_correct', true);
            } elseif ($request->correctness === 'incorrect') {
                $query->where('is_correct', false);
            } elseif ($request->correctness === 'unscored') {
                $query->whereNull('is_correct');
            }
        }

        // Apply date filter
        if ($request->filled('date_from')) {
            $query->whereDate('answered_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('answered_at', '<=', $request->date_to);
        }

        // Order by latest responses
        $query->orderBy('answered_at', 'desc');

        $responses = $query->paginate(20)->withQueryString();

        // Get all users who have responded for filter dropdown
        $users = $videoInteraction->responses()
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->sortBy('name');

        // Calculate statistics
        $stats = [
            'total_responses' => $videoInteraction->responses()->count(),
            'unique_users' => $videoInteraction->responses()->distinct('user_id')->count(),
            'correct_responses' => $videoInteraction->responses()->where('is_correct', true)->count(),
            'incorrect_responses' => $videoInteraction->responses()->where('is_correct', false)->count(),
            'unscored_responses' => $videoInteraction->responses()->whereNull('is_correct')->count(),
        ];

        if ($stats['total_responses'] > 0) {
            $stats['success_rate'] = ($stats['correct_responses'] / $stats['total_responses']) * 100;
        } else {
            $stats['success_rate'] = 0;
        }

        return view('admin.video-interactions.responses', compact(
            'content', 
            'videoInteraction', 
            'responses', 
            'users', 
            'stats'
        ));
    }

    /**
     * Update interaction order via AJAX
     */
    public function updateOrder(Request $request, Content $content)
    {
        $this->authorize('update', $content);

        $validator = Validator::make($request->all(), [
            'interactions' => 'required|array',
            'interactions.*.id' => 'required|exists:video_interactions,id',
            'interactions.*.order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->interactions as $interactionData) {
            VideoInteraction::where('id', $interactionData['id'])
                ->where('content_id', $content->id)
                ->update(['order' => $interactionData['order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Process quiz-specific data
     */
    private function processQuizData(Request $request): array
    {
        $quizData = [
            'options' => [],
            'correct_answer' => (int) $request->input('correct_answer', 0),
            'correct_feedback' => $request->input('correct_feedback', 'Correct!'),
            'incorrect_feedback' => $request->input('incorrect_feedback', 'Incorrect answer.')
        ];

        // Process options
        $options = $request->input('options', []);
        \Log::info('Processing quiz options', ['options' => $options]);
        
        foreach ($options as $index => $option) {
            if (!empty($option['text'])) {
                $quizData['options'][] = [
                    'text' => trim($option['text']),
                    'index' => $index
                ];
            }
        }

        \Log::info('Processed quiz data', ['quiz_data' => $quizData]);
        return $quizData;
    }

    /**
     * Process reflection-specific data
     */
    private function processReflectionData(Request $request): array
    {
        $reflectionData = [
            'reflection_type' => $request->input('reflection_type', 'text'),
            'reflection_question' => $request->input('reflection_question', ''),
            'reflection_is_required' => $request->boolean('reflection_is_required'),
            'reflection_has_scoring' => $request->boolean('reflection_has_scoring'),
            'reflection_general_feedback' => $request->input('reflection_general_feedback') ?: 'Thank you for your reflection'
        ];

        // If it's multiple choice reflection, process the options
        if ($request->input('reflection_type') === 'multiple_choice') {
            $reflectionData['reflection_options'] = [];
            $reflectionData['reflection_correct_answer'] = (int) $request->input('reflection_correct_answer', 0);
            $reflectionData['reflection_correct_feedback'] = $request->input('reflection_correct_feedback', 'Great reflection!');
            $reflectionData['reflection_general_feedback'] = $request->input('reflection_general_feedback', 'Thank you for your reflection');

            // Process options
            $options = $request->input('reflection_options', []);
            \Log::info('Processing reflection options', ['options' => $options]);
            
            foreach ($options as $index => $option) {
                if (!empty($option['text'])) {
                    $reflectionData['reflection_options'][] = [
                        'text' => trim($option['text']),
                        'index' => $index
                    ];
                }
            }
        }

        \Log::info('Processed reflection data', ['reflection_data' => $reflectionData]);
        return $reflectionData;
    }

    /**
     * Get next order number for interactions
     */
    private function getNextOrder(Content $content): int
    {
        $maxOrder = $content->getAllVideoInteractions()->max('order') ?? 0;
        return $maxOrder + 1;
    }
}
