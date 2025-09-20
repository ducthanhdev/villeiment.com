/**
 * Mixed Content Fix for ngrok HTTPS testing
 * Fixes HTTP URLs and localhost URLs to work with HTTPS
 */
(function() {
    'use strict';
    
    const currentOrigin = window.location.origin;
    console.log('Mixed Content Fix: Current origin:', currentOrigin);
    
    // Biến đếm để tránh lặp vô hạn
    let fixCount = 0;
    const maxFixes = 5;
    
    // Function chính để fix tất cả Mixed Content
    function fixAllMixedContent() {
        if (fixCount >= maxFixes) {
            console.log('Mixed Content Fix: Stopped (max attempts reached)');
            return;
        }
        
        fixCount++;
        console.log('Mixed Content Fix: Running fix... (attempt ' + fixCount + ')');
        
        // Fix localhost URLs
        document.querySelectorAll('link[href*="localhost"], link[href*="127.0.0.1"]').forEach(function(link) {
            let newHref = link.href.replace(/https?:\/\/(localhost|127\.0\.0\.1)[^\/]*/, currentOrigin);
            if (newHref !== link.href) {
                link.href = newHref;
                console.log('Fixed localhost link:', link.href);
            }
        });
        
        document.querySelectorAll('script[src*="localhost"], script[src*="127.0.0.1"]').forEach(function(script) {
            let newSrc = script.src.replace(/https?:\/\/(localhost|127\.0\.0\.1)[^\/]*/, currentOrigin);
            if (newSrc !== script.src) {
                script.src = newSrc;
                console.log('Fixed localhost script:', script.src);
            }
        });
        
        document.querySelectorAll('img[src*="localhost"], img[src*="127.0.0.1"]').forEach(function(img) {
            let newSrc = img.src.replace(/https?:\/\/(localhost|127\.0\.0\.1)[^\/]*/, currentOrigin);
            if (newSrc !== img.src) {
                img.src = newSrc;
                console.log('Fixed localhost image:', img.src);
            }
        });
        
        // Fix HTTP URLs
        document.querySelectorAll('link[href^="http://"]').forEach(function(link) {
            let newHref = link.href.replace('http://', 'https://');
            if (newHref !== link.href) {
                link.href = newHref;
                console.log('Fixed HTTP link:', link.href);
            }
        });
        
        document.querySelectorAll('script[src^="http://"]').forEach(function(script) {
            let newSrc = script.src.replace('http://', 'https://');
            if (newSrc !== script.src) {
                script.src = newSrc;
                console.log('Fixed HTTP script:', script.src);
            }
        });
        
        document.querySelectorAll('img[src^="http://"]').forEach(function(img) {
            let newSrc = img.src.replace('http://', 'https://');
            if (newSrc !== img.src) {
                img.src = newSrc;
                console.log('Fixed HTTP image:', img.src);
            }
        });
        
        document.querySelectorAll('a[href^="http://"]').forEach(function(link) {
            let newHref = link.href.replace('http://', 'https://');
            if (newHref !== link.href) {
                link.href = newHref;
                console.log('Fixed HTTP navigation link:', link.href);
            }
        });
        
        document.querySelectorAll('form[action^="http://"]').forEach(function(form) {
            let newAction = form.action.replace('http://', 'https://');
            if (newAction !== form.action) {
                form.action = newAction;
                console.log('Fixed HTTP form action:', form.action);
            }
        });
        
        // Fix background images and styles
        document.querySelectorAll('*').forEach(function(element) {
            const style = window.getComputedStyle(element);
            const bgImage = style.backgroundImage;
            if (bgImage && (bgImage.includes('localhost') || bgImage.includes('127.0.0.1') || bgImage.includes('http://'))) {
                let newBgImage = bgImage.replace(/https?:\/\/(localhost|127\.0\.0\.1)[^\/]*/, currentOrigin);
                newBgImage = newBgImage.replace('http://', 'https://');
                if (newBgImage !== bgImage) {
                    element.style.backgroundImage = newBgImage;
                    console.log('Fixed background image:', element.style.backgroundImage);
                }
            }
        });
    }
    
    // Override fetch để fix URLs
    const originalFetch = window.fetch;
    window.fetch = function(url, options) {
        if (typeof url === 'string') {
            // Fix ngrok domain HTTP to HTTPS
            if (url.includes(window.location.hostname) && url.startsWith('http://')) {
                url = url.replace('http://', 'https://');
                console.log('Fixed fetch ngrok HTTP to HTTPS URL:', url);
            }
            // Fix localhost URLs
            if (url.includes('localhost') || url.includes('127.0.0.1')) {
                url = url.replace(/https?:\/\/(localhost|127\.0\.0\.1)[^\/]*/, window.location.origin);
                console.log('Fixed fetch localhost URL:', url);
            }
            // Fix any HTTP URLs
            if (url.startsWith('http://')) {
                url = url.replace('http://', 'https://');
                console.log('Fixed fetch HTTP to HTTPS URL:', url);
            }
        }
        return originalFetch.call(this, url, options);
    };
    
    // Override XMLHttpRequest để fix URLs
    const originalXHROpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        if (typeof url === 'string') {
            // Fix ngrok domain HTTP to HTTPS
            if (url.includes(window.location.hostname) && url.startsWith('http://')) {
                url = url.replace('http://', 'https://');
                console.log('Fixed XHR ngrok HTTP to HTTPS URL:', url);
            }
            // Fix localhost URLs
            if (url.includes('localhost') || url.includes('127.0.0.1')) {
                url = url.replace(/https?:\/\/(localhost|127\.0\.0\.1)[^\/]*/, window.location.origin);
                console.log('Fixed XHR localhost URL:', url);
            }
            // Fix any HTTP URLs
            if (url.startsWith('http://')) {
                url = url.replace('http://', 'https://');
                console.log('Fixed XHR HTTP to HTTPS URL:', url);
            }
        }
        return originalXHROpen.call(this, method, url, async, user, password);
    };
    
    // Chạy fix ngay lập tức
    fixAllMixedContent();
    
    // Chạy lại khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixAllMixedContent);
    } else {
        fixAllMixedContent();
    }
    
    // Chạy định kỳ để fix các elements được thêm sau
    setInterval(fixAllMixedContent, 2000);
    
    // Chạy fix khi window load
    window.addEventListener('load', fixAllMixedContent);
    
    console.log('Mixed Content Fix: Initialized successfully');
})();
