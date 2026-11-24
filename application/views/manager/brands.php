<div class="space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Brand Portfolio Management</h1>
            <p class="text-xs text-gray-500 mt-1 font-medium">Manage client brand profiles, budgets, platforms, and team access under Armful Media.</p>
        </div>
        <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-deep to-brand-green text-white rounded-2xl text-xs font-extrabold hover:opacity-95 transition-all shadow-lg shadow-green-500/20 transform hover:-translate-y-0.5">
            <i class="fas fa-plus text-[10px]"></i>
            <span>Add New Client Brand</span>
        </button>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-2xl shadow-xs flex items-center justify-between">
            <p class="text-xs font-bold flex items-center gap-2"><i class="fas fa-check-circle text-emerald-500"></i> <?php echo $this->session->flashdata('success'); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-2xl shadow-xs flex items-center justify-between">
            <p class="text-xs font-bold flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-500"></i> <?php echo $this->session->flashdata('error'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Summary KPI Cards Grid -->
    <?php 
        $total_brands = !empty($brands) ? count($brands) : 0;
        $total_active_campaigns = 0;
        $total_portfolio_spent = 0;
        if (!empty($brands)) {
            foreach ($brands as $b) {
                $total_active_campaigns += (int)$b->active_campaigns;
                $total_portfolio_spent += (float)$b->total_budget_spent;
            }
        }
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="rounded-3xl border border-gray-200/80 bg-white p-5 shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Total Client Brands</p>
                    <p class="text-2xl font-black text-gray-900 mt-1"><?php echo $total_brands; ?></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
        <div class="rounded-3xl border border-gray-200/80 bg-white p-5 shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Active Campaigns</p>
                    <p class="text-2xl font-black text-brand-deep mt-1"><?php echo $total_active_campaigns; ?></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                    <i class="fas fa-bullhorn"></i>
                </div>
            </div>
        </div>
        <div class="rounded-3xl border border-gray-200/80 bg-white p-5 shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Total Portfolio Spent</p>
                    <p class="text-2xl font-black text-emerald-800 mt-1">$<?php echo number_format($total_portfolio_spent, 2); ?></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="brandSearch" onkeyup="filterBrands()" placeholder="Search brand name or industry..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
        </div>
        <p class="text-xs font-bold text-gray-400"><span id="visibleBrandCount"><?php echo $total_brands; ?></span> brand profile(s) showing</p>
    </div>

    <!-- Brand Cards Grid -->
    <div id="brandGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <?php if (!empty($brands)): ?>
            <?php $active_brand_id = (int)$this->session->userdata('brand_id'); ?>
            <?php foreach($brands as $b): ?>
                <div class="brand-card bg-white rounded-[32px] border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col <?php echo ((int)$b->id === $active_brand_id) ? 'ring-2 ring-brand-green' : ''; ?>" data-name="<?php echo strtolower(html_escape($b->name)); ?>" data-industry="<?php echo strtolower(html_escape($b->industry ?: '')); ?>">
                    <!-- Card Top: Brand Header -->
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-white text-brand-green flex items-center justify-center text-xl font-bold border border-gray-100 shadow-sm">
                                    <?php echo strtoupper(substr($b->name, 0, 1)); ?>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <?php echo html_escape($b->name); ?>
                                        <?php if ((int)$b->id === $active_brand_id): ?>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">ACTIVE</span>
                                        <?php endif; ?>
                                    </h3>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mt-0.5">
                                        <?php echo $b->industry ? html_escape($b->industry) : 'General Industry'; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if ((int)$b->id !== $active_brand_id): ?>
                                    <a href="<?php echo base_url('auth/switch_brand/' . (int)$b->id); ?>" class="px-3 py-2 bg-emerald-50 text-brand-deep hover:bg-emerald-100 flex items-center gap-1.5 rounded-xl border border-emerald-200 text-xs font-bold transition-all shadow-xs" title="Switch Workspace to this Brand">
                                        <i class="fas fa-exchange-alt text-[10px]"></i> Switch
                                    </a>
                                <?php endif; ?>
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($b)); ?>)" class="w-9 h-9 bg-white text-gray-400 hover:text-blue-600 flex items-center justify-center rounded-xl border border-gray-200 hover:border-blue-400 transition-all shadow-xs" title="Edit Brand Profile">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body: Stats Summary -->
                    <div class="p-8 grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Active Campaigns</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-black text-gray-900"><?php echo $b->active_campaigns; ?></span>
                                <span class="text-xs font-semibold text-gray-400">/ <?php echo $b->total_campaigns; ?> total</span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Budget Spent</p>
                            <span class="text-2xl font-black text-brand-green">$<?php echo number_format($b->total_budget_spent, 2); ?></span>
                        </div>

                        <!-- Info Pills (Budget Range & Platforms) -->
                        <div class="col-span-2 flex flex-wrap gap-2 pt-2">
                            <?php if(!empty($b->budget_range)): ?>
                                <span class="px-3 py-1 bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl flex items-center gap-1.5">
                                    <i class="fas fa-dollar-sign text-emerald-600 text-[10px]"></i> <?php echo html_escape($b->budget_range); ?>
                                </span>
                            <?php endif; ?>
                            <?php if(!empty($b->preferred_platforms)): ?>
                                <span class="px-3 py-1 bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl flex items-center gap-1.5">
                                    <i class="fas fa-share-alt text-brand-green text-[10px]"></i> <?php echo html_escape($b->preferred_platforms); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Top Influencers Section -->
                        <div class="col-span-2 pt-4 border-t border-gray-50">
                            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Top Influencers Used</p>
                            <div class="flex flex-wrap gap-2">
                                <?php if (!empty($b->top_influencers)): ?>
                                    <?php foreach($b->top_influencers as $inf): ?>
                                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-full border border-gray-100">
                                            <div class="w-5 h-5 rounded-full bg-brand-green/20 text-[10px] flex items-center justify-center text-brand-green font-bold">
                                                <?php echo strtoupper(substr($inf->full_name, 0, 1)); ?>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700"><?php echo html_escape($inf->full_name); ?></span>
                                            <span class="text-[10px] text-gray-400 font-bold"><?php echo $inf->usage_count; ?>x</span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-xs text-gray-400 italic font-medium">No influencers recorded yet for this client.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Contact Person -->
                    <div class="mt-auto px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user-circle text-gray-300 text-lg"></i>
                            <div>
                                <p class="text-xs font-bold text-gray-800"><?php echo !empty($b->contact_person) ? html_escape($b->contact_person) : 'No Primary Contact'; ?></p>
                                <p class="text-[10px] text-gray-400 font-medium"><?php echo !empty($b->email) ? html_escape($b->email) : '---'; ?></p>
                            </div>
                        </div>
                        <?php if ((int)$b->id !== $active_brand_id): ?>
                            <button onclick="confirmDelete(<?php echo (int)$b->id; ?>, '<?php echo addslashes($b->name); ?>')" class="text-[10px] font-bold text-red-500 hover:text-red-700 uppercase tracking-widest transition-colors flex items-center gap-1">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-2 bg-white rounded-3xl border border-dashed border-gray-200 p-20 text-center">
                <i class="fas fa-building text-4xl text-gray-200 mb-4"></i>
                <p class="text-gray-500 font-medium">No client brand profiles found. Start by creating a new brand profile.</p>
                <button onclick="openCreateModal()" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-2xl text-xs font-bold hover:bg-brand-deep transition-all shadow-md">
                    <i class="fas fa-plus text-[10px]"></i> Add New Client Brand
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Client Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Add New Client Profile</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Create a new brand profile under Armful Media portfolio.</p>
                </div>
                <button onclick="closeCreateModal()" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-200/60 transition-colors">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>
            <?php echo form_open('manager/create_brand', ['class' => 'p-8']); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left: Brand Core -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Brand Identity Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="e.g. Nike, Starbucks">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Industry / Niche</label>
                            <input type="text" name="industry" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="e.g. Fashion, Skincare, Tech">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Budget Range</label>
                                <input type="text" name="budget_range" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="$1k - $5k">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Platforms</label>
                                <input type="text" name="preferred_platforms" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="TikTok, Instagram">
                            </div>
                        </div>
                    </div>
                    <!-- Right: Contact & Notes -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Primary Contact Name</label>
                            <input type="text" name="contact_person" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="e.g. Jane Doe">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="client@email.com">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Phone</label>
                                <input type="text" name="phone" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="+1 234 567...">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Client Notes</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="Goals, target demographics, key deliverables..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-xs hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-brand-green text-white rounded-2xl font-extrabold text-xs hover:bg-brand-deep transition-all shadow-md">Create Brand Profile</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Edit Client Profile</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Update profile information for <span id="display_brand_name" class="text-brand-green font-bold"></span>.</p>
                </div>
                <button onclick="closeEditModal()" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-200/60 transition-colors">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>
            <?php echo form_open('manager/update_brand', ['class' => 'p-8']); ?>
                <input type="hidden" name="id" id="edit_brand_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Brand Identity Name</label>
                            <input type="text" name="name" id="edit_brand_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Industry / Niche</label>
                            <input type="text" name="industry" id="edit_brand_industry" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Budget Range</label>
                                <input type="text" name="budget_range" id="edit_brand_budget" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Status</label>
                                <select name="status" id="edit_brand_status" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all cursor-pointer">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Preferred Platforms</label>
                            <input type="text" name="preferred_platforms" id="edit_brand_platforms" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Primary Contact Name</label>
                            <input type="text" name="contact_person" id="edit_brand_contact" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" name="email" id="edit_brand_email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Phone</label>
                                <input type="text" name="phone" id="edit_brand_phone" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Client Notes</label>
                            <textarea name="notes" id="edit_brand_notes" rows="4" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-xs hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-brand-dark text-white rounded-2xl font-extrabold text-xs hover:bg-gray-800 transition-all shadow-md">Update Profile</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function filterBrands() {
    const query = document.getElementById('brandSearch').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.brand-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const industry = card.getAttribute('data-industry') || '';
        if (name.includes(query) || industry.includes(query)) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const countEl = document.getElementById('visibleBrandCount');
    if (countEl) countEl.textContent = visibleCount;
}

function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openEditModal(brand) {
    document.getElementById('edit_brand_id').value = brand.id;
    document.getElementById('edit_brand_name').value = brand.name;
    document.getElementById('display_brand_name').textContent = brand.name;
    document.getElementById('edit_brand_industry').value = brand.industry || '';
    document.getElementById('edit_brand_contact').value = brand.contact_person || '';
    document.getElementById('edit_brand_email').value = brand.email || '';
    document.getElementById('edit_brand_phone').value = brand.phone || '';
    document.getElementById('edit_brand_budget').value = brand.budget_range || '';
    document.getElementById('edit_brand_platforms').value = brand.preferred_platforms || '';
    document.getElementById('edit_brand_notes').value = brand.notes || '';
    document.getElementById('edit_brand_status').value = brand.status;
    
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmDelete(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"? This will remove all associated data and cannot be undone.')) {
        window.location.href = '<?php echo base_url("manager/delete_brand/"); ?>' + id;
    }
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
    }
});
</script>
