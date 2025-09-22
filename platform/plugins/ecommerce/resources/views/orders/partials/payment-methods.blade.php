@if (is_plugin_active('payment') && $orderAmount)
    @php
        $paymentMethods = apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
            'amount' => format_price($orderAmount, null, true),
            'currency' => strtoupper(get_application_currency()->title),
            'name' => null,
            'selected' => PaymentMethods::getSelectedMethod(),
            'default' => PaymentMethods::getDefaultMethod(),
            'selecting' => PaymentMethods::getSelectingMethod(),
        ]) . PaymentMethods::render();
    @endphp
    
    <input name="currency" type="hidden" value="{{ strtoupper(get_application_currency()->title) }}">
    <input name="amount" type="hidden" value="{{ $orderAmount }}">
    
    <div class="payment-methods-container mb-4">
        <h5 class="checkout-payment-title text-black">{{ __('Payment method') }}</h5>
        
        <div id="payment-error" class="alert alert-danger" style="display: none;"></div>
        <div id="payment-loading" class="text-center" style="display: none;">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Processing payment...</p>
        </div>
        
        <ul class="list-group list_payment_method">
            {{-- Google Pay --}}
            <li class="list-group-item text-center">
                <div id="googlePay-container">
                    <button id="google-pay-btn" class="btn btn-outline-dark w-100 mb-2" data-method="googlePay" disabled>
                        <img src="{{ asset('images/google-pay-logo.svg') }}" style="height:20px" class="me-2">
                        <span id="google-pay-text">Đang kiểm tra Google Pay...</span>
                    </button>
                </div>
            </li>
            
            {{-- Apple Pay --}}
            <li class="list-group-item text-center">
                <div id="applePay-container">
                    <button id="apple-pay-btn" class="btn btn-outline-dark w-100 mb-2" data-method="applePay" disabled>
                        <img src="{{ asset('images/apple-pay-logo.svg') }}" style="height:20px" class="me-2">
                        <span id="apple-pay-text">Đang kiểm tra Apple Pay...</span>
                    </button>
                </div>
            </li>
            
            {{-- Credit Card --}}
            <li class="list-group-item text-center">
                <button id="card-pay-btn" class="btn btn-outline-primary w-100" data-method="card">
                    <img src="{{ asset('images/visa-logo.svg') }}" style="height:20px" class="me-2">
                    Credit Card
                </button>
                <div id="card-container" style="display:none;" class="mt-3">
                    <div id="card-element"></div>
                    <button id="card-submit" class="btn btn-primary w-100 mt-3" style="display:none;">
                        Pay ${{ number_format($orderAmount, 2) }}
                    </button>
                </div>
            </li>
            
            {{-- Divider --}}
            <li class="list-group-item text-center border-0">
                <div class="d-flex align-items-center">
                    <hr class="flex-grow-1">
                    <span class="px-2 fw-bold text-muted">{{ __('OR') }}</span>
                    <hr class="flex-grow-1">
                </div>
            </li>
            
            {{-- Other methods --}}
            {!! $paymentMethods !!}
        </ul>
    </div>
    
    {{-- Load Stripe.js SDK --}}
    <script src="https://js.stripe.com/v3/"></script> 
    <script>
        let stripe;
        let elements;
        let paymentIntent;
        
        function showError(message) {
            const errorDiv = document.getElementById('payment-error');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            hideLoading();
        }
        
        function hideError() {
            document.getElementById('payment-error').style.display = 'none';
        }
        
        function showLoading() {
            document.getElementById('payment-loading').style.display = 'block';
            document.querySelectorAll('button[data-method]').forEach(btn => btn.disabled = true);
        }
        
        function hideLoading() {
            document.getElementById('payment-loading').style.display = 'none';
            document.querySelectorAll('button[data-method]').forEach(btn => btn.disabled = false);
        }
        
        function handlePaymentCancellation(paymentMethod) {
            hideLoading();
            
            // Force hide loading with a small delay to ensure it's hidden
            setTimeout(() => {
                hideLoading();
            }, 100);
            
            // You can add custom logic here for cancellation
            // For example: track analytics, show a message, etc.
        }
        
        async function initializeStripe() {
            if (stripe) return;
            
            try {
                stripe = Stripe('{{ env('STRIPE_PUBLISHABLE_KEY') }}');
            } catch (error) {
                throw new Error('Failed to initialize Stripe: ' + error.message);
            }
        }
        
        async function createPaymentIntent() {
            const response = await fetch("/stripe/create-intent/{{ $token }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            });
            
            const responseText = await response.text();
            
            if (!response.ok) {
                try {
                    const errorData = JSON.parse(responseText);
                    throw new Error(errorData.message || 'Failed to create payment intent');
                } catch (parseError) {
                    throw new Error('Server error: ' + response.status + ' - ' + responseText.substring(0, 200));
                }
            }
            
            try {
                return JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Invalid JSON response: ' + responseText.substring(0, 200));
            }
        }
        
        async function confirmPayment(paymentMethodId, method) {
            // Ensure paymentIntent exists
            if (!paymentIntent || (!paymentIntent.id && !paymentIntent.payment_intent_id)) {
                throw new Error('Payment intent not found. Please try again.');
            }
            
            // Get payment intent ID from either structure
            const paymentIntentId = paymentIntent.id || paymentIntent.payment_intent_id;
            
            const response = await fetch(`/stripe/confirm/{{ $token }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payment_method_id: paymentMethodId,
                    payment_intent_id: paymentIntentId,
                    method: method
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                window.location.href = result.redirect_url;
            } else {
                throw new Error(result.message || 'Payment confirmation failed');
            }
        }
        
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                await initializeStripe();
            } catch (error) {
                showError(error.message);
                return;
            }
            
            // Initialize Google Pay on page load
            async function initializeGooglePay() {
                try {
                    paymentIntent = await createPaymentIntent();
                    
                    const currency = '{{ strtolower(get_application_currency()->title ?? 'usd') }}' || 'usd';
                    const amount = Math.round({{ $orderAmount * 100 }});
                    
                    // Validate currency and amount
                    if (!currency || currency.length !== 3) {
                        throw new Error('Invalid currency: ' + currency);
                    }
                    
                    if (!amount || amount <= 0) {
                        throw new Error('Invalid amount: ' + amount);
                    }
                    
                    // Check if HTTPS
                    if (location.protocol !== 'https:') {
                        throw new Error('Google Pay requires HTTPS. Current protocol: ' + location.protocol);
                    }
                    
                    // Create Payment Request for Google Pay/Apple Pay
                    const paymentRequest = stripe.paymentRequest({
                        country: 'US', // Stripe only supports specific countries
                        currency: currency,
                        total: {
                            label: 'Total',
                            amount: amount,
                        },
                        requestPayerName: true,
                        requestPayerEmail: true,
                        // Disable fallback methods
                        disableWallets: ['link'],
                    });
                    
                    // Check if Google Pay/Apple Pay is available
                    const canMakePayment = await paymentRequest.canMakePayment();
                    
                    // Check specifically for Google Pay
                    const hasGooglePay = canMakePayment && canMakePayment.googlePay;
                    
                    if (hasGooglePay) {
                        // Update the existing button to show it's available
                        const existingBtn = document.getElementById('google-pay-btn');
                        const textElement = document.getElementById('google-pay-text');
                        if (existingBtn) {
                            existingBtn.disabled = false;
                            existingBtn.style.background = '#000';
                            existingBtn.style.color = 'white';
                            existingBtn.style.border = 'none';
                            if (textElement) {
                                textElement.textContent = 'Thanh toán bằng Google Pay';
                            }
                        }
                        
                        // Handle payment method creation - this is the main handler
                        paymentRequest.on('paymentmethod', async (event) => {
                            try {
                                showLoading();
                                await confirmPayment(event.paymentMethod.id, 'google_pay');
                                event.complete('success');
                            } catch (error) {
                                showError('Google Pay error: ' + error.message);
                                event.complete('fail');
                            }
                        });
                        
                        // Handle button click - show the payment request
                        if (existingBtn) {
                            existingBtn.addEventListener('click', async () => {
                                try {
                                    showLoading();
                                    
                                    const result = await paymentRequest.show();
                                    
                                    // Check if user cancelled (no result returned)
                                    if (!result) {
                                        handlePaymentCancellation('Google Pay');
                                        return;
                                    }
                                    
                                    // The paymentmethod event will handle the actual payment
                                    
                                } catch (error) {
                                    hideLoading();
                                    
                                    // Handle cancellation specifically
                                    if (error.message.includes('cancelled') || error.message.includes('canceled')) {
                                        handlePaymentCancellation('Google Pay');
                                        return;
                                    }
                                    
                                    // Handle other errors
                                    let errorMessage = 'Google Pay error: ';
                                    if (error.message.includes('failed')) {
                                        errorMessage += 'Thanh toán thất bại. Vui lòng thử lại';
                                    } else if (error.message.includes('not supported')) {
                                        errorMessage += 'Google Pay không được hỗ trợ trên thiết bị này';
                                    } else {
                                        errorMessage += error.message;
                                    }
                                    
                                    showError(errorMessage);
                                }
                            });
                        }
                    } else {
                        // Show helpful message instead of error
                        const existingBtn = document.getElementById('google-pay-btn');
                        const textElement = document.getElementById('google-pay-text');
                        if (existingBtn) {
                            existingBtn.style.background = '#f5f5f5';
                            existingBtn.style.color = '#666';
                            existingBtn.style.border = '1px solid #ddd';
                            existingBtn.disabled = true;
                            if (textElement) {
                                textElement.textContent = 'Google Pay không khả dụng';
                            }
                        }
                    }
                    
                } catch (error) {
                    showError('Google Pay failed: ' + error.message);
                }
            }
            
            // Initialize Google Pay when page loads
            initializeGooglePay();
            
            // Initialize Apple Pay on page load
            async function initializeApplePay() {
                try {
                    paymentIntent = await createPaymentIntent();
                    
                    // Create Payment Request for Apple Pay
                    const currency = '{{ strtolower(get_application_currency()->title ?? 'usd') }}' || 'usd';
                    
                    const applePaymentRequest = stripe.paymentRequest({
                        country: 'US',
                        currency: currency,
                        total: {
                            label: 'Total',
                            amount: {{ $orderAmount * 100 }},
                        },
                        requestPayerName: true,
                        requestPayerEmail: true,
                        // Disable fallback methods
                        disableWallets: ['link'],
                    });
                    
                    // Check if Apple Pay is available
                    const canMakeApplePayment = await applePaymentRequest.canMakePayment();
                    const hasApplePay = canMakeApplePayment && canMakeApplePayment.applePay;
                    
                    if (hasApplePay) {
                        // Update the existing button to show it's available
                        const existingBtn = document.getElementById('apple-pay-btn');
                        const textElement = document.getElementById('apple-pay-text');
                        if (existingBtn) {
                            existingBtn.disabled = false;
                            existingBtn.style.background = '#000';
                            existingBtn.style.color = 'white';
                            existingBtn.style.border = 'none';
                            if (textElement) {
                                textElement.textContent = 'Thanh toán bằng Apple Pay';
                            }
                        }
                        
                        // Handle button click
                        if (existingBtn) {
                            existingBtn.addEventListener('click', async () => {
                                try {
                                    showLoading();
                                    
                                    const result = await applePaymentRequest.show();
                                    
                                    
                                    if (!result) {
                                        // User cancelled the payment
                                        handlePaymentCancellation('Apple Pay');
                                        return;
                                    }
                                    
                                    if (result.error) {
                                        throw new Error(result.error.message);
                                    }
                                    
                                    if (result.paymentMethod) {
                                        await confirmPayment(result.paymentMethod.id, 'apple_pay');
                                    } else {
                                        throw new Error('No payment method returned');
                                    }
                                    
                                } catch (error) {
                                    hideLoading();
                                    
                                    // Handle cancellation specifically
                                    if (error.message.includes('cancelled') || error.message.includes('canceled')) {
                                        handlePaymentCancellation('Apple Pay');
                                        return;
                                    }
                                    
                                    // Handle other errors
                                    showError('Apple Pay error: ' + error.message);
                                }
                            });
                        }
                        
                    } else {
                        // Show helpful message instead of error
                        const existingBtn = document.getElementById('apple-pay-btn');
                        const textElement = document.getElementById('apple-pay-text');
                        if (existingBtn) {
                            existingBtn.style.background = '#f5f5f5';
                            existingBtn.style.color = '#666';
                            existingBtn.style.border = '1px solid #ddd';
                            existingBtn.disabled = true;
                            if (textElement) {
                                textElement.textContent = 'Apple Pay không khả dụng';
                            }
                        }
                    }
                    
                } catch (error) {
                    showError('Apple Pay failed: ' + error.message);
                }
            }
            
            // Initialize Apple Pay when page loads
            initializeApplePay();
            
            // Card Payment Button
            document.getElementById('card-pay-btn').addEventListener('click', async function() {
                hideError();
                
                const cardContainer = document.getElementById('card-container');
                
                if (cardContainer.style.display === 'none' || !cardContainer.style.display) {
                    showLoading();
                    
                    try {
                        paymentIntent = await createPaymentIntent();
                        
                        
                        if (!paymentIntent || (!paymentIntent.id && !paymentIntent.payment_intent_id)) {
                            throw new Error('Failed to create payment intent for card payment');
                        }
                        
                        elements = stripe.elements({
                            clientSecret: paymentIntent.client_secret,
                        });
                        
                        const cardElement = elements.create('card', {
                            style: {
                                base: {
                                    fontSize: '16px',
                                    color: '#424770',
                                    '::placeholder': {
                                        color: '#aab7c4',
                                    },
                                },
                            },
                        });
                        
                        const cardElementContainer = document.getElementById('card-element');
                        cardElementContainer.innerHTML = '';
                        
                        cardElement.mount(cardElementContainer);
                        
                        cardContainer.style.display = 'block';
                        document.getElementById('card-submit').style.display = 'block';
                        hideLoading();
                        
                        // Handle card submit
                        document.getElementById('card-submit').onclick = async function() {
                            showLoading();
                            
                            try {
                                const {error, paymentMethod} = await stripe.createPaymentMethod({
                                    type: 'card',
                                    card: cardElement,
                                });
                                
                                if (error) {
                                    throw new Error(error.message);
                                }
                                
                                
                                await confirmPayment(paymentMethod.id, 'card');
                                
                            } catch (error) {
                                showError('Card payment failed: ' + error.message);
                            }
                        };
                        
                    } catch (error) {
                        showError('Card setup failed: ' + error.message);
                    }
                } else {
                    cardContainer.style.display = 'none';
                }
            });
        });

        function resetPaymentUI() {
            // Ẩn các container và reset
            document.getElementById('googlePay-container').innerHTML = '';
            document.getElementById('applePay-container').innerHTML = '';
            document.getElementById('card-element').innerHTML = '';

            document.getElementById('card-container').style.display = 'none';
            document.getElementById('card-submit').style.display = 'none';
        }

    </script>
@endif