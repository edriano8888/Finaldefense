<div class="space-y-6 sm:space-y-8 pb-12">
    <!-- Dynamic Toast Notification (Lower Right Corner) -->
    <div id="toast-notification" class="fixed bottom-6 right-6 z-[60] hidden bg-brand-deep text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 text-xs font-extrabold border border-emerald-400/40 transition-all duration-300 transform translate-y-[20px]">
        <i class="fas fa-check-circle text-brand-green text-base" id="toast-icon"></i>
        <span id="toast-message">Success!</span>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-gray-200/80 shadow-xs">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-green to-brand-deep text-white flex items-center justify-center text-xl shadow-md shadow-green-500/20 shrink-0">
                <i class="fas fa-bullseye"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Campaign Planning & Task Hub</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-brand-deep border border-emerald-200">Operations</span>
                </div>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Create campaigns, assign creators, and delegate deliverable tasks to your team in one unified workflow.</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button onclick="toggleModal('create-campaign-modal')" class="w-full sm:w-auto justify-center inline-flex items-center gap-2 px-5 py-3 bg-brand-green text-white rounded-2xl text-xs font-extrabold hover:bg-brand-deep transition-all shadow-lg shadow-green-500/20 cursor-pointer shrink-0">
                <i class="fas fa-plus text-xs"></i>
                <span>Create New Campaign</span>
            </button>
        </div>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50/90 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl shadow-xs flex items-center justify-between text-xs font-bold animate-fade-in">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-brand-green text-sm"></i>
                <span><?php echo html_escape($this->session->flashdata('success')); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
        <div class="bg-rose-50/90 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl shadow-xs flex items-center justify-between text-xs font-bold animate-fade-in">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-exclamation-circle text-rose-500 text-sm"></i>
                <span><?php echo html_escape($this->session->flashdata('error')); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <?php
        $total_campaigns = isset($campaigns) ? count($campaigns) : 0;
        $active_count = 0;
        $planning_count = 0;
        $done_count_camp = 0;
        $failed_count_camp = 0;
        $total_budget = 0.0;
        $total_spent_all = 0.0;
        $total_creators_assigned = 0;
        $total_tasks_all = 0;
        $done_tasks_all = 0;

        if (isset($campaigns) && !empty($campaigns)) {
            foreach ($campaigns as $c) {
                $c_st = !empty($c->status) ? $c->status : 'Planning';
                if ($c_st === 'Active' || $c_st === 'Ongoing') $active_count++;
                elseif ($c_st === 'Planning') $planning_count++;
                elseif ($c_st === 'Done' || $c_st === 'Completed') $done_count_camp++;
                elseif ($c_st === 'Failed') $failed_count_camp++;

                $total_budget += (float)($c->budget ?? 0);
                $total_spent_all += (float)($c->actual_spent ?? 0);
                $total_creators_assigned += isset($c->influencer_count) ? (int)$c->influencer_count : 0;
                $total_tasks_all += isset($c->total_tasks) ? (int)$c->total_tasks : 0;
                $done_tasks_all += isset($c->done_tasks) ? (int)$c->done_tasks : 0;
            }
        }
    ?>

    <!-- Executive Stats Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider truncate">Total Campaigns</p>
                <p class="text-xl sm:text-2xl font-black text-gray-900 mt-1"><?php echo $total_campaigns; ?></p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-50 text-brand-green flex items-center justify-center text-base sm:text-lg font-bold shrink-0">
                <i class="fas fa-bullseye"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider truncate">Active Campaigns</p>
                <p class="text-xl sm:text-2xl font-black text-brand-green mt-1"><?php echo $active_count; ?></p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-green-50 text-emerald-600 flex items-center justify-center text-base sm:text-lg font-bold shrink-0">
                <i class="fas fa-rocket"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider truncate">Total Budget</p>
                <p class="text-xl sm:text-2xl font-black text-gray-900 mt-1 truncate">$<?php echo number_format($total_budget, 2); ?></p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-base sm:text-lg font-bold shrink-0">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider truncate">Assigned Creators</p>
                <p class="text-xl sm:text-2xl font-black text-purple-700 mt-1"><?php echo $total_creators_assigned; ?></p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-base sm:text-lg font-bold shrink-0">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="col-span-2 lg:col-span-1 bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider truncate">Checklist Tasks</p>
                <p class="text-xl sm:text-2xl font-black text-amber-600 mt-1"><?php echo $done_tasks_all; ?><span class="text-sm text-gray-400 font-bold">/<?php echo $total_tasks_all; ?></span></p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-base sm:text-lg font-bold shrink-0">
                <i class="fas fa-tasks"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-3.5 sm:p-4 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
        <!-- Search Input -->
        <div class="relative flex-1 max-w-full lg:max-w-md">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input 
                type="text" 
                id="campaign-search-input" 
                onkeyup="filterCampaigns()" 
                placeholder="Search campaigns by title, description, or creator..." 
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all"
            />
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
            <span class="text-[10px] font-extrabold uppercase text-gray-400 mr-1 tracking-wider shrink-0">Status:</span>
            <button onclick="filterByStatus('all')" class="filter-tab px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all bg-brand-deep text-white shadow-xs shrink-0 cursor-pointer" data-status="all">All (<?php echo $total_campaigns; ?>)</button>
            <button onclick="filterByStatus('Planning')" class="filter-tab px-3.5 py-1.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all shrink-0 cursor-pointer" data-status="Planning">Planning (<?php echo $planning_count; ?>)</button>
            <button onclick="filterByStatus('Active')" class="filter-tab px-3.5 py-1.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all shrink-0 cursor-pointer" data-status="Active">Active (<?php echo $active_count; ?>)</button>
            <button onclick="filterByStatus('Done')" class="filter-tab px-3.5 py-1.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all shrink-0 cursor-pointer" data-status="Done">Done (<?php echo $done_count_camp; ?>)</button>
            <button onclick="filterByStatus('Failed')" class="filter-tab px-3.5 py-1.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all shrink-0 cursor-pointer" data-status="Failed">Failed (<?php echo $failed_count_camp; ?>)</button>
        </div>
    </div>

    <!-- Campaign Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="campaigns-container">
        <?php if(isset($campaigns) && !empty($campaigns)): ?>
            <?php foreach($campaigns as $campaign): ?>
                <?php 
                    $st = !empty($campaign->status) ? $campaign->status : 'Planning';
                    $status_configs = [
                        'Planning' => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
                        'Active' => ['badge' => 'bg-emerald-50 text-brand-deep border-emerald-200', 'dot' => 'bg-brand-green'],
                        'Ongoing' => ['badge' => 'bg-emerald-50 text-brand-deep border-emerald-200', 'dot' => 'bg-brand-green'],
                        'Done' => ['badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500'],
                        'Completed' => ['badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500'],
                        'Pending' => ['badge' => 'bg-gray-50 text-gray-600 border-gray-200', 'dot' => 'bg-gray-400'],
                        'Failed' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500']
                    ];
                    $cfg = $status_configs[$st] ?? ['badge' => 'bg-gray-50 text-gray-600 border-gray-200', 'dot' => 'bg-gray-400'];

                    $assigned_creators = isset($campaign->influencers) ? $campaign->influencers : [];
                    $assigned_ids_json = isset($campaign->assigned_influencer_ids) ? htmlspecialchars(json_encode($campaign->assigned_influencer_ids), ENT_QUOTES, 'UTF-8') : '[]';

                    $creator_names_search = '';
                    $creators_for_js = [];
                    foreach ($assigned_creators as $ac) {
                        $creator_names_search .= ' ' . strtolower(($ac->username ?? '') . ' ' . ($ac->full_name ?? ''));
                        $creators_for_js[] = [
                            'influencer_id' => (int)($ac->influencer_id ?? 0),
                            'username' => !empty($ac->username) ? $ac->username : 'Creator',
                            'platform' => !empty($ac->platform) ? $ac->platform : 'Instagram'
                        ];
                    }
                    $creators_js_json = htmlspecialchars(json_encode($creators_for_js), ENT_QUOTES, 'UTF-8');

                    $c_total_tasks = (int)($campaign->total_tasks ?? 0);
                    $c_done_tasks = (int)($campaign->done_tasks ?? 0);
                    $c_pending_tasks = (int)($campaign->pending_tasks ?? 0);
                    $c_task_pct = (int)($campaign->task_progress_pct ?? 0);
                    $c_spent = (float)($campaign->actual_spent ?? 0.0);
                    $c_budget = (float)($campaign->budget ?? 0.0);

                    $campaign_edit_payload = htmlspecialchars(json_encode([
                        'id' => $campaign->id,
                        'title' => $campaign->title,
                        'description' => $campaign->description,
                        'budget' => $campaign->budget,
                        'status' => $campaign->status,
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="campaign-card bg-white rounded-3xl border border-gray-200/80 shadow-xs hover:shadow-lg hover:border-emerald-200 transition-all overflow-hidden flex flex-col group"
                     data-title="<?php echo strtolower(html_escape($campaign->title)); ?>"
                     data-desc="<?php echo strtolower(html_escape(($campaign->description ?? '') . $creator_names_search)); ?>"
                     data-status="<?php echo html_escape($st); ?>">
                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-5">
                        
                        <!-- Top Row: Interactive Status Dropdown & Actions -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="relative inline-flex items-center">
                                <select 
                                    onchange="changeCampaignStatus(<?php echo (int)$campaign->id; ?>, this.value, this)"
                                    class="status-select appearance-none pl-3 pr-7 py-1.5 rounded-xl text-[11px] font-extrabold uppercase tracking-wider border cursor-pointer transition-all outline-none <?php echo $cfg['badge']; ?>"
                                >
                                    <option value="Planning" <?php echo $st === 'Planning' ? 'selected' : ''; ?>>⚡ Planning</option>
                                    <option value="Active" <?php echo ($st === 'Active' || $st === 'Ongoing') ? 'selected' : ''; ?>>🚀 Active</option>
                                    <option value="Done" <?php echo ($st === 'Done' || $st === 'Completed') ? 'selected' : ''; ?>>✅ Done</option>
                                    <option value="Failed" <?php echo $st === 'Failed' ? 'selected' : ''; ?>>❌ Failed</option>
                                </select>
                                <div class="pointer-events-none absolute right-2 text-[9px] text-gray-500">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            <!-- Card Top Action Buttons -->
                            <div class="flex items-center gap-1.5">
                                <button 
                                    type="button"
                                    onclick='openEditModal(<?php echo $campaign_edit_payload; ?>)' 
                                    class="w-8 h-8 rounded-xl bg-gray-50 hover:bg-emerald-50 text-gray-500 hover:text-brand-deep border border-gray-100 flex items-center justify-center text-xs transition-colors cursor-pointer"
                                    title="Edit Campaign Details"
                                >
                                    <i class="fas fa-pen"></i>
                                </button>
                                <a 
                                    href="<?php echo base_url('manager/campaign_details/' . $campaign->id); ?>" 
                                    class="w-8 h-8 rounded-xl bg-gray-50 hover:bg-emerald-50 text-gray-500 hover:text-brand-deep border border-gray-100 flex items-center justify-center text-xs transition-colors"
                                    title="Open Full Campaign Workspace"
                                >
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button 
                                    type="button"
                                    onclick="confirmDeleteCampaign(<?php echo (int)$campaign->id; ?>, '<?php echo addslashes(html_escape($campaign->title)); ?>')" 
                                    class="w-8 h-8 rounded-xl bg-gray-50 hover:bg-rose-50 text-gray-400 hover:text-rose-600 border border-gray-100 flex items-center justify-center text-xs transition-colors cursor-pointer"
                                    title="Delete Campaign"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Campaign Header Info -->
                        <div>
                            <a href="<?php echo base_url('manager/campaign_details/' . $campaign->id); ?>" class="block group/title">
                                <h3 class="text-base sm:text-lg font-black text-gray-900 group-hover/title:text-brand-green transition-colors line-clamp-1">
                                    <?php echo html_escape($campaign->title); ?>
                                </h3>
                            </a>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed min-h-[34px]">
                                <?php echo html_escape(!empty($campaign->description) ? $campaign->description : 'No campaign description provided.'); ?>
                            </p>
                        </div>

                        <!-- Assigned Creators Section (Clickable Chips) -->
                        <div class="pt-3 border-t border-gray-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Assigned Creators (<?php echo count($assigned_creators); ?>)</span>
                                <button type="button" onclick="openAssignModal(<?php echo (int)$campaign->id; ?>, '<?php echo addslashes(html_escape($campaign->title)); ?>', <?php echo $assigned_ids_json; ?>)" class="text-[11px] font-extrabold text-brand-green hover:text-brand-deep hover:underline cursor-pointer flex items-center gap-1">
                                    <i class="fas fa-user-plus text-[9px]"></i> Manage
                                </button>
                            </div>

                            <?php if (!empty($assigned_creators)): ?>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <?php 
                                        $shown = array_slice($assigned_creators, 0, 4);
                                        foreach ($shown as $c_inf): 
                                            $c_uname = !empty($c_inf->username) ? $c_inf->username : 'Creator';
                                            $c_fallback = 'https://ui-avatars.com/api/?name=' . urlencode($c_uname) . '&background=0F6B3E&color=fff&rounded=true&bold=true';
                                            $c_avatar = !empty($c_inf->profile_image) ? $c_inf->profile_image : $c_fallback;
                                    ?>
                                        <a href="<?php echo base_url('manager/campaign_details/' . $campaign->id . '?influencer_id=' . (int)$c_inf->influencer_id); ?>"
                                           title="Open @<?php echo html_escape($c_uname); ?>'s Checklist"
                                           class="inline-flex items-center gap-1.5 pl-1 pr-2.5 py-1 rounded-full bg-gray-50 hover:bg-emerald-50 border border-gray-200/80 hover:border-emerald-200 transition-all text-[11px] font-bold text-gray-700 hover:text-brand-deep max-w-[135px]">
                                            <img 
                                                src="<?php echo html_escape($c_avatar); ?>" 
                                                alt="<?php echo html_escape($c_uname); ?>" 
                                                referrerpolicy="no-referrer"
                                                onerror="this.onerror=null;this.src='<?php echo html_escape($c_fallback); ?>';"
                                                class="h-5 w-5 rounded-full object-cover shrink-0 bg-emerald-50"
                                            />
                                            <span class="truncate">@<?php echo html_escape($c_uname); ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                    <?php if (count($assigned_creators) > 4): ?>
                                        <a href="<?php echo base_url('manager/campaign_details/' . $campaign->id); ?>" class="text-[10px] font-extrabold text-brand-deep bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-full hover:bg-emerald-100 transition-colors">
                                            +<?php echo count($assigned_creators) - 4; ?> more
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center justify-between bg-amber-50/60 border border-amber-100 rounded-xl px-3 py-2">
                                    <span class="text-[11px] text-amber-700 font-semibold">No creators assigned yet</span>
                                    <button type="button" onclick="openAssignModal(<?php echo (int)$campaign->id; ?>, '<?php echo addslashes(html_escape($campaign->title)); ?>', [])" class="text-[11px] font-extrabold text-amber-800 underline cursor-pointer">Assign Now</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Financial & Timeline Row -->
                        <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50/80 rounded-2xl border border-gray-100">
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Budget / Spent</p>
                                <p class="text-xs sm:text-sm font-black text-gray-900 mt-0.5 truncate">
                                    $<?php echo number_format($c_budget, 2); ?>
                                    <span class="text-[10px] font-bold text-brand-green">($<?php echo number_format($c_spent, 0); ?>)</span>
                                </p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Timeline</p>
                                <p class="text-xs font-bold text-gray-700 mt-0.5 truncate">
                                    <?php echo !empty($campaign->start_date) ? date('M d', strtotime($campaign->start_date)) : 'TBD'; ?> - 
                                    <?php echo !empty($campaign->end_date) ? date('M d, Y', strtotime($campaign->end_date)) : 'TBD'; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Checklist Tasks Progress Bar -->
                        <div class="space-y-1.5 bg-slate-50/70 p-3 rounded-2xl border border-slate-100">
                            <div class="flex justify-between items-center text-[10px] font-bold">
                                <span class="text-gray-600 flex items-center gap-1.5">
                                    <i class="fas fa-clipboard-check text-brand-green"></i>
                                    Deliverable Checklist
                                </span>
                                <span class="<?php echo $c_total_tasks > 0 ? 'text-brand-deep font-extrabold' : 'text-gray-400'; ?>">
                                    <?php echo $c_done_tasks; ?>/<?php echo $c_total_tasks; ?> Tasks Done (<?php echo $c_task_pct; ?>%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200/70 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-brand-green h-full rounded-full transition-all duration-500" style="width: <?php echo $c_task_pct; ?>%"></div>
                            </div>
                        </div>

                        <!-- Bottom 3 Professional Action Buttons -->
                        <div class="grid grid-cols-3 gap-2 pt-1">
                            <button 
                                type="button"
                                onclick="openAssignModal(<?php echo (int)$campaign->id; ?>, '<?php echo addslashes(html_escape($campaign->title)); ?>', <?php echo $assigned_ids_json; ?>)" 
                                class="py-2.5 px-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-[11px] font-extrabold transition-all border border-gray-200/80 flex items-center justify-center gap-1.5 cursor-pointer"
                                title="Assign or remove creators"
                            >
                                <i class="fas fa-user-plus text-[10px] text-gray-500"></i>
                                <span class="truncate">Creators</span>
                            </button>

                            <button 
                                type="button"
                                onclick='openQuickTaskModal(<?php echo (int)$campaign->id; ?>, <?php echo json_encode($campaign->title); ?>, <?php echo $creators_js_json; ?>)' 
                                class="py-2.5 px-2 bg-emerald-50 text-brand-deep hover:bg-brand-green hover:text-white rounded-xl text-[11px] font-extrabold transition-all border border-emerald-200/80 flex items-center justify-center gap-1.5 cursor-pointer"
                                title="Quickly assign a task to a team member"
                            >
                                <i class="fas fa-plus-circle text-[10px]"></i>
                                <span class="truncate">Assign Task</span>
                            </button>

                            <a 
                                href="<?php echo base_url('manager/campaign_details/' . $campaign->id); ?>" 
                                class="py-2.5 px-2 bg-brand-deep hover:bg-black text-white rounded-xl text-[11px] font-extrabold transition-all flex items-center justify-center gap-1.5 shadow-xs"
                                title="Open Full Checklist & Strategy Workspace"
                            >
                                <i class="fas fa-tasks text-[10px]"></i>
                                <span class="truncate">Workspace</span>
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-20 px-4 text-center bg-white rounded-3xl border border-dashed border-gray-200 shadow-xs">
                <div class="w-16 h-16 bg-emerald-50 text-brand-green rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="text-base font-black text-gray-900 mb-1">No campaigns created yet</h3>
                <p class="text-xs text-gray-400 max-w-sm mx-auto mb-6">Launch your first marketing campaign to assign influencers, delegate deliverable tasks, and manage budgets.</p>
                <button onclick="toggleModal('create-campaign-modal')" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-green text-white rounded-2xl text-xs font-extrabold hover:bg-brand-deep transition-all shadow-md cursor-pointer">
                    <i class="fas fa-plus"></i>
                    <span>Create Campaign Now</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Assign Task Modal (Direct from Campaign Planning) -->
<div id="quick-task-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden animate-fade-in border border-gray-100 my-8">
        <div class="px-6 sm:px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-900 to-[#0F6B3E] text-white">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-emerald-300 shrink-0">
                    <i class="fas fa-clipboard-list text-base"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-black truncate">Assign Campaign Task</h3>
                    <p class="text-[11px] text-emerald-200 truncate mt-0.5">Campaign: <span id="qt-campaign-title" class="font-bold text-white"></span></p>
                </div>
            </div>
            <button type="button" onclick="toggleModal('quick-task-modal')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form action="<?php echo base_url('manager/add_task'); ?>" method="post" class="p-6 sm:p-8 space-y-5">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="campaign_id" id="qt-campaign-id">
            <input type="hidden" name="redirect_to" value="campaign_planning">

            <!-- Step 1: Select Creator Target -->
            <div>
                <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider mb-1.5">1. Target Creator(s)</label>
                <select name="influencer_id" id="qt-influencer-select" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all cursor-pointer">
                </select>
                <p id="qt-no-creator-warning" class="hidden text-[11px] text-amber-600 font-bold mt-1.5">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Please assign at least one creator to this campaign first before adding tasks.
                </p>
            </div>

            <!-- Step 2: Quick Task Presets + Custom Description -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider">2. Deliverable / Task Description</label>
                    <span class="text-[10px] text-brand-green font-bold">Click a preset or type custom</span>
                </div>
                <div class="flex flex-wrap gap-1.5 mb-2.5">
                    <button type="button" onclick="setQuickTaskPreset('Send Direct Outreach & Campaign Brief')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer">📩 Direct Outreach</button>
                    <button type="button" onclick="setQuickTaskPreset('Follow Up & Finalize Rate / Deliverables')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer">🤝 Rate & Negotiation</button>
                    <button type="button" onclick="setQuickTaskPreset('Process Product Sample Order & Shipping Tracking')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer">📦 Product Sample Order</button>
                    <button type="button" onclick="setQuickTaskPreset('Follow Up Content Draft for Review')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer">🎬 Content Draft Review</button>
                    <button type="button" onclick="setQuickTaskPreset('Verify Live Post Link & Campaign Hashtags')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer">✅ Verify Live Post</button>
                    <button type="button" onclick="setQuickTaskPreset('Record Post Analytics & GMV Performance')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-brand-deep border border-gray-200 hover:border-emerald-200 text-[10px] font-bold transition-all cursor-pointer">📊 Track Analytics</button>
                </div>
                <input type="text" name="task_name" id="qt-task-name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all" placeholder="e.g. Confirm product package receipt & filming schedule">
            </div>

            <!-- Step 3: Team Member & Due Date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider mb-1.5">3. Assign To Team Member</label>
                    <select name="assigned_to" required class="w-full px-3.5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all cursor-pointer">
                        <option value="">-- Select Team Member --</option>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $m): ?>
                                <option value="<?php echo (int)($m->user_id ?? 0); ?>">
                                    <?php echo html_escape(!empty($m->name) ? $m->name : 'Member'); ?> (<?php echo html_escape($m->email ?? ''); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider">4. Due Date</label>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="setQuickDueDate('qt-due-date', 0)" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep cursor-pointer">Today</button>
                            <button type="button" onclick="setQuickDueDate('qt-due-date', 3)" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep cursor-pointer">+3d</button>
                            <button type="button" onclick="setQuickDueDate('qt-due-date', 7)" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-brand-deep cursor-pointer">+7d</button>
                        </div>
                    </div>
                    <input type="date" name="due_date" id="qt-due-date" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all">
                </div>
            </div>

            <div class="pt-3 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="toggleModal('quick-task-modal')" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all cursor-pointer">Cancel</button>
                <button type="submit" id="qt-submit-btn" class="flex-1 py-3 bg-brand-green text-white rounded-xl font-extrabold text-xs hover:bg-brand-deep transition-all shadow-md shadow-green-500/20 cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Assign Task Now</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create Campaign Modal -->
<div id="create-campaign-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in border border-gray-100 my-8">
        <div class="px-6 sm:px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">🚀</span>
                <div>
                    <h3 class="text-base font-black text-gray-900">Create New Campaign</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Define objectives, timeline, and budget for your brand.</p>
                </div>
            </div>
            <button onclick="toggleModal('create-campaign-modal')" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <form action="<?php echo base_url('manager/create_campaign'); ?>" method="post" class="p-6 sm:p-8 space-y-5">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Campaign Title</label>
                <input type="text" name="title" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all" placeholder="e.g. Summer Product Launch 2026">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Campaign Goal / Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all leading-relaxed" placeholder="Describe key deliverables, targets, or influencer guidelines..."></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Budget ($)</label>
                    <input type="number" name="budget" step="0.01" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all" placeholder="1500.00">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Initial Status</label>
                    <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all cursor-pointer">
                        <option value="Planning">⚡ Planning</option>
                        <option value="Active">🚀 Active</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Start Date</label>
                    <input type="date" name="start_date" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">End Date</label>
                    <input type="date" name="end_date" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all">
                </div>
            </div>
            <div class="pt-3 flex gap-3">
                <button type="button" onclick="toggleModal('create-campaign-modal')" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-brand-green text-white rounded-xl font-extrabold text-xs hover:bg-brand-deep transition-all shadow-md shadow-green-500/20 cursor-pointer">Launch Campaign</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Campaign Modal -->
<div id="edit-campaign-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in border border-gray-100 my-8">
        <div class="px-6 sm:px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">✏️</span>
                <div>
                    <h3 class="text-base font-black text-gray-900">Edit Campaign</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Update configuration for <span id="edit_display_title" class="text-brand-green font-bold"></span>.</p>
                </div>
            </div>
            <button onclick="toggleModal('edit-campaign-modal')" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <form action="<?php echo base_url('manager/update_campaign'); ?>" method="post" class="p-6 sm:p-8 space-y-5">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" id="edit_campaign_id">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Campaign Title</label>
                <input type="text" name="title" id="edit_campaign_title" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" id="edit_campaign_desc" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all leading-relaxed"></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Budget ($)</label>
                    <input type="number" name="budget" id="edit_campaign_budget" step="0.01" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Current Status</label>
                    <select name="status" id="edit_campaign_status" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all cursor-pointer">
                        <option value="Planning">⚡ Planning</option>
                        <option value="Active">🚀 Active</option>
                        <option value="Done">✅ Done</option>
                        <option value="Failed">❌ Failed</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Start Date</label>
                    <input type="date" name="start_date" id="edit_campaign_start" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">End Date</label>
                    <input type="date" name="end_date" id="edit_campaign_end" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-brand-green transition-all">
                </div>
            </div>
            <div class="pt-3 flex gap-3">
                <button type="button" onclick="toggleModal('edit-campaign-modal')" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-brand-deep text-white rounded-xl font-extrabold text-xs hover:bg-black transition-all shadow-md cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Influencers Modal -->
<div id="assign-influencers-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in border border-gray-100 flex flex-col max-h-[90vh]">
        <div class="px-6 sm:px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">👥</span>
                <div>
                    <h3 class="text-base font-black text-gray-900">Assign Creators</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Select creators for <span id="assign-campaign-title" class="text-brand-green font-bold"></span>.</p>
                </div>
            </div>
            <button onclick="toggleModal('assign-influencers-modal')" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form action="<?php echo base_url('manager/assign_influencers'); ?>" method="post" class="flex-1 flex flex-col min-h-0">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="campaign_id" id="assign-campaign-id">
            
            <!-- Quick Creator Search & Actions Bar -->
            <div class="px-6 sm:px-8 pt-4 pb-3 bg-white flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 flex-shrink-0">
                <div class="relative flex-1 min-w-[180px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input 
                        type="text" 
                        id="assign-creator-search" 
                        onkeyup="filterAssignCreators()" 
                        placeholder="Search creator name or username..." 
                        class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs outline-none focus:border-brand-green"
                    />
                </div>
                <div class="flex items-center gap-2 text-[11px]">
                    <span id="selected-creators-count" class="px-2 py-0.5 rounded-md bg-emerald-50 text-brand-deep font-extrabold">0 selected</span>
                    <button type="button" onclick="selectAllCreators(true)" class="text-brand-green hover:underline font-bold cursor-pointer">Select All</button>
                    <span class="text-gray-300">•</span>
                    <button type="button" onclick="selectAllCreators(false)" class="text-gray-500 hover:underline font-medium cursor-pointer">Clear</button>
                </div>
            </div>

            <!-- Scrollable Creators List -->
            <div class="p-6 sm:p-8 overflow-y-auto space-y-2.5 custom-scrollbar flex-1" id="assign-creators-list">
                <?php if(isset($influencers) && !empty($influencers)): ?>
                    <?php foreach($influencers as $inf): ?>
                        <?php
                            $modal_uname = !empty($inf->username) ? $inf->username : 'Creator';
                            $modal_fallback = 'https://ui-avatars.com/api/?name=' . urlencode($modal_uname) . '&background=0F6B3E&color=fff&rounded=true&bold=true';
                            $modal_avatar = !empty($inf->profile_image) ? $inf->profile_image : $modal_fallback;
                        ?>
                        <label class="assign-creator-item flex items-center justify-between p-3.5 bg-gray-50 hover:bg-emerald-50/50 border border-gray-200/70 rounded-2xl cursor-pointer transition-all group" data-search="<?php echo strtolower(html_escape(($inf->username ?? '').' '.($inf->full_name ?? '').' '.($inf->platform ?? ''))); ?>">
                            <div class="flex items-center gap-3 min-w-0">
                                <input 
                                    type="checkbox" 
                                    name="influencer_ids[]" 
                                    value="<?php echo (int)$inf->id; ?>" 
                                    onchange="updateSelectedCreatorsCount()"
                                    class="creator-checkbox w-4 h-4 text-brand-green rounded border-gray-300 focus:ring-brand-green cursor-pointer"
                                />
                                <img 
                                    src="<?php echo html_escape($modal_avatar); ?>" 
                                    alt="<?php echo html_escape($modal_uname); ?>" 
                                    referrerpolicy="no-referrer"
                                    onerror="this.onerror=null;this.src='<?php echo html_escape($modal_fallback); ?>';"
                                    class="w-9 h-9 rounded-full border border-gray-200 object-cover shadow-xs flex-shrink-0 bg-emerald-50"
                                />
                                <div class="min-w-0">
                                    <p class="text-xs font-black text-gray-900 group-hover:text-brand-deep transition-colors truncate">
                                        @<?php echo html_escape($modal_uname); ?>
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase truncate">
                                        <?php echo html_escape(!empty($inf->platform) ? $inf->platform : 'Instagram'); ?>
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold bg-white text-gray-600 border border-gray-200 px-2 py-0.5 rounded-lg flex-shrink-0">
                                <?php echo html_escape(!empty($inf->pipeline_status) ? $inf->pipeline_status : 'Active'); ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-12 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-xs font-bold text-gray-400">No influencers found in database.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer Buttons -->
            <div class="p-5 sm:p-6 bg-gray-50/80 border-t border-gray-100 flex gap-3 flex-shrink-0">
                <button type="button" onclick="toggleModal('assign-influencers-modal')" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-brand-green text-white rounded-xl font-extrabold text-xs hover:bg-brand-deep transition-all shadow-md shadow-green-500/20 cursor-pointer">Save Assignments</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.currentCsrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    window.csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';

    function updateCsrfToken(newToken) {
        if (!newToken) return;
        window.currentCsrfHash = newToken;
        document.querySelectorAll(`input[name="${window.csrfTokenName}"]`).forEach(input => {
            input.value = newToken;
        });
    }

    function showToast(msg, isError = false) {
        if (typeof window.showToast === 'function') {
            window.showToast(isError ? 'Notice' : 'Campaign Updated', msg, isError ? 'error' : 'success');
        }
    }

    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }
    }

    function updateSelectedCreatorsCount() {
        const checked = document.querySelectorAll('.creator-checkbox:checked').length;
        const badge = document.getElementById('selected-creators-count');
        if (badge) {
            badge.textContent = `${checked} selected`;
        }
    }

    function openAssignModal(id, title, assignedIds = []) {
        document.getElementById('assign-campaign-id').value = id;
        document.getElementById('assign-campaign-title').innerText = title;

        const checkboxes = document.querySelectorAll('.creator-checkbox');
        checkboxes.forEach(cb => {
            const infId = parseInt(cb.value);
            cb.checked = Array.isArray(assignedIds) && assignedIds.includes(infId);
        });
        updateSelectedCreatorsCount();

        const searchInput = document.getElementById('assign-creator-search');
        if (searchInput) {
            searchInput.value = '';
            filterAssignCreators();
        }

        toggleModal('assign-influencers-modal');
    }

    function openQuickTaskModal(campaignId, campaignTitle, creators = []) {
        document.getElementById('qt-campaign-id').value = campaignId;
        document.getElementById('qt-campaign-title').textContent = campaignTitle;
        document.getElementById('qt-task-name').value = '';

        const select = document.getElementById('qt-influencer-select');
        const warning = document.getElementById('qt-no-creator-warning');
        const submitBtn = document.getElementById('qt-submit-btn');
        select.innerHTML = '';

        if (Array.isArray(creators) && creators.length > 0) {
            warning.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'pointer-events-none');

            if (creators.length > 1) {
                const allOpt = document.createElement('option');
                allOpt.value = 'all';
                allOpt.textContent = `🌟 All Assigned Creators (${creators.length} Creators)`;
                select.appendChild(allOpt);
            }

            creators.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.influencer_id;
                opt.textContent = `@${c.username} (${c.platform || 'Instagram'})`;
                select.appendChild(opt);
            });
        } else {
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '-- No creators assigned to this campaign --';
            select.appendChild(emptyOpt);
            warning.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'pointer-events-none');
        }

        setQuickDueDate('qt-due-date', 3);
        toggleModal('quick-task-modal');
    }

    function setQuickTaskPreset(text) {
        const input = document.getElementById('qt-task-name');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function setQuickDueDate(inputId, daysFromNow) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const d = new Date();
        d.setDate(d.getDate() + (parseInt(daysFromNow, 10) || 0));
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        input.value = `${yyyy}-${mm}-${dd}`;
    }

    function openEditModal(campaign) {
        document.getElementById('edit_campaign_id').value = campaign.id;
        document.getElementById('edit_campaign_title').value = campaign.title;
        document.getElementById('edit_display_title').textContent = campaign.title;
        document.getElementById('edit_campaign_desc').value = campaign.description || '';
        document.getElementById('edit_campaign_budget').value = campaign.budget || 0;
        document.getElementById('edit_campaign_status').value = campaign.status || 'Planning';
        document.getElementById('edit_campaign_start').value = campaign.start_date || '';
        document.getElementById('edit_campaign_end').value = campaign.end_date || '';
        toggleModal('edit-campaign-modal');
    }

    async function changeCampaignStatus(campaignId, newStatus, selectElem) {
        const formData = new FormData();
        formData.append('id', campaignId);
        formData.append('status', newStatus);
        formData.append('ajax', '1');
        formData.append(window.csrfTokenName, window.currentCsrfHash);

        try {
            const response = await fetch('<?php echo base_url("manager/update_campaign_status"); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const res = await response.json();

            if (res.csrf_token) {
                updateCsrfToken(res.csrf_token);
            }

            if (res.status === 'success') {
                showToast(`Campaign status updated to '${newStatus}'`);
                const badgeClasses = {
                    'Planning': 'bg-amber-50 text-amber-700 border-amber-200',
                    'Active': 'bg-emerald-50 text-brand-deep border-emerald-200',
                    'Ongoing': 'bg-emerald-50 text-brand-deep border-emerald-200',
                    'Done': 'bg-blue-50 text-blue-700 border-blue-200',
                    'Failed': 'bg-rose-50 text-rose-700 border-rose-200'
                };
                selectElem.className = `status-select appearance-none pl-3 pr-7 py-1.5 rounded-xl text-[11px] font-extrabold uppercase tracking-wider border cursor-pointer transition-all outline-none ${badgeClasses[newStatus] || 'bg-gray-50 text-gray-600 border-gray-200'}`;
                
                const card = selectElem.closest('.campaign-card');
                if (card) {
                    card.setAttribute('data-status', newStatus);
                }
            } else {
                showToast(res.message || 'Failed to update status', true);
            }
        } catch (err) {
            console.error(err);
            showToast('Error updating status', true);
        }
    }

    async function confirmDeleteCampaign(campaignId, campaignTitle) {
        if (confirm(`Are you sure you want to delete the campaign "${campaignTitle}"?`)) {
            const formData = new FormData();
            formData.append('id', campaignId);
            formData.append('ajax', '1');
            formData.append(window.csrfTokenName, window.currentCsrfHash);

            try {
                const response = await fetch('<?php echo base_url("manager/delete_campaign"); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const res = await response.json();

                if (res.csrf_token) {
                    updateCsrfToken(res.csrf_token);
                }

                if (res.status === 'success') {
                    showToast(`Campaign "${campaignTitle}" deleted!`);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(res.message || 'Failed to delete campaign', true);
                }
            } catch (err) {
                console.error(err);
                showToast('Error deleting campaign', true);
            }
        }
    }

    function filterCampaigns() {
        const query = document.getElementById('campaign-search-input').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.campaign-card');

        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const desc = card.getAttribute('data-desc') || '';
            if (title.includes(query) || desc.includes(query)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function filterByStatus(status) {
        const tabs = document.querySelectorAll('.filter-tab');
        tabs.forEach(tab => {
            if (tab.getAttribute('data-status') === status) {
                tab.className = 'filter-tab px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all bg-brand-deep text-white shadow-xs shrink-0 cursor-pointer';
            } else {
                tab.className = 'filter-tab px-3.5 py-1.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all shrink-0 cursor-pointer';
            }
        });

        const cards = document.querySelectorAll('.campaign-card');
        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            if (status === 'all' || cardStatus === status || (status === 'Active' && cardStatus === 'Ongoing') || (status === 'Done' && cardStatus === 'Completed')) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function filterAssignCreators() {
        const query = (document.getElementById('assign-creator-search').value || '').toLowerCase().trim();
        const items = document.querySelectorAll('.assign-creator-item');
        items.forEach(item => {
            const text = item.getAttribute('data-search') || '';
            if (text.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function selectAllCreators(select) {
        const checkboxes = document.querySelectorAll('.creator-checkbox');
        checkboxes.forEach(cb => {
            if (!cb.closest('.assign-creator-item').classList.contains('hidden')) {
                cb.checked = select;
            }
        });
        updateSelectedCreatorsCount();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.fixed').forEach(m => {
                if (m.id !== 'toast-notification') m.classList.add('hidden');
            });
            document.body.style.overflow = 'auto';
        }
    });
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f9fafb;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
}
</style>
