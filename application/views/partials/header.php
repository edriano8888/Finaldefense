<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI =& get_instance();
if (!isset($CI->Brand_model)) {
    $CI->load->model('Brand_model');
}
$active_brand_id = (int)$CI->session->userdata('brand_id');
$active_brand = $active_brand_id ? $CI->Brand_model->get_brand($active_brand_id) : null;
$active_brand_name = $active_brand ? $active_brand->name : 'Select Brand';
$available_brands = $CI->Brand_model->get_all_brands();

$user_name = $CI->session->userdata('name') ?: 'User';
$user_role = $CI->session->userdata('role') ?: 'Employee';
$user_profile_image = $CI->session->userdata('profile_image');
?>

<!-- Premium Header Style Variables & Animations -->
<style>
    html, body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    .glass-header {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    }
    
    /* Dropdown Animations */
    .dropdown-menu-animate {
        transform-origin: top right;
        transition: opacity 200ms ease, transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
    }
    .dropdown-menu-animate.hidden {
        display: none !important;
        opacity: 0;
        transform: scale(0.95) translateY(-8px);
    }
    .dropdown-menu-animate:not(.hidden) {
        opacity: 1;
        transform: scale(1) translateY(0);
    }

    /* Pulse animation for active online badge */
    @keyframes status-pulse {
        0% { transform: scale(0.95); opacity: 1; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); opacity: 0.5; box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); opacity: 0; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    .status-pulse-indicator {
        animation: status-pulse 2s infinite;
    }

    /* Notification dot pulse */
    @keyframes badge-pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 5px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    .badge-pulse-indicator {
        animation: badge-pulse 1.8s infinite;
    }

    /* Styled notification list scrollbar */
    .notif-list-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .notif-list-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .notif-list-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .notif-list-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<!-- Top Navigation Bar -->
