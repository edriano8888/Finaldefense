<?php
    $display_name = isset($user_name) && $user_name ? $user_name : 'Manager';
    $total_members = isset($total_members_count) ? (int)$total_members_count : 0;
    $pending_influencers = isset($pending_influencers_count) ? (int)$pending_influencers_count : 0;
    $pending_orders = isset($pending_orders_count) ? (int)$pending_orders_count : 0;
    $active_campaigns = isset($active_campaigns_count) ? (int)$active_campaigns_count : 0;

    // Advanced analytics fallbacks
    $sales_amount = isset($analytics['sales_amount']) ? (float)$analytics['sales_amount'] : 0.0;
    $product_expenditure = isset($analytics['product_expenditure']) ? (float)$analytics['product_expenditure'] : 0.0;
    $total_views = isset($analytics['total_views']) ? (int)$analytics['total_views'] : 0;
    $avg_eng_rate = isset($analytics['avg_eng_rate']) ? (float)$analytics['avg_eng_rate'] : 0.0;

    // Safe Pipeline mapping
    $pipeline_raw = isset($analytics['pipeline_breakdown']) ? $analytics['pipeline_breakdown'] : [];
    $pipeline_data = [
        'Sourced'   => (int)($pipeline_raw['Sourced'] ?? 0),
        'Contacted' => (int)($pipeline_raw['Contacted'] ?? 0),
        'Replied'   => (int)($pipeline_raw['Replied'] ?? 0),
        'Posted'    => (int)($pipeline_raw['Posted'] ?? ($pipeline_raw['Posted_count'] ?? 0)),
        'Completed' => (int)($pipeline_raw['Campaign Completed'] ?? ($pipeline_raw['Completed_count'] ?? 0))
    ];

    // Safe Platform mapping
    $platform_raw = isset($analytics['platform_counts']) ? $analytics['platform_counts'] : [];
    $platform_labels = ['Instagram', 'TikTok', 'YouTube', 'Facebook'];
    $platform_data = [];
    foreach ($platform_labels as $p) {
        $platform_data[$p] = (int)($platform_raw[$p] ?? ($platform_raw[strtolower($p)] ?? 0));
    }
?>

