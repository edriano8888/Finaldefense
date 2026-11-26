<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign ROI & Performance Executive Report - <?php echo $report_date; ?></title>
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
            <span class="text-xs font-semibold text-slate-500">Campaign ROI Report (PDF Ready)</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Campaign_ROI_Report_<?php echo date('Ymd'); ?>.pdf', true, this)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <i class="fas fa-print"></i> Print Clean PDF
            </button>
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Campaign_ROI_Report_<?php echo date('Ymd'); ?>.pdf', false, this)" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold transition-all flex items-center gap-2 shadow-md shadow-blue-200 cursor-pointer">
                <i class="fas fa-file-pdf text-sm"></i> Download Executive PDF
            </button>
        </div>
    </div>

    <!-- Main PDF Report Container (Explicit 794px width for exact 100% A4 PDF fit) -->
    <div id="reportContainer" class="print-page w-[794px] mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0F172A] via-[#1E293B] to-[#2563EB] p-6 md:p-8 text-white relative overflow-hidden print-section">
            <div class="flex flex-row justify-between items-center gap-4 relative z-10 mb-6 border-b border-white/20 pb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl border border-white/20 flex items-center justify-center backdrop-blur-md">
                        <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom Logo" class="w-8 h-8 object-contain" crossorigin="anonymous">
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">INSTROOM</span>
                        <p class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Campaign ROI & Performance Overview</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-white/15 backdrop-blur-md border border-white/25 text-white rounded-full text-[10px] font-extrabold uppercase tracking-wider mb-1">Executive ROI Report</span>
                    <p class="text-[11px] text-blue-100 font-medium">Generated: <?php echo $report_date; ?></p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3 pt-1 relative z-10">
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-blue-200 font-bold">Brand</p>
                    <p class="text-xs font-bold text-white truncate"><?php echo html_escape($brand_name); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-blue-200 font-bold">Total Campaigns</p>
                    <p class="text-xs font-bold text-white"><?php echo count($campaigns); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-blue-200 font-bold">Active Trackers</p>
                    <p class="text-xs font-bold text-white"><?php echo count($campaigns); ?> Campaigns</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-blue-200 font-bold">Prepared By</p>
                    <p class="text-xs font-extrabold text-blue-200 truncate"><?php echo html_escape($user_name); ?></p>
                </div>
            </div>
        </div>

        <!-- Body Content -->
        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Section Header -->
            <div class="print-section flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-bullseye text-blue-600"></i> Active & Historical Campaigns Overview
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Performance metrics and influencer distribution per active campaign.</p>
                </div>
            </div>

            <!-- Campaigns Table -->
            <div class="print-section space-y-3">
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse table-fixed">
                        <thead>
                            <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3 w-[34%] whitespace-nowrap">Campaign Title</th>
                                <th class="p-3 text-center w-[15%] whitespace-nowrap">Status</th>
                                <th class="p-3 text-center w-[17%] whitespace-nowrap">Assigned Creators</th>
                                <th class="p-3 text-center w-[17%] whitespace-nowrap">Start Date</th>
                                <th class="p-3 text-center w-[17%] whitespace-nowrap">End Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700 font-medium text-[11px]">
                            <?php if (!empty($campaigns)): ?>
                                <?php foreach ($campaigns as $c): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="p-2.5 font-bold text-slate-900">
                                            <div class="truncate text-slate-900 font-bold"><?php echo html_escape($c->title); ?></div>
                                            <?php if (!empty($c->description)): ?>
                                                <div class="text-[10px] text-slate-400 font-normal truncate"><?php echo html_escape(strip_tags($c->description)); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-2.5 text-center">
                                            <?php 
                                                $statusClass = 'bg-slate-100 text-slate-700';
                                                if ($c->status === 'active') $statusClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                                if ($c->status === 'completed') $statusClass = 'bg-blue-50 text-blue-700 border border-blue-200';
                                                if ($c->status === 'draft') $statusClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                                            ?>
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider inline-block <?php echo $statusClass; ?>">
                                                <?php echo html_escape(ucfirst($c->status)); ?>
                                            </span>
                                        </td>
                                        <td class="p-2.5 text-center font-bold text-slate-900">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-md font-extrabold text-[10px]">
                                                <?php echo number_format($c->assigned_count ?? 0); ?> Creators
                                            </span>
                                        </td>
                                        <td class="p-2.5 text-center font-semibold text-slate-800">
                                            <?php echo !empty($c->start_date) ? date('M d, Y', strtotime($c->start_date)) : 'N/A'; ?>
                                        </td>
                                        <td class="p-2.5 text-center font-semibold text-slate-800">
                                            <?php echo !empty($c->end_date) ? date('M d, Y', strtotime($c->end_date)) : 'N/A'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">No campaigns recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer & Signoff -->
            <div class="print-section pt-6 border-t border-slate-200 text-xs text-slate-500 flex flex-row justify-between items-center gap-4">
                <div>
                    <p class="font-bold text-slate-800">CONFIDENTIAL ROI REPORT</p>
                    <p class="mt-0.5 text-[11px]">Prepared for <?php echo html_escape($brand_name); ?> Executive Leadership.</p>
                </div>
                <div class="text-right text-[11px]">
                    <p class="font-semibold text-slate-600">Report Status: Approved</p>
                    <p class="text-slate-400 mt-0.5">System Date: <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>

        </div>
    </div>

    <!-- Isolated Clean Rendering Engine for 100% Full-Width PDF Output -->
    <?php $this->load->view("reports/_pdf_engine"); ?>
</body>
</html>
