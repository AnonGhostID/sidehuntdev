<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Manajemen Panel - ' . config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom styles for image modal -->


    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            color: #6b7280; /* gray-500 */
        }
        .sidebar-link:hover {
            background-color: #e5e7eb; /* gray-200 */
            color: #1f2937; /* gray-800 */
        }
        .sidebar-link.active {
            background-color: #3b82f6; /* blue-500 */
            color: white;
            font-weight: 500;
        }
        .sidebar-link.active i {
            color: white;
        }
        .sidebar-link i {
            margin-right: 0.75rem;
            width: 1.25rem; /* w-5 */
            text-align: center;
            color: #9ca3af; /* gray-400 */
        }
        .sidebar-link.active i {
            color: white;
        }
        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Notification Styles */
        .notification-bell {
            position: relative;
            display: inline-block;
            margin-right: 1rem;
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            min-width: 20px;
        }
        
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .notification-dropdown.show {
            display: block;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f9fafb;
        }
        
        .notification-item.unread {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .notification-message {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 4px;
        }
        
        .notification-time {
            color: #9ca3af;
            font-size: 12px;
        }
        
        .notification-header {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-footer {
            padding: 12px 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .btn-mark-all-read {
            color: #3b82f6;
            text-decoration: none;
            font-size: 14px;
        }
        
        .btn-mark-all-read:hover {
            text-decoration: underline;
        }
        
        .no-notifications {
            padding: 32px 16px;
            text-align: center;
            color: #9ca3af;
        }
    </style>
</head>
<body class="bg-gray-100 antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">
        <aside
            class="fixed inset-y-0 left-0 z-30 flex flex-col h-full w-64 transform transition-transform duration-300 ease-in-out bg-white border-r border-gray-200 shadow-lg lg:translate-x-0 lg:static lg:inset-0"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <div class="flex items-center justify-between p-4 h-16 border-b border-gray-200">
                <a href="{{ route('manajemen.dashboard') }}" class="text-2xl font-bold text-blue-600">
                    {{ config('app.name', 'SideHunt') }}
                </a>
                <button @click="sidebarOpen = false" class="text-gray-500 lg:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('manajemen.dashboard') }}" class="sidebar-link {{ request()->routeIs('manajemen.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                @if(session('account') && !session('account')->isAdmin())
                <h3 class="mt-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pekerjaan</h3>
                
                @if(session('account'))
                    @if(session('account')->isUser())
                        <a href="{{ route('manajemen.pekerjaan.berlangsung') }}" class="sidebar-link {{ request()->routeIs('manajemen.pekerjaan.berlangsung') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i>
                            <span>Pekerjaan Berlangsung</span>
                        </a>
                    @endif
                @endif
                @if(session('account'))
                    @if(session('account')->isMitra())
                        <a href="{{ route('manajemen.pekerjaan.terdaftar') }}" class="sidebar-link {{ request()->routeIs('manajemen.pekerjaan.terdaftar') ? 'active' : '' }}">
                            <i class="fas fa-list-check"></i>
                            <span>Pekerjaan Terdaftar</span>
                        </a>
                    @endif
                @endif
                @if(session('account'))
                    @if(session('account')->isUser())
                        <a href="{{ route('manajemen.laporan.upload') }}" class="sidebar-link {{ request()->routeIs('manajemen.laporan.upload') ? 'active' : '' }}">
                            <i class="fas fa-file-upload"></i>
                            <span>Upload Laporan Hasil</span>
                        </a>
                    @endif
                @endif
                @if(session('account'))
                    @if(session('account')->isUser())
                        <a href="{{ route('manajemen.pekerjaan.riwayat') }}" class="sidebar-link {{ request()->routeIs('manajemen.pekerjaan.riwayat') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span>Riwayat Pekerjaan</span>
                        </a>
                    @endif
                @endif
                @endif

                <h3 class="mt-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Keuangan</h3>
                @if(session('account') && !session('account')->isUser())
                                <a href="{{ route('manajemen.topUp') }}" class="sidebar-link {{ request()->routeIs('manajemen.topUp') ? 'active' : '' }}">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Top Up Saldo</span>
                                </a>
                @endif
                 <a href="{{ route('manajemen.tarik_saldo') }}" class="sidebar-link {{ request()->routeIs('manajemen.tarik_saldo') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-transfer"></i>
                    <span>Tarik Saldo</span>
                </a>
                <a href="{{ route('manajemen.transaksi.riwayat') }}" class="sidebar-link {{ request()->routeIs('manajemen.transaksi.riwayat') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i>
                    <span>Riwayat Transaksi</span>
                </a>
                <!-- <a href="{{ route('manajemen.dana.refund') }}" class="sidebar-link {{ request()->routeIs('manajemen.dana.refund') ? 'active' : '' }}">
                    <i class="fas fa-undo-alt"></i>
                    <span>Refund Dana</span>
                </a> -->
                <a href="{{ route('manajemen.keuangan.laporan') }}" class="sidebar-link {{ request()->routeIs('manajemen.keuangan.laporan') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Laporan Keuangan</span>
                </a>

                <h3 class="mt-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelaporan & Bantuan</h3>
                <a href="{{ route('manajemen.bantuan.panel') }}" class="sidebar-link {{ request()->routeIs('manajemen.bantuan.panel') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i>
                    <span>Panel Bantuan dan Laporan Penipuan</span>
                </a>

                <h3 class="mt-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lainnya</h3>
                <a href="{{ route('manajemen.notifications.page') }}" class="sidebar-link {{ request()->routeIs('manajemen.notifications.page') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>Riwayat Notifikasi</span>
                </a>
                @if(session('account') && (session('account')->isMitra()))
                <a href="{{ route('manajemen.pelamar.track-record') }}" class="sidebar-link {{ request()->routeIs('manajemen.pelamar.track-record') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i>
                    <span>Track Record Pelamar</span>
                </a>
                @endif


            </nav>

            <div class="p-4 border-t border-gray-200">
                @if(session('account'))
                <div class="flex items-center mb-3">
                    <img src="{{ asset('img/progress.png') }}"class="w-10 h-10 rounded-full mr-3 object-cover">
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ session('account')->nama }}</p>
                        <p class="text-xs text-gray-500">{{ session('account')->email }}</p>
                    </div>
                </div>
                <a href="/Logout"
                   class="sidebar-link bg-red-50 hover:bg-red-100 text-red-600">
                    <i class="fas fa-sign-out-alt text-red-500"></i>
                    <span>Logout</span>
                </a>
                <!-- <form id="logout-form" action="" method="POST" class="d-none">
                    @csrf
                </form> -->
                @endif
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex items-center justify-between p-4 h-16 bg-white border-b border-gray-200 shadow-sm">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="text-xl font-semibold text-gray-700">@yield('page-title', 'Dashboard')</div>
                <div class="flex items-center">
                    {{-- Notification Bell --}}
                    <div class="notification-bell">
                        <button id="notification-bell" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notification-badge" class="notification-badge" style="display: none;">0</span>
                        </button>
                        
                        {{-- Notification Dropdown --}}
                        <div id="notification-dropdown" class="notification-dropdown">
                            <div class="notification-header">
                                <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                                <button id="mark-all-read" class="btn-mark-all-read">Tandai Semua Dibaca</button>
                            </div>
                            
                            <div id="notification-list">
                                <div class="no-notifications">
                                    <i class="fas fa-bell-slash text-2xl text-gray-300 mb-2"></i>
                                    <p>Tidak ada notifikasi</p>
                                </div>
                            </div>
                            
                            <div class="notification-footer">
                                <a href="{{ route('manajemen.notifications.page') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Lihat Semua Notifikasi
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Home Button --}}
                    <a href="{{ url('/') }}" class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-x-hidden overflow-y-auto bg-gray-100">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/balance-update.js') }}"></script>
    
    {{-- Notification System JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBell = document.getElementById('notification-bell');
            const notificationDropdown = document.getElementById('notification-dropdown');
            const notificationBadge = document.getElementById('notification-badge');
            const notificationList = document.getElementById('notification-list');
            const markAllReadBtn = document.getElementById('mark-all-read');
            
            let isDropdownOpen = false;
            let pollInterval;
            
            // CSRF Token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Toggle notification dropdown
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                isDropdownOpen = !isDropdownOpen;
                
                if (isDropdownOpen) {
                    notificationDropdown.classList.add('show');
                    loadNotifications();
                } else {
                    notificationDropdown.classList.remove('show');
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
                    notificationDropdown.classList.remove('show');
                    isDropdownOpen = false;
                }
            });
            
            // Mark all as read
            markAllReadBtn.addEventListener('click', function() {
                markAllNotificationsAsRead();
            });
            
            // Load notifications
            function loadNotifications() {
                fetch('/management/notifications', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.notifications) {
                        displayNotifications(data.notifications);
                        updateBadge(data.unread_count);
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
            }
            
            // Display notifications in dropdown
            function displayNotifications(notifications) {
                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="no-notifications">
                            <i class="fas fa-bell-slash text-2xl text-gray-300 mb-2"></i>
                            <p>Tidak ada notifikasi</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                notifications.forEach(notification => {
                    const unreadClass = notification.is_read ? '' : 'unread';
                    const typeIcon = getNotificationIcon(notification.type);
                    
                    html += `
                        <div class="notification-item ${unreadClass}" data-id="${notification.id}">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="${typeIcon} text-blue-500"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="notification-title">${notification.title}</div>
                                    <div class="notification-message">${notification.message}</div>
                                    <div class="notification-time">${notification.time_ago}</div>
                                </div>
                                ${!notification.is_read ? '<div class="flex-shrink-0"><div class="w-2 h-2 bg-blue-500 rounded-full"></div></div>' : ''}
                            </div>
                        </div>
                    `;
                });
                
                notificationList.innerHTML = html;
                
                // Add click handlers for notification items
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const notificationId = this.dataset.id;
                        if (this.classList.contains('unread')) {
                            markNotificationAsRead(notificationId, this);
                        }
                    });
                });
            }
            
            // Get notification icon based on type
            function getNotificationIcon(type) {
                switch (type) {
                    case 'application_status':
                        return 'fas fa-check-circle';
                    case 'new_application':
                        return 'fas fa-user-plus';
                    case 'job_status':
                        return 'fas fa-briefcase';
                    default:
                        return 'fas fa-bell';
                }
            }
            
            // Mark single notification as read
            function markNotificationAsRead(notificationId, element) {
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
                        element.classList.remove('unread');
                        const unreadDot = element.querySelector('.w-2.h-2.bg-blue-500');
                        if (unreadDot) {
                            unreadDot.remove();
                        }
                        updateUnreadCount();
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
            }
            
            // Mark all notifications as read
            function markAllNotificationsAsRead() {
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
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            const unreadDot = item.querySelector('.w-2.h-2.bg-blue-500');
                            if (unreadDot) {
                                unreadDot.remove();
                            }
                        });
                        updateBadge(0);
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                });
            }
            
            // Update notification badge
            function updateBadge(count) {
                if (count > 0) {
                    notificationBadge.textContent = count > 99 ? '99+' : count;
                    notificationBadge.style.display = 'flex';
                } else {
                    notificationBadge.style.display = 'none';
                }
            }
            
            // Update unread count
            function updateUnreadCount() {
                fetch('/management/notifications/unread-count', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateBadge(data.unread_count);
                })
                .catch(error => {
                    console.error('Error getting unread count:', error);
                });
            }
            
            // Polling for new notifications
            function startPolling() {
                pollInterval = setInterval(() => {
                    if (!isDropdownOpen) {
                        fetch('/management/notifications/poll', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            updateBadge(data.unread_count);
                            
                            // Show notification sound/alert if there are new notifications
                            if (data.has_new && data.recent_notifications.length > 0) {
                                // Optional: Play notification sound
                                // playNotificationSound();
                                
                                // Optional: Show browser notification
                                // showBrowserNotification(data.recent_notifications[0]);
                            }
                        })
                        .catch(error => {
                            console.error('Error polling notifications:', error);
                        });
                    }
                }, 30000); // Poll every 30 seconds
            }
            
            // Optional: Play notification sound
            function playNotificationSound() {
                // You can add audio file for notification sound
                // const audio = new Audio('/sounds/notification.mp3');
                // audio.play().catch(() => {}); // Ignore if audio fails
            }
            
            // Optional: Show browser notification
            function showBrowserNotification(notification) {
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(notification.title, {
                        body: notification.message,
                        icon: '/img/logoOnlyIcon.svg'
                    });
                }
            }
            
            // Request notification permission
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
            
            // Initialize
            updateUnreadCount();
            startPolling();
            
            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
            });
        });
    </script>
    
    <!-- Custom image modal script is included in the specific view -->
    
    @stack('scripts')
</body>
</html>
