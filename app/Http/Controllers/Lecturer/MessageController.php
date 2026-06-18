<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display lecturer inbox
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

        return view('lecturer.messages.inbox', compact('messages', 'unreadCount'));
    }

    /**
     * Show message compose form
     */
    public function compose()
    {
        // Get students from the lecturer's department
        $students = User::where('role_id', 3)
            ->where('department_id', Auth::user()->department_id)
            ->get();

        return view('lecturer.messages.compose', compact('students'));
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
            'subject' => $validated['subject'] ?? 'Message from Lecturer',
            'is_read' => false,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'data' => $message
            ]);
        }

        return redirect()->route('lecturer.messages.inbox')
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Show a single message
     */
    public function show(Message $message)
    {
        if ($message->sender_id != Auth::id() && $message->recipient_id != Auth::id()) {
            abort(403);
        }

        if ($message->recipient_id == Auth::id() && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('lecturer.messages.show', compact('message'));
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

        return view('lecturer.messages.sent', compact('messages'));
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
