<?php
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$per_page_limit = isset($per_page_limit) ? $per_page_limit : 100;
$total_rows = isset($total_rows) ? (int)$total_rows : (isset($influencers) ? count($influencers) : 0);
$start_index = isset($start_index) ? (int)$start_index : ($total_rows > 0 ? 1 : 0);
$end_index = isset($end_index) ? (int)$end_index : $total_rows;
$current_page = isset($current_page) ? (int)$current_page : 1;
$total_pages = isset($total_pages) ? (int)$total_pages : 1;

$custom_columns = isset($custom_columns) && is_array($custom_columns) ? $custom_columns : [];
$custom_values = isset($custom_values) && is_array($custom_values) ? $custom_values : [];

// Split custom columns into sections
$custom_inf_cols = [];
$custom_app_cols = [];
foreach ($custom_columns as $col) {
    if ($col->section === 'approval') {
        $custom_app_cols[] = $col;
    } else {
        $custom_inf_cols[] = $col;
    }
}

// Fixed base column counts:
// Influencer: Handle, Platform, Niche, Gender, Location, Followers, ER, First Name, Contact Info = 9
$inf_colspan = 9 + count($custom_inf_cols);
// Approval: Approve/Decline, Date Reviewed, Notes = 3, + 1 for '+ Add column' header = 4 + custom
$app_colspan = 3 + count($custom_app_cols) + 1;
?>

<style>
    /* Native Microsoft Excel cell cursor (+) when hovering over the spreadsheet table */
    #excelRootContainer table,
    #excelRootContainer table tbody td,
    #excelRootContainer table tbody tr {
        cursor: cell;
    }
    #excelRootContainer table tbody td input[type="text"],
    #excelRootContainer table tbody td input[type="number"],
    #excelRootContainer table tbody td input[type="email"],
    #excelRootContainer table tbody td input[type="url"],
    #excelRootContainer table tbody td input[type="date"],
    #excelRootContainer table tbody td textarea {
        cursor: text;
    }
    #excelRootContainer table tbody td input[type="checkbox"],
    #excelRootContainer table tbody td button,
    #excelRootContainer table tbody td a,
    #excelRootContainer table thead th button {
        cursor: pointer;
    }
</style>

