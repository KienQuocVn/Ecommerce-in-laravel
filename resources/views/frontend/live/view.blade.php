@extends('frontend.layouts.master')
@section('title','Live Shopping - ' . ($stream->title ?? 'Đang phát trực tiếp'))

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .live-shopping-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
        position: relative;
    }

    .live-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.95) 0%, rgba(26, 26, 26, 0.95) 100%);
        backdrop-filter: blur(10px);
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        border-bottom: 2px solid rgba(255, 0, 0, 0.3);
    }

    .live-badge {
        background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
        animation: pulse-live 2s infinite;
    }

    .live-dot {
        width: 10px;
        height: 10px;
        background: white;
        border-radius: 50%;
        animation: blink 1s infinite;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(0.8);
        }
    }

    @keyframes pulse-live {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(255, 0, 0, 0.6);
        }
    }

    .video-container {
        flex: 1;
        position: relative;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #zego-video-container {
        width: 100%;
        height: 100vh;
        position: relative;
    }

    #zego-video-container video {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .products-sidebar {
        width: 380px;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        padding: 25px;
        overflow-y: auto;
        max-height: 100vh;
        box-shadow: -5px 0 20px rgba(0, 0, 0, 0.5);
    }

    .products-sidebar h4 {
        color: white;
        margin-bottom: 25px;
        font-size: 20px;
        font-weight: 600;
        text-align: center;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(255, 107, 107, 0.3);
    }

    .product-item {
        background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
        padding: 18px;
        margin-bottom: 15px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .product-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 107, 107, 0.1), transparent);
        transition: left 0.5s;
    }

    .product-item:hover::before {
        left: 100%;
    }

    .product-item:hover {
        background: linear-gradient(135deg, #3a3a3a 0%, #4a4a4a 100%);
        transform: translateY(-5px) scale(1.02);
        border-color: rgba(255, 107, 107, 0.5);
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    }

    .product-item img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s;
    }

    .product-item:hover img {
        border-color: rgba(255, 107, 107, 0.5);
        transform: scale(1.1);
    }

    .product-item h5 {
        color: white;
        font-size: 15px;
        margin: 0 0 8px 0;
        line-height: 1.4;
        font-weight: 600;
    }

    .product-item .price {
        color: #ff6b6b;
        font-size: 18px;
        font-weight: bold;
        margin: 0;
    }

    .product-item .old-price {
        color: #999;
        text-decoration: line-through;
        font-size: 13px;
        margin-left: 8px;
    }

    #buy-button-container {
        position: fixed;
        bottom: 40px;
        right: 40px;
        z-index: 1001;
        display: none;
        animation: slideInUp 0.5s ease;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #buy-now-btn {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        border: none;
        padding: 18px 40px;
        border-radius: 35px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 6px 25px rgba(255, 107, 107, 0.5);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #buy-now-btn:hover {
        transform: scale(1.1) translateY(-3px);
        box-shadow: 0 10px 35px rgba(255, 107, 107, 0.7);
    }

    #buy-now-btn:active {
        transform: scale(1.05);
    }

    .close-btn {
        color: white;
        text-decoration: none;
        font-size: 24px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s;
    }

    .close-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
        color: white;
    }

    .viewer-count {
        color: #ccc;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .viewer-count::before {
        content: '👁️';
        font-size: 16px;
    }

    .empty-products {
        color: #999;
        text-align: center;
        padding: 40px 20px;
        font-style: italic;
    }

    /* Scrollbar styling */
    .products-sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .products-sidebar::-webkit-scrollbar-track {
        background: #1a1a1a;
    }

    .products-sidebar::-webkit-scrollbar-thumb {
        background: #ff6b6b;
        border-radius: 4px;
    }

    .products-sidebar::-webkit-scrollbar-thumb:hover {
        background: #ee5a6f;
    }
</style>
@endpush

