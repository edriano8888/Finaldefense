<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/influencer.css'); ?>" rel="stylesheet">
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
                    },
                    boxShadow: {
                        'card': '0 2px 5px -1px rgba(0, 0, 0, 0.05), 0 1px 3px -1px rgba(0, 0, 0, 0.03)',
                        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)',
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">
    <?php
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
        $available_brands = $this->Brand_model->get_all_brands();
        $status = isset($filter_status) ? (string)$filter_status : '';
        $user_email = (string)$this->session->userdata('email');
    ?>

    <?php $this->load->view('partials/sidebar'); ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Influencer', 'child' => 'My Tasks']); ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 sm:p-6 md:p-8 scroll-smooth relative">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-green-50/50 to-transparent -z-10"></div>
            
            <div class="max-w-7xl mx-auto relative">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-deep tracking-tight">My Tasks</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">Manage and track your assigned campaign activities.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <form method="get" action="<?php echo base_url('influencer/my_tasks'); ?>" class="flex items-center justify-between sm:justify-start gap-2 bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100 w-full sm:w-auto">
                            <select name="status" onchange="this.form.submit()" class="flex-1 sm:flex-initial pl-3 sm:pl-4 pr-8 py-2 rounded-xl border-none bg-transparent text-xs sm:text-sm font-bold text-gray-700 focus:ring-0 cursor-pointer outline-none">
                                <option value="" <?php echo $status === '' ? 'selected' : ''; ?>>All Tasks</option>
                                <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending Only</option>
                                <option value="Done" <?php echo $status === 'Done' ? 'selected' : ''; ?>>Completed Only</option>
                            </select>
                            <button type="submit" class="px-4 sm:px-5 py-2 rounded-xl bg-brand-green text-white text-xs sm:text-sm font-black hover:bg-brand-deep transition-all shadow-md shadow-green-200 cursor-pointer shrink-0">
                                Filter
                            </button>
                        </form>
                    </div>
                </div>

                <?php if($this->session->flashdata('success')): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs sm:text-sm font-bold mb-6 animate-fade-in flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-check-circle text-brand-green"></i>
                            <span><?php echo htmlspecialchars((string)$this->session->flashdata('success')); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fas fa-times"></i></button>
                    </div>
                <?php endif; ?>

                <?php if($this->session->flashdata('error')): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-xs sm:text-sm font-bold mb-6 animate-fade-in flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                            <span><?php echo htmlspecialchars((string)$this->session->flashdata('error')); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                    </div>
                <?php endif; ?>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <?php 
                        $total = isset($tasks) ? count($tasks) : 0;
                        $pending = 0;
                        $completed = 0;
                        if($total > 0) {
                            foreach($tasks as $t) {
                                if(isset($t->status) && $t->status === 'Done') $completed++;
                                else $pending++;
                            }
                        }
                    ?>
                    <div class="bg-white p-5 sm:p-6 rounded-3xl sm:rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4 sm:gap-5 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                            <i class="fas fa-tasks text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Total Assigned</p>
                            <h4 class="text-xl sm:text-2xl font-black text-gray-900"><?php echo $total; ?></h4>
                        </div>
                    </div>
                    <div class="bg-white p-5 sm:p-6 rounded-3xl sm:rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4 sm:gap-5 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 shadow-inner">
                            <i class="fas fa-clock text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Pending</p>
                            <h4 class="text-xl sm:text-2xl font-black text-gray-900"><?php echo $pending; ?></h4>
                        </div>
                    </div>
                    <div class="bg-white p-5 sm:p-6 rounded-3xl sm:rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4 sm:gap-5 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 bg-green-50 rounded-2xl flex items-center justify-center text-brand-green shadow-inner">
                            <i class="fas fa-check-double text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Completed</p>
                            <h4 class="text-xl sm:text-2xl font-black text-gray-900"><?php echo $completed; ?></h4>
                        </div>
                    </div>
                </div>

                <?php
                    // Group tasks per influencer for executive status breakdown
                    $influencer_groups = [];
                    if (isset($tasks) && !empty($tasks)) {
                        foreach ($tasks as $t) {
                            $uname_key = strtolower(trim((string)($t->influencer_username ?? 'unknown')));
                            $plat_key = strtolower(trim((string)($t->influencer_platform ?? 'instagram')));
                            $group_key = $uname_key . '|' . $plat_key;

                            if (!isset($influencer_groups[$group_key])) {
                                $inf_uname = !empty($t->influencer_username) ? $t->influencer_username : 'Unknown';
                                $inf_fallback = "https://ui-avatars.com/api/?name=" . urlencode((string)$inf_uname) . "&background=0F6B3E&color=fff&rounded=true&bold=true";
                                $inf_avatar = !empty($t->influencer_profile_image) ? $t->influencer_profile_image : $inf_fallback;
                                if (strpos($plat_key, 'tiktok') !== false) {
                                    $plat_icon_cls = 'fab fa-tiktok text-gray-900';
                                } elseif (strpos($plat_key, 'youtube') !== false) {
                                    $plat_icon_cls = 'fab fa-youtube text-red-500';
                                } else {
                                    $plat_icon_cls = 'fab fa-instagram text-pink-500';
                                }
                                $influencer_groups[$group_key] = [
                                    'username' => $inf_uname,
                                    'platform' => !empty($t->influencer_platform) ? $t->influencer_platform : 'Instagram',
                                    'plat_icon_cls' => $plat_icon_cls,
                                    'avatar' => $inf_avatar,
                                    'fallback' => $inf_fallback,
                                    'campaigns' => [],
                                    'total' => 0,
                                    'done' => 0,
                                    'pending' => 0,
                                    'overdue' => 0,
                                    'tasks' => []
                                ];
                            }

                            $is_done_item = (isset($t->status) && $t->status === 'Done');
                            $is_overdue_item = (!$is_done_item && !empty($t->due_date) && strtotime($t->due_date) < strtotime('today'));
                            $camp_name = !empty($t->campaign_title) ? $t->campaign_title : 'General';
                            if (!in_array($camp_name, $influencer_groups[$group_key]['campaigns'], true)) {
                                $influencer_groups[$group_key]['campaigns'][] = $camp_name;
                            }
                            $influencer_groups[$group_key]['total']++;
                            if ($is_done_item) {
                                $influencer_groups[$group_key]['done']++;
                            } else {
                                $influencer_groups[$group_key]['pending']++;
                            }
                            if ($is_overdue_item) {
                                $influencer_groups[$group_key]['overdue']++;
                            }
                            $influencer_groups[$group_key]['tasks'][] = $t;
                        }
                    }
                ?>

                <?php if (!empty($influencer_groups)): ?>
                    <!-- Section Header & View Toggle -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-gray-900 tracking-tight">Task Progress by Influencer</h3>
                            <p class="text-xs text-gray-400 font-medium">Track completion rate, pending deliverables, and deadlines per creator (<?php echo count($influencer_groups); ?> influencer<?php echo count($influencer_groups) > 1 ? 's' : ''; ?>).</p>
                        </div>
                        <div class="inline-flex items-center bg-white border border-gray-200 rounded-xl p-1 shadow-xs self-start sm:self-auto">
                            <button type="button" id="btnViewByInfluencer" onclick="switchTasksView('influencer')" class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all bg-brand-green text-white shadow-xs cursor-pointer">
                                <i class="fas fa-user-check mr-1.5"></i> By Influencer
                            </button>
                            <button type="button" id="btnViewAllTable" onclick="switchTasksView('table')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-500 hover:text-gray-800 cursor-pointer">
                                <i class="fas fa-table mr-1.5"></i> Table View
                            </button>
                        </div>
                    </div>

                    <!-- VIEW 1: Executive Per-Influencer Task Status Cards (Default) -->
                    <div id="tasksByInfluencerView" class="space-y-5 mb-12">
                        <?php foreach ($influencer_groups as $grp): ?>
                            <?php
                                $pct = $grp['total'] > 0 ? (int)round(($grp['done'] / $grp['total']) * 100) : 0;
                                if ($pct === 100) {
                                    $grp_badge_cls = 'bg-emerald-50 text-brand-green border-emerald-200';
                                    $grp_badge_text = 'Completed';
                                    $grp_bar_cls = 'bg-brand-green';
                                } elseif ($grp['overdue'] > 0) {
                                    $grp_badge_cls = 'bg-red-50 text-red-600 border-red-200';
                                    $grp_badge_text = 'Overdue (' . $grp['overdue'] . ')';
                                    $grp_bar_cls = 'bg-red-500';
                                } elseif ($grp['done'] > 0) {
                                    $grp_badge_cls = 'bg-blue-50 text-blue-600 border-blue-200';
                                    $grp_badge_text = 'In Progress';
                                    $grp_bar_cls = 'bg-blue-500';
                                } else {
                                    $grp_badge_cls = 'bg-amber-50 text-amber-600 border-amber-200';
                                    $grp_badge_text = 'Pending';
                                    $grp_bar_cls = 'bg-amber-400';
                                }
                            ?>
                            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                                <!-- Influencer Summary Header -->
                                <div class="p-5 sm:p-6 bg-gray-50/60 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="relative shrink-0">
                                            <img src="<?php echo htmlspecialchars((string)$grp['avatar']); ?>"
                                                 alt="<?php echo htmlspecialchars((string)$grp['username']); ?>"
                                                 referrerpolicy="no-referrer"
                                                 onerror="this.onerror=null;this.src='<?php echo htmlspecialchars((string)$grp['fallback']); ?>';"
                                                 class="w-12 h-12 rounded-2xl border-2 border-white shadow-sm object-cover bg-emerald-50">
                                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-lg flex items-center justify-center shadow-sm border border-gray-100">
                                                <i class="<?php echo $grp['plat_icon_cls']; ?> text-[10px]"></i>
                                            </div>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="text-base font-black text-gray-900 truncate">@<?php echo htmlspecialchars((string)$grp['username']); ?></h4>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border <?php echo $grp_badge_cls; ?>">
                                                    <?php echo $grp_badge_text; ?>
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"><?php echo htmlspecialchars((string)$grp['platform']); ?></span>
                                                <span class="text-gray-300">•</span>
                                                <?php foreach ($grp['campaigns'] as $c_title): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white border border-gray-200 text-[10px] font-bold text-gray-600">
                                                        <i class="fas fa-bullhorn text-[9px] text-brand-green"></i>
                                                        <?php echo htmlspecialchars((string)$c_title); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Per-Influencer Progress Bar & Counters -->
                                    <div class="w-full lg:w-72 shrink-0 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs">
                                        <div class="flex items-center justify-between text-[11px] font-extrabold mb-1.5">
                                            <span class="text-gray-600">
                                                <span class="text-brand-green"><?php echo $grp['done']; ?> Done</span>
                                                <span class="text-gray-300 mx-1">/</span>
                                                <span class="text-amber-600"><?php echo $grp['pending']; ?> Pending</span>
                                            </span>
                                            <span class="text-gray-900"><?php echo $pct; ?>%</span>
                                        </div>
                                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full <?php echo $grp_bar_cls; ?> rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Influencer's Specific Tasks Checklist -->
                                <div class="divide-y divide-gray-50">
                                    <?php foreach ($grp['tasks'] as $t): ?>
                                        <?php
                                            $is_done = isset($t->status) && $t->status === 'Done';
                                            $is_overdue = (!$is_done && !empty($t->due_date) && strtotime($t->due_date) < strtotime('today'));
                                        ?>
                                        <div class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/60 transition-colors">
                                            <div class="flex items-start gap-3.5 min-w-0">
                                                <div class="mt-0.5 w-8 h-8 rounded-xl <?php echo $is_done ? 'bg-green-50 text-brand-green' : ($is_overdue ? 'bg-red-50 text-red-500' : 'bg-amber-50 text-amber-500'); ?> flex items-center justify-center shrink-0">
                                                    <i class="fas <?php echo $is_done ? 'fa-check' : ($is_overdue ? 'fa-exclamation' : 'fa-list-ul'); ?> text-xs"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-xs sm:text-sm font-extrabold text-gray-900 break-words <?php echo $is_done ? 'line-through opacity-50' : ''; ?>">
                                                        <?php echo htmlspecialchars((string)($t->task_name ?? '')); ?>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider <?php echo $is_overdue ? 'text-red-500' : 'text-gray-400'; ?>">
                                                            <i class="far fa-calendar-alt text-[10px]"></i>
                                                            Due: <?php echo !empty($t->due_date) ? date('M d, Y', strtotime($t->due_date)) : 'No deadline'; ?>
                                                            <?php if ($is_overdue): ?> (Overdue)<?php endif; ?>
                                                        </span>
                                                        <?php if (!empty($t->assignee_name)): ?>
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-brand-deep bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md">
                                                                <i class="far fa-user text-[9px]"></i>
                                                                <?php echo htmlspecialchars((string)$t->assignee_name); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 self-end sm:self-center shrink-0">
                                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest <?php echo $is_done ? 'bg-green-50 text-brand-green border border-green-100' : 'bg-amber-50 text-amber-600 border border-amber-100'; ?>">
                                                    <?php echo htmlspecialchars((string)($t->status ?: 'Pending')); ?>
                                                </span>
                                                <form action="<?php echo base_url('influencer/update_my_task_status'); ?>" method="post">
                                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                    <input type="hidden" name="task_id" value="<?php echo (int)$t->id; ?>">
                                                    <input type="hidden" name="status" value="<?php echo $is_done ? 'Pending' : 'Done'; ?>">
                                                    <input type="hidden" name="filter_status" value="<?php echo htmlspecialchars((string)$status); ?>">
                                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black transition-all cursor-pointer <?php echo $is_done ? 'bg-gray-100 text-gray-500 hover:bg-gray-200' : 'bg-brand-green text-white hover:bg-brand-deep shadow-md shadow-green-100'; ?>">
                                                        <?php echo $is_done ? 'Mark Pending' : 'Mark Done'; ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- VIEW 2: Classic Table View (Toggleable) -->
                <div id="tasksAllTableView" class="<?php echo !empty($influencer_groups) ? 'hidden' : ''; ?> bg-white rounded-3xl sm:rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mb-12">
                    <?php if(isset($tasks) && !empty($tasks)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        <th class="px-5 sm:px-8 py-4 sm:py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Task Details</th>
                                        <th class="px-4 sm:px-6 py-4 sm:py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Influencer</th>
                                        <th class="px-4 sm:px-6 py-4 sm:py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Campaign</th>
                                        <th class="px-4 sm:px-6 py-4 sm:py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                        <th class="px-5 sm:px-8 py-4 sm:py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php foreach($tasks as $t): ?>
                                        <?php
                                            $is_done = isset($t->status) && $t->status === 'Done';
                                            $inf_uname = !empty($t->influencer_username) ? $t->influencer_username : 'Unknown';
                                            $inf_fallback = "https://ui-avatars.com/api/?name=" . urlencode((string)$inf_uname) . "&background=0F6B3E&color=fff&rounded=true&bold=true";
                                            $inf_avatar = !empty($t->influencer_profile_image) ? $t->influencer_profile_image : $inf_fallback;
                                            $plat_raw = strtolower((string)($t->influencer_platform ?? 'instagram'));
                                            if (strpos($plat_raw, 'tiktok') !== false) {
                                                $plat_icon_cls = 'fab fa-tiktok text-gray-900';
                                            } elseif (strpos($plat_raw, 'youtube') !== false) {
                                                $plat_icon_cls = 'fab fa-youtube text-red-500';
                                            } else {
                                                $plat_icon_cls = 'fab fa-instagram text-pink-500';
                                            }
                                            $is_overdue = (!$is_done && !empty($t->due_date) && strtotime($t->due_date) < strtotime('today'));
                                        ?>
                                        <tr class="group hover:bg-gray-50/50 transition-all duration-300">
                                            <td class="px-5 sm:px-8 py-4 sm:py-6">
                                                <div class="flex items-start gap-3 sm:gap-4 min-w-[180px]">
                                                    <div class="mt-0.5 w-8 h-8 rounded-xl <?php echo $is_done ? 'bg-green-50 text-brand-green' : 'bg-amber-50 text-amber-500'; ?> flex items-center justify-center shrink-0 shadow-sm border border-white">
                                                        <i class="fas <?php echo $is_done ? 'fa-check' : 'fa-list-ul'; ?> text-xs"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="text-xs sm:text-sm font-extrabold text-gray-900 break-words <?php echo $is_done ? 'line-through opacity-50' : ''; ?>"><?php echo htmlspecialchars((string)($t->task_name ?? '')); ?></div>
                                                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider <?php echo $is_overdue ? 'text-red-500' : 'text-gray-400'; ?>">
                                                                <i class="far fa-calendar-alt text-[10px] <?php echo $is_overdue ? 'text-red-500' : 'text-gray-400'; ?>"></i>
                                                                Due: <?php echo !empty($t->due_date) ? date('M d, Y', strtotime($t->due_date)) : 'No deadline'; ?>
                                                                <?php if($is_overdue): ?> (Overdue)<?php endif; ?>
                                                            </span>
                                                            <?php if(!empty($t->assignee_name)): ?>
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-brand-deep bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md">
                                                                    <i class="far fa-user text-[9px]"></i>
                                                                    <?php echo htmlspecialchars((string)$t->assignee_name); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 sm:py-6">
                                                <div class="flex items-center gap-3 min-w-[150px]">
                                                    <div class="relative shrink-0">
                                                        <img src="<?php echo htmlspecialchars((string)$inf_avatar); ?>"
                                                             alt="<?php echo htmlspecialchars((string)$inf_uname); ?>"
                                                             referrerpolicy="no-referrer"
                                                             onerror="this.onerror=null;this.src='<?php echo htmlspecialchars((string)$inf_fallback); ?>';"
                                                             class="w-10 h-10 rounded-2xl border-2 border-white shadow-sm object-cover bg-emerald-50">
                                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                                            <i class="<?php echo $plat_icon_cls; ?> text-[8px]"></i>
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="text-xs sm:text-sm font-extrabold text-gray-800 truncate">@<?php echo htmlspecialchars((string)$inf_uname); ?></div>
                                                        <div class="text-[10px] font-bold text-gray-400 uppercase truncate"><?php echo htmlspecialchars((string)($t->influencer_platform ?? 'Instagram')); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 sm:py-6">
                                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100 border border-gray-200 whitespace-nowrap">
                                                    <i class="fas fa-bullhorn text-[10px] text-gray-500"></i>
                                                    <span class="text-xs font-extrabold text-gray-600"><?php echo htmlspecialchars((string)($t->campaign_title ?: 'General')); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 sm:py-6 text-center whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest <?php echo $is_done ? 'bg-green-50 text-brand-green border border-green-100' : 'bg-amber-50 text-amber-600 border border-amber-100'; ?>">
                                                    <?php echo htmlspecialchars((string)($t->status ?: 'Pending')); ?>
                                                </span>
                                            </td>
                                            <td class="px-5 sm:px-8 py-4 sm:py-6 text-right whitespace-nowrap">
                                                <form action="<?php echo base_url('influencer/update_my_task_status'); ?>" method="post">
                                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                    <input type="hidden" name="task_id" value="<?php echo (int)$t->id; ?>">
                                                    <input type="hidden" name="status" value="<?php echo $is_done ? 'Pending' : 'Done'; ?>">
                                                    <input type="hidden" name="filter_status" value="<?php echo htmlspecialchars((string)$status); ?>">
                                                    <button type="submit" class="group/btn relative px-4 sm:px-6 py-2.5 rounded-xl text-xs font-black transition-all duration-300 overflow-hidden cursor-pointer <?php echo $is_done ? 'bg-gray-100 text-gray-500 hover:bg-gray-200' : 'bg-brand-green text-white hover:bg-brand-deep shadow-lg shadow-green-100'; ?>">
                                                        <span class="relative z-10"><?php echo $is_done ? 'Mark Pending' : 'Mark Done'; ?></span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="py-16 sm:py-24 px-4 text-center">
                            <div class="relative inline-block mb-6 sm:mb-8">
                                <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gray-50 rounded-[2.5rem] sm:rounded-[3rem] flex items-center justify-center mx-auto border-2 border-dashed border-gray-200">
                                    <i class="fas fa-clipboard-check text-gray-200 text-4xl sm:text-5xl"></i>
                                </div>
                                <div class="absolute -top-2 -right-2 w-9 h-9 sm:w-10 sm:h-10 bg-green-50 rounded-2xl flex items-center justify-center text-brand-green shadow-sm animate-bounce">
                                    <i class="fas fa-smile text-lg sm:text-xl"></i>
                                </div>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-2">You're all caught up!</h3>
                            <p class="text-xs sm:text-sm text-gray-500 font-medium max-w-md mx-auto">No tasks are currently assigned to you. Enjoy your free time or check back later for updates.</p>
                            <div class="mt-6 sm:mt-8">
                                <a href="<?php echo base_url('influencer/dashboard'); ?>" class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 bg-white border-2 border-gray-100 rounded-2xl text-xs sm:text-sm font-black text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    Go to Dashboard
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script>
        function switchTasksView(mode) {
            const byInf = document.getElementById('tasksByInfluencerView');
            const tableV = document.getElementById('tasksAllTableView');
            const btnInf = document.getElementById('btnViewByInfluencer');
            const btnTbl = document.getElementById('btnViewAllTable');
            if (!byInf || !tableV) return;

            if (mode === 'table') {
                byInf.classList.add('hidden');
                tableV.classList.remove('hidden');
                if (btnTbl) { btnTbl.classList.add('bg-brand-green', 'text-white', 'shadow-xs', 'font-extrabold'); btnTbl.classList.remove('text-gray-500', 'font-bold'); }
                if (btnInf) { btnInf.classList.remove('bg-brand-green', 'text-white', 'shadow-xs', 'font-extrabold'); btnInf.classList.add('text-gray-500', 'font-bold'); }
            } else {
                tableV.classList.add('hidden');
                byInf.classList.remove('hidden');
                if (btnInf) { btnInf.classList.add('bg-brand-green', 'text-white', 'shadow-xs', 'font-extrabold'); btnInf.classList.remove('text-gray-500', 'font-bold'); }
                if (btnTbl) { btnTbl.classList.remove('bg-brand-green', 'text-white', 'shadow-xs', 'font-extrabold'); btnTbl.classList.add('text-gray-500', 'font-bold'); }
            }
        }
    </script>
</body>
</html>
