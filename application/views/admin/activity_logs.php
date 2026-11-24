<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/admin.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#111827] flex-shrink-0 flex flex-col transition-all duration-300 hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-brand-green rounded-lg flex items-center justify-center text-white font-bold">A</div>
                <span class="text-xl font-bold tracking-tight text-white">Admin Panel</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a href="<?php echo base_url('admin'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-tachometer-alt w-6"></i>
                <span class="ml-2">Dashboard</span>
            </a>

            <!-- User Management -->
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mt-6 mb-2">User Management</p>
            <a href="<?php echo base_url('admin/users'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-users-cog w-6"></i>
                <span class="ml-2">User Accounts</span>
            </a>

            <!-- System Configuration -->
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mt-6 mb-2">Configuration</p>
            <a href="<?php echo base_url('admin/settings'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-sliders-h w-6"></i>
                <span class="ml-2">System Config</span>
            </a>

            <!-- Master Data -->
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mt-6 mb-2">Master Data</p>
            <a href="<?php echo base_url('influencer'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-database w-6"></i>
                <span class="ml-2">Influencer DB</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors opacity-50 cursor-not-allowed">
                <i class="fas fa-tags w-6"></i>
                <span class="ml-2">Campaign Categories</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors opacity-50 cursor-not-allowed">
                <i class="fas fa-layer-group w-6"></i>
                <span class="ml-2">Platform Settings</span>
            </a>

            <!-- Monitoring -->
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider mt-6 mb-2">Monitoring</p>
            <a href="<?php echo base_url('admin/activity_logs'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg bg-brand-green text-white">
                <i class="fas fa-clipboard-list w-6"></i>
                <span class="ml-2">Audit Logs</span>
            </a>

            <div class="border-t border-white/10 my-4"></div>
            
            <a href="<?php echo base_url('influencer'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-arrow-left w-6"></i>
                <span class="ml-2">Back to App</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10">
            <h2 class="text-lg font-semibold text-gray-800">System Activity Logs</h2>
            
            <div class="flex items-center gap-4">
                <div class="h-8 w-px bg-gray-200 mx-2"></div>
                <!-- User Dropdown -->
                <div class="relative" id="user-menu-container">
                    <button onclick="toggleUserMenu()" class="flex items-center gap-3 cursor-pointer group focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-brand-green transition-colors"><?php echo isset($user_name) ? $user_name : 'Admin'; ?></p>
                            <p class="text-xs text-gray-500"><?php echo isset($user_role) ? ucfirst($user_role) : 'Administrator'; ?></p>
                        </div>
                        <div class="relative">
                             <?php $nav_fallback = "https://ui-avatars.com/api/?name=" . urlencode(isset($user_name) ? $user_name : 'User') . "&background=111827&color=fff&rounded=true&bold=true"; ?>
                             <?php $nav_avatar = !empty($user_profile_image) ? base_url($user_profile_image) : $nav_fallback; ?>
                             <img src="<?php echo html_escape($nav_avatar); ?>" alt="User Avatar" onerror="this.onerror=null;this.src='<?php echo $nav_fallback; ?>';" class="w-9 h-9 rounded-full shadow-sm border-2 border-gray-200 group-hover:border-brand-green transition-all object-cover">
                             <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs ml-1 group-hover:text-brand-green transition-colors"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="user-menu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-50 transform origin-top-right transition-all">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Account</p>
                        </div>
                        <a href="<?php echo base_url('influencer'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-green transition-colors">
                            <i class="fas fa-tachometer-alt w-5"></i> User Dashboard
                        </a>
                        <a href="<?php echo base_url('profile'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-green transition-colors">
                            <i class="far fa-user w-5"></i> Profile
                        </a>
                        <a href="<?php echo base_url('settings'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-green transition-colors">
                            <i class="fas fa-cog w-5"></i> Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="<?php echo base_url('auth/logout'); ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-5"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
            
            <!-- Logs Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-800">Recent Activities</h3>
                    <div class="text-xs text-gray-500">Showing latest 100 records</div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Date & Time</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">User</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">Action</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Details</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(isset($logs) && !empty($logs)): ?>
                                <?php foreach($logs as $log): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 text-sm text-gray-600 whitespace-nowrap">
                                        <?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-800 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                <?php echo substr($log['user_name'], 0, 1); ?>
                                            </div>
                                            <?php echo $log['user_name']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium 
                                            <?php 
                                            if (stripos($log['action'], 'delete') !== false) echo 'bg-red-100 text-red-700';
                                            elseif (stripos($log['action'], 'login') !== false) echo 'bg-green-100 text-green-700';
                                            elseif (stripos($log['action'], 'update') !== false) echo 'bg-blue-100 text-blue-700';
                                            else echo 'bg-gray-100 text-gray-700';
                                            ?>">
                                            <?php echo $log['action']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-600 truncate max-w-md" title="<?php echo htmlspecialchars($log['details']); ?>">
                                        <?php echo $log['details']; ?>
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-400 font-mono">
                                        <?php echo $log['ip_address']; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                                            <p>No activity logs found.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Simple Footer -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end">
                    <!-- Pagination could go here -->
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        // Close on click outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('user-menu-container');
            const menu = document.getElementById('user-menu');
            if (container && menu && !container.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