@section('main-content')
<!-- Live Shopping Page -->
<div class="live-shopping-container">
    <!-- Live Stream Header -->
    <div class="live-header">
        <div style="display: flex; align-items: center; gap: 20px; flex: 1;">
            <div class="live-badge">
                <span class="live-dot"></span>
                LIVE
            </div>
            <div style="flex: 1;">
                <h3 style="color: white; margin: 0; font-size: 20px; font-weight: 600;">
                    {{ $stream->title ?? 'Live Shopping' }}
                </h3>
                @if($stream->description)
                <p style="color: #aaa; margin: 5px 0 0 0; font-size: 13px;">
                    {{ Str::limit($stream->description, 60) }}
                </p>
                @endif
            </div>
            <div class="viewer-count" id="viewer-count">
                {{ $stream->viewer_count ?? 0 }} người đang xem
            </div>
        </div>
        <a href="{{ route('home') }}" class="close-btn" title="Đóng">
            ✕
        </a>
    </div>

    <!-- Main Content -->
    <div style="display: flex; padding-top: 80px; min-height: 100vh;">
        <!-- Video Container -->
        <div class="video-container">
            <div id="zego-video-container">
                <!-- ZegoCloud video will be rendered here -->
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #666; text-align: center;">
                    <i class="fas fa-video" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                    <p>Đang tải video...</p>
                </div>
            </div>
        </div>

        <!-- Products Sidebar -->
        <div class="products-sidebar">
            <h4>
                <i class="fas fa-shopping-bag"></i> Sản phẩm đang bán
            </h4>
            <div class="products-list">
                @forelse($products as $product)
                <div class="product-item" onclick="selectProduct('{{ $product->slug }}')" data-product-slug="{{ $product->slug }}">
                    <div style="display: flex; gap: 15px;">
                        <img src="{{ Helper::getImageUrl($product->photo) }}" alt="{{ $product->title }}">
                        <div style="flex: 1;">
                            <h5>{{ $product->title }}</h5>
                            <p class="price">
                                {{ number_format($product->price, 0, ',', '.') }} đ
                                @if($product->discount)
                                <span class="old-price">
                                    {{ number_format($product->price + ($product->price * $product->discount / 100), 0, ',', '.') }} đ
                                </span>
                                <span style="color: #4caf50; font-size: 12px; margin-left: 5px;">
                                    (-{{ $product->discount }}%)
                                </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-products">
                    <i class="fas fa-box-open" style="font-size: 48px; display: block; margin-bottom: 15px; opacity: 0.5;"></i>
                    <p>Chưa có sản phẩm nào</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Buy Button (Floating) -->
    <div id="buy-button-container">
        <button id="buy-now-btn">
            <i class="fas fa-shopping-cart"></i>
            <span>Mua Ngay</span>
        </button>
    </div>
</div>

