<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $user = Auth::user();
        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($user->id)) {
                $announcement->markAsRead($user->id);
            }
        }

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:all,admin,lecturer,student',
            'published_at' => 'nullable|date',
        ]);

        $targetRoles = $validated['target_roles'];

        if (in_array('all', $targetRoles)) {
            $targetRole = 'all';
        } else {
            sort($targetRoles);
            $targetRole = implode(',', $targetRoles);
        }

        // ✅ REMOVED 'read_by' => null - it will be NULL by default
        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'target_role' => $targetRole,
            'posted_by' => Auth::id(),
            'is_active' => true,
            'published_at' => $request->filled('published_at')
                ? Carbon::parse($validated['published_at'])
                : now(),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function show($id)
    {
        $announcement = Announcement::with('creator')->findOrFail($id);
        $announcement->markAsRead(Auth::id());
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:all,admin,lecturer,student',
            'published_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $targetRoles = $validated['target_roles'];

        if (in_array('all', $targetRoles)) {
            $targetRole = 'all';
        } else {
            sort($targetRoles);
            $targetRole = implode(',', $targetRoles);
        }

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'target_role' => $targetRole,
            'is_active' => $request->has('is_active'),
            'published_at' => $request->filled('published_at')
                ? Carbon::parse($validated['published_at'])
                : $announcement->published_at,
            // read_by is NOT updated here - it tracks who has read the announcement
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'is_active' => !$announcement->is_active
        ]);

        $status = $announcement->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.announcements.index')
            ->with('success', "Announcement {$status} successfully!");
    }

    /**
     * Get unread announcements count
     */
    public function unreadCount()
    {
        try {
            $user = Auth::user();
            $count = Announcement::getUnreadCount($user);

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Mark announcement as read
     */
    public function markAsRead($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->markAsRead(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Announcement marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Reset unread status for current user (for testing)
     */
    public function resetUnread()
    {
        $user = Auth::user();
        Announcement::resetReadStatus($user->id);

        return redirect()->back()->with('success', 'All announcements marked as unread!');
    }

    /**
     * Check unread count for debugging
     */
    public function checkUnread()
    {
        $user = Auth::user();
        $count = Announcement::getUnreadCount($user);

        $total = Announcement::forRole($user->role->name ?? 'student')
            ->where('is_active', true)
            ->count();

        return response()->json([
            'user_id' => $user->id,
            'user_role' => $user->role->name ?? 'student',
            'unread_count' => $count,
            'total_announcements' => $total,
        ]);
    }

    /**
     * Force reset all read statuses
     */
    public function forceResetAll()
    {
        Announcement::forceResetAll();
        return redirect()->back()->with('success', 'All read statuses reset!');
    }
}
