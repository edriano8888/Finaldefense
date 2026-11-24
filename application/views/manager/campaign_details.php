<?php
    $actual_spent = 0.0;
    $assigned_ids_list = [];
    if (!empty($assigned_influencers)) {
        foreach ($assigned_influencers as $assigned) {
            $actual_spent += (float)($assigned->product_cost ?? 0.0);
            $assigned_ids_list[] = (int)$assigned->influencer_id;
        }
    }
    $budget = (float)($campaign->budget ?? 0.0);
    $remaining_budget = $budget - $actual_spent;
    $spent_pct = $budget > 0 ? min(100, round(($actual_spent / $budget) * 100)) : 0;

    $selected_inf_id = isset($_GET['influencer_id']) ? (int)$_GET['influencer_id'] : 0;
    $active_inf_id = 0;
    if (!empty($assigned_influencers)) {
        foreach ($assigned_influencers as $inf_item) {
            if ((int)$inf_item->influencer_id === $selected_inf_id) {
                $active_inf_id = $selected_inf_id;
                break;
            }
        }
        if ($active_inf_id === 0) {
            $active_inf_id = (int)$assigned_influencers[0]->influencer_id;
        }
    }
?>

<div class="space-y-6 sm:space-y-8 pb-16">
    <!-- Breadcrumb, Back & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-3xl border border-gray-200/80 shadow-xs">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <a href="<?php echo base_url('manager/campaign_planning'); ?>" class="w-10 h-10 shrink-0 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 hover:text-brand-green hover:border-brand-green transition-all shadow-xs" title="Back to Campaign Planning">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-2xl font-black text-gray-900 truncate"><?php echo html_escape($campaign->title ?? 'Campaign Details'); ?></h1>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Campaign Strategy, Creator Deliverables & Team Task Assignment</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <button type="button" onclick="toggleDetailsAssignModal()" class="w-full sm:w-auto justify-center inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-brand-green text-brand-deep hover:text-white border border-emerald-200 rounded-xl text-xs font-extrabold transition-all cursor-pointer">
                <i class="fas fa-user-plus text-xs"></i>
                <span>Manage Creators (<?php echo count($assigned_ids_list); ?>)</span>
            </button>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-brand-green"></i>
                <span><?php echo html_escape($this->session->flashdata('success')); ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-rose-500"></i>
                <span><?php echo html_escape($this->session->flashdata('error')); ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <!-- Strategic Header & Financial Summary Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 bg-gradient-to-br from-[#111827] to-[#1F2937] p-5 sm:p-8 rounded-3xl sm:rounded-[32px] text-white shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(31,174,91,0.12),transparent_40%)]"></div>
        
        <!-- Left: Campaign Overview Meta -->
        <div class="lg:col-span-2 space-y-4 relative z-10">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <?php
                    $camp_status = $campaign->status ?? 'Planning';
                    $status_colors = [
                        'Planning' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                        'Active' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                        'Ongoing' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                        'Done' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                        'Completed' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                        'Failed' => 'bg-red-500/20 text-red-400 border-red-500/30'
                    ];
                    $badge_class = $status_colors[$camp_status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                ?>
                <span class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider border <?php echo $badge_class; ?>">
                    <?php echo html_escape($camp_status); ?>
                </span>
                <span class="text-xs text-gray-400">
                    <i class="far fa-calendar-alt mr-1"></i>
                    <?php echo !empty($campaign->start_date) ? date('M d, Y', strtotime($campaign->start_date)) : 'TBD'; ?> - 
                    <?php echo !empty($campaign->end_date) ? date('M d, Y', strtotime($campaign->end_date)) : 'TBD'; ?>
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed max-w-2xl break-words">
                <?php echo html_escape(!empty($campaign->description) ? $campaign->description : 'No campaign description provided.'); ?>
            </p>
        </div>

        <!-- Right: Financial Tracker -->
        <div class="bg-white/5 rounded-2xl border border-white/10 p-5 sm:p-6 relative z-10 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Financial Governance</span>
                    <span class="px-2 py-0.5 rounded bg-brand-green/20 text-brand-green text-[9px] font-bold shrink-0"><?php echo $spent_pct; ?>% Spent</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400">Budget</p>
                        <p class="text-base sm:text-lg font-black truncate">$<?php echo number_format($budget, 2); ?></p>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400">Spent (Actual)</p>
                        <p class="text-base sm:text-lg font-black text-brand-green truncate">$<?php echo number_format($actual_spent, 2); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Remaining</p>
                    <p class="text-sm font-bold truncate <?php echo $remaining_budget >= 0 ? 'text-emerald-400' : 'text-red-400'; ?>">
                        $<?php echo number_format($remaining_budget, 2); ?>
                    </p>
                </div>
                <div class="w-24 shrink-0 h-2 bg-white/15 rounded-full overflow-hidden">
                    <div class="h-full bg-brand-green rounded-full" style="width: <?php echo $spent_pct; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Workspace Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
        
        <!-- Left: Influencers List -->
        <div class="lg:col-span-1 space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Assigned Creators (<?php echo is_array($assigned_influencers) ? count($assigned_influencers) : 0; ?>)</h3>
                <button type="button" onclick="toggleDetailsAssignModal()" class="text-[11px] font-extrabold text-brand-green hover:text-brand-deep hover:underline cursor-pointer">
                    + Add Creator
                </button>
            </div>
            <div class="space-y-3">
                <?php if(!empty($assigned_influencers)): ?>
                    <?php foreach($assigned_influencers as $idx => $assigned): ?>
                        <?php 
                            $tasks = isset($assigned->tasks) && is_array($assigned->tasks) ? $assigned->tasks : [];
                            $total_tasks = count($tasks);
                            $done_tasks = 0;
                            foreach ($tasks as $t) {
                                if (($t->status ?? '') === 'Done') $done_tasks++;
                            }
                            $pct_complete = $total_tasks > 0 ? round(($done_tasks / $total_tasks) * 100) : 0;
                            $inf_username = !empty($assigned->username) ? $assigned->username : 'Influencer';
                            $inf_fullname = !empty($assigned->full_name) ? $assigned->full_name : $inf_username;
                            $inf_platform = !empty($assigned->platform) ? $assigned->platform : 'Instagram';
                            $inf_fallback = 'https://ui-avatars.com/api/?name=' . urlencode($inf_username) . '&background=0F6B3E&color=fff&rounded=true&bold=true';
                            $inf_avatar = !empty($assigned->profile_image) ? $assigned->profile_image : $inf_fallback;
                            $is_active_card = ((int)$assigned->influencer_id === $active_inf_id);

                            $plat_lower = strtolower($inf_platform);
                            if (strpos($plat_lower, 'tiktok') !== false) {
                                $plat_icon = 'fab fa-tiktok text-gray-900';
                            } elseif (strpos($plat_lower, 'youtube') !== false) {
                                $plat_icon = 'fab fa-youtube text-red-500';
                            } else {
                                $plat_icon = 'fab fa-instagram text-pink-500';
                            }
                        ?>
                        <button type="button" onclick="showTasks(<?php echo (int)$assigned->influencer_id; ?>)" id="btn-influencer-<?php echo (int)$assigned->influencer_id; ?>" class="influencer-btn w-full text-left bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-xs hover:border-brand-green hover:shadow-md transition-all flex flex-col gap-4 group cursor-pointer <?php echo $is_active_card ? 'ring-2 ring-brand-green' : ''; ?>">
                            <div class="flex items-start justify-between gap-3 w-full">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative shrink-0">
                                        <img src="<?php echo html_escape($inf_avatar); ?>"
                                             alt="<?php echo html_escape($inf_username); ?>"
                                             referrerpolicy="no-referrer"
                                             onerror="this.onerror=null;this.src='<?php echo html_escape($inf_fallback); ?>';"
                                             class="w-11 h-11 rounded-full border border-gray-100 shadow-xs object-cover bg-emerald-50">
                                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-white border border-gray-100 shadow-xs flex items-center justify-center">
                                            <i class="<?php echo $plat_icon; ?> text-[9px]"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-extrabold text-gray-900 group-hover:text-brand-green transition-colors truncate"><?php echo html_escape($inf_username); ?></p>
                                        <p class="text-xs text-gray-500 truncate mt-0.5"><?php echo html_escape($inf_fullname); ?></p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-gray-50 border border-gray-200 text-gray-600 shrink-0">
                                    <?php echo html_escape($inf_platform); ?>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:gap-4 w-full pt-3.5 border-t border-gray-50 text-xs">
                                <div class="min-w-0">
                                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Pipeline Status</p>
                                    <span class="inline-flex mt-1 text-gray-700 font-semibold truncate"><?php echo html_escape(!empty($assigned->pipeline_status) ? $assigned->pipeline_status : 'For Outreach'); ?></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Sample Cost</p>
                                    <span class="inline-flex mt-1 font-bold text-gray-900 truncate">$<?php echo number_format((float)($assigned->product_cost ?? 0.0), 2); ?></span>
                                </div>
                            </div>

                            <div class="w-full space-y-1.5">
                                <div class="flex justify-between text-[10px] text-gray-500 font-semibold">
                                    <span>Checklist Progress</span>
                                    <span><?php echo "$done_tasks/$total_tasks"; ?> Done</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-brand-green rounded-full transition-all duration-300" style="width: <?php echo $pct_complete; ?>%"></div>
                                </div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-3xl border border-dashed border-gray-200 p-8 text-center">
                        <i class="fas fa-user-friends text-2xl text-gray-300 mb-2"></i>
                        <p class="text-xs text-gray-400 mb-3">No influencers assigned to this campaign.</p>
                        <button type="button" onclick="toggleDetailsAssignModal()" class="px-4 py-2 bg-brand-green text-white rounded-xl text-xs font-extrabold hover:bg-brand-deep transition-all cursor-pointer">
                            + Assign Creators
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Tasks checklist list -->
        <div class="lg:col-span-2" id="checklist-workspace-pane">
            <?php if(!empty($assigned_influencers)): ?>
                <?php foreach($assigned_influencers as $idx => $assigned): ?>
                    <?php 
                        $tasks = isset($assigned->tasks) && is_array($assigned->tasks) ? $assigned->tasks : [];
                        $total_tasks = count($tasks);
                        $done_tasks = 0;
                        foreach ($tasks as $t) {
                            if (($t->status ?? '') === 'Done') $done_tasks++;
                        }
                        $pct_complete = $total_tasks > 0 ? round(($done_tasks / $total_tasks) * 100) : 0;
                        $inf_id_int = (int)$assigned->influencer_id;
                        $inf_username = !empty($assigned->username) ? $assigned->username : 'Influencer';
                        $inf_platform = !empty($assigned->platform) ? $assigned->platform : 'Instagram';
                        $inf_deal = !empty($assigned->campaign_type) ? $assigned->campaign_type : 'Outreach';
                        $inf_fallback = 'https://ui-avatars.com/api/?name=' . urlencode($inf_username) . '&background=0F6B3E&color=fff&rounded=true&bold=true';
                        $inf_avatar = !empty($assigned->profile_image) ? $assigned->profile_image : $inf_fallback;
                        $is_active_pane = ($inf_id_int === $active_inf_id);
                    ?>
                    <div id="tasks-pane-<?php echo $inf_id_int; ?>" class="task-pane bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden animate-fade-in <?php echo $is_active_pane ? '' : 'hidden'; ?>">
                        <!-- Creator Pane Header -->
                        <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="<?php echo html_escape($inf_avatar); ?>"
                                     alt="<?php echo html_escape($inf_username); ?>"
                                     referrerpolicy="no-referrer"
                                     onerror="this.onerror=null;this.src='<?php echo html_escape($inf_fallback); ?>';"
                                     class="w-11 h-11 shrink-0 rounded-full border border-white shadow-sm object-cover bg-emerald-50">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 truncate"><?php echo html_escape($inf_username); ?>'s Checklist</h4>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">Platform: <span class="font-semibold text-gray-700"><?php echo html_escape($inf_platform); ?></span> | Deal: <span class="font-semibold text-gray-700"><?php echo html_escape($inf_deal); ?></span></p>
                                </div>
                            </div>
                            <span class="self-start sm:self-auto px-3 py-1.5 rounded-full text-xs font-bold bg-green-50 text-brand-green border border-green-100 shadow-xs inline-flex items-center gap-1.5 shrink-0">
                                <span class="w-2 h-2 rounded-full bg-brand-green"></span>
                                <?php echo $pct_complete; ?>% Complete (<?php echo "$done_tasks/$total_tasks"; ?>)
                            </span>
                        </div>

                        <div class="p-4 sm:p-6 space-y-6">
                            <!-- Expanded Professional Add Task Form -->
                            <form action="<?php echo base_url('manager/add_task'); ?>" method="post" class="bg-slate-50/70 p-4 sm:p-5 rounded-2xl border border-slate-200/80 space-y-4">
                                <input type="hidden" name="campaign_id" value="<?php echo (int)$campaign->id; ?>">
                                <input type="hidden" name="influencer_id" value="<?php echo $inf_id_int; ?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs font-extrabold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-plus-circle text-brand-green"></i>
                                        Assign New Deliverable Task
                                    </p>
                                    <?php if (count($assigned_influencers) > 1): ?>
                                        <label class="inline-flex items-center gap-1.5 text-[11px] font-bold text-brand-deep bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg cursor-pointer">
                                            <input type="checkbox" name="apply_to_all" value="1" class="w-3.5 h-3.5 text-brand-green rounded border-gray-300 focus:ring-brand-green cursor-pointer">
                                            <span>Apply to all <?php echo count($assigned_influencers); ?> creators in campaign</span>
                                        </label>
                                    <?php endif; ?>
                                </div>

                                <!-- 1-Click Standard Task Presets -->
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1.5">Quick Deliverable Presets (1-Click Fill):</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" onclick="fillTaskPreset(<?php echo $inf_id_int; ?>, 'Send Direct Outreach & Campaign Brief')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer shadow-2xs">📩 Direct Outreach</button>
                                        <button type="button" onclick="fillTaskPreset(<?php echo $inf_id_int; ?>, 'Follow Up & Finalize Rate / Deliverables')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer shadow-2xs">🤝 Rate & Negotiation</button>
                                        <button type="button" onclick="fillTaskPreset(<?php echo $inf_id_int; ?>, 'Process Product Sample Order & Shipping Tracking')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer shadow-2xs">📦 Product Sample Order</button>
                                        <button type="button" onclick="fillTaskPreset(<?php echo $inf_id_int; ?>, 'Follow Up Content Draft for Review')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer shadow-2xs">🎬 Content Draft Review</button>
                                        <button type="button" onclick="fillTaskPreset(<?php echo $inf_id_int; ?>, 'Verify Live Post Link & Campaign Hashtags')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer shadow-2xs">✅ Verify Live Post</button>
                                        <button type="button" onclick="fillTaskPreset(<?php echo $inf_id_int; ?>, 'Record Post Analytics & GMV Performance')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer shadow-2xs">📊 Track Analytics</button>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-[10px] text-gray-500 font-extrabold uppercase mb-1.5">Task Description</label>
                                        <input type="text" name="task_name" id="task-name-input-<?php echo $inf_id_int; ?>" required class="w-full px-4 py-2.5 bg-white border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl text-xs font-semibold outline-none transition-all" placeholder="e.g. Confirm product package receipt">
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="block text-[10px] text-gray-500 font-extrabold uppercase">Due Date</label>
                                            <div class="flex items-center gap-1">
                                                <button type="button" onclick="fillTaskDueDate(<?php echo $inf_id_int; ?>, 0)" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-white border border-gray-200 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep cursor-pointer">Today</button>
                                                <button type="button" onclick="fillTaskDueDate(<?php echo $inf_id_int; ?>, 3)" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-white border border-gray-200 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep cursor-pointer">+3d</button>
                                                <button type="button" onclick="fillTaskDueDate(<?php echo $inf_id_int; ?>, 7)" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-white border border-gray-200 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep cursor-pointer">+7d</button>
                                            </div>
                                        </div>
                                        <input type="date" name="due_date" id="task-due-input-<?php echo $inf_id_int; ?>" class="w-full px-4 py-2.5 bg-white border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl text-xs font-semibold outline-none transition-all">
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-4 pt-1">
                                    <div class="flex-1 w-full">
                                        <label class="block text-[10px] text-gray-500 font-extrabold uppercase mb-1.5">Assign Team Member (Employee)</label>
                                        <select name="assigned_to" required class="w-full px-3.5 py-2.5 bg-white border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl text-xs font-bold text-gray-800 outline-none transition-all cursor-pointer">
                                            <option value="">-- Select Team Member --</option>
                                            <?php if (!empty($members)): ?>
                                                <?php foreach ($members as $m): ?>
                                                    <option value="<?php echo (int)$m->user_id; ?>">
                                                        <?php echo html_escape($m->name ?? 'Member'); ?> (<?php echo html_escape($m->email ?? ''); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto justify-center bg-brand-green hover:bg-brand-deep text-white px-5 py-2.5 rounded-xl font-extrabold transition-all shadow-md shadow-green-500/10 text-xs flex items-center gap-1.5 h-[38px] shrink-0 cursor-pointer">
                                        <i class="fas fa-paper-plane text-[10px]"></i> Assign Task
                                    </button>
                                </div>
                            </form>

                            <!-- Tasks List -->
                            <div class="space-y-3">
                                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">Checklist Tasks (<?php echo $total_tasks; ?>)</h5>
                                <?php if(!empty($tasks)): ?>
                                    <?php foreach($tasks as $task): ?>
                                        <?php
                                            $assignee_name = !empty($task->assignee_name) ? html_escape($task->assignee_name) : 'Unassigned';
                                            if ($assignee_name === 'Unassigned' && !empty($task->assigned_to) && !empty($members)) {
                                                foreach ($members as $m) {
                                                    if ((int)$m->user_id === (int)$task->assigned_to) {
                                                        $assignee_name = html_escape($m->name ?? 'Member');
                                                        break;
                                                    }
                                                }
                                            }
                                            $task_status = $task->status ?? 'Pending';
                                            $is_overdue = false;
                                            if ($task_status === 'Pending' && !empty($task->due_date)) {
                                                $is_overdue = strtotime($task->due_date) < strtotime('today');
                                            }
                                        ?>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-white rounded-2xl border border-gray-100 hover:border-gray-200 transition-colors group">
                                            <div class="flex items-start sm:items-center gap-3 min-w-0 flex-1">
                                                <form action="<?php echo base_url('manager/update_task_status'); ?>" method="post" class="shrink-0 mt-0.5 sm:mt-0">
                                                    <input type="hidden" name="task_id" value="<?php echo (int)$task->id; ?>">
                                                    <input type="hidden" name="campaign_id" value="<?php echo (int)$campaign->id; ?>">
                                                    <input type="hidden" name="influencer_id" value="<?php echo $inf_id_int; ?>">
                                                    <input type="hidden" name="status" value="<?php echo $task_status === 'Pending' ? 'Done' : 'Pending'; ?>">
                                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                    <button type="submit" class="w-6 h-6 rounded-lg border-2 transition-all flex items-center justify-center cursor-pointer <?php echo $task_status === 'Done' ? 'bg-brand-green border-brand-green text-white shadow-xs' : 'border-gray-200 hover:border-brand-green hover:bg-emerald-50/50'; ?>" title="<?php echo $task_status === 'Done' ? 'Mark Pending' : 'Mark Done'; ?>">
                                                        <?php if($task_status === 'Done'): ?><i class="fas fa-check text-[11px]"></i><?php endif; ?>
                                                    </button>
                                                </form>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-sm font-bold block break-words <?php echo $task_status === 'Done' ? 'text-gray-400 line-through' : 'text-gray-900'; ?>">
                                                        <?php echo html_escape($task->task_name ?? ''); ?>
                                                    </span>
                                                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                        <span class="text-[10px] font-bold text-brand-deep bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded flex items-center gap-1">
                                                            <i class="far fa-user text-[9px]"></i> <?php echo $assignee_name; ?>
                                                        </span>
                                                        <?php if(!empty($task->due_date)): ?>
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1 <?php echo $is_overdue ? 'bg-red-50 text-red-500 border border-red-100' : 'bg-gray-50 border border-gray-100 text-gray-500'; ?>">
                                                                <i class="far fa-calendar text-[9px]"></i> 
                                                                <?php echo date('M d, Y', strtotime($task->due_date)); ?> 
                                                                <?php if($is_overdue): ?>(Overdue)<?php endif; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-end gap-2 sm:gap-3 pl-9 sm:pl-0 shrink-0">
                                                <span class="text-[9px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-lg <?php echo $task_status === 'Done' ? 'bg-green-50 text-brand-green border border-green-100' : 'bg-orange-50 text-orange-500 border border-orange-100'; ?>">
                                                    <?php echo html_escape($task_status); ?>
                                                </span>
                                                <form action="<?php echo base_url('manager/delete_task'); ?>" method="post" onsubmit="return confirm('Delete this task?')">
                                                    <input type="hidden" name="task_id" value="<?php echo (int)$task->id; ?>">
                                                    <input type="hidden" name="campaign_id" value="<?php echo (int)$campaign->id; ?>">
                                                    <input type="hidden" name="influencer_id" value="<?php echo $inf_id_int; ?>">
                                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors opacity-100 sm:opacity-0 sm:group-hover:opacity-100 cursor-pointer" title="Delete Task">
                                                        <i class="far fa-trash-alt text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-12 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                        <i class="fas fa-tasks text-gray-300 text-2xl mb-2"></i>
                                        <p class="text-xs text-gray-400">No tasks added to this checklist yet. Choose a preset above to assign one!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-3xl border border-dashed border-gray-200 p-10 sm:p-16 text-center">
                    <i class="fas fa-user-tag text-gray-200 text-4xl mb-4"></i>
                    <h4 class="text-sm font-bold text-gray-700">Assign creators to start building your campaign checklist</h4>
                    <p class="text-xs text-gray-400 mt-1 mb-5">Select sourced creators from your database to manage deliverables and assign team tasks.</p>
                    <button type="button" onclick="toggleDetailsAssignModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-green text-white rounded-2xl text-xs font-extrabold hover:bg-brand-deep transition-all shadow-md cursor-pointer">
                        <i class="fas fa-user-plus"></i>
                        <span>Assign Creators Now</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Assign Creators Modal inside Campaign Details -->
<div id="details-assign-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in border border-gray-100 flex flex-col max-h-[90vh]">
        <div class="px-6 sm:px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">👥</span>
                <div>
                    <h3 class="text-base font-black text-gray-900">Manage Campaign Creators</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Campaign: <span class="text-brand-green font-bold"><?php echo html_escape($campaign->title ?? ''); ?></span></p>
                </div>
            </div>
            <button type="button" onclick="toggleDetailsAssignModal()" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form action="<?php echo base_url('manager/assign_influencers'); ?>" method="post" class="flex-1 flex flex-col min-h-0">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="campaign_id" value="<?php echo (int)$campaign->id; ?>">
            <input type="hidden" name="redirect_to" value="campaign_details">

            <div class="p-6 sm:p-8 overflow-y-auto space-y-2.5 flex-1">
                <?php if(!empty($all_influencers)): ?>
                    <?php foreach($all_influencers as $inf): ?>
                        <?php
                            $m_uname = !empty($inf->username) ? $inf->username : 'Creator';
                            $m_fallback = 'https://ui-avatars.com/api/?name=' . urlencode($m_uname) . '&background=0F6B3E&color=fff&rounded=true&bold=true';
                            $m_avatar = !empty($inf->profile_image) ? $inf->profile_image : $m_fallback;
                            $is_checked = in_array((int)$inf->id, $assigned_ids_list, true);
                        ?>
                        <label class="flex items-center justify-between p-3.5 bg-gray-50 hover:bg-emerald-50/50 border border-gray-200/70 rounded-2xl cursor-pointer transition-all group">
                            <div class="flex items-center gap-3 min-w-0">
                                <input 
                                    type="checkbox" 
                                    name="influencer_ids[]" 
                                    value="<?php echo (int)$inf->id; ?>" 
                                    <?php echo $is_checked ? 'checked' : ''; ?>
                                    class="w-4 h-4 text-brand-green rounded border-gray-300 focus:ring-brand-green cursor-pointer"
                                />
                                <img 
                                    src="<?php echo html_escape($m_avatar); ?>" 
                                    alt="<?php echo html_escape($m_uname); ?>" 
                                    referrerpolicy="no-referrer"
                                    onerror="this.onerror=null;this.src='<?php echo html_escape($m_fallback); ?>';"
                                    class="w-9 h-9 rounded-full border border-gray-200 object-cover shadow-xs flex-shrink-0 bg-emerald-50"
                                />
                                <div class="min-w-0">
                                    <p class="text-xs font-black text-gray-900 group-hover:text-brand-deep transition-colors truncate">
                                        @<?php echo html_escape($m_uname); ?>
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase truncate">
                                        <?php echo html_escape($inf->platform ?? 'Instagram'); ?>
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold bg-white text-gray-600 border border-gray-200 px-2 py-0.5 rounded-lg flex-shrink-0">
                                <?php echo html_escape($inf->pipeline_status ?? 'Active'); ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-12 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-xs font-bold text-gray-400">No influencers found in database.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-5 sm:p-6 bg-gray-50/80 border-t border-gray-100 flex gap-3 flex-shrink-0">
                <button type="button" onclick="toggleDetailsAssignModal()" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-brand-green text-white rounded-xl font-extrabold text-xs hover:bg-brand-deep transition-all shadow-md shadow-green-500/20 cursor-pointer">Save Assignments</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showTasks(infId) {
        document.querySelectorAll('.task-pane').forEach(p => p.classList.add('hidden'));
        const pane = document.getElementById('tasks-pane-' + infId);
        if (pane) pane.classList.remove('hidden');

        document.querySelectorAll('.influencer-btn').forEach(b => {
            b.classList.remove('ring-2', 'ring-brand-green');
        });
        const btn = document.getElementById('btn-influencer-' + infId);
        if (btn) btn.classList.add('ring-2', 'ring-brand-green');

        if (window.innerWidth < 1024) {
            const workspace = document.getElementById('checklist-workspace-pane');
            if (workspace) {
                workspace.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    function fillTaskPreset(infId, text) {
        const input = document.getElementById('task-name-input-' + infId);
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function fillTaskDueDate(infId, daysFromNow) {
        const input = document.getElementById('task-due-input-' + infId);
        if (!input) return;
        const d = new Date();
        d.setDate(d.getDate() + (parseInt(daysFromNow, 10) || 0));
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        input.value = `${yyyy}-${mm}-${dd}`;
    }

    function toggleDetailsAssignModal() {
        const modal = document.getElementById('details-assign-modal');
        if (modal) {
            modal.classList.toggle('hidden');
            document.body.style.overflow = modal.classList.contains('hidden') ? 'auto' : 'hidden';
        }
    }
</script>
