<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            black: '#000000',
                            dark: '#111111',
                            green: '#1FAE5B',
                            deep: '#0F6B3E',
                            gray: '#333333',
                            light: '#F4F4F5',
                            border: '#E4E4E7',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 h-screen flex overflow-hidden text-gray-900 font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#0A0A0C] border-r border-white/[0.06] flex-shrink-0 flex flex-col transition-all duration-300 hidden md:flex">
        <!-- Modern Admin Brand Header -->
        <div class="h-20 flex items-center px-6 border-b border-white/[0.08] bg-gradient-to-r from-red-950/30 via-transparent to-transparent">
            <div class="flex items-center gap-3.5">
                <!-- Glowing Logo Icon Badge -->
                <div class="relative group cursor-pointer">
                    <div class="absolute -inset-1.5 bg-gradient-to-r from-red-600 via-rose-500 to-amber-500 rounded-2xl blur-xs opacity-50 group-hover:opacity-80 transition duration-300"></div>
                    <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-br from-[#1C1C20] to-[#0D0D11] border border-white/15 flex items-center justify-center p-2 shadow-2xl shadow-black/80">
                        <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom Logo" class="w-full h-full object-contain filter drop-shadow">
                    </div>
                    <!-- Mini Super Admin Shield Indicator -->
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-gradient-to-tr from-red-600 to-rose-500 border-2 border-[#0A0A0C] flex items-center justify-center text-[7px] text-white shadow-xs" title="Super Admin">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>

                <!-- Modern Typography & Status -->
                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-[0.95rem] font-black tracking-wider text-white font-sans uppercase">INSTROOM</span>
                        <span class="text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-red-500/20 text-rose-300 border border-red-500/30">ADMIN</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-gray-400 tracking-wider">Master Panel</span>
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5">
            <?php $section = isset($active_section) ? $active_section : 'dashboard'; ?>
            
            <a href="<?php echo site_url('admin/dashboard'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo $section === 'dashboard' ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-900'; ?>">
                <i class="fas fa-tachometer-alt w-6 text-lg opacity-100 transition-opacity"></i>
                <span class="ml-3">Dashboard</span>
            </a>
            
            <a href="<?php echo site_url('admin/user_management'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo $section === 'user_management' ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-900'; ?>">
                <i class="fas fa-users-cog w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                <span class="ml-3">User Management</span>
            </a>
            
            <a href="<?php echo site_url('admin/system_settings'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo $section === 'system_settings' ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-900'; ?>">
                <i class="fas fa-cogs w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                <span class="ml-3">System Settings</span>
            </a>

            <a href="<?php echo site_url('admin/audit_logs'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo $section === 'audit_logs' ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-900'; ?>">
                <i class="fas fa-history w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                <span class="ml-3">Audit Logs</span>
            </a>

            <a href="<?php echo site_url('admin/backups'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo $section === 'backups' ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-900'; ?>">
                <i class="fas fa-database w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                <span class="ml-3">Backups</span>
            </a>

            <a href="<?php echo site_url('admin/announcements'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo $section === 'announcements' ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-900'; ?>">
                <i class="fas fa-bullhorn w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                <span class="ml-3">Announcements</span>
            </a>

            <div class="border-t border-gray-800 my-4 mx-4"></div>
            
            <a href="<?php echo site_url('influencer'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-900">
                <i class="fas fa-user-friends w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                <span class="ml-3">Go to Employee Panel</span>
            </a>
            
            <a href="<?php echo site_url('admin/dashboard'); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-900">
                <i class="fas fa-arrow-left w-6 text-lg opacity-70 group-hover:opacity-100 transition-opacity"></i>
                <span class="ml-3">Back to Dashboard</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-20 bg-white border-b border-gray-200/80 flex items-center justify-between px-8 z-10 sticky top-0 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#111] to-[#1C1C20] border border-gray-800 flex items-center justify-center shadow-sm p-2 md:hidden">
                        <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-lg font-black text-gray-900 tracking-tight">Admin Console</h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-50 text-red-700 border border-red-100 shadow-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                Root Level
                            </span>
                        </div>
                        <p class="text-[11px] font-semibold text-gray-400 hidden sm:block">Instroom Enterprise System Management</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="h-8 w-px bg-gray-200 mx-2"></div>
                <div class="relative group">
                    <button class="flex items-center gap-3 focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-black text-gray-900">System Admin</p>
                            <p class="text-[10px] font-extrabold text-red-600 uppercase tracking-wider">Super Administrator</p>
                        </div>
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 border border-red-400/30 flex items-center justify-center text-white font-extrabold shadow-md shadow-red-500/20 text-xs">
                            <i class="fas fa-shield-alt text-sm"></i>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform group-hover:rotate-180"></i>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-2xl py-1.5 border border-gray-100 hidden group-hover:block hover:block z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Admin Account</p>
                            <p class="text-xs font-bold text-gray-800 truncate">admin@armfulmedia.com</p>
                        </div>
                        <a href="<?php echo site_url('admin/system_settings'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                            <i class="fas fa-cogs text-gray-400 w-4"></i> Settings
                        </a>
                        <a href="<?php echo site_url('influencer'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                            <i class="fas fa-arrow-left text-gray-400 w-4"></i> Switch to App
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="<?php echo site_url('auth/logout'); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-4"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8">
            
            <!-- Dashboard Overview -->
            <?php if ($section === 'dashboard'): ?>
            <!-- Hero Header Card -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-gray-950 via-gray-900 to-black text-white p-6 md:p-8 mb-8 shadow-xl border border-gray-800">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-40 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-xs font-semibold text-emerald-400 mb-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            System Status: All Services Operational
                        </div>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Welcome back, Admin 👋</h1>
                        <p class="text-sm text-gray-400 mt-1 max-w-xl">Instroom Operational Control Center. Track user growth, active influencer campaigns, and real-time audit logs.</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <button onclick="document.getElementById('addUserModal')?.classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs transition-all shadow-lg shadow-red-600/30 flex items-center gap-2">
                            <i class="fas fa-user-plus"></i> Add User
                        </button>
                        <a href="<?php echo site_url('admin/audit_logs'); ?>" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-white font-extrabold text-xs backdrop-blur-md transition-all flex items-center gap-2">
                            <i class="fas fa-history"></i> Audit Trail
                        </a>
                        <a href="<?php echo site_url('admin/system_settings'); ?>" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-white font-extrabold text-xs backdrop-blur-md transition-all flex items-center gap-2">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Metric Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total Registered Users Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Total Users</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-users-cog text-base"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-gray-900"><?php echo $total_users; ?></h3>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Active: <?php echo $active_users; ?></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 font-medium">
                        <span class="font-bold text-gray-700"><?php echo $employee_count; ?></span> Employees, 
                        <span class="font-bold text-gray-700"><?php echo $manager_count; ?></span> Manager, 
                        <span class="font-bold text-gray-700"><?php echo $admin_count; ?></span> Admin
                    </p>
                </div>

                <!-- Total Sourced Influencers Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Influencers</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-plus text-base"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-gray-900"><?php echo $total_influencers; ?></h3>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">In Pipeline</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Across active campaigns & outreach</p>
                </div>

                <!-- Total Active Campaigns Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Campaigns</span>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-handshake text-base"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-gray-900"><?php echo $total_campaigns; ?></h3>
                        <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-md">Active</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Brand outreach & drip campaigns</p>
                </div>

                <!-- Total Drip Automations Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Email Drips</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-paper-plane text-base"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-gray-900"><?php echo $total_drips; ?></h3>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">Sequences</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Automated email drip steps</p>
                </div>
            </div>

            <!-- Analytics & System Monitor Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Activity & Growth Chart (2 cols) -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">System Activity & User Overview</h3>
                            <p class="text-xs text-gray-400 font-medium">Monthly user engagement and system activity trend</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-lg">
                                <span class="w-2 h-2 rounded-full bg-red-600"></span> Users
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-lg">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Influencers
                            </span>
                        </div>
                    </div>
                    <div class="relative flex-1 min-h-[240px]">
                        <canvas id="adminOverviewChart"></canvas>
                    </div>
                </div>

                <!-- Server Health & Quick Diagnostics (1 col) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 text-base">Server Health</h3>
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> 100% Operational
                            </span>
                        </div>
                        
                        <div class="space-y-4 my-4">
                            <!-- Database Status -->
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">MySQL Database</p>
                                        <p class="text-[10px] text-gray-400 font-medium">Hostinger / XAMPP Local</p>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Connected</span>
                            </div>

                            <!-- PHP Engine -->
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">
                                        <i class="fab fa-php"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">PHP CodeIgniter 3</p>
                                        <p class="text-[10px] text-gray-400 font-medium">Version PHP <?php echo phpversion(); ?></p>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Active</span>
                            </div>

                            <!-- Session Security -->
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">Session Security</p>
                                        <p class="text-[10px] text-gray-400 font-medium">Bcrypt Hashing Active</p>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Secure</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tools -->
                    <div class="pt-4 border-t border-gray-100 flex gap-2">
                        <a href="<?php echo site_url('admin/user_management'); ?>" class="flex-1 text-center py-2.5 bg-black hover:bg-gray-800 text-white font-extrabold text-xs rounded-xl transition-colors">
                            Manage Users
                        </a>
                        <a href="<?php echo site_url('admin/audit_logs'); ?>" class="flex-1 text-center py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-extrabold text-xs rounded-xl transition-colors">
                            Audit Logs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Audit Log Feed & Role Distribution -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent System Activity (2 cols) -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Recent Audit Stream</h3>
                            <p class="text-xs text-gray-400 font-medium">Live record of user and administrative operations</p>
                        </div>
                        <a href="<?php echo site_url('admin/audit_logs'); ?>" class="text-xs font-extrabold text-red-600 hover:text-red-700 transition">
                            View All Logs <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <?php if(!empty($recent_logs)): ?>
                            <?php foreach($recent_logs as $log): ?>
                            <div class="py-3.5 flex items-center justify-between gap-4 hover:bg-gray-50/50 px-2 rounded-xl transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-gray-100 text-gray-700 flex-shrink-0 flex items-center justify-center font-bold text-xs border border-gray-200">
                                        <?php echo strtoupper(substr($log['user'], 0, 1)); ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate"><?php echo htmlspecialchars($log['action']); ?></p>
                                        <p class="text-[11px] text-gray-500 truncate"><?php echo htmlspecialchars($log['details']); ?> <span class="text-gray-400">by <?php echo htmlspecialchars($log['user']); ?></span></p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-medium text-gray-400 whitespace-nowrap"><?php echo $log['time']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="py-8 text-center text-gray-400 text-xs">No recent activity logs.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Role Distribution & System Info (1 col) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base mb-4">User Roles Breakdown</h3>
                        
                        <div class="space-y-4">
                            <!-- Employee ratio -->
                            <div>
                                <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                    <span>Employees</span>
                                    <span><?php echo $employee_count; ?> accounts</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: <?php echo $total_users > 0 ? round(($employee_count / $total_users) * 100) : 0; ?>%"></div>
                                </div>
                            </div>

                            <!-- Manager ratio -->
                            <div>
                                <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                    <span>Managers</span>
                                    <span><?php echo $manager_count; ?> accounts</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full bg-purple-600 rounded-full" style="width: <?php echo $total_users > 0 ? round(($manager_count / $total_users) * 100) : 0; ?>%"></div>
                                </div>
                            </div>

                            <!-- Admin ratio -->
                            <div>
                                <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                    <span>System Admins</span>
                                    <span><?php echo $admin_count; ?> accounts</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full bg-red-600 rounded-full" style="width: <?php echo $total_users > 0 ? round(($admin_count / $total_users) * 100) : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Instroom Engine v2.0</p>
                        <p class="text-xs text-gray-500 mt-1">Multi-Brand Influencer Management System</p>
                    </div>
                </div>
            </div>

            <!-- Chart Initialization Script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('adminOverviewChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                            datasets: [
                                {
                                    label: 'Users Registered',
                                    data: [2, 3, 5, 6, 7, 8, 9, <?php echo $total_users; ?>],
                                    borderColor: '#dc2626',
                                    backgroundColor: 'rgba(220, 38, 38, 0.05)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3
                                },
                                {
                                    label: 'Influencer Records',
                                    data: [0, 1, 2, 4, 5, 6, 7, <?php echo $total_influencers; ?>],
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }
                            }
                        }
                    });
                });
            </script>
            <?php endif; ?>

            <!-- Backups -->
            <?php if ($section === 'backups'): ?>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Database Backups</h1>
                <p class="text-gray-500">Manage and restore system data.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <h3 class="font-bold text-gray-900 mb-4">Create New Backup</h3>
                <div class="flex items-center gap-4">
                    <button class="bg-black text-white px-6 py-3 rounded-lg text-sm font-bold hover:bg-gray-800 transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Generate Backup Now
                    </button>
                    <p class="text-sm text-gray-500">Last backup was created 12 hours ago.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-900">Available Backups</h3>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-gray-600 font-medium border-b border-gray-200">
                        <tr>
                            <th class="p-4">Filename</th>
                            <th class="p-4">Size</th>
                            <th class="p-4">Created Date</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($backups as $backup): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-mono text-gray-900 font-medium">
                                <i class="fas fa-file-archive text-yellow-500 mr-2"></i> <?php echo $backup->filename; ?>
                            </td>
                            <td class="p-4 text-gray-500"><?php echo $backup->size; ?></td>
                            <td class="p-4 text-gray-500"><?php echo $backup->created_at; ?></td>
                            <td class="p-4 text-right">
                                <button class="text-blue-600 hover:text-blue-800 mr-3 font-bold text-xs border border-blue-200 px-2 py-1 rounded bg-blue-50">Download</button>
                                <button class="text-red-600 hover:text-red-800 font-bold text-xs border border-red-200 px-2 py-1 rounded bg-red-50">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Announcements -->
            <?php if ($section === 'announcements'): ?>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Global Announcements</h1>
                <p class="text-gray-500">Broadcast messages to system users.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                        <h3 class="font-bold text-gray-900 mb-4">Post New Announcement</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-black focus:border-black outline-none" placeholder="e.g. System Maintenance">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea rows="4" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-black focus:border-black outline-none" placeholder="Enter your message here..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                                <select class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-black focus:border-black outline-none">
                                    <option>All Users</option>
                                    <option>Managers Only</option>
                                    <option>Employees Only</option>
                                </select>
                            </div>
                            <button class="w-full bg-black text-white py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition">Publish Announcement</button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-bold text-gray-900">Active Announcements</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <?php foreach($announcements as $announcement): ?>
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-lg text-gray-900"><?php echo $announcement->title; ?></h4>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold uppercase"><?php echo $announcement->status; ?></span>
                                </div>
                                <p class="text-gray-600 text-sm mb-4"><?php echo $announcement->message; ?></p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center gap-4">
                                        <span><i class="fas fa-users mr-1"></i> <?php echo $announcement->audience; ?></span>
                                        <span><i class="far fa-clock mr-1"></i> <?php echo $announcement->created_at; ?></span>
                                    </div>
                                    <button class="text-red-600 hover:text-red-800 font-bold hover:underline">Delete</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- User Management -->
            <?php if ($section === 'user_management'): ?>
            <?php
                $total_users_count = is_array($users) ? count($users) : 0;
                $active_users_count = 0;
                $inactive_users_count = 0;
                $manager_users_count = 0;
                foreach ($users as $u) {
                    $status = isset($u->status) ? (string)$u->status : 'inactive';
                    if ($status === 'active') $active_users_count++;
                    else $inactive_users_count++;
                    if (isset($u->role) && (string)$u->role === 'manager') $manager_users_count++;
                }
            ?>

            <div class="relative">
                <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-red-50/60 to-transparent -z-10"></div>

                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">User Management</h1>
                        <p class="text-gray-500 mt-1 font-medium">Manage employee and manager accounts.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <div class="relative flex-1 lg:flex-none">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="userSearch" type="text" placeholder="Search name, email, role..." class="w-full lg:w-80 pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 placeholder:text-gray-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                        </div>
                        <button type="button" onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="bg-black text-white px-5 py-2.5 rounded-xl text-sm font-extrabold hover:bg-gray-900 transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i> Add New User
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Users</p>
                                <p class="text-3xl font-black text-gray-900 mt-2"><?php echo $total_users_count; ?></p>
                            </div>
                            <div class="w-11 h-11 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active</p>
                                <p class="text-3xl font-black text-gray-900 mt-2"><?php echo $active_users_count; ?></p>
                            </div>
                            <div class="w-11 h-11 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inactive</p>
                                <p class="text-3xl font-black text-gray-900 mt-2"><?php echo $inactive_users_count; ?></p>
                            </div>
                            <div class="w-11 h-11 rounded-2xl bg-gray-50 text-gray-600 flex items-center justify-center">
                                <i class="fas fa-pause-circle text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>



            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-white to-gray-50">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">User Accounts</h3>
                            <p class="text-xs text-gray-400 font-black uppercase tracking-widest mt-1">Manage access, roles, and status</p>
                        </div>
                        <div class="flex items-center gap-2 bg-red-50 px-3 py-1.5 rounded-xl border border-red-100">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="text-[10px] font-black text-red-700 uppercase tracking-wider">Admin Panel</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white text-gray-500 border-b border-gray-200">
                            <tr class="text-[10px] uppercase tracking-widest font-black">
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Last Login</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="divide-y divide-gray-100">
                            <?php foreach($users as $user): ?>
                            <?php
                                $role = isset($user->role) ? (string)$user->role : 'employee';
                                $status = isset($user->status) ? (string)$user->status : 'inactive';
                                $initial = strtoupper(substr((string)$user->name, 0, 1));
                                $role_pill = 'bg-gray-100 text-gray-700 border border-gray-200';
                                if ($role === 'admin') $role_pill = 'bg-red-50 text-red-700 border border-red-100';
                                elseif ($role === 'manager') $role_pill = 'bg-blue-50 text-blue-700 border border-blue-100';
                                $status_pill = ($status === 'active') ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-600 border border-gray-200';
                                $status_dot = ($status === 'active') ? 'bg-green-500 animate-pulse' : 'bg-gray-400';
                                $last_login = isset($user->last_login) && $user->last_login ? $user->last_login : 'Never';
                                $is_self = ((int)$user->id === (int)$this->session->userdata('user_id'));
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-colors" data-name="<?php echo html_escape(strtolower((string)$user->name)); ?>" data-email="<?php echo html_escape(strtolower((string)$user->email)); ?>" data-role="<?php echo html_escape(strtolower($role)); ?>">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-2xl bg-gray-100 border border-gray-200 flex items-center justify-center font-black text-gray-700">
                                            <?php echo $initial; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-gray-900 truncate"><?php echo $user->name; ?></div>
                                            <div class="text-xs text-gray-500 font-semibold truncate"><?php echo $user->email; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest <?php echo $role_pill; ?>">
                                        <?php echo $role; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest <?php echo $status_pill; ?>">
                                        <span class="w-2 h-2 rounded-full <?php echo $status_dot; ?>"></span>
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-gray-600 font-semibold">
                                    <?php echo $last_login; ?>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-200 text-gray-600 hover:text-blue-700 hover:border-blue-200 hover:bg-blue-50 transition-all" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <?php if(!$is_self): ?>
                                            <a href="<?php echo site_url('admin/toggle_status/'.$user->id.'/' . $status); ?>" class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-200 text-gray-600 hover:text-black hover:border-gray-300 hover:bg-white transition-all flex items-center justify-center" title="<?php echo ($status === 'active') ? 'Deactivate' : 'Activate'; ?>">
                                                <i class="<?php echo ($status === 'active') ? 'fas fa-user-slash' : 'fas fa-user-check'; ?>"></i>
                                            </a>
                                            <a href="<?php echo site_url('admin/delete_user/'.$user->id); ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="w-9 h-9 rounded-xl bg-red-50 border border-red-100 text-red-700 hover:bg-red-100 transition-all flex items-center justify-center" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs font-bold text-gray-400 px-3">You</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add User Modal -->
            <div id="addUserModal" class="fixed inset-0 hidden z-50">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="document.getElementById('addUserModal').classList.add('hidden')"></div>
                <div class="relative h-full w-full flex items-end sm:items-center justify-center p-4">
                    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-red-50 text-red-700 flex items-center justify-center">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-gray-900">Add New User</h3>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Create an account</p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 text-gray-500 hover:bg-gray-100 transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form action="<?php echo site_url('admin/add_user'); ?>" method="POST" class="p-6 space-y-4">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Name</label>
                                <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" name="email" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Password</label>
                                <input type="password" name="password" id="addPassword" required class="w-full bg-gray-50 border border-gray-200 rounded-xl pr-10 px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                                <button type="button" id="toggleAddPassword" class="absolute inset-y-0 right-3 top-6 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="far fa-eye-slash" id="toggleAddPasswordIcon"></i>
                                </button>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Role</label>
                                <select name="role" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                                    <option value="employee">Employee</option>
                                    <option value="manager">Manager</option>
                                </select>
                            </div>
                            <div class="pt-2 flex items-center justify-end gap-2">
                                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-extrabold bg-black text-white hover:bg-gray-900 transition-colors shadow-sm">
                                    Add User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div id="editUserModal" class="fixed inset-0 hidden z-50">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="document.getElementById('editUserModal').classList.add('hidden')"></div>
                <div class="relative h-full w-full flex items-end sm:items-center justify-center p-4">
                    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gray-50 text-gray-800 flex items-center justify-center border border-gray-200">
                                    <i class="fas fa-user-pen"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-gray-900">Edit User</h3>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Update details</p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 text-gray-500 hover:bg-gray-100 transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form action="<?php echo site_url('admin/edit_user'); ?>" method="POST" class="p-6 space-y-4">
                            <input type="hidden" id="edit_id" name="id">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Name</label>
                                <input type="text" id="edit_name" name="name" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" id="edit_email" name="email" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">New Password</label>
                                <input type="password" name="password" id="editPassword" class="w-full bg-gray-50 border border-gray-200 rounded-xl pr-10 px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all" placeholder="Leave blank to keep current">
                                <button type="button" id="toggleEditPassword" class="absolute inset-y-0 right-3 top-6 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="far fa-eye-slash" id="toggleEditPasswordIcon"></i>
                                </button>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Role</label>
                                <select id="edit_role" name="role" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                                    <option value="employee">Employee</option>
                                    <option value="manager">Manager</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Status</label>
                                <select id="edit_status" name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="pt-2 flex items-center justify-end gap-2">
                                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-extrabold bg-black text-white hover:bg-gray-900 transition-colors shadow-sm">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                // Auto-hide flash messages after 5 seconds
                document.addEventListener('DOMContentLoaded', function() {
                    const flashMessages = document.querySelectorAll('[role="alert"]');
                    flashMessages.forEach(alert => {
                        setTimeout(() => {
                            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            alert.style.opacity = '0';
                            alert.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                alert.remove();
                            }, 500);
                        }, 5000);
                    });
                });

                function setupPasswordToggle(inputId, toggleId, iconId) {
                    const passwordInput = document.getElementById(inputId);
                    const toggleButton = document.getElementById(toggleId);
                    const toggleIcon = document.getElementById(iconId);

                    toggleButton.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        if (type === 'text') {
                            toggleIcon.classList.remove('fa-eye-slash');
                            toggleIcon.classList.add('fa-eye');
                        } else {
                            toggleIcon.classList.remove('fa-eye');
                            toggleIcon.classList.add('fa-eye-slash');
                        }
                    });
                }

                // Initialize password toggles for admin forms
                setupPasswordToggle('addPassword', 'toggleAddPassword', 'toggleAddPasswordIcon');
                setupPasswordToggle('editPassword', 'toggleEditPassword', 'toggleEditPasswordIcon');

                function openEditModal(user) {
                    document.getElementById('edit_id').value = user.id;
                    document.getElementById('edit_name').value = user.name;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_status').value = user.status || 'active'; // Default to active if undefined
                    document.getElementById('editUserModal').classList.remove('hidden');
                }

                (function () {
                    const search = document.getElementById('userSearch');
                    const body = document.getElementById('usersTableBody');
                    if (!search || !body) return;

                    const rows = Array.from(body.querySelectorAll('tr'));
                    const filter = () => {
                        const q = (search.value || '').trim().toLowerCase();
                        rows.forEach(row => {
                            const name = (row.getAttribute('data-name') || '');
                            const email = (row.getAttribute('data-email') || '');
                            const role = (row.getAttribute('data-role') || '');
                            const show = q === '' || name.includes(q) || email.includes(q) || role.includes(q);
                            row.style.display = show ? '' : 'none';
                        });
                    };

                    search.addEventListener('input', filter);
                })();
            </script>
            <?php endif; ?>

            <!-- System Settings -->
            <?php if ($section === 'system_settings'): ?>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
                <p class="text-gray-500">Configure global application preferences.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">General Configuration</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" value="<?php echo $settings['site_name']; ?>" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-black focus:border-black outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Currency</label>
                            <select class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-black focus:border-black outline-none">
                                <option value="USD" selected>USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Maintenance Mode</p>
                                <p class="text-xs text-gray-500">Disable access for non-admins</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Allow User Registration</p>
                                <p class="text-xs text-gray-500">Enable new user signups</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo $settings['allow_registration'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                            </label>
                        </div>
                    </div>
                    <div class="mt-6 text-right">
                        <button class="bg-black text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition">Save Changes</button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Security & Access</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                            <div class="flex gap-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                                <div>
                                    <h4 class="text-sm font-bold text-yellow-800">Force Password Reset</h4>
                                    <p class="text-xs text-yellow-700 mt-1">Require all users to reset their password on next login.</p>
                                    <button class="mt-2 text-xs font-bold text-yellow-800 border border-yellow-300 px-2 py-1 rounded bg-white hover:bg-yellow-50">Execute Action</button>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-red-50 border border-red-100 rounded-lg">
                            <div class="flex gap-3">
                                <i class="fas fa-trash-alt text-red-600 mt-0.5"></i>
                                <div>
                                    <h4 class="text-sm font-bold text-red-800">Clear System Cache</h4>
                                    <p class="text-xs text-red-700 mt-1">Delete all temporary files and session data.</p>
                                    <button class="mt-2 text-xs font-bold text-red-800 border border-red-300 px-2 py-1 rounded bg-white hover:bg-red-50">Clear Cache</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Audit Logs -->
            <?php if ($section === 'audit_logs'): ?>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
                <p class="text-gray-500">Track all system activities and user actions.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex gap-4">
                    <input type="text" placeholder="Search logs..." class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-64 focus:outline-none focus:border-black">
                    <select class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-black">
                        <option>All Actions</option>
                        <option>Add User</option>
                        <option>Update User</option>
                        <option>Delete User</option>
                        <option>Login</option>
                    </select>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-gray-600 font-medium border-b border-gray-200">
                        <tr>
                            <th class="p-4">Timestamp</th>
                            <th class="p-4">User</th>
                            <th class="p-4">Action</th>
                            <th class="p-4">Details</th>
                            <th class="p-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(isset($logs) && !empty($logs)): ?>
                            <?php foreach($logs as $log): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 font-mono text-xs text-gray-500"><?php echo $log->created_at; ?></td>
                                <td class="p-4 font-medium text-gray-900"><?php echo $log->user_name; ?></td>
                                <td class="p-4">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs border border-gray-200 font-medium"><?php echo $log->action; ?></span>
                                </td>
                                <td class="p-4 text-gray-500 text-xs"><?php echo $log->details; ?></td>
                                <td class="p-4 text-gray-500 text-xs font-mono"><?php echo $log->ip_address; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-500">No activity logs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </main>
    </div>
    <?php $this->load->view('partials/toast'); ?>
</body>
</html>
