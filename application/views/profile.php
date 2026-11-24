<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    /**
     * @var object $user
     * @var string $user_name
     */
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
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
    <style>
        /* Circular crop box styling for Cropper.js */
        .cropper-view-box {
            box-shadow: 0 0 0 1px #1FAE5B;
            border-radius: 50%;
            outline: 0;
        }
        .cropper-face {
            background-color: inherit;
            border-radius: 50%;
        }
        .cropper-dashed,
        .cropper-point,
        .cropper-line {
            display: none !important;
        }
        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }
        /* Custom styles for range slider track & thumb */
        input[type="range"]::-webkit-slider-runnable-track {
            background: #E5E7EB;
            height: 6px;
            border-radius: 9999px;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 9999px;
            background: #1FAE5B;
            cursor: pointer;
            margin-top: -5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            transition: transform 0.1s ease, background-color 0.1s ease;
        }
        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.15);
            background: #0F6B3E;
        }
    </style>
</head>
<body class="bg-brand-gray h-screen flex overflow-hidden text-gray-800 font-sans">

    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Profile', 'child' => 'Edit Profile']); ?>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-8 scroll-smooth">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">User Profile</h1>
                <p class="text-gray-500 mt-1">Manage your account settings and preferences.</p>
            </div>

            <!-- Flash Messages -->
            <?php if($this->session->flashdata('success')): ?>
            <div class="bg-green-50 border-l-4 border-brand-green p-4 mb-6 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-brand-green"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700"><?php echo $this->session->flashdata('success'); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($this->session->flashdata('error')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700"><?php echo $this->session->flashdata('error'); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-brand-deep h-32"></div>
                        <div class="px-6 pb-6 relative">
                            <div class="relative -top-12 mb-[-30px]">
                                <?php $avatar_url = (!empty($user->profile_image)) ? base_url($user->profile_image) : ("https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=ffffff&color=0F6B3E&rounded=true&bold=true&size=128"); ?>
                                <form id="profile-photo-form" action="<?php echo base_url('profile/upload_photo'); ?>" method="post" enctype="multipart/form-data" class="relative inline-block group">
                                    <div class="relative w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden cursor-pointer bg-white" onclick="document.getElementById('profile-photo-input').click();">
                                        <img id="profile-photo-img" src="<?php echo html_escape($avatar_url); ?>" alt="User Avatar" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=ffffff&color=0F6B3E&rounded=true&bold=true&size=128';" class="w-full h-full object-cover">
                                        <!-- Premium Overlay on Hover -->
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white text-[10px] font-bold transition-all duration-200 ease-in-out">
                                            <i class="fas fa-camera text-base mb-1"></i>
                                            <span>Change Photo</span>
                                        </div>
                                    </div>
                                    <input id="profile-photo-input" type="file" name="profile_image" accept="image/png,image/jpeg,image/webp" class="hidden">
                                    <button type="button" onclick="document.getElementById('profile-photo-input').click();" class="absolute -bottom-1 -right-1 w-9 h-9 rounded-full bg-white border border-gray-200 shadow-md flex items-center justify-center text-gray-600 hover:text-brand-green hover:border-brand-green transition-all duration-200 transform hover:scale-105 active:scale-95">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </form>
                                <p class="mt-2 text-xs text-gray-400">PNG, JPG, WEBP up to 2MB</p>
                            </div>
                            <div class="mt-4 pt-10">
                                <h2 class="text-xl font-bold text-gray-900"><?php echo $user->name; ?></h2>
                                <p class="text-sm text-gray-500"><?php echo $user->email; ?></p>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-50 rounded-full border border-green-100"><?php echo isset($user->role) ? ucfirst($user->role) : 'User'; ?></span>
                                    <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-50 rounded-full border border-blue-100">Verified</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forms -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Update Info -->
                    <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Personal Information</h3>
                        <form action="<?php echo base_url('profile/update'); ?>" method="post">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                    <input type="text" name="name" value="<?php echo $user->name; ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-colors" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" value="<?php echo $user->email; ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-colors" required>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-brand-green hover:bg-green-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Change Password</h3>
                        <form action="<?php echo base_url('profile/change_password'); ?>" method="post">
                            <div class="space-y-4 mb-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                                    <input type="password" name="current_password" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-colors" required>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                                        <input type="password" name="new_password" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-colors" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-colors" required>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                                    <i class="fas fa-key"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <div id="crop-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeCropModal()"></div>
        <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gray-900">Update Profile Picture</p>
                    <p class="text-xs text-gray-500 mt-0.5">Crop your photo then save.</p>
                </div>
                <button type="button" onclick="closeCropModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 text-gray-500 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-5 gap-6">
                <div class="md:col-span-3">
                    <div class="w-full bg-gray-50 rounded-xl border border-gray-100 overflow-hidden flex items-center justify-center" style="min-height: 360px;">
                        <img id="crop-image" src="" alt="" class="max-w-full">
                    </div>
                </div>
                <div class="md:col-span-2 flex flex-col gap-4">
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4">
                        <div class="flex justify-between items-center mb-1">
                            <p class="text-xs font-bold text-gray-700 uppercase tracking-wider">Zoom</p>
                            <span id="crop-zoom-percent" class="text-[10px] font-semibold text-gray-400">100%</span>
                        </div>
                        <div class="flex items-center gap-3 mt-1">
                            <button type="button" onclick="adjustZoomSlider(-10)" class="w-7 h-7 rounded bg-white border border-gray-200 flex items-center justify-center text-xs text-gray-500 hover:bg-gray-50 hover:text-brand-green active:scale-95 transition-all">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input id="crop-zoom" type="range" min="0" max="100" value="0" class="flex-1 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand-green" oninput="onCropZoom(this.value)">
                            <button type="button" onclick="adjustZoomSlider(10)" class="w-7 h-7 rounded bg-white border border-gray-200 flex items-center justify-center text-xs text-gray-500 hover:bg-gray-50 hover:text-brand-green active:scale-95 transition-all">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-4">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider">Adjustments</p>
                        <div class="grid grid-cols-2 gap-2 mt-3">
                            <button type="button" onclick="rotateCrop(-90)" class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2">
                                <i class="fas fa-rotate-left"></i> Left 90°
                            </button>
                            <button type="button" onclick="rotateCrop(90)" class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2">
                                <i class="fas fa-rotate-right"></i> Right 90°
                            </button>
                            <button type="button" onclick="flipCrop('h')" class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2">
                                <i class="fas fa-arrows-left-right"></i> Flip H
                            </button>
                            <button type="button" onclick="flipCrop('v')" class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2">
                                <i class="fas fa-arrows-up-down"></i> Flip V
                            </button>
                        </div>
                    </div>
                    <div class="mt-auto flex items-center gap-3">
                        <button type="button" onclick="closeCropModal()" class="flex-1 px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button id="crop-save-btn" type="button" onclick="saveCroppedPhoto()" class="flex-1 px-4 py-2.5 rounded-lg bg-brand-green hover:bg-green-600 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Save
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 text-center">Square crop, 512×512 output.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Sidebar Script -->
    <script>
        const NOTIF_BASE_URL = <?php echo json_encode(base_url()); ?>;
        const PROFILE_UPLOAD_URL = <?php echo json_encode(base_url('profile/upload_photo')); ?>;

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
        </script>

    <script>
        let cropperInstance = null;
        let cropZoomMin = -0.5;
        let cropZoomMax = 1.5;
        let flipH = 1;
        let flipV = 1;

        function openCropModal(file) {
            const modal = document.getElementById('crop-modal');
            const img = document.getElementById('crop-image');
            const zoom = document.getElementById('crop-zoom');
            const pct = document.getElementById('crop-zoom-percent');
            const saveBtn = document.getElementById('crop-save-btn');
            if (!modal || !img || !zoom || !saveBtn) return;

            zoom.value = '0';
            if (pct) pct.textContent = '100%';
            saveBtn.disabled = true;
            saveBtn.innerHTML = 'Save';

            const url = URL.createObjectURL(file);
            img.src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            flipH = 1;
            flipV = 1;

            img.onload = function() {
                if (cropperInstance) {
                    cropperInstance.destroy();
                    cropperInstance = null;
                }
                cropperInstance = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                    guides: false,
                    center: true,
                    highlight: false,
                    ready() {
                        saveBtn.disabled = false;
                    }
                });
            };
        }

        function closeCropModal() {
            const modal = document.getElementById('crop-modal');
            const img = document.getElementById('crop-image');
            const input = document.getElementById('profile-photo-input');
            const saveBtn = document.getElementById('crop-save-btn');
            const cancelBtn = document.querySelector('#crop-modal button[onclick="closeCropModal()"]');
            
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
            if (img && img.src && img.src.startsWith('blob:')) {
                try { URL.revokeObjectURL(img.src); } catch (e) {}
                img.src = '';
            }
            if (input) input.value = '';
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = 'Save';
            }
            if (cancelBtn) {
                cancelBtn.disabled = false;
            }

            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function onCropZoom(val) {
            if (!cropperInstance) return;
            const ratio = parseFloat(val) / 100;
            const zoom = cropZoomMin + (cropZoomMax - cropZoomMin) * ratio;
            cropperInstance.zoomTo(1 + zoom);

            // Update percentage readout
            const pct = document.getElementById('crop-zoom-percent');
            if (pct) {
                pct.textContent = Math.round(100 + zoom * 100) + '%';
            }
        }

        function adjustZoomSlider(delta) {
            const slider = document.getElementById('crop-zoom');
            if (!slider) return;
            let newValue = parseInt(slider.value) + delta;
            if (newValue < 0) newValue = 0;
            if (newValue > 100) newValue = 100;
            slider.value = newValue;
            onCropZoom(newValue);
        }

        function rotateCrop(deg) {
            if (!cropperInstance) return;
            cropperInstance.rotate(deg);
        }

        function flipCrop(dir) {
            if (!cropperInstance) return;
            if (dir === 'h') {
                flipH = -flipH;
                cropperInstance.scaleX(flipH);
            } else {
                flipV = -flipV;
                cropperInstance.scaleY(flipV);
            }
        }

        function saveCroppedPhoto() {
            if (!cropperInstance) return;
            const saveBtn = document.getElementById('crop-save-btn');
            const cancelBtn = document.querySelector('#crop-modal button[onclick="closeCropModal()"]');

            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...';
            }
            if (cancelBtn) {
                cancelBtn.disabled = true;
            }

            const canvas = cropperInstance.getCroppedCanvas({ width: 512, height: 512, fillColor: '#ffffff' });
            if (!canvas) {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Save';
                }
                if (cancelBtn) cancelBtn.disabled = false;
                return;
            }

            canvas.toBlob(async (blob) => {
                if (!blob) {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = 'Save';
                    }
                    if (cancelBtn) cancelBtn.disabled = false;
                    return;
                }

                const fd = new FormData();
                fd.append('profile_image', blob, 'profile.jpg');
                fd.append('ajax', '1');

                try {
                    const res = await fetch(PROFILE_UPLOAD_URL, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    });
                    const text = await res.text();
                    let data = null;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        const start = text.indexOf('{');
                        const end = text.lastIndexOf('}');
                        if (start !== -1 && end !== -1 && end > start) {
                            try {
                                data = JSON.parse(text.slice(start, end + 1));
                            } catch (e2) {}
                        }
                    }
                    if (!data) {
                        alert('Upload failed: ' + String(text).slice(0, 180));
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = 'Save';
                        }
                        if (cancelBtn) cancelBtn.disabled = false;
                        return;
                    }
                    if (data.status !== 'success' || !data.url) {
                        alert(data.message ? data.message : 'Upload failed');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = 'Save';
                        }
                        if (cancelBtn) cancelBtn.disabled = false;
                        return;
                    }

                    const url = data.url + (data.url.includes('?') ? '&' : '?') + 't=' + Date.now();
                    const mainImg = document.getElementById('profile-photo-img');
                    const navImg = document.getElementById('nav-user-avatar');
                    if (mainImg) mainImg.src = url;
                    if (navImg) navImg.src = url;
                    closeCropModal();
                } catch (e) {
                    alert('Upload failed: ' + (e && e.message ? e.message : 'Unknown error'));
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = 'Save';
                    }
                    if (cancelBtn) cancelBtn.disabled = false;
                }
            }, 'image/jpeg', 0.92);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('profile-photo-input');
            if (input) {
                input.addEventListener('change', function() {
                    const file = input.files && input.files[0] ? input.files[0] : null;
                    if (!file) return;
                    openCropModal(file);
                });
            }
        });
    </script>
</body>
</html>
