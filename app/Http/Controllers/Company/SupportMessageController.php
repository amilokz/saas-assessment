<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportMessageController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        
        $messages = $company->messages()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadCount = $company->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->count();

        return view('company.support', compact('messages', 'unreadCount'));
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

        return redirect()->route('company.support')
            ->with('success', 'Support request sent successfully.');
    }

    public function reply(Message $message, Request $request)
    {
        // Verify ownership
        if ($message->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
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

        return redirect()->route('company.support')
            ->with('success', 'Reply sent successfully.');
    }

    public function show(Message $message)
    {
        // Verify ownership
        if ($message->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // Mark as read
        if (!$message->is_read && $message->user_id !== Auth::id()) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        $message->load(['user', 'replies.user']);

        return view('company.support-show', compact('message'));
    }

    public function close(Message $message)
    {
        // Verify ownership and permissions
        $user = Auth::user();
        if ($message->company_id !== $user->company_id || 
            !($user->isCompanyAdmin() || $user->isSupportUser())) {
            abort(403, 'Unauthorized action.');
        }

        $message->update([
            'status' => 'closed',
        ]);

        return redirect()->route('company.support')
            ->with('success', 'Support request closed.');
    }

    public function reopen(Message $message)
    {
        // Verify ownership
        if ($message->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $message->update([
            'status' => 'open',
        ]);

        return redirect()->route('company.support')
            ->with('success', 'Support request reopened.');
    }
}