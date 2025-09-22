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
                <button id="google-pay-btn" class="btn btn-outline-dark w-100 mb-2" data-method="googlePay">
                    <img src="{{ asset('images/google-pay-logo.svg') }}" style="height:20px" class="me-2">
                    Google Pay
                </button>
                <div id="googlePay-container"></div>
            </li>
            
            {{-- Apple Pay --}}
            <li class="list-group-item text-center">
                <button id="apple-pay-btn" class="btn btn-outline-dark w-100 mb-2" data-method="applePay">
                    <img src="{{ asset('images/apple-pay-logo.svg') }}" style="height:20px" class="me-2">
                    Apple Pay
                </button>
                <div id="applePay-container"></div>
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
            const response = await fetch(`/stripe/confirm/{{ $token }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payment_method_id: paymentMethodId,
                    payment_intent_id: paymentIntent.id,
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
            
            // Google Pay Button
            document.getElementById('google-pay-btn').addEventListener('click', async function() {
                hideError();
                showLoading();
                
                try {
                    paymentIntent = await createPaymentIntent();
                    
                    // Debug currency and amount
                    const currency = '{{ strtolower(get_application_currency()->title ?? 'usd') }}';
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
                    
                    const container = document.getElementById('googlePay-container');
                    container.innerHTML = '';
                    
                    // Check if Google Pay/Apple Pay is available
                    const canMakePayment = await paymentRequest.canMakePayment();
                    
                    
                    // Check specifically for Google Pay
                    const hasGooglePay = canMakePayment && canMakePayment.googlePay;
                    const hasApplePay = canMakePayment && canMakePayment.applePay;
                    
                    if (hasGooglePay) {
                        
                        // Create custom Google Pay button
                        container.innerHTML = `
                            <button id="google-pay-custom-btn" style="
                                width: 100%;
                                height: 48px;
                                background: #000;
                                color: white;
                                border: none;
                                border-radius: 8px;
                                font-size: 16px;
                                font-weight: 500;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 8px;
                            ">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Pay with Google Pay
                            </button>
                        `;
                        
                        hideLoading();
                        
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
                        
                        // Handle custom button click - just show the payment request
                        document.getElementById('google-pay-custom-btn').addEventListener('click', async () => {
                            try {
                                showLoading();
                                
                                const result = await paymentRequest.show();
                                
                                // The paymentmethod event will handle the actual payment
                                
                            } catch (error) {
                                console.error('Google Pay show() error:', error);
                                
                                // More specific error messages
                                let errorMessage = 'Google Pay error: ';
                                if (error.message.includes('cancelled')) {
                                    errorMessage += 'Thanh toán đã bị hủy bởi người dùng';
                                } else if (error.message.includes('failed')) {
                                    errorMessage += 'Thanh toán thất bại. Vui lòng thử lại';
                                } else if (error.message.includes('not supported')) {
                                    errorMessage += 'Google Pay không được hỗ trợ trên thiết bị này';
                                } else {
                                    errorMessage += error.message;
                                }
                                
                                showError(errorMessage);
                                hideLoading();
                            }
                        });
                    } else {
                        hideLoading();
                        
                        // Show helpful message instead of error
                        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                        const message = isMobile ? 
                            'Google Pay không khả dụng trên thiết bị này' : 
                            'Google Pay chỉ khả dụng trên mobile devices';
                        const suggestion = isMobile ? 
                            'Vui lòng sử dụng phương thức thanh toán khác' : 
                            'Trên desktop, hãy sử dụng Credit Card';
                            
                        container.innerHTML = `
                            <div style="
                                padding: 16px;
                                border: 1px solid #e0e0e0;
                                border-radius: 8px;
                                background: #f9f9f9;
                                text-align: center;
                                color: #666;
                            ">
                                <p style="margin: 0 0 8px 0; font-weight: 500;">${message}</p>
                                <p style="margin: 0; font-size: 14px;">${suggestion}</p>
                            </div>
                        `;
                    }
                    
                } catch (error) {
                    showError('Google Pay failed: ' + error.message);
                }
            });
            
            // Apple Pay Button  
            document.getElementById('apple-pay-btn').addEventListener('click', async function() {
                hideError();
                showLoading();
                
                try {
                    paymentIntent = await createPaymentIntent();
                    
                    // Create Payment Request for Apple Pay
                    const applePaymentRequest = stripe.paymentRequest({
                        country: 'US',
                        currency: '{{ strtolower(get_application_currency()->title ?? 'usd') }}',
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
                    
                    console.log('Apple Pay canMakePayment result:', canMakeApplePayment);
                    console.log('Apple Pay canMakePayment details:', {
                        googlePay: canMakeApplePayment?.googlePay,
                        applePay: canMakeApplePayment?.applePay,
                        availableMethods: Object.keys(canMakeApplePayment || {})
                    });
                    
                    // Check specifically for Apple Pay
                    const hasApplePay = canMakeApplePayment && canMakeApplePayment.applePay;
                    
                    console.log('Has Apple Pay:', hasApplePay);
                    
                    if (hasApplePay) {
                        console.log('Apple Pay is available, creating button');
                        
                        // Create custom Apple Pay button
                        const container = document.getElementById('applePay-container');
                        container.innerHTML = `
                            <button id="apple-pay-custom-btn" style="
                                width: 100%;
                                height: 48px;
                                background: #000;
                                color: white;
                                border: none;
                                border-radius: 8px;
                                font-size: 16px;
                                font-weight: 500;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 8px;
                            ">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                </svg>
                                Pay with Apple Pay
                            </button>
                        `;
                        
                        hideLoading();
                        
                        // Handle custom button click
                        document.getElementById('apple-pay-custom-btn').addEventListener('click', async () => {
                            try {
                                showLoading();
                                console.log('Showing Apple Pay request...');
                                
                                const result = await applePaymentRequest.show();
                                console.log('Apple Pay result:', result);
                                console.log('Apple Pay result type:', typeof result);
                                console.log('Apple Pay result keys:', result ? Object.keys(result) : 'null/undefined');
                                
                                if (!result) {
                                    throw new Error('Payment request was cancelled or failed');
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
                                console.error('Apple Pay error:', error);
                                showError('Apple Pay error: ' + error.message);
                                hideLoading();
                            }
                        });
                        
                        // Handle payment method creation
                        applePaymentRequest.on('paymentmethod', async (event) => {
                            try {
                                await confirmPayment(event.paymentMethod.id, 'apple_pay');
                                event.complete('success');
                            } catch (error) {
                                showError('Apple Pay error: ' + error.message);
                                event.complete('fail');
                            }
                        });
                    } else {
                        hideLoading();
                        console.log('Apple Pay not available, showing fallback message');
                        
                        // Show helpful message instead of error
                        const container = document.getElementById('applePay-container');
                        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                        const message = isMobile ? 
                            'Apple Pay không khả dụng trên thiết bị này' : 
                            'Apple Pay chỉ khả dụng trên mobile devices';
                        const suggestion = isMobile ? 
                            'Vui lòng sử dụng phương thức thanh toán khác' : 
                            'Trên desktop, hãy sử dụng Credit Card';
                            
                        container.innerHTML = `
                            <div style="
                                padding: 16px;
                                border: 1px solid #e0e0e0;
                                border-radius: 8px;
                                background: #f9f9f9;
                                text-align: center;
                                color: #666;
                            ">
                                <p style="margin: 0 0 8px 0; font-weight: 500;">${message}</p>
                                <p style="margin: 0; font-size: 14px;">${suggestion}</p>
                            </div>
                        `;
                    }
                    
                } catch (error) {
                    showError('Apple Pay failed: ' + error.message);
                }
            });
            
            // Card Payment Button
            document.getElementById('card-pay-btn').addEventListener('click', async function() {
                hideError();
                
                const cardContainer = document.getElementById('card-container');
                
                if (cardContainer.style.display === 'none' || !cardContainer.style.display) {
                    showLoading();
                    
                    try {
                        paymentIntent = await createPaymentIntent();
                        
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