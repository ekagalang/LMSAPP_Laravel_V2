<?php
// app/Http/Controllers/Api/ChatController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\CoursePeriod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    // ✅ EXISTING API METHODS (unchanged)
    public function index(Request $request)
    {
        $user = $request->user();

        $chats = Chat::forUser($user->id)
            ->active()
            ->with([
                'latestMessage.user:id,name',
                'activeParticipants:id,name',
                'coursePeriod.course:id,title'
            ])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($chat) {
                $context = $chat->getContextInfo();

                return [
                    'id' => $chat->id,
                    'name' => $chat->getDisplayName(),
                    'type' => $chat->type,
                    'context' => $context,
                    'participants' => $chat->activeParticipants,
                    'latest_message' => $chat->latestMessage ? [
                        'content' => $chat->latestMessage->content,
                        'user' => $chat->latestMessage->user->name,
                        'created_at' => $chat->latestMessage->created_at->diffForHumans(),
                    ] : null,
                    'last_message_at' => $chat->last_message_at?->diffForHumans(),
                    'unread_count' => $this->getUnreadCount($chat, $user->id),
                ];
            });

        return response()->json(['chats' => $chats]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Chat::class);

        $request->validate([
            'type' => ['required', Rule::in(['direct', 'group'])],
            'course_period_id' => ['nullable', 'exists:course_periods,id'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['exists:users,id'],
            'name' => ['required_if:type,group', 'string', 'max:255'],
        ]);

        $coursePeriod = null;
        if ($request->course_period_id) {
            $coursePeriod = CoursePeriod::findOrFail($request->course_period_id);

            // Verify period is active for chat
            if (!$coursePeriod->isChatAllowed()) {
                return response()->json([
                    'message' => 'Chat tidak tersedia untuk periode ini.'
                ], 422);
            }

            // Verify all participants are in the course period
            foreach ($request->participant_ids as $participantId) {
                if (!$coursePeriod->hasUser($participantId)) {
                    return response()->json([
                        'message' => 'Semua peserta harus terdaftar dalam periode kursus ini.'
                    ], 422);
                }
            }
        }

        // Check for existing direct chat
        if ($request->type === 'direct' && count($request->participant_ids) === 1) {
            $existingChat = Chat::where('type', 'direct')
                ->where('course_period_id', $request->course_period_id)
                ->whereHas('participants', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                })
                ->whereHas('participants', function ($q) use ($request) {
                    $q->where('user_id', $request->participant_ids[0]);
                })
                ->first();

            if ($existingChat) {
                return response()->json(['chat' => $existingChat->load('activeParticipants:id,name')]);
            }
        }

        $chat = Chat::create([
            'course_period_id' => $request->course_period_id,
            'created_by' => $request->user()->id,
            'name' => $request->name,
            'type' => $request->type,
        ]);

        // Add participants
        $chat->addParticipant($request->user()->id);
        foreach ($request->participant_ids as $participantId) {
            $chat->addParticipant($participantId);
        }

        return response()->json([
            'chat' => $chat->load('activeParticipants:id,name')
        ], 201);
    }

    public function show(Chat $chat)
    {
        Gate::authorize('view', $chat);

        $context = $chat->getContextInfo();

        return response()->json([
            'chat' => [
                'id' => $chat->id,
                'name' => $chat->getDisplayName(),
                'type' => $chat->type,
                'context' => $context,
                'participants' => $chat->activeParticipants,
                'can_send_message' => $chat->isChatAllowed(),
                'course_period' => $chat->coursePeriod ? [
                    'id' => $chat->coursePeriod->id,
                    'name' => $chat->coursePeriod->name,
                    'course_title' => $chat->coursePeriod->course->title,
                    'is_active' => $chat->coursePeriod->isActive(),
                    'end_date' => $chat->coursePeriod->end_date->format('Y-m-d H:i:s'),
                ] : null,
            ]
        ]);
    }

   

public function availableUsers(Request $request)
{
    $user = $request->user();
    $query = $request->get('q', '');
    $coursePeriodId = $request->get('course_period_id');

    if ($coursePeriodId) {
        // Get users for specific course period
        $coursePeriod = CoursePeriod::findOrFail($coursePeriodId);
        $users = $coursePeriod->course->getAllUsers()
            ->where('id', '!=', $user->id)
            ->when($query, function ($collection) use ($query) {
                return $collection->filter(function ($user) use ($query) {
                    return stripos($user->name, $query) !== false ||
                        stripos($user->email, $query) !== false;
                });
            })
            ->take(50)
            ->values();
    } else {
        // For admin: get all users, for others: get available chat users
        if ($user->hasRole('super-admin')) {
            $usersQuery = User::where('id', '!=', $user->id);
            
            if ($query) {
                $usersQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                });
            }
            
            $users = $usersQuery->take(50)->get()->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email
                ];
            });
        } else {
            // Use existing method for non-admin users
            $users = $user->getAvailableChatUsers($query);
        }
    }

    return response()->json(['users' => $users]);
}

public function availableCoursePeriods(Request $request)
{
    $user = $request->user();

    if ($user->hasRole('super-admin')) {
        // Admin can see all active course periods
        $periods = CoursePeriod::active()
            ->with('course:id,title')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'course_title' => $period->course->title,
                    'full_name' => $period->course->title . ' - ' . $period->name,
                ];
            });
    } else {
        // Non-admin users see only their course periods
        $periods = CoursePeriod::forUser($user->id)
            ->active()
            ->with('course:id,title')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'course_title' => $period->course->title,
                    'full_name' => $period->course->title . ' - ' . $period->name,
                ];
            });
    }

    return response()->json(['periods' => $periods]);
}

    private function getUnreadCount(Chat $chat, $userId): int
    {
        $participant = $chat->participants()->where('user_id', $userId)->first();

        if (!$participant || !$participant->pivot->last_read_at) {
            return $chat->messages()->count();
        }

        return $chat->messages()
            ->where('created_at', '>', $participant->pivot->last_read_at)
            ->where('user_id', '!=', $userId)
            ->count();
    }

    // ✅ NEW WEB METHODS
    /**
     * Display chat index page for web interface
     */
    public function webIndex(Request $request)
    {
        $user = $request->user();

        $chats = Chat::forUser($user->id)
            ->active()
            ->with([
                'latestMessage.user:id,name',
                'activeParticipants:id,name',
                'coursePeriod.course:id,title'
            ])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('chat.index', compact('chats'));
    }

    /**
     * Display specific chat page for web interface
     */
    public function webShow(Chat $chat)
    {
        Gate::authorize('view', $chat);

        // Load recent messages for initial display
        $messages = $chat->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // Update last read timestamp
        $chat->participants()->updateExistingPivot(auth()->id(), [
            'last_read_at' => now()
        ]);

        return view('chat.show', compact('chat', 'messages'));
    }
}