<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Performance & Sourcing Analytics Report - <?php echo $report_date; ?></title>
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

    <!-- Top Action Toolbar (Hidden during PDF render / Print) -->
    <div class="no-print standalone-toolbar max-w-[794px] mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?php echo base_url('manager/reports'); ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Reports Center
            </a>
            <span class="text-xs font-semibold text-slate-500">Team Analytics Report (PDF Ready)</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Team_Analytics_Report_<?php echo date('Ymd'); ?>.pdf', true, this)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <i class="fas fa-print"></i> Print Clean PDF
            </button>
            <button onclick="renderCleanPDF('reportContainer', 'Instroom_Team_Analytics_Report_<?php echo date('Ymd'); ?>.pdf', false, this)" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-extrabold transition-all flex items-center gap-2 shadow-md shadow-indigo-200 cursor-pointer">
                <i class="fas fa-file-pdf text-sm"></i> Download Executive PDF
            </button>
        </div>
    </div>

    <!-- Main PDF Report Container (Explicit 794px width for exact 100% A4 PDF fit) -->
    <div id="reportContainer" class="print-page w-[794px] mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#1E1B4B] via-[#3730A3] to-[#6366F1] p-6 md:p-8 text-white relative overflow-hidden print-section">
            <div class="flex flex-row justify-between items-center gap-4 relative z-10 mb-6 border-b border-white/20 pb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl border border-white/20 flex items-center justify-center backdrop-blur-md">
                        <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom Logo" class="w-8 h-8 object-contain" crossorigin="anonymous">
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">INSTROOM</span>
                        <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest">Team Performance & Sourcing Analytics</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-white/15 backdrop-blur-md border border-white/25 text-white rounded-full text-[10px] font-extrabold uppercase tracking-wider mb-1">Executive Team Report</span>
                    <p class="text-[11px] text-indigo-100 font-medium">Generated: <?php echo $report_date; ?></p>
                </div>
            </div>

            <?php 
                $total_sourced = 0;
                $total_ambassadors = 0;
                $total_team_gmv = 0;
                if (!empty($members)) {
                    foreach ($members as $m) {
                        $total_sourced += $m->total_sourced ?? 0;
                        $total_ambassadors += $m->ambassadors ?? 0;
                        $total_team_gmv += $m->total_gmv ?? 0;
                    }
                }
                $avg_conv_rate = $total_sourced > 0 ? round(($total_ambassadors / $total_sourced) * 100, 1) : 0;
            ?>

            <div class="grid grid-cols-4 gap-3 pt-1 relative z-10">
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-indigo-200 font-bold">Brand</p>
                    <p class="text-xs font-bold text-white truncate"><?php echo html_escape($brand_name); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-indigo-200 font-bold">Team Members</p>
                    <p class="text-xs font-bold text-white"><?php echo count($members); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-indigo-200 font-bold">Total Sourced</p>
                    <p class="text-xs font-bold text-white"><?php echo number_format($total_sourced); ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/15">
                    <p class="text-[9px] uppercase tracking-wider text-indigo-200 font-bold">Avg Conversion</p>
                    <p class="text-xs font-extrabold text-indigo-300"><?php echo $avg_conv_rate; ?>%</p>
                </div>
            </div>
        </div>

        <!-- Body Content -->
        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Section Header -->
            <div class="print-section flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-chart-line text-indigo-600"></i> Team Member Productivity & Sourcing Scorecard
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Individual metrics tracking sourcing volume, ambassador conversion, and ROI contribution.</p>
                </div>
            </div>

            <!-- Team Performance Table (Structured table-fixed to fit 100% inside A4 portrait) -->
            <div class="print-section space-y-3">
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse table-fixed">
                        <thead>
                            <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3 w-[28%] whitespace-nowrap">Team Member</th>
                                <th class="p-3 w-[14%] whitespace-nowrap">Role</th>
                                <th class="p-3 text-center w-[12%] whitespace-nowrap">Sourced</th>
                                <th class="p-3 text-center w-[14%] whitespace-nowrap">Ambassadors</th>
                                <th class="p-3 text-center w-[14%] whitespace-nowrap">Conversion</th>
                                <th class="p-3 text-right w-[18%] whitespace-nowrap">Generated GMV ($)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700 font-medium text-[11px]">
                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $m): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="p-2.5 overflow-hidden">
                                            <div class="font-bold text-slate-900 truncate"><?php echo html_escape($m->name); ?></div>
                                            <div class="text-[10px] text-slate-400 font-normal truncate"><?php echo html_escape($m->email); ?></div>
                                        </td>
                                        <td class="p-2.5">
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md text-[9px] font-bold uppercase tracking-wider inline-block">
                                                <?php echo html_escape(ucfirst($m->role)); ?>
                                            </span>
                                        </td>
                                        <td class="p-2.5 text-center font-bold text-slate-900"><?php echo number_format($m->total_sourced); ?></td>
                                        <td class="p-2.5 text-center text-emerald-600 font-bold"><?php echo number_format($m->ambassadors); ?></td>
                                        <td class="p-2.5 text-center">
                                            <span class="font-bold text-slate-900"><?php echo number_format($m->conversion_rate, 1); ?>%</span>
                                        </td>
                                        <td class="p-2.5 text-right font-black text-slate-900">$<?php echo number_format($m->total_gmv ?? 0, 0); ?></td>
                                        <td class="p-2.5 text-center">
                                            <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 font-extrabold text-[10px] rounded border border-emerald-200">
                                                <?php echo number_format($m->roi ?? 0, 1); ?>x
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- Summary Footer Row -->
                                <tr class="bg-slate-100 font-bold text-slate-900 text-xs">
                                    <td class="p-2.5" colspan="2">TOTAL / AVERAGE</td>
                                    <td class="p-2.5 text-center"><?php echo number_format($total_sourced); ?></td>
                                    <td class="p-2.5 text-center text-emerald-700"><?php echo number_format($total_ambassadors); ?></td>
                                    <td class="p-2.5 text-center"><?php echo $avg_conv_rate; ?>%</td>
                                    <td class="p-2.5 text-right font-black">$<?php echo number_format($total_team_gmv, 0); ?></td>
                                    <td class="p-2.5 text-center">-</td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-slate-400">No team members registered yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer & Signoff -->
            <div class="print-section pt-6 border-t border-slate-200 text-xs text-slate-500 flex flex-row justify-between items-center gap-4">
                <div>
                    <p class="font-bold text-slate-800">CONFIDENTIAL & PROPRIETARY REPORT</p>
                    <p class="mt-0.5 text-[11px]">Prepared for <?php echo html_escape($brand_name); ?> Team Management via Instroom Intelligence.</p>
                </div>
                <div class="text-right text-[11px]">
                    <p class="font-semibold text-slate-600">Report Status: Approved & Verified</p>
                    <p class="text-slate-400 mt-0.5">System Date: <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>

        </div>
    </div>

    <!-- Isolated Clean Rendering Engine for 100% Full-Width PDF Output -->
    <?php $this->load->view("reports/_pdf_engine"); ?>
</body>
</html>
