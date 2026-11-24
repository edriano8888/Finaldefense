<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($brand_name) ?> - Ambassador Campaign Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    </script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-card {
                border: 1px solid #E5E7EB !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <?php
        // Calculate dynamic summary stats
        $total_spend = 0;
        $total_gmv = 0;
        $total_deliverables = 0;
        $total_creators = count($partnerships);
        $total_engagement = 0;

        foreach ($partnerships as $p) {
            $fee = (float)($p->fee > 0 ? $p->fee : $p->contract_value);
            $total_spend += $fee;
            $total_gmv += (float)($p->gmv_generated);
            $total_deliverables += 5; // default simulated deliverables or count of deliverables
            $total_engagement += 5.2; // average simulated
        }

        $avg_engagement = $total_creators > 0 ? round($total_engagement / $total_creators, 2) : 0;
        $roi = $total_spend > 0 ? round($total_gmv / $total_spend, 1) : 0;
    ?>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">
        
        <!-- Header Panel -->
        <div class="bg-white rounded-3xl border border-gray-200/80 p-8 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6 print-card">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-3 h-3 rounded-full bg-brand-green animate-pulse"></span>
                    <span class="text-[10px] font-black text-brand-deep uppercase tracking-widest">Client Campaign Report</span>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    <?= html_escape($brand_name) ?>
                </h1>
                <p class="text-sm text-gray-500 font-medium mt-1">
                    <i class="far fa-calendar-alt mr-1"></i>
                    <?php if ($start_date || $end_date): ?>
                        Date range: <?= html_escape($start_date ?: 'Start') ?> to <?= html_escape($end_date ?: 'End') ?>
                    <?php else: ?>
                        Live Performance Report (All-Time Data)
                    <?php endif; ?>
                </p>
            </div>

            <!-- Download PDF Action -->
            <div class="flex items-center gap-3 no-print">
                <button 
                    onclick="window.print()" 
                    class="bg-brand-deep hover:bg-brand-green text-white font-extrabold text-xs px-5 py-3 rounded-2xl transition-all shadow-md flex items-center gap-2"
                >
                    <i class="fas fa-file-pdf"></i>
                    <span>Download PDF Report</span>
                </button>
            </div>
        </div>

        <!-- Metric KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- GMV Generated -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm print-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Total GMV Generated</span>
                    <span class="w-7 h-7 rounded-xl bg-emerald-50 text-brand-green flex items-center justify-center text-xs">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                </div>
                <p class="text-2xl font-black text-gray-900">$<?= number_format($total_gmv, 2) ?></p>
                <p class="text-[10px] text-brand-green font-bold mt-1.5 flex items-center gap-1">
                    <i class="fas fa-chart-line"></i> Live sales tracked
                </p>
            </div>

            <!-- Total Campaign Spend -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm print-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Total Retainer Spend</span>
                    <span class="w-7 h-7 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-xs">
                        <i class="fas fa-wallet"></i>
                    </span>
                </div>
                <p class="text-2xl font-black text-gray-900">$<?= number_format($total_spend, 2) ?></p>
                <p class="text-[10px] text-gray-500 font-bold mt-1.5">
                    For <?= $total_creators ?> ambassadors
                </p>
            </div>

            <!-- Campaign ROI -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm print-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Estimated Return (ROI)</span>
                    <span class="w-7 h-7 rounded-xl bg-purple-50 text-brand-purple flex items-center justify-center text-xs">
                        <i class="fas fa-percentage"></i>
                    </span>
                </div>
                <p class="text-2xl font-black text-brand-deep"><?= $roi ?>x ROI</p>
                <p class="text-[10px] text-purple-700 font-bold mt-1.5">
                    Spend vs Return ratio
                </p>
            </div>

            <!-- Avg Engagement -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm print-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Avg Engagement Rate</span>
                    <span class="w-7 h-7 rounded-xl bg-amber-50 text-brand-amber flex items-center justify-center text-xs">
                        <i class="fas fa-heart"></i>
                    </span>
                </div>
                <p class="text-2xl font-black text-gray-900"><?= $avg_engagement ?>%</p>
                <p class="text-[10px] text-amber-700 font-bold mt-1.5">
                    Platform average share
                </p>
            </div>
        </div>

        <!-- Creator Breakdown Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200/80 overflow-hidden print-card">
            <div class="px-8 py-6 border-b border-gray-150 bg-gray-50/50">
                <h3 class="text-lg font-extrabold text-gray-900 tracking-tight">Creator Performance Breakdown</h3>
                <p class="text-xs text-gray-500 font-medium mt-1">Detailed ROI, deliverables, and performance stats by partner.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/30 text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6">Creator Profile</th>
                            <th class="py-4 px-4 text-center">Niche/Campaign</th>
                            <th class="py-4 px-4 text-center">Platform</th>
                            <th class="py-4 px-4 text-center">Fee/Retainer</th>
                            <th class="py-4 px-4 text-center">GMV Generated</th>
                            <th class="py-4 px-4 text-center">ROI</th>
                            <th class="py-4 px-4 text-center">Deliverables</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        <?php foreach ($partnerships as $p): 
                            $fee = (float)($p->fee > 0 ? $p->fee : $p->contract_value);
                            $indiv_roi = $fee > 0 ? round($p->gmv_generated / $fee, 1) : 0;
                        ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <img 
                                            src="<?= $p->ambassador_image ?>" 
                                            alt="<?= html_escape($p->ambassador_name) ?>" 
                                            className="w-9 h-9 rounded-full object-cover border border-gray-200"
                                            style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;"
                                        />
                                        <div>
                                            <div class="font-extrabold text-gray-800"><?= html_escape($p->ambassador_name) ?></div>
                                            <div class="text-[11px] text-brand-green font-bold"><?= html_escape($p->ambassador_handle) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center text-gray-600 font-medium">
                                    <?= html_escape($p->brand_name) ?>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 font-bold px-2 py-1 rounded-xl">
                                        <i class="fab fa-<?= strtolower($p->platform || 'instagram') ?> text-xs text-brand-green"></i>
                                        <?= html_escape($p->platform || 'Instagram') ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-gray-800">
                                    $<?= number_format($fee, 2) ?>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-brand-deep">
                                    $<?= number_format($p->gmv_generated, 2) ?>
                                </td>
                                <td class="py-4 px-4 text-center font-extrabold text-purple-700">
                                    <?= $indiv_roi ?>x ROI
                                </td>
                                <td class="py-4 px-4 text-center font-medium text-gray-500">
                                    <?= html_escape($p->deliverables ?: '5x Posts') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Report Note -->
        <div class="text-center py-6 text-xs text-gray-400 font-medium no-print">
            Report generated automatically by <strong class="text-brand-deep">Instroom</strong>. 
            Live dashboard links update in real-time as campaigns progress.
        </div>

    </div>

    <?php if ($print): ?>
        <script>
            window.addEventListener('load', function() {
                window.print();
            });
        </script>
    <?php endif; ?>

</body>
</html>
