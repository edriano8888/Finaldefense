<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creator Performance & Audit Report - <?php echo htmlspecialchars(!empty($influencer['full_name']) ? $influencer['full_name'] : (!empty($influencer['username']) ? $influencer['username'] : 'Influencer')); ?> - <?php echo $report_date; ?></title>
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

    <!-- Top Action Toolbar (Hidden during PDF render / Print) -->
    <div class="no-print standalone-toolbar max-w-[794px] mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?php echo base_url('manager/reports'); ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Reports Center
            </a>
            <span class="text-xs font-semibold text-slate-500">Executive Influencer Audit Dossier (Clean PDF Ready)</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="renderCleanPDF('reportContainer', 'Enterprise_Creator_Audit_<?php echo urlencode(!empty($influencer['username']) ? $influencer['username'] : 'creator'); ?>_<?php echo date('Ymd'); ?>.pdf', true, this)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <i class="fas fa-print"></i> Print Clean PDF
            </button>
            <button onclick="renderCleanPDF('reportContainer', 'Enterprise_Creator_Audit_<?php echo urlencode(!empty($influencer['username']) ? $influencer['username'] : 'creator'); ?>_<?php echo date('Ymd'); ?>.pdf', false, this)" class="px-5 py-2.5 bg-brand-green hover:bg-brand-deep text-white rounded-xl text-xs font-extrabold transition-all flex items-center gap-2 shadow-md shadow-green-200 cursor-pointer">
                <i class="fas fa-file-pdf text-sm"></i> Download Executive PDF
            </button>
        </div>
    </div>

    <!-- Main PDF Report Container (Explicit 794px width for exact 100% A4 PDF fit) -->
    <div id="reportContainer" class="print-page w-[794px] mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        
        <?php
            // Calculate Performance Ratios & Benchmarks
            $followers = (float)($influencer['followers'] ?? 0);
            $eng_rate = (float)($influencer['eng_rate'] ?? 0);
            $avg_views = (float)($influencer['avg_video_views'] ?? 0);
            $fee = (float)($influencer['fee'] ?? 0);
            $prod_cost = (float)($influencer['product_cost'] ?? 0);
            $gmv = (float)($influencer['gmv'] ?? 0);
            
            $total_investment = $fee + $prod_cost;
            $roi = $total_investment > 0 ? ($gmv / $total_investment) : ($gmv > 0 ? 5.0 : 0.0);
            
            // Performance Tier Assessment
            if ($gmv >= 500000 || $roi >= 4.0) {
                $tier = 'Gold Partner / Top Converter';
                $tier_badge = 'bg-amber-400/20 text-amber-300 border-amber-400/40';
            } elseif ($gmv >= 100000 || $roi >= 2.0) {
                $tier = 'Silver Partner / High Performer';
                $tier_badge = 'bg-slate-300/20 text-slate-200 border-slate-300/40';
            } else {
                $tier = 'Standard Creator / Active Pipeline';
                $tier_badge = 'bg-emerald-400/20 text-emerald-300 border-emerald-400/40';
            }
        ?>

        <!-- Executive Header Banner -->
        <div class="bg-gradient-to-r from-[#0A2E1C] via-[#0F6B3E] to-[#1FAE5B] p-6 md:p-8 text-white relative overflow-hidden print-section">
            <div class="flex flex-row justify-between items-center gap-4 relative z-10 mb-6 border-b border-white/20 pb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl border border-white/20 flex items-center justify-center backdrop-blur-md">
                        <i class="fas fa-chart-line text-xl text-white"></i>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">INSTROOM</span>
                        <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Enterprise Media Intelligence & Creator Audit</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-white/10 text-emerald-200 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1 border border-white/10">CONFIDENTIAL EXECUTIVE AUDIT</span>
                    <p class="text-xs font-semibold text-white">Audit Ref: <span class="font-mono text-emerald-300"><?php echo $report_id; ?></span></p>
                    <p class="text-[11px] text-emerald-100">Audit Date: <?php echo $report_date; ?></p>
                </div>
            </div>

            <!-- Profile Overview Block inside Banner -->
            <div class="flex flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <?php 
                        $img = !empty($influencer['profile_image']) ? $influencer['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode(!empty($influencer['full_name']) ? $influencer['full_name'] : ($influencer['username'] ?? 'User')) . '&background=1FAE5B&color=fff&size=128';
                    ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Profile" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/30 shadow-lg shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-black text-white leading-tight">
                                <?php 
                                    echo htmlspecialchars(!empty($influencer['full_name']) ? $influencer['full_name'] : (!empty($influencer['username']) ? $influencer['username'] : 'Influencer Partner')); 
                                ?>
                            </h1>
                        </div>
                        <p class="text-xs text-emerald-100 font-medium flex items-center gap-2 mt-0.5">
                            <span class="font-bold">@<?php echo htmlspecialchars($influencer['username'] ?? 'n/a'); ?></span>
                            <span>•</span>
                            <span class="capitalize"><?php echo htmlspecialchars($influencer['platform'] ?? 'Social Media'); ?> KOL</span>
                        </p>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="px-2.5 py-0.5 bg-white/20 text-white rounded-md text-[10px] font-bold capitalize backdrop-blur-md border border-white/20">
                                <i class="fas fa-tasks text-[9px] mr-1"></i> Stage: <?php echo htmlspecialchars($influencer['pipeline_status'] ?? 'Active'); ?>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold border backdrop-blur-md <?php echo $tier_badge; ?>">
                                <i class="fas fa-award text-[9px] mr-1"></i> <?php echo $tier; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-xl border border-white/20 text-right shrink-0">
                    <p class="text-[9px] font-extrabold uppercase text-emerald-200 tracking-wider">Managing Brand</p>
                    <p class="text-xs font-bold text-white"><?php echo htmlspecialchars(!empty($influencer['brand_name']) ? $influencer['brand_name'] : 'Instroom Enterprise'); ?></p>
                    <p class="text-[10px] text-emerald-100 mt-0.5"><i class="fas fa-user-check text-[9px] mr-1"></i> Account Mgr: <?php echo htmlspecialchars(!empty($influencer['sourced_by_name']) ? $influencer['sourced_by_name'] : ($user_name ?? 'Senior Strategist')); ?></p>
                </div>
            </div>
        </div>

        <!-- Executive Narrative & Performance Context -->
        <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/60 print-section">
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Executive Summary & Campaign Context</h2>
            <p class="text-xs text-slate-700 leading-relaxed font-normal">
                This document serves as an official performance evaluation and commercial audit for partner creator 
                <strong class="text-slate-900">@<?php echo htmlspecialchars($influencer['username'] ?? 'creator'); ?></strong>. 
                All data points, engagement statistics, financial inputs, and campaign deliverables presented herein have been calculated live from 
                <strong class="text-brand-deep">Instroom Media Intelligence Database</strong>.
            </p>
        </div>

        <!-- Key Financial & Reach Performance Metrics -->
        <div class="p-6 md:p-8 border-b border-slate-100 print-section">
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Key Financial & Performance Benchmarks</h2>
            <div class="grid grid-cols-6 gap-3">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Reach</p>
                    <p class="text-sm font-black text-slate-900"><?php echo number_format($followers); ?></p>
                    <p class="text-[9px] text-slate-500">Followers</p>
                </div>
                <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-0.5">Eng. Rate</p>
                    <p class="text-sm font-black text-emerald-700"><?php echo number_format($eng_rate, 2); ?>%</p>
                    <p class="text-[9px] text-emerald-600">Cross-Platform</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                    <p class="text-[9px] font-bold text-blue-600 uppercase tracking-wider mb-0.5">Avg Views</p>
                    <p class="text-sm font-black text-blue-700"><?php echo number_format($avg_views); ?></p>
                    <p class="text-[9px] text-blue-600">Per Post</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-xl border border-purple-100">
                    <p class="text-[9px] font-bold text-purple-600 uppercase tracking-wider mb-0.5">Investment</p>
                    <p class="text-sm font-black text-purple-700">$<?php echo number_format($total_investment, 0); ?></p>
                    <p class="text-[9px] text-purple-600">Fee + Prod</p>
                </div>
                <div class="bg-amber-50 p-3 rounded-xl border border-amber-100">
                    <p class="text-[9px] font-bold text-amber-600 uppercase tracking-wider mb-0.5">Sales GMV</p>
                    <p class="text-sm font-black text-amber-700">$<?php echo number_format($gmv, 0); ?></p>
                    <p class="text-[9px] text-amber-600">Attributed</p>
                </div>
                <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                    <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-wider mb-0.5">ROI</p>
                    <p class="text-sm font-black text-indigo-700"><?php echo number_format($roi, 1); ?>x</p>
                    <p class="text-[9px] text-indigo-600">Return</p>
                </div>
            </div>
        </div>

        <!-- Detailed Audit Grid (2 Columns) -->
        <div class="p-6 md:p-8 grid grid-cols-2 gap-6 border-b border-slate-100 print-section">
            
            <!-- Column 1: Commercial & Profile Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center text-slate-700">
                        <i class="fas fa-id-card text-xs"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900">Partner & Contact Verification</h3>
                </div>

                <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Primary Contact:</span>
                        <span class="font-bold text-slate-900 text-right truncate max-w-[170px]"><?php echo htmlspecialchars(!empty($influencer['full_name']) ? $influencer['full_name'] : 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Verified Email:</span>
                        <span class="font-bold text-slate-900 text-right truncate max-w-[170px]"><?php echo htmlspecialchars(!empty($influencer['email']) ? $influencer['email'] : 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Direct Phone:</span>
                        <span class="font-bold text-slate-900 text-right truncate max-w-[170px]"><?php echo htmlspecialchars(!empty($influencer['contact_number']) ? $influencer['contact_number'] : (!empty($influencer['contact_info']) ? $influencer['contact_info'] : 'N/A')); ?></span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Territory:</span>
                        <span class="font-bold text-slate-900 text-right truncate max-w-[170px]"><?php echo htmlspecialchars(!empty($influencer['location']) ? $influencer['location'] : 'Global'); ?></span>
                    </div>
                    <div class="flex justify-between items-start pt-0.5">
                        <span class="text-slate-500 font-semibold">Fulfillment Address:</span>
                        <span class="font-medium text-slate-800 text-right max-w-[170px] truncate"><?php echo htmlspecialchars(!empty($influencer['shipping_address']) ? $influencer['shipping_address'] : 'No shipping address recorded'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Column 2: Contractual & Financial Terms -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center text-brand-green">
                        <i class="fas fa-file-contract text-xs"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900">Commercial Terms & Campaign Agreement</h3>
                </div>

                <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Assigned Campaign:</span>
                        <span class="font-bold text-slate-900 text-right truncate max-w-[170px]"><?php echo htmlspecialchars(!empty($influencer['product_name']) ? $influencer['product_name'] : (!empty($influencer['campaign_type']) ? $influencer['campaign_type'] : 'General Partnership')); ?></span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Order Reference:</span>
                        <span class="font-mono font-bold text-slate-800 text-right truncate max-w-[170px]"><?php echo htmlspecialchars(!empty($influencer['order_number']) ? $influencer['order_number'] : 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Discount Code:</span>
                        <span class="font-mono font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded text-right"><?php echo htmlspecialchars(!empty($influencer['discount_code']) ? $influencer['discount_code'] : 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500 font-semibold">Compensation Fee:</span>
                        <span class="font-bold text-emerald-700 text-right">$<?php echo number_format($fee, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-start pt-0.5">
                        <span class="text-slate-500 font-semibold">Execution Deadline:</span>
                        <span class="font-bold text-slate-900 text-right"><?php echo !empty($influencer['deadline']) ? date('M d, Y', strtotime($influencer['deadline'])) : 'Open Contract'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contractual Deliverables & Tracking Assets Audit -->
        <div class="p-6 md:p-8 border-b border-slate-100 print-section">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Contractual Deliverables & Asset Audit</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i class="fas fa-list-check text-brand-green"></i> Scope of Deliverables
                    </p>
                    <div class="text-xs text-slate-700 leading-relaxed font-medium bg-white p-2.5 rounded-lg border border-slate-200">
                        <?php echo !empty($influencer['deliverables']) ? nl2br(htmlspecialchars($influencer['deliverables'])) : 'No specific deliverables documented.'; ?>
                    </div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-2">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i class="fas fa-link text-blue-500"></i> Digital Tracking Assets
                    </p>
                    <div class="bg-white p-2.5 rounded-lg border border-slate-200 space-y-1.5 text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 font-semibold block">Affiliate URL:</span>
                            <span class="font-mono text-blue-600 truncate block"><?php echo htmlspecialchars(!empty($influencer['affiliate_link']) ? $influencer['affiliate_link'] : 'N/A'); ?></span>
                        </div>
                        <div class="border-t border-slate-100 pt-1">
                            <span class="text-[10px] text-slate-400 font-semibold block">Tracking Link:</span>
                            <span class="font-mono text-emerald-600 truncate block"><?php echo htmlspecialchars(!empty($influencer['tracking_link']) ? $influencer['tracking_link'] : 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Qualitative Assessment & Sign-off -->
        <div class="p-6 md:p-8 bg-slate-50 border-b border-slate-100 print-section">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Executive Authorization & Sign-off</h3>
            <div class="grid grid-cols-2 gap-6 text-xs">
                <div class="border-t-2 border-slate-300 pt-2">
                    <p class="font-bold text-slate-800"><?php echo htmlspecialchars(!empty($influencer['sourced_by_name']) ? $influencer['sourced_by_name'] : ($user_name ?? 'Campaign Strategist')); ?></p>
                    <p class="text-[10px] text-slate-500 font-medium uppercase">Influencer Campaign Strategist</p>
                    <p class="text-[10px] text-slate-400">Date: <?php echo date('F d, Y'); ?></p>
                </div>
                <div class="border-t-2 border-slate-300 pt-2">
                    <p class="font-bold text-slate-800">Media Director / Executive Approver</p>
                    <p class="text-[10px] text-slate-500 font-medium uppercase">Instroom Enterprise Governance</p>
                    <p class="text-[10px] text-slate-400">Status: Certified Audit Document</p>
                </div>
            </div>
        </div>

        <!-- Report Footer -->
        <div class="bg-slate-900 p-6 text-white flex flex-row justify-between items-center gap-4 text-xs print-section">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 bg-white/10 rounded-lg flex items-center justify-center font-black text-emerald-400">
                    I
                </div>
                <div>
                    <p class="font-bold text-white text-xs">INSTROOM Enterprise Media Intelligence</p>
                    <p class="text-[9px] text-slate-400">Strictly Confidential • Generated for Corporate Audit</p>
                </div>
            </div>
            <div class="text-right text-slate-400 text-[10px]">
                <p>Auditor: <?php echo htmlspecialchars($user_name ?? 'Manager'); ?></p>
                <p class="text-slate-500">Document ID: <?php echo $report_id; ?></p>
            </div>
        </div>

    </div>

    <!-- Script for HTML2PDF Download & Clean Print Stream -->
    <?php $this->load->view("reports/_pdf_engine"); ?>
</body>
</html>
