@extends('backend.layouts.master')
@section('title','Quản lý Live Stream')

@push('styles')
<style>
    .live-stream-table {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 1rem;
    }

    .table tbody tr {
        transition: all 0.3s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fc;
        transform: translateX(5px);
    }

    .badge-live {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        animation: pulse-live 2s infinite;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-live::before {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: blink 1s infinite;
    }

    .badge-ended {
        background: #6c757d;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .badge-pending {
        background: #ffc107;
        color: #856404;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
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

    .btn-action {
        margin: 0 2px;
        border-radius: 0.25rem;
        transition: all 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .empty-state {
        padding: 3rem;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.35rem 0.35rem 0 0 !important;
    }

    .btn-create {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        transition: all 0.3s;
    }

    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        color: white;
    }
</style>
@endpush

@section('main-content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-video text-danger"></i> Quản lý Live Stream
        </h1>
        <a href="{{ route('admin.livestream.create') }}" class="btn btn-create">
            <i class="fas fa-plus"></i> Tạo Live Stream Mới
        </a>
    </div>

    <div class="card shadow mb-4 live-stream-table">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-list"></i> Danh sách Live Stream
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Người tạo</th>
                            <th>Trạng thái</th>
                            <th>Người xem</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($streams as $stream)
                        <tr>
                            <td><strong>#{{ $stream->id }}</strong></td>
                            <td>
                                <strong>{{ $stream->title ?? 'N/A' }}</strong>
                                @if($stream->description)
                                <br><small class="text-muted">{{ Str::limit($stream->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-user text-primary"></i>
                                {{ $stream->startedBy->name ?? 'N/A' }}
                            </td>
                            <td>
                                @if($stream->status == 'live')
                                <span class="badge-live">Đang LIVE</span>
                                @elseif($stream->status == 'ended')
                                <span class="badge-ended">Đã kết thúc</span>
                                @else
                                <span class="badge-pending">Chờ</span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-users text-info"></i>
                                <strong>{{ $stream->viewer_count }}</strong>
                            </td>
                            <td>
                                @if($stream->started_at)
                                <i class="fas fa-clock text-success"></i>
                                {{ $stream->started_at->format('d/m/Y H:i') }}
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($stream->ended_at)
                                <i class="fas fa-stop-circle text-danger"></i>
                                {{ $stream->ended_at->format('d/m/Y H:i') }}
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($stream->status == 'live')
                                    <form action="{{ route('admin.livestream.end', $stream->id) }}" method="POST" style="display: inline;" id="end-form-{{ $stream->id }}">
                                        @csrf
                                        <button type="button"
                                            class="btn btn-sm btn-danger btn-action"
                                            onclick="endLiveStream({{ $stream->id }})"
                                            title="Kết thúc">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('livestream.view', $stream->id) }}"
                                        class="btn btn-sm btn-info btn-action"
                                        target="_blank"
                                        title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-video-slash"></i>
                                    <h5>Chưa có live stream nào</h5>
                                    <p class="text-muted">Bắt đầu tạo live stream đầu tiên của bạn!</p>
                                    <a href="{{ route('admin.livestream.create') }}" class="btn btn-create mt-3">
                                        <i class="fas fa-plus"></i> Tạo Live Stream Mới
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($streams->hasPages())
            <div class="mt-3">
                {{ $streams->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function endLiveStream(streamId) {
        if (!confirm('Bạn có chắc muốn kết thúc live stream này?')) {
            return;
        }

        const form = document.getElementById('end-form-' + streamId);
        const btn = form.querySelector('button');
        const originalHTML = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã kết thúc live stream thành công!');
                    // Reload page to update status
                    window.location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + (data.error || 'Unknown error'));
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kết thúc live stream');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
    }
</script>
@endpush
@endsection