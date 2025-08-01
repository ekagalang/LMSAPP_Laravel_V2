<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Chat;
use App\Models\CoursePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
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
                    'unread_count' => $this->getUnreadCount($chat, $request->user()->id),
                ];
            });

        return response()->json(['chats' => $chats]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Chat::class);

        // ✅ Validasi name hanya wajib untuk group chat
        $request->validate([
            'type' => ['required', Rule::in(['direct', 'group'])],
            'course_period_id' => ['nullable', 'exists:course_periods,id'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['exists:users,id'],
            'name' => [
                Rule::requiredIf(fn() => $request->type === 'group'),
                'nullable',
                'string',
                'max:255'
            ],
        ]);

        $coursePeriod = null;
        if ($request->course_period_id) {
            $coursePeriod = CoursePeriod::findOrFail($request->course_period_id);

            // Cek apakah chat di periode ini diizinkan
            if (!$coursePeriod->isChatAllowed()) {
                return response()->json([
                    'message' => 'Chat tidak tersedia untuk periode ini.'
                ], 422);
            }

            // Cek semua peserta terdaftar di periode kursus
            foreach ($request->participant_ids as $participantId) {
                if (!$coursePeriod->hasUser($participantId)) {
                    return response()->json([
                        'message' => 'Semua peserta harus terdaftar dalam periode kursus ini.'
                    ], 422);
                }
            }
        }

        // ✅ Cek direct chat sudah ada
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

        // ✅ Auto set nama jika kosong
        $chatName = $request->name;
        if ($request->type === 'group') {
            if (!$chatName && $coursePeriod) {
                $chatName = $coursePeriod->course->title . ' - ' . $coursePeriod->name;
            } elseif (!$chatName) {
                $chatName = 'Group Chat';
            }
        } else {
            if (!$chatName) {
                $chatName = 'Direct Chat';
            }
        }

        // ✅ Buat chat
        $chat = Chat::create([
            'course_period_id' => $request->course_period_id,
            'created_by' => $request->user()->id,
            'name' => $chatName,
            'type' => $request->type,
        ]);

        // Tambahkan peserta
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
                $users = $user->getAvailableChatUsers($query);
            }
        }

        return response()->json(['users' => $users]);
    }

    public function availableCoursePeriods(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('super-admin')) {
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

    public function webShow(Chat $chat)
    {
        Gate::authorize('view', $chat);

        $messages = $chat->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        $chat->participants()->updateExistingPivot(auth()->id(), [
            'last_read_at' => now()
        ]);

        return view('chat.show', compact('chat', 'messages'));
    }
}