<div class="w-full flex flex-col font-sans select-none" id="excelRootContainer">
    <!-- Top Action & Search Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
        <!-- Left: Search, Filters, Stats Counter -->
        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Search Box -->
            <div class="relative w-64 md:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" 
                       id="excelSearchInput" 
                       value="<?= htmlspecialchars($this->input->get('search') ?: '') ?>" 
                       placeholder="Search influencer..." 
                       class="w-full bg-white border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
            </div>

            <!-- Filters Toggle Button -->
            <button type="button" 
                    id="excelFiltersBtn"
                    onclick="toggleExcelFilters()" 
                    class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-semibold py-1.5 px-3 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
                <i class="fas fa-filter text-[11px] text-gray-400"></i>
                <span>Filters</span>
                <span id="activeFiltersBadge" class="hidden w-2 h-2 rounded-full bg-blue-500"></span>
            </button>

            <!-- Counter Badge -->
            <div class="text-xs text-gray-500 font-medium ml-1">
                <span class="text-gray-900 font-bold" id="excelHeaderCounter"><?= $total_rows > 0 ? (count($influencers) . ' of ' . $total_rows) : '0 of 0' ?> influencers</span>
                <span class="text-gray-400 ml-1.5">Updated just now</span>
            </div>
        </div>

        <!-- Right: Add Influencer, Import / Export, Menu -->
        <div class="flex items-center gap-2">
            <!-- Add Influencer Button -->
            <button type="button" 
                    onclick="toggleModal('addInfluencerModal')" 
                    class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 text-xs font-semibold py-1.5 px-3.5 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
                <i class="fas fa-plus text-[11px] text-gray-500"></i>
                <span>Add Influencer</span>
            </button>

            <!-- Import / Export Menu -->
            <div class="relative inline-block text-left" id="excelExportDropdown">
                <button type="button" 
                        onclick="toggleExcelDropdown('excelExportMenu')" 
                        class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 text-xs font-semibold py-1.5 px-3 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
                    <i class="fas fa-cog text-[11px] text-gray-400"></i>
                    <span>Import / Export</span>
                </button>
                <div id="excelExportMenu" class="hidden absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                    <button type="button" onclick="toggleModal('importModal'); toggleExcelDropdown('excelExportMenu');" class="w-full text-left px-3.5 py-2 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                        <i class="fas fa-file-import text-blue-500 w-4"></i> Import CSV
                    </button>
                    <a href="<?= site_url('influencer/export_csv') ?>" onclick="toggleExcelDropdown('excelExportMenu');" class="w-full text-left px-3.5 py-2 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                        <i class="fas fa-file-csv text-emerald-500 w-4"></i> Export All CSV
                    </a>
                    <button type="button" onclick="bulkExport(); toggleExcelDropdown('excelExportMenu');" class="w-full text-left px-3.5 py-2 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                        <i class="fas fa-check-square text-purple-500 w-4"></i> Export Selected
                    </button>
                </div>
            </div>

            <!-- More Options Menu (...) -->
            <div class="relative inline-block text-left" id="excelMoreDropdown">
                <button type="button" 
                        onclick="toggleExcelDropdown('excelMoreMenu')" 
                        class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 text-xs font-semibold p-1.5 px-2.5 rounded-lg shadow-sm transition-all">
                    <i class="fas fa-ellipsis-v text-xs"></i>
                </button>
                <div id="excelMoreMenu" class="hidden absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                    <a href="<?= site_url('influencer/pipeline') ?>" class="w-full text-left px-3.5 py-2 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                        <i class="fas fa-columns text-blue-500 w-4"></i> Pipeline View
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <button type="button" onclick="confirmDeleteAll(); toggleExcelDropdown('excelMoreMenu');" class="w-full text-left px-3.5 py-2 text-xs text-red-600 hover:bg-red-50 flex items-center gap-2">
                        <i class="fas fa-trash-alt text-red-500 w-4"></i> Delete All Records
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Collapsible Filters Panel -->
    <div id="excelFiltersPanel" class="<?= ($this->input->get('platform') || $this->input->get('status') || $this->input->get('niche')) ? '' : 'hidden' ?> bg-white border border-gray-200 rounded-xl p-3 mb-3 shadow-sm transition-all">
        <form method="GET" action="<?= site_url('influencer/manage') ?>" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="limit" value="<?= $per_page_limit ?>">
            
            <!-- Platform Filter -->
            <div class="flex items-center gap-1.5">
                <span class="text-[11px] font-semibold text-gray-500 uppercase">Platform:</span>
                <select name="platform" class="text-xs border border-gray-200 rounded-lg px-2.5 py-1 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Platforms</option>
                    <option value="Instagram" <?= $this->input->get('platform') === 'Instagram' ? 'selected' : '' ?>>Instagram</option>
                    <option value="TikTok" <?= $this->input->get('platform') === 'TikTok' ? 'selected' : '' ?>>TikTok</option>
                    <option value="YouTube" <?= $this->input->get('platform') === 'YouTube' ? 'selected' : '' ?>>YouTube</option>
                </select>
            </div>

            <!-- Assessment / Status Filter -->
            <div class="flex items-center gap-1.5">
                <span class="text-[11px] font-semibold text-gray-500 uppercase">Approval:</span>
                <select name="status" class="text-xs border border-gray-200 rounded-lg px-2.5 py-1 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?= $this->input->get('status') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Qualified" <?= in_array($this->input->get('status'), ['Qualified', 'Approved']) ? 'selected' : '' ?>>Approved</option>
                    <option value="Disqualified" <?= in_array($this->input->get('status'), ['Disqualified', 'Declined']) ? 'selected' : '' ?>>Declined</option>
                </select>
            </div>

            <!-- Niche Filter -->
            <div class="flex items-center gap-1.5">
                <span class="text-[11px] font-semibold text-gray-500 uppercase">Niche:</span>
                <input type="text" name="niche" value="<?= htmlspecialchars($this->input->get('niche') ?: '') ?>" placeholder="e.g. Beauty, Tech" class="text-xs border border-gray-200 rounded-lg px-2.5 py-1 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500 w-36">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1 rounded-lg transition-colors">Apply</button>
            <a href="<?= site_url('influencer/manage') ?>" class="text-xs text-gray-500 hover:text-gray-800 font-medium ml-1">Reset</a>
        </form>
    </div>

    <!-- Floating Bulk Actions Bar (Image 4) -->
    <div id="bulkActions" class="hidden bg-[#eff6ff] border border-[#bfdbfe] rounded-xl px-4 py-2 flex items-center gap-3 shadow-sm mb-3 transition-all animate-fade-in">
        <span class="text-xs font-bold text-[#1d4ed8]">
            <span id="selectedCount">0</span> selected
        </span>

        <button type="button" 
                onclick="clearAllSelections()" 
                class="text-xs font-medium text-gray-500 hover:text-gray-800 transition-colors flex items-center gap-1">
            ✕ Clear
        </button>

        <div class="h-4 w-px bg-blue-200 mx-1"></div>

        <button type="button" 
                onclick="bulkApproveSelected()" 
                class="bg-[#10b981] hover:bg-emerald-600 text-white text-xs font-semibold px-3.5 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
            <i class="fas fa-check text-[10px]"></i> Approve
        </button>

        <button type="button" 
                onclick="deleteSelected()" 
                class="border border-red-200 bg-white hover:bg-red-50 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
            <i class="fas fa-trash-alt text-[10px]"></i> Delete <span id="deleteBtnCount">0</span>
        </button>

        <button type="button" 
                onclick="bulkAddPipeline()" 
                class="border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
            <i class="fas fa-layer-group text-gray-400 text-[10px]"></i> Pipeline
        </button>

        <button type="button" 
                onclick="bulkExport()" 
                class="border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
            <i class="fas fa-file-export text-gray-400 text-[10px]"></i> Export
        </button>
    </div>

    <!-- Excel Spreadsheet Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col relative">
        <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] min-h-[380px] relative scrollbar-thin" id="excelScrollWrapper">
            <table class="w-full min-w-max border-collapse text-left text-xs text-gray-700 table-auto" id="excelSpreadsheetTable">
                <!-- Group Headers Row (Row 1) -->
                <thead>
                    <tr class="border-b border-gray-200 text-[11px] font-bold">
                        <!-- Sticky Index Col Header -->
                        <th class="w-12 bg-white border-r border-gray-200 sticky left-0 z-30 p-0 text-center"></th>
                        
                        <!-- Influencer Details Super Header (Light Blue) -->
                        <th colspan="<?= $inf_colspan ?>" id="thInfluencerDetails" class="bg-[#eef4ff] text-[#2563eb] py-2 px-3 text-center border-r border-blue-200 uppercase tracking-wider font-bold">
                            Influencer Details
                        </th>
                        
                        <!-- Approval Details Super Header (Light Purple) -->
                        <th colspan="<?= $app_colspan ?>" id="thApprovalDetails" class="bg-[#faf5ff] text-[#9333ea] py-2 px-3 text-center uppercase tracking-wider font-bold">
                            Approval Details
                        </th>
                    </tr>

                    <!-- Column Headers Row (Row 2) -->
                    <tr id="excelColumnHeadersRow" class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold text-[11px] sticky top-0 z-20">
                        <!-- Checkbox & Index Column Header -->
                        <th class="w-12 bg-gray-50 border-r border-gray-200 text-center py-2 px-2 sticky left-0 z-30">
                            <div class="flex flex-col items-center justify-center gap-0.5">
                                <input type="checkbox" 
                                       id="selectAllCheckbox" 
                                       onchange="toggleSelectAll(this)" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-0 w-3.5 h-3.5 cursor-pointer">
                                <span class="text-[9px] text-gray-400 font-mono">#</span>
                            </div>
                        </th>

                        <!-- Influencer Details Columns -->
                        <th class="min-w-[210px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600">Handle</th>
                        <th class="min-w-[85px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600 text-center">Platform</th>
                        <th class="min-w-[120px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600">Niche</th>
                        <th class="min-w-[110px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600">Gender</th>
                        <th class="min-w-[130px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600">Location</th>
                        <th class="min-w-[100px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600 text-right">Followers</th>
                        <th class="min-w-[125px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600 text-right">Engagement Rate</th>
                        <th class="min-w-[120px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600">First Name</th>
                        <th class="min-w-[160px] bg-white border-r border-gray-200 py-2.5 px-3 font-semibold text-gray-600">Contact Info</th>

                        <!-- Custom Columns under Influencer Details -->
                        <?php foreach ($custom_inf_cols as $c_col): ?>
                            <th class="min-w-[130px] bg-[#f8fbff] border-r border-blue-100 py-2.5 px-3 font-semibold text-gray-700 relative group" data-custom-id="<?= $c_col->id ?>">
                                <div class="flex items-center justify-between">
                                    <span><?= htmlspecialchars($c_col->column_name) ?></span>
                                    <button type="button" onclick="deleteCustomColumn(<?= $c_col->id ?>, event)" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity text-xs" title="Delete column">✕</button>
                                </div>
                            </th>
                        <?php endforeach; ?>

                        <!-- Approval Details Columns -->
                        <th id="thApproveDecline" class="min-w-[130px] bg-[#faf5ff] border-r border-purple-100 py-2.5 px-3 font-semibold text-gray-700 text-center">Approve/Decline</th>
                        <th class="min-w-[120px] bg-[#faf5ff] border-r border-purple-100 py-2.5 px-3 font-semibold text-gray-700 text-center">Date Reviewed</th>
                        <th class="min-w-[170px] bg-[#faf5ff] border-r border-purple-100 py-2.5 px-3 font-semibold text-gray-700">Notes</th>

                        <!-- Custom Columns under Approval Details -->
                        <?php foreach ($custom_app_cols as $c_col): ?>
                            <th class="min-w-[130px] bg-[#faf5ff] border-r border-purple-100 py-2.5 px-3 font-semibold text-gray-700 relative group" data-custom-id="<?= $c_col->id ?>">
                                <div class="flex items-center justify-between">
                                    <span><?= htmlspecialchars($c_col->column_name) ?></span>
                                    <button type="button" onclick="deleteCustomColumn(<?= $c_col->id ?>, event)" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity text-xs" title="Delete column">✕</button>
                                </div>
                            </th>
                        <?php endforeach; ?>

                        <!-- + Add Column Header Button -->
                        <th id="thAddColumnBtn" class="min-w-[120px] bg-[#faf5ff] py-2.5 px-3 font-semibold text-purple-600 hover:text-purple-800 hover:bg-purple-100/50 text-center cursor-pointer transition-colors" onclick="openAddCustomColumnModal()">
                            <span class="flex items-center justify-center gap-1">
                                <i class="fas fa-plus text-[10px]"></i> Add column
                            </span>
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody id="excelTableBody">
                    <?php if (!empty($influencers)): ?>
                        <?php 
                        $row_num = $start_index;
                        foreach ($influencers as $inf): 
                            $assessment = $inf->assessment ?: 'Pending';
                            $is_approved = in_array($assessment, ['Approved', 'Qualified']);
                            $is_declined = in_array($assessment, ['Declined', 'Disqualified', 'Rejected']);
                            $avatar_url = !empty($inf->profile_image) ? $inf->profile_image : '';
                            $first_char = strtoupper(substr($inf->username ?: ($inf->full_name ?: '?'), 0, 1));
                        ?>
                        <tr class="excel-row border-b border-gray-200 hover:bg-gray-50/70 transition-colors" 
                            data-id="<?= $inf->id ?>" 
                            id="excel-row-<?= $inf->id ?>">
                            
                            <!-- Row Index & Checkbox (Sticky Left) -->
                            <td class="w-12 bg-white border-r border-gray-200 text-center py-2 px-2 sticky left-0 z-10 sticky-col">
                                <div class="flex flex-col items-center justify-center gap-0.5">
                                    <input type="checkbox" 
                                           name="influencer_ids[]" 
                                           value="<?= $inf->id ?>" 
                                           class="influencer-checkbox rounded border-gray-300 text-blue-600 focus:ring-0 w-3.5 h-3.5 cursor-pointer"
                                           onchange="onRowCheckboxChange(this)">
                                    <span class="text-[10px] text-gray-400 font-mono font-medium row-num-label"><?= $row_num++ ?></span>
                                </div>
                            </td>

                            <!-- 1. Handle -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
                                tabindex="0" 
                                data-col="username" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->username ?: '') ?>">
                                <div class="flex items-center gap-2.5">
                                    <?php if ($avatar_url): ?>
                                        <img src="<?= htmlspecialchars($avatar_url) ?>" 
                                             class="w-6 h-6 rounded-full object-cover border border-gray-200 cursor-pointer" 
                                             onclick="openDetailModal(<?= $inf->id ?>)"
                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($first_char) ?>&background=random';">
                                    <?php else: ?>
                                        <div onclick="openDetailModal(<?= $inf->id ?>)" 
                                             class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500 cursor-pointer">
                                            <?= $first_char ?: '?' ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="cell-display font-medium text-gray-800 <?= empty($inf->username) ? 'text-gray-400 italic' : '' ?>"
                                          ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                        <?= !empty($inf->username) ? '@' . htmlspecialchars($inf->username) : 'Enter username' ?>
                                    </span>
                                </div>
                            </td>

                            <!-- 2. Platform -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-center cursor-pointer" 
                                tabindex="0" 
                                data-col="platform" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->platform ?: 'Instagram') ?>"
                                onclick="openPlatformPopover(this, event)">
                                <div class="inline-flex items-center justify-center w-6 h-6">
                                    <?php if (strtolower($inf->platform) === 'instagram'): ?>
                                        <i class="fab fa-instagram text-pink-500 text-base" title="Instagram"></i>
                                    <?php elseif (strtolower($inf->platform) === 'tiktok'): ?>
                                        <i class="fab fa-tiktok text-gray-900 text-base" title="TikTok"></i>
                                    <?php elseif (strtolower($inf->platform) === 'youtube'): ?>
                                        <i class="fab fa-youtube text-red-600 text-base" title="YouTube"></i>
                                    <?php else: ?>
                                        <i class="fas fa-globe text-gray-400 text-base" title="Platform"></i>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- 3. Niche -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
                                tabindex="0" 
                                data-col="niche" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->niche ?: 'General') ?>">
                                <span class="cell-display text-gray-700 <?= empty($inf->niche) ? 'text-gray-400' : '' ?>"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?= htmlspecialchars($inf->niche ?: '—') ?>
                                </span>
                            </td>

                            <!-- 4. Gender (Matching Image 3) -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs cursor-pointer" 
                                tabindex="0" 
                                data-col="gender" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->gender ?: '') ?>"
                                onclick="openGenderPopover(this, event)">
                                <span class="cell-display text-gray-700 <?= empty($inf->gender) ? 'text-gray-400' : '' ?>">
                                    <?= htmlspecialchars($inf->gender ?: '—') ?>
                                </span>
                            </td>

                            <!-- 5. Location -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
                                tabindex="0" 
                                data-col="location" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->location ?: '') ?>">
                                <span class="cell-display text-gray-700 <?= empty($inf->location) ? 'text-gray-400' : '' ?>"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?= htmlspecialchars($inf->location ?: '—') ?>
                                </span>
                            </td>

                            <!-- 6. Followers -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-right" 
                                tabindex="0" 
                                data-col="followers" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= (int)$inf->followers ?>">
                                <span class="cell-display font-medium text-gray-800"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?php 
                                    if ($inf->followers >= 1000000) {
                                        echo round($inf->followers / 1000000, 1) . 'M';
                                    } elseif ($inf->followers >= 1000) {
                                        echo number_format($inf->followers);
                                    } elseif ($inf->followers > 0) {
                                        echo $inf->followers;
                                    } else {
                                        echo '<span class="text-gray-400">—</span>';
                                    }
                                    ?>
                                </span>
                            </td>

                            <!-- 7. Engagement Rate -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-right" 
                                tabindex="0" 
                                data-col="engagement_rate" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= (float)$inf->eng_rate ?>">
                                <span class="cell-display font-medium text-gray-800"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?= ($inf->eng_rate > 0) ? number_format($inf->eng_rate, 2) . '%' : '<span class="text-gray-400">—</span>' ?>
                                </span>
                            </td>

                            <!-- 8. First Name -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
                                tabindex="0" 
                                data-col="first_name" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->first_name ?: ($inf->full_name ?: '')) ?>">
                                <span class="cell-display text-gray-700 <?= empty($inf->first_name) ? 'text-gray-400' : '' ?>"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?= htmlspecialchars($inf->first_name ?: ($inf->full_name ?: '—')) ?>
                                </span>
                            </td>

                            <!-- 9. Contact Info -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
                                tabindex="0" 
                                data-col="contact_info" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->contact_info ?: ($inf->email ?: '')) ?>">
                                <span class="cell-display text-gray-700 <?= empty($inf->contact_info) && empty($inf->email) ? 'text-gray-400' : '' ?>"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?= htmlspecialchars($inf->contact_info ?: ($inf->email ?: '—')) ?>
                                </span>
                            </td>

                            <!-- Custom Columns under Influencer Details (Body Cells) -->
                            <?php foreach ($custom_inf_cols as $c_col): 
                                $c_val = isset($custom_values[$c_col->id . '_' . $inf->id]) ? $custom_values[$c_col->id . '_' . $inf->id] : '';
                            ?>
                                <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs bg-[#f8fbff]" 
                                    tabindex="0" 
                                    data-is-custom="true"
                                    data-custom-col-id="<?= $c_col->id ?>" 
                                    data-field-type="<?= htmlspecialchars($c_col->field_type) ?>"
                                    data-collab-id="<?= $inf->id ?>"
                                    data-val="<?= htmlspecialchars($c_val) ?>">
                                    <span class="cell-display text-gray-700 <?= empty($c_val) ? 'text-gray-400' : '' ?>"
                                          ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                        <?= htmlspecialchars($c_val ?: '—') ?>
                                    </span>
                                </td>
                            <?php endforeach; ?>

                            <!-- 10. Approve/Decline Badge (Group 2) -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-center cursor-pointer bg-[#faf5ff]/40" 
                                tabindex="0" 
                                data-col="assessment" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($assessment) ?>"
                                onclick="openApprovalPopover(this, event)">
                                <span class="cell-display">
                                    <?php if ($is_approved): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Approved
                                        </span>
                                    <?php elseif ($is_declined): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                            Declined
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </td>

                            <!-- 11. Date Reviewed -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-center bg-[#faf5ff]/40" 
                                tabindex="0" 
                                data-col="date_reviewed" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->date_reviewed ?: '') ?>">
                                <span class="cell-display text-gray-600 <?= empty($inf->date_reviewed) ? 'text-gray-400' : '' ?>"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?= !empty($inf->date_reviewed) ? date('M d, Y', strtotime($inf->date_reviewed)) : '—' ?>
                                </span>
                            </td>

                            <!-- 12. Notes -->
                            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs bg-[#faf5ff]/40" 
                                tabindex="0" 
                                data-col="notes" 
                                data-id="<?= $inf->id ?>"
                                data-val="<?= htmlspecialchars($inf->notes ?: '') ?>">
                                <span class="cell-display text-gray-600 truncate block max-w-[200px] <?= empty($inf->notes) ? 'text-gray-400' : '' ?>"
                                      title="<?= htmlspecialchars($inf->notes ?: '') ?>"
                                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                    <?= htmlspecialchars($inf->notes ?: '—') ?>
                                </span>
                            </td>

                            <!-- Custom Columns under Approval Details (Body Cells) -->
                            <?php foreach ($custom_app_cols as $c_col): 
                                $c_val = isset($custom_values[$c_col->id . '_' . $inf->id]) ? $custom_values[$c_col->id . '_' . $inf->id] : '';
                            ?>
                                <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs bg-[#faf5ff]/40" 
                                    tabindex="0" 
                                    data-is-custom="true"
                                    data-custom-col-id="<?= $c_col->id ?>" 
                                    data-field-type="<?= htmlspecialchars($c_col->field_type) ?>"
                                    data-collab-id="<?= $inf->id ?>"
                                    data-val="<?= htmlspecialchars($c_val) ?>">
                                    <span class="cell-display text-gray-700 <?= empty($c_val) ? 'text-gray-400' : '' ?>"
                                          ondblclick="startCellEdit(this.closest('.excel-cell'))">
                                        <?= htmlspecialchars($c_val ?: '—') ?>
                                    </span>
                                </td>
                            <?php endforeach; ?>

                            <!-- 13. Empty Cell below "+ Add column" -->
                            <td class="excel-cell py-2 px-3 text-xs text-gray-400 bg-[#faf5ff]/20 text-center" tabindex="0">
                                —
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $inf_colspan + $app_colspan + 1 ?>" class="py-12 text-center text-gray-400 bg-white">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-users-slash text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-sm font-semibold text-gray-600">No influencers found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or click "+ Add row" below.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Under Table Actions: + Add row and Add multiple rows -->
        <div class="flex items-center gap-2 p-2.5 bg-white border-t border-gray-200">
            <button type="button" 
                    onclick="excelAddRow()" 
                    class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold py-1.5 px-3 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
                <i class="fas fa-plus text-[10px] text-gray-400"></i>
                <span>Add row</span>
            </button>

            <button type="button" 
                    onclick="openAddMultipleRowsModal()" 
                    class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold py-1.5 px-3 rounded-lg shadow-sm flex items-center gap-1.5 transition-all">
                <i class="far fa-clone text-[10px] text-gray-400"></i>
                <span>Add multiple rows</span>
            </button>
        </div>
    </div>

    <!-- Bottom Controls & Pagination (Image 1) -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-3 px-1">
        <!-- Rows per page -->
        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
            <span>Rows per page:</span>
            <select onchange="changeExcelRowsPerPage(this.value)" 
                    class="border border-gray-200 rounded-lg px-2.5 py-1 text-xs font-semibold text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer shadow-sm">
                <option value="10" <?= $per_page_limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?= $per_page_limit == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= $per_page_limit == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?= $per_page_limit == 100 ? 'selected' : '' ?>>100</option>
            </select>
            <span id="bottomSelectedText" class="text-xs font-semibold text-blue-600 hidden ml-2"></span>
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center gap-3 text-xs text-gray-500">
            <span class="font-medium" id="excelBottomCounter"><?= $total_rows > 0 ? ($start_index . '-' . $end_index . ' of ' . $total_rows) : '0 of 0' ?></span>
            <div class="flex items-center gap-1">
                <?php if ($current_page > 1): ?>
                    <a href="<?= site_url('influencer/manage?per_page=' . (($current_page - 2) * $per_page_limit) . '&limit=' . $per_page_limit . ($this->input->get('search') ? '&search=' . urlencode($this->input->get('search')) : '')) ?>" 
                       class="px-2.5 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 font-semibold transition-colors">
                        « Prev
                    </a>
                <?php else: ?>
                    <span class="px-2.5 py-1 border border-gray-100 rounded-lg text-gray-300 font-semibold cursor-not-allowed">
                        « Prev
                    </span>
                <?php endif; ?>

                <span class="px-2.5 py-1 border border-gray-200 bg-gray-50 rounded-lg text-gray-700 font-bold">
                    <?= $current_page ?>/<?= max(1, $total_pages) ?>
                </span>

                <?php if ($current_page < $total_pages): ?>
                    <a href="<?= site_url('influencer/manage?per_page=' . ($current_page * $per_page_limit) . '&limit=' . $per_page_limit . ($this->input->get('search') ? '&search=' . urlencode($this->input->get('search')) : '')) ?>" 
                       class="px-2.5 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 font-semibold transition-colors">
                        Next »
                    </a>
                <?php else: ?>
                    <span class="px-2.5 py-1 border border-gray-100 rounded-lg text-gray-300 font-semibold cursor-not-allowed">
                        Next »
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Keyboard Shortcuts Legend (Images 1-4) -->
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 pt-4 border-t border-gray-200/70 text-[11px] text-gray-400">
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Ctrl</kbd>
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Click</kbd> Multi-select
        </span>
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Shift</kbd>
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Click</kbd> Range select
        </span>
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">↑</kbd>
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">↓</kbd>
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">←</kbd>
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">→</kbd> Navigate
        </span>
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Enter</kbd> Edit
        </span>
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Tab</kbd> Next cell
        </span>
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Esc</kbd> Cancel
        </span>
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Del</kbd> Clear
        </span>
        <span class="inline-flex items-center gap-1">
            <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-600 font-mono text-[10px]">Dbl-click</kbd> View Profile
        </span>
        <span class="text-gray-400 ml-auto flex items-center gap-1 font-medium hidden md:inline-flex">
            <i class="fas fa-grip-vertical text-gray-300"></i> Drag custom columns to assign groups
        </span>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: ADD MULTIPLE ROWS (Matching Image 1) -->
<!-- ========================================== -->
<div id="addMultipleRowsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddMultipleRowsModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <!-- Modal Card -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full p-6 relative animate-fade-in">
            <!-- Close Button -->
            <button type="button" onclick="closeAddMultipleRowsModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>

            <!-- Header -->
            <h3 class="text-lg font-bold text-gray-900 mb-4">Add Multiple Rows</h3>

            <!-- Number of rows -->
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-600 mb-2">Number of rows</label>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="stepMultipleRows(-1)" 
                            class="w-10 h-10 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 font-bold text-lg active:scale-95 transition-all shadow-sm">
                        −
                    </button>
                    <input type="number" 
                           id="modalRowsCountInput" 
                           value="5" 
                           min="1" 
                           max="50" 
                           oninput="updateMultipleRowsBtnText()" 
                           class="w-24 h-10 border border-gray-200 rounded-xl text-center font-bold text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    <button type="button" 
                            onclick="stepMultipleRows(1)" 
                            class="w-10 h-10 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 font-bold text-lg active:scale-95 transition-all shadow-sm">
                        +
                    </button>
                    <span class="text-sm font-medium text-gray-500 ml-1.5">rows</span>
                </div>
            </div>

            <!-- Insert position -->
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-600 mb-2">Insert position</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            id="posAtEndBtn" 
                            onclick="setInsertPosition('end')" 
                            class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center bg-blue-600 text-white shadow-sm">
                        At the end
                    </button>
                    <button type="button" 
                            id="posAfterSelectedBtn" 
                            onclick="setInsertPosition('after')" 
                            class="py-2.5 px-3 rounded-xl text-xs font-medium transition-all text-center bg-gray-50 text-gray-400 border border-gray-100 hover:bg-gray-100">
                        After selected
                    </button>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-100">
                <button type="button" 
                        onclick="closeAddMultipleRowsModal()" 
                        class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-all text-center">
                    Cancel
                </button>
                <button type="button" 
                        id="submitAddMultipleRowsBtn" 
                        onclick="confirmAddMultipleRows()" 
                        class="flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-md active:scale-95 text-center">
                    Add 5 Rows
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: ADD CUSTOM COLUMN (Matching Image 2) -->
<!-- ========================================== -->
<div id="addCustomColumnModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddCustomColumnModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <!-- Modal Card -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6 sm:p-7 relative animate-fade-in">
            <!-- Close Button -->
            <button type="button" onclick="closeAddCustomColumnModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>

            <!-- Header -->
            <div class="mb-5">
                <h3 class="text-lg font-bold text-gray-900">Add Custom Column</h3>
                <p class="text-xs text-gray-500 mt-0.5">Extend your table with custom data fields</p>
            </div>

            <!-- Form Content -->
            <div class="space-y-4">
                <!-- Column Name -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Column Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="customColNameInput" 
                           placeholder="e.g., Content Type, Contract Status" 
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Description <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="text" 
                           id="customColDescInput" 
                           placeholder="Briefly describe what this column tracks" 
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Field Type -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Field Type</label>
                    <select id="customColTypeSelect" 
                            onchange="onCustomFieldTypeChange(this.value)"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer bg-white">
                        <option value="Text">Text</option>
                        <option value="Number">Number</option>
                        <option value="Date">Date</option>
                        <option value="Single Select">Single Select</option>
                        <option value="Checkbox">Checkbox</option>
                    </select>

                    <!-- Field Type Helper Box (Matching Image 2) -->
                    <div id="fieldTypeHelperBox" class="mt-2 bg-gray-50/80 border border-gray-100 rounded-xl p-3 text-xs text-gray-500 leading-relaxed">
                        <span id="helperBoxDesc" class="font-medium text-gray-700">Free-form text input for any value</span><br>
                        <span id="helperBoxExample" class="italic text-gray-400">e.g., "Prefers email contact"</span>
                    </div>
                </div>

                <!-- Section Selector (Cards) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Section</label>
                    <div class="space-y-2">
                        <!-- Card 1: Influencer Details -->
                        <div id="sectionCardInfluencer" 
                             onclick="selectColSection('influencer')" 
                             class="flex items-start gap-3 p-3 rounded-xl border border-blue-500 bg-blue-50/30 cursor-pointer transition-all">
                            <input type="radio" 
                                   name="col_section_radio" 
                                   id="radioInfluencer" 
                                   value="influencer" 
                                   checked 
                                   class="mt-0.5 text-blue-600 focus:ring-0 cursor-pointer">
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Influencer Details</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Profile info, demographics, social metrics</p>
                            </div>
                        </div>

                        <!-- Card 2: Approval Details -->
                        <div id="sectionCardApproval" 
                             onclick="selectColSection('approval')" 
                             class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 hover:border-gray-300 cursor-pointer transition-all">
                            <input type="radio" 
                                   name="col_section_radio" 
                                   id="radioApproval" 
                                   value="approval" 
                                   class="mt-0.5 text-blue-600 focus:ring-0 cursor-pointer">
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Approval Details</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Review status, notes, decision tracking</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help link -->
                <div class="pt-1">
                    <a href="javascript:void(0)" onclick="alert('Custom columns allow you to store and track any extra information per influencer. They are saved automatically in your table.')" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700 font-medium">
                        <i class="far fa-lightbulb"></i>
                        <span>How do custom columns work?</span>
                    </a>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between gap-3 pt-5 mt-5 border-t border-gray-100">
                <button type="button" 
                        onclick="closeAddCustomColumnModal()" 
                        class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-all text-center">
                    Cancel
                </button>
                <button type="button" 
                        id="submitAddCustomColBtn" 
                        onclick="confirmAddCustomColumn()" 
                        class="flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-md active:scale-95 text-center">
                    Add Column
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Excel Popover (Shared for Gender, Platform, Approval) -->
<div id="excelFloatingPopover" class="hidden fixed bg-white rounded-xl shadow-xl border border-gray-200 py-1.5 z-[9999] min-w-[145px] text-xs animate-fade-in">
    <!-- Popover items injected dynamically -->
</div>

<!-- Excel Spreadsheet Styles -->
<style>
/* Excel Active Cell Highlight (matches Image 3) */
.excel-cell:focus, 
.excel-cell.cell-focused {
    box-shadow: inset 0 0 0 2px #3b82f6 !important;
    outline: none !important;
    background-color: #ffffff !important;
}

/* Selected Row Background Tint (matches Image 4) */
tr.excel-row.row-selected,
tr.excel-row.row-selected td {
    background-color: #eff6ff !important;
}

tr.excel-row.row-selected td.sticky-col {
    background-color: #eff6ff !important;
}

/* Cell Flash on Successful Save */
.cell-save-success {
    animation: flashGreen 1.2s ease-out;
}
@keyframes flashGreen {
    0% { background-color: #d1fae5; }
    100% { background-color: inherit; }
}

/* Scrollbar styling */
#excelScrollWrapper::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
#excelScrollWrapper::-webkit-scrollbar-track {
    background: #f1f5f9;
}
#excelScrollWrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
#excelScrollWrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<!-- Excel Spreadsheet Engine Scripts -->
<script>
window.EXCEL_CSRF = {
    name: '<?= $csrf_name ?>',
    hash: '<?= $csrf_hash ?>'
};
window.EXCEL_URLS = {
    update_detail: '<?= site_url('influencer/update_detail_ajax') ?>',
    bulk_approve: '<?= site_url('influencer/bulk_approve') ?>',
    add_row: '<?= site_url('influencer/add_row_ajax') ?>',
    add_custom_col: '<?= site_url('influencer/add_custom_column_ajax') ?>',
    delete_custom_col: '<?= site_url('influencer/delete_custom_column_ajax') ?>',
    save_custom_val: '<?= site_url('influencer/save_custom_value_ajax') ?>',
    delete_selected: '<?= site_url('influencer/delete_selected') ?>',
    delete_all: '<?= site_url('influencer/delete_all') ?>'
};
window.EXCEL_CUSTOM_COLS = <?= json_encode($custom_columns) ?>;
window.EXCEL_TOTAL_ROWS = <?= (int)$total_rows ?>;
window.EXCEL_START_INDEX = <?= (int)$start_index ?>;
window.EXCEL_PER_PAGE = <?= (int)$per_page_limit ?>;

let activeFocusedCell = null;
let lastClickedCheckboxIndex = null;
let currentInsertPosition = 'end';
let currentSelectedSection = 'influencer';

document.addEventListener('DOMContentLoaded', function() {
    initExcelGrid();
});

function initExcelGrid() {
    const table = document.getElementById('excelSpreadsheetTable');
    if (!table) return;

    // Search enter handler
    const searchInput = document.getElementById('excelSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const url = new URL(window.location.href);
                if (this.value.trim()) {
                    url.searchParams.set('search', this.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('per_page');
                window.location.href = url.toString();
            }
        });
    }

    // Cell Focus & Keyboard Navigation
    table.addEventListener('click', function(e) {
        const cell = e.target.closest('.excel-cell');
        if (cell) {
            focusCell(cell);
        }
    });

    table.addEventListener('keydown', function(e) {
        if (!activeFocusedCell) return;

        // If currently editing inside an input/select, don't hijack unless Escape or Enter
        if (activeFocusedCell.querySelector('input, select, textarea')) {
            if (e.key === 'Escape') {
                cancelCellEdit(activeFocusedCell);
            }
            return;
        }

        const tr = activeFocusedCell.closest('tr');
        const cells = Array.from(tr.querySelectorAll('.excel-cell'));
        const currentColIdx = cells.indexOf(activeFocusedCell);
        const rows = Array.from(document.querySelectorAll('#excelTableBody tr.excel-row'));
        const currentRowIdx = rows.indexOf(tr);

        if (e.key === 'ArrowRight' || (e.key === 'Tab' && !e.shiftKey)) {
            e.preventDefault();
            if (currentColIdx < cells.length - 1) {
                focusCell(cells[currentColIdx + 1]);
            } else if (currentRowIdx < rows.length - 1) {
                const nextRowCells = rows[currentRowIdx + 1].querySelectorAll('.excel-cell');
                if (nextRowCells.length > 0) focusCell(nextRowCells[0]);
            }
        } else if (e.key === 'ArrowLeft' || (e.key === 'Tab' && e.shiftKey)) {
            e.preventDefault();
            if (currentColIdx > 0) {
                focusCell(cells[currentColIdx - 1]);
            } else if (currentRowIdx > 0) {
                const prevRowCells = rows[currentRowIdx - 1].querySelectorAll('.excel-cell');
                if (prevRowCells.length > 0) focusCell(prevRowCells[prevRowCells.length - 1]);
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (currentRowIdx < rows.length - 1) {
                const nextRowCells = rows[currentRowIdx + 1].querySelectorAll('.excel-cell');
                if (nextRowCells[currentColIdx]) focusCell(nextRowCells[currentColIdx]);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (currentRowIdx > 0) {
                const prevRowCells = rows[currentRowIdx - 1].querySelectorAll('.excel-cell');
                if (prevRowCells[currentColIdx]) focusCell(prevRowCells[currentColIdx]);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const col = activeFocusedCell.dataset.col;
            if (col === 'gender') {
                openGenderPopover(activeFocusedCell, e);
            } else if (col === 'platform') {
                openPlatformPopover(activeFocusedCell, e);
            } else if (col === 'assessment') {
                openApprovalPopover(activeFocusedCell, e);
            } else {
                startCellEdit(activeFocusedCell);
            }
        } else if (e.key === 'Delete' || e.key === 'Backspace') {
            const col = activeFocusedCell.dataset.col;
            const isCustom = activeFocusedCell.dataset.isCustom;
            if (isCustom) {
                e.preventDefault();
                saveCustomCellValue(activeFocusedCell, '');
            } else if (col && !['platform', 'assessment'].includes(col)) {
                e.preventDefault();
                saveCellValue(activeFocusedCell, '');
            }
        }
    });

    // Close popovers on click outside
    document.addEventListener('click', function(e) {
        const popover = document.getElementById('excelFloatingPopover');
        if (popover && !popover.classList.contains('hidden')) {
            if (!popover.contains(e.target) && !e.target.closest('.excel-cell')) {
                hideExcelPopover();
            }
        }
        // Export dropdown
        const expMenu = document.getElementById('excelExportMenu');
        if (expMenu && !expMenu.classList.contains('hidden') && !e.target.closest('#excelExportDropdown')) {
            expMenu.classList.add('hidden');
        }
        // More dropdown
        const moreMenu = document.getElementById('excelMoreMenu');
        if (moreMenu && !moreMenu.classList.contains('hidden') && !e.target.closest('#excelMoreDropdown')) {
            moreMenu.classList.add('hidden');
        }
    });

    renderExcelBulkBar();
}

function focusCell(cell) {
    if (activeFocusedCell) {
        activeFocusedCell.classList.remove('cell-focused');
    }
    activeFocusedCell = cell;
    cell.classList.add('cell-focused');
    cell.focus();
}

// Inline Cell Editing
function startCellEdit(cell) {
    if (!cell) return;
    const col = cell.dataset.col;
    const isCustom = cell.dataset.isCustom;
    if (!isCustom && (!col || ['platform', 'gender', 'assessment'].includes(col))) return;

    if (cell.querySelector('input')) return; // already editing

    const currentVal = cell.dataset.val !== undefined ? cell.dataset.val : '';
    const displaySpan = cell.querySelector('.cell-display');
    if (displaySpan) displaySpan.style.display = 'none';

    const fieldType = cell.dataset.fieldType || '';
    const input = document.createElement('input');
    if (col === 'followers' || col === 'engagement_rate' || fieldType === 'Number') {
        input.type = 'number';
        if (col === 'engagement_rate') input.step = '0.01';
    } else if (col === 'date_reviewed' || fieldType === 'Date') {
        input.type = 'date';
    } else {
        input.type = 'text';
    }
    input.value = currentVal;
    input.className = 'w-full bg-white border border-blue-500 rounded px-1.5 py-0.5 text-xs text-gray-900 focus:outline-none shadow-inner';
    
    cell.appendChild(input);
    input.focus();
    input.select();

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            commitCellEdit(cell, input.value);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            cancelCellEdit(cell);
        }
    });

    input.addEventListener('blur', function() {
        commitCellEdit(cell, input.value);
    });
}

function commitCellEdit(cell, newVal) {
    const input = cell.querySelector('input');
    if (input) input.remove();

    const displaySpan = cell.querySelector('.cell-display');
    if (displaySpan) displaySpan.style.display = '';

    if (cell.dataset.val !== newVal) {
        if (cell.dataset.isCustom) {
            saveCustomCellValue(cell, newVal);
        } else {
            saveCellValue(cell, newVal);
        }
    }
}

function cancelCellEdit(cell) {
    const input = cell.querySelector('input');
    if (input) input.remove();

    const displaySpan = cell.querySelector('.cell-display');
    if (displaySpan) displaySpan.style.display = '';
}

// AJAX Save Standard Cell Value
function saveCellValue(cell, value) {
    const id = cell.dataset.id;
    const col = cell.dataset.col;
    if (!id || !col) return;

    const payload = new URLSearchParams();
    payload.append('id', id);
    payload.append(col, value);
    payload.append(window.EXCEL_CSRF.name, window.EXCEL_CSRF.hash);

    // Optimistic UI Update
    cell.dataset.val = value;
    const displaySpan = cell.querySelector('.cell-display');
    if (displaySpan) {
        if (col === 'username') {
            displaySpan.textContent = value ? '@' + value : 'Enter username';
            displaySpan.className = 'cell-display font-medium text-gray-800 ' + (!value ? 'text-gray-400 italic' : '');
        } else if (col === 'engagement_rate') {
            displaySpan.textContent = value ? parseFloat(value).toFixed(2) + '%' : '—';
        } else if (col === 'followers') {
            displaySpan.textContent = value ? parseInt(value).toLocaleString() : '—';
        } else {
            displaySpan.textContent = value || '—';
            if (!value) displaySpan.classList.add('text-gray-400');
            else displaySpan.classList.remove('text-gray-400');
        }
    }

    cell.classList.add('cell-save-success');
    setTimeout(() => cell.classList.remove('cell-save-success'), 1200);

    fetch(window.EXCEL_URLS.update_detail, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
    })
    .then(res => res.json())
    .catch(err => {
        console.error('AJAX error:', err);
    });
}

// AJAX Save Custom Column Cell Value
function saveCustomCellValue(cell, value) {
    const colId = cell.dataset.customColId;
    const collabId = cell.dataset.collabId;
    if (!colId || !collabId) return;

    const payload = new URLSearchParams();
    payload.append('column_id', colId);
    payload.append('collaboration_id', collabId);
    payload.append('value', value);
    payload.append(window.EXCEL_CSRF.name, window.EXCEL_CSRF.hash);

    cell.dataset.val = value;
    const displaySpan = cell.querySelector('.cell-display');
    if (displaySpan) {
        displaySpan.textContent = value || '—';
        if (!value) displaySpan.classList.add('text-gray-400');
        else displaySpan.classList.remove('text-gray-400');
    }

    cell.classList.add('cell-save-success');
    setTimeout(() => cell.classList.remove('cell-save-success'), 1200);

    fetch(window.EXCEL_URLS.save_custom_val, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
    })
    .then(res => res.json())
    .catch(err => {
        console.error('AJAX error saving custom cell:', err);
    });
}

// Floating Popover Logic (Matches Image 3)
function showExcelPopover(targetEl, itemsHtml) {
    const popover = document.getElementById('excelFloatingPopover');
    if (!popover) return;

    popover.innerHTML = itemsHtml;
    popover.classList.remove('hidden');

    const rect = targetEl.getBoundingClientRect();
    let top = rect.bottom + 4;
    let left = rect.left;

    if (left + 160 > window.innerWidth) {
        left = window.innerWidth - 170;
    }
    if (top + 200 > window.innerHeight) {
        top = rect.top - popover.offsetHeight - 4;
    }

    popover.style.top = top + 'px';
    popover.style.left = left + 'px';
}

function hideExcelPopover() {
    const popover = document.getElementById('excelFloatingPopover');
    if (popover) popover.classList.add('hidden');
}

// Gender Popover (Image 3)
function openGenderPopover(cell, e) {
    if (e) e.stopPropagation();
    focusCell(cell);

    const current = cell.dataset.val || '';
    const options = [
        { label: '— Non —', val: '' },
        { label: 'Male', val: 'Male' },
        { label: 'Female', val: 'Female' },
        { label: 'Non-binary', val: 'Non-binary' },
        { label: 'Other', val: 'Other' }
    ];

    let html = '';
    options.forEach(opt => {
        const isSelected = (opt.val === current || (opt.val === '' && !current));
        html += `
            <div onclick="selectGenderOption('${opt.val}')" 
                 class="px-3.5 py-1.5 cursor-pointer text-xs font-medium hover:bg-blue-50 hover:text-blue-600 transition-colors ${isSelected ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-700'}">
                ${opt.label}
            </div>
        `;
    });

    showExcelPopover(cell, html);
}

function selectGenderOption(val) {
    if (activeFocusedCell) {
        saveCellValue(activeFocusedCell, val);
        const span = activeFocusedCell.querySelector('.cell-display');
        if (span) span.textContent = val || '—';
    }
    hideExcelPopover();
}

// Platform Popover
function openPlatformPopover(cell, e) {
    if (e) e.stopPropagation();
    focusCell(cell);

    const options = [
        { label: 'Instagram', val: 'Instagram', icon: 'fab fa-instagram text-pink-500' },
        { label: 'TikTok', val: 'TikTok', icon: 'fab fa-tiktok text-gray-900' },
        { label: 'YouTube', val: 'YouTube', icon: 'fab fa-youtube text-red-600' }
    ];

    let html = '';
    options.forEach(opt => {
        html += `
            <div onclick="selectPlatformOption('${opt.val}')" 
                 class="px-3.5 py-1.5 cursor-pointer text-xs font-medium hover:bg-blue-50 hover:text-blue-600 transition-colors flex items-center gap-2 text-gray-700">
                <i class="${opt.icon} w-4 text-sm"></i>
                <span>${opt.label}</span>
            </div>
        `;
    });

    showExcelPopover(cell, html);
}

function selectPlatformOption(val) {
    if (activeFocusedCell) {
        saveCellValue(activeFocusedCell, val);
        const iconContainer = activeFocusedCell.querySelector('div');
        if (iconContainer) {
            let iconClass = 'fas fa-globe text-gray-400';
            if (val.toLowerCase() === 'instagram') iconClass = 'fab fa-instagram text-pink-500';
            if (val.toLowerCase() === 'tiktok') iconClass = 'fab fa-tiktok text-gray-900';
            if (val.toLowerCase() === 'youtube') iconClass = 'fab fa-youtube text-red-600';
            iconContainer.innerHTML = `<i class="${iconClass} text-base" title="${val}"></i>`;
        }
    }
    hideExcelPopover();
}

// Approve/Decline Popover
function openApprovalPopover(cell, e) {
    if (e) e.stopPropagation();
    focusCell(cell);

    const html = `
        <div onclick="selectApprovalOption('Approved')" 
             class="px-3.5 py-1.5 cursor-pointer text-xs font-semibold hover:bg-emerald-50 text-emerald-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Approved
        </div>
        <div onclick="selectApprovalOption('Declined')" 
             class="px-3.5 py-1.5 cursor-pointer text-xs font-semibold hover:bg-rose-50 text-rose-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Declined
        </div>
        <div onclick="selectApprovalOption('Pending')" 
             class="px-3.5 py-1.5 cursor-pointer text-xs font-semibold hover:bg-amber-50 text-amber-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Pending
        </div>
    `;

    showExcelPopover(cell, html);
}

function selectApprovalOption(val) {
    if (activeFocusedCell) {
        const dbVal = (val === 'Approved') ? 'Qualified' : (val === 'Declined' ? 'Disqualified' : 'Pending');
        saveCellValue(activeFocusedCell, dbVal);

        const span = activeFocusedCell.querySelector('.cell-display');
        if (span) {
            if (val === 'Approved') {
                span.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Approved</span>`;
            } else if (val === 'Declined') {
                span.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">Declined</span>`;
            } else {
                span.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Pending</span>`;
            }
        }

        const tr = activeFocusedCell.closest('tr');
        if (tr && val !== 'Pending') {
            const dateCell = tr.querySelector('.excel-cell[data-col="date_reviewed"]');
            if (dateCell) {
                const todayFormatted = new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' }).format(new Date());
                const dateSpan = dateCell.querySelector('.cell-display');
                if (dateSpan) {
                    dateSpan.textContent = todayFormatted;
                    dateSpan.classList.remove('text-gray-400');
                }
            }
        }
    }
    hideExcelPopover();
}

// Bulk Actions & Checkbox Handlers
function onRowCheckboxChange(cb, e) {
    const tr = cb.closest('tr.excel-row');
    if (cb.checked) {
        tr.classList.add('row-selected');
    } else {
        tr.classList.remove('row-selected');
    }

    const allCbs = Array.from(document.querySelectorAll('.influencer-checkbox'));
    const currentIndex = allCbs.indexOf(cb);

    if (e && e.shiftKey && lastClickedCheckboxIndex !== null && lastClickedCheckboxIndex !== currentIndex) {
        const start = Math.min(lastClickedCheckboxIndex, currentIndex);
        const end = Math.max(lastClickedCheckboxIndex, currentIndex);
        for (let i = start; i <= end; i++) {
            allCbs[i].checked = cb.checked;
            const r = allCbs[i].closest('tr.excel-row');
            if (cb.checked) r.classList.add('row-selected');
            else r.classList.remove('row-selected');
        }
    }
    lastClickedCheckboxIndex = currentIndex;

    renderExcelBulkBar();
}

function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.influencer-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
        const tr = cb.closest('tr.excel-row');
        if (source.checked) tr.classList.add('row-selected');
        else tr.classList.remove('row-selected');
    });
    renderExcelBulkBar();
}

function clearAllSelections() {
    const checkboxes = document.querySelectorAll('.influencer-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = false;
        const tr = cb.closest('tr.excel-row');
        if (tr) tr.classList.remove('row-selected');
    });
    const selectAll = document.getElementById('selectAllCheckbox');
    if (selectAll) selectAll.checked = false;
    renderExcelBulkBar();
}

function renderExcelBulkBar() {
    const checked = document.querySelectorAll('.influencer-checkbox:checked');
    const all = document.querySelectorAll('.influencer-checkbox');
    const bulkBar = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const deleteBtnCount = document.getElementById('deleteBtnCount');
    const deleteCountModal = document.getElementById('deleteCount');
    const bottomCounter = document.getElementById('bottomSelectedText');
    const selectAll = document.getElementById('selectAllCheckbox');

    const count = checked.length;

    if (selectAll) {
        if (all.length > 0 && count === all.length) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else if (count === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    if (count > 0) {
        if (bulkBar) {
            bulkBar.classList.remove('hidden');
            bulkBar.style.display = 'flex';
        }
        if (selectedCount) selectedCount.textContent = count;
        if (deleteBtnCount) deleteBtnCount.textContent = count;
        if (deleteCountModal) deleteCountModal.textContent = count;
        if (bottomCounter) {
            bottomCounter.textContent = count + ' selected';
            bottomCounter.classList.remove('hidden');
        }
    } else {
        if (bulkBar) {
            bulkBar.classList.add('hidden');
            bulkBar.style.display = 'none';
        }
        if (bottomCounter) {
            bottomCounter.classList.add('hidden');
        }
    }
}

// Bulk Approve Action
function bulkApproveSelected() {
    const checked = document.querySelectorAll('.influencer-checkbox:checked');
    const ids = Array.from(checked).map(cb => cb.value);
    if (ids.length === 0) return;

    if (typeof showLoading === 'function') showLoading();

    const payload = new URLSearchParams();
    payload.append('ids', ids.join(','));
    payload.append(window.EXCEL_CSRF.name, window.EXCEL_CSRF.hash);

    fetch(window.EXCEL_URLS.bulk_approve, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (typeof hideLoading === 'function') hideLoading();
        if (data.status === 'success') {
            const todayFormatted = new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' }).format(new Date());
            ids.forEach(id => {
                const tr = document.getElementById('excel-row-' + id);
                if (tr) {
                    const statusCell = tr.querySelector('.excel-cell[data-col="assessment"]');
                    if (statusCell) {
                        statusCell.dataset.val = 'Qualified';
                        const span = statusCell.querySelector('.cell-display');
                        if (span) span.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Approved</span>`;
                    }
                    const dateCell = tr.querySelector('.excel-cell[data-col="date_reviewed"]');
                    if (dateCell) {
                        const dateSpan = dateCell.querySelector('.cell-display');
                        if (dateSpan) {
                            dateSpan.textContent = todayFormatted;
                            dateSpan.classList.remove('text-gray-400');
                        }
                    }
                }
            });
            clearAllSelections();
        } else {
            alert(data.message || 'Error approving items');
        }
    })
    .catch(err => {
        if (typeof hideLoading === 'function') hideLoading();
        console.error('Bulk approve error:', err);
    });
}

