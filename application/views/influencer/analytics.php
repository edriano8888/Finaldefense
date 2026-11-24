<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics | Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    },
                    boxShadow: {
                        'card': '0 2px 5px -1px rgba(0, 0, 0, 0.05), 0 1px 3px -1px rgba(0, 0, 0, 0.03)',
                        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>
    <link href="<?php echo base_url('assets/css/influencer.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">
    <?php
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
        $available_brands = $this->Brand_model->get_all_brands();
    ?>

    <!-- Sidebar -->
    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Campaigns', 'child' => 'Analytics']); ?>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F8FAFC] p-6 md:p-8 scroll-smooth relative">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-green-50/50 to-transparent -z-10"></div>
            
            <div class="max-w-7xl mx-auto relative">
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-brand-deep tracking-tight">Analytics Overview</h1>
                        <p class="text-gray-500 mt-1 font-medium">Track your campaign performance and ROI in real-time.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Live Metrics</span>
                        </div>
                        <a href="<?php echo base_url('influencer/export_analytics_pdf'); ?>" target="_blank" class="bg-brand-green hover:bg-brand-deep text-white px-5 py-2.5 rounded-xl text-sm font-extrabold shadow-md shadow-green-200 hover:shadow-lg transition-all flex items-center gap-2 group cursor-pointer">
                            <i class="fas fa-file-pdf text-white/90 group-hover:scale-110 transition-transform"></i>
                            <span>Export PDF Report</span>
                        </a>
                        <a href="<?php echo base_url('influencer/export_analytics_csv'); ?>" class="bg-white hover:bg-green-50 text-brand-green border-2 border-brand-green px-5 py-2 rounded-xl text-sm font-extrabold shadow-sm hover:shadow-md transition-all flex items-center gap-2 group cursor-pointer">
                            <i class="fas fa-file-csv group-hover:scale-110 transition-transform"></i>
                            <span>Export CSV</span>
                        </a>
                    </div>
                </div>

                <!-- Top Key Metrics (Cards) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Total Sales -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-green-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-brand-green rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-200">
                                    <i class="fas fa-dollar-sign text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-green-600 bg-green-50 px-2 py-1 rounded-lg uppercase tracking-wider">Revenue</span>
                                    <span class="text-xs font-bold text-green-500 mt-1">+12.5%</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Sales Amount</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1">$<?php echo number_format($stats['sales_amount']); ?></h3>
                        </div>
                    </div>

                    <!-- ROI -->
                    <?php 
                        $roi = ($stats['product_expenditure'] > 0) ? (($stats['sales_amount'] - $stats['product_expenditure']) / $stats['product_expenditure']) * 100 : 0;
                    ?>
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                    <i class="fas fa-chart-pie text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-wider">Efficiency</span>
                                    <span class="text-xs font-bold text-blue-500 mt-1">ROI</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Return on Investment</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?php echo number_format($roi, 1); ?>%</h3>
                        </div>
                    </div>

                    <!-- Total Influencers -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-purple-200">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-purple-600 bg-purple-50 px-2 py-1 rounded-lg uppercase tracking-wider">Network</span>
                                    <span class="text-xs font-bold text-purple-500 mt-1">Active</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Influencers</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?php echo $stats['total_influencers']; ?></h3>
                        </div>
                    </div>

                    <!-- Engagement -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                                    <i class="fas fa-eye text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg uppercase tracking-wider">Reach</span>
                                    <span class="text-xs font-bold text-amber-500 mt-1">Views</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Views Generated</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?php echo number_format($stats['total_views']); ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Growth Trend Chart -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-10 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-extrabold text-brand-deep">Influencer Growth</h2>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">New collaborations over 30 days</p>
                        </div>
                        <div class="flex items-center gap-2 bg-green-50 px-3 py-1.5 rounded-lg">
                            <span class="w-2 h-2 rounded-full bg-brand-green"></span>
                            <span class="text-[10px] font-bold text-brand-deep uppercase tracking-wider">New Profiles</span>
                        </div>
                    </div>
                    <div class="relative h-80 w-full">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>

            <!-- MAIN LAYOUT - RESTRUCTURED FOR HORIZONTAL DESIGN -->
            
            <?php
                // Dynamic helper calculations
                $total_influencers_count = max(1, (int)($stats['total_influencers'] ?? 0));
                $won_influencers_count = (int)($stats['won_count'] ?? 0);
                
                // Real engagement rate status badge
                $avg_eng = (float)($stats['avg_eng_rate'] ?? 0);
                $eng_badge_text = $avg_eng >= 5.0 ? 'High Engagement' : ($avg_eng >= 2.0 ? 'Optimal Rate' : 'Standard Rate');
                $eng_badge_color = $avg_eng >= 3.0 ? 'text-green-600' : 'text-blue-600';

                // CPM status badge
                $cpm_val = (float)($stats['cpm'] ?? 0);
                $cpm_badge_text = $cpm_val > 0 ? '$' . number_format($cpm_val, 2) . ' / 1k views' : 'Cost Efficient';

                // Expenditure & Budget calculation (Dynamic scale based on total expenditure & active influencers)
                $expenditure = (float)($stats['product_expenditure'] ?? 0);
                $estimated_budget = max(1000, $total_influencers_count * 500); // Dynamic estimated campaign budget
                $budget_percent = min(100, round(($expenditure / $estimated_budget) * 100));
                if ($budget_percent == 0 && $expenditure > 0) $budget_percent = 5;

                // Avg Sales per Won Influencer
                $sales_amount = (float)($stats['sales_amount'] ?? 0);
                $avg_sales_per_won = ($won_influencers_count > 0) ? ($sales_amount / $won_influencers_count) : 0;
                $sales_perf_percent = $sales_amount > 0 ? min(100, max(10, round(($avg_sales_per_won / max(1, $sales_amount)) * 100))) : 0;
                $sales_perf_text = $avg_sales_per_won > 0 ? ('$' . number_format($avg_sales_per_won, 2) . ' avg revenue / deal') : 'Awaiting Deals';

                // Pipeline Conversion / Campaigns Won Rate
                $conversion_rate = round(($won_influencers_count / $total_influencers_count) * 100, 1);
            ?>

            <!-- SECTION 1: Brand Awareness (Horizontal) -->
            <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-bullhorn text-brand-green"></i> Brand Awareness & Engagement
                    </h3>
                    <span class="text-xs text-gray-500 font-medium bg-gray-50 px-3 py-1 rounded-full border border-gray-100">Live Database Metrics</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Likes -->
                    <div class="p-5 bg-gradient-to-br from-pink-50 to-white rounded-xl border border-pink-100 group hover:border-pink-200 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Estimated Likes</div>
                            <div class="p-1.5 bg-pink-100 text-pink-600 rounded-lg group-hover:bg-pink-600 group-hover:text-white transition-colors">
                                <i class="fas fa-heart text-xs"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_likes'] ?? 0); ?></div>
                        <div class="mt-2 flex items-center text-xs text-pink-600 font-medium">
                            <i class="fas fa-chart-line mr-1"></i>
                            <span>Based on audience interactions</span>
                        </div>
                    </div>

                    <!-- Total Comments -->
                    <div class="p-5 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-100 group hover:border-blue-200 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Estimated Comments</div>
                            <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <i class="fas fa-comment text-xs"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_comments'] ?? 0); ?></div>
                        <div class="mt-2 flex items-center text-xs text-blue-600 font-medium">
                            <i class="fas fa-comments mr-1"></i>
                            <span>Audience conversation volume</span>
                        </div>
                    </div>

                    <!-- Avg CPM -->
                    <div class="p-5 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-100 group hover:border-purple-200 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wide">Avg CPM (Cost / 1k Views)</div>
                            <div class="p-1.5 bg-purple-100 text-purple-600 rounded-lg group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                <i class="fas fa-tag text-xs"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900">$<?php echo number_format((float)($stats['cpm'] ?? 0), 2); ?></div>
                        <div class="mt-2 flex items-center text-xs text-purple-600 font-medium">
                            <i class="fas fa-eye mr-1"></i>
                            <span><?php echo $cpm_badge_text; ?></span>
                        </div>
                    </div>

                    <!-- Avg Eng. Rate -->
                    <div class="p-5 bg-gradient-to-br from-orange-50 to-white rounded-xl border border-orange-100 group hover:border-orange-200 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wide">Avg Eng. Rate</div>
                            <div class="p-1.5 bg-orange-100 text-orange-600 rounded-lg group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <i class="fas fa-chart-bar text-xs"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format((float)($stats['avg_eng_rate'] ?? 0), 2); ?>%</div>
                        <div class="mt-2 flex items-center text-xs <?php echo $eng_badge_color; ?> font-medium">
                            <i class="fas fa-check-circle mr-1"></i>
                            <span><?php echo $eng_badge_text; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Financial Overview (Horizontal / Wide) -->
            <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-wallet text-brand-green"></i> Financial Overview & Budget Tracking
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Expenditure -->
                    <div class="p-5 bg-gray-50 rounded-xl border border-gray-100 flex flex-col justify-center">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Product Expenditure</div>
                        <div class="text-3xl font-bold text-gray-900">$<?php echo number_format($expenditure); ?></div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                            <div class="bg-brand-green h-1.5 rounded-full transition-all duration-500" style="width: <?php echo max(3, $budget_percent); ?>%"></div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2 font-medium"><?php echo $budget_percent; ?>% of estimated campaign budget used</p>
                    </div>

                    <!-- Avg Sales / Won -->
                    <div class="p-5 bg-gray-50 rounded-xl border border-gray-100 flex flex-col justify-center">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Avg Revenue / Won Creator</div>
                        <div class="text-3xl font-bold text-gray-900">$<?php echo number_format($avg_sales_per_won, 2); ?></div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                            <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" style="width: <?php echo max(5, $sales_perf_percent); ?>%"></div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2 font-medium"><?php echo $sales_perf_text; ?></p>
                    </div>

                    <!-- Campaigns Won -->
                    <div class="p-5 bg-emerald-50/60 rounded-xl border border-emerald-100 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Campaigns Won / Closed</div>
                            <div class="text-3xl font-bold text-brand-deep"><?php echo number_format($won_influencers_count); ?></div>
                            <div class="text-[10px] text-brand-green mt-2 font-bold"><?php echo $conversion_rate; ?>% Pipeline Conversion Rate</div>
                        </div>
                        <div class="h-14 w-14 bg-white rounded-2xl flex items-center justify-center shadow-sm text-brand-green">
                            <i class="fas fa-trophy text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Platform Performance (Wide) -->
            <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-brand-green"></i> Platform Performance
                    </h3>
                </div>
                
                <div class="flex flex-col lg:flex-row gap-8 items-center">
                    <!-- Chart -->
                    <div class="w-full lg:w-1/3 h-64 relative">
                        <canvas id="platformChart"></canvas>
                    </div>

                    <!-- List -->
                    <div class="w-full lg:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach($platforms as $p): 
                            $detail = $stats['platform_details'][$p];
                            $iconClass = $p == 'Facebook' ? 'fab fa-facebook text-blue-600' : ($p == 'Instagram' ? 'fab fa-instagram text-pink-600' : ($p == 'TikTok' ? 'fab fa-tiktok text-black' : 'fab fa-youtube text-red-600'));
                            $bgClass = $p == 'Facebook' ? 'bg-blue-50' : ($p == 'Instagram' ? 'bg-pink-50' : ($p == 'TikTok' ? 'bg-gray-100' : 'bg-red-50'));
                        ?>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-shadow group flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg <?php echo $bgClass; ?> flex items-center justify-center flex-shrink-0">
                                    <i class="<?php echo $iconClass; ?> text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm"><?php echo $p; ?></h4>
                                    <p class="text-[10px] text-gray-500"><?php echo $detail['total']; ?> Influencers</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900"><?php echo $detail['won']; ?> Won</div>
                                <div class="text-[10px] text-gray-500">$<?php echo number_format($detail['sales_amount']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <!-- SECTION 4: Specific Influencer Individual Report Generator -->
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-card border border-gray-100 mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-id-card text-brand-green text-xl"></i> Specific Influencer Individual Reports
                        </h3>
                        <p class="text-xs text-gray-500 font-medium mt-1">Select a creator to generate custom individual PDF & CSV analytical reports.</p>
                    </div>
                </div>

                <!-- Influencer Selector Dropdown -->
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        <div class="md:col-span-8">
                            <label for="influencer_report_select" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                <i class="fas fa-search text-brand-green mr-1"></i> Select Influencer Roster Member:
                            </label>
                            <select id="influencer_report_select" onchange="updateSelectedInfluencerReport()" class="w-full bg-white border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-800 focus:outline-none focus:border-brand-green transition-all shadow-sm">
                                <option value="">-- Choose Influencer --</option>
                                <?php if (!empty($all_influencers)): ?>
                                    <?php foreach ($all_influencers as $inf): 
                                        $inf_name = !empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : 'Influencer');
                                        $inf_user = !empty($inf->username) ? $inf->username : '-';
                                        $inf_plat = !empty($inf->platform) ? $inf->platform : '-';
                                        $inf_niche = !empty($inf->niche) ? $inf->niche : '-';
                                        $inf_loc = !empty($inf->location) ? $inf->location : '-';
                                        $inf_status = !empty($inf->pipeline_status) ? $inf->pipeline_status : 'Active';
                                    ?>
                                        <option value="<?php echo $inf->id; ?>" 
                                                data-name="<?php echo htmlspecialchars($inf_name); ?>"
                                                data-username="<?php echo htmlspecialchars($inf_user); ?>"
                                                data-platform="<?php echo htmlspecialchars($inf_plat); ?>"
                                                data-followers="<?php echo number_format((int)($inf->followers ?? 0)); ?>"
                                                data-eng="<?php echo number_format((float)($inf->eng_rate ?? 0), 2); ?>%"
                                                data-niche="<?php echo htmlspecialchars($inf_niche); ?>"
                                                data-location="<?php echo htmlspecialchars($inf_loc); ?>"
                                                data-status="<?php echo htmlspecialchars($inf_status); ?>">
                                            <?php echo htmlspecialchars($inf_name); ?> 
                                            (<?php echo ucfirst($inf_plat); ?> • <?php echo number_format((int)($inf->followers ?? 0)); ?> followers)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="md:col-span-4 flex items-end gap-2 pt-2 md:pt-6">
                            <a id="btn_export_single_pdf" href="#" target="_blank" class="flex-1 bg-brand-green hover:bg-brand-deep text-white px-4 py-3 rounded-xl text-xs font-bold shadow-md shadow-green-200 transition-all flex items-center justify-center gap-2 pointer-events-none opacity-50">
                                <i class="fas fa-file-pdf"></i>
                                <span>Export PDF</span>
                            </a>
                            <a id="btn_export_single_csv" href="#" class="flex-1 bg-white hover:bg-green-50 text-brand-green border-2 border-brand-green px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 pointer-events-none opacity-50">
                                <i class="fas fa-file-csv"></i>
                                <span>Export CSV</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Selected Influencer Live Preview Card -->
                <div id="influencer_preview_card" class="hidden bg-gradient-to-br from-emerald-50/60 via-white to-gray-50 p-6 rounded-2xl border border-emerald-100/80 shadow-sm transition-all">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-brand-green text-white flex items-center justify-center font-black text-xl shadow-lg shadow-green-200" id="preview_initials">
                                INF
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-lg font-bold text-gray-900" id="preview_name">-</h4>
                                    <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full uppercase" id="preview_platform">-</span>
                                </div>
                                <p class="text-xs font-medium text-gray-500 mt-0.5" id="preview_username">@username</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full md:w-auto">
                            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm text-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Followers</span>
                                <span class="text-sm font-extrabold text-gray-800" id="preview_followers">0</span>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm text-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Eng. Rate</span>
                                <span class="text-sm font-extrabold text-brand-green" id="preview_eng">0%</span>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm text-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Niche</span>
                                <span class="text-xs font-extrabold text-gray-700 truncate block" id="preview_niche">-</span>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm text-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Status</span>
                                <span class="text-xs font-extrabold text-blue-600" id="preview_status">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: Influencers Performance Directory & Action Reports Table -->
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-card border border-gray-100 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-list-check text-brand-green"></i> Influencer Reports Directory
                        </h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Quickly generate individual reports directly from your influencer list.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="pb-3">Influencer</th>
                                <th class="pb-3">Platform</th>
                                <th class="pb-3">Followers</th>
                                <th class="pb-3">Eng. Rate</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Individual Export Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs font-medium">
                            <?php if (!empty($all_influencers)): ?>
                                <?php foreach (array_slice($all_influencers, 0, 10) as $inf): 
                                    $t_name = !empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : 'Influencer');
                                    $t_user = !empty($inf->username) ? ltrim($inf->username, '@') : '-';
                                    $t_plat = !empty($inf->platform) ? $inf->platform : 'Social';
                                    $t_status = !empty($inf->pipeline_status) ? $inf->pipeline_status : 'Active';
                                ?>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 font-bold text-gray-900">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-[10px]">
                                                    <?php echo strtoupper(substr($t_name, 0, 2)); ?>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-800"><?php echo htmlspecialchars($t_name); ?></div>
                                                    <div class="text-[10px] text-gray-400">@<?php echo htmlspecialchars($t_user); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-gray-100 text-gray-700">
                                                <?php echo ucfirst($t_plat); ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 font-bold text-gray-800"><?php echo number_format((int)($inf->followers ?? 0)); ?></td>
                                        <td class="py-3.5 font-bold text-brand-green"><?php echo number_format((float)($inf->eng_rate ?? 0), 2); ?>%</td>
                                        <td class="py-3.5">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700">
                                                <?php echo htmlspecialchars($t_status); ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-right space-x-1">
                                            <a href="<?php echo base_url('influencer/export_influencer_pdf?influencer_id=' . $inf->id); ?>" target="_blank" title="Export PDF" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                            <a href="<?php echo base_url('influencer/export_single_influencer_csv?influencer_id=' . $inf->id); ?>" title="Export CSV" class="inline-flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors">
                                                <i class="fas fa-file-csv"></i> CSV
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-400 font-bold">No influencers found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </main>
    </div>

    <!-- Charts Script -->
    <script>
        // Prepare Data
        const growthData = <?php echo json_encode($growth_trend); ?>;
        const platformData = <?php echo json_encode($stats['platform_counts']); ?>;
        
        // Process Growth Data
        const growthLabels = Object.keys(growthData);
        const growthValues = Object.values(growthData);

        // Process Platform Data
        const platformLabels = Object.keys(platformData);
        const platformValues = Object.values(platformData);
        
        // Color mapping for known platforms
        const colorMap = {
            'Facebook': '#3B82F6', // Blue
            'Instagram': '#EC4899', // Pink
            'TikTok': '#000000', // Black
            'YouTube': '#EF4444', // Red
            'LinkedIn': '#0A66C2', // LinkedIn Blue
            'Twitter': '#1DA1F2', // Twitter Blue
            'Pinterest': '#E60023' // Pinterest Red
        };
        
        // Generate colors for platforms
        const platformColors = platformLabels.map(label => {
            return colorMap[label] || '#' + Math.floor(Math.random()*16777215).toString(16); // Fallback to random color
        });

        // Chart Global Defaults
        Chart.defaults.font.family = '"Plus Jakarta Sans", sans-serif';
        Chart.defaults.color = '#6B7280';

        // 1. Growth Line Chart
        const ctxGrowth = document.getElementById('growthChart').getContext('2d');
        const gradientGrowth = ctxGrowth.createLinearGradient(0, 0, 0, 400);
        gradientGrowth.addColorStop(0, 'rgba(31, 174, 91, 0.2)');
        gradientGrowth.addColorStop(1, 'rgba(31, 174, 91, 0)');

        new Chart(ctxGrowth, {
            type: 'line',
            data: {
                labels: growthLabels,
                datasets: [{
                    label: 'New Influencers',
                    data: growthValues,
                    borderColor: '#1FAE5B',
                    backgroundColor: gradientGrowth,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#1FAE5B',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#111827',
                        bodyColor: '#4B5563',
                        borderColor: '#E5E7EB',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' New Profiles';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#F3F4F6' },
                        ticks: { padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { padding: 10, maxTicksLimit: 10 }
                    }
                }
            }
        });

        // 2. Platform Doughnut Chart
        const ctxPlatform = document.getElementById('platformChart').getContext('2d');
        new Chart(ctxPlatform, {
            type: 'doughnut',
            data: {
                labels: platformLabels,
                datasets: [{
                    data: platformValues,
                    backgroundColor: platformColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { usePointStyle: true, boxWidth: 6, padding: 20, font: { size: 11 } }
                    }
                }
            }
        });
        // 3. Specific Influencer Report Selector Handler
        function updateSelectedInfluencerReport() {
            const select = document.getElementById('influencer_report_select');
            const option = select.options[select.selectedIndex];
            const card = document.getElementById('influencer_preview_card');
            const pdfBtn = document.getElementById('btn_export_single_pdf');
            const csvBtn = document.getElementById('btn_export_single_csv');

            if (!select.value) {
                card.classList.add('hidden');
                pdfBtn.classList.add('pointer-events-none', 'opacity-50');
                csvBtn.classList.add('pointer-events-none', 'opacity-50');
                pdfBtn.href = '#';
                csvBtn.href = '#';
                return;
            }

            const name = option.getAttribute('data-name');
            const username = option.getAttribute('data-username');
            const platform = option.getAttribute('data-platform');
            const followers = option.getAttribute('data-followers');
            const eng = option.getAttribute('data-eng');
            const niche = option.getAttribute('data-niche');
            const status = option.getAttribute('data-status');

            document.getElementById('preview_name').textContent = name;
            document.getElementById('preview_username').textContent = '@' + username.replace(/^@/, '');
            document.getElementById('preview_platform').textContent = platform;
            document.getElementById('preview_followers').textContent = followers;
            document.getElementById('preview_eng').textContent = eng;
            document.getElementById('preview_niche').textContent = niche;
            document.getElementById('preview_status').textContent = status;
            document.getElementById('preview_initials').textContent = name.substring(0, 2).toUpperCase();

            const baseUrl = '<?php echo base_url(); ?>';
            pdfBtn.href = baseUrl + 'influencer/export_influencer_pdf?influencer_id=' + select.value;
            csvBtn.href = baseUrl + 'influencer/export_single_influencer_csv?influencer_id=' + select.value;

            pdfBtn.classList.remove('pointer-events-none', 'opacity-50');
            csvBtn.classList.remove('pointer-events-none', 'opacity-50');
            card.classList.remove('hidden');
        }
    </script>
</body>
</html>
