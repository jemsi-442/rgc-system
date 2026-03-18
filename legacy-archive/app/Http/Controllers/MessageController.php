<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display all conversations
     */
    public function index()
    {
        $userId = Auth::id();

        // Get unique conversations with last message
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($messages) use ($userId) {
                $lastMessage = $messages->first();
                $otherUserId = $lastMessage->sender_id == $userId ? $lastMessage->receiver_id : $lastMessage->sender_id;
                $unreadCount = $messages->where('receiver_id', $userId)->where('is_read', false)->count();

                return [
                    'user' => User::with('role')->find($otherUserId),
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->filter(function ($conv) {
                return $conv['user'] !== null;
            })
            ->values();

        // Get all users that can be messaged (for new conversation)
        // Messages page is for leaders only, so only show other leaders
        $allUsers = User::where('id', '!=', Auth::id())
            ->whereHas('role', function ($q) {
                $q->whereIn('name', ['Mchungaji', 'Mhasibu']);
            })
            ->where('is_active', true)
            ->with('role')
            ->orderBy('name')
            ->get();

        // Calculate unread count per contact
        $unreadPerContact = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->selectRaw('sender_id, COUNT(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');

        // Count online users (active in last 5 minutes)
        // Wrapped in try-catch in case last_seen_at column doesn't exist yet
        try {
            $onlineCount = User::where('id', '!=', $userId)
                ->where('is_active', true)
                ->where('last_seen_at', '>=', now()->subMinutes(5))
                ->count();
        } catch (\Exception $e) {
            $onlineCount = 0;
        }

        return view('panel.messages.index', compact('conversations', 'allUsers', 'unreadPerContact', 'onlineCount'));
    }

    /**
     * Show conversation with a specific user
     */
    public function conversation(Request $request, $userId)
    {
        $otherUser = User::findOrFail($userId);
        $currentUserId = Auth::id();

        // Get all messages in conversation
        $messages = Message::conversation($currentUserId, $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'messages' => $messages,
                'last_id' => $messages->last()?->id ?? 0,
                'other_user' => $otherUser,
            ]);
        }

        // Get leaders for new conversation (for members)
        $leaders = [];
        if (Auth::user()->isMwanachama()) {
            $leaders = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Mchungaji', 'Mhasibu']);
            })->where('is_active', true)->get();
        }

        // Get conversations for sidebar
        $conversations = Message::where('sender_id', $currentUserId)
            ->orWhere('receiver_id', $currentUserId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($currentUserId) {
                return $message->sender_id == $currentUserId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($msgs) use ($currentUserId) {
                $lastMessage = $msgs->first();
                $otherUserId = $lastMessage->sender_id == $currentUserId ? $lastMessage->receiver_id : $lastMessage->sender_id;
                $unreadCount = $msgs->where('receiver_id', $currentUserId)->where('is_read', false)->count();

                return [
                    'user' => User::with('role')->find($otherUserId),
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->filter(function ($conv) {
                return $conv['user'] !== null;
            })
            ->values();

        return view('panel.messages.conversation', compact('messages', 'otherUser', 'leaders', 'conversations'));
    }

    /**
     * Send a new message
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:2000',
        ], [
            'receiver_id.required' => 'Tafadhali chagua mpokeaji',
            'receiver_id.exists' => 'Mpokeaji hapatikani',
            'content.required' => 'Tafadhali andika ujumbe',
            'content.max' => 'Ujumbe ni mrefu mno (max 2000 herufi)',
        ]);

        // Verify receiver is a leader (Mchungaji or Mhasibu)
        $receiver = User::with('role')->find($validated['receiver_id']);
        if (!$receiver || !$receiver->role || !in_array($receiver->role->name, ['Mchungaji', 'Mhasibu'])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unaweza kutuma ujumbe kwa viongozi tu',
                ], 403);
            }
            return back()->with('error', 'Unaweza kutuma ujumbe kwa viongozi tu');
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content'],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->route('messages.conversation', $validated['receiver_id'])
            ->with('success', 'Ujumbe umetumwa kikamilifu');
    }

    /**
     * Get unread messages count (for AJAX)
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get new messages in conversation (for polling)
     */
    public function getNewMessages(Request $request, $userId)
    {
        $lastId = $request->get('last_id', 0);
        $currentUserId = Auth::id();

        $messages = Message::conversation($currentUserId, $userId)
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'asc')
            ->with('sender')
            ->get();

        // Mark received messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'messages' => $messages,
            'last_id' => $messages->last()?->id ?? $lastId,
        ]);
    }

    /**
     * Start new conversation (show form)
     */
    public function create()
    {
        // Messages page is for leaders only, so only show other leaders
        $users = User::where('id', '!=', Auth::id())
            ->whereHas('role', function ($q) {
                $q->whereIn('name', ['Mchungaji', 'Mhasibu']);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('panel.messages.create', compact('users'));
    }
}
