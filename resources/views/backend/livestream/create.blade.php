@extends('backend.layouts.master')
@section('title','Tạo Live Stream')

@push('styles')
<style>
    .live-stream-card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-radius: 0.35rem;
        transition: all 0.3s;
    }

    .live-stream-card:hover {
        box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.35rem 0.35rem 0 0 !important;
        padding: 1.25rem 1.5rem;
    }

    .card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .form-group label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 0.35rem;
        border: 1px solid #d1d3e2;
        padding: 0.75rem;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-live {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.35rem;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
    }

    .btn-live:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        color: white;
    }

    .btn-live:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .alert-warning {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        border: none;
        color: #856404;
        border-radius: 0.35rem;
        padding: 1rem 1.5rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        border: none;
        color: #155724;
        border-radius: 0.35rem;
        padding: 1.5rem;
    }

    .video-container-admin {
        background: #000;
        border-radius: 0.35rem;
        position: relative;
        overflow: hidden;
        min-height: 600px;
        height: 600px;
        width: 100%;
    }

    .video-container-admin iframe {
        border: none;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }

    .video-container-admin::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, #1a1a1a 25%, transparent 25%, transparent 75%, #1a1a1a 75%, #1a1a1a),
            linear-gradient(45deg, #1a1a1a 25%, transparent 25%, transparent 75%, #1a1a1a 75%, #1a1a1a);
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
        opacity: 0.3;
    }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #dc3545;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        animation: pulse-live 2s infinite;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: blink 1s infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }
    }

    @keyframes pulse-live {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .info-box {
        background: white;
        border-radius: 0.35rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
    }

    .btn-end-live {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.35rem;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(235, 51, 73, 0.3);
    }

    .btn-end-live:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(235, 51, 73, 0.4);
        color: white;
    }

    .loading-overlay {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 0.35rem;
        z-index: 1000;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: white;
    }

    .loading-overlay.active {
        display: flex;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endpush

@section('main-content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-video text-danger"></i> Tạo Live Stream Mới
        </h1>
        <a href="{{ route('admin.livestream.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if($activeStream)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle"></i> Lưu ý:</strong> Đang có một live stream đang hoạt động.
        Bạn có muốn kết thúc nó và tạo mới không?
        <div class="mt-3">
            <form action="{{ route('admin.livestream.end', $activeStream->id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn kết thúc live stream này?')">
                    <i class="fas fa-stop"></i> Kết thúc Live Stream hiện tại
                </button>
            </form>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card live-stream-card shadow mb-4">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-video-camera"></i> Thông tin Live Stream
                    </h4>
                </div>
                <div class="card-body">
                    <form id="start-live-form">
                        @csrf
                        <div class="form-group">
                            <label for="title">
                                <i class="fas fa-heading text-primary"></i> Tiêu đề Live Stream
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control"
                                id="title"
                                name="title"
                                value="Live Shopping - {{ date('d/m/Y H:i') }}"
                                required
                                placeholder="Nhập tiêu đề cho live stream...">
                            <small class="form-text text-muted">Tiêu đề sẽ hiển thị cho người xem</small>
                        </div>

                        <div class="form-group">
                            <label for="description">
                                <i class="fas fa-align-left text-primary"></i> Mô tả
                            </label>
                            <textarea class="form-control"
                                id="description"
                                name="description"
                                rows="4"
                                placeholder="Mô tả về live stream này... (Ví dụ: Giới thiệu sản phẩm mới, sale đặc biệt, v.v.)"></textarea>
                            <small class="form-text text-muted">Mô tả chi tiết về nội dung live stream</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-live btn-lg" id="start-btn">
                                <i class="fas fa-video-camera"></i> Bắt đầu Live Stream
                            </button>
                            <a href="{{ route('admin.livestream.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>

                    <div id="live-info" style="display: none;">
                        <div class="alert alert-success">
                            <h5 class="mb-3">
                                <i class="fas fa-check-circle"></i> Live Stream đã được tạo thành công!
                            </h5>
                            <div class="info-box">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-door-open"></i>
                                    </div>
                                    <div>
                                        <strong>Room ID:</strong>
                                        <div class="text-muted" id="room-id">-</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                    <div>
                                        <strong>Trạng thái:</strong>
                                        <div><span class="live-badge"><span class="live-dot"></span>Đang LIVE</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="#" id="view-live-link" class="btn btn-success btn-lg" target="_blank">
                                    <i class="fas fa-eye"></i> Xem Live Stream
                                </a>
                                <a href="{{ route('admin.livestream.index') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-list"></i> Quay lại danh sách
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Hướng dẫn
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Bước 1:</strong> Điền thông tin live stream
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Bước 2:</strong> Click "Bắt đầu Live Stream"
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Bước 3:</strong> Cho phép truy cập camera/microphone
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Bước 4:</strong> Bắt đầu phát trực tiếp
                        </li>
                    </ul>
                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-lightbulb"></i>
                            <strong>Lưu ý:</strong> Đảm bảo camera và microphone hoạt động tốt trước khi bắt đầu.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Stream Video Container (for Admin) -->
    <div id="admin-live-container" style="display: none; margin-top: 2rem;">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-video"></i> Video Live Stream (Admin View)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="video-container-admin" id="zego-video-container-admin" style="position: relative;">
                    <div class="loading-overlay" id="video-loading">
                        <div class="spinner"></div>
                        <p class="mt-3">Đang kết nối...</p>
                    </div>
                </div>
                <div class="p-3 bg-light border-top">
                    <button id="end-live-btn" class="btn btn-end-live">
                        <i class="fas fa-stop"></i> Kết thúc Live Stream
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://{{ config('services.jitsi.domain', 'meet.jit.si') }}/external_api.js"></script>
<script>
    // Suppress Echo error if not defined (from other scripts)
    if (typeof Echo === 'undefined') {
        window.Echo = {
            channel: function() {
                return {
                    listen: function() {}
                };
            }
        };
    }

    let api = null;
    let currentStreamId = null;
    const jitsiDomain = '{{ config("services.jitsi.domain", "meet.jit.si") }}';

    document.getElementById('start-live-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('start-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang tạo...';

        try {
            const response = await fetch('{{ route("admin.livestream.start") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: document.getElementById('title').value,
                    description: document.getElementById('description').value
                })
            });

            const data = await response.json();

            if (data.success) {
                currentStreamId = data.stream.id;
                document.getElementById('room-id').textContent = data.stream.room_id;
                document.getElementById('view-live-link').href = '/live-stream/view/' + data.stream.id;

                document.getElementById('start-live-form').style.display = 'none';
                document.getElementById('live-info').style.display = 'block';
                document.getElementById('admin-live-container').style.display = 'block';

                // Show loading
                document.getElementById('video-loading').classList.add('active');

                // Initialize Jitsi Meet for admin
                await initJitsiAdmin(data.jitsi_config.room_name);
            } else {
                alert('Có lỗi xảy ra: ' + (data.error || 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-video-camera"></i> Bắt đầu Live Stream';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo live stream');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-video-camera"></i> Bắt đầu Live Stream';
        }
    });

    function initJitsiAdmin(roomName) {
        try {
            const container = document.getElementById('zego-video-container-admin');
            container.innerHTML = '';

            const options = {
                roomName: roomName,
                parentNode: container,
                width: '100%',
                height: '100%',
                configOverwrite: {
                    startWithAudioMuted: false,
                    startWithVideoMuted: false,
                    enableWelcomePage: false,
                    enableClosePage: false,
                    disableDeepLinking: true,
                    prejoinPageEnabled: false,
                    enableLayerSuspension: false,
                    enableInsecureRoomNameWarning: false,
                    enableNoAudioDetection: false,
                    enableNoisyMicDetection: false,
                    enableTalkWhileMuted: false,
                    disableRemoteMute: false,
                    disableSelfView: false,
                    toolbarConfig: {
                        alwaysVisible: true,
                        timeout: 0,
                        initialTimeout: 0,
                    },
                },
                interfaceConfigOverwrite: {
                    TOOLBAR_BUTTONS: [
                        'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                        'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                        'livestreaming', 'settings', 'raisehand', 'videoquality', 'filmstrip',
                        'invite', 'feedback', 'stats', 'shortcuts', 'tileview', 'videobackgroundblur',
                        'download', 'help', 'mute-everyone', 'mute-video-everyone', 'security'
                    ],
                    SETTINGS_SECTIONS: ['devices', 'language', 'moderator', 'profile', 'calendar'],
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
                    TOOLBAR_ALWAYS_VISIBLE: true,
                    INITIAL_TOOLBAR_TIMEOUT: 0,
                    TOOLBAR_TIMEOUT: 0,
                    DEFAULT_BACKGROUND: '#1a1a1a',
                },
                userInfo: {
                    displayName: '{{ Auth::user()->name }}',
                    email: '{{ Auth::user()->email }}',
                },
            };

            api = new JitsiMeetExternalAPI(jitsiDomain, options);

            api.addEventListener('videoConferenceJoined', () => {
                console.log('Joined conference as moderator');
                document.getElementById('video-loading').classList.remove('active');
            });

            api.addEventListener('readyToClose', () => {
                console.log('Ready to close');
            });

            api.addEventListener('participantJoined', (participant) => {
                console.log('Participant joined:', participant);
                // Update viewer count
                fetch('/api/live-stream/' + currentStreamId + '/join', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(err => console.error('Failed to notify join:', err));
            });

            api.addEventListener('participantLeft', (participant) => {
                console.log('Participant left:', participant);
                // Update viewer count
                fetch('/api/live-stream/' + currentStreamId + '/leave', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(err => console.error('Failed to notify leave:', err));
            });

            api.addEventListener('errorOccurred', (error) => {
                console.error('Jitsi error:', error);
                document.getElementById('video-loading').classList.remove('active');

                let errorMsg = 'Có lỗi xảy ra khi kết nối. ';
                if (error && error.error) {
                    errorMsg += 'Chi tiết: ' + error.error;
                }
                alert(errorMsg);
            });

            api.addEventListener('videoConferenceLeft', () => {
                console.log('Left conference');
            });

            api.addEventListener('participantRoleChanged', (participant) => {
                console.log('Participant role changed:', participant);
            });

        } catch (error) {
            console.error('Init Jitsi failed:', error);
            document.getElementById('video-loading').classList.remove('active');
            alert('Có lỗi xảy ra khi khởi tạo Jitsi Meet. Vui lòng thử lại.');
        }
    }

    document.getElementById('end-live-btn').addEventListener('click', async function() {
        if (!confirm('Bạn có chắc muốn kết thúc live stream này?')) {
            return;
        }

        if (currentStreamId) {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang kết thúc...';

            try {
                // Disconnect from Jitsi
                if (api) {
                    try {
                        api.dispose();
                    } catch (e) {
                        console.error('Error disposing Jitsi:', e);
                    }
                }

                const response = await fetch('/admin/livestream/' + currentStreamId + '/end', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Đã kết thúc live stream thành công!');
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.livestream.index") }}';
                    }, 500);
                } else {
                    alert('Có lỗi xảy ra: ' + (data.error || 'Unknown error'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-stop"></i> Kết thúc Live Stream';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kết thúc live stream');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-stop"></i> Kết thúc Live Stream';
            }
        }
    });
</script>
@endpush
@endsection