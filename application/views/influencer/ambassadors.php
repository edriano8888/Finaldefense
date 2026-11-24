<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Ambassadors & Contracts - Instroom</title>
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
                            dark: '#111827',
                            gray: '#F9FAFB',
                            border: '#E5E7EB',
                            blue: '#3B82F6',
                            purple: '#8B5CF6',
                            rose: '#F43F5E',
                            amber: '#F59E0B'
                        }
                    }
                }
            }
        }

        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            if (menu) menu.classList.toggle('hidden');
        }

        function toggleBrandMenu() {
            const menu = document.getElementById('brand-menu');
            if (menu) menu.classList.toggle('hidden');
        }

        function toggleNotifMenu() {
            const menu = document.getElementById('notif-menu');
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Click outside handler for dropdowns
            document.addEventListener('click', function(e) {
                const userContainer = document.getElementById('user-menu-container');
                const userMenu = document.getElementById('user-menu');
                if (userContainer && userMenu && !userContainer.contains(e.target)) {
                    userMenu.classList.add('hidden');
                }

                const brandContainer = document.getElementById('brand-menu-container');
                const brandMenu = document.getElementById('brand-menu');
                if (brandContainer && brandMenu && !brandContainer.contains(e.target)) {
                    brandMenu.classList.add('hidden');
                }

                const notifContainer = document.getElementById('notif-container');
                const notifMenu = document.getElementById('notif-menu');
                if (notifContainer && notifMenu && !notifContainer.contains(e.target)) {
                    notifMenu.classList.add('hidden');
                }
            });
        });
    </script>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">

    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Campaigns', 'child' => 'Ambassadors']); ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 md:p-8 scroll-smooth relative">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-80 bg-gradient-to-b from-emerald-950/5 via-green-50/40 to-transparent -z-10"></div>
            
            <div class="max-w-7xl mx-auto relative space-y-8">
                
                <!-- 🌟 1. HERO BANNER: Brand Partners Copy & Checkmark Features -->
                <div class="relative bg-gradient-to-br from-[#0F6B3E] via-[#1FAE5B] to-[#0A4729] rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl overflow-hidden border border-emerald-400/20">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-300/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl"></div>

                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-8 space-y-4">
                            <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-3.5 py-1.5 rounded-full text-xs font-black text-emerald-100 border border-white/20 uppercase tracking-wider">
                                🤝 Relationship-Driven Influencer System
                            </div>

                            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white leading-tight">
                                Brand Partners
                            </h1>

                            <p class="text-emerald-100 font-extrabold text-lg md:text-xl italic">
                                "Creators worth more than a campaign."
                            </p>

                            <p class="text-emerald-50 text-sm md:text-base font-medium leading-relaxed max-w-2xl">
                                Some creators keep delivering, campaign after campaign. <strong>Brand Partners</strong> gives those relationships structure: <strong>tiered status</strong>, <strong>retainer tracking</strong>, and <strong>full performance history</strong>.
                            </p>

                            <p class="text-white/80 text-xs md:text-sm font-medium leading-relaxed max-w-2xl pt-1">
                                When the budget conversation comes up, the answer is already in the data. You know exactly who's making you money, who's consistent, and who deserves a retainer. The best influencer programs aren't built on campaigns. They're built on relationships.
                            </p>

                            <!-- Checkmark Features Row -->
                            <div class="pt-4 flex flex-wrap items-center gap-4 md:gap-6 border-t border-white/15">
                                <div class="flex items-center gap-2 bg-white/10 px-3.5 py-2 rounded-2xl backdrop-blur-sm border border-white/10 text-xs font-black text-white">
                                    <i class="fas fa-check-circle text-emerald-300"></i>
                                    <span>Client workspace access</span>
                                </div>
                                <div class="flex items-center gap-2 bg-white/10 px-3.5 py-2 rounded-2xl backdrop-blur-sm border border-white/10 text-xs font-black text-white">
                                    <i class="fas fa-check-circle text-emerald-300"></i>
                                    <span>Permission levels</span>
                                </div>
                                <div class="flex items-center gap-2 bg-white/10 px-3.5 py-2 rounded-2xl backdrop-blur-sm border border-white/10 text-xs font-black text-white">
                                    <i class="fas fa-check-circle text-emerald-300"></i>
                                    <span>Real-time updates</span>
                                </div>
                                <div class="flex items-center gap-2 bg-white/10 px-3.5 py-2 rounded-2xl backdrop-blur-sm border border-white/10 text-xs font-black text-white">
                                    <i class="fas fa-check-circle text-emerald-300"></i>
                                    <span>White-labeling ready</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right CTA / Retainer Highlight Box -->
                        <div class="lg:col-span-4 flex justify-center lg:justify-end">
                            <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-3xl space-y-5 text-white shadow-2xl max-w-sm w-full">
                                <div class="flex items-center justify-between border-b border-white/15 pb-3">
                                    <div class="flex items-center gap-2 text-amber-300 text-xs font-black">
                                        <i class="fas fa-crown text-base"></i>
                                        <span>AUTOMATED TIERS</span>
                                    </div>
                                    <span class="bg-emerald-400/20 text-emerald-200 text-[10px] font-black px-2 py-0.5 rounded-full border border-emerald-300/30 uppercase">Live Sync</span>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-emerald-100">🥇 Gold Milestone</span>
                                        <span class="font-black text-amber-300">&gt; $500,000 GMV</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-emerald-100">🥈 Silver Milestone</span>
                                        <span class="font-black text-slate-200">$150k - $500k GMV</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-emerald-100">🥉 Bronze Milestone</span>
                                        <span class="font-black text-amber-200">$50k - $150k GMV</span>
                                    </div>
                                </div>

                                <div class="bg-black/20 p-3 rounded-2xl text-[11px] text-white/90 leading-relaxed font-medium">
                                    <i class="fas fa-magic text-emerald-300 mr-1"></i>
                                    Instroom assigns Bronze, Silver, and Gold tiers automatically as creators hit milestones. No manual updating, no missed promotions.
                                </div>

                                <a href="<?php echo base_url('ambassadors/proposal'); ?>" class="block w-full py-3 bg-white hover:bg-emerald-50 text-[#0F6B3E] font-black text-xs text-center rounded-2xl transition-all shadow-lg hover:shadow-xl">
                                    Draft New Partner Agreement
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ⚙️ 2. AUTOMATED REVENUE THRESHOLDS & BUDGET DECISION DATA -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    <!-- Revenue Threshold Controls -->
                    <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-sliders-h text-brand-green"></i>
                                    Set Revenue Thresholds
                                </h3>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Automated Tier milestone triggers for instant status updates.</p>
                            </div>
                            <span class="px-3 py-1 bg-green-50 text-brand-deep font-extrabold text-[11px] rounded-xl border border-green-100">
                                Real-Time Milestones
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <!-- Gold Card -->
                            <div class="bg-gradient-to-br from-amber-50 to-yellow-50/40 rounded-2xl p-4 border border-amber-200/80">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-black uppercase text-amber-900 tracking-wider">Gold Tier</span>
                                    <span class="text-base">🥇</span>
                                </div>
                                <p class="text-lg font-black text-amber-950">&gt; $500,000</p>
                                <p class="text-[10px] text-amber-800/80 font-bold mt-1">Retainer & VIP Access</p>
                            </div>

                            <!-- Silver Card -->
                            <div class="bg-gradient-to-br from-slate-50 to-gray-100/50 rounded-2xl p-4 border border-slate-200">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-black uppercase text-slate-800 tracking-wider">Silver Tier</span>
                                    <span class="text-base">🥈</span>
                                </div>
                                <p class="text-lg font-black text-slate-900">$150k - $500k</p>
                                <p class="text-[10px] text-slate-600 font-bold mt-1">Consistent Partner</p>
                            </div>

                            <!-- Bronze Card -->
                            <div class="bg-gradient-to-br from-amber-900/5 to-amber-700/5 rounded-2xl p-4 border border-amber-800/20">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-black uppercase text-amber-900 tracking-wider">Bronze Tier</span>
                                    <span class="text-base">🥉</span>
                                </div>
                                <p class="text-lg font-black text-amber-900">$50k - $150k</p>
                                <p class="text-[10px] text-amber-800 font-bold mt-1">Rising Milestone</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-3.5 flex items-center justify-between text-xs text-gray-600 font-medium border border-gray-100">
                            <span>No manual updating required. Tiers update automatically when creators cross milestone boundaries.</span>
                            <button onclick="openThresholdModal()" class="text-brand-deep hover:text-brand-green font-extrabold flex-shrink-0 ml-3">
                                Configure Thresholds &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- Executive Budget & Retainer Insights -->
                    <div class="lg:col-span-5 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white rounded-3xl p-6 shadow-sm space-y-4 border border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-black text-white flex items-center gap-2">
                                <i class="fas fa-chart-pie text-amber-400"></i>
                                Retainer & Budget Decision Data
                            </h3>
                            <span class="text-[10px] font-bold bg-amber-400/20 text-amber-300 px-2.5 py-1 rounded-full border border-amber-300/30">
                                Data-Driven
                            </span>
                        </div>

                        <p class="text-xs text-gray-300 font-medium leading-relaxed">
                            "When the budget conversation comes up, the answer is already in the data. You know exactly who's making you money, who's consistent, and who deserves a retainer."
                        </p>

                        <?php
                            $top_money_maker_name = 'None';
                            $top_money_maker_gmv = 0;
                            $most_consistent_name = 'None';
                            $most_consistent_score = 0;
                            $retainer_rec_name = 'None';
                            $retainer_rec_amount = 0;

                            foreach ($partnerships as $p) {
                                if ($p->gmv_generated > $top_money_maker_gmv) {
                                    $top_money_maker_gmv = $p->gmv_generated;
                                    $top_money_maker_name = $p->ambassador_name;
                                }
                                if ($p->performance_score > $most_consistent_score) {
                                    $most_consistent_score = $p->performance_score;
                                    $most_consistent_name = $p->ambassador_name;
                                }
                                if ($p->retainer_amount > $retainer_rec_amount) {
                                    $retainer_rec_amount = $p->retainer_amount;
                                    $retainer_rec_name = $p->ambassador_name;
                                }
                            }
                            if ($retainer_rec_name === 'None' && !empty($partnerships)) {
                                foreach ($partnerships as $p) {
                                    if ($p->tier === 'Silver' || $p->tier === 'Gold') {
                                        $retainer_rec_name = $p->ambassador_name;
                                        $retainer_rec_amount = ($p->tier === 'Gold') ? 15000 : 8000;
                                        break;
                                    }
                                }
                            }
                        ?>
                        <div class="space-y-2.5 pt-1">
                            <div class="bg-white/10 rounded-2xl p-3 flex items-center justify-between text-xs">
                                <span class="text-gray-300 font-bold">Top Money Maker (GMV):</span>
                                <span class="font-black text-amber-300"><?= html_escape($top_money_maker_name) ?> ($<?= number_format($top_money_maker_gmv) ?>)</span>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-3 flex items-center justify-between text-xs">
                                <span class="text-gray-300 font-bold">Most Consistent Delivery:</span>
                                <span class="font-black text-emerald-300"><?= html_escape($most_consistent_name) ?> (<?= $most_consistent_score ?>% On-Time)</span>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-3 flex items-center justify-between text-xs">
                                <span class="text-gray-300 font-bold">Retainer Recommended:</span>
                                <span class="font-black text-purple-300">
                                    <?php if ($retainer_rec_name !== 'None'): ?>
                                        <?= html_escape($retainer_rec_name) ?> ($<?= number_format($retainer_rec_amount) ?>/mo)
                                    <?php else: ?>
                                        None
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 📊 3. TIER & COMMUNITY STATUS FILTER CHIPS -->
                <div class="flex flex-wrap items-center justify-between gap-4 bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-1">Tier:</span>
                        <button onclick="filterTier('all')" id="btn-tier-all" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-brand-deep text-white shadow-xs">
                            All Tiers (<?= count($partnerships) ?>)
                        </button>
                        <button onclick="filterTier('Gold')" id="btn-tier-Gold" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-amber-100 hover:bg-amber-200 text-amber-950 border border-amber-300">
                            🥇 Gold (&gt;$<?= number_format($revenue_thresholds->gold / 1000) ?>k)
                        </button>
                        <button onclick="filterTier('Silver')" id="btn-tier-Silver" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300">
                            🥈 Silver ($<?= number_format($revenue_thresholds->silver / 1000) ?>k-$<?= number_format($revenue_thresholds->gold / 1000) ?>k)
                        </button>
                        <button onclick="filterTier('Bronze')" id="btn-tier-Bronze" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300">
                            🥉 Bronze ($<?= number_format($revenue_thresholds->bronze / 1000) ?>k-$<?= number_format($revenue_thresholds->silver / 1000) ?>k)
                        </button>

                        <div class="h-5 w-px bg-gray-200 mx-2 hidden sm:block"></div>

                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-1">Community Status:</span>
                        <button onclick="filterCommunity('all')" id="btn-comm-all" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-gray-900 text-white shadow-xs">
                            All Statuses
                        </button>
                        <button onclick="filterCommunity('Joined')" id="btn-comm-Joined" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200">
                            🟢 Joined
                        </button>
                        <button onclick="filterCommunity('Invited')" id="btn-comm-Invited" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200">
                            🔵 Invited
                        </button>
                        <button onclick="filterCommunity('Pending')" id="btn-comm-Pending" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200">
                            🟡 Pending
                        </button>
                    </div>
                </div>

                <!-- 📋 4. BRAND PARTNERS MAIN TABLE WITH RETAINER TRACKING & PERFORMANCE HISTORY -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[11px] font-black text-brand-deep uppercase tracking-wider">Live Ambassador Directory</span>
                        </div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Brand Partners & Retainer Roster</h2>
                        <p class="text-xs text-gray-500 font-medium mt-1">Tiered Status • Retainer Tracking • Full Performance History • Community Status</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button 
                            onclick="openReportModal()"
                            class="px-4 py-2 bg-brand-deep hover:bg-brand-green text-white font-extrabold text-xs rounded-2xl transition-all shadow-md flex items-center gap-2"
                        >
                            <i class="fas fa-chart-line"></i>
                            <span>Client Reports</span>
                        </button>
                        <span class="text-xs font-bold text-gray-600 bg-white px-3.5 py-2 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-2">
                            <i class="fas fa-handshake text-brand-green"></i>
                            <span>Active Partners: <strong class="text-gray-900 font-black"><?= count($partnerships) ?></strong></span>
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-gray-100/90 backdrop-blur-md z-10 border-b border-gray-200">
                            <tr class="text-[11px] uppercase tracking-wider text-gray-700 font-black">
                                <th class="px-6 py-4"><i class="fas fa-crown text-amber-500 mr-1.5"></i> Tier Milestone</th>
                                <th class="px-6 py-4"><i class="fas fa-building text-brand-green mr-1.5"></i> Brand Partner & Creator</th>
                                <th class="px-6 py-4 text-center"><i class="fas fa-users text-blue-500 mr-1.5"></i> Community Status</th>
                                <th class="px-6 py-4 text-center"><i class="fas fa-handshake text-purple-600 mr-1.5"></i> Retainer Tracking</th>
                                <th class="px-6 py-4 text-center"><i class="fas fa-chart-line text-blue-600 mr-1.5"></i> Performance History</th>
                                <th class="px-6 py-4 text-right"><i class="fas fa-dollar-sign text-emerald-600 mr-1.5"></i> GMV Revenue</th>
                                <th class="px-6 py-4 text-center"><i class="fas fa-shield-alt text-amber-600 mr-1.5"></i> Workspace & Access</th>
                                <th class="px-6 py-4 text-center"><i class="fas fa-cog text-gray-500 mr-1.5"></i> Action</th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-gray-100" id="partnership-table-body">
                                <?php foreach($partnerships as $p): ?>
                                <tr class="partnership-row group hover:bg-gray-50/80 transition-all" data-tier="<?= $p->tier ?>" data-community="<?= $p->community_status ?>">
                                    
                                    <!-- Tier Milestone -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-full flex items-center justify-center font-black text-xs shadow-xs <?= $p->rank === 1 ? 'bg-amber-400 text-amber-950' : ($p->rank === 2 ? 'bg-slate-300 text-slate-900' : ($p->rank === 3 ? 'bg-amber-700 text-white' : 'bg-gray-100 text-gray-700')) ?>">
                                                #<?= $p->rank ?>
                                            </span>
                                            <?php if ($p->tier === 'Gold'): ?>
                                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500 text-amber-950 shadow-xs border border-yellow-300">
                                                    🥇 Gold Tier
                                                </span>
                                            <?php elseif ($p->tier === 'Silver'): ?>
                                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-gradient-to-r from-slate-200 via-gray-100 to-slate-400 text-slate-900 shadow-xs border border-slate-300">
                                                    🥈 Silver Tier
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-gradient-to-r from-amber-700 via-amber-600 to-yellow-800 text-amber-50 shadow-xs border border-amber-600">
                                                    🥉 Bronze Tier
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Brand Partner & Creator -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <a href="<?= base_url('ambassadors/manage_brand/' . $p->id) ?>" class="w-11 h-11 rounded-2xl bg-white border border-gray-200 p-1 flex items-center justify-center shadow-xs flex-shrink-0 hover:scale-105 transition-transform" title="Manage Brand Details & SOP">
                                                <img src="<?= $p->brand_logo ?>" class="w-full h-full object-contain rounded-xl">
                                            </a>
                                            <div>
                                                <h4 class="font-extrabold text-gray-900 text-sm flex items-center gap-1.5">
                                                    <a href="<?= base_url('ambassadors/manage_brand/' . $p->id) ?>" class="hover:text-brand-green hover:underline transition-colors" title="Manage Brand Details & SOP">
                                                        <?= html_escape($p->brand_name) ?>
                                                    </a>
                                                    <?php if (!empty($p->is_top_brand)): ?>
                                                        <span title="Best Brand Ambassador Partnership">👑</span>
                                                    <?php endif; ?>
                                                </h4>
                                                <div class="flex items-center gap-2 text-xs text-gray-500 font-bold mt-0.5">
                                                    <span><?= html_escape($p->ambassador_name) ?></span>
                                                    <span class="text-brand-green"><?= html_escape($p->ambassador_handle) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Community Status -->
                                    <td class="px-6 py-5 text-center">
                                        <select onchange="updateCommunityStatus(<?= $p->id ?>, this.value)" class="text-xs font-bold px-2.5 py-1 rounded-xl outline-none border cursor-pointer transition-all <?= $p->community_status === 'Joined' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($p->community_status === 'Invited' ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-amber-50 text-amber-800 border-amber-200') ?>">
                                            <option value="Joined" <?= $p->community_status === 'Joined' ? 'selected' : '' ?>>🟢 Joined</option>
                                            <option value="Invited" <?= $p->community_status === 'Invited' ? 'selected' : '' ?>>🔵 Invited</option>
                                            <option value="Pending" <?= $p->community_status === 'Pending' ? 'selected' : '' ?>>🟡 Pending</option>
                                        </select>
                                    </td>

                                    <!-- Retainer Tracking -->
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider <?= strpos($p->retainer_status, 'Active') !== false ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : (strpos($p->retainer_status, 'Recommended') !== false ? 'bg-purple-50 text-purple-800 border border-purple-200' : 'bg-gray-100 text-gray-700 border border-gray-200') ?>">
                                                <i class="fas fa-handshake mr-1.5"></i>
                                                <?= html_escape($p->retainer_status) ?>
                                            </span>
                                            <?php if ($p->retainer_amount > 0): ?>
                                                <span class="text-xs font-black text-gray-900 mt-1">$<?= number_format($p->retainer_amount) ?> / month</span>
                                            <?php else: ?>
                                                <span class="text-[11px] font-bold text-gray-400 mt-1">Per-Campaign Rate</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Full Performance History -->
                                    <td class="px-6 py-5 text-center">
                                        <button onclick="openPerformanceModal(<?= $p->id ?>)" class="group flex flex-col items-center mx-auto hover:scale-105 transition-transform" title="Click to view full performance history">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-black text-gray-900 group-hover:text-brand-green"><?= $p->campaigns_delivered ?> Campaigns</span>
                                                <span class="text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100"><?= $p->avg_roi ?></span>
                                            </div>
                                            <span class="text-[10px] text-gray-400 font-bold group-hover:underline">View History &rarr;</span>
                                        </button>
                                    </td>

                                    <!-- GMV Revenue -->
                                    <td class="px-6 py-5 text-right">
                                        <p class="text-sm font-black text-brand-green">$<?= number_format($p->gmv_generated) ?></p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">Contract: $<?= number_format($p->contract_value) ?></p>
                                    </td>

                                    <!-- Workspace & Access -->
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-[11px] font-black text-gray-800 flex items-center gap-1">
                                                <i class="fas fa-user-shield text-brand-green"></i>
                                                <?= html_escape($p->permission_level) ?>
                                            </span>
                                            <div class="flex items-center gap-1 text-[9px] font-bold">
                                                <span class="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Workspace Access ✓</span>
                                                <?php if(!empty($p->white_label_ready)): ?>
                                                    <span class="text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">White-Label</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-6 py-5 text-center">
                                        <button 
                                            onclick="openRetainerModal(<?= $p->id ?>, '<?= html_escape($p->ambassador_name) ?>', '<?= html_escape($p->brand_name) ?>', <?= $p->retainer_amount ?>, '<?= html_escape($p->retainer_status) ?>', '<?= html_escape($p->retainer_notes ?? '') ?>')"
                                            class="px-3.5 py-2 bg-brand-deep hover:bg-brand-green text-white rounded-xl text-xs font-bold transition-all shadow-xs"
                                        >
                                            Manage Retainer
                                        </button>
                                    </td>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (empty($partnerships)): ?>
                    <div class="p-12 text-center space-y-3">
                        <div class="w-16 h-16 bg-emerald-50 text-brand-green rounded-full flex items-center justify-center mx-auto text-2xl shadow-sm border border-emerald-100">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3 class="text-lg font-black text-gray-900">No Brand Partners Added Yet</h3>
                        <p class="text-xs text-gray-500 font-medium max-w-md mx-auto">
                            Qualify creators from Discovery or Pipeline to automatically assign Bronze, Silver, and Gold milestone tiers based on revenue data.
                        </p>
                        <div class="pt-2">
                            <a href="<?php echo base_url('discovery'); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-deep hover:bg-brand-green text-white font-bold text-xs rounded-xl transition-all shadow-md">
                                <i class="fas fa-search"></i> Discover Creators
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- 🏆 Set Revenue Thresholds Modal -->
    <div id="threshold-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl p-6 border border-gray-100 space-y-5">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🏆</span>
                    <h3 class="text-base font-black text-gray-900">Set Revenue Thresholds</h3>
                </div>
                <button onclick="closeThresholdModal()" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <p class="text-xs text-gray-500 font-medium">Automatic tier milestones assigned as creators hit GMV targets.</p>
            <form id="threshold-form" class="space-y-4" onsubmit="saveThresholds(event)">
                <div>
                    <label class="block text-xs font-bold text-amber-900 mb-1">🥇 Gold Milestone ($ GMV)</label>
                    <input type="number" id="thresh-gold" name="gold" value="<?= $revenue_thresholds->gold ?>" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-brand-green outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">🥈 Silver Milestone ($ GMV)</label>
                    <input type="number" id="thresh-silver" name="silver" value="<?= $revenue_thresholds->silver ?>" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-brand-green outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-amber-800 mb-1">🥉 Bronze Milestone ($ GMV)</label>
                    <input type="number" id="thresh-bronze" name="bronze" value="<?= $revenue_thresholds->bronze ?>" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-brand-green outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeThresholdModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold text-xs rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-deep hover:bg-brand-green text-white font-bold text-xs rounded-xl shadow-sm">Save Thresholds</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🤝 Manage Retainer Modal -->
    <div id="retainer-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl p-6 border border-gray-100 space-y-5">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🤝</span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Manage Partner Retainer</h3>
                        <p id="retainer-modal-subtitle" class="text-[11px] text-brand-green font-bold"></p>
                    </div>
                </div>
                <button onclick="closeRetainerModal()" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form id="retainer-form" class="space-y-4" onsubmit="saveRetainer(event)">
                <input type="hidden" id="retainer-collab-id" name="collaboration_id">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Monthly Retainer Amount ($/mo)</label>
                    <input type="number" step="0.01" id="retainer-amount-input" name="retainer_amount" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-brand-green outline-none" placeholder="e.g. 15000">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Retainer Status</label>
                    <select id="retainer-status-select" name="retainer_status" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 focus:border-brand-green outline-none">
                        <option value="Active Monthly Retainer">Active Monthly Retainer</option>
                        <option value="Retainer Recommended">Retainer Recommended</option>
                        <option value="Per-Campaign Basis">Per-Campaign Basis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Contract & Retainer Notes</label>
                    <textarea id="retainer-notes-input" name="retainer_notes" rows="3" class="w-full border border-gray-200 rounded-xl p-3 text-xs font-medium focus:border-brand-green outline-none" placeholder="Add contract terms, deliverable quotas, payment notes..."></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRetainerModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold text-xs rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-deep hover:bg-brand-green text-white font-bold text-xs rounded-xl shadow-sm">Save Retainer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📈 Full Performance History Modal -->
    <div id="performance-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-100" style="max-height: 85vh;">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-900 to-[#0F6B3E] text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img id="perf-avatar" src="" class="w-10 h-10 rounded-xl object-cover border border-white/20">
                    <div>
                        <h3 id="perf-name" class="text-sm font-black">Full Performance History</h3>
                        <p id="perf-handle" class="text-xs text-emerald-200 font-bold"></p>
                    </div>
                </div>
                <button onclick="closePerformanceModal()" class="text-white/80 hover:text-white text-lg">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto space-y-5 bg-gray-50/40">
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-2xl p-4 border border-gray-150 shadow-xs text-center">
                        <span class="text-[10px] font-extrabold text-gray-400 uppercase">GMV Generated</span>
                        <p id="perf-gmv" class="text-lg font-black text-brand-green mt-1">$0.00</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-gray-150 shadow-xs text-center">
                        <span class="text-[10px] font-extrabold text-gray-400 uppercase">Contract Value</span>
                        <p id="perf-contract" class="text-lg font-black text-gray-900 mt-1">$0.00</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-gray-150 shadow-xs text-center">
                        <span class="text-[10px] font-extrabold text-gray-400 uppercase">ROI Multiplier</span>
                        <p id="perf-roi" class="text-lg font-black text-purple-700 mt-1">0.0x</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-150 space-y-3">
                    <h4 class="text-xs font-black text-gray-800 uppercase tracking-wider">Campaign Deliverables & Retainer History</h4>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 font-bold block">Deliverables:</span>
                            <span id="perf-deliverables" class="font-bold text-gray-800"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-bold block">Community Status:</span>
                            <span id="perf-community" class="font-bold text-emerald-600"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-bold block">Retainer Status:</span>
                            <span id="perf-retainer-status" class="font-bold text-purple-700"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-bold block">Retainer Amount:</span>
                            <span id="perf-retainer-amount" class="font-bold text-gray-900"></span>
                        </div>
                    </div>
                    <div id="perf-notes-box" class="pt-2 border-t border-gray-100 hidden">
                        <span class="text-gray-400 text-[11px] font-bold block">Retainer Notes:</span>
                        <p id="perf-notes" class="text-xs text-gray-700 italic mt-0.5"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentTierFilter = 'all';
        let currentCommunityFilter = 'all';

        function applyFilters() {
            const rows = document.querySelectorAll('.partnership-row');
            rows.forEach(row => {
                const tier = row.getAttribute('data-tier');
                const comm = row.getAttribute('data-community');

                const matchTier = (currentTierFilter === 'all' || tier === currentTierFilter);
                const matchComm = (currentCommunityFilter === 'all' || comm === currentCommunityFilter);

                if (matchTier && matchComm) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function filterTier(tier) {
            currentTierFilter = tier;
            applyFilters();

            ['all', 'Gold', 'Silver', 'Bronze'].forEach(t => {
                const btn = document.getElementById(`btn-tier-${t}`);
                if (btn) {
                    if (t === tier) {
                        btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-brand-deep text-white shadow-xs";
                    } else {
                        btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200";
                    }
                }
            });
        }

        function filterCommunity(status) {
            currentCommunityFilter = status;
            applyFilters();

            ['all', 'Joined', 'Invited', 'Pending'].forEach(s => {
                const btn = document.getElementById(`btn-comm-${s}`);
                if (btn) {
                    if (s === status) {
                        btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-gray-900 text-white shadow-xs";
                    } else {
                        btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200";
                    }
                }
            });
        }

        // Threshold Modal Handlers
        function openThresholdModal() {
            document.getElementById('threshold-modal').classList.remove('hidden');
        }
        function closeThresholdModal() {
            document.getElementById('threshold-modal').classList.add('hidden');
        }
        async function saveThresholds(e) {
            e.preventDefault();
            const form = document.getElementById('threshold-form');
            const data = new FormData(form);
            try {
                const res = await fetch('<?= base_url('ambassadors/update_thresholds') ?>', {
                    method: 'POST',
                    body: data
                });
                const result = await res.json();
                if (result.status === 'success') {
                    window.location.reload();
                } else {
                    alert(result.message || 'Failed to update thresholds');
                }
            } catch(err) {
                alert('Error updating thresholds');
            }
        }

        // Retainer Modal Handlers
        function openRetainerModal(collabId, creatorName, brandName, amount, status, notes) {
            document.getElementById('retainer-collab-id').value = collabId;
            document.getElementById('retainer-modal-subtitle').textContent = `${brandName} × ${creatorName}`;
            document.getElementById('retainer-amount-input').value = amount || 0;
            document.getElementById('retainer-status-select').value = status || 'Per-Campaign Basis';
            document.getElementById('retainer-notes-input').value = notes || '';
            document.getElementById('retainer-modal').classList.remove('hidden');
        }
        function closeRetainerModal() {
            document.getElementById('retainer-modal').classList.add('hidden');
        }
        async function saveRetainer(e) {
            e.preventDefault();
            const form = document.getElementById('retainer-form');
            const data = new FormData(form);
            try {
                const res = await fetch('<?= base_url('ambassadors/update_retainer') ?>', {
                    method: 'POST',
                    body: data
                });
                const result = await res.json();
                if (result.status === 'success') {
                    window.location.reload();
                } else {
                    alert(result.message || 'Failed to update retainer');
                }
            } catch(err) {
                alert('Error updating retainer');
            }
        }

        // Community Status Handler
        async function updateCommunityStatus(collabId, newStatus) {
            const formData = new FormData();
            formData.append('collaboration_id', collabId);
            formData.append('community_status', newStatus);
            try {
                const res = await fetch('<?= base_url('ambassadors/update_community_status') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.status === 'success') {
                    const row = document.querySelector(`.partnership-row[data-tier] select[onchange*="${collabId}"]`)?.closest('tr');
                    if (row) {
                        row.setAttribute('data-community', newStatus);
                    }
                } else {
                    alert(result.message || 'Failed to update status');
                }
            } catch(err) {
                alert('Error updating community status');
            }
        }

        // Performance History Modal Handler
        async function openPerformanceModal(partnerId) {
            try {
                const res = await fetch('<?= base_url('ambassadors/get_partner_history/') ?>' + partnerId);
                const result = await res.json();
                if (result.status === 'success') {
                    const d = result.data;
                    document.getElementById('perf-name').textContent = d.creator_name + ' (' + d.platform + ')';
                    document.getElementById('perf-handle').textContent = d.handle;
                    document.getElementById('perf-avatar').src = d.profile_image || 'https://ui-avatars.com/api/?name=' + urlencode(d.creator_name);
                    document.getElementById('perf-gmv').textContent = '$' + parseFloat(d.gmv_generated).toLocaleString('en-US', {minimumFractionDigits: 2});
                    document.getElementById('perf-contract').textContent = '$' + parseFloat(d.contract_value).toLocaleString('en-US', {minimumFractionDigits: 2});
                    document.getElementById('perf-roi').textContent = d.roi_multiplier + 'x ROI';
                    document.getElementById('perf-deliverables').textContent = d.deliverables;
                    document.getElementById('perf-community').textContent = d.community_status;
                    document.getElementById('perf-retainer-status').textContent = d.retainer_status;
                    document.getElementById('perf-retainer-amount').textContent = d.retainer_amount > 0 ? '$' + parseFloat(d.retainer_amount).toLocaleString() + ' / month' : 'Per-Campaign';
                    
                    if (d.retainer_notes) {
                        document.getElementById('perf-notes').textContent = d.retainer_notes;
                        document.getElementById('perf-notes-box').classList.remove('hidden');
                    } else {
                        document.getElementById('perf-notes-box').classList.add('hidden');
                    }

                    document.getElementById('performance-modal').classList.remove('hidden');
                } else {
                    alert(result.message || 'Failed to load history');
                }
            } catch(e) {
                alert('Error loading performance history');
            }
        }
        function closePerformanceModal() {
            document.getElementById('performance-modal').classList.add('hidden');
        }
    </script>

    <!-- Client Report Modal -->
    <div id="report-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-3xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col border border-gray-150" style="max-height: 90vh;">
            
            <!-- Modal Header -->
            <div class="px-8 py-5 border-b border-gray-150 flex items-center justify-between bg-gradient-to-r from-gray-50/50 to-white">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📊</span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Client-Ready Campaign Reports</h3>
                        <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-0.5"><?= html_escape($active_brand_name) ?></p>
                    </div>
                </div>
                <button onclick="closeReportModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-250 flex items-center justify-center text-gray-400 hover:text-gray-700 transition-colors">&times;</button>
            </div>

            <!-- Modal Body (with scroll) -->
            <div class="p-8 overflow-y-auto flex-1 space-y-6 custom-scrollbar bg-gray-50/30">
                
                <!-- Filters Grid -->
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs space-y-4">
                    <h4 class="text-xs font-black text-gray-700 uppercase tracking-wider">Report Filters & Date Range</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Start Date</label>
                            <input type="date" id="report-start-date" onchange="updateReportData()" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-100 focus:border-brand-green outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">End Date</label>
                            <input type="date" id="report-end-date" onchange="updateReportData()" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-100 focus:border-brand-green outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Filter by Creator</label>
                            <select id="report-creator-filter" onchange="updateReportData()" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-emerald-100 focus:border-brand-green outline-none cursor-pointer">
                                <option value="all">All Creators</option>
                                <?php foreach ($partnerships as $p): ?>
                                    <option value="<?= $p->id ?>"><?= html_escape($p->ambassador_name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Campaign Niche</label>
                            <select id="report-campaign-filter" onchange="updateReportData()" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-emerald-100 focus:border-brand-green outline-none cursor-pointer">
                                <option value="all">All Campaigns</option>
                                <?php 
                                    $niches = array_unique(array_column($partnerships, 'brand_name'));
                                    foreach ($niches as $niche): 
                                ?>
                                    <option value="<?= html_escape($niche) ?>"><?= html_escape($niche) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Export & Share Section -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Export PDF Card -->
                    <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <h5 class="text-xs font-black text-gray-800">Export Final Deliverables</h5>
                            <p class="text-[11px] text-gray-400 font-medium mt-1">Download report as a clean PDF layout for your client call.</p>
                        </div>
                        <button onclick="exportReportPDF()" class="bg-brand-deep hover:bg-brand-green text-white font-extrabold text-xs px-4 py-2.5 rounded-xl transition-all shadow-xs flex items-center gap-2">
                            <i class="fas fa-file-pdf"></i>
                            <span>Download PDF</span>
                        </button>
                    </div>

                    <!-- Share Link Card -->
                    <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-black text-gray-800">Share Live Updating Link</h5>
                            <span class="bg-emerald-50 text-brand-green text-[9px] font-black px-1.5 py-0.5 rounded border border-emerald-100 uppercase">Live Update</span>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="report-share-url" readonly class="flex-1 bg-gray-50 border border-gray-250 text-gray-600 rounded-xl px-3 py-1.5 text-xs font-semibold select-all outline-none" />
                            <button onclick="copyShareUrl()" class="bg-gray-150 hover:bg-gray-250 text-gray-700 font-extrabold text-xs px-3 rounded-xl transition-all border border-gray-200">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Performance Report Preview -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-150 bg-gray-50/50 flex items-center justify-between">
                        <h4 class="text-xs font-black text-gray-800 uppercase tracking-wider">Report Preview</h4>
                        <span class="text-[10px] text-gray-400 font-bold">Instroom Reports</span>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Stats Grid inside Preview -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bg-gray-50/50 rounded-xl p-3 border border-gray-100">
                                <span class="text-[9px] font-extrabold text-gray-400 uppercase tracking-wider">GMV Generated</span>
                                <p id="preview-gmv" class="text-base font-black text-gray-900 mt-1">$0.00</p>
                            </div>
                            <div class="bg-gray-50/50 rounded-xl p-3 border border-gray-100">
                                <span class="text-[9px] font-extrabold text-gray-400 uppercase tracking-wider">Retainer Spend</span>
                                <p id="preview-spend" class="text-base font-black text-gray-900 mt-1">$0.00</p>
                            </div>
                            <div class="bg-gray-50/50 rounded-xl p-3 border border-gray-100">
                                <span class="text-[9px] font-extrabold text-gray-400 uppercase tracking-wider">ROI Ratio</span>
                                <p id="preview-roi" class="text-base font-black text-brand-green mt-1">0.0x</p>
                            </div>
                            <div class="bg-gray-50/50 rounded-xl p-3 border border-gray-100">
                                <span class="text-[9px] font-extrabold text-gray-400 uppercase tracking-wider">Active Creators</span>
                                <p id="preview-creators" class="text-base font-black text-gray-900 mt-1">0</p>
                            </div>
                        </div>

                        <!-- Mini Table inside Preview -->
                        <div class="border border-gray-150 rounded-xl overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-50 text-[10px] font-extrabold text-gray-400 uppercase tracking-wider border-b border-gray-150">
                                        <th class="py-2.5 px-4">Creator</th>
                                        <th class="py-2.5 px-4 text-center">Spend</th>
                                        <th class="py-2.5 px-4 text-center">GMV</th>
                                        <th class="py-2.5 px-4 text-center">ROI</th>
                                    </tr>
                                </thead>
                                <tbody id="preview-table-body" class="divide-y divide-gray-100">
                                    <!-- Dynamic rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        const partnershipsData = <?= json_encode($partnerships) ?>;
        <?php $brand_id = isset($active_brand_id) ? (int)$active_brand_id : (int)$this->session->userdata('brand_id'); ?>
        const brandId = <?= $brand_id ?>;
        const baseShareUrl = "<?= base_url('ambassadors/public_report') ?>";
        const shareToken = "<?= md5($brand_id . 'instroom-salt-2026') ?>";

        function openReportModal() {
            document.getElementById('report-modal').classList.remove('hidden');
            updateReportData();
        }

        function closeReportModal() {
            document.getElementById('report-modal').classList.add('hidden');
        }

        function updateReportData() {
            const startDate = document.getElementById('report-start-date').value;
            const endDate = document.getElementById('report-end-date').value;
            const creatorId = document.getElementById('report-creator-filter').value;
            const campaignNiche = document.getElementById('report-campaign-filter').value;

            // Filter data
            let filtered = partnershipsData;

            if (creatorId && creatorId !== 'all') {
                filtered = filtered.filter(p => p.id == creatorId);
            }

            if (campaignNiche && campaignNiche !== 'all') {
                filtered = filtered.filter(p => p.brand_name === campaignNiche);
            }

            if (startDate) {
                const startMs = new Date(startDate + 'T00:00:00').getTime();
                filtered = filtered.filter(p => {
                    const itemMs = new Date(p.created_at).getTime();
                    return itemMs >= startMs;
                });
            }

            if (endDate) {
                const endMs = new Date(endDate + 'T23:59:59').getTime();
                filtered = filtered.filter(p => {
                    const itemMs = new Date(p.created_at).getTime();
                    return itemMs <= endMs;
                });
            }

            // Calculations
            let totalGmv = 0;
            let totalSpend = 0;
            filtered.forEach(p => {
                const fee = parseFloat(p.fee > 0 ? p.fee : p.contract_value);
                totalSpend += fee;
                totalGmv += parseFloat(p.gmv_generated);
            });

            const roi = totalSpend > 0 ? (totalGmv / totalSpend).toFixed(1) : '0.0';

            // Update DOM KPIs
            document.getElementById('preview-gmv').textContent = '$' + totalGmv.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('preview-spend').textContent = '$' + totalSpend.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('preview-roi').textContent = roi + 'x ROI';
            document.getElementById('preview-creators').textContent = filtered.length;

            // Update DOM Table Preview
            const tableBody = document.getElementById('preview-table-body');
            if (filtered.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="py-6 text-center text-gray-400 font-medium">No results match filters.</td></tr>';
            } else {
                tableBody.innerHTML = filtered.map(p => {
                    const fee = parseFloat(p.fee > 0 ? p.fee : p.contract_value);
                    const indivRoi = fee > 0 ? (p.gmv_generated / fee).toFixed(1) : '0.0';
                    return `
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 px-4 font-bold text-gray-800">${p.ambassador_name}</td>
                            <td class="py-2.5 px-4 text-center font-semibold text-gray-600">$${fee.toLocaleString()}</td>
                            <td class="py-2.5 px-4 text-center font-bold text-brand-deep">$${parseFloat(p.gmv_generated).toLocaleString()}</td>
                            <td class="py-2.5 px-4 text-center font-extrabold text-purple-700">${indivRoi}x</td>
                        </tr>
                    `;
                }).join('');
            }

            // Construct and update Live Share URL
            let shareUrl = `${baseShareUrl}?brand_id=${brandId}&token=${shareToken}`;
            if (creatorId && creatorId !== 'all') shareUrl += `&creator_id=${creatorId}`;
            if (campaignNiche && campaignNiche !== 'all') shareUrl += `&campaign_niche=${encodeURIComponent(campaignNiche)}`;
            if (startDate) shareUrl += `&start_date=${startDate}`;
            if (endDate) shareUrl += `&end_date=${endDate}`;

            document.getElementById('report-share-url').value = shareUrl;
        }

        function copyShareUrl() {
            const copyText = document.getElementById("report-share-url");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            alert("Shareable link copied to clipboard!");
        }

        function exportReportPDF() {
            const shareUrl = document.getElementById('report-share-url').value;
            window.open(shareUrl + '&print=1', '_blank');
        }
    </script>
</body>
</html>