// ==========================================
// ZERO-BLINK IN-PLACE DOM ROW BUILDER & INSERTER
// ==========================================
function buildRowDOM(id, rowNum) {
    const tr = document.createElement('tr');
    tr.className = 'excel-row border-b border-gray-200 hover:bg-gray-50/70 transition-colors bg-white';
    tr.dataset.id = id;
    tr.id = 'excel-row-' + id;

    let infCustomHtml = '';
    let appCustomHtml = '';
    (window.EXCEL_CUSTOM_COLS || []).forEach(c => {
        const isApp = (c.section === 'approval');
        const cellHtml = `
            <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs ${isApp ? 'bg-[#faf5ff]/40' : 'bg-[#f8fbff]'}" 
                tabindex="0" 
                data-is-custom="true"
                data-custom-col-id="${c.id}" 
                data-field-type="${c.field_type || 'Text'}"
                data-collab-id="${id}" 
                data-val="">
                <span class="cell-display text-gray-700 text-gray-400"
                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                    —
                </span>
            </td>
        `;
        if (isApp) appCustomHtml += cellHtml;
        else infCustomHtml += cellHtml;
    });

    tr.innerHTML = `
        <!-- Row Index & Checkbox (Sticky Left) -->
        <td class="w-12 bg-white border-r border-gray-200 text-center py-2 px-2 sticky left-0 z-10 sticky-col">
            <div class="flex flex-col items-center justify-center gap-0.5">
                <input type="checkbox" 
                       name="influencer_ids[]" 
                       value="${id}" 
                       class="influencer-checkbox rounded border-gray-300 text-blue-600 focus:ring-0 w-3.5 h-3.5 cursor-pointer"
                       onchange="onRowCheckboxChange(this)">
                <span class="text-[10px] text-gray-400 font-mono font-medium row-num-label">${rowNum}</span>
            </div>
        </td>

        <!-- 1. Handle -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
            tabindex="0" 
            data-col="username" 
            data-id="${id}"
            data-val="">
            <div class="flex items-center gap-2.5">
                <div onclick="openDetailModal(${id})" 
                     class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500 cursor-pointer">
                    ?
                </div>
                <span class="cell-display font-medium text-gray-800 text-gray-400 italic"
                      ondblclick="startCellEdit(this.closest('.excel-cell'))">
                    Enter username
                </span>
            </div>
        </td>

        <!-- 2. Platform -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-center cursor-pointer" 
            tabindex="0" 
            data-col="platform" 
            data-id="${id}"
            data-val="Instagram"
            onclick="openPlatformPopover(this, event)">
            <div class="inline-flex items-center justify-center w-6 h-6">
                <i class="fab fa-instagram text-pink-500 text-base" title="Instagram"></i>
            </div>
        </td>

        <!-- 3. Niche -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
            tabindex="0" 
            data-col="niche" 
            data-id="${id}"
            data-val="General">
            <span class="cell-display text-gray-700"
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                General
            </span>
        </td>

        <!-- 4. Gender -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs cursor-pointer" 
            tabindex="0" 
            data-col="gender" 
            data-id="${id}"
            data-val=""
            onclick="openGenderPopover(this, event)">
            <span class="cell-display text-gray-700 text-gray-400">
                —
            </span>
        </td>

        <!-- 5. Location -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
            tabindex="0" 
            data-col="location" 
            data-id="${id}"
            data-val="">
            <span class="cell-display text-gray-700 text-gray-400"
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                —
            </span>
        </td>

        <!-- 6. Followers -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-right" 
            tabindex="0" 
            data-col="followers" 
            data-id="${id}"
            data-val="0">
            <span class="cell-display font-medium text-gray-800"
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                <span class="text-gray-400">—</span>
            </span>
        </td>

        <!-- 7. Engagement Rate -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-right" 
            tabindex="0" 
            data-col="engagement_rate" 
            data-id="${id}"
            data-val="0">
            <span class="cell-display font-medium text-gray-800"
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                <span class="text-gray-400">—</span>
            </span>
        </td>

        <!-- 8. First Name -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
            tabindex="0" 
            data-col="first_name" 
            data-id="${id}"
            data-val="">
            <span class="cell-display text-gray-700 text-gray-400"
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                —
            </span>
        </td>

        <!-- 9. Contact Info -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs" 
            tabindex="0" 
            data-col="contact_info" 
            data-id="${id}"
            data-val="">
            <span class="cell-display text-gray-700 text-gray-400"
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                —
            </span>
        </td>

        ${infCustomHtml}

        <!-- 10. Approve/Decline Badge -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-center cursor-pointer bg-[#faf5ff]/40" 
            tabindex="0" 
            data-col="assessment" 
            data-id="${id}"
            data-val="Pending"
            onclick="openApprovalPopover(this, event)">
            <span class="cell-display">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                    Pending
                </span>
            </span>
        </td>

        <!-- 11. Date Reviewed -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs text-center bg-[#faf5ff]/40" 
            tabindex="0" 
            data-col="date_reviewed" 
            data-id="${id}"
            data-val="">
            <span class="cell-display text-gray-600 text-gray-400"
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                —
            </span>
        </td>

        <!-- 12. Notes -->
        <td class="excel-cell border-r border-gray-200 py-2 px-3 text-xs bg-[#faf5ff]/40" 
            tabindex="0" 
            data-col="notes" 
            data-id="${id}"
            data-val="">
            <span class="cell-display text-gray-600 truncate block max-w-[200px] text-gray-400"
                  title=""
                  ondblclick="startCellEdit(this.closest('.excel-cell'))">
                —
            </span>
        </td>

        ${appCustomHtml}

        <!-- 13. Empty Cell below '+ Add column' -->
        <td class="excel-cell py-2 px-3 text-xs text-gray-400 bg-[#faf5ff]/20 text-center" tabindex="0">
            —
        </td>
    `;
    return tr;
}

