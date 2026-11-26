<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instroom Influencer Report - <?php echo htmlspecialchars($report_date); ?></title>
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

    <!-- Top Action Toolbar -->
    <div class="no-print standalone-toolbar max-w-[794px] mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?php echo base_url('manager/reports'); ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Reports Center
            </a>
            <span class="text-xs font-semibold text-slate-500">Filtered Influencer Report</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Filtered_Report_<?php echo date('Ymd'); ?>.pdf', true, this)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <i class="fas fa-print"></i> Print Clean PDF
            </button>
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Filtered_Report_<?php echo date('Ymd'); ?>.pdf', false, this)" class="px-5 py-2.5 bg-brand-green hover:bg-brand-deep text-white rounded-xl text-xs font-extrabold transition-all flex items-center gap-2 shadow-md shadow-green-200 cursor-pointer">
                <i class="fas fa-file-pdf text-sm"></i> Download Executive PDF
            </button>
        </div>
    </div>

    <!-- Main PDF Report Container -->
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
                        <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Influencer Performance & Audit Report</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-white/10 text-emerald-200 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1 border border-white/10">EXECUTIVE REPORT</span>
                    <p class="text-xs font-semibold text-white">Report Ref: <span class="font-mono text-emerald-300"><?php echo htmlspecialchars($report_id ?? ('INS-RPT-' . date('Ymd'))); ?></span></p>
                    <p class="text-[11px] text-emerald-100">Date: <?php echo htmlspecialchars($report_date); ?></p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-white">Custom Influencer Audit Report</h2>
                    <p class="text-xs text-emerald-100 mt-1">Brand: <span class="font-bold text-white"><?php echo htmlspecialchars($brand_name ?? 'Client Brand'); ?></span> • Prepared by: <?php echo htmlspecialchars($user_name ?? 'Manager'); ?></p>
                </div>
                
                <!-- Filter Pills -->
                <div class="flex flex-wrap gap-2 text-xs">
                    <?php if (!empty($filter_influencer_name)): ?>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-white font-bold text-[11px] border border-white/20">
                            <i class="fas fa-user text-emerald-300 mr-1"></i> <?php echo htmlspecialchars($filter_influencer_name); ?>
                        </span>
                    <?php else: ?>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-white font-bold text-[11px] border border-white/20">
                            <i class="fas fa-users text-emerald-300 mr-1"></i> All Influencers
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($filter_month_label)): ?>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-white font-bold text-[11px] border border-white/20">
                            <i class="fas fa-calendar-alt text-emerald-300 mr-1"></i> Month: <?php echo htmlspecialchars($filter_month_label); ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($filter_day_label)): ?>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-white font-bold text-[11px] border border-white/20">
                            <i class="fas fa-calendar-day text-emerald-300 mr-1"></i> Day: <?php echo htmlspecialchars($filter_day_label); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <?php
            $total_creators = count($influencers ?? []);
            $total_reach = 0;
            $total_eng_sum = 0;
            foreach ($influencers as $inf) {
                $total_reach += (int)($inf->followers ?? 0);
                $total_eng_sum += (float)($inf->eng_rate ?? 0);
            }
            $avg_eng = $total_creators > 0 ? round($total_eng_sum / $total_creators, 2) : 0.0;
        ?>
        <div class="p-6 md:p-8 bg-slate-50 border-b border-slate-200">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Matched Creators</p>
                    <p class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format($total_creators); ?></p>
                    <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Based on filter criteria</p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Combined Reach</p>
                    <p class="text-2xl font-black text-slate-900 mt-1"><?php echo number_format($total_reach); ?></p>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Total Followers</p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Avg Engagement Rate</p>
                    <p class="text-2xl font-black text-brand-green mt-1"><?php echo number_format($avg_eng, 2); ?>%</p>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Across matched creators</p>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="p-6 md:p-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-700">Detailed Creator Records</h3>
                <span class="text-xs font-semibold text-slate-400"><?php echo $total_creators; ?> record(s)</span>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 uppercase text-[10px] font-extrabold tracking-wider border-b border-slate-200">
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Influencer</th>
                            <th class="py-3 px-3">Platform</th>
                            <th class="py-3 px-3">Niche</th>
                            <th class="py-3 px-3 text-right">Followers</th>
                            <th class="py-3 px-3 text-right">Eng. Rate</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Date Added</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        <?php if (!empty($influencers)): ?>
                            <?php foreach ($influencers as $idx => $inf): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-3 text-slate-400 font-mono text-[11px]"><?php echo $idx + 1; ?></td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-slate-900"><?php echo htmlspecialchars(!empty($inf->full_name) ? $inf->full_name : ($inf->username ?? 'Influencer')); ?></div>
                                        <div class="text-[11px] text-slate-400">@<?php echo htmlspecialchars($inf->username ?? '-'); ?></div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-slate-100 text-slate-700">
                                            <?php echo htmlspecialchars(Influencer_model::normalize_platform($inf->platform ?? '')); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600"><?php echo htmlspecialchars(!empty($inf->niche) ? $inf->niche : 'General'); ?></td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-800"><?php echo number_format((int)($inf->followers ?? 0)); ?></td>
                                    <td class="py-3 px-3 text-right font-bold text-emerald-600"><?php echo number_format((float)($inf->eng_rate ?? 0), 2); ?>%</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo (!empty($inf->pipeline_status) && in_array($inf->pipeline_status, ['Posted', 'Campaign Completed'])) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700'; ?>">
                                            <?php echo htmlspecialchars(!empty($inf->pipeline_status) ? $inf->pipeline_status : ($inf->status ?? 'Active')); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right text-slate-500 font-mono text-[11px]">
                                        <?php echo (!empty($inf->created_at) && $inf->created_at !== '0000-00-00 00:00:00') ? date('M d, Y', strtotime($inf->created_at)) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 italic">No influencers found matching the selected filter criteria.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer / Disclaimer -->
            <div class="mt-8 pt-4 border-t border-slate-200 flex flex-row justify-between items-center text-[10px] text-slate-400">
                <p>CONFIDENTIAL & PROPRIETARY • INSTROOM ENTERPRISE REPORTING</p>
                <p>Generated automatically via Instroom System</p>
            </div>
        </div>

    </div>

    <?php $this->load->view("reports/_pdf_engine"); ?>
</body>
</html>
