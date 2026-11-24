    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <title>Search Results - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    }
                }
            }
        }



        // Unified header scripts are loaded from partials/header.php

        <?php
        if (!empty($results) && is_array($results)) {
            foreach ($results as &$__r) {
                $__pimg = (string)($__r['profile_image'] ?? '');
                if (strpos($__pimg, '/assets/cache/avatars/') === 0) {
                    $__disk = FCPATH . ltrim($__pimg, '/');
                    if (file_exists($__disk) && filesize($__disk) > 200) {
                        $__r['avatar_inline'] = 'data:image/jpeg;base64,' . base64_encode((string)@file_get_contents($__disk));
                    }
                }
                if (!empty($__r['recent_media']) && is_array($__r['recent_media'])) {
                    foreach ($__r['recent_media'] as &$__m) {
                        $__murl = trim((string)($__m['media_url'] ?? ''));
                        if (strpos($__murl, '/home/image_proxy?url=') === 0 || strpos($__murl, '/discovery/image_proxy?url=') === 0) {
                            $__parts = explode('?url=', $__murl, 2);
                            $__raw_u = isset($__parts[1]) ? rawurldecode($__parts[1]) : '';
                            if ($__raw_u !== '') {
                                $__hash_file = 'assets/cache/ig_media/' . md5($__raw_u) . '.jpg';
                                if (file_exists(FCPATH . $__hash_file) && filesize(FCPATH . $__hash_file) > 100) {
                                    $__m['media_url'] = '/' . $__hash_file;
                                } else {
                                    $__m['media_url'] = '/home/image_proxy?url=' . rawurlencode($__raw_u);
                                }
                            }
                        } elseif (strpos($__murl, 'cdninstagram.com') !== false || strpos($__murl, 'fbcdn.net') !== false || strpos($__murl, 'tiktokcdn.com') !== false) {
                            $__hash_file = 'assets/cache/ig_media/' . md5($__murl) . '.jpg';
                            if (file_exists(FCPATH . $__hash_file) && filesize(FCPATH . $__hash_file) > 100) {
                                $__m['media_url'] = '/' . $__hash_file;
                            } else {
                                $__m['media_url'] = '/home/image_proxy?url=' . rawurlencode($__murl);
                            }
                        }
                    }
                    unset($__m);
                }
            }
            unset($__r);
        }
        ?>
        const DISCOVERY_RESULTS = <?php echo json_encode($results, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            refreshNotifSummary();
            initBulkSelection();
        });

        function isElementVisible(el) {
            if (!el) return false;
            const row = el.closest('tr');
            if (!row) return true;
            if (row.classList.contains('hidden')) return false;
            const style = window.getComputedStyle(row);
            return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
        }

        function getRowCheckboxes(onlyVisible) {
            const all = Array.from(document.querySelectorAll('input.row-checkbox'));
            if (!onlyVisible) return all;
            return all.filter(cb => isElementVisible(cb));
        }

        function getSelectedIndices() {
            return getRowCheckboxes(true)
                .filter(cb => cb.checked)
                .map(cb => parseInt(cb.getAttribute('data-index'), 10))
                .filter(n => Number.isFinite(n));
        }

        function updateBulkControls() {
            const selected = getSelectedIndices();
            const bulkBtn = document.getElementById('bulk-add-btn');
            const bulkLabel = document.getElementById('bulk-add-label');
            if (bulkBtn) bulkBtn.disabled = selected.length === 0;
            if (bulkLabel) bulkLabel.textContent = selected.length > 0 ? `Add to List (${selected.length})` : 'Add to List';

            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                const cbs = getRowCheckboxes(true);
                const checkedCount = cbs.filter(cb => cb.checked).length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < cbs.length;
                selectAll.checked = cbs.length > 0 && checkedCount === cbs.length;
            }
        }

        function initBulkSelection() {
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const cbs = getRowCheckboxes(true);
                    cbs.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    updateBulkControls();
                });
            }
            document.querySelectorAll('input.row-checkbox').forEach(cb => {
                cb.addEventListener('change', updateBulkControls);
            });
            updateBulkControls();
        }

        function getInfluencerDataByIndex(idx) {
            if (!Array.isArray(DISCOVERY_RESULTS)) return null;
            const item = DISCOVERY_RESULTS[idx];
            if (!item) return null;
            return item;
        }

        function setAddButtonSaved(btn) {
            if (!btn) return;
            btn.innerHTML = '<i class="fas fa-check text-xs"></i> Saved';
            btn.disabled = true;
            btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200', 'hover:border-brand-green', 'hover:text-brand-green', 'shadow-sm');
            btn.classList.add('bg-green-50', 'text-green-600', 'border-green-200', 'cursor-default');
        }

        function ajaxAddInfluencer(data, campaignId = null) {
            const payload = buildAddPayload(data);
            if (campaignId) {
                payload.campaign_id = campaignId;
            }
            return new Promise(resolve => {
                $.ajax({
                    url: '<?php echo base_url("discovery/add"); ?>',
                    type: 'POST',
                    data: payload,
                    dataType: 'json',
                    success: function(response) {
                        resolve(response && response.status === 'success' ? { ok: true, response } : { ok: false, response });
                    },
                    error: function() {
                        resolve({ ok: false, response: null });
                    }
                });
            });
        }

        function parseNumericValue(val) {
            if (val === null || val === undefined) return 0;
            if (typeof val === 'number' && Number.isFinite(val)) return val;
            const s = String(val).trim();
            if (!s) return 0;
            const normalized = s.replace(/,/g, '').toLowerCase();
            const m = normalized.match(/^([0-9]*\.?[0-9]+)\s*([km])?$/i);
            if (!m) {
                const digits = normalized.replace(/[^\d]/g, '');
                return digits ? parseInt(digits, 10) : 0;
            }
            const num = parseFloat(m[1]);
            const suf = m[2];
            if (suf === 'k') return Math.round(num * 1000);
            if (suf === 'm') return Math.round(num * 1000000);
            return Math.round(num);
        }

        function buildAddPayload(data) {
            const d = data || {};
            const platform = (d.platform || '').toString().toLowerCase();
            const username = (d.username || '').toString().trim();
            const fullName = (d.full_name || d.fullName || username).toString();
            const profileImage = (d.profile_image || d.profileImage || '').toString();
            const followers = parseNumericValue(d.followers);
            const avgViews = parseNumericValue(d.avg_video_views || d.avgViews);
            const engRateRaw = (d.eng_rate || '').toString().trim();
            const engRate = engRateRaw ? engRateRaw : '0%';
            const bio = (d.bio || '').toString();
            const notes = '';
            const location = (d.location || '').toString();
            const profileUrl = (d.profile_url || d.profileUrl || '').toString();
            const niche = d.niche || '';

            const email = (d.email || '').toString();

            return {
                platform: platform,
                username: username,
                full_name: fullName,
                profile_image: profileImage,
                followers: followers,
                avg_video_views: avgViews,
                eng_rate: engRate,
                location: location,
                bio: bio,
                notes: notes,
                profile_url: profileUrl,
                niche: niche,
                email: email
            };
        }

        let pendingTargetIndices = [];

        function openCampaignModal(indices) {
            pendingTargetIndices = indices;
            const modal = document.getElementById('campaign-select-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeCampaignModal() {
            const modal = document.getElementById('campaign-select-modal');
            if (modal) modal.classList.add('hidden');
            pendingTargetIndices = [];
            const confirmBtn = document.getElementById('confirm-campaign-btn');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-plus text-[10px]"></i> Add & Go to Campaign';
            }
        }

        function addInfluencerByIndex(idx, btnElement) {
            const data = getInfluencerDataByIndex(idx);
            if (!data) {
                showToast('Error', 'Invalid influencer data.');
                return;
            }
            openCampaignModal([idx]);
        }

        function bulkAddToList() {
            const indices = getSelectedIndices();
            if (indices.length === 0) {
                showToast('Error', 'Select at least one influencer.');
                return;
            }
            openCampaignModal(indices);
        }

        async function confirmAddToCampaign() {
            const selectedRadio = document.querySelector('input[name="modal_campaign_id"]:checked');
            const campaignId = selectedRadio ? parseInt(selectedRadio.value, 10) : 0;

            if (!pendingTargetIndices || pendingTargetIndices.length === 0) {
                closeCampaignModal();
                return;
            }

            const confirmBtn = document.getElementById('confirm-campaign-btn');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Adding...';
            }

            let added = 0;
            for (const idx of pendingTargetIndices) {
                const data = getInfluencerDataByIndex(idx);
                if (!data) continue;
                const res = await ajaxAddInfluencer(data, campaignId);
                if (res.ok) {
                    added++;
                    const addBtn = document.querySelector(`button[data-add-index="${idx}"]`);
                    if (addBtn) setAddButtonSaved(addBtn);
                    const rowCb = document.querySelector(`input.row-checkbox[data-index="${idx}"]`);
                    if (rowCb) rowCb.checked = false;
                }
            }

            closeCampaignModal();
            updateBulkControls();

            if (campaignId > 0) {
                showToast('Success', `${added} influencer(s) added! Opening campaign...`, 'success');
                setTimeout(() => {
                    window.location.href = '<?php echo base_url("email/drips?campaign_id="); ?>' + campaignId;
                }, 500);
            } else {
                showToast('Success', `${added} influencer(s) added to List!`, 'success');
            }
        }

        // Add Influencer AJAX
        function addInfluencer(influencerData, btnElement) {
            const originalText = btnElement.innerHTML;
            const originalClasses = btnElement.className;
            
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Saving...';
            btnElement.disabled = true;

            $.ajax({
                url: '<?php echo base_url("discovery/add"); ?>',
                type: 'POST',
                data: buildAddPayload(influencerData),
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        btnElement.innerHTML = '<i class="fas fa-check text-xs"></i> Saved';
                        // Remove initial styling classes
                        btnElement.classList.remove('bg-white', 'text-gray-600', 'border-gray-200', 'hover:border-brand-green', 'hover:text-brand-green', 'shadow-sm');
                        // Add success styling classes
                        btnElement.classList.add('bg-green-50', 'text-green-600', 'border-green-200', 'cursor-default');
                        showToast('Success', 'Influencer added to List!', 'success');
                    } else {
                        btnElement.innerHTML = originalText;
                        btnElement.disabled = false;
                        showToast('Error', response.message || 'Failed to add influencer.');
                    }
                },
                error: function(xhr) {
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                    const msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr && xhr.responseText ? String(xhr.responseText).slice(0, 180) : 'Server error. Please try again.');
                    showToast('Error', msg);
                }
            });
        }

        function showToast(title, message) {
            if (typeof window.showToast === 'function') {
                const isErr = (title && title.toLowerCase() === 'error');
                window.showToast(isErr ? 'Notice' : (title || 'Success'), message, isErr ? 'error' : 'success');
            }
        }
        
        function updateSearchUI(platform) {
            const icon = document.getElementById('searchPlatformIcon');
            // Reset classes
            icon.className = '';
            
            if (platform === 'youtube') {
                icon.className = 'fab fa-youtube text-red-600 text-lg';
            } else if (platform === 'tiktok') {
                icon.className = 'fab fa-tiktok text-black text-lg';
            } else {
                icon.className = 'fab fa-instagram text-pink-600 text-lg';
            }
        }

        function showPageLoading() {
            const el = document.getElementById('page-loading');
            if (el) el.classList.remove('hidden');
        }

        function cancelSearch() {
            if (window.stop) {
                window.stop();
            } else if (document.execCommand) {
                document.execCommand('Stop');
            }
            const el = document.getElementById('page-loading');
            if (el) el.classList.add('hidden');
        }
    </script>
    <link href="<?php echo base_url('assets/css/discovery.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">
    <?php
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
        $available_brands = $this->Brand_model->get_all_brands();
    ?>

    <!-- Sidebar -->
    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Influencer', 'child' => 'Search Results']); ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F8FAFC] relative">
            
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-green-50/50 to-transparent -z-10"></div>
            
            <div class="max-w-7xl mx-auto px-6 py-8 relative">

                <!-- Header Section -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <a href="<?php echo base_url('discovery'); ?>" class="group inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-xl text-xs font-bold text-gray-500 hover:text-brand-green hover:border-green-200 transition-all shadow-sm">
                                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> 
                                <span>Back to Search</span>
                            </a>
                        </div>
                        <h1 class="text-3xl font-black text-brand-deep tracking-tight">
                            Results for "<span class="text-brand-green"><?php echo isset($search_params['keyword']) ? htmlspecialchars($search_params['keyword']) : 'Search'; ?></span>"
                        </h1>
                        <p class="text-sm text-gray-500 mt-1 font-medium">Found <span class="font-bold text-gray-900"><?php echo number_format(count($results)); ?></span> creators matching your criteria</p>
                    </div>

                    <!-- Compact Modern Search Bar -->
                    <form action="<?php echo base_url('discovery/search'); ?>" method="post" onsubmit="showPageLoading()" class="w-full md:w-auto">
                        <!-- Persistent Filters -->
                        <input type="hidden" name="min_followers" value="<?php echo isset($search_params['min_followers']) ? html_escape($search_params['min_followers']) : ''; ?>">
                        <input type="hidden" name="max_followers" value="<?php echo isset($search_params['max_followers']) ? html_escape($search_params['max_followers']) : ''; ?>">
                        <input type="hidden" name="location" value="<?php echo isset($search_params['location']) ? html_escape($search_params['location']) : ''; ?>">
                        <input type="hidden" name="min_engagement" value="<?php echo isset($search_params['min_engagement']) ? html_escape($search_params['min_engagement']) : ''; ?>">
                        <input type="hidden" name="has_email" value="<?php echo isset($search_params['has_email']) ? html_escape($search_params['has_email']) : ''; ?>">
                        <input type="hidden" name="gender" value="<?php echo isset($search_params['gender']) ? html_escape($search_params['gender']) : ''; ?>">
                        <input type="hidden" name="exclude_saved" value="<?php echo (isset($search_params['exclude_saved']) && ($search_params['exclude_saved'] === 'true' || $search_params['exclude_saved'] === '1')) ? 'true' : 'false'; ?>">

                        <div class="flex items-center bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 group focus-within:ring-2 focus-within:ring-green-100 transition-all">
                            <div class="relative w-[50px] flex items-center justify-center border-r border-gray-50 self-stretch mr-2">
                                <select name="platform" onchange="updateSearchUI(this.value)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <option value="instagram" <?php echo (isset($search_params['platform']) && $search_params['platform'] == 'instagram') ? 'selected' : ''; ?>>Instagram</option>
                                    <option value="youtube" <?php echo (isset($search_params['platform']) && $search_params['platform'] == 'youtube') ? 'selected' : ''; ?>>YouTube</option>
                                    <option value="tiktok" <?php echo (isset($search_params['platform']) && $search_params['platform'] == 'tiktok') ? 'selected' : ''; ?>>TikTok</option>
                                </select>
                                <i id="searchPlatformIcon" class="<?php 
                                    $p = isset($search_params['platform']) ? $search_params['platform'] : 'instagram';
                                    if($p == 'youtube') echo 'fab fa-youtube text-red-500';
                                    elseif($p == 'tiktok') echo 'fab fa-tiktok text-gray-900';
                                    else echo 'fab fa-instagram text-pink-500';
                                ?> text-lg"></i>
                            </div>
                            <input type="text" name="keyword" value="<?php echo isset($search_params['keyword']) ? htmlspecialchars($search_params['keyword']) : ''; ?>" 
                                class="w-full md:w-64 pl-2 pr-4 py-2 text-sm font-bold text-gray-700 focus:outline-none bg-transparent placeholder-gray-400" 
                                placeholder="Search niche, #hashtag, @user...">
                            <button type="submit" class="w-10 h-10 bg-brand-green text-white rounded-xl hover:bg-brand-deep transition-all shadow-lg shadow-green-100 flex items-center justify-center">
                                <i class="fas fa-search text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php
                    $active_filter_badges = [];
                    if (!empty($search_params['min_followers']) || !empty($search_params['max_followers'])) {
                        $min_f = !empty($search_params['min_followers']) ? number_format((int)$search_params['min_followers']) : '0';
                        $max_f = !empty($search_params['max_followers']) ? number_format((int)$search_params['max_followers']) : 'Any';
                        $active_filter_badges[] = ['label' => "Followers: {$min_f} - {$max_f}", 'clear' => 'followers'];
                    }
                    if (!empty($search_params['location'])) {
                        $active_filter_badges[] = ['label' => 'Location: ' . $search_params['location'], 'clear' => 'location'];
                    }
                    if (!empty($search_params['min_engagement']) && (float)$search_params['min_engagement'] > 0) {
                        $active_filter_badges[] = ['label' => 'Engagement: ' . $search_params['min_engagement'] . '%+', 'clear' => 'engagement'];
                    }
                    if (isset($search_params['has_email']) && $search_params['has_email'] !== '') {
                        $active_filter_badges[] = ['label' => 'Email: ' . ($search_params['has_email'] === '1' ? 'Exists' : "Doesn't Exist"), 'clear' => 'email'];
                    }
                    if (!empty($search_params['gender']) && $search_params['gender'] !== 'any') {
                        $active_filter_badges[] = ['label' => 'Gender: ' . ucfirst($search_params['gender']), 'clear' => 'gender'];
                    }
                ?>
                <?php if (!empty($active_filter_badges)): ?>
                <div class="mb-6 flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400 mr-1"><i class="fas fa-sliders-h text-brand-green mr-1"></i> Active Filters:</span>
                    <?php foreach ($active_filter_badges as $badge): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-brand-deep border border-green-200">
                            <?php echo html_escape($badge['label']); ?>
                            <button type="button" onclick="clearSingleFilter('<?php echo $badge['clear']; ?>')" class="text-brand-deep/60 hover:text-red-500 transition-colors" title="Remove filter">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </span>
                    <?php endforeach; ?>
                    <button type="button" onclick="clearAllFiltersAndSubmit()" class="text-xs font-bold text-red-500 hover:text-red-700 ml-2 underline">
                        Reset All
                    </button>
                </div>
                <?php endif; ?>
                
                <?php if(isset($warning) && $warning): ?>
                <div class="mb-8 rounded-2xl border border-amber-100 bg-amber-50 text-amber-800 px-6 py-4 flex items-center gap-4 animate-fade-in shadow-sm">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-amber-500 shadow-sm shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <span class="text-sm font-medium"><?php echo $warning; ?></span>
                </div>
                <?php endif; ?>

                <?php if(isset($error) && $error): ?>
                <div class="mb-8 rounded-2xl border border-red-100 bg-red-50 text-red-800 px-6 py-4 flex items-center gap-4 animate-fade-in shadow-sm">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-red-500 shadow-sm shrink-0">
                        <i class="fas fa-circle-exclamation"></i>
                    </div>
                    <span class="text-sm font-medium"><?php echo $error; ?></span>
                </div>
                <?php endif; ?>

                <?php if(!empty($results)): ?>
                    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Bulk Actions</span>
                                <div class="h-4 w-px bg-gray-100"></div>
                                <button id="bulk-add-btn" type="button" onclick="bulkAddToList()" class="flex items-center gap-2 text-xs font-black text-brand-green hover:text-brand-deep disabled:opacity-30 disabled:cursor-not-allowed transition-colors" disabled>
                                    <i class="fas fa-plus-circle"></i>
                                    <span id="bulk-add-label">Add Selected</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-500">Exclude Saved</span>
                                <button type="button" onclick="toggleExcludeSaved(this)" id="exclude_saved_btn" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none <?php echo (isset($search_params['exclude_saved']) && ($search_params['exclude_saved'] === 'true' || $search_params['exclude_saved'] === '1')) ? 'bg-brand-green' : 'bg-gray-200'; ?>">
                                    <span class="<?php echo (isset($search_params['exclude_saved']) && ($search_params['exclude_saved'] === 'true' || $search_params['exclude_saved'] === '1')) ? 'translate-x-5' : 'translate-x-0'; ?> pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                            </div>
                            
                            <button onclick="toggleFilterModal()" class="px-5 py-2.5 bg-white border border-gray-100 rounded-xl text-xs font-black text-gray-700 hover:text-brand-green hover:border-green-200 transition-all shadow-sm flex items-center gap-2">
                                <i class="fas fa-filter text-brand-green opacity-70"></i> Refine Filters
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-12">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        <th class="p-6 w-10 text-center">
                                            <div class="flex items-center justify-center">
                                                <input id="select-all" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brand-green focus:ring-brand-green cursor-pointer transition-all">
                                            </div>
                                        </th>
                                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Creator</th>
                                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Username</th>
                                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Followers</th>
                                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Engagement</th>
                                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Est. Fair Fee</th>
                                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php foreach($results as $index => $row): ?>
                                        <tr class="group hover:bg-gray-50/50 transition-all duration-300">
                                            <td class="p-6 text-center">
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-brand-green focus:ring-brand-green cursor-pointer transition-all" data-index="<?php echo (int)$index; ?>">
                                                </div>
                                            </td>
                                            <td class="px-6 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div class="relative w-12 h-12 flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                                                        <?php
                                                            $__row_avatar = !empty($row['avatar_inline'])
                                                                ? $row['avatar_inline']
                                                                : (!empty($row['profile_image']) ? html_escape($row['profile_image']) : ('https://ui-avatars.com/api/?name=' . urlencode((string)($row['full_name'] ?: $row['username'] ?: 'User')) . '&background=0F6B3E&color=fff&rounded=true'));
                                                        ?>
                                                        <img src="<?php echo $__row_avatar; ?>" alt="" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?php echo urlencode((string)($row['full_name'] ?: $row['username'] ?: 'User')); ?>&background=0F6B3E&color=fff&rounded=true';" class="w-full h-full rounded-2xl object-cover border-2 border-white shadow-sm">
                                                        <?php if($row['platform'] == 'instagram'): ?>
                                                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-lg flex items-center justify-center shadow-md border border-gray-50">
                                                                <i class="fab fa-instagram text-pink-500 text-[10px]"></i>
                                                            </div>
                                                        <?php elseif($row['platform'] == 'tiktok'): ?>
                                                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-lg flex items-center justify-center shadow-md border border-gray-50">
                                                                <i class="fab fa-tiktok text-gray-900 text-[10px]"></i>
                                                            </div>
                                                        <?php elseif($row['platform'] == 'youtube'): ?>
                                                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-lg flex items-center justify-center shadow-md border border-gray-50">
                                                                <i class="fab fa-youtube text-red-600 text-[10px]"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-black text-gray-900 truncate max-w-[200px]" title="<?php echo html_escape($row['full_name'] ?: $row['username']); ?>">
                                                                <?php echo html_escape($row['full_name'] ?: $row['username']); ?>
                                                            </p>
                                                            <?php if(isset($row['is_verified']) && $row['is_verified']): ?>
                                                                <i class="fas fa-check-circle text-blue-500 text-[10px]" title="Verified"></i>
                                                            <?php endif; ?>
                                                            <?php if(isset($row['is_private']) && $row['is_private']): ?>
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-100" title="Private Account">
                                                                    <i class="fas fa-lock text-[8px]"></i> Private
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                                            <?php if(isset($row['is_local']) && $row['is_local']): ?>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-black uppercase bg-green-50 text-brand-green border border-green-100">
                                                                    <i class="fas fa-database mr-1"></i> In Database
                                                                </span>
                                                            <?php endif; ?>
                                                            <?php if(!empty($row['location'])): ?>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-600">
                                                                    <i class="fas fa-map-marker-alt text-brand-green mr-1 text-[9px]"></i> <?php echo html_escape($row['location']); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                            <?php if(!empty($row['niche'])): ?>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                                    #<?php echo html_escape(explode(',', $row['niche'])[0]); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-6 text-sm font-bold text-gray-500">
                                                 <div>@<?php echo html_escape($row['username']); ?></div>
                                                 <?php if(!empty($row['email'])): ?>
                                                     <div class="mt-1">
                                                         <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                                                             <i class="fas fa-envelope text-[10px] text-blue-500"></i>
                                                             <?php echo html_escape($row['email']); ?>
                                                         </span>
                                                     </div>
                                                 <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-6 text-center">
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-base font-black text-gray-900">
                                                        <?php 
                                                            if (isset($row['is_private']) && $row['is_private']) {
                                                                echo 'Private';
                                                            } else {
                                                                $followers = (int)$row['followers'];
                                                                if($followers >= 1000000) echo round($followers/1000000, 1).'M';
                                                                elseif($followers >= 1000) echo round($followers/1000, 1).'k';
                                                                else echo number_format($followers);
                                                            }
                                                        ?>
                                                    </span>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Followers</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-6 text-center">
                                                <div class="inline-flex flex-col items-center">
                                                    <?php if(isset($row['eng_rate'])): ?>
                                                        <span class="text-sm font-black text-brand-deep bg-green-50 px-2 py-1 rounded-lg border border-green-100">
                                                            <?php echo $row['eng_rate']; ?>
                                                        </span>
                                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Eng. Rate</span>
                                                    <?php else: ?>
                                                        <span class="text-gray-300 text-xs">N/A</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-6 text-center">
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-sm font-black text-gray-900">
                                                        <?php 
                                                            $followers_count = isset($row['followers']) ? (int)$row['followers'] : 0;
                                                            $eng_rate_val = floatval(str_replace('%', '', isset($row['eng_rate']) ? $row['eng_rate'] : '0'));
                                                            $min_cpm = 5; $max_cpm = 15;
                                                            if ($eng_rate_val >= 5.0) { $min_cpm += 5; $max_cpm += 5; } 
                                                            elseif ($eng_rate_val >= 2.0) { $min_cpm += 2; $max_cpm += 2; }
                                                            $low_est = max(10, floor(($followers_count / 1000) * $min_cpm));
                                                            $high_est = max(25, ceil(($followers_count / 1000) * $max_cpm));
                                                            echo '$' . ($low_est > 1000 ? number_format($low_est) : $low_est) . ' - $' . ($high_est > 1000 ? number_format($high_est) : $high_est);
                                                        ?>
                                                    </span>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mt-1 group relative cursor-help">
                                                        Est. Fair Fee <i class="fas fa-info-circle ml-0.5 opacity-50"></i>
                                                        <div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-40 bg-gray-900 text-white text-[10px] p-2 rounded-xl shadow-xl z-50 normal-case tracking-normal">
                                                            Based on $5-$15 CPM + Engagement Bonus
                                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                        </div>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button onclick="openPeekByIndex(<?php echo (int)$index; ?>)" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-brand-green hover:border-green-100 transition-all shadow-sm" title="Peek Profile">
                                                        <i class="far fa-eye text-xs"></i>
                                                    </button>
                                                    <button data-add-index="<?php echo (int)$index; ?>" onclick="addInfluencerByIndex(<?php echo (int)$index; ?>, this)" class="h-10 px-4 bg-white border border-gray-100 rounded-xl text-xs font-black text-gray-600 hover:text-brand-green hover:border-green-200 transition-all shadow-sm flex items-center gap-2">
                                                        <i class="fas fa-plus text-[10px]"></i> Add
                                                    </button>
                                                    <div class="w-px h-6 bg-gray-100 mx-1"></div>
                                                    <a href="<?php echo isset($row['profile_url']) && $row['profile_url'] ? $row['profile_url'] : ('https://instagram.com/' . $row['username']); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-500 hover:border-blue-100 transition-all shadow-sm" title="View Profile">
                                                        <i class="fas fa-external-link-alt text-xs"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Footer -->
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100 flex items-center justify-between">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Total Creators: <?php echo number_format(count($results)); ?>
                            </div>
                            <div class="flex gap-2">
                                <button class="px-4 py-2 bg-white border border-gray-100 rounded-xl text-xs font-black text-gray-300 cursor-not-allowed shadow-sm">Previous</button>
                                <button class="px-4 py-2 bg-white border border-gray-100 rounded-xl text-xs font-black text-gray-700 hover:text-brand-green hover:border-green-200 transition-all shadow-sm">Next Page</button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="py-24 text-center bg-white rounded-[3rem] border border-gray-100 shadow-sm overflow-hidden relative">
                        <div class="absolute inset-0 bg-gradient-to-b from-gray-50/50 to-transparent -z-10"></div>
                        <div class="relative inline-block mb-8">
                            <div class="w-32 h-32 bg-gray-50 rounded-[3rem] flex items-center justify-center mx-auto border-2 border-dashed border-gray-200">
                                <i class="fas fa-search-minus text-gray-200 text-5xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-10 h-10 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 shadow-sm animate-bounce">
                                <i class="fas fa-question text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2">No creators found</h3>
                        <p class="text-gray-500 font-medium max-w-md mx-auto leading-relaxed">We couldn't find any influencers matching "<?php echo isset($search_params['keyword']) ? htmlspecialchars($search_params['keyword']) : ''; ?>". Try a different keyword or check your filters.</p>
                        <div class="mt-8 flex items-center justify-center gap-3">
                            <button type="button" onclick="toggleFilterModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-green text-white rounded-2xl text-sm font-black hover:bg-brand-deep transition-all shadow-md">
                                <i class="fas fa-sliders-h text-xs"></i>
                                Adjust Filters
                            </button>
                            <a href="<?php echo base_url('discovery'); ?>" class="inline-flex items-center gap-2 px-8 py-3 bg-white border-2 border-gray-100 rounded-2xl text-sm font-black text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                                <i class="fas fa-arrow-left text-xs"></i>
                                Try Again
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-5 right-5 bg-white border-l-4 border-brand-green shadow-lg rounded-xl p-4 hidden transform transition-all duration-300 translate-y-full z-50 flex items-center gap-4 pr-6">
        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check text-brand-green"></i>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 text-sm">Success</h4>
            <p class="text-sm text-gray-500">Action completed successfully.</p>
        </div>
    </div>
    <div id="page-loading" class="fixed inset-0 bg-white/70 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="flex flex-col items-center gap-3 bg-white rounded-2xl shadow-xl border border-gray-100 p-6 max-w-xs text-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-notch fa-spin text-brand-green text-xl"></i>
                <span class="text-gray-800 font-bold text-sm">Searching live results...</span>
            </div>
            <button type="button" onclick="cancelSearch()" class="mt-1 px-4 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all border border-red-100 flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-times text-[10px]"></i> Cancel Search
            </button>
        </div>
    </div>
    <!-- Peek Drawer -->
    <div id="peek-overlay" class="fixed inset-0 bg-black/50 hidden z-50 transition-opacity duration-300" onclick="closePeek()"></div>
    <div id="peek-drawer" class="fixed top-0 right-0 h-full w-full md:w-[600px] bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 overflow-y-auto">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <img id="peek-avatar" src="" alt="Profile" referrerpolicy="no-referrer" class="w-16 h-16 rounded-full object-cover border-2 border-gray-100 p-0.5" onerror="this.src='https://ui-avatars.com/api/?name=User&background=random&color=fff'">
                        <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-sm">
                            <i id="peek-platform-icon" class="fab fa-instagram text-pink-500"></i>
                        </div>
                    </div>
                    <div>
                        <h2 id="peek-fullname" class="text-xl font-bold text-gray-900"></h2>
                        <p id="peek-username" class="text-gray-500 text-sm">@username</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="peek-add-btn" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add
                    </button>
                    <button onclick="closePeek()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="flex items-center gap-8 mb-8 border-b border-gray-100 pb-8">
                <div>
                    <p id="peek-posts-count" class="text-xl font-bold text-gray-900">0</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Post</p>
                </div>
                <div>
                    <p id="peek-followers" class="text-xl font-bold text-gray-900">0</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Followers</p>
                </div>
                <div>
                    <p id="peek-following" class="text-xl font-bold text-gray-900">0</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Following</p>
                </div>
            </div>

            <!-- Bio -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-gray-900 mb-2">Bio:</h3>
                <p id="peek-bio" class="text-gray-600 text-sm leading-relaxed"></p>
                <a id="peek-website" href="#" target="_blank" class="text-blue-500 text-sm mt-2 block hover:underline truncate"></a>
                
                <div class="mt-3.5 hidden" id="peek-email-container">
                    <span class="text-xs font-bold text-gray-500 block mb-1">Business Email:</span>
                    <a id="peek-email" href="#" class="inline-flex items-center gap-1.5 text-sm font-black text-blue-700 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 px-3.5 py-2 rounded-xl border border-blue-100 transition-colors">
                        <i class="fas fa-envelope text-blue-500"></i>
                        <span></span>
                    </a>
                </div>
            </div>

            <!-- Recent Media Grid -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Posts</h3>
                <div id="peek-media-grid" class="grid grid-cols-2 gap-4">
                    <!-- Media Items injected via JS -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPeekByIndex(idx) {
            const data = getInfluencerDataByIndex(idx);
            if (!data) {
                showToast('Error', 'Invalid influencer data.');
                return;
            }
            openPeek(data);
        }

        function openPeek(data) {
            const safe = data || {};
            if (typeof safe.recent_media === 'string') {
                try { safe.recent_media = JSON.parse(safe.recent_media); } catch(e) { safe.recent_media = []; }
            }
            const username = (safe.username || '').toString();
            const fullName = (safe.full_name || username || 'Unknown').toString();
            const avatar = (safe.avatar_inline || safe.profile_image || '').toString();

            const wantsLive = (safe.platform === 'instagram' || safe.platform === 'tiktok')
                && username
                && !safe.live_loaded
                && (!safe.recent_media || safe.recent_media.length === 0 || !safe.bio);
            if (wantsLive) {
                const grid = document.getElementById('peek-media-grid');
                if (grid) {
                    grid.innerHTML = `
                        <div class="col-span-2 py-12 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-sm">Loading live profile…</p>
                        </div>
                    `;
                }

                fetch('<?php echo base_url("discovery/profile"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ platform: safe.platform, username: username })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(resp => {
                    if (resp && resp.status === 'success' && resp.data) {
                        const merged = Object.assign({}, safe, resp.data, { is_local: safe.is_local, live_loaded: true });
                        openPeek(merged);
                    } else {
                        showToast('Error', (resp && resp.message) ? resp.message : 'Failed to load live profile.');
                    }
                })
                .catch(error => {
                    showToast('Error', 'Server error. Please try again.');
                });
            }

            // Populate Data
            document.getElementById('peek-fullname').textContent = fullName;
            document.getElementById('peek-username').textContent = username ? ('@' + username) : '@-';
            document.getElementById('peek-avatar').src = avatar ? avatar : ('https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&background=random&color=fff');
            
            // Platform Icon
            const icon = document.getElementById('peek-platform-icon');
            icon.className = ''; // Reset
            if(safe.platform === 'youtube') icon.className = 'fab fa-youtube text-red-600';
            else if(safe.platform === 'tiktok') icon.className = 'fab fa-tiktok text-black';
            else icon.className = 'fab fa-instagram text-pink-500';

            // Stats
            document.getElementById('peek-followers').textContent = formatNumber(safe.followers || 0);
            document.getElementById('peek-following').textContent = formatNumber(safe.following || 0);
            document.getElementById('peek-posts-count').textContent = formatNumber(safe.posts_count || 0);

            // Bio
            document.getElementById('peek-bio').textContent = safe.bio || 'No bio available';
            
            // Website
            const websiteEl = document.getElementById('peek-website');
            if(safe.website) {
                websiteEl.href = safe.website;
                websiteEl.textContent = safe.website;
                websiteEl.classList.remove('hidden');
            } else {
                websiteEl.classList.add('hidden');
            }

            // Email
            const emailContainer = document.getElementById('peek-email-container');
            const emailEl = document.getElementById('peek-email');
            if(emailContainer && emailEl) {
                if(safe.email) {
                    emailEl.href = 'mailto:' + safe.email;
                    emailEl.querySelector('span').textContent = safe.email;
                    emailContainer.classList.remove('hidden');
                } else {
                    emailContainer.classList.add('hidden');
                }
            }

            // Media Grid
            const grid = document.getElementById('peek-media-grid');
            grid.innerHTML = ''; // Clear

            if(safe.recent_media && safe.recent_media.length > 0) {
                const fallbackAvatar = avatar ? avatar : ('https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&background=0F6B3E&color=fff');
                safe.recent_media.forEach(media => {
                    const div = document.createElement('div');
                    div.className = 'relative group rounded-xl overflow-hidden aspect-square bg-gray-100 border border-gray-100' + (media.permalink ? ' cursor-pointer' : '');
                    if (media.permalink) {
                        div.onclick = () => window.open(media.permalink, '_blank', 'noopener');
                    }

                    let mediaSrc = (media.media_url || '').toString().trim();
                    if (mediaSrc.indexOf('/discovery/image_proxy?url=') === 0) {
                        mediaSrc = '/home/image_proxy?url=' + mediaSrc.substring('/discovery/image_proxy?url='.length);
                    } else if (/^https?:\/\/[^/]*(cdninstagram\.com|fbcdn\.net|tiktokcdn\.com)/i.test(mediaSrc)) {
                        mediaSrc = '/home/image_proxy?url=' + encodeURIComponent(mediaSrc);
                    }
                    if (!mediaSrc) {
                        mediaSrc = fallbackAvatar;
                    }
                    
                    const isVideo = media.media_type === 'VIDEO';
                    const iconOverlay = isVideo ? '<div class="absolute top-2 right-2 text-white drop-shadow-md"><i class="fas fa-video"></i></div>' : '';
                    const playOverlay = isVideo ? '<div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-colors"><i class="fas fa-play text-white text-2xl drop-shadow-lg"></i></div>' : '';

                    div.innerHTML = `
                        <img src="${mediaSrc}" referrerpolicy="no-referrer" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.onerror=null;this.src='${fallbackAvatar.replace(/'/g, "\\'")}'">
                        ${iconOverlay}
                        ${playOverlay}
                        <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="flex items-center gap-3 text-white text-xs font-medium">
                                <span><i class="fas fa-heart mr-1"></i> ${formatNumber(media.likes || 0)}</span>
                                <span><i class="fas fa-comment mr-1"></i> ${formatNumber(media.comments || 0)}</span>
                            </div>
                        </div>
                        <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                             <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm">Eng. Rate ${safe.eng_rate || 'N/A'}</span>
                        </div>
                    `;
                    grid.appendChild(div);
                });
            } else {
                grid.innerHTML = `
                    <div class="col-span-2 py-12 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <i class="fas fa-camera text-2xl mb-2"></i>
                        <p class="text-sm">No recent posts found</p>
                    </div>
                `;
            }

            // Update Add Button context
            const addBtn = document.getElementById('peek-add-btn');
            // Remove old listeners to prevent duplicates (cloning is a quick hack)
            const newBtn = addBtn.cloneNode(true);
            addBtn.parentNode.replaceChild(newBtn, addBtn);
            newBtn.onclick = function() {
                addInfluencer(safe, this);
            };

            // Show Drawer
            document.getElementById('peek-overlay').classList.remove('hidden');
            // Small delay to allow display:block to apply before transform
            setTimeout(() => {
                document.getElementById('peek-drawer').classList.remove('translate-x-full');
            }, 10);
            document.body.classList.add('overflow-hidden');
        }

        function closePeek() {
            document.getElementById('peek-drawer').classList.add('translate-x-full');
            setTimeout(() => {
                document.getElementById('peek-overlay').classList.add('hidden');
            }, 300);
            document.body.classList.remove('overflow-hidden');
        }

        function formatNumber(num) {
            if(num >= 1000000) return (num/1000000).toFixed(1) + 'M';
            if(num >= 1000) return (num/1000).toFixed(1) + 'k';
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('user-menu-container');
            const menu = document.getElementById('user-menu');
            if (!container.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        function showPageLoading() {
            document.getElementById('page-loading').classList.remove('hidden');
        }

        function showToast(title, message) {
            const toast = document.getElementById('toast');
            toast.querySelector('h4').textContent = title;
            toast.querySelector('p').textContent = message;
            
            toast.classList.remove('hidden', 'translate-y-full');
            
            setTimeout(() => {
                toast.classList.add('translate-y-full');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 300);
            }, 3000);
        }

        function addInfluencer(influencerData, btnElement) {
            const originalText = btnElement.innerHTML;
            const originalClasses = btnElement.className;
            
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Saving...';
            btnElement.disabled = true;

            $.ajax({
                url: '<?php echo base_url("discovery/add"); ?>',
                type: 'POST',
                data: buildAddPayload(influencerData),
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        btnElement.innerHTML = '<i class="fas fa-check text-xs"></i> Saved';
                        // Remove initial styling classes
                        btnElement.classList.remove('bg-white', 'text-gray-600', 'border-gray-200', 'hover:border-brand-green', 'hover:text-brand-green', 'shadow-sm');
                        // Add success styling classes
                        btnElement.classList.add('bg-green-50', 'text-green-600', 'border-green-200', 'cursor-default');
                        showToast('Success', 'Influencer added to your list!');
                    } else {
                        btnElement.innerHTML = originalText;
                        btnElement.disabled = false;
                        showToast('Error', response.message || 'Failed to add influencer.');
                    }
                },
                error: function(xhr) {
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                    const msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr && xhr.responseText ? String(xhr.responseText).slice(0, 180) : 'Server error. Please try again.');
                    showToast('Error', msg);
                }
            });
        }
    </script>
    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate-fade-in-up">
            
            <!-- Header -->
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-brand-green">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Filters</h3>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="resetFilters()" class="text-sm text-gray-500 hover:text-red-500 font-medium transition-colors">
                        Reset all
                    </button>
                    <button type="button" onclick="toggleFilterModal()" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-hidden flex flex-col">

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6 md:p-8">
                    <div class="grid grid-cols-1 gap-8">
                        
                        <!-- Followers -->
                        <div class="space-y-4">
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900">Followers</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Audience Size</span>
                            </label>
                            
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-4">
                                <button type="button" data-follower-tier="1000-10000" onclick="setFollowers(1000, 10000)" class="follower-tier-btn px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all text-center">
                                    Nano<br><span class="text-[10px] text-gray-400">1k-10k</span>
                                </button>
                                <button type="button" data-follower-tier="10000-50000" onclick="setFollowers(10000, 50000)" class="follower-tier-btn px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all text-center">
                                    Micro<br><span class="text-[10px] text-gray-400">10k-50k</span>
                                </button>
                                <button type="button" data-follower-tier="50000-500000" onclick="setFollowers(50000, 500000)" class="follower-tier-btn px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all text-center">
                                    Macro<br><span class="text-[10px] text-gray-400">50k-500k</span>
                                </button>
                                <button type="button" data-follower-tier="500000-1000000" onclick="setFollowers(500000, 1000000)" class="follower-tier-btn px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all text-center">
                                    Mega<br><span class="text-[10px] text-gray-400">500k-1M</span>
                                </button>
                                <button type="button" data-follower-tier="1000000-" onclick="setFollowers(1000000, '')" class="follower-tier-btn px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all text-center">
                                    Whale<br><span class="text-[10px] text-gray-400">1M+</span>
                                </button>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">Min</span>
                                    <input type="number" id="min_followers_input" oninput="highlightFollowerTier()" value="<?php echo html_escape($search_params['min_followers'] ?? ''); ?>" placeholder="0" class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green">
                                </div>
                                <span class="text-gray-300">-</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">Max</span>
                                    <input type="number" id="max_followers_input" oninput="highlightFollowerTier()" value="<?php echo html_escape($search_params['max_followers'] ?? ''); ?>" placeholder="Any" class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green">
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-900">Location</label>
                            <div class="relative">
                                <i class="fas fa-map-marker-alt absolute left-3.5 top-3 text-gray-400"></i>
                                <input type="text" id="location_input" oninput="highlightLocationPill()" value="<?php echo html_escape($search_params['location'] ?? ''); ?>" placeholder="Search locations (e.g., United States, London, Philippines)" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" data-location-pill="United States" onclick="setLocation('United States')" class="location-pill-btn px-3 py-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full text-xs text-gray-600 transition-colors">United States</button>
                                <button type="button" data-location-pill="United Kingdom" onclick="setLocation('United Kingdom')" class="location-pill-btn px-3 py-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full text-xs text-gray-600 transition-colors">United Kingdom</button>
                                <button type="button" data-location-pill="Canada" onclick="setLocation('Canada')" class="location-pill-btn px-3 py-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full text-xs text-gray-600 transition-colors">Canada</button>
                                <button type="button" data-location-pill="Australia" onclick="setLocation('Australia')" class="location-pill-btn px-3 py-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full text-xs text-gray-600 transition-colors">Australia</button>
                                <button type="button" data-location-pill="Philippines" onclick="setLocation('Philippines')" class="location-pill-btn px-3 py-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full text-xs text-gray-600 transition-colors">Philippines</button>
                            </div>
                        </div>

                        <!-- Engagement Rate -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-bold text-gray-900">Engagement Rate</label>
                                <span class="text-sm text-brand-green font-bold" id="engagement_display">Any</span>
                            </div>
                            <input type="range" id="engagement_slider" min="0" max="10" step="0.5" value="<?php echo html_escape($search_params['min_engagement'] ?? '0'); ?>" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand-green">
                            <div class="flex justify-between text-xs text-gray-400">
                                <span>0%</span>
                                <span>5%</span>
                                <span>10%+</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Has Email -->
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-900">Has Email</label>
                                <div class="flex p-1 bg-gray-100 rounded-lg">
                                    <button type="button" onclick="setHasEmail('')" id="email_any" class="flex-1 py-2 text-xs font-medium rounded-md shadow-sm bg-white text-gray-900 transition-all">Any</button>
                                    <button type="button" onclick="setHasEmail('1')" id="email_yes" class="flex-1 py-2 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 transition-all">Exists</button>
                                    <button type="button" onclick="setHasEmail('0')" id="email_no" class="flex-1 py-2 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 transition-all">Doesn't Exist</button>
                                </div>
                                <input type="hidden" id="has_email_input" value="<?php echo html_escape($search_params['has_email'] ?? ''); ?>">
                            </div>

                            <!-- Influencer Gender -->
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-900">Influencer Gender</label>
                                <div class="flex p-1 bg-gray-100 rounded-lg">
                                    <button type="button" onclick="setGender('any')" id="gender_any" class="flex-1 py-2 text-xs font-medium rounded-md shadow-sm bg-white text-gray-900 transition-all">Any</button>
                                    <button type="button" onclick="setGender('male')" id="gender_male" class="flex-1 py-2 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 transition-all">Male</button>
                                    <button type="button" onclick="setGender('female')" id="gender_female" class="flex-1 py-2 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 transition-all">Female</button>
                                </div>
                                <input type="hidden" id="gender_input" value="<?php echo html_escape($search_params['gender'] ?? 'any'); ?>">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button type="button" onclick="applyPreviousFilters()" class="text-sm font-medium text-gray-600 hover:text-brand-green transition-colors">
                    Apply Previous
                </button>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleFilterModal()" class="px-5 py-2.5 rounded-lg border border-gray-200 text-gray-600 font-medium text-sm hover:bg-white hover:border-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="applyFilters()" class="px-6 py-2.5 rounded-lg bg-brand-green text-white font-bold text-sm hover:bg-brand-deep shadow-md hover:shadow-lg transition-all">
                        Show Results
                    </button>
                </div>
            </div>

        </div>
    </div>
    
    <script>
        // Filter Logic
        function toggleFilterModal() {
            const modal = document.getElementById('filterModal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
                highlightFollowerTier();
                highlightLocationPill();
            } else {
                document.body.style.overflow = '';
            }
        }

        function setFollowers(min, max) {
            const minInput = document.getElementById('min_followers_input');
            const maxInput = document.getElementById('max_followers_input');
            if (String(minInput.value) === String(min) && String(maxInput.value) === String(max)) {
                minInput.value = '';
                maxInput.value = '';
            } else {
                minInput.value = min;
                maxInput.value = max;
            }
            highlightFollowerTier();
        }

        function highlightFollowerTier() {
            const minVal = String(document.getElementById('min_followers_input').value || '');
            const maxVal = String(document.getElementById('max_followers_input').value || '');
            const currentKey = `${minVal}-${maxVal}`;
            document.querySelectorAll('.follower-tier-btn').forEach(btn => {
                const key = btn.getAttribute('data-follower-tier');
                if (key === currentKey && minVal !== '') {
                    btn.classList.add('border-brand-green', 'bg-green-50', 'text-brand-green', 'font-bold');
                    btn.classList.remove('border-gray-200', 'text-gray-600');
                } else {
                    btn.classList.remove('border-brand-green', 'bg-green-50', 'text-brand-green', 'font-bold');
                    btn.classList.add('border-gray-200', 'text-gray-600');
                }
            });
        }

        function setLocation(loc) {
            const locInput = document.getElementById('location_input');
            if (locInput.value.trim().toLowerCase() === loc.toLowerCase()) {
                locInput.value = '';
            } else {
                locInput.value = loc;
            }
            highlightLocationPill();
        }

        function highlightLocationPill() {
            const currentLoc = (document.getElementById('location_input').value || '').trim().toLowerCase();
            document.querySelectorAll('.location-pill-btn').forEach(btn => {
                const pillLoc = (btn.getAttribute('data-location-pill') || '').toLowerCase();
                if (currentLoc !== '' && currentLoc === pillLoc) {
                    btn.classList.add('border-brand-green', 'bg-green-50', 'text-brand-green', 'font-bold');
                    btn.classList.remove('bg-gray-50', 'border-gray-200', 'text-gray-600');
                } else {
                    btn.classList.remove('border-brand-green', 'bg-green-50', 'text-brand-green', 'font-bold');
                    btn.classList.add('bg-gray-50', 'border-gray-200', 'text-gray-600');
                }
            });
        }

        function setHasEmail(val) {
            document.getElementById('has_email_input').value = val;
            
            ['email_any', 'email_yes', 'email_no'].forEach(id => {
                const btn = document.getElementById(id);
                if(btn) {
                    btn.classList.remove('shadow-sm', 'bg-white', 'text-gray-900');
                    btn.classList.add('text-gray-500', 'hover:text-gray-900');
                }
            });

            let activeId = 'email_any';
            if (val === '1') activeId = 'email_yes';
            if (val === '0') activeId = 'email_no';

            const activeBtn = document.getElementById(activeId);
            if(activeBtn) {
                activeBtn.classList.add('shadow-sm', 'bg-white', 'text-gray-900');
                activeBtn.classList.remove('text-gray-500');
            }
        }

        function setGender(val) {
            document.getElementById('gender_input').value = val;
            
            ['gender_any', 'gender_male', 'gender_female'].forEach(id => {
                const btn = document.getElementById(id);
                if(btn) {
                    btn.classList.remove('shadow-sm', 'bg-white', 'text-gray-900');
                    btn.classList.add('text-gray-500', 'hover:text-gray-900');
                }
            });

            let activeId = 'gender_any';
            if (val === 'male') activeId = 'gender_male';
            if (val === 'female') activeId = 'gender_female';

            const activeBtn = document.getElementById(activeId);
            if(activeBtn) {
                activeBtn.classList.add('shadow-sm', 'bg-white', 'text-gray-900');
                activeBtn.classList.remove('text-gray-500');
            }
        }

        const engagementSlider = document.getElementById('engagement_slider');
        const engagementDisplay = document.getElementById('engagement_display');
        
        if (engagementSlider) {
            engagementSlider.addEventListener('input', function() {
                if (this.value == 0) {
                    engagementDisplay.textContent = 'Any';
                } else {
                    engagementDisplay.textContent = this.value + '% +';
                }
            });
        }

        function resetFilters() {
            document.getElementById('min_followers_input').value = '';
            document.getElementById('max_followers_input').value = '';
            document.getElementById('location_input').value = '';
            if(engagementSlider) {
                engagementSlider.value = 0;
                engagementDisplay.textContent = 'Any';
            }
            setHasEmail('');
            setGender('any');
            highlightFollowerTier();
            highlightLocationPill();
        }

        function applyPreviousFilters() {
            try {
                const raw = localStorage.getItem('instroom_discovery_prev_filters');
                if (!raw) {
                    showToast('Notice', 'No previous filters saved yet.');
                    return;
                }
                const prev = JSON.parse(raw);
                document.getElementById('min_followers_input').value = prev.min_followers || '';
                document.getElementById('max_followers_input').value = prev.max_followers || '';
                document.getElementById('location_input').value = prev.location || '';
                if (engagementSlider) {
                    engagementSlider.value = prev.min_engagement || 0;
                    engagementDisplay.textContent = (prev.min_engagement && parseFloat(prev.min_engagement) > 0) ? (prev.min_engagement + '% +') : 'Any';
                }
                setHasEmail(prev.has_email !== undefined ? prev.has_email : '');
                setGender(prev.gender || 'any');
                highlightFollowerTier();
                highlightLocationPill();
            } catch (e) {}
        }

        function clearSingleFilter(type) {
            const form = document.querySelector('form[action*="discovery/search"]');
            if (!form) return;
            if (type === 'followers') {
                addHiddenInput(form, 'min_followers', '');
                addHiddenInput(form, 'max_followers', '');
            } else if (type === 'location') {
                addHiddenInput(form, 'location', '');
            } else if (type === 'engagement') {
                addHiddenInput(form, 'min_engagement', '0');
            } else if (type === 'email') {
                addHiddenInput(form, 'has_email', '');
            } else if (type === 'gender') {
                addHiddenInput(form, 'gender', 'any');
            }
            showPageLoading();
            form.submit();
        }

        function clearAllFiltersAndSubmit() {
            const form = document.querySelector('form[action*="discovery/search"]');
            if (!form) return;
            addHiddenInput(form, 'min_followers', '');
            addHiddenInput(form, 'max_followers', '');
            addHiddenInput(form, 'location', '');
            addHiddenInput(form, 'min_engagement', '0');
            addHiddenInput(form, 'has_email', '');
            addHiddenInput(form, 'gender', 'any');
            showPageLoading();
            form.submit();
        }

        function applyFilters() {
            const minFollowers = document.getElementById('min_followers_input').value;
            const maxFollowers = document.getElementById('max_followers_input').value;
            const location = document.getElementById('location_input').value;
            const engagement = document.getElementById('engagement_slider').value;
            const hasEmail = document.getElementById('has_email_input').value;
            const gender = document.getElementById('gender_input').value;
            
            const excludeSavedBtn = document.getElementById('exclude_saved_btn');
            const excludeSaved = excludeSavedBtn ? excludeSavedBtn.classList.contains('bg-brand-green') : false;

            try {
                localStorage.setItem('instroom_discovery_prev_filters', JSON.stringify({
                    min_followers: minFollowers,
                    max_followers: maxFollowers,
                    location: location,
                    min_engagement: engagement,
                    has_email: hasEmail,
                    gender: gender
                }));
            } catch (e) {}
            
            const form = document.querySelector('form[action*="discovery/search"]');
            
            addHiddenInput(form, 'min_followers', minFollowers);
            addHiddenInput(form, 'max_followers', maxFollowers);
            addHiddenInput(form, 'location', location);
            addHiddenInput(form, 'min_engagement', engagement);
            addHiddenInput(form, 'has_email', hasEmail);
            addHiddenInput(form, 'gender', gender);
            addHiddenInput(form, 'exclude_saved', excludeSaved ? 'true' : 'false');
            
            toggleFilterModal();
            showPageLoading();
            form.submit();
        }

        function toggleExcludeSaved(btn) {
            const isChecked = btn.classList.contains('bg-brand-green');
            const newState = !isChecked;
            
            // Visual Toggle
            if (newState) {
                btn.classList.remove('bg-gray-200');
                btn.classList.add('bg-brand-green');
                btn.querySelector('span').classList.remove('translate-x-0');
                btn.querySelector('span').classList.add('translate-x-5');
            } else {
                btn.classList.add('bg-gray-200');
                btn.classList.remove('bg-brand-green');
                btn.querySelector('span').classList.add('translate-x-0');
                btn.querySelector('span').classList.remove('translate-x-5');
            }
            
            // Update Form & Submit
            const form = document.querySelector('form[action*="discovery/search"]');
            addHiddenInput(form, 'exclude_saved', newState);
            showPageLoading();
            form.submit();
        }

        function addHiddenInput(form, name, value) {
            let input = form.querySelector(`input[name="${name}"]`);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                form.appendChild(input);
            }
            input.value = value;
        }

        // Initialize Filters on Load
        document.addEventListener('DOMContentLoaded', function() {
            const params = <?php echo json_encode(isset($search_params) ? $search_params : []); ?>;
            const form = document.querySelector('form[action*="discovery/search"]');

            if(params.min_followers) {
                document.getElementById('min_followers_input').value = params.min_followers;
                addHiddenInput(form, 'min_followers', params.min_followers);
            }
            if(params.max_followers) {
                document.getElementById('max_followers_input').value = params.max_followers;
                addHiddenInput(form, 'max_followers', params.max_followers);
            }
            if(params.location) {
                document.getElementById('location_input').value = params.location;
                addHiddenInput(form, 'location', params.location);
            }
            if(params.min_engagement) {
                const engVal = params.min_engagement;
                if(engagementSlider) {
                    engagementSlider.value = engVal;
                    if(engVal > 0) {
                        document.getElementById('engagement_display').textContent = engVal + '% +';
                    }
                }
                addHiddenInput(form, 'min_engagement', params.min_engagement);
            }
            
            // Handle Buttons (Email)
            if(params.has_email !== null && params.has_email !== '') {
                setHasEmail(params.has_email);
                addHiddenInput(form, 'has_email', params.has_email);
            }

            // Handle Buttons (Gender)
            if(params.gender) {
                setGender(params.gender);
                addHiddenInput(form, 'gender', params.gender);
            }

            // Handle Exclude Saved
            if(params.exclude_saved === 'true' || params.exclude_saved === '1') {
                const btn = document.getElementById('exclude_saved_btn');
                if(btn) {
                    btn.classList.remove('bg-gray-200');
                    btn.classList.add('bg-brand-green');
                    btn.querySelector('span').classList.remove('translate-x-0');
                    btn.querySelector('span').classList.add('translate-x-5');
                    addHiddenInput(form, 'exclude_saved', 'true');
                }
            }

            highlightFollowerTier();
            highlightLocationPill();
        });
    </script>

    <!-- Select Campaign Modal -->
    <div id="campaign-select-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-gray-100 p-6 transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 text-brand-deep flex items-center justify-center text-lg font-bold">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Select Email Campaign</h3>
                        <p class="text-xs text-gray-500">Add creator(s) and launch drip sequence</p>
                    </div>
                </div>
                <button type="button" onclick="closeCampaignModal()" class="text-gray-400 hover:text-gray-600 p-1 text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-2.5 mb-6 max-h-64 overflow-y-auto custom-scrollbar">
                <?php if (!empty($campaigns)): ?>
                    <?php foreach ($campaigns as $idx => $c): ?>
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-gray-200 hover:border-brand-green hover:bg-green-50/40 cursor-pointer transition-all">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="modal_campaign_id" value="<?= (int)$c->id ?>" <?= $idx === 0 ? 'checked' : '' ?> class="w-4 h-4 text-brand-green focus:ring-brand-green border-gray-300">
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= html_escape($c->name) ?></p>
                                    <p class="text-[11px] text-gray-500 mt-0.5"><?= (int)$c->creators_count ?> Creators • <?= ucfirst($c->drip_type) ?></p>
                                </div>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded <?= $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>"><?= $c->is_active ? 'ACTIVE' : 'DRAFT' ?></span>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-6 text-sm text-gray-500">
                        <p>No active drip campaigns found.</p>
                        <a href="<?= base_url('email/drips') ?>" class="text-brand-deep font-bold text-xs hover:underline mt-1 inline-block">+ Create a Campaign</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeCampaignModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="confirmAddToCampaign()" id="confirm-campaign-btn" class="px-5 py-2.5 rounded-xl bg-brand-deep hover:bg-emerald-800 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fas fa-plus text-[10px]"></i>
                    Add & Go to Campaign
                </button>
            </div>
        </div>
    </div>

</body>
</html>