function insertNewRowsDOM(ids, position) {
    if (!ids || ids.length === 0) return;
    const tbody = document.getElementById('excelTableBody');
    if (!tbody) return;

    // Remove empty state placeholder row if present
    const emptyState = tbody.querySelector('td[colspan]');
    if (emptyState && tbody.querySelectorAll('tr.excel-row').length === 0) {
        tbody.innerHTML = '';
    }

    let insertTarget = null;
    if (position === 'after') {
        const checked = tbody.querySelectorAll('.influencer-checkbox:checked');
        if (checked.length > 0) {
            insertTarget = checked[checked.length - 1].closest('tr.excel-row');
        } else if (activeFocusedCell) {
            insertTarget = activeFocusedCell.closest('tr.excel-row');
        }
    }

    const createdRows = [];
    ids.forEach(id => {
        const tr = buildRowDOM(id, 1);
        if (insertTarget && insertTarget.parentNode === tbody) {
            tbody.insertBefore(tr, insertTarget.nextSibling);
            insertTarget = tr;
        } else {
            tbody.appendChild(tr);
        }
        createdRows.push(tr);
    });

    // Re-index all row numbers sequentially
    const allRows = Array.from(tbody.querySelectorAll('tr.excel-row'));
    allRows.forEach((row, idx) => {
        const label = row.querySelector('.row-num-label');
        if (label) {
            label.textContent = (window.EXCEL_START_INDEX || 1) + idx;
        }
    });

    // Update counters smoothly without page reload
    window.EXCEL_TOTAL_ROWS = (window.EXCEL_TOTAL_ROWS || 0) + ids.length;
    const headerCounter = document.getElementById('excelHeaderCounter');
    if (headerCounter) {
        headerCounter.textContent = `${allRows.length} of ${window.EXCEL_TOTAL_ROWS} influencers`;
    }
    const bottomCounter = document.getElementById('excelBottomCounter');
    if (bottomCounter) {
        const start = window.EXCEL_START_INDEX || 1;
        bottomCounter.textContent = `${start}-${allRows.length} of ${window.EXCEL_TOTAL_ROWS}`;
    }

    // Explicitly scroll container so newly inserted row(s) are instantly in view
    const scrollWrapper = document.getElementById('excelScrollWrapper');
    if (scrollWrapper) {
        if (position === 'end') {
            scrollWrapper.scrollTo({ top: scrollWrapper.scrollHeight, behavior: 'smooth' });
        } else if (insertTarget) {
            scrollWrapper.scrollTo({ top: Math.max(0, insertTarget.offsetTop - 60), behavior: 'smooth' });
        }
    }

    // Smooth focus and highlight on newly created row(s)
    if (createdRows.length > 0) {
        const firstRow = createdRows[0];
        const firstCell = firstRow.querySelector('.excel-cell[data-col="username"]');
        if (firstCell) {
            focusCell(firstCell);
        }
        createdRows.forEach(r => {
            r.classList.add('cell-save-success');
            setTimeout(() => r.classList.remove('cell-save-success'), 1200);
        });
    }

    renderExcelBulkBar();
}

