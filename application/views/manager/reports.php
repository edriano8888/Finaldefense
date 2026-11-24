<div class="space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports Center</h1>
            <p class="text-sm text-gray-500 mt-1">Generate and export detailed performance and operational reports.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Last Sync: <?php echo date('H:i'); ?></span>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Reports Available</p>
            <h3 class="text-2xl font-bold text-gray-900">4</h3>
            <p class="text-xs text-brand-green mt-1 font-medium">Ready for export & print</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Active Campaigns</p>
            <h3 class="text-2xl font-bold text-gray-900"><?php echo $total_campaigns; ?></h3>
            <p class="text-xs text-blue-500 mt-1 font-medium">In ROI report</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Team Members</p>
            <h3 class="text-2xl font-bold text-gray-900"><?php echo $total_members; ?></h3>
            <p class="text-xs text-purple-500 mt-1 font-medium">In performance report</p>
        </div>
    </div>

    <!-- Master Custom Report Filter & Export Studio -->
    <div class="bg-white rounded-3xl border border-emerald-200/80 shadow-md p-6 sm:p-8 relative overflow-hidden ring-4 ring-emerald-500/5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-brand-green rounded-full text-xs font-extrabold uppercase tracking-wider mb-2 border border-emerald-100">
                    <i class="fas fa-filter"></i> Interactive Filter & Export Studio
                </div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Custom Export Generator</h3>
                <p class="text-xs text-slate-500 mt-1">Filter by specific Influencer, Month, and Day to generate customized Executive PDF or Excel / CSV reports.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="setFilterPreset('this_month')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">This Month</button>
                <button type="button" onclick="setFilterPreset('today')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">Today</button>
                <button type="button" onclick="setFilterPreset('all')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">Reset All</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-12 gap-4 items-end">
            <!-- 1. Influencer Filter -->
            <div class="lg:col-span-5">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    <i class="fas fa-user-astronaut text-brand-green mr-1"></i> Select Influencer:
                </label>
                <select id="filter_influencer_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none shadow-xs transition-all cursor-pointer">
                    <option value="all">⭐ All Influencers (Full Roster)</option>
                    <?php if (!empty($all_influencers)): ?>
                        <?php foreach ($all_influencers as $inf): ?>
                            <?php 
                                $name = !empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : 'Influencer #' . $inf->id);
                                $handle = !empty($inf->username) ? ' (@' . $inf->username . ')' : '';
                                $platform = !empty($inf->platform) ? ' - ' . ucfirst($inf->platform) : '';
                            ?>
                            <option value="<?php echo $inf->id; ?>"><?php echo htmlspecialchars($name . $handle . $platform); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- 2. Month Filter -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    <i class="fas fa-calendar-alt text-brand-green mr-1"></i> Filter by Month:
                </label>
                <select id="filter_month" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none shadow-xs transition-all cursor-pointer">
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>

            <!-- 3. Day / Date Filter -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    <i class="fas fa-calendar-day text-brand-green mr-1"></i> Day of Month:
                </label>
                <select id="filter_day" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none shadow-xs transition-all cursor-pointer">
                    <option value="">All Days</option>
                    <?php for ($d = 1; $d <= 31; $d++): ?>
                        <option value="<?php echo $d; ?>">Day <?php echo $d; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- 4. Specific Date Input -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    <i class="fas fa-clock text-brand-green mr-1"></i> Or Exact Date:
                </label>
                <input type="date" id="filter_date" onchange="onDateInputChange(this.value)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none shadow-xs transition-all cursor-pointer">
            </div>
        </div>

        <!-- Export Buttons Toolbar -->
        <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <i class="fas fa-info-circle text-brand-green"></i>
                <span id="filter_preview_summary">Generating report for: <b>All Influencers</b> across <b>All Months & Days</b></span>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" onclick="triggerCustomExport('pdf')" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-green hover:bg-brand-deep text-white rounded-xl font-extrabold text-xs shadow-md shadow-green-500/20 hover:scale-[1.02] transition-all cursor-pointer">
                    <i class="fas fa-file-pdf text-sm"></i> Export PDF Report
                </button>
                <button type="button" onclick="triggerCustomExport('csv')" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-brand-green border-2 border-brand-green hover:bg-emerald-50 rounded-xl font-extrabold text-xs shadow-xs hover:scale-[1.02] transition-all cursor-pointer">
                    <i class="fas fa-file-csv text-sm"></i> Export Excel / CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Report Templates Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Individual Influencer Specific Report -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col justify-between ring-2 ring-emerald-500/20">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 bg-emerald-50 text-brand-green rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-astronaut text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-50 text-brand-green text-[10px] font-extrabold uppercase tracking-wider rounded-full border border-emerald-100">Per Influencer PDF</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-1">Influencer Specific Report</h4>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Export a complete, accurate performance dossier for a specific creator, including metrics, contact, deliverables & logistics.</p>
                
                <div class="space-y-3 mb-6 bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Select Influencer:</label>
                    <select id="influencer_pdf_select" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none shadow-sm cursor-pointer">
                        <?php if (!empty($all_influencers)): ?>
                            <?php foreach ($all_influencers as $inf): ?>
                                <option value="<?php echo $inf->id; ?>">
                                    <?php 
                                        $name = !empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : 'Influencer #' . $inf->id);
                                        $handle = !empty($inf->username) ? ' (@' . $inf->username . ')' : '';
                                        $platform = !empty($inf->platform) ? ' - ' . ucfirst($inf->platform) : '';
                                        echo htmlspecialchars($name . $handle . $platform); 
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No influencers found</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="p-6 pt-0 space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="exportSelectedInfluencer('pdf')" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-brand-green text-white rounded-xl font-extrabold text-xs hover:bg-brand-deep transition-all shadow-md shadow-green-500/20 hover:scale-[1.02] cursor-pointer">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button onclick="exportSelectedInfluencer('csv')" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-white text-brand-green border-2 border-brand-green rounded-xl font-extrabold text-xs hover:bg-emerald-50 transition-all hover:scale-[1.02] cursor-pointer">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                </div>
                <a href="<?php echo base_url('manager/export_influencers_csv'); ?>" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-slate-500 hover:text-brand-green rounded-xl font-bold text-[11px] transition-all">
                    <i class="fas fa-table"></i> Export full roster (CSV)
                </a>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col justify-between">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-pie text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-teal-50 text-teal-600 text-[10px] font-extrabold uppercase tracking-wider rounded-full border border-teal-100">Executive PDF</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-1">Performance Summary</h4>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Comprehensive overview of influencer marketing impact, including GMV, engagement rates, and platform breakdown.</p>
                
                <div class="space-y-2 mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-teal-500"></i>
                        <span>GMV & Expenditure Analysis</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-teal-500"></i>
                        <span>Platform-wise Breakdown</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-teal-500"></i>
                        <span>Engagement Benchmarks</span>
                    </div>
                </div>
            </div>

            <div class="p-6 pt-0 grid grid-cols-2 gap-2">
                <button type="button" onclick="openReportPreview('<?php echo base_url('manager/export_performance_pdf'); ?>', 'Performance Summary Executive Report', 'Instroom_Performance_Report_<?php echo date('Ymd'); ?>.pdf')" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-teal-600 text-white rounded-xl font-extrabold text-xs hover:bg-teal-700 transition-all shadow-md shadow-teal-500/20 hover:scale-[1.02] cursor-pointer">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <a href="<?php echo base_url('manager/export_performance'); ?>" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-white text-teal-600 border-2 border-teal-600 rounded-xl font-extrabold text-xs hover:bg-teal-50 transition-all hover:scale-[1.02]">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
            </div>
        </div>

        <!-- Team Performance -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col justify-between">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-users-cog text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-extrabold uppercase tracking-wider rounded-full border border-indigo-100">Executive PDF</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-1">Team Analytics</h4>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Detailed metrics for each team member, tracking their sourcing efficiency, conversion rates, and ROI.</p>
                
                <div class="space-y-2 mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-indigo-500"></i>
                        <span>Individual Sourcing Stats</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-indigo-500"></i>
                        <span>Team Member Conversion Rates</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-indigo-500"></i>
                        <span>ROI Contribution by Member</span>
                    </div>
                </div>
            </div>

            <div class="p-6 pt-0 grid grid-cols-2 gap-2">
                <button type="button" onclick="openReportPreview('<?php echo base_url('manager/export_team_pdf'); ?>', 'Team Analytics Executive Report', 'Instroom_Team_Analytics_<?php echo date('Ymd'); ?>.pdf')" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-indigo-600 text-white rounded-xl font-extrabold text-xs hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20 hover:scale-[1.02] cursor-pointer">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <a href="<?php echo base_url('manager/export_team'); ?>" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-white text-indigo-600 border-2 border-indigo-600 rounded-xl font-extrabold text-xs hover:bg-indigo-50 transition-all hover:scale-[1.02]">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
            </div>
        </div>

        <!-- Campaign ROI -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col justify-between">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-rocket text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-extrabold uppercase tracking-wider rounded-full border border-blue-100">Executive PDF</span>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-1">Campaign ROI</h4>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Track the success of individual campaigns, comparing their goals against real-world performance.</p>
                
                <div class="space-y-2 mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-blue-500"></i>
                        <span>Active vs Completed Campaigns</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-blue-500"></i>
                        <span>Budget vs Revenue Analysis</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                        <i class="fas fa-check-circle text-blue-500"></i>
                        <span>Timeline & Milestones</span>
                    </div>
                </div>
            </div>

            <div class="p-6 pt-0 grid grid-cols-2 gap-2">
                <button type="button" onclick="openReportPreview('<?php echo base_url('manager/export_campaigns_pdf'); ?>', 'Campaign ROI Executive Report', 'Instroom_Campaign_ROI_<?php echo date('Ymd'); ?>.pdf')" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-blue-600 text-white rounded-xl font-extrabold text-xs hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20 hover:scale-[1.02] cursor-pointer">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <a href="<?php echo base_url('manager/export_campaigns'); ?>" class="w-full inline-flex items-center justify-center gap-2 px-3 py-3 bg-white text-blue-600 border-2 border-blue-600 rounded-xl font-extrabold text-xs hover:bg-blue-50 transition-all hover:scale-[1.02]">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
            </div>
        </div>

    </div>

    <!-- Instructions / Notes -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-8 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-brand-green text-xl shrink-0 backdrop-blur-md">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div>
                <h5 class="text-base font-bold text-white mb-1">Executive & Individual PDF Reports</h5>
                <p class="text-xs text-slate-300 leading-relaxed">All PDF reports open in our integrated A4 Executive Preview studio — ready for crisp one-click PDF download or direct Print-to-PDF without messy browser popups.</p>
            </div>
        </div>
    </div>