<div class="space-y-8 animate-fade-in pb-16">
    <!-- Hero / Header Section -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-gradient-to-r from-[#111827] to-[#1F2937] p-8 rounded-[32px] text-white shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(31,174,91,0.15),transparent_45%)]"></div>
        <div class="min-w-0 relative z-10">
            <span class="px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-brand-green/20 text-brand-green border border-brand-green/30">Executive Overview</span>
            <h1 class="text-3xl font-extrabold tracking-tight mt-2">Welcome back, <?php echo html_escape($display_name); ?>!</h1>
            <p class="text-sm text-gray-300 mt-1">Here is a visual overview of your brand's performance metrics and outreach operations.</p>
        </div>
        <div class="flex flex-wrap gap-3 relative z-10">
            <a href="<?php echo base_url('manager/campaign_planning'); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-green text-white text-sm font-bold hover:bg-brand-deep transition-all shadow-lg shadow-green-500/25 hover:scale-[1.02]">
                <i class="fas fa-plus"></i>
                <span>New Campaign</span>
            </a>
            <a href="<?php echo base_url('manager/team_management'); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/10 text-white text-sm font-bold border border-white/10 hover:bg-white/15 transition-all hover:scale-[1.02]">
                <i class="fas fa-user-friends text-gray-300"></i>
                <span>Manage Team</span>
            </a>
        </div>
    </div>

    <!-- Core Operational Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total GMV -->
        <div class="group bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full bg-amber-50 group-hover:scale-110 transition-transform"></div>
            <div class="flex items-start justify-between relative z-10">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Sales Generated (GMV)</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">$<?php echo number_format($sales_amount, 2); ?></p>
                    <p class="text-[11px] text-gray-500 mt-1">Total revenue via campaigns</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100 group-hover:bg-amber-100 transition-colors">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Expenditure -->
        <div class="group bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full bg-indigo-50 group-hover:scale-110 transition-transform"></div>
            <div class="flex items-start justify-between relative z-10">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Product Expenditure</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">$<?php echo number_format($product_expenditure, 2); ?></p>
                    <p class="text-[11px] text-gray-500 mt-1">Total spent on samples/deals</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center border border-indigo-100 group-hover:bg-indigo-100 transition-colors">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Views -->
        <div class="group bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full bg-emerald-50 group-hover:scale-110 transition-transform"></div>
            <div class="flex items-start justify-between relative z-10">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Estimated Views</p>
                    <p class="mt-2 text-2xl font-black text-gray-900"><?php echo number_format($total_views); ?></p>
                    <p class="text-[11px] text-gray-500 mt-1">Creator video views aggregate</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-brand-green flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-100 transition-colors">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Engagement Rate -->
        <div class="group bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full bg-red-50 group-hover:scale-110 transition-transform"></div>
            <div class="flex items-start justify-between relative z-10">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Avg Engagement Rate</p>
                    <p class="mt-2 text-2xl font-black text-gray-900"><?php echo number_format($avg_eng_rate, 2); ?>%</p>
                    <p class="text-[11px] text-gray-500 mt-1">Avg interactions per follower</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center border border-red-100 group-hover:bg-red-100 transition-colors">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Action Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Pipeline Funnel Graph -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Outreach Pipeline Distribution</h3>
                <p class="text-xs text-gray-500 mt-0.5">Summary of creators at each stage of collaboration.</p>
            </div>
            <div class="mt-6 h-64 relative flex items-center justify-center">
                <canvas id="pipelineChart"></canvas>
            </div>
        </div>

        <!-- Platform Distribution Graph -->
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Platform Share</h3>
                <p class="text-xs text-gray-500 mt-0.5">Outreach distribution by social network.</p>
            </div>
            <div class="mt-6 h-64 relative flex items-center justify-center">
                <canvas id="platformChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Center and Campaigns -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Active Campaigns Panel -->
        <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Active Campaigns</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Overview of current and scheduled campaigns.</p>
                </div>
                <a href="<?php echo base_url('manager/campaign_planning'); ?>" class="text-xs font-bold text-brand-green hover:text-brand-deep transition-colors">View All</a>
            </div>

            <div class="overflow-x-auto">
                <?php if (!empty($campaigns)): ?>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="py-3 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Campaign</th>
                                <th class="py-3 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                                <th class="py-3 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Budget</th>
                                <th class="py-3 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">End Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach(array_slice($campaigns, 0, 4) as $camp): ?>
                                <tr class="hover:bg-gray-50/40 transition-colors">
                                    <td class="py-4 px-4">
                                        <a href="<?php echo base_url('manager/campaign_details/' . $camp->id); ?>" class="text-sm font-bold text-gray-900 hover:text-brand-green transition-colors">
                                            <?php echo html_escape($camp->title); ?>
                                        </a>
                                        <p class="text-xs text-gray-500 truncate max-w-[200px] mt-0.5"><?php echo html_escape($camp->description ?: 'No description provided.'); ?></p>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <?php
                                            $st_colors = [
                                                'Planning' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                'Active' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                'Ongoing' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                'Done' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'Completed' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'Failed' => 'bg-red-50 text-red-600 border-red-100'
                                            ];
                                            $badge = $st_colors[$camp->status] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                                        ?>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border <?php echo $badge; ?>">
                                            <?php echo $camp->status; ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-sm font-bold text-gray-900">
                                        $<?php echo number_format($camp->budget, 2); ?>
                                    </td>
                                    <td class="py-4 px-4 text-xs text-gray-500">
                                        <?php echo !empty($camp->end_date) ? date('M d, Y', strtotime($camp->end_date)) : 'No Date'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-10">
                        <i class="fas fa-bullseye text-2xl text-gray-200 mb-2"></i>
                        <p class="text-xs text-gray-400">No campaigns created yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex flex-col">
            <h3 class="text-base font-bold text-gray-900">Recent Activity</h3>
            <p class="text-xs text-gray-500 mt-0.5">Live operational logs from your workspace.</p>
            
            <div class="mt-6 flex-1 space-y-4">
                <?php if (!empty($recent_activities)): ?>
                    <?php foreach($recent_activities as $act): ?>
                        <?php
                            $act_lower = strtolower($act['action']);
                            $icon_class = 'fa-bell text-gray-400 bg-gray-50 border-gray-100';
                            if (strpos($act_lower, 'login') !== false) {
                                $icon_class = 'fa-sign-in-alt text-blue-500 bg-blue-50 border-blue-100';
                            } elseif (strpos($act_lower, 'add') !== false || strpos($act_lower, 'create') !== false) {
                                $icon_class = 'fa-plus text-emerald-500 bg-emerald-50 border-emerald-100';
                            } elseif (strpos($act_lower, 'delete') !== false || strpos($act_lower, 'remove') !== false) {
                                $icon_class = 'fa-trash-alt text-red-500 bg-red-50 border-red-100';
                            } elseif (strpos($act_lower, 'update') !== false || strpos($act_lower, 'edit') !== false) {
                                $icon_class = 'fa-edit text-amber-500 bg-amber-50 border-amber-100';
                            }
                        ?>
                        <div class="flex items-start gap-3 p-3 rounded-2xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center border text-xs shrink-0 <?php echo $icon_class; ?>">
                                <i class="fas <?php echo explode(' ', $icon_class)[0]; ?>"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-gray-900 truncate"><?php echo html_escape($act['action']); ?></p>
                                <p class="text-[11px] text-gray-500 truncate mt-0.5"><?php echo html_escape($act['details']); ?></p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase"><?php echo html_escape($act['user_name']); ?></span>
                                    <span class="text-[10px] text-gray-300">•</span>
                                    <span class="text-[10px] text-gray-400">
                                        <?php echo timespan(strtotime($act['created_at']), time(), 1); ?> ago
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-full py-10">
                        <i class="fas fa-history text-2xl text-gray-200 mb-2"></i>
                        <p class="text-xs text-gray-400">No activity logs recorded.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Pipeline Funnel Chart
        const pipelineCtx = document.getElementById('pipelineChart').getContext('2d');
        new Chart(pipelineCtx, {
            type: 'bar',
            data: {
                labels: ['Sourced', 'Contacted', 'Replied', 'Posted', 'Completed'],
                datasets: [{
                    label: 'Creators Count',
                    data: [
                        <?php echo $pipeline_data['Sourced']; ?>,
                        <?php echo $pipeline_data['Contacted']; ?>,
                        <?php echo $pipeline_data['Replied']; ?>,
                        <?php echo $pipeline_data['Posted']; ?>,
                        <?php echo $pipeline_data['Completed']; ?>
                    ],
                    backgroundColor: [
                        'rgba(156, 163, 175, 0.65)',  // Gray for Sourced
                        'rgba(245, 158, 11, 0.65)',   // Amber for Contacted
                        'rgba(99, 102, 241, 0.65)',   // Indigo for Replied
                        'rgba(59, 130, 246, 0.65)',   // Blue for Posted
                        'rgba(31, 174, 91, 0.65)'     // Green for Completed
                    ],
                    borderColor: [
                        '#9CA3AF',
                        '#F59E0B',
                        '#6366F1',
                        '#3B82F6',
                        '#1FAE5B'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                family: '"Plus Jakarta Sans", sans-serif',
                                size: 10
                            }
                        },
                        grid: {
                            color: '#F3F4F6'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                family: '"Plus Jakarta Sans", sans-serif',
                                size: 10,
                                weight: '600'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 2. Platform Doughnut Chart
        const platformCtx = document.getElementById('platformChart').getContext('2d');
        new Chart(platformCtx, {
            type: 'doughnut',
            data: {
                labels: ['Instagram', 'TikTok', 'YouTube', 'Facebook'],
                datasets: [{
                    data: [
                        <?php echo $platform_data['Instagram']; ?>,
                        <?php echo $platform_data['TikTok']; ?>,
                        <?php echo $platform_data['YouTube']; ?>,
                        <?php echo $platform_data['Facebook']; ?>
                    ],
                    backgroundColor: [
                        '#EC4899', // Pink (IG)
                        '#111827', // Dark/Black (TikTok)
                        '#EF4444', // Red (YouTube)
                        '#3B82F6'  // Blue (Facebook)
                    ],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                family: '"Plus Jakarta Sans", sans-serif',
                                size: 10,
                                weight: '600'
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