// 1-Click Add Single Row (Zero Blink, Instant DOM Insert)
function excelAddRow() {
    const payload = new URLSearchParams();
    payload.append('count', 1);
    payload.append(window.EXCEL_CSRF.name, window.EXCEL_CSRF.hash);

    fetch(window.EXCEL_URLS.add_row, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.csrf_hash) {
                window.EXCEL_CSRF.hash = data.csrf_hash;
            }
            const ids = data.collab_ids || (data.id ? [data.id] : []);
            insertNewRowsDOM(ids, 'end');
        } else {
            alert(data.message || 'Could not add row');
        }
    })
    .catch(err => {
        console.error('Add row error:', err);
    });
}

// ==========================================
// MODAL 1 FUNCTIONS: ADD MULTIPLE ROWS
// ==========================================
function openAddMultipleRowsModal() {
    const modal = document.getElementById('addMultipleRowsModal');
    if (!modal) return;
    const input = document.getElementById('modalRowsCountInput');
    if (input) input.value = 5;
    updateMultipleRowsBtnText();

    const checked = document.querySelectorAll('.influencer-checkbox:checked');
    const afterBtn = document.getElementById('posAfterSelectedBtn');
    if (afterBtn) {
        if (checked.length > 0) {
            afterBtn.classList.remove('text-gray-400', 'bg-gray-50');
            afterBtn.classList.add('text-gray-700', 'hover:bg-gray-100');
        } else {
            afterBtn.classList.add('text-gray-400', 'bg-gray-50');
            afterBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
        }
    }
    setInsertPosition('end');
    modal.classList.remove('hidden');
}