<header class="h-16 glass-header flex items-center justify-between px-6 z-20 sticky top-0 shadow-sm transition-all duration-300">
    <!-- Left Section: Mobile Toggle & Breadcrumbs -->
    <div class="flex items-center gap-4">
        <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-xl hover:bg-gray-100 transition-colors" onclick="toggleSidebar()">
            <i class="fas fa-bars text-xl"></i>
        </button>
        
        <!-- Breadcrumbs Layout -->
        <nav class="hidden md:flex items-center text-sm font-medium text-gray-500">
            <a href="<?php echo base_url('influencer/dashboard'); ?>" class="text-gray-400 hover:text-brand-deep transition-colors flex items-center">
                <i class="fas fa-home text-base"></i>
            </a>
            <?php if (isset($parent)): ?>
                <i class="fas fa-chevron-right text-[10px] mx-3 text-gray-300"></i>
                <span class="text-gray-400 font-semibold cursor-default transition-colors"><?php echo html_escape($parent); ?></span>
            <?php endif; ?>
            <?php if (isset($child)): ?>
                <i class="fas fa-chevron-right text-[10px] mx-3 text-gray-300"></i>
                <span class="text-brand-deep bg-brand-light border border-emerald-100/80 px-3 py-1 rounded-full font-bold shadow-xs hover:shadow-sm transition-all"><?php echo html_escape($child); ?></span>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Right Section: Actions, Notifications, Brand Switcher, User Dropdown -->
    <div class="flex items-center gap-4">
        
        <!-- Closed Collaboration Process Button -->
        <button type="button" onclick="toggleWorkflowModal()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-green/10 text-brand-green hover:bg-brand-green hover:text-white border border-brand-green/20 font-bold text-xs transition-all shadow-xs" title="View 10-Step Closed Collaboration Process">
            <i class="fas fa-sitemap"></i>
            <span class="hidden lg:inline">Collaboration Process</span>
        </button>

        <!-- Notification Dropdown Container -->
        <div class="relative" id="notif-container">
            <button type="button" onclick="toggleNotifMenu()" class="relative p-2 text-gray-400 hover:text-brand-deep transition-all rounded-full hover:bg-slate-100 focus:outline-none">
                <i class="far fa-bell text-xl"></i>
                <span id="notif-badge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center px-1 border-2 border-white leading-none badge-pulse-indicator"></span>
            </button>
            
            <div id="notif-menu" class="hidden dropdown-menu-animate absolute right-0 top-full mt-2 w-[420px] max-w-[90vw] bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <span class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <i class="fas fa-bell text-brand-green text-sm"></i> Notifications
                    </span>
                    <button type="button" onclick="markAllNotifRead()" class="text-xs text-brand-deep hover:text-[#0c4e2d] font-bold transition">Mark all read</button>
                </div>
                <div id="notif-list" class="overflow-y-auto notif-list-scroll" style="max-height: 380px;">
                    <!-- Rendered via JS -->
                </div>
                <div class="px-4 py-3.5 border-t border-slate-100 text-center">
                    <?php 
                        $audit_log_path = 'influencer/activity_logs';
                        if (strtolower($user_role) === 'admin') {
                            $audit_log_path = 'admin/activity_logs';
                        } elseif (strtolower($user_role) === 'manager') {
                            $audit_log_path = 'manager/activity_logs';
                        }
                    ?>
                    <a href="<?php echo site_url($audit_log_path); ?>" class="text-xs text-brand-green hover:text-brand-deep font-bold transition">View Full Audit Log</a>
                </div>
            </div>
        </div>

        <!-- Brand Menu Selector Dropdown -->
        <div class="relative" id="brand-menu-container">
            <button type="button" onclick="toggleBrandMenu()" class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200/80 bg-white hover:bg-slate-50 transition-all focus:outline-none shadow-xs">
                <i class="fas fa-building text-slate-400 text-sm"></i>
                <span class="text-sm font-bold text-slate-700 max-w-[180px] truncate"><?php echo html_escape($active_brand_name); ?></span>
                <i class="fas fa-chevron-down text-slate-400 text-[10px] ml-1 transition-transform" id="brand-chevron"></i>
            </button>
            
            <div id="brand-menu" class="hidden dropdown-menu-animate absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                <div class="px-4 py-2 bg-slate-50/50">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black">Active Organization</p>
                    <p class="text-sm font-extrabold text-slate-800 truncate mt-0.5"><?php echo html_escape($active_brand_name); ?></p>
                </div>
                <div class="border-t border-slate-100 my-2"></div>
                <div class="max-h-64 overflow-y-auto notif-list-scroll">
                    <?php if (!empty($available_brands)): ?>
                        <?php foreach ($available_brands as $b): ?>
                            <a href="<?php echo base_url('auth/switch_brand/' . (int)$b->id); ?>" class="flex items-center justify-between gap-3 px-4 py-2.5 text-sm hover:bg-slate-50 transition-colors <?php echo ((int)$b->id === $active_brand_id) ? 'text-brand-green font-bold bg-green-50/30' : 'text-slate-600 font-medium'; ?>">
                                <span class="truncate"><?php echo html_escape($b->name); ?></span>
                                <?php if ((int)$b->id === $active_brand_id): ?>
                                    <i class="fas fa-check text-xs text-brand-green"></i>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-4 py-3 text-xs text-slate-500 font-medium">No brands available.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="h-6 w-px bg-slate-200 mx-1"></div>

        <!-- User Options Dropdown -->
        <div class="relative" id="user-menu-container">
            <button onclick="toggleUserMenu()" class="flex items-center gap-3 cursor-pointer group focus:outline-none">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-700 group-hover:text-brand-deep transition-colors"><?php echo html_escape($user_name); ?></p>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider"><?php echo html_escape(ucfirst($user_role)); ?></p>
                </div>
                
                <!-- Avatar with glowing online status -->
                <div class="relative flex-shrink-0">
                    <?php 
                    $nav_fallback = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=1FAE5B&color=fff&rounded=true&bold=true"; 
                    $nav_avatar = !empty($user_profile_image) ? base_url($user_profile_image) : $nav_fallback; 
                    ?>
                    <img src="<?php echo html_escape($nav_avatar); ?>" alt="User" onerror="this.onerror=null;this.src='<?php echo $nav_fallback; ?>';" class="w-9 h-9 rounded-full shadow-sm border-2 border-white group-hover:border-brand-green transition-all object-cover">
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></div>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white status-pulse-indicator"></div>
                </div>
                <i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform" id="user-chevron"></i>
            </button>
            
            <div id="user-menu" class="hidden dropdown-menu-animate absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black">Logged in as</p>
                    <p class="text-xs font-bold text-slate-700 truncate mt-0.5"><?php echo html_escape($user_name); ?></p>
                </div>
                
                <?php if (isset($user_role) && $user_role === 'admin'): ?>
                    <a href="<?php echo base_url('admin'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-brand-green transition-colors font-medium">
                        <i class="fas fa-shield-alt w-5 text-slate-400"></i> Admin Panel
                    </a>
                <?php endif; ?>
                
                <a href="<?php echo base_url('profile'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-brand-green transition-colors font-medium">
                    <i class="fas fa-user-circle w-5 text-slate-400"></i> My Profile
                </a>
                
                <a href="<?php echo base_url('ambassadors/brand_details'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-brand-green transition-colors font-medium">
                    <i class="fas fa-building w-5 text-slate-400"></i> Brand Profile
                </a>

                <a href="<?php echo base_url('settings'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-brand-green transition-colors font-medium">
                    <i class="fas fa-sliders-h w-5 text-slate-400"></i> Preferences
                </a>
                
                <div class="border-t border-slate-100 my-1"></div>
                
                <a href="<?php echo base_url('auth/logout'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors font-bold">
                    <i class="fas fa-sign-out-alt w-5 text-red-400"></i> Log Out
                </a>
            </div>
        </div>

    </div>
