<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display admin inbox
     */
    public function inbox()
    {
        $messages = Message::where('recipient_id', Auth::id())
            ->orWhere('sender_id', Auth::id())
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('admin.messages.inbox', compact('messages', 'unreadCount'));
    }

    /**
     * Show message compose form
     */
    public function compose()
    {
        $students = User::where('role_id', 3)->get();
        $lecturers = User::where('role_id', 2)->get();
        return view('admin.messages.compose', compact('students', 'lecturers'));
    }

    /**
     * Send a message
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|min:1',
            'subject' => 'nullable|string|max:255',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $validated['recipient_id'],
            'message' => $validated['message'],
            'subject' => $validated['subject'] ?? 'Message from Admin',
            'is_read' => false,
        ]);

        // For AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'data' => $message
            ]);
        }

        return redirect()->route('admin.messages.inbox')
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Show a single message
     */
    public function show(Message $message)
    {
        // Ensure admin is either sender or recipient
        if ($message->sender_id != Auth::id() && $message->recipient_id != Auth::id()) {
            abort(403);
        }

        // Mark as read if admin is recipient
        if ($message->recipient_id == Auth::id() && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Get unread message count (for AJAX)
     */
    public function unreadCount()
    {
        $count = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Get sent messages
     */
    public function sent()
    {
        $messages = Message::where('sender_id', Auth::id())
            ->with(['recipient'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.messages.sent', compact('messages'));
    }

    /**
     * Get messages for a specific user (AJAX)
     */
    public function getMessages(User $user)
    {
        $messages = Message::where(function($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('recipient_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('recipient_id', Auth::id());
        })
        ->with(['sender', 'recipient'])
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Mark a message as read
     */
    public function markAsRead(Message $message)
    {
        if ($message->recipient_id != Auth::id()) {
            abort(403);
        }

        $message->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }
}
