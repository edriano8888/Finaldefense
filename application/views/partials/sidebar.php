<?php
$CI =& get_instance();
$user_role = $CI->session->userdata('role');
$user_id = $CI->session->userdata('user_id');

// Dynamic unread count calculation
$unread_count = 0;
if ($user_id) {
    if (!isset($CI->Email_model)) {
        $CI->load->model('Email_model');
    }
    if (isset($CI->Email_model) && method_exists($CI->Email_model, 'get_unread_count')) {
        $unread_count = (int)$CI->Email_model->get_unread_count(null, $user_id);
    }
}

// Active link helper logic
if (!function_exists('is_sidebar_active')) {
    function is_sidebar_active($pattern) {
        $current_uri = uri_string();
        if ($pattern === 'email') {
            return ($current_uri === 'email' || (strpos($current_uri, 'email/') === 0 && strpos($current_uri, 'email/drips') === false));
        }
        if ($pattern === 'manager') {
            return ($current_uri === 'manager' || (strpos($current_uri, 'manager/') === 0 && strpos($current_uri, 'manager/team_management') === false && strpos($current_uri, 'manager/activity_logs') === false));
        }
        if ($pattern === 'ambassadors') {
            return ($current_uri === 'ambassadors' || strpos($current_uri, 'ambassadors/') === 0 || strpos($current_uri, 'influencer/brand_details') === 0);
        }
        return ($current_uri === $pattern || strpos($current_uri, $pattern . '/') === 0);
    }
}

if (!function_exists('get_sidebar_class')) {
    function get_sidebar_class($pattern) {
        if (is_sidebar_active($pattern)) {
            return "sidebar-item active group flex items-center px-4 py-3 text-sm font-medium rounded-[2rem] transition-all duration-200 text-white bg-white/10 shadow-lg shadow-black/5";
        }
        return "sidebar-item group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-white/70 hover:text-white";
    }
}

if (!function_exists('get_sidebar_icon_class')) {
    function get_sidebar_icon_class($pattern, $base_icon) {
        if (is_sidebar_active($pattern)) {
            return "$base_icon w-6 text-lg opacity-100 transition-opacity";
        }
        return "$base_icon w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity";
    }
}
?>

<style>
    /* Smooth width transition for sidebar */
    #sidebar {
        transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Text elements styling */
    #sidebar .sidebar-brand-text,
    #sidebar nav p,
    #sidebar nav a.sidebar-item span {
        transition: opacity 200ms ease, max-width 300ms cubic-bezier(0.4, 0, 0.2, 1), margin 300ms cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
        white-space: nowrap;
        vertical-align: middle;
        overflow: hidden;
    }

    #sidebar:not(.collapsed) .sidebar-brand-text,
    #sidebar:not(.collapsed) nav a.sidebar-item span {
        opacity: 1;
        max-width: 200px;
    }
    
    #sidebar:not(.collapsed) nav p {
        opacity: 1;
        max-width: 200px;
    }

    /* Collapsed states */
    #sidebar.collapsed {
        width: 5rem !important; /* w-20 */
    }

    #sidebar.collapsed .sidebar-brand-text,
    #sidebar.collapsed nav p,
    #sidebar.collapsed nav a.sidebar-item span {
        opacity: 0 !important;
        max-width: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        pointer-events: none;
    }

    #sidebar.collapsed nav p {
        margin-top: 0.75rem !important;
        margin-bottom: 0.75rem !important;
        height: 1px;
        background: rgba(255,255,255,0.1);
        content: '';
        overflow: hidden;
    }

    #sidebar.collapsed nav a.sidebar-item {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 3.25rem;
        height: 3.25rem;
        margin-left: auto;
        margin-right: auto;
        border-radius: 9999px; /* Make item round when collapsed for a premium app feel */
    }
    
    /* Center active item cleanly */
    #sidebar.collapsed nav a.sidebar-item.active {
        border-radius: 9999px !important;
    }

    #sidebar.collapsed .sidebar-brand-wrapper {
        justify-content: center !important;
        width: 100%;
        padding: 0 !important;
    }

    /* Brand Logo alignment when collapsed */
    #sidebar.collapsed .sidebar-brand-wrapper > div {
        margin: 0 auto !important;
    }

    #sidebar.collapsed .h-16 flex {
        justify-content: center !important;
    }
    
    /* Hide the brand close button on mobile, but keep it centered */
    #sidebar.collapsed .md\:hidden {
        display: none !important;
    }

    /* Custom CSS Premium Tooltips */
    #sidebar.collapsed nav a.sidebar-item {
        position: relative;
    }

    #sidebar.collapsed nav a.sidebar-item::after {
        content: attr(data-tooltip);
        position: absolute;
        left: calc(100% + 12px);
        top: 50%;
        transform: translateY(-50%) scale(0.9);
        background-color: #111827; /* Dark Slate Gray */
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        opacity: 0;
        pointer-events: none;
        white-space: nowrap;
        z-index: 9999;
        transition: opacity 150ms ease, transform 150ms cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    #sidebar.collapsed nav a.sidebar-item:hover::after {
        opacity: 1;
        transform: translateY(-50%) scale(1);
    }

    #sidebar.collapsed nav a.sidebar-item::before {
        content: '';
        position: absolute;
        left: calc(100% + 6px);
        top: 50%;
        transform: translateY(-50%) scale(0.9);
        border-width: 4px;
        border-style: solid;
        border-color: transparent #111827 transparent transparent;
        opacity: 0;
        pointer-events: none;
        z-index: 9999;
        transition: opacity 150ms ease, transform 150ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    #sidebar.collapsed nav a.sidebar-item:hover::before {
        opacity: 1;
        transform: translateY(-50%) scale(1);
    }