function closeAddMultipleRowsModal() {
    const modal = document.getElementById('addMultipleRowsModal');
    if (modal) modal.classList.add('hidden');
}

function stepMultipleRows(delta) {
    const input = document.getElementById('modalRowsCountInput');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    val += delta;
    if (val < 1) val = 1;
    if (val > 50) val = 50;
    input.value = val;
    updateMultipleRowsBtnText();
}

function updateMultipleRowsBtnText() {
    const input = document.getElementById('modalRowsCountInput');
    const submitBtn = document.getElementById('submitAddMultipleRowsBtn');
    if (!input || !submitBtn) return;
    let count = parseInt(input.value) || 1;
    if (count < 1) count = 1;
    if (count > 50) count = 50;
    submitBtn.textContent = `Add ${count} Row${count > 1 ? 's' : ''}`;
}

function setInsertPosition(pos) {
    currentInsertPosition = pos;
    const endBtn = document.getElementById('posAtEndBtn');
    const afterBtn = document.getElementById('posAfterSelectedBtn');
    if (!endBtn || !afterBtn) return;

    if (pos === 'end') {
        endBtn.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center bg-blue-600 text-white shadow-sm';
        afterBtn.className = 'py-2.5 px-3 rounded-xl text-xs font-medium transition-all text-center bg-gray-50 text-gray-500 border border-gray-100 hover:bg-gray-100';
    } else {
        afterBtn.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center bg-blue-600 text-white shadow-sm';
        endBtn.className = 'py-2.5 px-3 rounded-xl text-xs font-medium transition-all text-center bg-gray-50 text-gray-500 border border-gray-100 hover:bg-gray-100';
    }
}

