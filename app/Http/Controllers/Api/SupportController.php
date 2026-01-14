<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $company = Auth::user()->company;
        
        $query = $company->messages()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $messages = $query->paginate($request->get('per_page', 10));

        $unreadCount = $company->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->count();

        return response()->json([
            'data' => $messages,
            'stats' => [
                'total' => $messages->total(),
                'unread' => $unreadCount,
                'open' => $company->messages()->where('status', 'open')->count(),
                'in_progress' => $company->messages()->where('status', 'in_progress')->count(),
                'closed' => $company->messages()->where('status', 'closed')->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'message' => 'required|string|min:10|max:2000',
            'subject' => 'nullable|string|max:255',
        ]);

        $message = Message::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'subject' => $validated['subject'] ?? 'Support Request',
            'type' => 'support',
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Support request sent successfully',
            'data' => $message,
        ], 201);
    }

    public function show(Message $message)
    {
        // Verify ownership
        if ($message->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark as read
        if (!$message->is_read && $message->user_id !== Auth::id()) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        $message->load(['user', 'replies.user']);

        return response()->json([
            'data' => $message,
        ]);
    }

    public function reply(Message $message, Request $request)
    {
        // Verify ownership
        if ($message->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:10|max:2000',
        ]);

        $reply = Message::create([
            'company_id' => $message->company_id,
            'user_id' => Auth::id(),
            'parent_id' => $message->id,
            'message' => $validated['message'],
            'type' => 'support',
            'status' => 'awaiting_reply',
        ]);

        // Mark parent as read if replying
        if (!$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Reply sent successfully',
            'data' => $reply,
        ], 201);
    }

    public function close(Message $message)
    {
        // Verify ownership and permissions
        $user = Auth::user();
        if ($message->company_id !== $user->company_id || 
            !($user->isCompanyAdmin() || $user->isSupportUser())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->update([
            'status' => 'closed',
        ]);

        return response()->json([
            'message' => 'Support request closed',
            'data' => $message,
        ]);
    }

    public function reopen(Message $message)
    {
        // Verify ownership
        if ($message->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->update([
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Support request reopened',
            'data' => $message,
        ]);
    }
}