</header>

<!-- Header Scripts: Global Notification & Dropdown Control -->
<script>
    const HEADER_BASE_URL = <?php echo json_encode(rtrim(site_url(), '/') . '/'); ?>;

    function toggleNotifMenu() {
        const menu = document.getElementById('notif-menu');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        closeAllHeaderMenus();
        if (isHidden) {
            menu.classList.remove('hidden');
            loadHeaderNotifs();
        }
    }

    function toggleBrandMenu() {
        const menu = document.getElementById('brand-menu');
        const chev = document.getElementById('brand-chevron');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        closeAllHeaderMenus();
        if (isHidden) {
            menu.classList.remove('hidden');
            if (chev) chev.classList.add('rotate-180');
        }
    }

    function toggleUserMenu() {
        const menu = document.getElementById('user-menu');
        const chev = document.getElementById('user-chevron');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        closeAllHeaderMenus();
        if (isHidden) {
            menu.classList.remove('hidden');
            if (chev) chev.classList.add('rotate-180');
        }
    }

    function closeAllHeaderMenus() {
        const menus = ['notif-menu', 'brand-menu', 'user-menu'];
        menus.forEach(id => {
            const m = document.getElementById(id);
            if (m) m.classList.add('hidden');
        });
        const brandChevron = document.getElementById('brand-chevron');
        if (brandChevron) brandChevron.classList.remove('rotate-180');
        const userChevron = document.getElementById('user-chevron');
        if (userChevron) userChevron.classList.remove('rotate-180');
    }

    // Click outside to close menus
    document.addEventListener('click', function(event) {
        const notifContainer = document.getElementById('notif-container');
        const brandContainer = document.getElementById('brand-menu-container');
        const userContainer = document.getElementById('user-menu-container');
        
        if (notifContainer && !notifContainer.contains(event.target)) {
            const m = document.getElementById('notif-menu');
            if (m) m.classList.add('hidden');
        }
        if (brandContainer && !brandContainer.contains(event.target)) {
            const m = document.getElementById('brand-menu');
            if (m) m.classList.add('hidden');
            const chev = document.getElementById('brand-chevron');
            if (chev) chev.classList.remove('rotate-180');
        }
        if (userContainer && !userContainer.contains(event.target)) {
            const m = document.getElementById('user-menu');
            if (m) m.classList.add('hidden');
            const chev = document.getElementById('user-chevron');
            if (chev) chev.classList.remove('rotate-180');
        }
    });

    // --- Notification System ---
    const _HEADER_NOTIF_MAP = {
        login:      { icon: 'fa-sign-in-alt',          bg: 'bg-blue-50 border-blue-100',   fg: 'text-blue-600'   },
        success:    { icon: 'fa-check-circle',         bg: 'bg-emerald-50 border-emerald-100',  fg: 'text-emerald-600'  },
        warning:    { icon: 'fa-exclamation-triangle', bg: 'bg-amber-50 border-amber-100', fg: 'text-amber-600' },
        error:      { icon: 'fa-times-circle',          bg: 'bg-red-50 border-red-100',    fg: 'text-red-600'    },
        influencer: { icon: 'fa-user-plus',             bg: 'bg-indigo-50 border-indigo-100', fg: 'text-indigo-600' },
        campaign:   { icon: 'fa-handshake',            bg: 'bg-purple-50 border-purple-100', fg: 'text-purple-600' },
        task:       { icon: 'fa-tasks',                bg: 'bg-teal-50 border-teal-100',   fg: 'text-teal-600'   },
        info:       { icon: 'fa-bell',                 bg: 'bg-slate-50 border-slate-100',   fg: 'text-slate-500'   },
    };

    function escapeHtml(str) {
        return String(str || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function renderHeaderNotifIcon(type, customIcon) {
        const item = _HEADER_NOTIF_MAP[type] || _HEADER_NOTIF_MAP.info;
        const icon = (customIcon && customIcon !== 'fa-bell') ? customIcon : item.icon;
        return `<span class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center ${item.bg} border border-dashed"><i class="fas ${icon} text-sm ${item.fg}"></i></span>`;
    }

    function formatHeaderNotifTime(dateStr) {
        if (!dateStr) return 'Just now';
        const now  = Date.now();
        const then = new Date(dateStr.replace(' ', 'T')).getTime();
        if (isNaN(then)) return dateStr;
        const diff = Math.max(0, Math.floor((now - then) / 1000));
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + ' min ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hr ago';
        if (diff < 172800) return 'Yesterday';
        return Math.floor(diff / 86400) + ' days ago';
    }

    async function loadHeaderNotifs() {
        try {
            const res = await fetch(HEADER_BASE_URL + 'notifications/list?limit=15', { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            const items = data.items || [];
            const wrap = document.getElementById('notif-list');
            if (!wrap) return;

            if (items.length === 0) {
                wrap.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-14 text-slate-300">
                        <i class="far fa-bell-slash text-4xl mb-3 text-slate-300/80"></i>
                        <p class="text-sm font-bold text-slate-400">All caught up!</p>
                        <p class="text-[11px] text-slate-400 mt-1">No new notifications here.</p>
                    </div>`;
                return;
            }

            wrap.innerHTML = items.map(n => {
                const unreadBar = n.is_read ? '' : '<span class="absolute left-0 top-0 h-full w-1 bg-brand-green rounded-l-md"></span>';
                const rowBg = n.is_read ? '' : 'bg-slate-50/50';
                const iconHtml = renderHeaderNotifIcon(n.type, n.icon);
                const timeStr = formatHeaderNotifTime(n.created_at);
                const title = escapeHtml(n.title || 'Notification');
                const msg = escapeHtml(n.message || '');
                const url = n.url || '';
                
                return `
                    <div class="relative flex items-start gap-0 border-b border-slate-50 ${rowBg} hover:bg-slate-50/80 transition duration-150">
                        ${unreadBar}
                        <div onclick="openHeaderNotif(${n.id},'${encodeURIComponent(url)}')" class="flex items-start gap-3 flex-1 min-w-0 px-5 py-3.5 cursor-pointer">
                            ${iconHtml}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-700 truncate">${title}</p>
                                ${msg ? `<p class="text-xs text-slate-400 leading-snug line-clamp-2 mt-0.5">${msg}</p>` : ''}
                                <p class="text-[10px] text-slate-400 font-medium mt-1"><i class="far fa-clock mr-1 text-[9px]"></i>${timeStr}</p>
                            </div>
                        </div>
                        <button onclick="dismissHeaderNotif(${n.id}, event)" class="flex-shrink-0 self-start mt-3.5 mr-4 text-slate-300 hover:text-red-500 transition text-lg leading-none" title="Dismiss">&times;</button>
                    </div>`;
            }).join('');

            updateHeaderBadge(data.unread || 0);
        } catch (e) {}
    }

    function updateHeaderBadge(count) {
        const badge = document.getElementById('notif-badge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    async function refreshHeaderNotifBadge() {
        try {
            const res = await fetch(HEADER_BASE_URL + 'notifications/summary', { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            updateHeaderBadge(data.unread || 0);
        } catch (e) {}
    }

    async function markAllNotifRead() {
        try {
            await fetch(HEADER_BASE_URL + 'notifications/mark_read', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'all=1'
            });
        } catch (e) {}
        loadHeaderNotifs();
        refreshHeaderNotifBadge();
    }

    async function dismissHeaderNotif(id, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        try {
            await fetch(HEADER_BASE_URL + 'notifications/delete', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            });
        } catch (e) {}
        loadHeaderNotifs();
        refreshHeaderNotifBadge();
    }

    function openHeaderNotif(id, encodedUrl) {
        fetch(HEADER_BASE_URL + 'notifications/mark_read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        }).then(() => refreshHeaderNotifBadge()).catch(() => {});
        const url = decodeURIComponent(encodedUrl);
        if (url && url !== '#') window.location.href = url;
    }

    function toggleWorkflowModal() {
        const modal = document.getElementById('workflow-modal');
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    // Refresh badge count on load
    document.addEventListener('DOMContentLoaded', () => {
        refreshHeaderNotifBadge();
        // Auto-refresh notifications badge every 60 seconds
        setInterval(refreshHeaderNotifBadge, 60000);
    });
</script>

<!-- Closed Collaboration Process Modal -->
<div id="workflow-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden border border-gray-100 animate-fade-in my-8 max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="p-6 bg-gradient-to-r from-gray-900 via-gray-800 to-[#0F6B3E] text-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-lg">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold tracking-tight">Typical Closed Collaboration Process</h3>
                    <p class="text-xs text-gray-300 mt-0.5">End-to-End 10-Step Campaign Workflow in Instroom</p>
                </div>
            </div>
            <button onclick="toggleWorkflowModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Body: 10 Steps Grid -->
        <div class="p-6 overflow-y-auto space-y-4 flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Step 1 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">1</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">1. Brand / Campaign Requirement</h4>
                            <a href="<?php echo base_url('manager/campaign_planning'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Client provides campaign details. Armful Media identifies target audience, platform (TikTok, IG, YT), content type, budget, & deliverables.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">2</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">2. Influencer Sourcing</h4>
                            <a href="<?php echo base_url('discovery'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Influencer Relations team searches for creators on TikTok, Instagram, or YouTube based on niche, engagement, location, and fit.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">3</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">3. Influencer Shortlisting</h4>
                            <a href="<?php echo base_url('influencer/manage'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Suitable influencers are added to a shortlist with performance data organized for brand/client review.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">4</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">4. Direct Invitation / Outreach</h4>
                            <a href="<?php echo base_url('influencer/pipeline'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Direct contact with selected creators (closed invitation) with campaign details & asking for interest.</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-pink-100 text-pink-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">5</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">5. Negotiation & Confirmation</h4>
                            <a href="<?php echo base_url('influencer/pipeline'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Discuss requirements, compensation, products, deadlines, & deliverables. Once agreed, creator is confirmed.</p>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-cyan-100 text-cyan-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">6</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">6. Product / Order Processing</h4>
                            <a href="<?php echo base_url('influencer/closed_collaborations'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Order & shipping details, tracking active, discount codes, or referral tracking links are processed.</p>
                    </div>
                </div>

                <!-- Step 7 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">7</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">7. Content Creation</h4>
                            <a href="<?php echo base_url('influencer/my_tasks'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Influencer creates required video/image content according to the campaign brief & guidelines.</p>
                    </div>
                </div>

                <!-- Step 8 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">8</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">8. Submission & Monitoring</h4>
                            <a href="<?php echo base_url('influencer/closed_collaborations'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Monitor deliverable completion, live post link verification, and campaign guideline compliance.</p>
                    </div>
                </div>

                <!-- Step 9 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 font-extrabold text-sm flex items-center justify-center flex-shrink-0">9</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">9. Performance Tracking</h4>
                            <a href="<?php echo base_url('influencer/analytics'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Collect real-time metrics: views, likes, comments, engagement rate, link clicks, and sales GMV.</p>
                    </div>
                </div>

                <!-- Step 10 -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold text-sm flex items-center justify-center flex-shrink-0">10</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm">10. Reporting</h4>
                            <a href="<?php echo base_url('manager/reports'); ?>" class="text-[10px] font-bold text-emerald-600 hover:underline">View Module &rarr;</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Campaign results compiled into executive PDF / CSV reports for client presentation & review.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 flex-shrink-0">
            <span>Armful Media - Closed Collaboration Standard Operating Procedure</span>
            <button onclick="toggleWorkflowModal()" class="px-4 py-2 bg-[#0F6B3E] text-white font-bold rounded-xl hover:bg-emerald-800 transition-all">Close Guide</button>
        </div>
    </div>
</div>

<?php $this->load->view('partials/toast'); ?>

