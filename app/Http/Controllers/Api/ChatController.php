<?php
// app/Http/Controllers/Api/ChatController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Models\CoursePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ChatCreatedNotification;

class ChatController extends Controller
{
    /**
     * Display chat index page with unified layout
     */
    public function webIndex(Request $request)
    {
        // Get user's chats with latest messages
        $chats = Chat::whereHas('participants', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with([
                'participants:id,name',
                'lastMessage.user:id,name',
                'activeParticipants'
            ])
            ->withCount('activeParticipants')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Add display name and format data for each chat
        $chats->each(function ($chat) {
            $chat->display_name = $chat->getDisplayName();

            // Add unread count (you can implement this based on your needs)
            $chat->unread_count = 0; // Placeholder - implement based on your message read tracking
        });

        $chatNotificationCount = 0;
        try {
            $types = [\App\Notifications\ChatMessageNotification::class, \App\Notifications\ChatCreatedNotification::class];
            $chatNotificationCount = auth()->user()->unreadNotifications()->whereIn('type', $types)->count();
        } catch (\Throwable $e) {}

        return view('chat.index', compact('chats', 'chatNotificationCount'));
    }

    /**
     * Show specific chat (AJAX response for unified layout)
     */
    public function webShow(Request $request, Chat $chat)
    {
        Gate::authorize('view', $chat);

        // Load chat with participants and messages
        $chat->load([
            'participants:id,name',
            'activeParticipants',
            'messages' => function ($query) {
                $query->with('user:id,name')
                    ->orderBy('created_at', 'asc')
                    ->limit(50); // Load last 50 messages
            }
        ]);

        // Mark chat message notifications for this chat as read
        try {
            $user = $request->user();
            $user->unreadNotifications()
                ->where('type', \App\Notifications\ChatMessageNotification::class)
                ->whereJsonContains('data->chat_id', $chat->id)
                ->update(['read_at' => now()]);
        } catch (\Throwable $e) {}

        // If AJAX request, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'chat' => [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'display_name' => $chat->getDisplayName(),
                    'type' => $chat->type,
                    'participants_count' => $chat->activeParticipants->count(),
                    'participants' => $chat->activeParticipants->map(function ($participant) {
                        return [
                            'id' => $participant->id,
                            'name' => $participant->name
                        ];
                    })
                ],
                'messages' => $chat->messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'content' => $message->content,
                        'user_id' => $message->user_id,
                        'user' => [
                            'id' => $message->user->id,
                            'name' => $message->user->name
                        ],
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->created_at->format('H:i')
                    ];
                })
            ]);
        }

        // For non-AJAX requests, redirect to index (maintain backward compatibility)
        return redirect()->route('chat.index');
    }

    /**
     * Store a newly created chat
     */
    public function store(Request $request)
    {
        \Log::info('=== CHAT CREATION START ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Current user:', ['id' => auth()->id(), 'name' => auth()->user()->name]);

        try {
            // Validation
            \Log::info('Starting validation...');
            $request->validate([
                'type' => ['required', 'in:direct,group'],
                'participant_ids' => ['required', 'array', 'min:1'],
                'participant_ids.*' => ['exists:users,id'],
                'name' => ['nullable', 'string', 'max:255'],
                'course_class_id' => ['nullable', 'exists:course_classes,id']
            ]);
            \Log::info('Validation passed');

            // Authorization check
            \Log::info('Checking authorization...');
            if ($request->filled('course_class_id')) {
                \Log::info('Checking course-specific permission for class: ' . $request->course_class_id);

                // Debug course period
                $courseClass = \App\Models\CourseClass::find($request->course_class_id);
                if ($courseClass) {
                    \Log::info('Course class found:', [
                        'id' => $courseClass->id,
                        'name' => $courseClass->name,
                        'course_title' => $courseClass->course->title ?? 'No Course'
                    ]);
                } else {
                    \Log::error('Course class not found: ' . $request->course_class_id);
                }

                Gate::authorize('createForCourseClass', [Chat::class, $request->course_class_id]);
            } else {
                \Log::info('Checking general create permission');
                Gate::authorize('create', Chat::class);
            }
            \Log::info('Authorization passed');

            // Participant validation
            \Log::info('Validating participants...');
            foreach ($request->participant_ids as $participantId) {
                $targetUser = User::find($participantId);
                if (!$targetUser) {
                    \Log::error('Participant not found: ' . $participantId);
                    continue;
                }

                \Log::info('Checking if user can chat with: ' . $targetUser->name);
                $canChat = auth()->user()->canChatWith($targetUser, $request->course_class_id);
                \Log::info('Can chat result: ' . ($canChat ? 'YES' : 'NO'));

                if (!$canChat) {
                    \Log::warning('User cannot chat with participant: ' . $targetUser->name);
                    return response()->json([
                        'message' => "You cannot chat with {$targetUser->name} in this context.",
                        'errors' => ['participant_ids' => ["Invalid participant: {$targetUser->name}"]]
                    ], 422);
                }
            }
            \Log::info('Participant validation passed');

            DB::beginTransaction();
            \Log::info('Database transaction started');

            // Create chat
            $chatData = [
                'name' => $request->name,
                'type' => $request->type,
                'course_class_id' => $request->course_class_id,
                'created_by' => auth()->id(),
                'is_active' => true // Make sure this is set
            ];

            \Log::info('Creating chat with data:', $chatData);

            $chat = Chat::create($chatData);
            \Log::info('Chat created successfully with ID: ' . $chat->id);

            // Prepare participants
            $participants = collect($request->participant_ids)
                ->map(function ($id) {
                    return (int) $id; // Pastikan semua ID adalah integer
                })
                ->unique() // Remove duplicates dari input
                ->filter(); // Remove empty values

            // ✅ FIXED: Pastikan current user selalu included tanpa duplicate
            if (!$participants->contains(auth()->id())) {
                $participants->push(auth()->id());
            }

            \Log::info('Final participants after deduplication:', $participants->toArray());

            // ✅ IMPROVED: Add participants dengan duplicate check
            foreach ($participants->unique() as $userId) {
                \Log::info('Processing participant: ' . $userId);

                // Check if user is already a participant (untuk safety)
                $existingParticipant = $chat->participants()
                    ->where('user_id', $userId)
                    ->first();

                if ($existingParticipant) {
                    \Log::info('User ' . $userId . ' is already a participant, skipping');
                    continue;
                }

                \Log::info('Adding new participant: ' . $userId);
                $chat->participants()->attach($userId, [
                    'joined_at' => now(),
                    'is_active' => true
                ]);
            }
            // Add participants
            \Log::info('All participants added successfully');

            DB::commit();
            \Log::info('Database transaction committed');

            // Notify participants (exclude creator)
            try {
                $recipientIds = $chat->participants()->pluck('users.id')->filter(fn($id) => (int)$id !== (int)auth()->id());
                if ($recipientIds->isNotEmpty()) {
                    $recipients = User::whereIn('id', $recipientIds)->get();
                    if ($recipients->isNotEmpty()) {
                        Notification::send($recipients, new ChatCreatedNotification($chat, auth()->user()));
                    }
                }
            } catch (\Throwable $e) { \Log::warning('Chat notify error: '.$e->getMessage()); }

            // Load fresh data
            $chat->load(['participants:id,name', 'activeParticipants']);
            \Log::info('Chat data reloaded');

            $response = [
                'message' => 'Chat created successfully',
                'chat' => [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'display_name' => $chat->getDisplayName(),
                    'type' => $chat->type,
                    'participants' => $chat->activeParticipants->map(function ($p) {
                        return ['id' => $p->id, 'name' => $p->name];
                    })
                ]
            ];

            \Log::info('=== CHAT CREATION SUCCESS ===');
            \Log::info('Response:', $response);

            if ($request->wantsJson()) {
                return response()->json($response, 201);
            }

            return redirect()->route('chat.index')->with('success', 'Chat created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('=== VALIDATION ERROR ===');
            \Log::error('Validation errors:', $e->errors());

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors());
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            \Log::error('=== AUTHORIZATION ERROR ===');
            \Log::error('Auth error:', ['message' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Unauthorized to create chat',
                    'error' => $e->getMessage()
                ], 403);
            }

            return back()->withErrors(['error' => 'Unauthorized to create chat: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('=== GENERAL ERROR ===');
            \Log::error('Error details:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Failed to create chat',
                    'error' => $e->getMessage(),
                    'debug' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to create chat: ' . $e->getMessage()]);
        }
    }

    /**
     * Get available users for chat creation
     */
    public function availableUsers(Request $request)
    {
        $request->validate([
            'course_class_id' => 'nullable|exists:course_classes,id',
            'search' => 'nullable|string|max:100'
        ]);

        $courseClassId = $request->get('course_class_id');
        $search = $request->get('search');

        // Use the new method from User model
        $users = auth()->user()->getAvailableUsersForChat($courseClassId, $search);

        // Add additional info untuk setiap user
        $users = $users->map(function ($user) use ($courseClassId) {
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];

            // Jika course period dipilih, tampilkan role user di course tersebut
            if ($courseClassId) {
                $courseClass = \App\Models\CourseClass::find($courseClassId);
                if ($courseClass) {
                    // Cek role user di course ini
                    $userRole = 'participant'; // default

                    if ($courseClass->course->instructors->contains($user->id)) {
                        $userRole = 'instructor';
                    } elseif ($courseClass->course->eventOrganizers->contains($user->id)) {
                        $userRole = 'organizer';
                    }

                    $userData['role_in_course'] = $userRole;
                }
            }

            return $userData;
        });

        return response()->json($users);
    }



    /**
     * Get available course classes for chat creation
     */
    public function availableCourseClasses(Request $request)
    {
        // Use the new method from User model
        $courseClasses = auth()->user()->getAccessibleCourseClasses();

        $courseClasses = $courseClasses->map(function ($class) {
            return [
                'id' => $class->id,
                'name' => $class->name,
                'course_title' => $class->course->title ?? 'Unknown Course',
                'display_name' => ($class->course->title ?? 'Unknown') . ' - ' . $class->name,
                'start_date' => $class->start_date->format('Y-m-d'),
                'end_date' => $class->end_date->format('Y-m-d')
            ];
        });

        return response()->json($courseClasses);
    }

    /**
     * Add participants to an existing chat (JSON)
     */
    public function addParticipants(Request $request, Chat $chat)
    {
        Gate::authorize('addParticipant', $chat);

        $validated = $request->validate([
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['exists:users,id']
        ]);

        DB::transaction(function () use ($chat, $validated) {
            $ids = collect($validated['participant_ids'])->map(fn($id) => (int) $id)->unique()->filter();
            foreach ($ids as $id) {
                if (!$chat->participants()->where('user_id', $id)->exists()) {
                    $chat->participants()->attach($id, ['joined_at' => now(), 'is_active' => true]);
                }
            }
            $chat->touch();
        });

        return response()->json(['message' => 'Participants added']);
    }

    /**
     * Remove a participant from chat (JSON)
     */
    public function removeParticipant(Request $request, Chat $chat, User $user)
    {
        Gate::authorize('removeParticipant', $chat);

        // Prevent removing self via this path; client should leave instead
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Use leave chat to remove yourself'], 422);
        }

        $chat->participants()->detach($user->id);
        $chat->touch();

        return response()->json(['message' => 'Participant removed']);
    }


    /**
     * API Index for mobile/external apps
     */
    public function index(Request $request)
    {
        $chats = Chat::whereHas('participants', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with([
                'participants:id,name',
                'lastMessage.user:id,name'
            ])
            ->withCount('activeParticipants')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'chats' => $chats->getCollection()->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'display_name' => $chat->getDisplayName(),
                    'type' => $chat->type,
                    'participants_count' => $chat->active_participants_count,
                    'last_message' => $chat->lastMessage ? [
                        'content' => $chat->lastMessage->content,
                        'user_name' => $chat->lastMessage->user->name,
                        'created_at' => $chat->lastMessage->created_at->toISOString()
                    ] : null,
                    'updated_at' => $chat->updated_at->toISOString()
                ];
            }),
            'pagination' => [
                'current_page' => $chats->currentPage(),
                'last_page' => $chats->lastPage(),
                'per_page' => $chats->perPage(),
                'total' => $chats->total()
            ]
        ]);
    }


    /**
     * API Show for mobile/external apps
     */
    public function show(Request $request, Chat $chat)
    {
        Gate::authorize('view', $chat);

        $chat->load([
            'participants:id,name',
            'activeParticipants',
            'messages' => function ($query) use ($request) {
                $query->with('user:id,name')
                    ->orderBy('created_at', 'desc');

                if ($request->filled('before')) {
                    $query->where('id', '<', $request->get('before'));
                }

                $query->limit($request->get('limit', 50));
            }
        ]);

        return response()->json([
            'chat' => [
                'id' => $chat->id,
                'name' => $chat->name,
                'display_name' => $chat->getDisplayName(),
                'type' => $chat->type,
                'participants' => $chat->activeParticipants->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'name' => $participant->name
                    ];
                })
            ],
            'messages' => $chat->messages->reverse()->values()->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_id' => $message->user_id,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name
                    ],
                    'created_at' => $message->created_at->toISOString()
                ];
            })
        ]);
    }

    /**
     * Search chats
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:1']
        ]);

        $searchTerm = $request->get('query');

        $chats = Chat::whereHas('participants', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('participants', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('messages', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('content', 'like', "%{$searchTerm}%");
                    });
            })
            ->with(['participants:id,name', 'lastMessage.user:id,name'])
            ->withCount('activeParticipants')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'chats' => $chats->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'display_name' => $chat->getDisplayName(),
                    'type' => $chat->type,
                    'participants_count' => $chat->active_participants_count,
                    'last_message' => $chat->lastMessage ? [
                        'content' => Str::limit($chat->lastMessage->content, 50),
                        'user_name' => $chat->lastMessage->user->name,
                        'created_at' => $chat->lastMessage->created_at->toISOString()
                    ] : null
                ];
            })
        ]);
    }
}
