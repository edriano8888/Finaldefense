<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings | Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            green: '#1FAE5B',
                            deep: '#0F6B3E',
                            dark: '#111827',
                            gray: '#F9FAFB',
                            border: '#E5E7EB',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02)',
                        'card': '0 0 0 1px rgba(0,0,0,0.03), 0 2px 8px rgba(0,0,0,0.04)',
                    }
                }
            }
        }
    </script>
    <link href="<?php echo base_url('assets/css/settings.css'); ?>" rel="stylesheet">
</head>
<body class="bg-brand-gray h-screen flex overflow-hidden text-gray-800 font-sans">

    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Settings', 'child' => 'Preferences']); ?>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-8 scroll-smooth">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">System Settings</h1>
                <p class="text-gray-500 mt-1">Manage your application preferences.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Notifications -->
                <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Notifications</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Email Notifications</p>
                                <p class="text-xs text-gray-500">Receive emails about campaign updates.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-green"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Browser Alerts</p>
                                <p class="text-xs text-gray-500">Get push notifications in your browser.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-green"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Appearance -->
                <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Appearance</h3>
                    <div class="space-y-4">
                         <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Dark Mode</p>
                                <p class="text-xs text-gray-500">Switch between light and dark themes.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-green"></div>
                            </label>
                        </div>
                         <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Compact View</p>
                                <p class="text-xs text-gray-500">Make the interface more dense.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-green"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Toggle Sidebar Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            refreshNotifSummary();
        });

        const NOTIF_BASE_URL = <?php echo json_encode(base_url()); ?>;

        function escapeHtml(str) {
            return String(str || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function formatNotifTime(ts) {
            if (!ts) return '';
            const d = new Date(ts.replace(' ', 'T'));
            if (isNaN(d.getTime())) return ts;
            return d.toLocaleString();
        }

        // ── Notification helpers ─────────────────────────────────────────────────
        function _notifTimeAgo(dateStr) {
            const now  = Date.now();
            const then = new Date(dateStr.replace(' ', 'T')).getTime();
            if (isNaN(then)) return dateStr;
            const diff = Math.max(0, Math.floor((now - then) / 1000));
            if (diff <  60)    return 'Just now';
            if (diff < 3600)   return Math.floor(diff / 60)   + ' min ago';
            if (diff < 86400)  return Math.floor(diff / 3600) + ' hr ago';
            if (diff < 172800) return 'Yesterday';
            return Math.floor(diff / 86400) + ' days ago';
        }

        const _NOTIF_ICON = {
            login:      { icon: 'fa-sign-in-alt',          bg: 'bg-blue-100',   fg: 'text-blue-600'   },
            success:    { icon: 'fa-check-circle',          bg: 'bg-green-100',  fg: 'text-green-600'  },
            warning:    { icon: 'fa-exclamation-triangle',  bg: 'bg-yellow-100', fg: 'text-yellow-600' },
            error:      { icon: 'fa-times-circle',          bg: 'bg-red-100',    fg: 'text-red-600'    },
            influencer: { icon: 'fa-user-plus',             bg: 'bg-indigo-100', fg: 'text-indigo-600' },
            campaign:   { icon: 'fa-handshake',             bg: 'bg-purple-100', fg: 'text-purple-600' },
            task:       { icon: 'fa-tasks',                 bg: 'bg-teal-100',   fg: 'text-teal-600'   },
            info:       { icon: 'fa-bell',                  bg: 'bg-gray-100',   fg: 'text-gray-500'   },
        };

        function _notifIcon(n) {
            const m  = _NOTIF_ICON[n.type] || _NOTIF_ICON.info;
            const ic = (n.icon && n.icon !== 'fa-bell') ? n.icon : m.icon;
            return `<span class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center ${m.bg}"><i class="fas ${ic} text-sm ${m.fg}"></i></span>`;
        }

        async function refreshNotifSummary() {
            try {
                const res = await fetch(NOTIF_BASE_URL + 'notifications/summary', { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                const badge = document.getElementById('notif-badge');
                if (!badge) return;
                const count = data.unread || 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (e) {}
        }

        function renderNotifList(items) {
            const wrap = document.getElementById('notif-list');
            if (!wrap) return;
            if (!items || items.length === 0) {
                wrap.innerHTML =
                    '<div class="flex flex-col items-center justify-center py-12 text-gray-300">' +
                    '<i class="far fa-bell-slash text-4xl mb-3"></i>' +
                    '<p class="text-sm font-medium text-gray-400">No notifications yet</p>' +
                    '<p class="text-xs text-gray-300 mt-1">You\'re all caught up!</p>' +
                    '</div>';
                return;
            }
            wrap.innerHTML = items.map(n => {
                const unreadBar = n.is_read ? '' : '<span class="absolute left-0 top-0 h-full w-1 bg-indigo-500 rounded-l-md"></span>';
                const rowBg    = n.is_read ? '' : 'bg-indigo-50/30';
                const iconHtml = _notifIcon(n);
                const timeStr  = _notifTimeAgo(n.created_at || '');
                const title    = escapeHtml(n.title   || 'Notification');
                const msg      = escapeHtml(n.message || '');
                const url      = n.url || '';
                return (
                    `<div class="relative flex items-start gap-0 border-b border-gray-50 ${rowBg} hover:bg-gray-50 transition">` +
                    unreadBar +
                    `<div onclick="openNotif(${n.id},'${encodeURIComponent(url)}')" class="flex items-start gap-3 flex-1 min-w-0 px-5 py-3 cursor-pointer">` +
                    iconHtml +
                    `<div class="flex-1 min-w-0">` +
                    `<p class="text-sm font-semibold text-gray-800 truncate">${title}</p>` +
                    (msg ? `<p class="text-xs text-gray-500 leading-snug line-clamp-2 mt-0.5">${msg}</p>` : '') +
                    `<p class="text-[11px] text-gray-400 mt-1">${timeStr}</p>` +
                    `</div></div>` +
                    `<button onclick="deleteNotif(${n.id})" class="flex-shrink-0 self-start mt-2 mr-3 text-gray-300 hover:text-red-400 transition text-lg leading-none" title="Dismiss">&times;</button>` +
                    `</div>`
                );
            }).join('');
        }

        async function loadNotifList() {
            try {
                const res = await fetch(NOTIF_BASE_URL + 'notifications/list?limit=15', { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                renderNotifList(data.items || []);
                const badge = document.getElementById('notif-badge');
                if (!badge) return;
                const count = data.unread || 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (e) {}
        }

        function toggleNotifMenu() {
            const menu = document.getElementById('notif-menu');
            if (!menu) return;
            const opening = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (opening) loadNotifList();
        }

        async function markAllNotifRead() {
            try {
                await fetch(NOTIF_BASE_URL + 'notifications/mark_read', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'all=1'
                });
            } catch (e) {}
            await loadNotifList();
            await refreshNotifSummary();
        }

        async function deleteNotif(id) {
            try {
                await fetch(NOTIF_BASE_URL + 'notifications/delete', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                });
            } catch (e) {}
            loadNotifList();
        }

        function openNotif(id, encodedUrl) {
            fetch(NOTIF_BASE_URL + 'notifications/mark_read', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            }).then(() => refreshNotifSummary()).catch(() => {});
            const url = decodeURIComponent(encodedUrl);
            if (url && url !== '#') window.location.href = url;
        }

        // Unified header scripts are loaded from partials/header.php
        document.addEventListener('DOMContentLoaded', function() {
            refreshNotifSummary();
            setSidebarCollapsed(false);
        });

        window.addEventListener('resize', function() {
            setSidebarCollapsed(sidebarCollapsed);
        });
    </script>
</body>
</html>
