<?php

namespace App\Http\Controllers;

use App\Models\Reflection;
use App\Http\Requests\StoreReflectionRequest;
use App\Http\Requests\UpdateReflectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ReflectionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'my');

        // Optimize eager loading based on role
        $eagerLoad = $user->hasRole(['super-admin', 'instructor'])
            ? ['user:id,name,email', 'respondedBy:id,name']
            : ['respondedBy:id,name'];

        if ($user->hasRole(['super-admin', 'instructor'])) {
            // Instructors can see different views
            $query = Reflection::with($eagerLoad);

            switch ($filter) {
                case 'my':
                    $query->where('user_id', $user->id);
                    break;
                case 'needs_response':
                    $query->requiringResponse()->visibleToInstructors();
                    break;
                case 'all':
                    $query->visibleToInstructors();
                    break;
                case 'public':
                    $query->public();
                    break;
                default:
                    $query->where('user_id', $user->id);
            }
        } else {
            // Participants can only see their own reflections - use optimized query
            $query = Reflection::forUser($user->id)->with($eagerLoad);
            $filter = 'my'; // Override filter for participants
        }

        // Add index hints for better performance
        $perPage = min($request->get('per_page', config('reflection.pagination.per_page', 10)),
                      config('reflection.pagination.max_per_page', 50));

        $reflections = $query
            ->select(['id', 'user_id', 'title', 'content', 'mood', 'tags', 'visibility', 'requires_response', 'instructor_response', 'responded_by', 'responded_at', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('reflections.index', compact('reflections', 'filter'));
    }

    public function create()
    {
        return view('reflections.create');
    }

    public function store(StoreReflectionRequest $request)
    {
        $validated = $request->validated();

        $reflection = Reflection::create($validated);

        // Clear analytics cache when new reflection is created
        $this->clearAnalyticsCache();

        Log::info('Reflection Created', [
            'reflection_id' => $reflection->id,
            'user_id' => Auth::id(),
            'title' => $reflection->title
        ]);

        return redirect()->route('reflections.index')
            ->with('success', 'Refleksi berhasil dibuat! 📝');
    }


    public function edit($id)
    {
        $reflection = Reflection::findOrFail($id);

        // Only the author can edit
        if ($reflection->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own reflections.');
        }

        return view('reflections.edit', compact('reflection'));
    }

    public function update(UpdateReflectionRequest $request, $id)
    {
        $reflection = Reflection::findOrFail($id);
        $validated = $request->validated();

        $reflection->update($validated);

        // Clear analytics cache when reflection is updated
        $this->clearAnalyticsCache();

        Log::info('Reflection Updated', [
            'reflection_id' => $reflection->id,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('reflections.show', $reflection)
            ->with('success', 'Refleksi berhasil diperbarui! ✅');
    }

    public function destroy($id)
    {
        $reflection = Reflection::findOrFail($id);

        // Only the author can delete
        if ($reflection->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own reflections.');
        }

        $reflection->delete();

        // Clear analytics cache when reflection is deleted
        $this->clearAnalyticsCache();

        Log::info('Reflection Deleted', [
            'reflection_id' => $id,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('reflections.index')
            ->with('success', 'Refleksi berhasil dihapus! 🗑️');
    }

    public function respond(Request $request, $id)
    {
        // Only instructors can respond
        if (!Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Only instructors can respond to reflections.');
        }

        $reflection = Reflection::findOrFail($id);

        $validated = $request->validate([
            'instructor_response' => 'required|string'
        ]);

        $reflection->update([
            'instructor_response' => $validated['instructor_response'],
            'responded_by' => Auth::id(),
            'responded_at' => now()
        ]);

        Log::info('Reflection Response Added', [
            'reflection_id' => $reflection->id,
            'instructor_id' => Auth::id(),
            'student_id' => $reflection->user_id
        ]);

        return redirect()->route('reflections.show', $reflection)
            ->with('success', 'Respon berhasil ditambahkan! 💬');
    }

    public function removeResponse($id)
    {
        // Only instructors can remove responses
        if (!Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Only instructors can remove responses.');
        }

        $reflection = Reflection::findOrFail($id);

        $reflection->update([
            'instructor_response' => null,
            'responded_by' => null,
            'responded_at' => null
        ]);

        Log::info('Reflection Response Removed', [
            'reflection_id' => $reflection->id,
            'instructor_id' => Auth::id()
        ]);

        return redirect()->route('reflections.show', $reflection)
            ->with('success', 'Respon berhasil dihapus! ❌');
    }

    public function analytics()
    {
        // Only instructors can view analytics
        if (!Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Only instructors can view reflection analytics.');
        }

        // Cache analytics data with configurable TTL
        $cacheKey = 'reflection_analytics_' . now()->format('Y-m-d-H-i');
        $cacheMinutes = config('reflection.cache.analytics_ttl', 15);

        $stats = Cache::remember($cacheKey, $cacheMinutes * 60, function () {
            return [
                'total_reflections' => Reflection::count(),
                'this_month' => Reflection::whereYear('created_at', now()->year)
                                       ->whereMonth('created_at', now()->month)
                                       ->count(),
                'needs_response' => Reflection::requiringResponse()->count(),
                'mood_distribution' => Reflection::selectRaw('mood, count(*) as count')
                                                ->whereNotNull('mood')
                                                ->groupBy('mood')
                                                ->pluck('count', 'mood')
                                                ->toArray(),
                'recent_reflections' => Reflection::with(['user:id,name'])
                                                ->visibleToInstructors()
                                                ->recent(10)
                                                ->get()
            ];
        });

        return view('reflections.analytics', compact('stats'));
    }

    private function canViewReflection(Reflection $reflection, $user): bool
    {
        // Author can always view their own
        if ($reflection->user_id === $user->id) {
            return true;
        }

        // Check visibility settings
        switch ($reflection->visibility) {
            case 'private':
                return false;
            case 'instructors_only':
                return $user->hasRole(['super-admin', 'instructor']);
            case 'public':
                return true;
            default:
                return false;
        }
    }

    /**
     * Clear analytics cache when data changes
     */
    private function clearAnalyticsCache(): void
    {
        // More targeted cache clearing for production efficiency
        $currentHour = now()->format('Y-m-d-H');
        $cacheKeys = [
            "reflection_analytics_{$currentHour}-0",
            "reflection_analytics_{$currentHour}-1",
            "reflection_analytics_{$currentHour}-2",
            "reflection_analytics_{$currentHour}-3"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Also clear previous hour cache
        $previousHour = now()->subHour()->format('Y-m-d-H');
        $previousKeys = [
            "reflection_analytics_{$previousHour}-0",
            "reflection_analytics_{$previousHour}-1",
            "reflection_analytics_{$previousHour}-2",
            "reflection_analytics_{$previousHour}-3"
        ];

        foreach ($previousKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Optimized show method with selective eager loading
     */
    public function show($id)
    {
        $user = Auth::user();

        // Only load relationships we actually need for the view
        $reflection = Reflection::with([
            'user:id,name,email',
            'respondedBy:id,name'
        ])->findOrFail($id);

        // Check if user can view this reflection
        if (!$this->canViewReflection($reflection, $user)) {
            abort(403, 'You do not have permission to view this reflection.');
        }

        return view('reflections.show', compact('reflection'));
    }
}