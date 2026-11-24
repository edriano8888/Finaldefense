<div class="space-y-8 pb-16">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Orders & Discounts Management</h1>
            <p class="text-xs text-gray-500 mt-1 font-medium">Manage product sample requests, influencer gifting packages, and promo coupon codes.</p>
        </div>
        <div>
            <button onclick="openCreateOrderModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-deep to-brand-green text-white rounded-2xl text-xs font-extrabold hover:opacity-95 transition-all shadow-lg shadow-green-500/20 transform hover:-translate-y-0.5">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Create Sample Request</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
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

    <!-- Summary Stats Cards -->
    <?php
        $total_cost_sum = 0;
        if (!empty($pending_orders)) {
            foreach ($pending_orders as $o) {
                $total_cost_sum += (float)($o->total_cost ?? 0);
            }
        }
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Pending Review</p>
                    <h4 class="text-2xl font-black text-amber-600 mt-1"><?php echo isset($stats['pending']) ? (int)$stats['pending'] : 0; ?></h4>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center text-lg">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Approved Requests</p>
                    <h4 class="text-2xl font-black text-brand-green mt-1"><?php echo isset($stats['approved']) ? (int)$stats['approved'] : 0; ?></h4>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Rejected Requests</p>
                    <h4 class="text-2xl font-black text-gray-900 mt-1"><?php echo isset($stats['rejected']) ? (int)$stats['rejected'] : 0; ?></h4>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 text-red-500 flex items-center justify-center text-lg">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Total Requests Spent</p>
                    <h4 class="text-2xl font-black text-emerald-800 mt-1">$<?php echo number_format($total_cost_sum, 2); ?></h4>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-2">Filter:</span>
            <a href="<?php echo base_url('manager/orders_discounts'); ?>" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all <?php echo (!isset($current_status) || $current_status === 'all') ? 'bg-brand-deep text-white shadow-sm' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'; ?>">
                All Requests (<?php echo isset($stats['total']) ? (int)$stats['total'] : 0; ?>)
            </a>
            <a href="<?php echo base_url('manager/orders_discounts?status=pending'); ?>" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all <?php echo (isset($current_status) && $current_status === 'pending') ? 'bg-amber-500 text-white shadow-sm' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'; ?>">
                Pending (<?php echo isset($stats['pending']) ? (int)$stats['pending'] : 0; ?>)
            </a>
            <a href="<?php echo base_url('manager/orders_discounts?status=approved'); ?>" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all <?php echo (isset($current_status) && $current_status === 'approved') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'; ?>">
                Approved (<?php echo isset($stats['approved']) ? (int)$stats['approved'] : 0; ?>)
            </a>
            <a href="<?php echo base_url('manager/orders_discounts?status=rejected'); ?>" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all <?php echo (isset($current_status) && $current_status === 'rejected') ? 'bg-red-500 text-white shadow-sm' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'; ?>">
                Rejected (<?php echo isset($stats['rejected']) ? (int)$stats['rejected'] : 0; ?>)
            </a>
        </div>
        <div class="text-xs font-bold text-gray-400">
            Showing <?php echo !empty($pending_orders) ? count($pending_orders) : 0; ?> records
        </div>
    </div>

    <!-- Table of Orders -->
    <div class="bg-white rounded-3xl shadow-card border border-gray-200/80 overflow-hidden">
        <?php if(isset($pending_orders) && !empty($pending_orders)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-200 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Order ID & Influencer</th>
                            <th class="px-6 py-4">Requested Product</th>
                            <th class="px-6 py-4">Coupon Code</th>
                            <th class="px-6 py-4 text-center">Cost</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($pending_orders as $order): ?>
                            <tr class="hover:bg-emerald-50/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-500 border border-orange-100 flex items-center justify-center text-sm font-bold shrink-0">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-900">#<?php echo str_pad($order->id, 6, '0', STR_PAD_LEFT); ?></p>
                                            <p class="text-[11px] font-bold text-brand-deep flex items-center gap-1 mt-0.5">
                                                <i class="far fa-user text-[9px]"></i>
                                                <?php echo !empty($order->influencer_name) ? html_escape($order->influencer_name) : 'Allocated Creator'; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900"><?php echo html_escape($order->product_name); ?></p>
                                    <?php if(!empty($order->notes)): ?>
                                        <p class="text-[11px] text-gray-400 font-medium truncate max-w-xs mt-0.5"><?php echo html_escape($order->notes); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if(!empty($order->coupon_code)): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-mono font-bold text-xs">
                                            <i class="fas fa-ticket-alt text-[10px]"></i> <?php echo html_escape($order->coupon_code); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic text-[11px]">No coupon</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center font-black text-gray-900 whitespace-nowrap">
                                    $<?php echo number_format((float)$order->total_cost, 2); ?>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <?php
                                        $status = strtolower($order->status ?? 'pending');
                                        $st_colors = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'shipped' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'rejected' => 'bg-red-50 text-red-700 border-red-200'
                                        ];
                                        $st_badge = $st_colors[$status] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border tracking-wider <?php echo $st_badge; ?>">
                                        <?php echo html_escape($status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500 font-medium whitespace-nowrap">
                                    <?php echo date('M d, Y', strtotime($order->created_at)); ?>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <?php if($status === 'pending'): ?>
                                            <?php echo form_open('manager/update_order_status', ['class' => 'inline-flex gap-1.5']); ?>
                                                <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                                <button type="submit" name="status" value="approved" class="bg-brand-green hover:bg-brand-deep text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1">
                                                    <i class="fas fa-check text-[10px]"></i> Approve
                                                </button>
                                                <button type="submit" name="status" value="rejected" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1">
                                                    <i class="fas fa-times text-[10px]"></i> Reject
                                                </button>
                                            <?php echo form_close(); ?>
                                        <?php elseif($status === 'approved'): ?>
                                            <?php echo form_open('manager/update_order_status', ['class' => 'inline']); ?>
                                                <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                                <button type="submit" name="status" value="shipped" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1">
                                                    <i class="fas fa-shipping-fast text-[10px]"></i> Mark Shipped
                                                </button>
                                            <?php echo form_close(); ?>
                                        <?php elseif($status === 'shipped'): ?>
                                            <?php echo form_open('manager/update_order_status', ['class' => 'inline']); ?>
                                                <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                                <button type="submit" name="status" value="delivered" class="bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1">
                                                    <i class="fas fa-box-open text-[10px]"></i> Mark Delivered
                                                </button>
                                            <?php echo form_close(); ?>
                                        <?php else: ?>
                                            <span class="text-xs font-bold text-gray-400">Completed</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-brand-green text-2xl border border-emerald-100">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">No product sample requests found</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 font-medium">
                    Create a sample order request for influencer seeding packages or promo code giveaways.
                </p>
                <button onclick="openCreateOrderModal()" class="mt-5 inline-flex items-center gap-2 bg-brand-green hover:bg-brand-deep text-white px-5 py-2.5 rounded-2xl text-xs font-extrabold transition-all shadow-md">
                    <i class="fas fa-plus text-[10px]"></i> Create Sample Request
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Sample Request Modal -->
<div id="createOrderModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Create Sample Request</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Authorise sample products or promo codes for creator outreach.</p>
                </div>
                <button onclick="closeCreateOrderModal()" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-200/60 transition-colors">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>
            <?php echo form_open('manager/create_sample_order', ['class' => 'p-8 space-y-5']); ?>
                <div>
                    <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Requested Product Name</label>
                    <input type="text" name="product_name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="e.g. Instroom VIP Seeding Box / Hoodie">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Influencer / Creator Name</label>
                    <input type="text" name="influencer_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="e.g. @kayceesanmocte">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Sample Cost ($)</label>
                        <input type="number" step="0.01" min="0" name="total_cost" value="45.00" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Promo / Coupon Code</label>
                        <input type="text" name="coupon_code" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="e.g. EDRIAN15">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Tracking Number (Optional)</label>
                    <input type="text" name="tracking_number" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="e.g. DHL-98437264">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Notes & Shipping Instructions</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all" placeholder="Size details, shipping address, or campaign note..."></textarea>
                </div>
                <div class="mt-8 flex gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCreateOrderModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-xs hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-brand-green text-white rounded-2xl font-extrabold text-xs hover:bg-brand-deep transition-all shadow-md">Submit Request</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function openCreateOrderModal() {
    document.getElementById('createOrderModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCreateOrderModal() {
    document.getElementById('createOrderModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCreateOrderModal();
});
</script>
