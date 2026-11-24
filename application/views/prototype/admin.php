<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Prototype - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F9FAFB] min-h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-screen">
        <div class="p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center text-white font-bold text-xl">A</div>
                <span class="font-bold text-xl tracking-tight">Admin</span>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <a href="?section=dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo $section === 'dashboard' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-grid-2 text-lg"></i>
                Dashboard
            </a>
            <div class="pt-4 pb-2 px-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">System Management</span>
            </div>
            <a href="?section=user_management" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo $section === 'user_management' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-users-gear text-lg"></i>
                User Management
            </a>
            <a href="?section=system_settings" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo $section === 'system_settings' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-sliders text-lg"></i>
                System Settings
            </a>
            <a href="?section=audit_logs" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo $section === 'audit_logs' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-list-check text-lg"></i>
                Audit Logs
            </a>
            <a href="?section=backups" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo $section === 'backups' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-database text-lg"></i>
                Backups
            </a>
            <a href="?section=announcement" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo $section === 'announcement' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-bullhorn text-lg"></i>
                Announcement
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <a href="<?php echo base_url('auth/logout'); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-all">
                <i class="fas fa-arrow-right-from-bracket text-lg"></i>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
            <h2 class="font-semibold text-gray-800 uppercase tracking-wide text-sm">
                <?php echo str_replace('_', ' ', $section); ?>
            </h2>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="p-2 text-gray-400 hover:text-gray-600"><i class="far fa-bell text-xl"></i></button>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </div>
                <div class="h-8 w-px bg-gray-200"></div>
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-900">Admin Prototype</p>
                        <p class="text-[10px] text-gray-500">Super Admin</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin+Proto&background=000&color=fff" class="w-9 h-9 rounded-full border border-gray-200 group-hover:border-blue-500 transition-all">
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8">
            
            <?php if ($section === 'dashboard'): ?>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Users</p>
                        <h3 class="text-3xl font-bold">1,284</h3>
                        <p class="text-xs text-green-500 mt-2 font-medium"><i class="fas fa-arrow-up mr-1"></i> 12% from last month</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">System Health</p>
                        <h3 class="text-3xl font-bold text-blue-600">99.9%</h3>
                        <p class="text-xs text-gray-500 mt-2 font-medium">Optimal Performance</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Active Logs</p>
                        <h3 class="text-3xl font-bold text-orange-500">4,290</h3>
                        <p class="text-xs text-gray-500 mt-2 font-medium">Recorded today</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Backups</p>
                        <h3 class="text-3xl font-bold text-green-600">Secured</h3>
                        <p class="text-xs text-gray-500 mt-2 font-medium">Last: 2 hours ago</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-history text-blue-500"></i> Recent Activities
                        </h4>
                        <div class="space-y-6">
                            <?php for($i=1; $i<=5; $i++): ?>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400"><i class="fas fa-user"></i></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">User Added</p>
                                    <p class="text-xs text-gray-500">Admin added new manager "John Doe"</p>
                                    <p class="text-[10px] text-gray-400 mt-1">10 minutes ago</p>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-bullhorn text-orange-500"></i> System Announcements
                        </h4>
                        <div class="space-y-4">
                            <div class="p-4 bg-orange-50 border border-orange-100 rounded-xl">
                                <h5 class="text-sm font-bold text-orange-800">System Maintenance</h5>
                                <p class="text-xs text-orange-700 mt-1">Planned maintenance on April 15, 2026, from 02:00 AM to 04:00 AM UTC.</p>
                            </div>
                            <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                <h5 class="text-sm font-bold text-blue-800">New Feature Update</h5>
                                <p class="text-xs text-blue-700 mt-1">V2.4 Release: Advanced influencer analytics dashboard is now live.</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($section === 'user_management'): ?>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">User Management</h3>
                        <p class="text-sm text-gray-500">Manage all system users and their permissions.</p>
                    </div>
                    <button class="bg-black text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-800 transition flex items-center gap-2 shadow-lg shadow-black/10">
                        <i class="fas fa-plus"></i> Add New User
                    </button>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">User Info</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php 
                            $users = [
                                ['name' => 'John Manager', 'email' => 'john@example.com', 'role' => 'Manager', 'status' => 'Active'],
                                ['name' => 'Alice Admin', 'email' => 'alice@example.com', 'role' => 'Admin', 'status' => 'Active'],
                                ['name' => 'Bob Employee', 'email' => 'bob@example.com', 'role' => 'Employee', 'status' => 'Inactive'],
                            ];
                            foreach($users as $user): 
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold"><?php echo substr($user['name'], 0, 1); ?></div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900"><?php echo $user['name']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $user['email']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $user['role'] === 'Admin' ? 'bg-purple-100 text-purple-700' : ($user['role'] === 'Manager' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'); ?>">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full <?php echo $user['status'] === 'Active' ? 'bg-green-500' : 'bg-red-500'; ?>"></div>
                                        <span class="text-xs font-medium text-gray-600"><?php echo $user['status']; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"><i class="far fa-edit"></i></button>
                                        <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"><i class="far fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($section === 'system_settings'): ?>
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">System Settings</h3>
                    <p class="text-sm text-gray-500">Configure core system parameters and integrations.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                        <h4 class="font-bold text-gray-800 mb-6">General Configuration</h4>
                        <div class="space-y-6">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">System Name</label>
                                <input type="text" value="Instroom V2" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Admin Email</label>
                                <input type="email" value="admin@instroom.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all text-sm">
                            </div>
                            <button class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">Save Configuration</button>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                        <h4 class="font-bold text-gray-800 mb-6">API & Integrations</h4>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <i class="fab fa-shopify text-2xl text-green-600"></i>
                                    <div>
                                        <p class="text-sm font-bold">Shopify Integration</p>
                                        <p class="text-[10px] text-gray-500">Sync orders and inventory</p>
                                    </div>
                                </div>
                                <div class="w-12 h-6 bg-green-500 rounded-full p-1 cursor-pointer">
                                    <div class="w-4 h-4 bg-white rounded-full ml-auto"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <i class="fab fa-instagram text-2xl text-pink-600"></i>
                                    <div>
                                        <p class="text-sm font-bold">Instagram API</p>
                                        <p class="text-[10px] text-gray-500">Fetch influencer stats</p>
                                    </div>
                                </div>
                                <div class="w-12 h-6 bg-gray-300 rounded-full p-1 cursor-pointer">
                                    <div class="w-4 h-4 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($section === 'audit_logs'): ?>
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Audit Logs</h3>
                    <p class="text-sm text-gray-500">Track all administrative activities and system changes.</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div class="flex items-center gap-4">
                            <input type="text" placeholder="Search logs..." class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <select class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>All Categories</option>
                                <option>User Auth</option>
                                <option>Data Change</option>
                            </select>
                        </div>
                    </div>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Timestamp</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Admin</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Action</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            <?php for($i=1; $i<=10; $i++): ?>
                            <tr>
                                <td class="px-6 py-4 text-gray-500 font-mono text-xs">2026-04-10 14:23:0<?php echo $i; ?></td>
                                <td class="px-6 py-4 font-bold">Admin Prototype</td>
                                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-bold uppercase">Update</span></td>
                                <td class="px-6 py-4 text-gray-600">Updated system configuration for Instagram API</td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($section === 'backups'): ?>
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">System Backups</h3>
                    <p class="text-sm text-gray-500">Secure your data with manual and automated backups.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 mx-auto mb-6 text-3xl">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 text-lg mb-2">Run Manual Backup</h4>
                            <p class="text-sm text-gray-500 mb-8">Create a snapshot of your database and system files immediately.</p>
                            <button class="w-full bg-blue-600 text-white px-6 py-3.5 rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                                <i class="fas fa-play mr-2"></i> Start Backup
                            </button>
                        </div>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h4 class="font-bold text-gray-800">Available Backups</h4>
                            </div>
                            <div class="divide-y divide-gray-50">
                                <?php for($i=1; $i<=4; $i++): ?>
                                <div class="p-6 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                                            <i class="fas fa-file-zipper"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">backup_2026_04_0<?php echo $i; ?>.sql.gz</p>
                                            <p class="text-xs text-gray-500">124.5 MB • Created 2026-04-0<?php echo $i; ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"><i class="fas fa-download"></i></button>
                                        <button class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all"><i class="fas fa-rotate-left"></i></button>
                                        <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($section === 'announcement'): ?>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">System Announcements</h3>
                        <p class="text-sm text-gray-500">Broadcast messages to all system users.</p>
                    </div>
                    <button class="bg-orange-500 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition flex items-center gap-2 shadow-lg shadow-orange-500/10">
                        <i class="fas fa-plus"></i> Post Announcement
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <?php 
                    $announcements = [
                        ['title' => 'Server Upgrade Complete', 'date' => '2026-04-09', 'author' => 'System', 'priority' => 'Low'],
                        ['title' => 'Important: Password Policy Change', 'date' => '2026-04-05', 'author' => 'Admin', 'priority' => 'High'],
                        ['title' => 'New Influencer Search Filters', 'date' => '2026-04-01', 'author' => 'Product Team', 'priority' => 'Medium'],
                    ];
                    foreach($announcements as $a): 
                    ?>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between">
                        <div class="flex gap-6">
                            <div class="w-12 h-12 rounded-2xl <?php echo $a['priority'] === 'High' ? 'bg-red-50 text-red-500' : ($a['priority'] === 'Medium' ? 'bg-orange-50 text-orange-500' : 'bg-blue-50 text-blue-500'); ?> flex items-center justify-center text-xl">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-bold text-gray-900 mb-1"><?php echo $a['title']; ?></h5>
                                <p class="text-sm text-gray-500">Posted by <span class="font-semibold text-gray-700"><?php echo $a['author']; ?></span> on <?php echo $a['date']; ?></p>
                                <div class="mt-4 flex items-center gap-4">
                                    <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded <?php echo $a['priority'] === 'High' ? 'bg-red-100 text-red-700' : ($a['priority'] === 'Medium' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'); ?>">
                                        <?php echo $a['priority']; ?> Priority
                                    </span>
                                    <a href="#" class="text-blue-600 text-xs font-bold hover:underline">Read Full Announcement</a>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-2 text-gray-400 hover:text-blue-600 rounded-lg transition-all"><i class="far fa-edit"></i></button>
                            <button class="p-2 text-gray-400 hover:text-red-600 rounded-lg transition-all"><i class="far fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>