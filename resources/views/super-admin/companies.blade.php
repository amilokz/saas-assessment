@extends('layouts.app')

@section('title', 'Manage Companies')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manage Companies
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            @php
                $statuses = [
                    'pending' => ['bg-yellow-100 text-yellow-800', 'Pending Approval'],
                    'trial_pending_approval' => ['bg-blue-100 text-blue-800', 'On Trial'],
                    'approved' => ['bg-green-100 text-green-800', 'Active'],
                    'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
                    'suspended' => ['bg-gray-100 text-gray-800', 'Suspended'],
                ];
            @endphp

            @foreach($statuses as $status => [$bgColor, $label])
                @php
                    $count = $companies->where('status', $status)->count();
                @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-md p-2 {{ $bgColor }}">
                            <span class="text-lg font-semibold">{{ $count }}</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $count }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Companies Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Search and Filters -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">All Companies</h3>
                        <p class="text-sm text-gray-500">Manage company registrations and approvals</p>
                    </div>
                    
                    <div class="mt-4 md:mt-0">
                        <div class="relative">
                            <input type="text" 
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-64" 
                                placeholder="Search companies...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                @if($companies->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Company
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Admin
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Users
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Registered
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($companies as $company)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $company->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $company->email }}
                                                    </div>
                                                    @if($company->business_type)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $company->business_type }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $company->admin_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $company->users_count }} users
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'trial_pending_approval' => 'bg-blue-100 text-blue-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'suspended' => 'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$company->status] }}">
                                                {{ str_replace('_', ' ', ucfirst($company->status)) }}
                                            </span>
                                            @if($company->trial_ends_at)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Trial ends: {{ $company->trial_ends_at->format('M d') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $company->created_at->format('M d, Y') }}
                                            <div class="text-xs text-gray-400">
                                                {{ $company->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if(in_array($company->status, ['pending', 'trial_pending_approval']))
                                                    <form action="{{ route('super-admin.companies.approve', $company) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('super-admin.companies.reject', $company) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @elseif($company->status === 'approved')
                                                    <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                            Suspend
                                                        </button>
                                                    </form>
                                                    <a href="#" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        View
                                                    </a>
                                                @elseif($company->status === 'suspended')
                                                    <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                            Activate
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $companies->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No companies</h3>
                        <p class="mt-1 text-sm text-gray-500">No companies have registered yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection