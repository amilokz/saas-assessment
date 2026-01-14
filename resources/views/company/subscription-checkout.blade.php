@extends('layouts.app')

@section('title', 'Complete Subscription')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Complete Your Subscription</h1>
                    <p class="text-gray-600 mt-2">Enter your payment details to activate your subscription</p>
                </div>

                <div class="mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg mb-4">
                        <h3 class="font-semibold text-blue-800 mb-2">Subscription Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Plan:</span>
                                <span class="font-medium ml-2">{{ $subscription->plan->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Billing Cycle:</span>
                                <span class="font-medium ml-2">{{ ucfirst($subscription->billing_cycle) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-medium ml-2">
                                    ${{ number_format($subscription->amount, 2) }} / 
                                    {{ $subscription->billing_cycle === 'yearly' ? 'year' : 'month' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    Pending Payment
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stripe Payment Element -->
                <div id="payment-element">
                    <!-- Stripe.js will inject the Payment Element here -->
                </div>

                <!-- Display error message -->
                <div id="payment-message" class="hidden text-red-600 mt-4"></div>

                <!-- Submit button -->
                <button id="submit" class="w-full mt-6 bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="button-text">Pay Now</span>
                </button>

                <!-- Back link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('company.subscription') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        ← Back to subscription plans
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    
    const clientSecret = '{{ $clientSecret }}';
    
    const appearance = {
        theme: 'stripe',
        variables: {
            colorPrimary: '#0570de',
            colorBackground: '#ffffff',
            colorText: '#30313d',
            colorDanger: '#df1b41',
            fontFamily: 'Figtree, ui-sans-serif, system-ui',
            spacingUnit: '4px',
            borderRadius: '4px'
        }
    };
    
    const paymentElement = elements.create('payment', {
        appearance: appearance
    });
    
    paymentElement.mount('#payment-element');
    
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const paymentMessage = document.getElementById('payment-message');
    
    submitButton.addEventListener('click', async (e) => {
        e.preventDefault();
        
        submitButton.disabled = true;
        spinner.classList.remove('hidden');
        buttonText.textContent = 'Processing...';
        
        const { error } = await stripe.confirmPayment({
            elements,
            clientSecret,
            confirmParams: {
                return_url: '{{ route('company.subscription') }}?success=true',
            },
        });
        
        if (error) {
            paymentMessage.textContent = error.message;
            paymentMessage.classList.remove('hidden');
            submitButton.disabled = false;
            spinner.classList.add('hidden');
            buttonText.textContent = 'Pay Now';
        }
    });
    
    // Check for success parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        window.location.href = '{{ route('company.subscription') }}';
    }
</script>
<style>
    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 3px solid white;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 8px;
        vertical-align: middle;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush
@endsection