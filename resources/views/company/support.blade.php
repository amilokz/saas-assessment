@extends('layouts.app')

@section('title', 'Support Center')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Support Center
        </h2>
        <button onclick="document.getElementById('new-ticket-modal').classList.remove('hidden')"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Ticket
        </button>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Tickets</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $messages->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Open</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $messages->where('status', 'open')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">In Progress</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $messages->where('status', 'in_progress')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Unread</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $unreadCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Tickets -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Filter Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8">
                        <a href="{{ route('company.support') }}" 
                            class="{{ !request()->has('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            All Tickets
                            <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2 rounded-full text-xs">
                                {{ $messages->total() }}
                            </span>
                        </a>
                        <a href="{{ route('company.support') }}?status=open" 
                            class="{{ request()->get('status') == 'open' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Open
                            <span class="bg-green-100 text-green-800 ml-2 py-0.5 px-2 rounded-full text-xs">
                                {{ $messages->where('status', 'open')->count() }}
                            </span>
                        </a>
                        <a href="{{ route('company.support') }}?status=in_progress" 
                            class="{{ request()->get('status') == 'in_progress' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            In Progress
                            <span class="bg-yellow-100 text-yellow-800 ml-2 py-0.5 px-2 rounded-full text-xs">
                                {{ $messages->where('status', 'in_progress')->count() }}
                            </span>
                        </a>
                        <a href="{{ route('company.support') }}?status=closed" 
                            class="{{ request()->get('status') == 'closed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Closed
                            <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2 rounded-full text-xs">
                                {{ $messages->where('status', 'closed')->count() }}
                            </span>
                        </a>
                    </nav>
                </div>

                @if($messages->count() > 0)
                    <div class="space-y-4">
                        @foreach($messages as $ticket)
                            <div class="bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div class="p-4">
                                    <div class="flex justify-between items-start">
                                        <!-- Ticket Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                @if(!$ticket->is_read && $ticket->user_id !== Auth::id())
                                                    <span class="h-2 w-2 bg-blue-500 rounded-full mr-2"></span>
                                                @endif
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('company.support.show', $ticket) }}" class="hover:text-blue-600">
                                                        {{ $ticket->subject }}
                                                    </a>
                                                </h4>
                                                @php
                                                    $statusColors = [
                                                        'open' => 'bg-green-100 text-green-800',
                                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                                        'closed' => 'bg-gray-100 text-gray-800',
                                                        'awaiting_reply' => 'bg-blue-100 text-blue-800',
                                                    ];
                                                @endphp
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$ticket->status] }}">
                                                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 line-clamp-2">
                                                {{ Str::limit($ticket->message, 150) }}
                                            </p>
                                        </div>

                                        <!-- Meta Info -->
                                        <div class="ml-4 flex-shrink-0 text-right">
                                            <div class="text-sm text-gray-500">
                                                {{ $ticket->created_at->format('M d') }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $ticket->created_at->diffForHumans() }}
                                            </div>
                                            <div class="text-sm text-gray-600 mt-2">
                                                {{ $ticket->user->name }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Replies Count -->
                                    @if($ticket->replies->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                                </svg>
                                                {{ $ticket->replies->count() }} repl{{ $ticket->replies->count() == 1 ? 'y' : 'ies' }}
                                                @if($ticket->replies->max('created_at'))
                                                    <span class="mx-2">•</span>
                                                    Last reply {{ $ticket->replies->max('created_at')->diffForHumans() }}
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $messages->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No support tickets</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new support ticket.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- New Ticket Modal -->
<div id="new-ticket-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">New Support Ticket</h3>
            <button onclick="document.getElementById('new-ticket-modal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('company.support.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">
                    Subject
                </label>
                <input type="text" id="subject" name="subject"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Brief description of your issue"
                    value="{{ old('subject') }}">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">
                    Message *
                </label>
                <textarea id="message" name="message" rows="6" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Describe your issue in detail...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('new-ticket-modal').classList.add('hidden')"
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection