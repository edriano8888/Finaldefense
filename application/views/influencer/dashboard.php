<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dashboard - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/influencer.css'); ?>" rel="stylesheet">
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
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">
    <?php
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
        $available_brands = $this->Brand_model->get_all_brands();
    ?>

    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Influencer', 'child' => 'Dashboard']); ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 md:p-8 scroll-smooth">
            <?php
                $fmt = function($n) {
                    if ($n >= 1000000) return number_format($n/1000000, 1) . 'M';
                    if ($n >= 1000)    return number_format($n/1000, 1) . 'K';
                    return number_format($n);
                };
                $month_delta = isset($new_last_month) && $new_last_month > 0
                    ? round(($new_this_month - $new_last_month) / $new_last_month * 100, 1)
                    : ($new_this_month > 0 ? 100 : 0);
                $pipeline_total_val = isset($pipeline_total) ? $pipeline_total : 0;
            ?>
            <div class="max-w-7xl mx-auto">

                <!-- ── Page Header ─────────────────────────────────────────── -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">
                            Good <?php $h=(int)date('H'); echo $h<12?'morning':($h<17?'afternoon':'evening'); ?>, <?php echo html_escape(explode(' ',$user_name??'there')[0]); ?> 👋
                        </h1>
                        <p class="text-gray-500 text-sm mt-0.5">Here's what's happening with <span class="font-semibold text-gray-700"><?php echo html_escape($active_brand_name); ?></span> today.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo base_url('influencer/manage'); ?>" class="inline-flex items-center gap-2 bg-brand-green text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-brand-deep transition-colors shadow-sm">
                            <i class="fas fa-list-ul"></i> Manage Influencers
                        </a>
                        <a href="<?php echo base_url('influencer/pipeline'); ?>" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-50 transition-colors">
                            <i class="fas fa-filter"></i> Pipeline
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Card 1: Total Influencers -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-wider">Growth</span>
                                    <?php
                                        $delta_color = $month_delta >= 0 ? 'text-green-500' : 'text-red-500';
                                        $delta_text = ($month_delta >= 0 ? '+' : '') . $month_delta . '%';
                                    ?>
                                    <span class="text-xs font-bold <?php echo $delta_color; ?> mt-1"><?php echo $delta_text; ?></span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Influencers</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?php echo isset($total_influencers) ? number_format($total_influencers) : '0'; ?></h3>
                        </div>
                    </div>

                    <!-- Card 2: Active Campaigns -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-green-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-brand-green rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-200">
                                    <i class="fas fa-rocket text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-green-600 bg-green-50 px-2 py-1 rounded-lg uppercase tracking-wider">Active</span>
                                    <span class="text-xs font-bold text-green-500 mt-1">Running</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Active Campaigns</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?php echo isset($active_campaigns_count) ? number_format($active_campaigns_count) : '0'; ?></h3>
                        </div>
                    </div>

                    <!-- Card 3: Avg Engagement -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                                    <i class="fas fa-chart-line text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg uppercase tracking-wider">Average</span>
                                    <span class="text-xs font-bold text-green-500 mt-1">Live</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Avg. Engagement</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?php echo isset($avg_engagement) ? number_format($avg_engagement, 1) . '%' : '0%'; ?></h3>
                        </div>
                    </div>

                    <!-- Card 4: Total Spend -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 bg-brand-deep rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-900/20">
                                    <i class="fas fa-wallet text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-wider">Spend</span>
                                    <span class="text-xs font-bold text-gray-500 mt-1">YTD</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Spend</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1">$<?php echo isset($total_spend) ? number_format($total_spend) : '0'; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Pipeline Summary Card -->
                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm mb-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-extrabold text-gray-900">Influencer Outreach Pipeline</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Summary of collaborator outreach stages for this brand.</p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                        <?php 
                            $key_stages = ['For Outreach', 'Contacted', 'Replied', 'In - Progress', 'Campaign Completed'];
                            foreach ($pipeline_stages as $stage): 
                                if (!in_array($stage['name'], $key_stages)) continue;
                        ?>
                            <div class="bg-gray-50/50 border border-gray-100 rounded-2xl p-4 flex flex-col justify-between hover:border-brand-green/20 hover:bg-white transition-all group">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs" style="background-color: <?php echo $stage['color']; ?>15; color: <?php echo $stage['color']; ?>">
                                        <i class="fas <?php echo $stage['icon']; ?>"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider"><?php echo $stage['percent']; ?>%</span>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" title="<?php echo html_escape($stage['name']); ?>">
                                        <?php echo html_escape($stage['name']); ?>
                                    </p>
                                    <h4 class="text-lg font-black text-gray-900 mt-0.5"><?php echo $stage['count']; ?></h4>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Activity Feed (Left 2 columns) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-white to-gray-50/50">
                                <div>
                                    <h2 class="text-xl font-extrabold text-brand-deep">Recent Activity</h2>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Real-time logs</p>
                                </div>
                                <a href="<?php echo base_url('influencer/activity_logs'); ?>" class="text-xs font-bold text-brand-green hover:text-brand-deep transition-colors bg-green-50 px-3 py-1.5 rounded-lg">View All</a>
                            </div>
                            
                            <div class="p-8">
                                <div class="space-y-8 relative">
                                    <!-- Timeline line -->
                                    <div class="absolute left-5 top-2 bottom-2 w-0.5 bg-gray-100"></div>
                                    
                                    <?php if (isset($activities) && !empty($activities)): ?>
                                        <?php foreach ($activities as $index => $activity): ?>
                                            <div class="flex items-start group relative">
                                                <div class="flex-shrink-0 relative z-10">
                                                    <div class="h-10 w-10 rounded-2xl bg-white shadow-sm border border-gray-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                                        <i class="fas <?php echo $activity['icon']; ?> text-<?php echo str_replace('brand-', '', $activity['color']); ?>-500 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-6 min-w-0 flex-1 bg-gray-50/30 rounded-2xl p-4 border border-transparent hover:border-gray-100 hover:bg-white transition-all duration-300">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <h4 class="text-sm font-extrabold text-gray-900"><?php echo $activity['title']; ?></h4>
                                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter"><?php echo $activity['time']; ?></span>
                                                    </div>
                                                    <p class="text-sm text-gray-500 leading-relaxed">
                                                        <?php echo $activity['description']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-10">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fas fa-inbox text-gray-300 text-xl"></i>
                                            </div>
                                            <p class="text-gray-400 text-sm font-medium">No recent activity found.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Quick Actions or Mini Stats -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
                            <h3 class="text-lg font-extrabold text-gray-900 mb-6">Top Platforms</h3>
                            <div class="space-y-6">
                                <?php if (!empty($platforms)): ?>
                                    <?php foreach ($platforms as $p): ?>
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 <?php
                                                if ($p['name'] === 'Instagram') echo 'bg-pink-50 text-pink-500';
                                                elseif ($p['name'] === 'TikTok') echo 'bg-gray-900 text-white border border-transparent';
                                                elseif ($p['name'] === 'YouTube') echo 'bg-red-50 text-red-500';
                                                else echo 'bg-blue-50 text-blue-500';
                                            ?>">
                                                <i class="<?php echo $p['fab'] ? 'fab' : 'fas'; ?> <?php echo $p['icon']; ?>"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-xs font-bold text-gray-700"><?php echo html_escape($p['name']); ?></span>
                                                    <span class="text-xs font-bold text-gray-900"><?php echo $p['percent']; ?>%</span>
                                                </div>
                                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full <?php
                                                        if ($p['name'] === 'Instagram') echo 'bg-pink-500';
                                                        elseif ($p['name'] === 'TikTok') echo 'bg-gray-900';
                                                        elseif ($p['name'] === 'YouTube') echo 'bg-red-500';
                                                        else echo 'bg-blue-500';
                                                    ?>" style="width: <?php echo $p['percent']; ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-xs text-gray-400 text-center py-6">No platform share data.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Unified header scripts are loaded from partials/header.php
    </script>
</body>
</html>
