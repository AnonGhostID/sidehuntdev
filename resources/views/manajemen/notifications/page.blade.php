@extends('layouts.management')

@section('title', 'Notifikasi - ' . config('app.name', 'Laravel'))

@section('page-title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
                    <p class="text-sm text-gray-600 mt-1">Riwayat semua notifikasi Anda</p>
                </div>
                @if($notifications->where('read_at', null)->count() > 0)
                <button id="mark-all-read-page" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-check-double mr-2"></i>
                    Tandai Semua Dibaca
                </button>
                @endif
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                <div class="notification-item-page {{ $notification->isUnread() ? 'unread-page' : '' }} p-6 hover:bg-gray-50 transition-colors" data-id="{{ $notification->id }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            @switch($notification->type)
                                @case('application_status')
                                    @if($notification->data && isset($notification->data['status']) && $notification->data['status'] === 'diterima')
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-times-circle text-red-600"></i>
                                        </div>
                                    @endif
                                    @break
                                @case('new_application')
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-plus text-blue-600"></i>
                                    </div>
                                    @break
                                @default
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-bell text-gray-600"></i>
                                    </div>
                            @endswitch
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        {{ $notification->title }}
                                        @if($notification->isUnread())
                                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                        @endif
                                    </h3>
                                    <p class="text-gray-700 mb-2 leading-relaxed">{{ $notification->message }}</p>
                                    
                                    @if($notification->data)
                                    <div class="text-sm text-gray-500 mb-2">
                                        @if(isset($notification->data['job_name']))
                                            <span class="inline-flex items-center">
                                                <i class="fas fa-briefcase mr-1"></i>
                                                Pekerjaan: <span class="font-medium ml-1">{{ $notification->data['job_name'] }}</span>
                                            </span>
                                        @endif
                                        @if(isset($notification->data['applicant_name']))
                                            <span class="inline-flex items-center ml-4">
                                                <i class="fas fa-user mr-1"></i>
                                                Pelamar: <span class="font-medium ml-1">{{ $notification->data['applicant_name'] }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $notification->time_ago }}
                                        <span class="mx-2">•</span>
                                        {{ $notification->created_at->format('d M Y, H:i') }}
                                        @if($notification->isRead())
                                            <span class="mx-2">•</span>
                                            <span class="text-green-600">
                                                <i class="fas fa-check mr-1"></i>
                                                Dibaca
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2 ml-4">
                                    @if($notification->isUnread())
                                    <button class="mark-as-read-btn text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors" data-id="{{ $notification->id }}" title="Tandai sebagai dibaca">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    <button class="delete-notification-btn text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors" data-id="{{ $notification->id }}" title="Hapus notifikasi">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Notifikasi</h3>
                    <p class="text-gray-500">Notifikasi akan muncul di sini ketika ada aktivitas terkait akun Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.unread-page {
    background-color: #eff6ff;
    border-left: 4px solid #3b82f6;
}

.notification-item-page {
    cursor: pointer;
}

.notification-item-page:hover {
    background-color: #f9fafb !important;
}

.unread-page:hover {
    background-color: #dbeafe !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Mark all as read button
    const markAllReadBtn = document.getElementById('mark-all-read-page');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch('/management/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        });
    }
    
    // Mark single notification as read
    document.querySelectorAll('.mark-as-read-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.dataset.id;
            
            fetch(`/management/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        });
    });
    
    // Delete notification
    document.querySelectorAll('.delete-notification-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.dataset.id;
            
            if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
                fetch(`/management/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error deleting notification:', error);
                });
            }
        });
    });
    
    // Click on notification item to mark as read
    document.querySelectorAll('.notification-item-page.unread-page').forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't trigger if clicking on action buttons
            if (e.target.closest('.mark-as-read-btn, .delete-notification-btn')) {
                return;
            }
            
            const notificationId = this.dataset.id;
            
            fetch(`/management/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.remove('unread-page');
                    const unreadDot = this.querySelector('.w-2.h-2.bg-blue-500');
                    if (unreadDot) {
                        unreadDot.remove();
                    }
                    const markAsReadBtn = this.querySelector('.mark-as-read-btn');
                    if (markAsReadBtn) {
                        markAsReadBtn.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        });
    });
});
</script>
@endsection
