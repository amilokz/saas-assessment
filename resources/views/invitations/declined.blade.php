<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invitation Declined - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <div class="flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-3xl font-extrabold text-gray-900">
                        {{ config('app.name', 'SaaS Platform') }}
                    </h2>
                </a>
            </div>

            <div class="bg-white py-8 px-6 shadow-lg rounded-lg sm:px-10">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <!-- Title -->
                <h3 class="text-center text-2xl font-bold text-gray-900 mb-2">
                    Invitation Declined
                </h3>

                <!-- Message -->
                <p class="text-center text-gray-600 mb-8">
                    You have successfully declined the invitation to join 
                    <span class="font-semibold text-gray-800">{{ $invitation->company->name ?? 'the company' }}</span>.
                </p>

                <!-- Details Box -->
                @if(isset($invitation))
                <div class="bg-gray-50 rounded-lg p-4 mb-8">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Invitation Details:</h4>
                    <div class="space-y-2 text-sm">
                        @if($invitation->company)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Company:</span>
                                <span class="font-medium">{{ $invitation->company->name }}</span>
                            </div>
                        @endif
                        @if($invitation->inviter)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Invited by:</span>
                                <span class="font-medium">{{ $invitation->inviter->name }}</span>
                            </div>
                        @endif
                        @if($invitation->email)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">{{ $invitation->email }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-red-600">Declined</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ now()->format('F d, Y \a\t g:i A') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Information -->
                <div class="text-center text-gray-500 text-sm mb-8">
                    <p class="mb-2">
                        The inviter has been notified that you declined their invitation.
                    </p>
                    <p>
                        If this was a mistake, please contact the person who invited you.
                    </p>
                </div>

                <!-- Actions -->
                <div class="space-y-4">
                    <!-- Go Home Button -->
                    <a href="{{ route('home') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Go to Homepage
                    </a>

                    <!-- Login Button -->
                    @guest
                    <a href="{{ route('login') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Sign In to Your Account
                    </a>
                    @endguest

                    <!-- Register Button -->
                    @guest
                    <p class="text-center text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register.company') }}" class="font-medium text-blue-600 hover:text-blue-800">
                            Register your company
                        </a>
                    </p>
                    @endguest
                </div>

                <!-- Contact Support -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">
                        Need help?
                        <a href="mailto:support@example.com" class="font-medium text-gray-600 hover:text-gray-800">
                            Contact Support
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} {{ config('app.name', 'SaaS Platform') }}. All rights reserved.
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    <a href="#" class="hover:text-gray-600">Privacy Policy</a> • 
                    <a href="#" class="hover:text-gray-600">Terms of Service</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Animation -->
    <script>
        // Add some subtle animation
        document.addEventListener('DOMContentLoaded', function() {
            const icon = document.querySelector('svg');
            icon.classList.add('animate-bounce');
            
            setTimeout(() => {
                icon.classList.remove('animate-bounce');
            }, 1000);
        });
    </script>
</body>
</html>