@push('scripts')
<script>
    // Load Jitsi Meet External API
    function loadJitsiAPI() {
        return new Promise((resolve, reject) => {
            if (typeof JitsiMeetExternalAPI !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://{{ config("services.jitsi.domain", "meet.jit.si") }}/external_api.js';
            script.async = true;
            script.onload = () => {
                setTimeout(() => {
                    if (typeof JitsiMeetExternalAPI !== 'undefined') {
                        resolve();
                    } else {
                        reject(new Error('JitsiMeetExternalAPI not loaded'));
                    }
                }, 500);
            };
            script.onerror = () => reject(new Error('Failed to load Jitsi Meet API'));
            document.head.appendChild(script);
        });
    }

    let api = null;
    const streamId = parseInt('{{ $stream->id }}');
    const roomId = '{{ $stream->room_id }}';
    const jitsiDomain = '{{ config("services.jitsi.domain", "meet.jit.si") }}';
    const userID = '{{ Auth::check() ? Auth::id() : "guest_".time() }}';
    const userName = '{{ Auth::check() ? Auth::user()->name : "Guest" }}';

    // Initialize Jitsi Meet for viewer
    async function initJitsi() {
        const container = document.getElementById('zego-video-container');

        try {
            // Show loading
            container.innerHTML = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 48px; margin-bottom: 15px; display: block;"></i><p>Đang kết nối...</p></div>';

            // Load Jitsi API
            await loadJitsiAPI();

            if (typeof JitsiMeetExternalAPI === 'undefined') {
                throw new Error('JitsiMeetExternalAPI is not defined');
            }

            container.innerHTML = '';

            const options = {
                roomName: roomId,
                parentNode: container,
                width: '100%',
                height: '100%',
                configOverwrite: {
                    startWithAudioMuted: true,
                    startWithVideoMuted: true,
                    enableWelcomePage: false,
                    enableClosePage: false,
                    disableDeepLinking: true,
                    prejoinPageEnabled: false,
                    enableLayerSuspension: true,
                    enableNoAudioDetection: false,
                    enableNoisyMicDetection: false,
                    enableTalkWhileMuted: false,
                    enableInsecureRoomNameWarning: false,
                    disableRemoteMute: true,
                    disableSelfView: false,
                },
                interfaceConfigOverwrite: {
                    TOOLBAR_BUTTONS: [
                        'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                        'fodeviceselection', 'hangup', 'profile', 'chat', 'settings',
                        'raisehand', 'videoquality', 'filmstrip', 'invite', 'feedback',
                        'stats', 'shortcuts', 'tileview', 'videobackgroundblur', 'download',
                        'help', 'mute-everyone', 'security'
                    ],
                    SETTINGS_SECTIONS: ['devices', 'language', 'moderator', 'profile'],
                    SHOW_JITSI_WATERMARK: false,
                    SHOW_WATERMARK_FOR_GUESTS: false,
                    SHOW_BRAND_WATERMARK: false,
                    BRAND_WATERMARK_LINK: '',
                    SHOW_POWERED_BY: false,
                    DISPLAY_WELCOME_PAGE_CONTENT: false,
                    DISPLAY_WELCOME_PAGE_TOOLBAR_ADDITIONAL_CONTENT: false,
                    APP_NAME: 'Live Shopping',
                    NATIVE_APP_NAME: 'Live Shopping',
                    PROVIDER_NAME: 'Live Shopping',
                    DEFAULT_BACKGROUND: '#1a1a1a',
                    INITIAL_TOOLBAR_TIMEOUT: 20000,
                    TOOLBAR_TIMEOUT: 4000,
                },
                userInfo: {
                    displayName: userName,
                    email: '{{ Auth::check() ? Auth::user()->email : "" }}',
                },
            };

            api = new JitsiMeetExternalAPI(jitsiDomain, options);

            api.addEventListener('videoConferenceJoined', () => {
                console.log('Joined conference as viewer');
                // Notify server that user joined
                fetch('/api/live-stream/' + streamId + '/join', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(err => console.error('Failed to notify join:', err));
            });

            api.addEventListener('participantJoined', (participant) => {
                console.log('Participant joined:', participant);
            });

            api.addEventListener('participantLeft', (participant) => {
                console.log('Participant left:', participant);
            });

            api.addEventListener('readyToClose', () => {
                console.log('Ready to close');
                // Notify server that user left
                fetch('/api/live-stream/' + streamId + '/leave', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(err => console.error('Failed to notify leave:', err));
            });

            api.addEventListener('errorOccurred', (error) => {
                console.error('Jitsi error:', error);
                showError('Có lỗi xảy ra khi kết nối. Vui lòng thử lại.');
            });

            api.addEventListener('videoConferenceLeft', () => {
                console.log('Left conference');
            });

        } catch (error) {
            console.error('Init Jitsi failed:', error);
            showError('Không thể kết nối đến live stream. Vui lòng kiểm tra kết nối internet và thử lại.');
        }
    }

    function showError(message) {
        const container = document.getElementById('zego-video-container');
        container.innerHTML = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #ff6b6b; text-align: center; padding: 20px; background: rgba(0,0,0,0.8); border-radius: 10px;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px; display: block;"></i><p>' + message + '</p><button onclick="location.reload()" style="margin-top: 15px; padding: 10px 20px; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer;">Thử lại</button></div>';
    }

    // Select product function
    function selectProduct(productSlug) {
        // Show buy button with animation
        const buyContainer = document.getElementById('buy-button-container');
        buyContainer.style.display = 'block';

        // Store selected product slug
        window.selectedProductSlug = productSlug;

        // Update buy button
        document.getElementById('buy-now-btn').onclick = function() {
            window.location.href = '/product-detail/' + productSlug;
        };

        // Highlight selected product
        document.querySelectorAll('.product-item').forEach(item => {
            item.style.border = '2px solid transparent';
        });
        event.currentTarget.style.border = '2px solid #ff6b6b';
    }

    // Check live status periodically
    setInterval(() => {
        fetch('/api/live-stream/status')
            .then(res => res.json())
            .then(data => {
                if (!data.has_active) {
                    alert('Phiên live đã kết thúc');
                    window.location.href = '/';
                }
                if (data.stream) {
                    document.getElementById('viewer-count').innerHTML =
                        '<i class="fas fa-eye"></i> ' + data.stream.viewer_count + ' người đang xem';
                }
            })
            .catch(err => console.error('Error checking status:', err));
    }, 5000);

    // Initialize on page load
    window.addEventListener('load', () => {
        initJitsi();
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (api) {
            try {
                api.dispose();
            } catch (e) {
                console.error('Error disposing Jitsi:', e);
            }
            fetch('/api/live-stream/' + streamId + '/leave', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).catch(err => console.error('Failed to notify leave:', err));
        }
    });
</script>
@endpush
@endsection