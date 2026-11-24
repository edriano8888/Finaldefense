<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Summary Executive Report - <?php echo $report_date; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
                            dark: '#0A2E1C',
                            charcoal: '#1E293B',
                            lightbg: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>
    
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 font-sans py-8 px-4 sm:px-6">

    <!-- Floating Top Toolbar (Hidden during PDF render / Print) -->
    <div class="no-print standalone-toolbar max-w-[794px] mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?php echo base_url('manager/reports'); ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Reports Center
            </a>
            <span class="text-xs font-semibold text-slate-500">Executive Performance Report (PDF Ready)</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Performance_Report_<?php echo date('Ymd'); ?>.pdf', true, this)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <i class="fas fa-print"></i> Print Clean PDF
            </button>
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Performance_Report_<?php echo date('Ymd'); ?>.pdf', false, this)" class="px-5 py-2.5 bg-brand-green hover:bg-brand-deep text-white rounded-xl text-xs font-extrabold transition-all flex items-center gap-2 shadow-md shadow-green-200 cursor-pointer">
                <i class="fas fa-file-pdf text-sm"></i> Download Executive PDF
            </button>
        </div>
    </div>

    <!-- Main PDF Report Container (Explicit 794px width for exact 100% A4 PDF fit) -->
    <div id="reportContainer" class="print-page w-[794px] mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A2E1C] via-[#0F6B3E] to-[#1FAE5B] p-6 md:p-8 text-white relative overflow-hidden print-section">
            <div class="flex flex-row justify-between items-center gap-4 relative z-10 mb-6 border-b border-white/20 pb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl border border-white/20 flex items-center justify-center backdrop-blur-md">
                        <i class="fas fa-chart-pie text-xl text-white"></i>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">INSTROOM</span>
                        <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Influencer Marketing Intelligence</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-white/15 backdrop-blur-md border border-white/25 text-white rounded-full text-[10px] font-extrabold uppercase tracking-wider mb-1">Executive Performance Report</span>
                    <p class="text-[11px] text-emerald-100 font-medium">Generated: <?php echo $report_date; ?></p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3 pt-1 relative z-10">
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Brand</p>
                    <p class="text-xs font-bold text-white truncate"><?php echo html_escape($brand_name); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Prepared By</p>
                    <p class="text-xs font-bold text-white truncate"><?php echo html_escape($user_name); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Total Influencers</p>
                    <p class="text-xs font-bold text-white"><?php echo number_format($analytics['total_influencers']); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Total Sales (GMV)</p>
                    <p class="text-xs font-extrabold text-emerald-300">$<?php echo number_format($analytics['sales_amount'], 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Body Content -->
        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Section 1: Core Performance Metrics -->
            <div class="print-section space-y-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                    <i class="fas fa-tachometer-alt text-brand-green"></i> Key Operational Benchmarks
                </h3>

                <div class="grid grid-cols-4 gap-3">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Views</p>
                        <p class="text-base font-black text-slate-900 mt-1"><?php echo number_format($analytics['total_views']); ?></p>
                        <p class="text-[10px] text-slate-500">Video Impressions</p>
                    </div>
                    <div class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-200">
                        <p class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider">Avg Eng. Rate</p>
                        <p class="text-base font-black text-emerald-800 mt-1"><?php echo number_format($analytics['avg_eng_rate'], 2); ?>%</p>
                        <p class="text-[10px] text-emerald-600">Cross-Platform</p>
                    </div>
                    <div class="bg-blue-50 p-3.5 rounded-xl border border-blue-200">
                        <p class="text-[9px] font-bold text-blue-700 uppercase tracking-wider">Total Investment</p>
                        <p class="text-base font-black text-blue-800 mt-1">$<?php echo number_format($analytics['product_expenditure'], 2); ?></p>
                        <p class="text-[10px] text-blue-600">Product & Fees</p>
                    </div>
                    <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200">
                        <p class="text-[9px] font-bold text-amber-700 uppercase tracking-wider">Average CPM</p>
                        <p class="text-base font-black text-amber-800 mt-1">$<?php echo number_format($analytics['cpm'], 2); ?></p>
                        <p class="text-[10px] text-amber-600">Per 1,000 Views</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Platform Breakdown Table -->
            <div class="print-section space-y-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                    <i class="fas fa-layer-group text-emerald-600"></i> Platform Distribution & Sales Conversion
                </h3>

                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse table-fixed">
                        <thead>
                            <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3 w-[25%] whitespace-nowrap">Platform</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Total Sourced</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Responded</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Ambassadors</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Posted</th>
                                <th class="p-3 text-right w-[15%] whitespace-nowrap">Sales (GMV)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700 font-medium text-[11px]">
                            <?php if (!empty($analytics['platform_details'])): ?>
                                <?php foreach ($analytics['platform_details'] as $platform => $stats): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="p-2.5 font-bold text-slate-900 flex items-center gap-2">
                                            <?php 
                                                $icon = 'fa-globe';
                                                if (strtolower($platform) === 'instagram') $icon = 'fa-instagram text-pink-600';
                                                if (strtolower($platform) === 'tiktok') $icon = 'fa-tiktok text-slate-900';
                                                if (strtolower($platform) === 'youtube') $icon = 'fa-youtube text-red-600';
                                            ?>
                                            <i class="fab <?php echo $icon; ?>"></i>
                                            <span class="capitalize"><?php echo html_escape($platform); ?></span>
                                        </td>
                                        <td class="p-2.5 text-center font-bold text-slate-900"><?php echo number_format($stats['total']); ?></td>
                                        <td class="p-2.5 text-center text-blue-600 font-bold"><?php echo number_format($stats['responded']); ?></td>
                                        <td class="p-2.5 text-center text-emerald-600 font-bold"><?php echo number_format($stats['won']); ?></td>
                                        <td class="p-2.5 text-center text-purple-600 font-bold"><?php echo number_format($stats['posted']); ?></td>
                                        <td class="p-2.5 text-right font-black text-slate-900">$<?php echo number_format($stats['sales_amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer & Signoff -->
            <div class="print-section pt-6 border-t border-slate-200 text-xs text-slate-500 flex flex-row justify-between items-center gap-4">
                <div>
                    <p class="font-bold text-slate-800">CONFIDENTIAL EXECUTIVE REPORT</p>
                    <p class="mt-0.5 text-[11px]">Prepared for <?php echo html_escape($brand_name); ?> Executive Leadership.</p>
                </div>
                <div class="text-right text-[11px]">
                    <p class="font-semibold text-slate-600">Report Status: Certified Audit</p>
                    <p class="text-slate-400 mt-0.5">System Date: <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>

        </div>
    </div>

    <!-- Isolated Clean Rendering Engine for 100% Full-Width PDF Output -->
    <?php $this->load->view("reports/_pdf_engine"); ?>
</body>
</html>
