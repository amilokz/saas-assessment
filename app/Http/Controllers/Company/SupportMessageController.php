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
        $user = Auth::user();
        $company = $user->company;
        
        $messages = Message::where('company_id', $company->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => $messages->total(),
            'open' => Message::where('company_id', $company->id)
                ->whereNull('parent_id')
                ->where('status', 'open')
                ->count(),
            'in_progress' => Message::where('company_id', $company->id)
                ->whereNull('parent_id')
                ->where('status', 'in_progress')
                ->count(),
            'closed' => Message::where('company_id', $company->id)
                ->whereNull('parent_id')
                ->where('status', 'closed')
                ->count(),
        ];

        return view('company.support', compact('messages', 'stats'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        Message::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'type' => 'support',
            'status' => 'open',
        ]);

        return redirect()->back()
            ->with('success', 'Support ticket created.');
    }

    public function show(Message $message)
    {
        $user = Auth::user();
        
        if ($message->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        $message->load(['user', 'replies.user']);

        return view('company.support-show', compact('message'));
    }

    public function reply(Request $request, Message $message)
    {
        $user = Auth::user();
        
        if ($message->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        // Check if user can reply (admin or support user)
        if (!($user->isCompanyAdmin() || $user->isSupportUser())) {
            abort(403, 'Only administrators and support users can reply.');
        }

        $validated = $request->validate([
            'message' => 'required|string|min:5',
        ]);

        Message::create([
            'company_id' => $message->company_id,
            'user_id' => $user->id,
            'parent_id' => $message->id,
            'message' => $validated['message'],
            'type' => 'support',
            'status' => 'open',
        ]);

        return redirect()->back()
            ->with('success', 'Reply sent.');
    }

    public function close(Request $request, Message $message)
    {
        $user = Auth::user();
        
        if ($message->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        if (!($user->isCompanyAdmin() || $user->isSupportUser())) {
            abort(403, 'Only administrators and support users can close tickets.');
        }

        $message->update(['status' => 'closed']);

        return redirect()->back()
            ->with('success', 'Ticket closed.');
    }

    public function reopen(Request $request, Message $message)
    {
        $user = Auth::user();
        
        if ($message->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        $message->update(['status' => 'open']);

        return redirect()->back()
            ->with('success', 'Ticket reopened.');
    }
}