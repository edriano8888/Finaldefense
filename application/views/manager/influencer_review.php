<div class="space-y-8 pb-16">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Influencer Review Queue</h1>
            <p class="text-xs text-gray-500 mt-0.5">Approve or reject sourced influencer profiles before team outreach begins.</p>
        </div>
        
        <?php if(isset($pending_influencers) && count($pending_influencers) > 0): ?>
            <div class="flex flex-wrap items-center gap-3 bg-white px-4 py-2.5 rounded-2xl border border-gray-100 shadow-xs">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider" id="selectedCount">0 Selected</span>
                <div class="h-4 w-px bg-gray-200 mx-1"></div>
                <button type="button" onclick="bulkAction('approve')" class="bg-brand-green hover:bg-brand-deep text-white px-4 py-2 rounded-xl text-xs font-bold transition-all opacity-50 pointer-events-none shadow-sm shadow-green-500/10" id="bulkApproveBtn" disabled>
                    <i class="fas fa-check mr-1"></i> Approve
                </button>
                <button type="button" onclick="bulkAction('reject')" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold transition-all opacity-50 pointer-events-none border border-red-100" id="bulkRejectBtn" disabled>
                    <i class="fas fa-times mr-1"></i> Reject
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Feedback Alerts -->
    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg shadow-xs">
            <p class="text-sm font-medium"><?php echo $this->session->flashdata('success'); ?></p>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-xs">
            <p class="text-sm font-medium"><?php echo $this->session->flashdata('error'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-lg">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waiting Review</p>
                <h4 class="text-2xl font-black text-gray-900 mt-1"><?php echo count($pending_influencers); ?></h4>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-lg">
                <i class="fas fa-filter"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Active Filters</p>
                <h4 class="text-xs font-semibold text-gray-700 mt-1">
                    <?php echo isset($current_platform) && $current_platform !== 'all' ? html_escape($current_platform) : 'All Platforms'; ?>
                </h4>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-brand-green flex items-center justify-center text-lg">
                <i class="fas fa-shield-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Compliance Status</p>
                <h4 class="text-xs font-bold text-brand-green mt-1">Active Verification</h4>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <form method="GET" action="<?php echo base_url('manager/influencer_review'); ?>" class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-3xl border border-gray-100 shadow-xs">
        <div class="flex-1 min-w-[240px] relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="<?php echo isset($current_search) ? htmlspecialchars($current_search) : ''; ?>" placeholder="Search creator username, name..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
        </div>
        <select name="platform" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-gray-100 text-xs outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green bg-white cursor-pointer font-bold text-gray-600">
            <option value="all" <?php echo (!isset($current_platform) || $current_platform === 'all') ? 'selected' : ''; ?>>All Networks</option>
            <option value="Instagram" <?php echo (isset($current_platform) && $current_platform === 'Instagram') ? 'selected' : ''; ?>>Instagram</option>
            <option value="TikTok" <?php echo (isset($current_platform) && $current_platform === 'TikTok') ? 'selected' : ''; ?>>TikTok</option>
            <option value="YouTube" <?php echo (isset($current_platform) && $current_platform === 'YouTube') ? 'selected' : ''; ?>>YouTube</option>
            <option value="Facebook" <?php echo (isset($current_platform) && $current_platform === 'Facebook') ? 'selected' : ''; ?>>Facebook</option>
        </select>
        <button type="submit" class="bg-brand-green hover:bg-brand-deep text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-xs">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
        <?php if(!empty($current_search) || (isset($current_platform) && $current_platform !== 'all')): ?>
            <a href="<?php echo base_url('manager/influencer_review'); ?>" class="text-xs text-gray-500 hover:text-red-500 font-bold px-2 flex items-center gap-1">
                <i class="fas fa-times-circle"></i> Clear Filters
            </a>
        <?php endif; ?>
    </form>

    <!-- Bulk Update Form -->
    <form id="bulkForm" method="POST" action="<?php echo base_url('manager/bulk_update_influencers'); ?>">
        <input type="hidden" name="ids" id="selectedIds">
        <input type="hidden" name="action" id="bulkAction">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    </form>

    <!-- Table of Pending Review Sourced Influencers -->
    <div class="bg-white rounded-[32px] shadow-xs border border-gray-100 overflow-hidden">
        <?php if(isset($pending_influencers) && !empty($pending_influencers)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 w-12 text-center">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-brand-green focus:ring-brand-green/20">
                            </th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Creator Profile</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Network</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Sourcing Status</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Followers</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($pending_influencers as $inf): ?>
                            <tr class="hover:bg-gray-50/40 transition-colors cursor-pointer group" onclick="showDetail(<?php echo $inf->id; ?>)">
                                <td class="px-6 py-4 text-center" onclick="event.stopPropagation();">
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-brand-green focus:ring-brand-green/20" value="<?php echo $inf->id; ?>">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?php echo !empty($inf->profile_image) ? html_escape($inf->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($inf->username ?? 'U'); ?>" class="w-10 h-10 rounded-full border border-gray-100 shadow-xs object-cover">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-900 group-hover:text-brand-green transition-colors truncate">
                                                <?php echo html_escape($inf->full_name ?: ($inf->username ?: 'Unknown')); ?>
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">@<?php echo html_escape($inf->username ?? '-'); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $platform = $inf->platform ?? 'Unknown';
                                        $pf_colors = [
                                            'Instagram' => 'bg-pink-50 text-pink-600 border-pink-100',
                                            'TikTok' => 'bg-gray-900 text-white border-transparent',
                                            'YouTube' => 'bg-red-50 text-red-600 border-red-100',
                                            'Facebook' => 'bg-blue-50 text-blue-600 border-blue-100'
                                        ];
                                        $pf_badge = $pf_colors[$platform] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                                    ?>
                                    <span class="px-2.5 py-0.5 rounded text-[9px] font-bold uppercase border tracking-wider <?php echo $pf_badge; ?>">
                                        <?php echo html_escape($platform); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php 
                                        $status = $inf->pipeline_status ?? 'Sourced';
                                        $st_colors = [
                                            'Sourced' => 'bg-orange-50 text-orange-600 border-orange-100',
                                            'Contacted' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'Rejected' => 'bg-red-50 text-red-600 border-red-100'
                                        ];
                                        $st_badge = $st_colors[$status] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                                    ?>
                                    <span class="px-2.5 py-0.5 rounded text-[9px] font-bold uppercase border tracking-wider <?php echo $st_badge; ?>">
                                        <?php echo html_escape($status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                                    <?php echo isset($inf->followers) ? number_format($inf->followers) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 text-right" onclick="event.stopPropagation();">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="showDetail(<?php echo $inf->id; ?>)" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-brand-green flex items-center justify-center transition-all" title="Review Details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        <?php if($status === 'Sourced'): ?>
                                            <form action="<?php echo base_url('manager/update_influencer_status'); ?>" method="post" class="inline">
                                                <input type="hidden" name="id" value="<?php echo $inf->id; ?>">
                                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                <button type="submit" name="status" value="Contacted" class="w-8 h-8 rounded-lg bg-brand-green hover:bg-brand-deep text-white flex items-center justify-center transition-all shadow-xs" title="Approve">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </form>
                                            <form action="<?php echo base_url('manager/update_influencer_status'); ?>" method="post" class="inline">
                                                <input type="hidden" name="id" value="<?php echo $inf->id; ?>">
                                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                <button type="submit" name="status" value="Rejected" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 flex items-center justify-center transition-all" title="Reject">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-20">
                <div class="w-16 h-16 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Queue is Clear!</h3>
                <p class="text-xs text-gray-400 max-w-xs mx-auto mt-1">
                    No influencer profiles are currently waiting for qualification review.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Dynamic AJAX Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-[32px] shadow-2xl max-w-xl w-full max-h-[90vh] overflow-y-auto animate-fade-in-up">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between sticky top-0 bg-white rounded-t-[32px] z-10">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Creator Profile Qualification</h3>
                <p class="text-xs text-gray-400 mt-0.5">Review social stats and make an outreach decision.</p>
            </div>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-8" id="modalContent">
            <!-- Content will be injected dynamically via AJAX -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    const selectedCount = document.getElementById('selectedCount');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkUI();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkUI);
    });

    function updateBulkUI() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;

        if(selectedCount) selectedCount.textContent = count + ' selected';

        if (count > 0) {
            bulkApproveBtn.classList.remove('opacity-50', 'pointer-events-none');
            bulkApproveBtn.disabled = false;
            bulkRejectBtn.classList.remove('opacity-50', 'pointer-events-none');
            bulkRejectBtn.disabled = false;
        } else {
            bulkApproveBtn.classList.add('opacity-50', 'pointer-events-none');
            bulkApproveBtn.disabled = true;
            bulkRejectBtn.classList.add('opacity-50', 'pointer-events-none');
            bulkRejectBtn.disabled = true;
        }
    }

    window.bulkAction = function(action) {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);

        if (ids.length === 0) return;

        const confirmMsg = action === 'approve'
            ? 'Approve ' + ids.length + ' influencer(s) for brand outreach?'
            : 'Reject ' + ids.length + ' influencer(s) from campaign pipeline?';

        if (confirm(confirmMsg)) {
            document.getElementById('selectedIds').value = JSON.stringify(ids);
            document.getElementById('bulkAction').value = action;
            document.getElementById('bulkForm').submit();
        }
    };

    window.showDetail = async function(id) {
        const modal = document.getElementById('detailModal');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        // Set Loading Spinner
        content.innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 gap-3">
                <div class="animate-spin w-8 h-8 border-4 border-brand-green border-t-transparent rounded-full"></div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Loading Creator Profile...</p>
            </div>
        `;

        try {
            // Fetch live creator details using get_data AJAX endpoint
            const res = await fetch('<?php echo base_url("influencer/get_data/"); ?>' + id);
            if (!res.ok) throw new Error('Failed to load');
            const inf = await res.json();

            if (!inf) {
                content.innerHTML = '<p class="text-center text-gray-500 py-8">Influencer profile not found.</p>';
                return;
            }

            const platform = inf.platform || 'Instagram';
            const platformIcon = platform.toLowerCase() === 'instagram' ? 'fab fa-instagram text-pink-500' :
                               platform.toLowerCase() === 'tiktok' ? 'fab fa-tiktok text-gray-900' :
                               platform.toLowerCase() === 'youtube' ? 'fab fa-youtube text-red-500' : 'fas fa-globe text-gray-400';

            const email_field = inf.email ? `
                <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                    <i class="fas fa-envelope text-gray-400 text-sm w-4"></i>
                    <div>
                        <p class="text-[9px] text-gray-400 font-bold uppercase">Email Address</p>
                        <p class="text-xs font-bold text-gray-800">${inf.email}</p>
                    </div>
                </div>
            ` : '';

            const contact_field = inf.contact_number ? `
                <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                    <i class="fas fa-phone text-gray-400 text-sm w-4"></i>
                    <div>
                        <p class="text-[9px] text-gray-400 font-bold uppercase">Phone Number</p>
                        <p class="text-xs font-bold text-gray-800">${inf.contact_number}</p>
                    </div>
                </div>
            ` : '';

            const location_field = inf.location ? `
                <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                    <i class="fas fa-map-marker-alt text-gray-400 text-sm w-4"></i>
                    <div>
                        <p class="text-[9px] text-gray-400 font-bold uppercase">Location</p>
                        <p class="text-xs font-bold text-gray-800">${inf.location}</p>
                    </div>
                </div>
            ` : '';

            const notes_field = inf.notes ? `
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                    <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Outreach Notes / Brief</p>
                    <p class="text-xs text-gray-700 leading-relaxed">${inf.notes}</p>
                </div>
            ` : '';

            const csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
            const csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

            content.innerHTML = `
                <!-- Identity Card -->
                <div class="flex flex-col items-center mb-8 bg-gray-50/50 p-6 border border-gray-50 rounded-3xl">
                    <img src="${inf.profile_image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(inf.username || 'U')}"
                         class="w-20 h-20 rounded-full object-cover shadow-md border-2 border-white mb-3">
                    <h4 class="text-lg font-black text-gray-900">${inf.full_name || inf.username || 'Unknown'}</h4>
                    <p class="text-xs text-gray-500">@${inf.username || '-'}</p>
                    <div class="flex items-center gap-1.5 mt-2 bg-white px-3 py-1 rounded-full border border-gray-100 shadow-2xs">
                        <i class="${platformIcon}"></i>
                        <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider">${platform}</span>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-center">
                        <p class="text-xl font-black text-gray-900">${Number(inf.followers || 0).toLocaleString()}</p>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Followers</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-center">
                        <p class="text-xl font-black text-brand-green">${Number(inf.eng_rate || 0).toFixed(1)}%</p>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Engagement Rate</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-center">
                        <p class="text-xl font-black text-gray-900">$${Number(inf.gmv || 0).toLocaleString()}</p>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Est. GMV</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-center">
                        <p class="text-xl font-black text-gray-900">$${Number(inf.product_cost || 0).toLocaleString()}</p>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Product Cost</p>
                    </div>
                </div>

                <!-- Contacts & Notes -->
                <div class="space-y-3 mb-8">
                    ${email_field}
                    ${contact_field}
                    ${location_field}
                    ${notes_field}
                </div>

                <!-- Action Form -->
                <div class="flex gap-4 border-t border-gray-100 pt-6">
                    <form action="<?php echo base_url('manager/update_influencer_status'); ?>" method="post" class="flex-1">
                        <input type="hidden" name="id" value="${inf.id}">
                        <input type="hidden" name="${csrf_name}" value="${csrf_hash}">
                        <button type="submit" name="status" value="Contacted" class="w-full bg-brand-green hover:bg-brand-deep text-white py-3.5 rounded-2xl text-xs font-bold transition-all shadow-md shadow-green-500/10">
                            <i class="fas fa-check mr-1.5"></i> Approve Outreach
                        </button>
                    </form>
                    <form action="<?php echo base_url('manager/update_influencer_status'); ?>" method="post" class="flex-1">
                        <input type="hidden" name="id" value="${inf.id}">
                        <input type="hidden" name="${csrf_name}" value="${csrf_hash}">
                        <button type="submit" name="status" value="Rejected" class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-3.5 rounded-2xl text-xs font-bold transition-all border border-red-100">
                            <i class="fas fa-times mr-1.5"></i> Reject Profile
                        </button>
                    </form>
                </div>
            `;
        } catch (err) {
            content.innerHTML = '<p class="text-center text-red-500 py-8 font-bold">Failed to load creator profile details. Try again.</p>';
        }
    };

    window.closeDetail = function() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    };

    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDetail();
    });
});
</script>