// Add Multiple Rows (Zero Blink, Instant DOM Insert)
function confirmAddMultipleRows() {
    const input = document.getElementById('modalRowsCountInput');
    let count = parseInt(input ? input.value : 5) || 5;
    if (count < 1) count = 1;
    if (count > 50) count = 50;

    closeAddMultipleRowsModal();

    const payload = new URLSearchParams();
    payload.append('count', count);
    payload.append(window.EXCEL_CSRF.name, window.EXCEL_CSRF.hash);

    fetch(window.EXCEL_URLS.add_row, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.csrf_hash) {
                window.EXCEL_CSRF.hash = data.csrf_hash;
            }
            const ids = data.collab_ids || (data.id ? [data.id] : []);
            insertNewRowsDOM(ids, currentInsertPosition);
        } else {
            alert(data.message || 'Error adding rows');
        }
    })
    .catch(err => {
        console.error('Add multiple rows error:', err);
    });
}

// ==========================================
// MODAL 2 FUNCTIONS: ADD CUSTOM COLUMN
// ==========================================
function openAddCustomColumnModal() {
    const modal = document.getElementById('addCustomColumnModal');
    if (!modal) return;
    const nameInput = document.getElementById('customColNameInput');
    const descInput = document.getElementById('customColDescInput');
    if (nameInput) nameInput.value = '';
    if (descInput) descInput.value = '';
    selectColSection('influencer');
    onCustomFieldTypeChange('Text');
    modal.classList.remove('hidden');
    if (nameInput) setTimeout(() => nameInput.focus(), 100);
}

