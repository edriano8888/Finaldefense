<div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Brand Approvals</h3>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4">
            <span class="block sm:inline"><?php echo $this->session->flashdata('success'); ?></span>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if(isset($pending_brands) && !empty($pending_brands)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-900 font-bold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4">Brand Name</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Website</th>
                            <th class="p-4">Contact</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($pending_brands as $brand): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 font-bold text-gray-900"><?php echo $brand->name; ?></td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-bold uppercase"><?php echo $brand->category; ?></span>
                                </td>
                                <td class="p-4"><a href="<?php echo $brand->website; ?>" target="_blank" class="text-brand-green hover:underline flex items-center gap-1"><i class="fas fa-external-link-alt text-[10px]"></i> Visit</a></td>
                                <td class="p-4">
                                    <div class="font-semibold"><?php echo $brand->contact_person; ?></div>
                                    <div class="text-[11px] text-gray-400"><?php echo $brand->email; ?></div>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="<?php echo base_url('manager/update_brand_status'); ?>" method="post" class="inline-flex gap-2">
                                        <input type="hidden" name="id" value="<?php echo $brand->id; ?>">
                                        <button type="submit" name="status" value="approved" class="bg-brand-green hover:bg-brand-deep text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                                            Approve
                                        </button>
                                        <button type="submit" name="status" value="rejected" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-double text-gray-300 text-2xl"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900">All caught up!</h4>
                <p class="text-sm text-gray-400">No pending brands to approve at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>