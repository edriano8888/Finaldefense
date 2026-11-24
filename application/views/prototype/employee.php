<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Prototype - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col h-screen">
        <div class="p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#1FAE5B] rounded-xl flex items-center justify-center text-white shadow-lg shadow-green-500/20">
                    <i class="fas fa-user-tie text-lg"></i>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-800">Employee</span>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
            <a href="?section=dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?php echo $section === 'dashboard' ? 'bg-green-50 text-[#1FAE5B]' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <i class="fas fa-home-alt text-lg"></i>
                Dashboard
            </a>
            
            <div class="pt-6 pb-2 px-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sourcing</span>
            </div>
            <a href="?section=discovery" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?php echo $section === 'discovery' ? 'bg-green-50 text-[#1FAE5B]' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <i class="fas fa-search text-lg"></i>
                Discovery
            </a>
            <a href="?section=influencer_list" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?php echo $section === 'influencer_list' ? 'bg-green-50 text-[#1FAE5B]' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <i class="fas fa-users text-lg"></i>
                Influencer List
            </a>

            <div class="pt-6 pb-2 px-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pipeline</span>
            </div>
            <a href="?section=pipeline" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?php echo $section === 'pipeline' ? 'bg-green-50 text-[#1FAE5B]' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <i class="fas fa-chart-column text-lg"></i>
                Pipeline
            </a>
            <a href="?section=closed_collaboration" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?php echo $section === 'closed_collaboration' ? 'bg-green-50 text-[#1FAE5B]' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <i class="fas fa-handshake text-lg"></i>
                Closed Collaboration
            </a>

            <div class="pt-6 pb-2 px-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Network</span>
            </div>
            <a href="?section=ambassadors" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?php echo $section === 'ambassadors' ? 'bg-green-50 text-[#1FAE5B]' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <i class="fas fa-star text-lg"></i>
                Ambassadors
            </a>
            <a href="?section=analytics" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?php echo $section === 'analytics' ? 'bg-green-50 text-[#1FAE5B]' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <i class="fas fa-chart-pie text-lg"></i>
                Analytics
            </a>
        </nav>

        <div class="p-4 border-t border-slate-100">
            <a href="<?php echo base_url('auth/logout'); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-red-500 hover:bg-red-50 transition-all">
                <i class="fas fa-sign-out-alt text-lg"></i>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <div class="flex items-center gap-4">
                <h2 class="font-bold text-slate-800 text-lg">
                    <?php echo ucwords(str_replace('_', ' ', $section)); ?>
                </h2>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex items-center bg-slate-100 rounded-full px-4 py-1.5 gap-2 border border-slate-200">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Online</span>
                </div>
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900">Employee User</p>
                        <p class="text-[10px] text-slate-500 font-medium">Employee Panel</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Employee+User&background=1FAE5B&color=fff&bold=true" class="w-9 h-9 rounded-full border-2 border-slate-100 group-hover:border-green-500 transition-all">
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8">
            
            <?php if ($section === 'dashboard'): ?>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-[#1FAE5B]"><i class="fas fa-search"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">New Found</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">12</h3>
                        <p class="text-xs text-slate-500 mt-1">Influencers sourced today</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500"><i class="fas fa-spinner"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">45</h3>
                        <p class="text-xs text-slate-500 mt-1">Ongoing pipeline stages</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500"><i class="fas fa-check-double"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Closed</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">156</h3>
                        <p class="text-xs text-slate-500 mt-1">Total collaborations</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500"><i class="fas fa-tasks"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tasks</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">8</h3>
                        <p class="text-xs text-red-500 mt-1 font-bold">Due today</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h4 class="font-bold text-slate-800 mb-6 flex items-center justify-between">
                            My Recent Pipeline
                            <a href="?section=pipeline" class="text-xs text-[#1FAE5B] hover:underline">View All</a>
                        </h4>
                        <div class="space-y-4">
                            <?php for($i=1; $i<=4; $i++): ?>
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-4">
                                    <img src="https://ui-avatars.com/api/?name=Influencer+<?php echo $i; ?>&background=random" class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">Influencer Profile #<?php echo $i; ?></p>
                                        <p class="text-[10px] text-slate-500">Instagram • 250k followers</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 uppercase">Negotiation</span>
                                    <button class="text-slate-400 hover:text-slate-600"><i class="fas fa-ellipsis-v"></i></button>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h4 class="font-bold text-slate-800 mb-6 flex items-center justify-between">
                            Today's Tasks
                            <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full">8 Tasks</span>
                        </h4>
                        <div class="space-y-4">
                            <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border-l-4 border-red-500">
                                <input type="checkbox" class="mt-1">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Email Follow-up</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Send contract to @influencer_name</p>
                                </div>
                            </div>
                            <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border-l-4 border-orange-500">
                                <input type="checkbox" class="mt-1">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Review Insights</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Check post performance for Campaign X</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($section === 'discovery'): ?>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 mb-8">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" placeholder="Search influencers by name, niche, or location..." class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 focus:border-green-500 focus:ring-4 focus:ring-green-50 transition-all text-sm outline-none">
                        </div>
                        <div class="flex gap-2">
                            <button class="px-6 py-3.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition">Filters</button>
                            <button class="px-8 py-3.5 bg-[#1FAE5B] text-white rounded-xl text-sm font-bold hover:bg-green-600 transition shadow-lg shadow-green-500/20">Search</button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php for($i=1; $i<=8; $i++): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden group hover:border-green-500 transition-all">
                        <div class="relative h-32 bg-slate-100">
                            <img src="https://ui-avatars.com/api/?name=Influencer+<?php echo $i; ?>&size=128&background=random" class="absolute -bottom-6 left-6 w-16 h-16 rounded-2xl border-4 border-white shadow-sm">
                            <button class="absolute top-4 right-4 w-8 h-8 bg-white/80 backdrop-blur rounded-lg text-slate-400 hover:text-red-500 transition-all"><i class="far fa-heart"></i></button>
                        </div>
                        <div class="pt-10 p-6">
                            <h4 class="font-bold text-slate-800">Influencer Name <?php echo $i; ?></h4>
                            <p class="text-xs text-slate-500 mb-4">Fashion & Lifestyle • Manila, PH</p>
                            <div class="grid grid-cols-2 gap-4 py-4 border-y border-slate-50 mb-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Followers</p>
                                    <p class="text-sm font-bold text-slate-800">125K</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Engagement</p>
                                    <p class="text-sm font-bold text-green-500">4.2%</p>
                                </div>
                            </div>
                            <button class="w-full py-2.5 bg-slate-50 text-slate-600 rounded-xl text-xs font-bold hover:bg-green-50 hover:text-[#1FAE5B] transition-all">Add to List</button>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <?php if ($section === 'influencer_list'): ?>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Influencer Database</h3>
                        <p class="text-sm text-slate-500">Manage your private network of sourced influencers.</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-slate-100 text-slate-600 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-200 transition flex items-center gap-2">
                            <i class="fas fa-file-import"></i> Import CSV
                        </button>
                        <button class="bg-black text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-800 transition flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Manually
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Influencer</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Platforms</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Niche</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Assessment</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php for($i=1; $i<=6; $i++): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=I+<?php echo $i; ?>&background=random" class="w-9 h-9 rounded-full">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">Username_<?php echo $i; ?></p>
                                            <p class="text-[10px] text-slate-500">Added 2 days ago</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <i class="fab fa-instagram text-pink-600 text-lg"></i>
                                        <i class="fab fa-tiktok text-slate-800 text-lg"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full uppercase">Lifestyle</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 text-orange-400">
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="far fa-star text-xs"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button class="px-3 py-1 bg-green-50 text-[#1FAE5B] rounded-lg text-[10px] font-bold hover:bg-green-100 transition-all uppercase tracking-wider">Qualify</button>
                                        <button class="p-2 text-slate-400 hover:text-slate-600"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($section === 'pipeline'): ?>
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Campaign Pipeline</h3>
                        <p class="text-sm text-slate-500">Track influencer progress through various collaboration stages.</p>
                    </div>
                    <button class="bg-[#1FAE5B] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-green-600 transition flex items-center gap-2 shadow-lg shadow-green-500/20">
                        <i class="fas fa-user-plus"></i> Add to Pipeline
                    </button>
                </div>

                <div class="flex gap-6 overflow-x-auto pb-6 -mx-8 px-8 h-[calc(100vh-250px)]">
                    <?php 
                    $stages = [
                        ['name' => 'Identified', 'count' => 12, 'color' => 'slate'],
                        ['name' => 'Outreach', 'count' => 8, 'color' => 'blue'],
                        ['name' => 'Negotiation', 'count' => 5, 'color' => 'orange'],
                        ['name' => 'Contracting', 'count' => 3, 'color' => 'purple'],
                        ['name' => 'Content Review', 'count' => 4, 'color' => 'yellow'],
                    ];
                    foreach($stages as $stage): 
                    ?>
                    <div class="w-80 flex-shrink-0 flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-widest"><?php echo $stage['name']; ?></h4>
                                <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo $stage['count']; ?></span>
                            </div>
                            <button class="text-slate-400 hover:text-slate-600"><i class="fas fa-plus-circle"></i></button>
                        </div>
                        <div class="flex-1 space-y-4 overflow-y-auto pr-2">
                            <?php for($i=1; $i<=3; $i++): ?>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm cursor-grab active:cursor-grabbing hover:border-green-500 transition-all">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name=User+<?php echo $i; ?>&background=random" class="w-8 h-8 rounded-full">
                                        <div>
                                            <p class="text-xs font-bold text-slate-800">Profile_Name_<?php echo $i; ?></p>
                                            <p class="text-[9px] text-slate-500">Instagram • 120k</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <div class="flex -space-x-2">
                                        <div class="w-6 h-6 rounded-full border-2 border-white bg-green-100 flex items-center justify-center text-[8px] text-green-600 font-bold">C</div>
                                        <div class="w-6 h-6 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-[8px] text-blue-600 font-bold">S</div>
                                    </div>
                                    <p class="text-[9px] text-slate-400">Updated 1h ago</p>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($section === 'closed_collaboration'): ?>
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Closed Collaborations</h1>
                        <p class="text-sm text-gray-400 mt-1 font-medium">Manage and track your completed campaigns</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="group flex items-center bg-white border border-gray-200 rounded-full px-4 py-2 shadow-sm hover:shadow-md focus-within:ring-2 focus-within:ring-green-500/20 focus-within:border-green-500 transition-all duration-300 w-72">
                            <i class="fas fa-search text-gray-400 group-hover:text-green-500 transition-colors mr-3"></i>
                            <input 
                                type="text" 
                                placeholder="Search by name or platform..." 
                                class="bg-transparent border-none text-sm focus:outline-none w-full text-gray-700 placeholder-gray-400"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex gap-6 items-start pb-8 overflow-x-auto h-[calc(100vh-250px)] px-1">
                    <?php 
                    $closed_stages = [
                        'For Order Creation' => ['color' => 'bg-teal-50', 'badge' => 'bg-teal-100 text-teal-700', 'icon' => 'fa-cart-plus'],
                        'In-Transit' => ['color' => 'bg-amber-50', 'badge' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-truck'],
                        'Delivered' => ['color' => 'bg-cyan-50', 'badge' => 'bg-cyan-100 text-cyan-700', 'icon' => 'fa-box-open'],
                        'Posted' => ['color' => 'bg-green-50', 'badge' => 'bg-green-100 text-green-700', 'icon' => 'fa-share-square'],
                        'Campaign Completed' => ['color' => 'bg-purple-50', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => 'fa-flag-checkered']
                    ];
                    foreach($closed_stages as $name => $cfg): 
                    ?>
                    <div class="w-80 flex-shrink-0 flex flex-col h-full bg-slate-50/50 rounded-2xl p-4 border border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full <?php echo str_replace('text-', 'bg-', explode(' ', $cfg['badge'])[1]); ?>"></span>
                                <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo $name; ?></h4>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400">2</span>
                        </div>
                        <div class="space-y-4 overflow-y-auto pr-1">
                            <?php for($i=1; $i<=2; $i++): ?>
                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md hover:border-green-500 transition-all cursor-pointer group">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name=User+<?php echo $i; ?>&background=random" class="w-10 h-10 rounded-full">
                                        <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5 shadow-sm">
                                            <i class="fab fa-instagram text-[10px] text-pink-600"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">Influencer_Name_<?php echo $i; ?></p>
                                        <p class="text-[10px] text-slate-400">@username_<?php echo $i; ?></p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <div class="flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-700">
                                        <i class="fas fa-truck"></i> FULFILLED
                                    </div>
                                    <div class="flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                        <i class="fas fa-gift"></i> GIFTING
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[8px] font-bold">JD</div>
                                    </div>
                                    <i class="fas <?php echo $cfg['icon']; ?> text-slate-300 group-hover:text-green-500 transition-colors"></i>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($section === 'ambassadors'): ?>
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Brand Ambassadors</h3>
                        <p class="text-sm text-slate-500">Your core network of long-term brand partners.</p>
                    </div>
                    <button class="bg-black text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-800 transition flex items-center gap-2 shadow-lg shadow-black/10">
                        <i class="fas fa-plus"></i> New Proposal
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php for($i=1; $i<=6; $i++): ?>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1 h-full bg-green-500 transition-all group-hover:w-2"></div>
                        <div class="flex items-center gap-4 mb-6">
                            <img src="https://ui-avatars.com/api/?name=Ambassador+<?php echo $i; ?>&background=random" class="w-14 h-14 rounded-2xl">
                            <div>
                                <h4 class="font-bold text-slate-800">Ambassador Profile <?php echo $i; ?></h4>
                                <p class="text-xs text-slate-500">Partner since Jan 2026</p>
                            </div>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Total Reach</span>
                                <span class="font-bold text-slate-800">1.2M</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Active Campaigns</span>
                                <span class="font-bold text-slate-800">2</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Contract Value</span>
                                <span class="font-bold text-slate-800">$12k/year</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex-1 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all">Brand Info</button>
                            <button class="flex-1 py-2 bg-green-50 text-[#1FAE5B] rounded-xl text-xs font-bold hover:bg-green-100 transition-all">Message</button>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <?php if ($section === 'analytics'): ?>
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-slate-800">Performance Analytics</h3>
                    <p class="text-sm text-slate-500">Visualize growth, engagement, and campaign ROI.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h4 class="font-bold text-slate-800 mb-6">Influencer Growth</h4>
                        <div class="h-64 flex items-end justify-between gap-2 px-2">
                            <?php for($i=1; $i<=12; $i++): $h = rand(30, 100); ?>
                            <div class="flex-1 bg-green-100 rounded-t-lg relative group transition-all hover:bg-[#1FAE5B]" style="height: <?php echo $h; ?>%;">
                                <div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap"><?php echo $h; ?>k growth</div>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <div class="flex justify-between mt-4 px-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jan</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jun</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dec</span>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h4 class="font-bold text-slate-800 mb-6">Platform Performance</h4>
                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-700"><i class="fab fa-instagram mr-2 text-pink-600"></i> Instagram</span>
                                    <span class="font-bold text-slate-900">65%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-pink-500 rounded-full" style="width: 65%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-700"><i class="fab fa-tiktok mr-2 text-slate-800"></i> TikTok</span>
                                    <span class="font-bold text-slate-900">25%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-slate-800 rounded-full" style="width: 25%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-700"><i class="fab fa-youtube mr-2 text-red-600"></i> YouTube</span>
                                    <span class="font-bold text-slate-900">10%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 rounded-full" style="width: 10%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h4 class="font-bold text-slate-800 mb-6">Brand Awareness Index</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                        <div class="p-4">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Impressions</p>
                            <h5 class="text-2xl font-bold text-slate-800">4.2M</h5>
                            <p class="text-xs text-green-500 mt-1 font-bold">+18%</p>
                        </div>
                        <div class="p-4 border-x border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Engagements</p>
                            <h5 class="text-2xl font-bold text-slate-800">284k</h5>
                            <p class="text-xs text-green-500 mt-1 font-bold">+5.2%</p>
                        </div>
                        <div class="p-4">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Conversions</p>
                            <h5 class="text-2xl font-bold text-slate-800">12.5k</h5>
                            <p class="text-xs text-red-500 mt-1 font-bold">-2.1%</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>