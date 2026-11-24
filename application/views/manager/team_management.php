<div class="p-8 pb-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Team Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage Armful Media's internal team, roles, and track their sourcing performance.</p>
        </div>
        <button onclick="openInviteModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-xl text-sm font-bold hover:bg-brand-deep transition-all shadow-lg shadow-green-500/20">
            <i class="fas fa-plus"></i>
            <span>Add Team Member</span>
        </button>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-sm">
            <p class="text-sm font-medium"><?php echo $this->session->flashdata('success'); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm">
            <p class="text-sm font-medium"><?php echo $this->session->flashdata('error'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Performance Cards (Top Members) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-brand-green/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 bg-brand-green/10 rounded-xl flex items-center justify-center text-brand-green">
                    <i class="fas fa-search"></i>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Sourced</span>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-900 relative">
                <?php 
                $total_sourced = array_sum(array_column($members, 'total_sourced'));
                echo number_format($total_sourced);
                ?>
            </h3>
            <p class="text-xs text-gray-500 mt-1 relative">Across all active team members</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                    <i class="fas fa-trophy"></i>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Ambassadors</span>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-900 relative">
                <?php 
                $total_ambassadors = array_sum(array_column($members, 'ambassadors'));
                echo number_format($total_ambassadors);
                ?>
            </h3>
            <p class="text-xs text-gray-500 mt-1 relative">Converted influencers</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center justify-between mb-4 relative">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Average ROI</span>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-900 relative">
                <?php 
                $total_gmv = array_sum(array_column($members, 'total_gmv'));
                $total_cost = array_sum(array_column($members, 'total_cost'));
                $avg_roi = $total_cost > 0 ? round($total_gmv / $total_cost, 2) : 0;
                echo $avg_roi . 'x';
                ?>
            </h3>
            <p class="text-xs text-gray-500 mt-1 relative">Campaign value multiplier</p>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
            <h4 class="font-bold text-gray-900">Active Team Members</h4>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-[10px] font-bold text-gray-500">
                    <?php echo count($members); ?> TOTAL
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Member</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Sourcing</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Conversion</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">ROI</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Activity</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach($members as $member): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-11 h-11 rounded-full bg-brand-green/10 text-brand-green flex items-center justify-center font-bold border-2 border-white shadow-sm overflow-hidden">
                                        <?php echo substr($member->name, 0, 1); ?>
                                    </div>
                                    <?php 
                                    $is_online = false;
                                    if (!empty($member->last_login)) {
                                        $is_online = (time() - strtotime($member->last_login)) < 300;
                                    }
                                    ?>
                                    <?php if($is_online): ?>
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white shadow-sm"></div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?php echo $member->name; ?></p>
                                    <p class="text-[11px] text-gray-500"><?php echo $member->email; ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex w-fit px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php 
                                    echo match($member->role) {
                                        'owner' => 'bg-amber-100 text-amber-700',
                                        'admin' => 'bg-blue-100 text-blue-700',
                                        'manager' => 'bg-purple-100 text-purple-700',
                                        default => 'bg-gray-100 text-gray-600'
                                    };
                                ?>">
                                    <?php echo $member->role; ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-sm font-bold text-gray-900"><?php echo number_format($member->total_sourced); ?></p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase"><?php echo number_format($member->ambassadors); ?> WON</p>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <p class="text-sm font-bold text-brand-green"><?php echo $member->conversion_rate; ?>%</p>
                                <div class="w-16 h-1 bg-gray-100 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-brand-green rounded-full" style="width: <?php echo min(100, $member->conversion_rate); ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-bold <?php echo $member->roi >= 3 ? 'bg-blue-50 text-blue-700' : ($member->roi > 0 ? 'bg-gray-50 text-gray-900' : 'text-gray-300'); ?>">
                                <?php echo $member->roi; ?>x
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold <?php echo $is_online ? 'text-green-600' : 'text-gray-500'; ?>">
                                    <?php echo $is_online ? 'Online Now' : 'Offline'; ?>
                                </span>
                                <span class="text-[10px] text-gray-400 mt-0.5">
                                    <?php 
                                    if (!empty($member->last_login)) {
                                        echo timespan(strtotime($member->last_login), time(), 1) . ' ago';
                                    } else {
                                        echo 'Never logged in';
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php if($member->role !== 'owner' && $member->user_id != $this->session->userdata('user_id')): ?>
                                    <button onclick="openRoleModal(<?php echo $member->user_id; ?>, '<?php echo $member->role; ?>', '<?php echo addslashes($member->name); ?>')" class="p-2 text-gray-400 hover:text-brand-blue hover:bg-blue-50 rounded-lg transition-all" title="Change Role">
                                        <i class="fas fa-user-tag"></i>
                                    </button>
                                    <button onclick="confirmRemove(<?php echo $member->user_id; ?>, '<?php echo addslashes($member->name); ?>')" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Remove Member">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest px-3">Protected</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invite Member Modal -->
<div id="inviteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-900">Add Team Member</h3>
                <button onclick="closeInviteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="<?php echo base_url('manager/invite_member'); ?>" method="POST" class="p-8">
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="Enter member's email...">
                        <p class="mt-2 text-[10px] text-gray-400">Note: The user must already have an Instroom account.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Assign Role</label>
                        <select name="role" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all cursor-pointer">
                            <option value="collaborator">Collaborator (Standard)</option>
                            <option value="manager">Manager (Elevated)</option>
                            <option value="admin">Admin (Full Control)</option>
                            <option value="viewer">Viewer (Read-only)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeInviteModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-brand-green text-white rounded-xl font-bold text-sm hover:bg-brand-deep transition-all shadow-lg shadow-green-500/20">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div id="roleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-900">Change Member Role</h3>
                <button onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="<?php echo base_url('manager/update_member_role'); ?>" method="POST" class="p-8">
                <input type="hidden" name="user_id" id="role_user_id">
                <div class="mb-6">
                    <p class="text-sm text-gray-600">Update role for <span id="role_user_name" class="font-bold text-gray-900"></span></p>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Select New Role</label>
                        <select name="role" id="role_select" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all cursor-pointer">
                            <option value="collaborator">Collaborator</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeRoleModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-brand-dark text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/20">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmRemove(userId, name) {
    if (confirm('Are you sure you want to remove ' + name + ' from the team? This action cannot be undone.')) {
        window.location.href = '<?php echo base_url("manager/remove_team_member/"); ?>' + userId;
    }
}

function openInviteModal() {
    document.getElementById('inviteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeInviteModal() {
    document.getElementById('inviteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openRoleModal(userId, currentRole, name) {
    document.getElementById('role_user_id').value = userId;
    document.getElementById('role_user_name').textContent = name;
    document.getElementById('role_select').value = currentRole;
    document.getElementById('roleModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeInviteModal();
        closeRoleModal();
    }
});
</script>
