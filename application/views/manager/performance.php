<?php
    $total_influencers = $analytics['total_influencers'] ?? 0;
    $sales_amount = $analytics['sales_amount'] ?? 0;
    $total_views = $analytics['total_views'] ?? 0;
    $avg_eng_rate = $analytics['avg_eng_rate'] ?? 0;
    $outreach_count = $analytics['outreach_count'] ?? 0;
    $won_count = $analytics['won_count'] ?? 0;
    $responded_count = $analytics['responded_count'] ?? 0;
    $posted_count = $analytics['posted_count'] ?? 0;
    $cpm = $analytics['cpm'] ?? 0;
    $product_expenditure = $analytics['product_expenditure'] ?? 0;

    // Rates
    $response_rate = ($outreach_count > 0) ? ($responded_count / $outreach_count) * 100 : 0;
    $won_rate = ($outreach_count > 0) ? ($won_count / $outreach_count) * 100 : 0;
    
    // Prepare Pipeline Data for Chart
    $pipeline_labels = array_keys($analytics['pipeline_breakdown'] ?? []);
    $pipeline_data = array_values($analytics['pipeline_breakdown'] ?? []);
    
    // Prepare Platform Data for Chart
    $platform_labels = array_keys($analytics['platform_counts'] ?? []);
    $platform_values = array_values($analytics['platform_counts'] ?? []);
?>

<div class="space-y-8 pb-12">
    <!-- Notifications -->
    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg shadow-sm mb-6">
            <p class="text-sm font-medium"><?php echo $this->session->flashdata('success'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Performance Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Real-time insights into Armful Media's influencer marketing performance.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-sync-alt mr-2 text-gray-400"></i> Refresh
            </button>
            <div class="h-8 w-px bg-gray-200 mx-1"></div>
            <form action="<?php echo base_url('manager/performance'); ?>" method="GET" id="periodForm">
                <select name="period" onchange="document.getElementById('periodForm').submit()" class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold text-gray-700 outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green shadow-sm cursor-pointer">
                    <option value="30" <?php echo (isset($current_period) && $current_period == '30') ? 'selected' : ''; ?>>Last 30 Days</option>
                    <option value="90" <?php echo (isset($current_period) && $current_period == '90') ? 'selected' : ''; ?>>Last Quarter</option>
                    <option value="365" <?php echo (isset($current_period) && $current_period == '365') ? 'selected' : ''; ?>>Last Year</option>
                    <option value="all" <?php echo (isset($current_period) && $current_period == 'all') ? 'selected' : ''; ?>>All Time</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Main KPIs Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Sales -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-dollar-sign text-5xl text-brand-green"></i>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total GMV</p>
            <h3 class="text-3xl font-extrabold text-gray-900">$<?php echo number_format($sales_amount, 2); ?></h3>
            <div class="mt-4 flex items-center text-xs font-medium text-emerald-600">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>Brand Revenue</span>
            </div>
        </div>

        <!-- Views -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-eye text-5xl text-blue-500"></i>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Views</p>
            <h3 class="text-3xl font-extrabold text-gray-900"><?php echo number_format($total_views); ?></h3>
            <div class="mt-4 flex items-center text-xs font-medium text-blue-600">
                <i class="fas fa-chart-line mr-1"></i>
                <span>Estimated Reach</span>
            </div>
        </div>

        <!-- Eng Rate -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-bolt text-5xl text-amber-500"></i>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Avg. Eng. Rate</p>
            <h3 class="text-3xl font-extrabold text-gray-900"><?php echo number_format($avg_eng_rate, 2); ?>%</h3>
            <div class="mt-4 flex items-center text-xs font-medium text-amber-600">
                <i class="fas fa-heart mr-1"></i>
                <span>Engagement Power</span>
            </div>
        </div>

        <!-- Outreach -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-paper-plane text-5xl text-purple-500"></i>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Outreach</p>
            <h3 class="text-3xl font-extrabold text-gray-900"><?php echo number_format($outreach_count); ?></h3>
            <div class="mt-4 flex items-center text-xs font-medium text-purple-600">
                <i class="fas fa-users mr-1"></i>
                <span>Campaign Size</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Pipeline Breakdown -->
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h4 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-filter text-brand-green"></i> Pipeline Funnel
                </h4>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Influencer Lifecycle</span>
            </div>
            <div class="h-[300px]">
                <canvas id="pipelineChart"></canvas>
            </div>
        </div>

        <!-- Platform Distribution -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h4 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-share-alt text-brand-green"></i> Platforms
                </h4>
            </div>
            <div class="h-[240px] flex items-center justify-center">
                <canvas id="platformChart"></canvas>
            </div>
            <div class="mt-6 space-y-2">
                <?php foreach($analytics['platform_counts'] as $platform => $count): ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <?php 
                                $icon = 'fa-share-alt';
                                $color = 'text-gray-400';
                                if($platform == 'Instagram') { $icon = 'fa-instagram'; $color = 'text-pink-500'; }
                                if($platform == 'TikTok') { $icon = 'fa-tiktok'; $color = 'text-black'; }
                                if($platform == 'YouTube') { $icon = 'fa-youtube'; $color = 'text-red-500'; }
                                if($platform == 'Facebook') { $icon = 'fa-facebook'; $color = 'text-blue-600'; }
                            ?>
                            <i class="fab <?php echo $icon . ' ' . $color; ?> w-4"></i>
                            <?php echo $platform; ?>
                        </span>
                        <span class="font-bold text-gray-900"><?php echo $count; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-emerald-50 p-6 rounded-3xl border border-emerald-100">
            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Total Expenditure</p>
            <h4 class="text-2xl font-bold text-emerald-900">$<?php echo number_format($product_expenditure, 2); ?></h4>
            <p class="text-[10px] text-emerald-600/70 mt-1 italic">Based on product cost</p>
        </div>
        <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100">
            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">Average CPM</p>
            <h4 class="text-2xl font-bold text-blue-900">$<?php echo number_format($cpm, 2); ?></h4>
            <p class="text-[10px] text-blue-600/70 mt-1 italic">Cost per 1,000 views</p>
        </div>
        <div class="bg-purple-50 p-6 rounded-3xl border border-purple-100">
            <p class="text-[10px] font-bold text-purple-600 uppercase tracking-widest mb-1">Response Rate</p>
            <h4 class="text-2xl font-bold text-purple-900"><?php echo number_format($response_rate, 1); ?>%</h4>
            <div class="w-full bg-purple-200 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-purple-600 h-full" style="width: <?php echo $response_rate; ?>%"></div>
            </div>
        </div>
        <div class="bg-amber-50 p-6 rounded-3xl border border-amber-100">
            <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1">Won Rate (ROI)</p>
            <h4 class="text-2xl font-bold text-amber-900"><?php echo number_format($won_rate, 1); ?>%</h4>
            <div class="w-full bg-amber-200 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-amber-600 h-full" style="width: <?php echo $won_rate; ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Detailed Platform Table -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
            <h4 class="font-bold text-gray-900">Platform Performance Breakdown</h4>
            <div class="flex gap-2">
                <span class="px-2 py-1 bg-gray-100 rounded text-[10px] font-bold text-gray-500">REAL-TIME DATA</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Platform</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Total</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Responded</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Won</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Posted</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Revenue (GMV)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach($analytics['platform_details'] as $platform => $stats): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <?php 
                                    $icon = 'fa-share-alt';
                                    $color = 'bg-gray-100 text-gray-500';
                                    if($platform == 'Instagram') { $icon = 'fa-instagram'; $color = 'bg-pink-50 text-pink-600'; }
                                    if($platform == 'TikTok') { $icon = 'fa-tiktok'; $color = 'bg-gray-900 text-white'; }
                                    if($platform == 'YouTube') { $icon = 'fa-youtube'; $color = 'bg-red-50 text-red-600'; }
                                    if($platform == 'Facebook') { $icon = 'fa-facebook'; $color = 'bg-blue-50 text-blue-600'; }
                                ?>
                                <div class="w-8 h-8 rounded-lg <?php echo $color; ?> flex items-center justify-center">
                                    <i class="fab <?php echo $icon; ?> text-sm"></i>
                                </div>
                                <span class="font-bold text-gray-900"><?php echo $platform; ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center font-semibold text-gray-700"><?php echo number_format($stats['total']); ?></td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-bold"><?php echo number_format($stats['responded']); ?></span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold"><?php echo number_format($stats['won']); ?></span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold"><?php echo number_format($stats['posted']); ?></span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="font-bold text-gray-900">$<?php echo number_format($stats['sales_amount'], 2); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pipeline Chart
    const ctxPipeline = document.getElementById('pipelineChart').getContext('2d');
    new Chart(ctxPipeline, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($pipeline_labels); ?>,
            datasets: [{
                label: 'Influencers',
                data: <?php echo json_encode($pipeline_data); ?>,
                backgroundColor: 'rgba(31, 174, 91, 0.2)',
                borderColor: '#1FAE5B',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: '#1FAE5B'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: { font: { weight: 'bold' } }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { weight: 'bold' } }
                }
            }
        }
    });

    // Platform Chart
    const ctxPlatform = document.getElementById('platformChart').getContext('2d');
    const platformLabels = <?php echo json_encode($platform_labels); ?>;
    const platformValues = <?php echo json_encode($platform_values); ?>;
    const colorMap = {
        'Instagram': '#E1306C',
        'TikTok': '#000000',
        'YouTube': '#FF0000',
        'Facebook': '#1877F2',
        'LinkedIn': '#0A66C2',
        'Twitter': '#1DA1F2'
    };
    const platformColors = platformLabels.map(label => colorMap[label] || '#6366F1');

    new Chart(ctxPlatform, {
        type: 'doughnut',
        data: {
            labels: platformLabels,
            datasets: [{
                data: platformValues,
                backgroundColor: platformColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
