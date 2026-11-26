<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instroom Analytics Executive Report - <?php echo $report_date; ?></title>
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

    <!-- Top Floating Toolbar (Hidden during Print / PDF render) -->
    <div class="no-print standalone-toolbar max-w-[794px] mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?php echo base_url('influencer/analytics'); ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Analytics
            </a>
            <span class="text-xs font-semibold text-slate-500">Analytics Executive Report (PDF Ready)</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Analytics_Report_<?php echo date('Ymd'); ?>.pdf', true, this)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <i class="fas fa-print"></i> Print Clean PDF
            </button>
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Analytics_Report_<?php echo date('Ymd'); ?>.pdf', false, this)" class="px-5 py-2.5 bg-brand-green hover:bg-brand-deep text-white rounded-xl text-xs font-extrabold transition-all flex items-center gap-2 shadow-md shadow-green-200 cursor-pointer">
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
                        <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom Logo" class="w-8 h-8 object-contain" crossorigin="anonymous">
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">INSTROOM</span>
                        <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Media Intelligence System</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-white/15 backdrop-blur-md border border-white/25 text-white rounded-full text-[10px] font-extrabold uppercase tracking-wider mb-1">Executive Analytics Report</span>
                    <p class="text-[11px] text-emerald-100 font-medium">Generated: <?php echo $report_date; ?></p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3 pt-1 relative z-10">
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Brand</p>
                    <p class="text-xs font-bold text-white truncate"><?php echo html_escape($active_brand_name); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Prepared By</p>
                    <p class="text-xs font-bold text-white truncate"><?php echo html_escape($user_name); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Total Influencers</p>
                    <p class="text-xs font-bold text-white"><?php echo number_format($stats['total_influencers']); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-200 font-bold">Total Sales (GMV)</p>
                    <p class="text-xs font-extrabold text-emerald-300">$<?php echo number_format($stats['sales_amount'], 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Body Content -->
        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Section 1: KPI Grid -->
            <div class="print-section space-y-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                    <i class="fas fa-layer-group text-brand-green"></i> Key Marketing Metrics
                </h3>

                <div class="grid grid-cols-4 gap-3">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Views</p>
                        <p class="text-base font-black text-slate-900 mt-1"><?php echo number_format($stats['total_views']); ?></p>
                    </div>
                    <div class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-200">
                        <p class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider">Avg Eng. Rate</p>
                        <p class="text-base font-black text-emerald-800 mt-1"><?php echo number_format($stats['avg_eng_rate'], 2); ?>%</p>
                    </div>
                    <div class="bg-blue-50 p-3.5 rounded-xl border border-blue-200">
                        <p class="text-[9px] font-bold text-blue-700 uppercase tracking-wider">Total Product Cost</p>
                        <p class="text-base font-black text-blue-800 mt-1">$<?php echo number_format($stats['product_expenditure'], 2); ?></p>
                    </div>
                    <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200">
                        <p class="text-[9px] font-bold text-amber-700 uppercase tracking-wider">Average CPM</p>
                        <p class="text-base font-black text-amber-800 mt-1">$<?php echo number_format($stats['cpm'], 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Top Creator Performers Table -->
            <div class="print-section space-y-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                    <i class="fas fa-award text-amber-500"></i> Top Influencers Leaderboard
                </h3>

                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse table-fixed">
                        <thead>
                            <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3 w-[35%] whitespace-nowrap">Creator / Handle</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Platform</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Followers</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Eng. Rate</th>
                                <th class="p-3 text-right w-[20%] whitespace-nowrap">Generated Sales (GMV)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700 font-medium text-[11px]">
                            <?php if (!empty($top_influencers)): ?>
                                <?php foreach ($top_influencers as $inf): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="p-2.5 overflow-hidden">
                                            <div class="font-bold text-slate-900 truncate"><?php echo html_escape(!empty($inf->full_name) ? $inf->full_name : $inf->username); ?></div>
                                            <div class="text-[10px] text-slate-400 font-normal truncate">@<?php echo html_escape($inf->username); ?></div>
                                        </td>
                                        <td class="p-2.5">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-md text-[9px] font-bold capitalize inline-block">
                                                <?php echo html_escape($inf->platform); ?>
                                            </span>
                                        </td>
                                        <td class="p-2.5 text-center font-bold text-slate-900"><?php echo number_format($inf->followers); ?></td>
                                        <td class="p-2.5 text-center text-emerald-600 font-bold"><?php echo number_format($inf->eng_rate, 2); ?>%</td>
                                        <td class="p-2.5 text-right font-black text-slate-900">$<?php echo number_format($inf->gmv ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">No active creator records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer & Signoff -->
            <div class="print-section pt-6 border-t border-slate-200 text-xs text-slate-500 flex flex-row justify-between items-center gap-4">
                <div>
                    <p class="font-bold text-slate-800">CONFIDENTIAL EXECUTIVE AUDIT</p>
                    <p class="mt-0.5 text-[11px]">Prepared for <?php echo html_escape($active_brand_name); ?> via Instroom Intelligence.</p>
                </div>
                <div class="text-right text-[11px]">
                    <p class="font-semibold text-slate-600">Report Status: Verified</p>
                    <p class="text-slate-400 mt-0.5">System Date: <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>

        </div>
    </div>

    <!-- Isolated Clean Rendering Engine for 100% Full-Width PDF Output -->
    <?php $this->load->view("reports/_pdf_engine"); ?>
</body>
</html>