</style>

<!-- Sidebar Container -->
<aside id="sidebar" class="relative w-72 bg-[#0F6B3E] flex-shrink-0 flex flex-col transition-all duration-300 hidden md:flex fixed md:relative z-30 h-full top-0 left-0 shadow-xl">
    <!-- Inline state restoration script to prevent rendering layout shifts/flickering -->
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('sidebarCollapsed');
                const sidebar = document.getElementById('sidebar');
                if (saved === 'true' && window.innerWidth >= 768) {
                    sidebar.classList.add('collapsed');
                    sidebar.style.width = '5rem';
                }
            } catch(e) {}
        })();
    </script>

    <div class="h-16 flex items-center px-6 border-b border-white/10 bg-gradient-to-r from-white/[0.06] to-transparent">
        <div class="flex items-center justify-between w-full">
            <div class="sidebar-brand-wrapper flex items-center gap-3 min-w-0">
                <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom" class="w-10 h-10 object-contain flex-shrink-0 drop-shadow-sm">
                <span class="sidebar-brand-text text-[1.05rem] font-extrabold tracking-wide text-white whitespace-nowrap">INSTROOM</span>
            </div>
            <button class="md:hidden text-white/70 hover:text-white transition-colors focus:outline-none" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Collapse Button -->
    <button id="sidebar-collapse-btn" type="button" class="hidden md:inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/25 text-white hover:bg-white/35 border border-white/30 shadow-xl transition-all absolute top-1/2 right-0 translate-x-1/2 -translate-y-1/2 hover:scale-105 z-40 focus:outline-none" onclick="toggleSidebarCollapse()">
        <i class="fas fa-chevron-left"></i>
    </button>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5">
        <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-3">Main Menu</p>
        
        <a href="<?php echo base_url('influencer/dashboard'); ?>" class="<?php echo get_sidebar_class('influencer/dashboard'); ?>" data-tooltip="Dashboard">
            <i class="<?php echo get_sidebar_icon_class('influencer/dashboard', 'fas fa-tachometer-alt'); ?>"></i>
            <span class="ml-3">Dashboard</span>
        </a>
        
        <a href="<?php echo base_url('discovery'); ?>" class="<?php echo get_sidebar_class('discovery'); ?>" data-tooltip="Discovery">
            <i class="<?php echo get_sidebar_icon_class('discovery', 'fas fa-search'); ?>"></i>
            <span class="ml-3">Discovery</span>
        </a>
        
        <a href="<?php echo base_url('influencer/manage'); ?>" class="<?php echo get_sidebar_class('influencer/manage'); ?>" data-tooltip="Influencer List">
            <i class="<?php echo get_sidebar_icon_class('influencer/manage', 'fas fa-list-ul'); ?>"></i>
            <span class="ml-3">Influencer List</span>
        </a>

        <a href="<?php echo base_url('email'); ?>" class="<?php echo get_sidebar_class('email'); ?>" data-tooltip="Inbox">
            <i class="<?php echo get_sidebar_icon_class('email', 'fas fa-inbox'); ?>"></i>
            <span class="ml-3">Inbox</span>
            <?php if ($unread_count > 0): ?>
                <span class="ml-auto bg-brand-green text-white text-xs font-bold px-2 py-0.5 rounded-full whitespace-nowrap badge-unread"><?= $unread_count ?></span>
            <?php endif; ?>
        </a>

        <a href="<?php echo base_url('email/drips'); ?>" class="<?php echo get_sidebar_class('email/drips'); ?>" data-tooltip="Email Drips">
            <i class="<?php echo get_sidebar_icon_class('email/drips', 'fas fa-envelope-open-text'); ?>"></i>
            <span class="ml-3">Email Drips</span>
        </a>

        <a href="<?php echo base_url('influencer/my_tasks'); ?>" class="<?php echo get_sidebar_class('influencer/my_tasks'); ?>" data-tooltip="My Tasks">
            <i class="<?php echo get_sidebar_icon_class('influencer/my_tasks', 'fas fa-check-square'); ?>"></i>
            <span class="ml-3">My Tasks</span>
        </a>

        <a href="<?php echo base_url('influencer/pipeline'); ?>" class="<?php echo get_sidebar_class('influencer/pipeline'); ?>" data-tooltip="Pipeline">
            <i class="<?php echo get_sidebar_icon_class('influencer/pipeline', 'fas fa-filter'); ?>"></i>
            <span class="ml-3">Pipeline</span>
        </a>

        <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mt-8 mb-3">Campaigns</p>

        <a href="<?php echo base_url('influencer/closed_collaborations'); ?>" class="<?php echo get_sidebar_class('influencer/closed_collaborations'); ?>" data-tooltip="Closed">
            <i class="<?php echo get_sidebar_icon_class('influencer/closed_collaborations', 'far fa-check-square'); ?>"></i>
            <span class="ml-3">Closed</span>
        </a>
        
        <a href="<?php echo base_url('ambassadors'); ?>" class="<?php echo get_sidebar_class('ambassadors'); ?>" data-tooltip="Ambassadors">
            <i class="<?php echo get_sidebar_icon_class('ambassadors', 'fas fa-users'); ?>"></i>
            <span class="ml-3">Ambassadors</span>
        </a>
        
        <a href="<?php echo base_url('influencer/analytics'); ?>" class="<?php echo get_sidebar_class('influencer/analytics'); ?>" data-tooltip="Analytics">
            <i class="<?php echo get_sidebar_icon_class('influencer/analytics', 'fas fa-chart-line'); ?>"></i>
            <span class="ml-3">Analytics</span>
        </a>

        <?php if(in_array($user_role, ['admin', 'manager'])): ?>
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mt-8 mb-3">Management</p>
            <a href="<?php echo base_url('manager'); ?>" class="<?php echo get_sidebar_class('manager'); ?>" data-tooltip="Brand Settings">
                <i class="<?php echo get_sidebar_icon_class('manager', 'fas fa-cog'); ?>"></i>
                <span class="ml-3">Brand Settings</span>
            </a>
            <a href="<?php echo base_url('manager/team_management'); ?>" class="<?php echo get_sidebar_class('manager/team_management'); ?>" data-tooltip="Team Management">
                <i class="<?php echo get_sidebar_icon_class('manager/team_management', 'fas fa-user-friends'); ?>"></i>
                <span class="ml-3">Team Management</span>
            </a>
            <a href="<?php echo base_url('manager/activity_logs'); ?>" class="<?php echo get_sidebar_class('manager/activity_logs'); ?>" data-tooltip="Activity Logs">
                <i class="<?php echo get_sidebar_icon_class('manager/activity_logs', 'fas fa-history'); ?>"></i>
                <span class="ml-3">Activity Logs</span>
            </a>
        <?php endif; ?>
    </nav>
