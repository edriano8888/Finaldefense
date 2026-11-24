<div class="bg-white border border-gray-100 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow cursor-pointer relative group">
    <div class="flex justify-between items-start mb-3">
        <div class="flex items-center space-x-2">
            <!-- Platform Icon -->
            <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                <?php if(isset($influencer->platform) && $influencer->platform == 'Instagram'): ?>
                    <i class="fab fa-instagram"></i>
                <?php elseif(isset($influencer->platform) && $influencer->platform == 'TikTok'): ?>
                    <i class="fab fa-tiktok"></i>
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <!-- Username -->
            <span class="font-bold text-sm text-gray-800 truncate max-w-[120px]" title="<?= isset($influencer->username) ? $influencer->username : '' ?>">
                <?= isset($influencer->username) ? $influencer->username : 'Unknown' ?>
            </span>
        </div>
        <!-- Email Icon -->
        <button class="text-brand-blue hover:text-blue-700">
            <i class="fas fa-envelope"></i>
        </button>
    </div>

    <div class="flex justify-between items-center mb-3">
        <div class="flex items-center space-x-1 text-gray-600">
            <i class="fas fa-user-friends text-xs"></i>
            <span class="text-xs font-medium"><?= isset($influencer->followers) ? number_format($influencer->followers) : '0' ?></span>
        </div>
        <div class="flex items-center space-x-1 text-gray-600">
            <i class="fas fa-comment text-xs"></i>
            <span class="text-xs font-medium"><?= isset($influencer->eng_rate) ? $influencer->eng_rate : (isset($influencer->engagement_rate) ? $influencer->engagement_rate : '0') ?>%</span>
        </div>
    </div>

    <div class="text-xs text-brand-blue font-medium pt-2 border-t border-gray-50">
        Contacted via Email
    </div>
</div>