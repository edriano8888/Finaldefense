<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Prototype - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                            dark: '#111827'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#111827] flex-shrink-0 flex flex-col transition-all duration-300 hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-brand-green rounded-lg flex items-center justify-center text-white font-bold">M</div>
                <span class="text-xl font-bold tracking-tight text-white">Manager Panel</span>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a href="?section=dashboard" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'dashboard' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-chart-line w-6"></i>
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="?section=campaign_planning" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'campaign_planning' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-bullseye w-6"></i>
                <span class="ml-2">Campaign Planning</span>
            </a>
            <a href="?section=team_management" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'team_management' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-user-friends w-6"></i>
                <span class="ml-2">Team Management</span>
            </a>
            <a href="?section=influencer_review" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'influencer_review' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-user-check w-6"></i>
                <span class="ml-2">Influencer Review</span>
            </a>
            <a href="?section=orders_discounts" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'orders_discounts' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-receipt w-6"></i>
                <span class="ml-2">Orders & Discounts</span>
            </a>
            <a href="?section=performance" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'performance' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-chart-bar w-6"></i>
                <span class="ml-2">Performance</span>
            </a>
            <a href="?section=reports" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'reports' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-file-alt w-6"></i>
                <span class="ml-2">Reports</span>
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
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10">
            <h2 class="text-lg font-semibold text-gray-800 uppercase tracking-wider">
                <?php echo str_replace('_', ' ', $section); ?>
            </h2>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="far fa-bell text-xl"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>
                <div class="h-8 w-px bg-gray-200 mx-2"></div>
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800 group-hover:text-brand-green transition-colors">Manager User</p>
                        <p class="text-xs text-gray-500">Brand Manager</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Manager+User&background=111827&color=fff&bold=true" class="w-9 h-9 rounded-full shadow-sm border-2 border-gray-200 group-hover:border-brand-green transition-all">
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-8">
            
            <?php if ($section === 'dashboard'): ?>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Active Campaigns</p>
                        <h3 class="text-3xl font-bold text-gray-800">12</h3>
                        <p class="text-xs text-green-500 mt-2 font-medium"><i class="fas fa-arrow-up mr-1"></i> +3 this month</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Pending Reviews</p>
                        <h3 class="text-3xl font-bold text-orange-500">45</h3>
                        <p class="text-xs text-gray-500 mt-2">Influencers sourced</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Pending Orders</p>
                        <h3 class="text-3xl font-bold text-blue-600">18</h3>
                        <p class="text-xs text-gray-500 mt-2">Ready for creation</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Members</p>
                        <h3 class="text-3xl font-bold text-gray-800">24</h3>
                        <p class="text-xs text-gray-500 mt-2">Team size</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-tasks text-brand-green"></i> Recent Campaigns
                        </h4>
                        <div class="space-y-4">
                            <?php for($i=1; $i<=3; $i++): ?>
                            <div class="p-4 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h5 class="text-sm font-bold text-gray-800">Campaign Title #<?php echo $i; ?></h5>
                                        <p class="text-xs text-gray-500">Starts: 2026-04-10</p>
                                    </div>
                                    <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full uppercase">Ongoing</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-200 rounded-full mt-4 overflow-hidden">
                                    <div class="h-full bg-brand-green rounded-full" style="width: 45%;"></div>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-users text-blue-500"></i> Top Team Performance
                        </h4>
                        <div class="space-y-4">
                            <?php for($i=1; $i<=4; $i++): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Member+<?php echo $i; ?>&background=random" class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">Team Member <?php echo $i; ?></p>
                                        <p class="text-[10px] text-gray-500">12 Influencers Source</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-brand-green">98% Success</span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($section === 'campaign_planning'): ?>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Campaign Planning</h3>
                        <p class="text-sm text-gray-500">Manage and schedule your brand collaborations.</p>
                    </div>
                    <button class="bg-brand-green text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-brand-deep transition flex items-center gap-2 shadow-lg shadow-green-500/20">
                        <i class="fas fa-plus"></i> Create Campaign
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <?php for($i=1; $i<=4; $i++): ?>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:border-brand-green transition-all group">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:text-brand-green transition-colors text-xl border border-gray-100">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div>
                                    <h5 class="text-base font-bold text-gray-800">Strategic Campaign - Phase <?php echo $i; ?></h5>
                                    <p class="text-xs text-gray-500">Duration: 30 Days • Target ROI: 4.5x</p>
                                    <div class="mt-2 flex items-center gap-3">
                                        <span class="text-[10px] font-bold uppercase bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Planning</span>
                                        <span class="text-[10px] font-bold text-gray-400"><i class="fas fa-users mr-1"></i> 8 Influencers assigned</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-200 transition-all">Details</button>
                                <button class="px-4 py-2 bg-green-50 text-brand-green rounded-lg text-xs font-bold hover:bg-green-100 transition-all">Manage</button>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <?php if ($section === 'team_management'): ?>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Users & Access</h3>
                        <p class="text-sm text-gray-500">Manage your brand team members and their roles.</p>
                    </div>
                    <button class="bg-brand-green text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-brand-deep transition flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Invite Member
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php for($i=1; $i<=6; $i++): ?>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name=User+<?php echo $i; ?>&background=random" class="w-12 h-12 rounded-full border-2 border-gray-50">
                            <div>
                                <h5 class="text-sm font-bold text-gray-800">Team Member <?php echo $i; ?></h5>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Employee</p>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600 p-2"><i class="fas fa-ellipsis-v"></i></button>
                    </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <?php if ($section === 'performance'): ?>
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Performance Analytics</h3>
                    <p class="text-sm text-gray-500">Track and analyze your brand's influencer marketing impact.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
                        <h4 class="font-bold text-gray-800 mb-8 flex items-center justify-between">
                            Revenue Over Time
                            <select class="text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1 outline-none">
                                <option>Last 6 Months</option>
                                <option>Last Year</option>
                            </select>
                        </h4>
                        <div class="h-80 flex items-end justify-between gap-4">
                            <?php for($i=1; $i<=6; $i++): $h = rand(40, 100); ?>
                            <div class="flex-1 bg-green-50 rounded-t-lg relative group transition-all hover:bg-brand-green" style="height: <?php echo $h; ?>%;">
                                <div class="hidden group-hover:block absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap">$<?php echo $h; ?>k Revenue</div>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <div class="flex justify-between mt-6 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <span>Jan</span>
                            <span>Feb</span>
                            <span>Mar</span>
                            <span>Apr</span>
                            <span>May</span>
                            <span>Jun</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
                        <h4 class="font-bold text-gray-800 mb-8">Campaign Metrics</h4>
                        <div class="space-y-8">
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-3">
                                    <span class="text-gray-600">Total Reach</span>
                                    <span class="text-gray-900">4.2M</span>
                                </div>
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-brand-green rounded-full" style="width: 85%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-3">
                                    <span class="text-gray-600">Engagement</span>
                                    <span class="text-gray-900">12.5%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: 65%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-3">
                                    <span class="text-gray-600">Conversion Rate</span>
                                    <span class="text-gray-900">3.2%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-orange-500 rounded-full" style="width: 45%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($section === 'influencer_review'): ?>
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Influencer Reviews</h1>
                    <p class="text-gray-500">Approve or reject influencers sourced by your team.</p>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Influencer</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Platform</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Followers</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Niche</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Influencer+<?php echo $i; ?>&background=random" class="w-10 h-10 rounded-full bg-gray-100">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">Influencer Name <?php echo $i; ?></p>
                                                <p class="text-xs text-gray-500">@username_<?php echo $i; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
                                            Instagram
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-700">
                                        <?php echo number_format(rand(10000, 500000)); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        Lifestyle
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <button class="bg-brand-green hover:bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-lg shadow-green-500/10">
                                                Approve
                                            </button>
                                            <button class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-xs font-bold transition-all border border-red-100">
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($section === 'orders_discounts'): ?>
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Orders & Discounts</h1>
                    <p class="text-gray-500">Review and approve product sample requests or discount codes.</p>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Product / Order</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Cost</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php for($i=1; $i<=4; $i++): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-lg">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">Product Sample #<?php echo $i; ?></p>
                                                <p class="text-xs text-gray-500">Influencer Campaign Request</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-700">
                                        $<?php echo number_format(rand(50, 500), 2); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        Apr 10, 2026
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <button class="bg-brand-green hover:bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-lg shadow-green-500/10">
                                                Approve
                                            </button>
                                            <button class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-xs font-bold transition-all border border-red-100">
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($section === 'reports'): ?>
                <div class="space-y-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Reports</h3>
                        <p class="text-gray-500 text-sm">Review team activities and campaign final results.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:border-brand-green transition-all group">
                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-brand-green mb-6 text-xl group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Review Employee Reports</h4>
                            <p class="text-sm text-gray-500 mb-6">Check and validate the sourcing reports submitted by your employees.</p>
                            <div class="flex items-center gap-3">
                                <input type="text" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/20 focus:border-brand-green outline-none transition-all" placeholder="Search reports...">
                                <button class="bg-brand-green hover:bg-brand-deep text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-green-500/10">Open</button>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:border-brand-green transition-all group">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 mb-6 text-xl group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Submit Final Campaign Report</h4>
                            <p class="text-sm text-gray-500 mb-6">Compile and submit the final ROI and performance metrics for active campaigns.</p>
                            <div class="flex items-center gap-3">
                                <input type="text" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/20 focus:border-brand-green outline-none transition-all" placeholder="Enter report title...">
                                <button class="bg-brand-green hover:bg-brand-deep text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-green-500/10">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

</body>
</html>