# 🚀 Complete E-commerce Website in Laravel 10

Một giải pháp **thương mại điện tử hoàn chỉnh** được xây dựng trên **Laravel 10**, tích hợp đầy đủ các tính năng từ cơ bản đến nâng cao như quản lý sản phẩm, thanh toán đa nền tảng, livestream bán hàng, chatbot AI, hệ thống loyalty, và nhiều tính năng hiện đại khác.

---

## 📋 Mục lục

-   [Tổng quan](#-tổng-quan)
-   [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
-   [Tính năng Frontend](#-tính-năng-frontend)
-   [Tính năng Backend/Admin](#-tính-năng-backendadmin)
-   [Tính năng User Dashboard](#-tính-năng-user-dashboard)
-   [Tính năng nâng cao](#-tính-năng-nâng-cao)
-   [Cài đặt](#-cài-đặt)
-   [Cấu hình](#-cấu-hình)
-   [Screenshots](#-screenshots)
-   [License](#-license)

---

## 🎯 Tổng quan

Dự án này là một hệ thống thương mại điện tử đầy đủ với:

-   ✅ **Frontend hiện đại** - Giao diện responsive, SEO-friendly
-   ✅ **Admin Panel mạnh mẽ** - Quản lý toàn diện cửa hàng
-   ✅ **Thanh toán đa nền tảng** - Stripe, PayPal, MoMo, VNPay
-   ✅ **Live Stream Shopping** - Bán hàng trực tiếp với Jitsi Meet
-   ✅ **AI Chatbot** - Hỗ trợ khách hàng thông minh với Groq AI
-   ✅ **Hệ thống Loyalty** - Điểm thưởng và hạng thành viên
-   ✅ **Quản lý vận chuyển** - Hệ thống shipper độc lập
-   ✅ **Cloudinary Integration** - Quản lý hình ảnh trên cloud
-   ✅ **Real-time Chat** - Chat trực tiếp với Pusher
-   ✅ **PWA Support** - Progressive Web App

---

## 🛠️ Công nghệ sử dụng

### Backend Framework & Core

-   **Laravel 10.x** - PHP Framework
-   **PHP 8.1+** - Programming Language
-   **MySQL/MariaDB** - Database
-   **Eloquent ORM** - Database Abstraction

### Frontend Technologies

-   **Blade Templates** - Template Engine
-   **Bootstrap 4** - CSS Framework
-   **jQuery** - JavaScript Library
-   **Vue.js 2** - Progressive JavaScript Framework
-   **Laravel Mix** - Asset Compilation
-   **SASS** - CSS Preprocessor

### Payment Gateways

-   **Stripe** - International Payment
-   **PayPal** - International Payment
-   **MoMo** - Vietnam Payment Gateway
-   **VNPay** - Vietnam Payment Gateway

### Third-party Services

-   **Cloudinary** - Cloud Image Management
-   **Jitsi Meet** - Live Video Streaming
-   **Groq AI** - AI Chatbot Service
-   **Pusher** - Real-time Communication
-   **Laravel Socialite** - Social Authentication (Google, Facebook, GitHub)

### Additional Packages

-   **Laravel Fortify** - Authentication Backend
-   **Laravel Sanctum** - API Authentication
-   **Laravel DomPDF** - PDF Generation
-   **Laravel Newsletter** - Email Newsletter
-   **Unisharp Laravel Filemanager** - File Management
-   **Guzzle HTTP** - HTTP Client

---

## 🎨 Tính năng Frontend

### 1. Trang chủ & Navigation

-   ✅ Trang chủ với banner slider
-   ✅ Menu đa cấp (danh mục sản phẩm)
-   ✅ Tìm kiếm sản phẩm nâng cao
-   ✅ Responsive design cho mọi thiết bị
-   ✅ SEO-friendly URLs và metadata

### 2. Quản lý Sản phẩm

-   ✅ **Danh sách sản phẩm** - Grid view và List view
-   ✅ **Chi tiết sản phẩm** - Thông tin đầy đủ, hình ảnh, reviews
-   ✅ **Lọc sản phẩm** - Theo danh mục, thương hiệu, giá, size, điều kiện
-   ✅ **Tìm kiếm thông minh** - Tìm kiếm theo từ khóa, phân tích ngữ nghĩa
-   ✅ **Sản phẩm nổi bật** - Featured products
-   ✅ **Sản phẩm liên quan** - Related products
-   ✅ **Sản phẩm gợi ý** - AI-powered recommendations

### 3. Giỏ hàng & Thanh toán

-   ✅ **Giỏ hàng** - Thêm, sửa, xóa sản phẩm
-   ✅ **Wishlist** - Danh sách yêu thích
-   ✅ **Checkout** - Quy trình thanh toán đầy đủ
-   ✅ **Mã giảm giá** - Áp dụng coupon
-   ✅ **Tính phí vận chuyển** - Tự động tính toán
-   ✅ **Thanh toán đa nền tảng** - Stripe, PayPal, MoMo, VNPay

### 4. Đánh giá & Bình luận

-   ✅ **Đánh giá sản phẩm** - Rating và review
-   ✅ **Bình luận đa cấp** - Nested comments
-   ✅ **Quản lý đánh giá** - User có thể sửa/xóa

### 5. Blog & Content

-   ✅ **Blog system** - Bài viết, danh mục, tags
-   ✅ **Tìm kiếm blog** - Search và filter
-   ✅ **Bình luận blog** - Comment system
-   ✅ **Newsletter** - Đăng ký nhận tin

### 6. User Features

-   ✅ **Đăng ký/Đăng nhập** - Traditional và Social Login
-   ✅ **Quên mật khẩu** - Password reset
-   ✅ **Theo dõi đơn hàng** - Order tracking
-   ✅ **Liên hệ** - Contact form

### 7. Live Stream Shopping

-   ✅ **Xem live stream** - Real-time video streaming
-   ✅ **Sản phẩm trong live** - Hiển thị sản phẩm đang bán
-   ✅ **Mua ngay từ live** - Quick purchase
-   ✅ **Đếm người xem** - Viewer count

### 8. AI Chatbot

-   ✅ **Chat hỗ trợ** - AI-powered customer support
-   ✅ **Tìm kiếm sản phẩm** - Natural language search
-   ✅ **Gợi ý sản phẩm** - Product recommendations

---

## 👨‍💼 Tính năng Backend/Admin

### 1. Dashboard & Analytics

-   ✅ **Dashboard tổng quan** - Thống kê doanh thu, đơn hàng, sản phẩm
-   ✅ **Biểu đồ doanh thu** - Income charts
-   ✅ **Thống kê người dùng** - User statistics
-   ✅ **Thống kê sản phẩm** - Product analytics

### 2. Quản lý Sản phẩm

-   ✅ **CRUD sản phẩm** - Create, Read, Update, Delete
-   ✅ **Upload hình ảnh** - Cloudinary integration
-   ✅ **Quản lý danh mục** - Category management (đa cấp)
-   ✅ **Quản lý thương hiệu** - Brand management
-   ✅ **Quản lý tồn kho** - Stock management
-   ✅ **Sản phẩm nổi bật** - Featured products

### 3. Quản lý Đơn hàng

-   ✅ **Danh sách đơn hàng** - Order list
-   ✅ **Chi tiết đơn hàng** - Order details
-   ✅ **Cập nhật trạng thái** - Status update
-   ✅ **Xuất PDF** - PDF invoice generation
-   ✅ **Quản lý vận chuyển** - Shipping management

### 4. Quản lý Người dùng

-   ✅ **Danh sách users** - User management
-   ✅ **Phân quyền** - Role-based access (Admin, User, Shipper)
-   ✅ **Quản lý profile** - User profiles

### 5. Quản lý Nội dung

-   ✅ **Banner management** - Quản lý banner
-   ✅ **Blog management** - Quản lý blog posts
-   ✅ **Category & Tags** - Quản lý danh mục và tags
-   ✅ **Media manager** - File manager

### 6. Marketing & Promotions

-   ✅ **Coupon system** - Mã giảm giá
-   ✅ **Discount management** - Quản lý giảm giá
-   ✅ **Newsletter** - Email marketing

### 7. Cài đặt

-   ✅ **General settings** - Cài đặt chung
-   ✅ **Shipping settings** - Cài đặt vận chuyển
-   ✅ **Payment settings** - Cài đặt thanh toán
-   ✅ **Email settings** - Cài đặt email

### 8. Live Stream Management

-   ✅ **Tạo live stream** - Start live shopping
-   ✅ **Quản lý live stream** - Manage active streams
-   ✅ **Kết thúc live** - End live stream
-   ✅ **Thống kê viewer** - Viewer statistics

### 9. Notifications & Messages

-   ✅ **Thông báo** - Notification system
-   ✅ **Tin nhắn** - Message management
-   ✅ **Real-time updates** - Pusher integration

---

## 👤 Tính năng User Dashboard

### 1. Profile Management

-   ✅ **Thông tin cá nhân** - Personal information
-   ✅ **Đổi mật khẩu** - Change password
-   ✅ **Cập nhật avatar** - Profile picture

### 2. Order Management

-   ✅ **Lịch sử đơn hàng** - Order history
-   ✅ **Chi tiết đơn hàng** - Order details
-   ✅ **Theo dõi đơn hàng** - Order tracking
-   ✅ **Hủy đơn hàng** - Cancel order
-   ✅ **Đánh giá giao hàng** - Delivery feedback

### 3. Reviews & Comments

-   ✅ **Đánh giá sản phẩm** - Product reviews
-   ✅ **Sửa/Xóa đánh giá** - Edit/Delete reviews
-   ✅ **Bình luận blog** - Blog comments
-   ✅ **Quản lý bình luận** - Comment management

### 4. Wishlist

-   ✅ **Danh sách yêu thích** - Wishlist management
-   ✅ **Thêm/Xóa** - Add/Remove items

---

## 🚀 Tính năng nâng cao

### 1. Thanh toán đa nền tảng (Multi-payment Gateway)

#### Stripe

-   ✅ International credit/debit cards
-   ✅ Webhook support
-   ✅ VND to USD conversion
-   ✅ Secure payment processing

#### PayPal

-   ✅ PayPal account payment
-   ✅ Credit card via PayPal
-   ✅ Sandbox & Production mode
-   ✅ Webhook integration

#### MoMo (Vietnam)

-   ✅ MoMo wallet payment
-   ✅ QR code payment
-   ✅ IPN (Instant Payment Notification)
-   ✅ VND support

#### VNPay (Vietnam)

-   ✅ Bank transfer
-   ✅ Credit card
-   ✅ E-wallet
-   ✅ IPN support

**Tính năng:**

-   ✅ Unified payment interface
-   ✅ Automatic currency conversion
-   ✅ Payment status tracking
-   ✅ Refund support
-   ✅ Payment history

### 2. Live Stream Shopping

**Công nghệ:** Jitsi Meet (Open-source, Free)

**Tính năng:**

-   ✅ **Admin phát live** - Start live stream với camera/microphone
-   ✅ **Khách hàng xem** - Real-time video streaming
-   ✅ **Sản phẩm trong live** - Hiển thị sản phẩm đang bán
-   ✅ **Mua ngay** - Quick purchase từ live stream
-   ✅ **Đếm người xem** - Real-time viewer count
-   ✅ **Chat trong live** - Live chat support
-   ✅ **Chia sẻ màn hình** - Screen sharing
-   ✅ **Chất lượng tự động** - Auto quality adjustment

**Cấu hình:**

-   Sử dụng Jitsi Meet public instance (miễn phí)
-   Có thể tự host Jitsi server
-   Không cần API key phức tạp

### 3. AI Chatbot với Groq AI

**Công nghệ:** Groq AI (GPT-OSS-120B model)

**Tính năng:**

-   ✅ **Chat tự nhiên** - Natural language processing
-   ✅ **Tìm kiếm sản phẩm** - Intelligent product search
-   ✅ **Gợi ý sản phẩm** - AI-powered recommendations
-   ✅ **Hỗ trợ khách hàng** - Customer support
-   ✅ **Phân tích ngữ nghĩa** - Semantic analysis
-   ✅ **Đa ngôn ngữ** - Multi-language support (Vietnamese focus)

**Cách hoạt động:**

1. User nhập câu hỏi/tìm kiếm
2. System tìm kiếm sản phẩm phù hợp
3. AI phân tích và trả lời thông minh
4. Gợi ý sản phẩm với link chi tiết

### 4. Cloudinary Image Management

**Tính năng:**

-   ✅ **Upload tự động** - Auto upload to Cloudinary
-   ✅ **CDN delivery** - Fast image delivery
-   ✅ **Image optimization** - Auto optimization
-   ✅ **Transformations** - Resize, crop, format conversion
-   ✅ **Fallback** - Local storage fallback
-   ✅ **Tích hợp toàn bộ** - Products, Categories, Posts, Banners

### 5. Hệ thống Loyalty & Points

**Tính năng:**

-   ✅ **Điểm thưởng** - Loyalty points system
-   ✅ **Hạng thành viên** - Tier system (Bronze, Silver, Gold, Platinum)
-   ✅ **Tự động cập nhật** - Auto sync after order
-   ✅ **Lịch sử điểm** - Points history
-   ✅ **Ưu đãi theo hạng** - Tier-based benefits

**Cấu hình:**

-   Points per VND spent
-   Tier requirements (orders, spending)
-   Customizable tiers

### 6. Quản lý Vận chuyển (Shipper System)

**Tính năng:**

-   ✅ **Dashboard shipper** - Shipper dashboard
-   ✅ **Nhận đơn hàng** - Accept delivery
-   ✅ **Cập nhật tiến độ** - Update progress
-   ✅ **Hoàn thành giao hàng** - Complete delivery
-   ✅ **Hủy giao hàng** - Cancel delivery
-   ✅ **Đánh giá shipper** - Shipper reviews
-   ✅ **Lịch sử giao hàng** - Delivery history

**Quy trình:**

1. Admin tạo đơn hàng → Tạo delivery
2. Shipper xem và nhận đơn
3. Shipper cập nhật tiến độ
4. Hoàn thành → Customer đánh giá

### 7. Real-time Chat

**Công nghệ:** Laravel Broadcasting + Pusher

**Tính năng:**

-   ✅ **Chat trực tiếp** - Real-time messaging
-   ✅ **Notification** - Real-time notifications
-   ✅ **Multi-user** - Multiple users support
-   ✅ **Message history** - Chat history

### 8. Tìm kiếm thông minh (Smart Search)

**Tính năng:**

-   ✅ **Full-text search** - Tìm kiếm toàn văn
-   ✅ **Filter nâng cao** - Advanced filters
-   ✅ **Phân tích ngữ nghĩa** - Semantic analysis
-   ✅ **Gợi ý tìm kiếm** - Search suggestions
-   ✅ **Tìm kiếm theo giá** - Price range search
-   ✅ **Tìm kiếm theo size** - Size filter
-   ✅ **Tìm kiếm theo thương hiệu** - Brand filter

### 9. Progressive Web App (PWA)

**Tính năng:**

-   ✅ **Service Worker** - Offline support
-   ✅ **App Manifest** - Install as app
-   ✅ **Push Notifications** - Browser notifications
-   ✅ **Offline Mode** - Work offline

### 10. Social Authentication

**Tính năng:**

-   ✅ **Google Login** - Google OAuth
-   ✅ **Facebook Login** - Facebook OAuth
-   ✅ **GitHub Login** - GitHub OAuth
-   ✅ **Traditional Login** - Email/Password

---

## 📦 Cài đặt

### Yêu cầu hệ thống

-   PHP >= 8.1
-   Composer
-   Node.js & NPM
-   MySQL/MariaDB
-   Web Server (Apache/Nginx)

### Bước 1: Clone Repository

```bash
git clone https://github.com/KienQuocVn/Shopfy_PHP.git
cd Ecommerce-in-laravel
```

### Bước 2: Cài đặt Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Bước 3: Cấu hình Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Bước 4: Cấu hình Database

Chỉnh sửa file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Bước 5: Chạy Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
```

### Bước 6: Tạo Storage Link

```bash
php artisan storage:link
```

### Bước 7: Compile Assets

```bash
# Development
npm run dev

# Production
npm run prod
```

### Bước 8: Chạy Server

```bash
php artisan serve
```

Truy cập: `http://127.0.0.1:8000`

---

## ⚙️ Cấu hình

### 1. Payment Gateways

#### Stripe

```env
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret
STRIPE_VND_TO_USD_RATE=24000
```

#### PayPal

```env
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_secret
PAYPAL_MODE=sandbox
PAYPAL_VND_TO_USD_RATE=28000
```

#### MoMo

```env
MOMO_PARTNER_CODE=your_partner_code
MOMO_ACCESS_KEY=your_access_key
MOMO_SECRET_KEY=your_secret_key
MOMO_ENDPOINT=https://test-payment.momo.vn
MOMO_REDIRECT_URL=http://your-domain.com/payments/momo/return
MOMO_IPN_URL=http://your-domain.com/webhooks/momo/ipn
```

#### VNPay

```env
VNP_TMN_CODE=your_tmn_code
VNP_HASH_SECRET=your_hash_secret
VNP_PAYMENT_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNP_RETURN_URL=http://your-domain.com/payments/vnpay/return
```

### 2. Cloudinary

```env
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
CLOUDINARY_UPLOAD_PRESET=your_upload_preset
```

### 3. Jitsi Meet (Live Stream)

```env
JITSI_DOMAIN=meet.jit.si
```

**Lưu ý:** Có thể tự host Jitsi Meet server nếu muốn.

### 4. Groq AI (Chatbot)

```env
GROQ_API_KEY=your_groq_api_key
GROQ_BASE_URL=https://api.groq.com/openai/v1
GROQ_MODEL=openai/gpt-oss-120b
```

### 5. Pusher (Real-time)

```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### 6. Social Authentication

#### Google

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_secret
GOOGLE_REDIRECT_URI=http://your-domain.com/auth/google/callback
```

#### Facebook

```env
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_secret
FACEBOOK_REDIRECT_URI=http://your-domain.com/auth/facebook/callback
```

#### GitHub

```env
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_secret
GITHUB_REDIRECT_URI=http://your-domain.com/auth/github/callback
```

### 7. Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 👤 Tài khoản mặc định

### Admin

-   **Email:** `admin@gmail.com`
-   **Password:** `1111`

### User (sau khi seed)

-   Tạo tài khoản mới hoặc đăng ký

---

## 📸 Screenshots

### Admin Panel

![Admin Dashboard](https://user-images.githubusercontent.com/29488275/90719413-13b82200-e2d4-11ea-8ca0-f0e5551c4c9d.png)

### Product Management

![Products](https://user-images.githubusercontent.com/29488275/90719534-61348f00-e2d4-11ea-8a81-409daee0ad94.png)

### User Dashboard

![User Dashboard](https://user-images.githubusercontent.com/29488275/90719563-7a3d4000-e2d4-11ea-9e6a-56caac13b146.png)

---

## 🗂️ Cấu trúc dự án

```
Ecommerce-in-laravel/
├── app/
│   ├── Console/
│   ├── Events/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── CartController.php
│   │   │   ├── ChatController.php
│   │   │   ├── LiveStreamController.php
│   │   │   ├── OrderController.php
│   │   │   ├── PaymentStartController.php
│   │   │   └── ...
│   │   ├── Middleware/
│   │   └── Helpers.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── LiveStream.php
│   │   └── ...
│   ├── Payments/
│   │   ├── Contracts/
│   │   └── Gateways/
│   │       ├── StripeGateway.php
│   │       ├── PaypalGateway.php
│   │       ├── MomoGateway.php
│   │       └── VnpayGateway.php
│   └── Services/
│       ├── CloudinaryService.php
│       ├── GroqClient.php
│       ├── LoyaltyService.php
│       ├── PaymentService.php
│       └── ProductSearchService.php
├── config/
│   ├── services.php
│   ├── cloudinary.php
│   ├── loyalty.php
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── views/
│   │   ├── backend/
│   │   ├── frontend/
│   │   └── ...
│   ├── js/
│   └── sass/
└── routes/
    └── web.php
```

---

## 🔒 Bảo mật

-   ✅ **CSRF Protection** - Laravel CSRF tokens
-   ✅ **XSS Protection** - Blade escaping
-   ✅ **SQL Injection Protection** - Eloquent ORM
-   ✅ **Authentication** - Laravel Fortify
-   ✅ **Authorization** - Role-based access control
-   ✅ **Password Hashing** - Bcrypt
-   ✅ **HTTPS Support** - SSL/TLS encryption
-   ✅ **Input Validation** - Form validation
-   ✅ **Rate Limiting** - API rate limiting

---

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter TestName
```

---

## 📝 API Documentation

### Live Stream API

#### Get Active Stream Status

```
GET /api/live-stream/status
```

#### Join Stream

```
POST /api/live-stream/{id}/join
```

#### Leave Stream

```
POST /api/live-stream/{id}/leave
```

### Payment API

#### Start Payment

```
GET /payments/{provider}/start
```

#### Payment Return

```
GET /payments/{provider}/return
```

#### Webhooks

```
POST /webhooks/{provider}
```

---

## 🤝 Đóng góp

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Tác giả

**Kiều Kiến Quốc**

-   Email: kieukienquocvn@gmail.com
-   GitHub: [@KienQuocVn](https://github.com/KienQuocVn)

---

## 🙏 Lời cảm ơn

-   Laravel Community
-   All contributors
-   Open source libraries used in this project

---

## 📞 Hỗ trợ

Nếu bạn gặp vấn đề hoặc có câu hỏi, vui lòng:

-   Tạo issue trên GitHub
-   Liên hệ qua email: kieukienquocvn@gmail.com

---

## 🔄 Changelog

### Version 1.0.0

-   ✅ Initial release
-   ✅ Complete e-commerce functionality
-   ✅ Multi-payment gateway integration
-   ✅ Live stream shopping
-   ✅ AI chatbot
-   ✅ Cloudinary integration
-   ✅ Loyalty system
-   ✅ Shipper management

---

**⭐ Nếu dự án này hữu ích, hãy cho một star! ⭐**
