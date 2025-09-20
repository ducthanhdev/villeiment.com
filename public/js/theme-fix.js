/**
 * Theme Fix Script - Enhanced for ngrok HTTPS
 * Fixes jQuery loading, Mixed Content, and ecommerce functionality
 */
(function() {
    'use strict';
    
    console.log('Theme Fix: Starting...');
    
    let jqueryLoadAttempts = 0;
    const maxAttempts = 3;
    
    // Enhanced jQuery loader with fallbacks
    function loadjQuery() {
        if (typeof jQuery !== 'undefined') {
            console.log('Theme Fix: jQuery already available');
            initializeTheme();
            return;
        }
        
        if (jqueryLoadAttempts >= maxAttempts) {
            console.log('Theme Fix: Max jQuery load attempts reached');
            return;
        }
        
        jqueryLoadAttempts++;
        console.log('Theme Fix: Loading jQuery... (attempt ' + jqueryLoadAttempts + ')');
        
        const script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
        script.onload = function() {
            console.log('Theme Fix: jQuery loaded successfully');
            initializeTheme();
        };
        script.onerror = function() {
            console.log('Theme Fix: Failed to load jQuery, retrying...');
            setTimeout(loadjQuery, 1000);
        };
        document.head.appendChild(script);
    }
    
    // Initialize theme functionality
    function initializeTheme() {
        if (typeof jQuery === 'undefined') {
            console.log('Theme Fix: jQuery not available, retrying...');
            setTimeout(loadjQuery, 500);
            return;
        }
        
        jQuery(document).ready(function() {
            console.log('Theme Fix: jQuery document ready');
            
            // Initialize LazyLoad if available
            if (typeof LazyLoad !== 'undefined') {
                window.lazyLoadInstance = new LazyLoad({
                    elements_selector: '.lazy'
                });
                console.log('Theme Fix: LazyLoad initialized');
            }
            
            // Fix add to cart buttons - multiple selectors
            jQuery(document).off('click', '[data-bb-toggle="add-to-cart"]').on('click', '[data-bb-toggle="add-to-cart"]', function(e) {
                e.preventDefault();
                console.log('Theme Fix: Add to cart button clicked');
                
                const button = jQuery(this);
                const url = button.data('url') || button.attr('href');
                const productId = button.data('id');
                
                if (url && productId) {
                    console.log('Theme Fix: Adding product to cart:', productId);
                    
                    // Show loading state
                    button.prop('disabled', true).addClass('btn-loading');
                    
                    // Make AJAX request
                    jQuery.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            id: productId,
                            qty: 1,
                            _token: jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Theme Fix: Add to cart success');
                            if (response.error) {
                                console.error('Theme Fix: Add to cart error:', response.message);
                            } else {
                                // Update mini cart if available
                                if (response.data && response.data.cart_mini) {
                                    jQuery('.cartmini__area').html(response.data.cart_mini);
                                }
                                if (response.data && response.data.count !== undefined) {
                                    jQuery('[data-bb-value="cart-count"]').text(response.data.count);
                                }
                                
                                // Show success message
                                if (window.Theme && window.Theme.showSuccess) {
                                    window.Theme.showSuccess(response.message || 'Product added to cart');
                                } else {
                                    alert(response.message || 'Product added to cart');
                                }
                                
                                // Auto open mini cart if enabled
                                if (window.themeOptions && window.themeOptions.ecommerce_auto_open_mini_cart === 'yes') {
                                    jQuery('.cartmini__area').addClass('cartmini-opened');
                                    jQuery('.body-overlay').addClass('opened');
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Theme Fix: Add to cart AJAX error:', error);
                            if (window.Theme && window.Theme.showError) {
                                window.Theme.showError('Failed to add product to cart');
                            } else {
                                alert('Failed to add product to cart');
                            }
                        },
                        complete: function() {
                            button.prop('disabled', false).removeClass('btn-loading');
                        }
                    });
                }
            });
            
            // Fix form submit buttons
            jQuery(document).off('click', '.product-form button[type="submit"]').on('click', '.product-form button[type="submit"]', function(e) {
                e.preventDefault();
                console.log('Theme Fix: Product form submit clicked');
                
                const button = jQuery(this);
                const form = button.closest('form');
                
                if (form.length) {
                    console.log('Theme Fix: Submitting product form');
                    form.trigger('submit');
                }
            });
            
            // Fix sticky action buttons
            jQuery(document).off('click', '.sticky-actions-button button').on('click', '.sticky-actions-button button', function(e) {
                e.preventDefault();
                console.log('Theme Fix: Sticky action button clicked');
                
                const button = jQuery(this);
                const form = jQuery('form.product-form');
                
                if (form.length) {
                    if (button.prop('name') === 'add-to-cart') {
                        form.find('button[type="submit"][name="add-to-cart"]').trigger('click');
                    } else if (button.prop('name') === 'checkout') {
                        form.find('button[type="submit"][name="checkout"]').trigger('click');
                    }
                }
            });
            
            console.log('Theme Fix: Ecommerce functionality initialized');
        });
    }
    
    // Start loading jQuery
    loadjQuery();
    
    console.log('Theme Fix: Initialized');
})();