</div>

<!-- In-App Executive Report Preview Modal (A4 Document Studio) -->
<div id="executiveReportModal" class="hidden fixed inset-0 z-[100] bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-2 sm:p-5">
    <div class="bg-white w-full max-w-5xl h-[92vh] rounded-3xl shadow-2xl border border-slate-200/80 flex flex-col overflow-hidden">
        <!-- Executive Studio Top Bar -->
        <div class="bg-slate-900 text-white px-5 py-3.5 flex flex-wrap items-center justify-between gap-3 shrink-0 border-b border-slate-800">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 shrink-0">
                    <i class="fas fa-file-alt text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h4 id="reportModalTitle" class="text-sm font-extrabold text-white truncate">Executive Report Preview</h4>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">A4 Portrait • Print & PDF Ready</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="triggerModalPrint()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-print"></i> Print to PDF
                </button>
                <button type="button" id="modalDownloadPdfBtn" onclick="triggerModalDownload(this)" class="px-4 py-2 bg-brand-green hover:bg-brand-deep text-white rounded-xl text-xs font-extrabold transition-all inline-flex items-center gap-2 shadow-lg shadow-emerald-500/20 cursor-pointer">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                <button type="button" onclick="closeReportPreview()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer" title="Close Preview">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- A4 Document Canvas -->
        <div class="relative flex-1 bg-slate-100 overflow-hidden">
            <div id="reportPreviewLoader" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-slate-100 z-10">
                <div class="w-10 h-10 rounded-full border-4 border-emerald-200 border-t-brand-green animate-spin"></div>
                <p class="text-xs font-bold text-slate-500">Preparing A4 Executive Document...</p>
            </div>
            <iframe id="reportPreviewFrame" onload="onReportFrameLoad()" class="w-full h-full border-0 transition-opacity duration-300 opacity-0 bg-slate-100"></iframe>
        </div>
    </div>