function closeAddCustomColumnModal() {
    const modal = document.getElementById('addCustomColumnModal');
    if (modal) modal.classList.add('hidden');
}

function onCustomFieldTypeChange(type) {
    const descEl = document.getElementById('helperBoxDesc');
    const exEl = document.getElementById('helperBoxExample');
    if (!descEl || !exEl) return;

    if (type === 'Text') {
        descEl.textContent = 'Free-form text input for any value';
        exEl.textContent = 'e.g., "Prefers email contact"';
    } else if (type === 'Number') {
        descEl.textContent = 'Numeric data such as custom rates or counts';
        exEl.textContent = 'e.g., 2500, 4.5';
    } else if (type === 'Date') {
        descEl.textContent = 'Date tracker for custom milestones or reviews';
        exEl.textContent = 'e.g., 2026-11-20';
    } else if (type === 'Single Select') {
        descEl.textContent = 'Single choice selector from defined options';
        exEl.textContent = 'e.g., Tier 1, Tier 2, Tier 3';
    } else if (type === 'Checkbox') {
        descEl.textContent = 'True / False boolean toggle';
        exEl.textContent = 'e.g., Contract Signed [✓]';
    }
}

function selectColSection(sec) {
    currentSelectedSection = sec;
    const cardInf = document.getElementById('sectionCardInfluencer');
    const cardApp = document.getElementById('sectionCardApproval');
    const radioInf = document.getElementById('radioInfluencer');
    const radioApp = document.getElementById('radioApproval');

    if (sec === 'influencer') {
        if (radioInf) radioInf.checked = true;
        if (cardInf) cardInf.className = 'flex items-start gap-3 p-3 rounded-xl border border-blue-500 bg-blue-50/30 cursor-pointer transition-all';
        if (cardApp) cardApp.className = 'flex items-start gap-3 p-3 rounded-xl border border-gray-200 hover:border-gray-300 cursor-pointer transition-all';
    } else {
        if (radioApp) radioApp.checked = true;
        if (cardApp) cardApp.className = 'flex items-start gap-3 p-3 rounded-xl border border-purple-500 bg-purple-50/30 cursor-pointer transition-all';
        if (cardInf) cardInf.className = 'flex items-start gap-3 p-3 rounded-xl border border-gray-200 hover:border-gray-300 cursor-pointer transition-all';
    }
}

// Add Custom Column (Zero Blink, Instant DOM Insert)
function confirmAddCustomColumn() {
    const nameInput = document.getElementById('customColNameInput');
    const descInput = document.getElementById('customColDescInput');
    const typeSelect = document.getElementById('customColTypeSelect');

    const name = nameInput ? nameInput.value.trim() : '';
    const desc = descInput ? descInput.value.trim() : '';
    const fieldType = typeSelect ? typeSelect.value : 'Text';

    if (!name) {
        alert('Please enter a column name.');
        if (nameInput) nameInput.focus();
        return;
    }

    closeAddCustomColumnModal();

    const payload = new URLSearchParams();
    payload.append('column_name', name);
    payload.append('description', desc);
    payload.append('field_type', fieldType);
    payload.append('section', currentSelectedSection);
    payload.append(window.EXCEL_CSRF.name, window.EXCEL_CSRF.hash);

    fetch(window.EXCEL_URLS.add_custom_col, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success' && data.column) {
            insertCustomColumnDOM(data.column);
        } else {
            alert(data.message || 'Error adding column');
        }
    })
    .catch(err => {
        console.error('Add custom column error:', err);
    });
}

function insertCustomColumnDOM(col) {
    if (!col) return;
    if (!window.EXCEL_CUSTOM_COLS) window.EXCEL_CUSTOM_COLS = [];
    window.EXCEL_CUSTOM_COLS.push(col);

    const isApproval = (col.section === 'approval');
    const superHeader = isApproval ? document.getElementById('thApprovalDetails') : document.getElementById('thInfluencerDetails');
    if (superHeader) {
        superHeader.colSpan = (superHeader.colSpan || 1) + 1;
    }

    const headersRow = document.getElementById('excelColumnHeadersRow');
    if (headersRow) {
        const th = document.createElement('th');
        th.className = `min-w-[130px] ${isApproval ? 'bg-[#faf5ff] border-purple-100' : 'bg-[#f8fbff] border-blue-100'} border-r py-2.5 px-3 font-semibold text-gray-700 relative group`;
        th.dataset.customId = col.id;
        th.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${col.column_name}</span>
                <button type="button" onclick="deleteCustomColumn(${col.id}, event)" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity text-xs" title="Delete column">✕</button>
            </div>
        `;

        if (isApproval) {
            const addColHeader = document.getElementById('thAddColumnBtn') || headersRow.lastElementChild;
            headersRow.insertBefore(th, addColHeader);
        } else {
            const firstAppTh = document.getElementById('thApproveDecline');
            if (firstAppTh) {
                headersRow.insertBefore(th, firstAppTh);
            } else {
                headersRow.appendChild(th);
            }
        }
    }

    const rows = document.querySelectorAll('#excelTableBody tr.excel-row');
    rows.forEach(tr => {
        const collabId = tr.dataset.id;
        const td = document.createElement('td');
        td.className = `excel-cell border-r border-gray-200 py-2 px-3 text-xs ${isApproval ? 'bg-[#faf5ff]/40' : 'bg-[#f8fbff]'}`;
        td.tabIndex = 0;
        td.dataset.isCustom = "true";
        td.dataset.customColId = col.id;
        td.dataset.fieldType = col.field_type || 'Text';
        td.dataset.collabId = collabId;
        td.dataset.val = "";
        td.innerHTML = `
            <span class="cell-display text-gray-700 text-gray-400" ondblclick="startCellEdit(this.closest('.excel-cell'))">
                —
            </span>
        `;

        if (isApproval) {
            const lastTd = tr.lastElementChild;
            tr.insertBefore(td, lastTd);
        } else {
            const assessmentTd = tr.querySelector('.excel-cell[data-col="assessment"]');
            if (assessmentTd) {
                tr.insertBefore(td, assessmentTd);
            } else {
                tr.appendChild(td);
            }
        }

        td.classList.add('cell-save-success');
        setTimeout(() => td.classList.remove('cell-save-success'), 1200);
    });
}

function deleteCustomColumn(colId, e) {
    if (e) e.stopPropagation();
    if (!confirm('Are you sure you want to delete this custom column?')) return;

    const payload = new URLSearchParams();
    payload.append('column_id', colId);
    payload.append(window.EXCEL_CSRF.name, window.EXCEL_CSRF.hash);

    fetch(window.EXCEL_URLS.delete_custom_col, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const colIdx = (window.EXCEL_CUSTOM_COLS || []).findIndex(c => String(c.id) === String(colId));
            let isApproval = false;
            if (colIdx !== -1) {
                isApproval = (window.EXCEL_CUSTOM_COLS[colIdx].section === 'approval');
                window.EXCEL_CUSTOM_COLS.splice(colIdx, 1);
            }

            const superHeader = isApproval ? document.getElementById('thApprovalDetails') : document.getElementById('thInfluencerDetails');
            if (superHeader && superHeader.colSpan > 1) {
                superHeader.colSpan--;
            }

            const th = document.querySelector(`#excelColumnHeadersRow th[data-custom-id="${colId}"]`);
            if (th) th.remove();

            document.querySelectorAll(`.excel-cell[data-custom-col-id="${colId}"]`).forEach(td => td.remove());
        } else {
            alert(data.message || 'Error deleting column');
        }
    })
    .catch(err => {
        console.error('Delete custom column error:', err);
    });
}

// Change rows per page
function changeExcelRowsPerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('limit', val);
    url.searchParams.delete('per_page');
    window.location.href = url.toString();
}

// Filter panel toggle
function toggleExcelFilters() {
    const panel = document.getElementById('excelFiltersPanel');
    if (panel) {
        panel.classList.toggle('hidden');
    }
}

function toggleExcelDropdown(id) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('hidden');
}

function confirmDeleteAll() {
    if (confirm('Are you sure you want to delete ALL influencers? This cannot be undone.')) {
        window.location.href = window.EXCEL_URLS.delete_all;
    }
}
</script>
