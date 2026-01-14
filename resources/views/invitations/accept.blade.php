<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Accept Invitation - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">
                        {{ config('app.name', 'SaaS Platform') }}
                    </a>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Join {{ $invitation->company->name }}
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    You've been invited by {{ $invitation->inviter->name }} to join as 
                    <span class="font-semibold">{{ $invitation->role->display_name ?? ucfirst(str_replace('_', ' ', $invitation->role->name)) }}</span>
                </p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('invitation.process', $invitation->token) }}" method="POST">
                @csrf

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4">
                    <!-- Invitation Info -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Invitation Details</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Company:</span>
                                <span class="font-medium">{{ $invitation->company->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Invited by:</span>
                                <span class="font-medium">{{ $invitation->inviter->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Your role:</span>
                                <span class="font-medium">
                                    {{ $invitation->role->display_name ?? ucfirst(str_replace('_', ' ', $invitation->role->name)) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">{{ $invitation->email }}</span>
                            </div>
                            @if($invitation->expires_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Expires:</span>
                                    <span class="font-medium text-red-600">
                                        {{ $invitation->expires_at->format('M d, Y') }}
                                        ({{ $invitation->expires_at->diffForHumans() }})
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Your Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Your Full Name *
                        </label>
                        <input type="text" id="name" name="name" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password *
                        </label>
                        <input type="password" id="password" name="password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password *
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the 
                        <a href="#" class="text-blue-600 hover:text-blue-800">Terms of Service</a> 
                        and 
                        <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Accept Invitation & Create Account
                    </button>
                </div>

                <!-- Decline Link -->
                <div class="text-center">
                    <a href="{{ route('invitation.decline', $invitation->token) }}"
                        class="text-sm text-gray-600 hover:text-gray-800"
                        onclick="return confirm('Are you sure you want to decline this invitation?')">
                        Decline Invitation
                    </a>
                </div>
            </form>

            <!-- Already have account -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>