# Ngrok HTTPS Fix Guide

## Vấn đề Mixed Content khi sử dụng ngrok với HTTPS

Khi sử dụng ngrok để test website với HTTPS, bạn có thể gặp lỗi **Mixed Content** vì website cố gắng load các tài nguyên HTTP trong khi trang được serve qua HTTPS.

## Giải pháp đã triển khai

### 1. JavaScript Fix (`public/js/mixed-content-fix.js`)
- Tự động chuyển đổi HTTP URLs thành HTTPS
- Fix localhost URLs thành ngrok domain
- Override fetch và XMLHttpRequest
- Chạy định kỳ để fix các elements mới

### 2. CSS Fix (`public/css/ngrok-https-fix.css`)
- CSS rules để handle mixed content
- Fallback fonts
- Base styles cho fixes

### 3. .htaccess Fix (`public/.htaccess`)
- Force HTTPS redirect
- Content Security Policy
- Security headers

## Cách sử dụng

### Bước 1: Cấu hình ngrok
```bash
# Khởi động ngrok với HTTPS
ngrok http 8000 --scheme=https
```

### Bước 2: Cập nhật .env
```env
APP_URL=https://your-ngrok-domain.ngrok-free.app
APP_ENV=local
APP_DEBUG=true

# Force HTTPS
FORCE_HTTPS=true
```

### Bước 3: Clear cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Bước 4: Restart server
```bash
php artisan serve
```

## Kiểm tra

1. Mở Developer Tools (F12)
2. Kiểm tra Console tab
3. Tìm các message "Mixed Content Fix: ..."
4. Đảm bảo không còn lỗi Mixed Content

## Troubleshooting

### Nếu vẫn có lỗi Mixed Content:
1. Kiểm tra APP_URL trong .env
2. Clear browser cache
3. Kiểm tra ngrok domain có đúng không
4. Restart ngrok và Laravel server

### Nếu jQuery không load:
1. Kiểm tra network tab trong DevTools
2. Đảm bảo jQuery được load từ HTTPS
3. Kiểm tra console errors

### Nếu fonts không load:
1. Kiểm tra font URLs trong network tab
2. Đảm bảo fonts được serve từ HTTPS
3. Kiểm tra CORS headers

## Files đã được tạo/sửa đổi

- `public/js/mixed-content-fix.js` - JavaScript fix script
- `public/css/ngrok-https-fix.css` - CSS fix styles
- `public/.htaccess` - Apache rewrite rules
- `platform/themes/shofy/layouts/base.blade.php` - Thêm script và CSS

## Lưu ý

- Script sẽ chạy tự động khi trang load
- Fix được áp dụng cho tất cả HTTP URLs
- Có giới hạn số lần fix để tránh lặp vô hạn
- Tương thích với tất cả browsers hiện đại