</aside>

<!-- Toggle Sidebar and Collapse Scripts -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('absolute');
        sidebar.classList.toggle('z-40');
        sidebar.classList.toggle('h-full');
    }

    let sidebarCollapsed = false;

    function setSidebarCollapsed(collapsed) {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
        if (window.innerWidth < 768) {
            collapsed = false;
        }
        sidebarCollapsed = collapsed;
        localStorage.setItem('sidebarCollapsed', collapsed ? 'true' : 'false');

        // Apply collapsed style/classes
        if (collapsed) {
            sidebar.classList.add('collapsed');
            sidebar.style.width = '5rem';
        } else {
            sidebar.classList.remove('collapsed');
            sidebar.style.width = '18rem';
        }

        // Hide unread badge number if collapsed, or show it
        const unreadBadge = sidebar.querySelector('.badge-unread');
        if (unreadBadge) {
            unreadBadge.classList.toggle('hidden', collapsed);
        }

        // Update collapse button chevron and title
        const btn = document.getElementById('sidebar-collapse-btn');
        if (btn) {
            btn.setAttribute('title', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
            btn.innerHTML = collapsed ? '<i class="fas fa-chevron-right"></i>' : '<i class="fas fa-chevron-left"></i>';
        }
    }

    function toggleSidebarCollapse() {
        setSidebarCollapsed(!sidebarCollapsed);
    }

    // Sync collapse state on load and resize
    document.addEventListener('DOMContentLoaded', function() {
        const saved = localStorage.getItem('sidebarCollapsed');
        if (saved !== null) {
            setSidebarCollapsed(saved === 'true');
        } else {
            setSidebarCollapsed(false);
        }
    });

    // Make sure sidebar is expanded properly if window is resized to desktop from mobile
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.remove('collapsed');
                sidebar.style.width = '';
            }
        } else {
            const saved = localStorage.getItem('sidebarCollapsed');
            setSidebarCollapsed(saved === 'true');
        }
    });
</script>

<?php $this->load->view('partials/toast'); ?>
