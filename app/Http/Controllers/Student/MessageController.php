<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display student inbox
     */
    public function inbox()
    {
        $student = Auth::user();

        $messages = Message::where('recipient_id', $student->id)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Message::where('recipient_id', $student->id)
            ->where('is_read', false)
            ->count();

        return view('student.messages.inbox', compact('messages', 'unreadCount'));
    }

    /**
     * Show a single message
     */
    public function show(Message $message)
    {
        // Ensure the student is the recipient
        if ($message->recipient_id != Auth::id()) {
            abort(403);
        }

        // Mark as read
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('student.messages.show', compact('message'));
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
}