</div>

<script>
    function updateFilterSummary() {
        const infSelect = document.getElementById('filter_influencer_id');
        const monthSelect = document.getElementById('filter_month');
        const daySelect = document.getElementById('filter_day');
        const dateInput = document.getElementById('filter_date');
        const summaryEl = document.getElementById('filter_preview_summary');

        if (!infSelect || !summaryEl) return;

        let infName = infSelect.options[infSelect.selectedIndex] ? infSelect.options[infSelect.selectedIndex].text : 'All Influencers';
        let monthName = (monthSelect && monthSelect.value) ? monthSelect.options[monthSelect.selectedIndex].text : 'All Months';
        let dayText = (dateInput && dateInput.value) ? dateInput.value : ((daySelect && daySelect.value) ? 'Day ' + daySelect.value : 'All Days');

        summaryEl.innerHTML = `Filter: <b class="text-slate-900">${infName}</b> | Month: <b class="text-slate-900">${monthName}</b> | Day: <b class="text-slate-900">${dayText}</b>`;
    }

    function onDateInputChange(val) {
        if (val) {
            const parts = val.split('-');
            if (parts.length === 3) {
                const mSelect = document.getElementById('filter_month');
                const dSelect = document.getElementById('filter_day');
                if (mSelect) mSelect.value = parseInt(parts[1], 10);
                if (dSelect) dSelect.value = parseInt(parts[2], 10);
            }
        }
        updateFilterSummary();
    }

    function setFilterPreset(preset) {
        const today = new Date();
        const dateEl = document.getElementById('filter_date');
        const monthEl = document.getElementById('filter_month');
        const dayEl = document.getElementById('filter_day');
        const infEl = document.getElementById('filter_influencer_id');

        if (preset === 'today') {
            const y = today.getFullYear();
            const m = String(today.getMonth() + 1).padStart(2, '0');
            const d = String(today.getDate()).padStart(2, '0');
            if (dateEl) dateEl.value = `${y}-${m}-${d}`;
            if (monthEl) monthEl.value = today.getMonth() + 1;
            if (dayEl) dayEl.value = today.getDate();
        } else if (preset === 'this_month') {
            if (dateEl) dateEl.value = '';
            if (monthEl) monthEl.value = today.getMonth() + 1;
            if (dayEl) dayEl.value = '';
        } else if (preset === 'all') {
            if (infEl) infEl.value = 'all';
            if (dateEl) dateEl.value = '';
            if (monthEl) monthEl.value = '';
            if (dayEl) dayEl.value = '';
        }
        updateFilterSummary();
    }

    function triggerCustomExport(type) {
        const infId = document.getElementById('filter_influencer_id') ? document.getElementById('filter_influencer_id').value : 'all';
        const month = document.getElementById('filter_month') ? document.getElementById('filter_month').value : '';
        const day = document.getElementById('filter_day') ? document.getElementById('filter_day').value : '';
        const date = document.getElementById('filter_date') ? document.getElementById('filter_date').value : '';

        const params = new URLSearchParams();
        if (infId && infId !== 'all') params.append('influencer_id', infId);
        if (month) params.append('month', month);
        if (day) params.append('day', day);
        if (date) params.append('date', date);

        if (type === 'pdf') {
            const url = '<?php echo base_url("manager/export_influencer_pdf"); ?>?' + params.toString();
            openReportPreview(url, 'Custom Influencer Audit Report', 'Instroom_Custom_Report_<?php echo date('Ymd'); ?>.pdf');
        } else {
            const url = '<?php echo base_url("manager/export_influencers_csv"); ?>?' + params.toString();
            window.location.href = url;
        }
    }

    function exportSelectedInfluencer(type) {
        const select = document.getElementById('influencer_pdf_select');
        const id = select ? select.value : '';
        if (!id) {
            alert('Please select an influencer first.');
            return;
        }

        const month = document.getElementById('filter_month') ? document.getElementById('filter_month').value : '';
        const day = document.getElementById('filter_day') ? document.getElementById('filter_day').value : '';
        const date = document.getElementById('filter_date') ? document.getElementById('filter_date').value : '';

        const params = new URLSearchParams();
        params.append('influencer_id', id);
        if (month) params.append('month', month);
        if (day) params.append('day', day);
        if (date) params.append('date', date);

        if (type === 'csv') {
            window.location.href = '<?php echo base_url("manager/export_influencers_csv"); ?>?' + params.toString();
        } else {
            const infLabel = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : 'Influencer Report';
            openReportPreview('<?php echo base_url("manager/export_influencer_pdf"); ?>?' + params.toString(), infLabel, 'Instroom_Influencer_Report_<?php echo date('Ymd'); ?>.pdf');
        }
    }

    // In-App Executive Report Preview Modal Controller (No Raw Popup Windows)
    let activeReportFilename = 'Instroom_Report.pdf';

    function openReportPreview(url, title, filename) {
        activeReportFilename = filename || 'Instroom_Report.pdf';
        const modal = document.getElementById('executiveReportModal');
        const titleEl = document.getElementById('reportModalTitle');
        const frame = document.getElementById('reportPreviewFrame');
        const loader = document.getElementById('reportPreviewLoader');

        if (!modal || !frame) return;

        titleEl.textContent = title || 'Executive Report Preview';
        loader.classList.remove('hidden');
        frame.classList.add('opacity-0');

        const sep = url.includes('?') ? '&' : '?';
        frame.src = url + sep + 'embed=1';

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeReportPreview() {
        const modal = document.getElementById('executiveReportModal');
        const frame = document.getElementById('reportPreviewFrame');
        if (modal) modal.classList.add('hidden');
        if (frame) frame.src = 'about:blank';
        document.body.classList.remove('overflow-hidden');
    }

    function onReportFrameLoad() {
        const frame = document.getElementById('reportPreviewFrame');
        const loader = document.getElementById('reportPreviewLoader');
        if (frame && frame.src !== 'about:blank') {
            if (loader) loader.classList.add('hidden');
            frame.classList.remove('opacity-0');
        }
    }

    function triggerModalPrint() {
        const frame = document.getElementById('reportPreviewFrame');
        if (frame && frame.contentWindow) {
            frame.contentWindow.postMessage({ type: 'INSTROOM_TRIGGER_PRINT' }, '*');
        }
    }

    function triggerModalDownload(btn) {
        const frame = document.getElementById('reportPreviewFrame');
        if (!frame || !frame.contentWindow) return;

        btn.dataset.origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
        btn.disabled = true;

        frame.contentWindow.postMessage({
            type: 'INSTROOM_TRIGGER_DOWNLOAD',
            filename: activeReportFilename
        }, '*');
    }

    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'INSTROOM_PDF_DONE') {
            const dlBtn = document.getElementById('modalDownloadPdfBtn');
            if (dlBtn && dlBtn.dataset.origHtml) {
                dlBtn.innerHTML = dlBtn.dataset.origHtml;
                dlBtn.disabled = false;
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('executiveReportModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeReportPreview();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const infEl = document.getElementById('filter_influencer_id');
        const monthEl = document.getElementById('filter_month');
        const dayEl = document.getElementById('filter_day');

        if (infEl) infEl.addEventListener('change', updateFilterSummary);
        if (monthEl) monthEl.addEventListener('change', updateFilterSummary);
        if (dayEl) dayEl.addEventListener('change', function() {
            const dateEl = document.getElementById('filter_date');
            if (dateEl) dateEl.value = '';
            updateFilterSummary();
        });
        updateFilterSummary();
    });
</script>
