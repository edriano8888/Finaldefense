<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Influencer List - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }



        document.addEventListener('DOMContentLoaded', function() {

            const setupBulk = () => {
                const selectAll = document.getElementById('selectAllCheckbox');
                if (selectAll && !selectAll.dataset.bound) {
                    selectAll.addEventListener('change', function() {
                        if (typeof window.toggleSelectAll === 'function') {
                            window.toggleSelectAll(this);
                        }
                    });
                    selectAll.dataset.bound = '1';
                }

                document.querySelectorAll('.influencer-checkbox').forEach(cb => {
                    if (cb.dataset.bound) return;
                    cb.addEventListener('change', function() {
                        if (typeof window.updateBulkButtonsState === 'function') {
                            window.updateBulkButtonsState();
                        }
                    });
                    cb.dataset.bound = '1';
                });

                if (typeof window.updateBulkButtonsState === 'function') {
                    window.updateBulkButtonsState();
                }
            };

            setupBulk();
        });



        // Close on click outside
        document.addEventListener('click', function(event) {
            const brandContainer = document.getElementById('brand-menu-container');
            const brandMenu = document.getElementById('brand-menu');
            if (brandContainer && brandMenu && !brandContainer.contains(event.target)) {
                brandMenu.classList.add('hidden');
            }
            const container = document.getElementById('user-menu-container');
            const menu = document.getElementById('user-menu');
            if (container && menu && !container.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        window.toggleModal = window.toggleModal || function(modalID) {
            const modal = document.getElementById(modalID);
            if (!modal) return;
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        };

        window.resetForm = window.resetForm || function(formId) {
            const form = document.getElementById(formId);
            if (form) form.reset();
            const ids = ['manual_id', 'instagram_id', 'tiktok_id'];
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
        };

        window.renderBulkActions = window.renderBulkActions || function() {
            const allCheckboxes = document.querySelectorAll('.influencer-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.influencer-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');

            const checkedCount = checkedCheckboxes.length;
            const totalCount = allCheckboxes.length;

            if (selectAllCheckbox) {
                if (totalCount > 0 && checkedCount === totalCount) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }

            if (checkedCount > 0) {
                if (bulkActions) {
                    bulkActions.classList.remove('hidden');
                    bulkActions.style.display = 'flex';
                }
                if (selectedCount) selectedCount.textContent = String(checkedCount);
            } else {
                if (bulkActions) {
                    bulkActions.classList.add('hidden');
                    bulkActions.style.display = 'none';
                }
                if (selectedCount) selectedCount.textContent = '0';
            }
        };

        window.toggleSelectAll = window.toggleSelectAll || function(source) {
            document.querySelectorAll('.influencer-checkbox').forEach(cb => {
                cb.checked = !!source.checked;
            });
            if (typeof window.renderBulkActions === 'function') {
                window.renderBulkActions();
            }
        };

        window.updateBulkButtonsState = window.updateBulkButtonsState || function() {
            if (typeof window.renderBulkActions === 'function') {
                window.renderBulkActions();
            }
        };

        window.showLoading = window.showLoading || function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.classList.remove('hidden');
        };

        window.hideLoading = window.hideLoading || function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.classList.add('hidden');
        };

        window.bulkAddPipeline = window.bulkAddPipeline || function() {
            const checkboxes = document.querySelectorAll('.influencer-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            const input = document.getElementById('bulk_influencer_ids');
            if (!input) return;
            input.value = ids.join(',');
            if (ids.length > 0) {
                window.toggleModal('pipelineStageModal');
            }
        };

        window.deleteSelected = window.deleteSelected || function() {
            const checkboxes = document.querySelectorAll('.influencer-checkbox:checked');
            const count = checkboxes.length;
            if (count <= 0) return;

            const deleteCount = document.getElementById('deleteCount');
            if (deleteCount) deleteCount.textContent = String(count);

            window.deleteAction = function() {
                window.showLoading();
                const ids = Array.from(checkboxes).map(cb => cb.value);
                fetch('<?= site_url('influencer/delete_selected') ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'ids=' + encodeURIComponent(ids.join(','))
                })
                .then(async (res) => {
                    let data = null;
                    try { data = await res.json(); } catch (e) {}
                    if (!res.ok || !data || data.status !== 'success') {
                        throw new Error((data && data.message) ? data.message : 'Delete failed');
                    }
                    return data;
                })
                .then(data => {
                    if (data && data.status === 'success') {
                        window.location.reload();
                    } else {
                        throw new Error((data && data.message) ? data.message : 'Delete failed');
                    }
                })
                .catch((err) => {
                    window.hideLoading();
                    alert(err && err.message ? err.message : 'Delete failed');
                });
            };

            window.toggleModal('deleteConfirmationModal');
        };

        function executeDelete() {
            if (window.deleteAction && typeof window.deleteAction === 'function') {
                window.deleteAction();
            }
        }

        const ASSESS_AJAX_URL = <?php echo json_encode(site_url('influencer/update_assessment_ajax')); ?>;

        window.performAssessmentUpdate = window.performAssessmentUpdate || function(id, status, reason) {
            const influencerId = parseInt(id, 10);
            if (!influencerId || !status) return;

            if (typeof window.showLoading === 'function') window.showLoading();

            const formData = new FormData();
            formData.append('id', String(influencerId));
            formData.append('assessment', String(status));
            if (reason) {
                formData.append('notes', String(reason));
            }

            fetch(ASSESS_AJAX_URL, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (typeof window.hideLoading === 'function') window.hideLoading();
                if (data && data.status === 'success') {
                    window.location.reload();
                    return;
                }
                const msg = (data && data.message) ? data.message : 'Assessment update failed';
                alert(msg);
            })
            .catch(() => {
                if (typeof window.hideLoading === 'function') window.hideLoading();
                alert('Assessment update failed');
            });
        };

        window.quickAssess = window.quickAssess || function(id, status) {
            const s = String(status || '');
            if (s === 'Not Qualified') {
                const idEl = document.getElementById('disqualify_id');
                const reasonEl = document.getElementById('disqualify_reason');
                if (idEl) idEl.value = String(id);
                if (reasonEl) reasonEl.value = '';
                if (typeof window.toggleModal === 'function') window.toggleModal('disqualificationModal');
                return;
            }
            window.performAssessmentUpdate(id, s);
        };

        window.confirmDeleteSingle = window.confirmDeleteSingle || function(id) {
            const influencerId = parseInt(id, 10);
            if (!influencerId) return;

            const deleteCount = document.getElementById('deleteCount');
            if (deleteCount) deleteCount.textContent = '1';

            const doDelete = function() {
                if (typeof window.showLoading === 'function') window.showLoading();
                fetch('<?= site_url('influencer/delete_selected') ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'ids=' + encodeURIComponent(String(influencerId))
                })
                .then(async (res) => {
                    let data = null;
                    try { data = await res.json(); } catch (e) {}
                    if (!res.ok || !data || data.status !== 'success') {
                        const msg = (data && data.message) ? data.message : 'Delete failed';
                        throw new Error(msg);
                    }
                    window.location.reload();
                })
                .catch((err) => {
                    if (typeof window.hideLoading === 'function') window.hideLoading();
                    alert(err && err.message ? err.message : 'Delete failed');
                });
            };

            window.deleteAction = doDelete;
            if (typeof window.toggleModal === 'function') window.toggleModal('deleteConfirmationModal');
        };

        const SWITCH_BRAND_AJAX_URL = <?php echo json_encode(site_url('auth/switch_brand_ajax')); ?>;
        const SWITCH_BRAND_REDIRECT = <?php echo json_encode(uri_string() . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')); ?>;
        const SWITCH_BRAND_SITE_URL = <?php echo json_encode(rtrim(site_url(), '/') . '/'); ?>;

        window.toggleBrandMenu = window.toggleBrandMenu || function() {
            const menu = document.getElementById('brand-menu');
            if (menu) menu.classList.toggle('hidden');
        };

        window.switchBrand = window.switchBrand || function(brandId) {
            const id = parseInt(brandId, 10);
            if (!id) return false;

            if (typeof window.showLoading === 'function') window.showLoading();

            fetch(SWITCH_BRAND_AJAX_URL, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'brand_id=' + encodeURIComponent(String(id))
            })
            .then(async (res) => {
                let data = null;
                try { data = await res.json(); } catch (e) {}
                if (!res.ok || !data || data.status !== 'success') {
                    const msg = (data && data.message) ? data.message : 'Switch brand failed';
                    throw new Error(msg);
                }
                const target = (SWITCH_BRAND_REDIRECT ? SWITCH_BRAND_REDIRECT : 'influencer/manage').replace(/^\/+/, '');
                window.location.href = SWITCH_BRAND_SITE_URL + target;
            })
            .catch((err) => {
                if (typeof window.hideLoading === 'function') window.hideLoading();
                alert(err && err.message ? err.message : 'Switch brand failed');
            });

            return false;
        };

        function submitBulkPipeline(formEl) {
            const form = formEl || document.getElementById('bulkPipelineForm');
            if (!form) return;

            if (typeof window.showLoading === 'function') window.showLoading();

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'success') {
                    if (typeof window.toggleModal === 'function') window.toggleModal('pipelineStageModal');
                    window.location.reload();
                } else {
                    throw new Error((data && data.message) ? data.message : 'Error moving influencers to pipeline');
                }
            })
            .catch((err) => {
                if (typeof window.hideLoading === 'function') window.hideLoading();
                alert(err && err.message ? err.message : 'Error moving influencers to pipeline');
            });
        }
    </script>
    <link href="<?php echo base_url('assets/css/influencer_manage.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">
    <?php
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
        $available_brands = $this->Brand_model->get_all_brands();
    ?>

    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        
        <!-- Top Navigation -->
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Influencer', 'child' => 'List']); ?>

        <!-- Main Content -->
        <!-- Main Content (Excel Spreadsheet Style) -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 md:p-6 scroll-smooth relative">
            <?php $this->load->view('influencer/excel_table_view'); ?>
        </main>
    </div>

    <!-- Add Influencer Modal -->
    <div id="addInfluencerModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('addInfluencerModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-8 relative animate-fade-in-up">
                
                <button onclick="toggleModal('addInfluencerModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Add New Influencer</h3>
                    <p class="text-sm text-gray-500 mb-8">Choose how you want to add an influencer to your list.</p>

                    <div class="space-y-4">
                        <button onclick="toggleModal('addInfluencerModal'); toggleModal('manualEntryModal'); resetForm('manualForm')" class="w-full bg-white border border-gray-200 hover:border-brand-blue hover:bg-blue-50/30 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-all flex items-center group">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-brand-blue flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-pen"></i>
                            </div>
                            <div class="text-left">
                                <span class="block text-gray-900">Manual Entry</span>
                                <span class="text-xs text-gray-500 font-normal">Enter details manually</span>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-brand-blue transition-colors"></i>
                        </button>

                        <button onclick="toggleModal('addInfluencerModal'); toggleModal('addInstagramModal'); resetForm('instagramForm')" class="w-full bg-white border border-gray-200 hover:border-pink-500 hover:bg-pink-50/30 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-all flex items-center group">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 text-white flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div class="text-left">
                                <span class="block text-gray-900">Instagram Influencer</span>
                                <span class="text-xs text-gray-500 font-normal">Fetch from Instagram</span>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-pink-500 transition-colors"></i>
                        </button>
                        
                        <button onclick="toggleModal('addInfluencerModal'); toggleModal('addTiktokModal'); resetForm('tiktokForm')" class="w-full bg-white border border-gray-200 hover:border-black hover:bg-gray-50 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-all flex items-center group">
                            <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fab fa-tiktok"></i>
                            </div>
                            <div class="text-left">
                                <span class="block text-gray-900">TikTok Creator</span>
                                <span class="text-xs text-gray-500 font-normal">Fetch from TikTok</span>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-black transition-colors"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div id="manualEntryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('manualEntryModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full p-8 relative animate-fade-in-up">
                
                <button onclick="toggleModal('manualEntryModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <h3 class="text-xl font-bold text-gray-900 mb-6">Add Manually</h3>

                <?php $redirect_uri = uri_string() . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''); ?>
                <form id="manualForm" action="<?= site_url('influencer/save_manual') ?>" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" id="manual_id">
                    <input type="hidden" name="redirect" value="<?php echo html_escape($redirect_uri); ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Platform <span class="text-red-500">*</span></label>
                            <select name="platform" id="manual_platform" onchange="togglePlatformFields()" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors text-sm">
                                <option value="Instagram">Instagram</option>
                                <option value="TikTok">TikTok</option>
                                <option value="YouTube">YouTube</option>
                            </select>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" placeholder="@username" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Full Name</label>
                            <input type="text" name="full_name" placeholder="First & Last Name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Email Address <span class="text-emerald-600 text-[10px] font-normal">(For Drips & Inbox)</span></label>
                            <input type="email" name="email" placeholder="influencer@realemail.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Contact Info / Phone</label>
                            <input type="text" name="contact_info" placeholder="Email, Phone or Social DM" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Followers <span class="text-red-500">*</span></label>
                            <input type="number" name="followers" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Eng. Rate (%) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="engagement_rate" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors" required>
                        </div>

                        <!-- TikTok Specific Fields -->
                        <div class="col-span-2 md:col-span-1 tiktok-field hidden">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Avg. Views</label>
                            <input type="number" name="avg_video_views" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors">
                        </div>
                        <div class="col-span-2 md:col-span-1 tiktok-field hidden">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">GMV ($)</label>
                            <input type="number" step="0.01" name="gmv" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors">
                        </div>
                        <div class="col-span-2 md:col-span-1 tiktok-field hidden">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Est. Post Rate ($)</label>
                            <input type="number" step="0.01" name="est_post_rate" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Location <span class="text-red-500">*</span></label>
                            <input type="text" name="location" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Niche <span class="text-red-500">*</span></label>
                            <select name="niche" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors text-sm">
                                <option value="">Select Niche</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Tech">Tech</option>
                                <option value="Beauty">Beauty</option>
                                <option value="Travel">Travel</option>
                            </select>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Assessment</label>
                            <select name="assessment" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors text-sm">
                                <option value="">Pending</option>
                                <option value="Qualified">Qualified</option>
                                <option value="Not Qualified">Not Qualified</option>
                            </select>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Enroll in Email Drip <span class="text-emerald-600 font-bold text-[10px]">(Auto Emails)</span></label>
                            <select name="drip_id" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-sm font-semibold">
                                <option value="">-- No Email Drip Campaign --</option>
                                <?php if (!empty($email_drips)): ?>
                                    <?php foreach ($email_drips as $drip): ?>
                                        <option value="<?= (int)$drip->id ?>">📧 <?= html_escape($drip->name) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Notes</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="toggleModal('manualEntryModal')" class="px-6 py-2.5 text-gray-600 font-medium hover:text-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-brand-green hover:bg-green-600 text-white font-medium rounded-lg shadow-sm transition-all transform hover:-translate-y-0.5">
                            Save Influencer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Instagram Modal -->
    <div id="addInstagramModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('addInstagramModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-8 relative animate-fade-in-up">
                
                <button onclick="toggleModal('addInstagramModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 text-white flex items-center justify-center shadow-md">
                        <i class="fab fa-instagram text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Add Instagram Influencer</h3>
                </div>

                <form id="instagramForm" action="<?= site_url('influencer/save_instagram') ?>" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" id="instagram_id">
                    <input type="hidden" name="redirect" value="<?php echo html_escape($redirect_uri); ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Username <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">@</span>
                                <input type="text" name="username" placeholder="username" class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors" required>
                            </div>
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Full Name</label>
                            <input type="text" name="full_name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Followers <span class="text-red-500">*</span></label>
                            <input type="number" name="followers" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Eng. Rate (%) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="engagement_rate" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Location</label>
                            <input type="text" name="location" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Niche</label>
                            <select name="niche" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors text-sm">
                                <option value="">Select Niche</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Tech">Tech</option>
                                <option value="Beauty">Beauty</option>
                                <option value="Travel">Travel</option>
                            </select>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Enroll in Email Drip <span class="text-emerald-600 font-bold text-[10px]">(Auto Emails)</span></label>
                            <select name="drip_id" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-sm font-semibold">
                                <option value="">-- No Email Drip Campaign --</option>
                                <?php if (!empty($email_drips)): ?>
                                    <?php foreach ($email_drips as $drip): ?>
                                        <option value="<?= (int)$drip->id ?>">📧 <?= html_escape($drip->name) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="toggleModal('addInstagramModal')" class="px-6 py-2.5 text-gray-600 font-medium hover:text-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-sm transition-all transform hover:-translate-y-0.5">
                            Save Influencer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TikTok Modal -->
    <div id="addTiktokModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('addTiktokModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-8 relative animate-fade-in-up">
                
                <button onclick="toggleModal('addTiktokModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center shadow-md">
                        <i class="fab fa-tiktok text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Add TikTok Creator</h3>
                </div>

                <form id="tiktokForm" action="<?= site_url('influencer/save_tiktok') ?>" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" id="tiktok_id">
                    <input type="hidden" name="redirect" value="<?php echo html_escape($redirect_uri); ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">TikTok Handle <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">@</span>
                                <input type="text" name="username" placeholder="username" class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors" required>
                            </div>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Full Name</label>
                            <input type="text" name="full_name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Followers <span class="text-red-500">*</span></label>
                            <input type="number" name="followers" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Eng. Rate (%) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="engagement_rate" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Avg. Views</label>
                            <input type="number" name="avg_video_views" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">GMV ($)</label>
                            <input type="number" step="0.01" name="gmv" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Est. Post Rate ($)</label>
                            <input type="number" step="0.01" name="est_post_rate" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Location</label>
                            <input type="text" name="location" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Niche</label>
                            <select name="niche" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-colors text-sm">
                                <option value="">Select Niche</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Tech">Tech</option>
                                <option value="Beauty">Beauty</option>
                                <option value="Travel">Travel</option>
                            </select>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase">Enroll in Email Drip <span class="text-emerald-600 font-bold text-[10px]">(Auto Emails)</span></label>
                            <select name="drip_id" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-sm font-semibold">
                                <option value="">-- No Email Drip Campaign --</option>
                                <?php if (!empty($email_drips)): ?>
                                    <?php foreach ($email_drips as $drip): ?>
                                        <option value="<?= (int)$drip->id ?>">📧 <?= html_escape($drip->name) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="toggleModal('addTiktokModal')" class="px-6 py-2.5 text-gray-600 font-medium hover:text-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-black hover:bg-gray-800 text-white font-medium rounded-lg shadow-sm transition-all transform hover:-translate-y-0.5">
                            Save Influencer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('importModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-8 relative animate-fade-in-up">
                
                <button onclick="toggleModal('importModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="flex flex-col items-center mb-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mb-4">
                        <i class="fas fa-file-csv text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Import Influencers</h3>
                    <p class="text-sm text-gray-500 mt-1">Upload a CSV file to bulk add influencers.</p>
                    <p class="text-xs text-slate-500 mt-1">Supported columns: <span class="font-semibold text-gray-700">Handle</span> (Username), <span class="font-semibold text-gray-700">Fullname</span>, <span class="font-semibold text-gray-700">Email</span></p>
                    <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <i class="fas fa-shield-alt text-[10px]"></i> Smart deduplication active (no duplicate handles or emails)
                    </span>
                    <a href="<?= site_url('influencer/download_template') ?>" class="inline-block mt-3 text-sm text-brand-blue hover:text-blue-700 font-medium hover:underline">
                        <i class="fas fa-download mr-1"></i> Download CSV Template
                    </a>
                </div>

                <form action="<?= site_url('influencer/import') ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-6">
                        <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-xl appearance-none cursor-pointer hover:border-brand-blue focus:outline-none">
                            <span class="flex items-center space-x-2">
                                <i class="fas fa-cloud-upload-alt text-gray-400"></i>
                                <span class="font-medium text-gray-600">Drop CSV file or <span class="text-brand-blue underline">browse</span></span>
                            </span>
                            <input type="file" name="file" class="hidden" accept=".csv" required>
                        </label>
                        <p class="text-xs text-center text-gray-400 mt-3">Download <a href="<?= site_url('influencer/download_template') ?>" class="text-brand-blue hover:underline font-semibold">sample template (.csv)</a></p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="toggleModal('importModal')" class="px-6 py-2.5 text-gray-600 font-medium hover:text-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-brand-blue hover:bg-blue-600 text-white font-medium rounded-lg shadow-sm transition-all transform hover:-translate-y-0.5">
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Influencer Detail Modal -->
    <div id="influencerDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('influencerDetailModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full relative animate-fade-in-up">
                
                <button onclick="toggleModal('influencerDetailModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors z-10">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="bg-white px-8 pt-7 pb-4 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                        <div class="flex items-center gap-4 min-w-0">
                            <div id="detail_avatar" class="w-12 h-12 rounded-2xl bg-brand-green text-white flex items-center justify-center font-black text-lg shadow-lg shadow-emerald-900/20">I</div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-3 min-w-0">
                                    <h3 id="detail_display_name" class="text-xl font-extrabold text-gray-900 truncate">Influencer</h3>
                                    <span id="detail_display_platform" class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-600 border border-gray-100">Platform</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                                    <span id="detail_display_username" class="font-bold text-gray-700">@-</span>
                                    <span class="text-gray-300">•</span>
                                    <span id="detail_display_email" class="text-gray-500 truncate">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full lg:w-auto">
                            <div class="min-w-0">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Status</label>
                                <select id="detail_status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="min-w-0">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pipeline Status</label>
                                <select id="detail_pipeline_status" class="w-full bg-amber-50 border border-amber-200 rounded-xl px-3 py-2.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-400/20 focus:border-amber-400 transition-all">
                                    <option value="For Outreach">For Outreach</option>
                                    <option value="Contacted">Contacted</option>
                                    <option value="Replied">Replied</option>
                                    <option value="In - Progress">In - Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Not Interested">Not Interested</option>
                                </select>
                            </div>
                            <div class="min-w-0">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Campaign Type</label>
                                <select id="detail_campaign_type" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all">
                                    <option value="">Select Type</option>
                                    <option value="Seeding">Seeding</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Affiliate">Affiliate</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-8 mt-6 overflow-x-auto">
                        <button onclick="switchTab('basic')" id="tab-btn-basic" class="tab-btn flex items-center gap-2 pb-3 border-b-2 border-black text-sm font-bold text-gray-900 whitespace-nowrap">
                            <i class="fas fa-info-circle"></i> Basic Information
                        </button>
                        <button onclick="switchTab('order')" id="tab-btn-order" class="tab-btn flex items-center gap-2 pb-3 border-b-2 border-transparent text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors whitespace-nowrap">
                            <i class="fas fa-shopping-cart"></i> Order Details
                        </button>
                        <button class="flex items-center gap-2 pb-3 border-b-2 border-transparent text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors whitespace-nowrap">
                            <i class="fas fa-chart-bar"></i> Post Insight
                        </button>
                        <button class="flex items-center gap-2 pb-3 border-b-2 border-transparent text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors whitespace-nowrap">
                            <i class="fas fa-chart-line"></i> Statistics
                        </button>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-8 bg-[#F8FAFC] max-h-[72vh] overflow-y-auto">
                    <form id="detailForm" onsubmit="event.preventDefault(); saveDetail();">
                        <input type="hidden" id="detail_id" name="id">
                        
                        <!-- Basic Information Tab -->
                        <div id="tab-basic" class="tab-content">
                            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2"><i class="fas fa-users mr-1"></i> Followers</label>
                                        <input type="text" id="detail_followers_input" name="followers" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-lg font-black text-gray-900 focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2"><i class="fas fa-chart-pie mr-1"></i> Eng. Rate</label>
                                        <input type="text" id="detail_eng_rate_input" name="engagement_rate" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-lg font-black text-gray-900 focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2"><i class="fas fa-play-circle mr-1"></i> Avg. Views</label>
                                        <input type="text" id="detail_avg_views_input" name="avg_video_views" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-lg font-black text-gray-900 focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2"><i class="fas fa-bullhorn mr-1"></i> GMV</label>
                                        <input type="text" id="detail_gmv_input" name="gmv" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-lg font-black text-gray-900 focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green outline-none transition-all">
                                    </div>
                                </div>
                            </div>



                        <!-- Details Grid -->
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                                <!-- Email -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">Email</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="email" id="detail_email_input" name="email" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                        <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">Last Name</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="text" id="detail_last_name_input" name="last_name" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                         <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>

                                <!-- First Name -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">First Name</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="text" id="detail_first_name_input" name="first_name" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                         <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>
                                
                                 <!-- Username -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">Username</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="text" id="detail_username_input" name="username" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                         <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>

                                <!-- Platform -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">Platform</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="text" id="detail_platform_input" name="platform" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                         <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>

                                <!-- IG Username -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">IG Username</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="text" id="detail_ig_username_input" name="ig_username" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                         <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">Location</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="text" id="detail_location_input" name="location" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                         <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>

                                <!-- Niche -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">Niche</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <input type="text" id="detail_niche_input" name="niche" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                         <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>

                                <!-- Assessment -->
                                <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-900">Assessment</span>
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <select id="detail_assessment_input" name="assessment" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full dir-rtl">
                                            <option value="">Pending</option>
                                            <option value="Qualified">Qualified</option>
                                            <option value="Not Qualified">Not Qualified</option>
                                        </select>
                                        <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-8">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Notes</label>
                            <textarea id="detail_notes_input" name="notes" rows="4" class="w-full bg-white border border-gray-200 rounded-2xl p-4 text-sm text-gray-700 focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green outline-none transition-all shadow-sm" placeholder="Add notes about this influencer..."></textarea>
                        </div>
                        </div> <!-- End tab-basic -->

                        <!-- Order Details Tab -->
                        <div id="tab-order" class="tab-content hidden">
                            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                                    
                                    <!-- First Name -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">First Name</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_first_name_order" readonly class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Last Name</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_last_name_order" readonly class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                        </div>
                                    </div>

                                    <!-- Contact Number -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Contact Number</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_contact_number" name="contact_number" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                    <!-- Product Name -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Product Name</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_product_name" name="product_name" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                    <!-- Order Number -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Order Number</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_order_number" name="order_number" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                    <!-- Product Cost -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Product Cost</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_product_cost" name="product_cost" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                    <!-- Discount Code -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Discount Code</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_discount_code" name="discount_code" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                    <!-- Affiliate Link -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Affiliate Link</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_affiliate_link" name="affiliate_link" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                    <!-- Shipping Address -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Shipping Address</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_shipping_address" name="shipping_address" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                    <!-- Tracking Link -->
                                    <div class="group flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="font-bold text-gray-900">Tracking Link</span>
                                        <div class="flex items-center gap-2 flex-1 justify-end">
                                            <input type="text" id="detail_tracking_link" name="tracking_link" class="text-right text-gray-600 bg-transparent border-none focus:ring-0 p-0 w-full" placeholder="-">
                                            <i class="fas fa-pen text-brand-blue text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"></i>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="sticky bottom-0 -mx-8 mt-10 px-8 py-5 bg-white/90 backdrop-blur border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" onclick="toggleModal('influencerDetailModal')" class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                                Close
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-brand-deep hover:bg-brand-green transition-colors shadow-lg shadow-emerald-900/15">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Task Modal -->
    <div id="taskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Campaign Tasks</h3>
                    <p class="text-xs text-gray-500">For <span id="taskModalUsername" class="font-bold text-brand-green"></span></p>
                </div>
                <button onclick="toggleModal('taskModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="taskListContent" class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button onclick="toggleModal('taskModal')" class="bg-brand-dark text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Pipeline Stage Selection Modal (For Bulk Action) -->
    <div id="pipelineStageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('pipelineStageModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full p-6 relative animate-fade-in-up">
                <button onclick="toggleModal('pipelineStageModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-brand-blue flex items-center justify-center">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Add to Pipeline</h3>
                </div>

                <form id="bulkPipelineForm" action="<?= site_url('influencer/bulk_add_pipeline') ?>" method="post" onsubmit="event.preventDefault(); submitBulkPipeline(this); return false;">
                    <input type="hidden" name="ids" id="bulk_influencer_ids">
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Select Stage</label>
                        <select name="stage" class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors text-sm">
                            <option value="For Outreach">For Outreach</option>
                            <option value="Contacted">Contacted</option>
                            <option value="Replied">Replied</option>
                            <option value="In - Progress">In - Progress</option>
                            <option value="Not Interested">Not Interested</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">Selected influencers will be moved to this stage.</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                         <button type="button" onclick="toggleModal('pipelineStageModal')" class="px-4 py-2 text-gray-600 text-sm font-medium hover:text-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-brand-blue hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm transition-all">
                            Move to Pipeline
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('deleteConfirmationModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full p-6 relative animate-fade-in-up">
                <button onclick="toggleModal('deleteConfirmationModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                
                <div class="text-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Delete Influencers?</h3>
                    <p class="text-sm text-gray-500 mt-2">Are you sure you want to delete <span id="deleteCount" class="font-bold text-gray-800">0</span> influencer(s)? This action cannot be undone.</p>
                </div>

                <div class="flex justify-center space-x-3">
                     <button type="button" onclick="toggleModal('deleteConfirmationModal')" class="px-4 py-2 text-gray-600 text-sm font-medium hover:text-gray-800 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="executeDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 z-[60] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-green"></div>
            <p class="mt-4 text-sm font-medium text-gray-600">Loading...</p>
        </div>
    </div>

    <script>
        let deleteAction = null;

        async function refreshNotifSummary() {
            try {
                const badge = document.getElementById('notif-badge');
                if (!badge) return;
                const res = await fetch('<?= site_url('notifications/summary') ?>', { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                const count = data.unread || 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (e) {}
        }

        function showLoading() {
             const overlay = document.getElementById('loadingOverlay');
             if(overlay) overlay.classList.remove('hidden');
        }

        function hideLoading() {
             const overlay = document.getElementById('loadingOverlay');
             if(overlay) overlay.classList.add('hidden');
        }
        
        // Attach loading to forms
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(form => {
                // Skip bulk pipeline form as we handle it manually
                if (form.id === 'bulkPipelineForm') return;
                
                form.addEventListener('submit', function() {
                    if(!this.target) showLoading(); 
                });
            });

            // Handle Bulk Pipeline Form
            const bulkPipelineForm = document.getElementById('bulkPipelineForm');
            if (bulkPipelineForm) {
                bulkPipelineForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showLoading();
                    
                    const formData = new FormData(this);
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            hideLoading();
                            alert(data.message || 'Error moving influencers to pipeline');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        alert('An error occurred');
                    });
                });
            }
        });

        function confirmDeleteSingle(id) {
            document.getElementById('deleteCount').textContent = '1';
            toggleModal('deleteConfirmationModal');
            deleteAction = function() {
                showLoading();
                fetch('<?= site_url('influencer/delete_selected') ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'ids=' + encodeURIComponent(String(id))
                })
                .then(async (res) => {
                    let data = null;
                    try { data = await res.json(); } catch (e) {}
                    if (!res.ok || !data || data.status !== 'success') {
                        const msg = (data && data.message) ? data.message : 'Delete failed';
                        throw new Error(msg);
                    }
                    window.location.reload();
                })
                .catch((err) => {
                    hideLoading();
                    alert(err && err.message ? err.message : 'Delete failed');
                });
            };
            window.deleteAction = deleteAction;
        }

        function executeDelete() {
            if (typeof deleteAction === 'function') {
                deleteAction();
                return;
            }
            if (window.deleteAction && typeof window.deleteAction === 'function') {
                window.deleteAction();
            }
        }



        // Unified header scripts are loaded from partials/header.php

        function togglePlatformFields() {
            const platform = document.getElementById('manual_platform').value;
            const tiktokFields = document.querySelectorAll('.tiktok-field');
            
            if (platform === 'TikTok') {
                tiktokFields.forEach(field => field.classList.remove('hidden'));
            } else {
                tiktokFields.forEach(field => field.classList.add('hidden'));
            }
        }

        // Auto-dismiss flash messages
        document.addEventListener('DOMContentLoaded', function() {
            refreshNotifSummary();
            setSidebarCollapsed(false);
            setTimeout(function() {
                const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });


        window.addEventListener('resize', function() {
            setSidebarCollapsed(sidebarCollapsed);
        });

        function viewTasks(id, username) {
            document.getElementById('taskModalUsername').innerText = username;
            document.getElementById('taskListContent').innerHTML = `
                <div class="py-10 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-green mx-auto mb-2"></div>
                    <p class="text-gray-400 text-sm">Loading tasks...</p>
                </div>
            `;
            toggleModal('taskModal');
            
            fetch(`<?= site_url('influencer/get_tasks_ajax/') ?>${id}`)
                .then(res => res.json())
                .then(result => {
                    if (result.status === 'success' && result.data.length > 0) {
                        let html = '<div class="space-y-4">';
                        
                        // Group by campaign
                        const campaigns = [...new Set(result.data.map(t => t.campaign_title))];
                        
                        campaigns.forEach(campTitle => {
                            html += `
                                <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                                    <div class="px-4 py-2 bg-white border-b border-gray-100 flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Campaign</span>
                                        <span class="text-sm font-bold text-brand-dark">${campTitle}</span>
                                    </div>
                                    <div class="p-3 space-y-2">
                            `;
                            
                            result.data.filter(t => t.campaign_title === campTitle).forEach(task => {
                                html += `
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-50 hover:border-brand-green/30 transition-all group">
                                        <div class="flex items-center gap-3">
                                            <button 
                                                onclick="toggleTaskStatus(${task.id}, '${task.status}', this)"
                                                class="w-5 h-5 rounded border-2 transition-all flex items-center justify-center ${task.status === 'Done' ? 'bg-brand-green border-brand-green text-white' : 'border-gray-300 hover:border-brand-green text-transparent'}"
                                            >
                                                <i class="fas fa-check text-[10px]"></i>
                                            </button>
                                            <span class="task-name text-sm font-medium ${task.status === 'Done' ? 'text-gray-400 line-through' : 'text-gray-700'}">
                                                ${task.task_name}
                                            </span>
                                        </div>
                                        <span class="task-status text-[10px] font-bold uppercase tracking-wider ${task.status === 'Done' ? 'text-brand-green' : 'text-orange-400'}">
                                            ${task.status}
                                        </span>
                                    </div>
                                `;
                            });
                            
                            html += `</div></div>`;
                        });
                        
                        html += '</div>';
                        document.getElementById('taskListContent').innerHTML = html;
                    } else {
                        document.getElementById('taskListContent').innerHTML = `
                            <div class="text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                    <i class="fas fa-clipboard-list text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No tasks assigned yet.</p>
                                <p class="text-xs text-gray-400 mt-1">Check back later or contact your manager.</p>
                            </div>
                        `;
                    }
                });
        }

        function toggleTaskStatus(taskId, currentStatus, btn) {
            const newStatus = currentStatus === 'Pending' ? 'Done' : 'Pending';
            const row = btn.closest('.group');
            const nameSpan = row.querySelector('.task-name');
            const statusSpan = row.querySelector('.task-status');
            
            // Optimistic update
            if (newStatus === 'Done') {
                btn.classList.add('bg-brand-green', 'border-brand-green', 'text-white');
                btn.classList.remove('border-gray-300', 'hover:border-brand-green', 'text-transparent');
                nameSpan.classList.add('text-gray-400', 'line-through');
                nameSpan.classList.remove('text-gray-700');
                statusSpan.innerText = 'Done';
                statusSpan.classList.add('text-brand-green');
                statusSpan.classList.remove('text-orange-400');
            } else {
                btn.classList.remove('bg-brand-green', 'border-brand-green', 'text-white');
                btn.classList.add('border-gray-300', 'hover:border-brand-green', 'text-transparent');
                nameSpan.classList.remove('text-gray-400', 'line-through');
                nameSpan.classList.add('text-gray-700');
                statusSpan.innerText = 'Pending';
                statusSpan.classList.remove('text-brand-green');
                statusSpan.classList.add('text-orange-400');
            }

            const formData = new FormData();
            formData.append('task_id', taskId);
            formData.append('status', newStatus);

            fetch('<?= site_url('influencer/update_task_status_ajax') ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.status !== 'success') {
                    alert('Failed to update task');
                    location.reload(); // Rollback by reload
                } else {
                    // Update the onclick for next toggle
                    btn.setAttribute('onclick', `toggleTaskStatus(${taskId}, '${newStatus}', this)`);
                }
            });
        }

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                if (modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            }
        }

        function resetForm(formId) {
            const form = document.getElementById(formId);
            if (form) {
                form.reset();
                if (formId === 'manualForm') document.getElementById('manual_id').value = '';
                if (formId === 'instagramForm') document.getElementById('instagram_id').value = '';
                if (formId === 'tiktokForm') document.getElementById('tiktok_id').value = '';
            }
        }

        function toggleFilters() {
            const filters = document.getElementById('mainFilterOptionsRow');
            if (filters.classList.contains('hidden')) {
                filters.classList.remove('hidden');
            } else {
                filters.classList.add('hidden');
            }
        }

        // Centralized Bulk Actions Renderer
        function renderBulkActions() {
            const allCheckboxes = document.querySelectorAll('.influencer-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.influencer-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            
            const checkedCount = checkedCheckboxes.length;
            const totalCount = allCheckboxes.length;

            // Update Select All checkbox state
            if (selectAllCheckbox) {
                if (totalCount > 0 && checkedCount === totalCount) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }

            // Update Bulk Actions Container Visibility
            if (checkedCount > 0) {
                if (bulkActions) {
                    bulkActions.classList.remove('hidden');
                    bulkActions.style.display = 'flex';
                }
                
                if (selectedCount) selectedCount.textContent = checkedCount;
            } else {
                if (bulkActions) {
                    bulkActions.classList.add('hidden');
                    bulkActions.style.display = 'none';
                }
            }
        }

        // Bulk Actions Logic
        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.influencer-checkbox');
            
            // Set all checkboxes to match the source
            checkboxes.forEach(cb => {
                cb.checked = source.checked;
            });
            
            renderBulkActions();
        }

        function updateBulkButtonsState() {
            renderBulkActions();
        }

        function bulkAddPipeline() {
            const checkboxes = document.querySelectorAll('.influencer-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            if (ids.length > 0) {
                document.getElementById('bulk_influencer_ids').value = ids.join(',');
                toggleModal('pipelineStageModal');
            }
        }
        
        function bulkExport() {
            const checkboxes = document.querySelectorAll('.influencer-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length > 0) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= site_url('influencer/export_selected_csv') ?>';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids';
                input.value = ids.join(',');
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        }
        
        function deleteSelected() {
             const checkboxes = document.querySelectorAll('.influencer-checkbox:checked');
             if (checkboxes.length > 0) {
                 document.getElementById('deleteCount').textContent = checkboxes.length;
                 toggleModal('deleteConfirmationModal');
                 
                 const ids = Array.from(checkboxes).map(cb => cb.value);
                 const doDelete = function() {
                     showLoading();
                     fetch('<?= site_url('influencer/delete_selected') ?>', {
                         method: 'POST',
                         credentials: 'same-origin',
                         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                         body: 'ids=' + encodeURIComponent(ids.join(','))
                     })
                     .then(async (res) => {
                         let data = null;
                         try { data = await res.json(); } catch (e) {}
                         if (!res.ok || !data || data.status !== 'success') {
                             const msg = (data && data.message) ? data.message : 'Error deleting items';
                             throw new Error(msg);
                         }
                         window.location.reload();
                     })
                     .catch(err => {
                         hideLoading();
                         alert(err && err.message ? err.message : 'Error deleting items');
                     });
                 };
                 deleteAction = doDelete;
                 window.deleteAction = doDelete;
             }
        }

        function setVal(id, val) {
            const el = document.getElementById(id);
            if(el) el.value = val !== null && val !== undefined ? val : '';
        }

        window.quickAssess = function(id, assessment) {
            const labelEl = document.getElementById('assessment-label-' + id);
            const statusEl = document.getElementById('status-badge-' + id);
            const btnQual = document.getElementById('btn-qual-' + id);
            const btnNotQual = document.getElementById('btn-notqual-' + id);

            const formData = new FormData();
            formData.append('id', id);
            formData.append('assessment', assessment);

            fetch('<?= site_url("influencer/update_assessment_ajax") ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.status === 'success') {
                    if (labelEl) {
                        labelEl.textContent = assessment;
                        labelEl.className = 'text-[11px] font-bold px-2 py-0.5 rounded-md inline-block ' + 
                            (assessment === 'Qualified' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200');
                    }
                    if (btnQual && btnNotQual) {
                        if (assessment === 'Qualified') {
                            btnQual.className = 'w-7 h-7 rounded-full bg-emerald-500 text-white border border-emerald-500 flex items-center justify-center transition-all shadow-xs';
                            btnNotQual.className = 'w-7 h-7 rounded-full border border-rose-300 text-rose-600 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all';
                        } else {
                            btnNotQual.className = 'w-7 h-7 rounded-full bg-rose-500 text-white border border-rose-500 flex items-center justify-center transition-all shadow-xs';
                            btnQual.className = 'w-7 h-7 rounded-full border border-emerald-300 text-emerald-600 hover:bg-emerald-500 hover:text-white flex items-center justify-center transition-all';
                        }
                    }
                    if (statusEl && assessment === 'Qualified') {
                        statusEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>For Outreach';
                        statusEl.className = 'px-2.5 py-1 inline-flex items-center gap-1.5 text-[11px] font-extrabold rounded-full border bg-yellow-50 text-yellow-700 border-yellow-200';
                    }
                } else {
                    alert(data && data.message ? data.message : 'Failed to update assessment');
                }
            })
            .catch(err => console.error('Assessment update error:', err));
        };

        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Show selected
            const target = document.getElementById('tab-' + tabName);
            if(target) target.classList.remove('hidden');

            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-black', 'text-gray-900', 'font-bold');
                btn.classList.add('border-transparent', 'text-gray-400', 'font-medium');
            });
            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if(activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-gray-400', 'font-medium');
                activeBtn.classList.add('border-black', 'text-gray-900', 'font-bold');
            }
        }

        function openDetailModal(id) {
            showLoading();
            // Reset tab
            switchTab('basic');
            
            fetch('<?= site_url('influencer/get_data/') ?>' + id, { credentials: 'same-origin' })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    
                    // ID
                    setVal('detail_id', data.id);

                    // Header Dropdowns
                    setVal('detail_status', data.status || 'Active');
                    setVal('detail_pipeline_status', data.pipeline_status || 'For Outreach');
                    setVal('detail_campaign_type', data.campaign_type || '');

                    // Metrics
                    setVal('detail_followers_input', data.followers);
                    setVal('detail_eng_rate_input', data.engagement_rate || data.eng_rate);
                    setVal('detail_avg_views_input', data.avg_video_views);
                    setVal('detail_gmv_input', data.gmv);

                    // Basic Info
                    setVal('detail_email_input', data.email);
                    setVal('detail_last_name_input', data.last_name);
                    setVal('detail_first_name_input', data.first_name);
                    setVal('detail_username_input', data.username);
                    setVal('detail_platform_input', data.platform);
                    setVal('detail_ig_username_input', data.ig_username);
                    setVal('detail_location_input', data.location);
                    setVal('detail_niche_input', data.niche);
                    setVal('detail_assessment_input', data.assessment);
                    setVal('detail_notes_input', data.notes);
                    
                    // Order Details
                    setVal('detail_first_name_order', data.first_name);
                    setVal('detail_last_name_order', data.last_name);
                    setVal('detail_contact_number', data.contact_number);
                    setVal('detail_product_name', data.product_name);
                    setVal('detail_order_number', data.order_number);
                    setVal('detail_product_cost', data.product_cost);
                    setVal('detail_discount_code', data.discount_code);
                    setVal('detail_affiliate_link', data.affiliate_link);
                    setVal('detail_shipping_address', data.shipping_address);
                    setVal('detail_tracking_link', data.tracking_link);

                    const displayNameEl = document.getElementById('detail_display_name');
                    const usernameEl = document.getElementById('detail_display_username');
                    const emailEl = document.getElementById('detail_display_email');
                    const platformEl = document.getElementById('detail_display_platform');
                    const avatarEl = document.getElementById('detail_avatar');

                    const fullName = ((data.first_name || '') + ' ' + (data.last_name || '')).trim() || (data.full_name || '');
                    const displayName = fullName || data.username || 'Influencer';
                    const displayUsername = data.username ? '@' + data.username : '@-';
                    const displayEmail = data.email || data.contact_info || '-';
                    const displayPlatform = data.platform || 'Platform';

                    if (displayNameEl) displayNameEl.textContent = displayName;
                    if (usernameEl) usernameEl.textContent = displayUsername;
                    if (emailEl) emailEl.textContent = displayEmail;
                    if (platformEl) {
                        platformEl.textContent = displayPlatform;
                        platformEl.classList.remove('hidden');
                    }
                    if (avatarEl) {
                        const initial = (displayName || 'I').trim().charAt(0).toUpperCase();
                        avatarEl.textContent = initial || 'I';
                    }

                    toggleModal('influencerDetailModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoading();
                });
        }

        function saveDetail() {
            showLoading();
            const form = document.getElementById('detailForm');
            const formData = new FormData(form);
            
            // Add header fields manually since they are outside the form tag
            const pipelineStatus = document.getElementById('detail_pipeline_status');
            const campaignType = document.getElementById('detail_campaign_type');
            const status = document.getElementById('detail_status');
            
            if(pipelineStatus) formData.append('pipeline_status', pipelineStatus.value);
            if(campaignType) formData.append('campaign_type', campaignType.value);
            if(status) formData.append('status', status.value);

            fetch('<?= site_url('influencer/update_detail_ajax') ?>', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if(data.status === 'success') {
                    // Show success feedback
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[70] animate-fade-in-up';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Saved successfully';
                    document.body.appendChild(alertDiv);
                    
                    setTimeout(() => {
                        alertDiv.remove();
                        toggleModal('influencerDetailModal');
                        window.location.reload(); 
                    }, 1000);
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                alert('An error occurred while saving.');
            });
        }

        function editInfluencer(id) {
            showLoading();
            fetch('<?= site_url('influencer/get_data/') ?>' + id)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    // Populate manual form for editing
                    const form = document.getElementById('manualForm');
                    document.getElementById('manual_id').value = data.id;
                    if(form.elements['platform']) {
                        form.elements['platform'].value = data.platform;
                        togglePlatformFields(); // Trigger visibility toggle
                    }
                    if(form.elements['username']) form.elements['username'].value = data.username || '';
                    if(form.elements['full_name']) form.elements['full_name'].value = data.full_name || '';
                    if(form.elements['email']) form.elements['email'].value = data.email || '';
                    if(form.elements['contact_info']) form.elements['contact_info'].value = data.contact_info || data.email || ''; 
                    if(form.elements['followers']) form.elements['followers'].value = data.followers || 0;
                    if(form.elements['engagement_rate']) form.elements['engagement_rate'].value = data.engagement_rate || data.eng_rate;
                    if(form.elements['location']) form.elements['location'].value = data.location;
                    if(form.elements['niche']) form.elements['niche'].value = data.niche;
                    if(form.elements['assessment']) form.elements['assessment'].value = data.assessment || '';

                    if(form.elements['notes']) form.elements['notes'].value = data.notes || '';
                    
                    // Populate TikTok Specific Fields if they exist
                    if(form.elements['avg_video_views']) form.elements['avg_video_views'].value = data.avg_video_views || '';
                    if(form.elements['gmv']) form.elements['gmv'].value = data.gmv || '';
                    if(form.elements['est_post_rate']) form.elements['est_post_rate'].value = data.est_post_rate || '';
                    
                    toggleModal('manualEntryModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoading();
                });
        }

        function quickAssess(id, status) {
            if (status === 'Not Qualified') {
                // Open Disqualification Modal
                document.getElementById('disqualify_id').value = id;
                document.getElementById('disqualify_reason').value = ''; // Reset reason
                toggleModal('disqualificationModal');
                return;
            }

            // Existing logic for Qualified
            performAssessmentUpdate(id, status);
        }

        function submitDisqualification() {
            const id = document.getElementById('disqualify_id').value;
            const reason = document.getElementById('disqualify_reason').value;
            
            if (!reason.trim()) {
                alert('Please enter a reason.');
                return;
            }

            performAssessmentUpdate(id, 'Not Qualified', reason);
        }

        function performAssessmentUpdate(id, status, reason = '') {
            showLoading();
            const formData = new FormData();
            formData.append('id', id);
            formData.append('assessment', status);
            if (reason) {
                formData.append('notes', reason);
            }

            fetch('<?= site_url('influencer/update_assessment_ajax') ?>', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if(data.status === 'success') {
                    // Close modal if open
                    const modal = document.getElementById('disqualificationModal');
                    if (!modal.classList.contains('hidden')) {
                        toggleModal('disqualificationModal');
                    }

                    // Update the UI immediately
                    const label = document.getElementById('assessment-label-' + id);
                    if(label) {
                        label.innerText = status;
                        label.className = 'text-sm font-semibold ' + 
                            (status === 'Qualified' ? 'text-green-600' : 
                             (status === 'Not Qualified' ? 'text-red-600' : 'text-gray-500'));
                    }
                    
                    // Show success feedback
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[70] animate-fade-in-up';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Assessment Updated';
                    document.body.appendChild(alertDiv);
                    
                    setTimeout(() => {
                        alertDiv.remove();
                        // Reload to show pipeline change or updated notes
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                alert('An error occurred.');
            });
        }

        function openInlineNote(id) {
            const disp = document.getElementById('note-display-' + id);
            const edit = document.getElementById('note-editor-' + id);
            const inp = document.getElementById('note-input-' + id);
            if (!disp || !edit || !inp) return;
            inp.dataset.original = inp.value;
            disp.classList.add('hidden');
            edit.classList.remove('hidden');
            inp.focus();
            inp.setSelectionRange(inp.value.length, inp.value.length);
        }

        function cancelInlineNote(id) {
            const disp = document.getElementById('note-display-' + id);
            const edit = document.getElementById('note-editor-' + id);
            const inp = document.getElementById('note-input-' + id);
            if (!disp || !edit || !inp) return;
            if (inp.dataset.original !== undefined) {
                inp.value = inp.dataset.original;
            }
            edit.classList.add('hidden');
            disp.classList.remove('hidden');
        }

        function handleInlineNoteKey(e, id) {
            if (e.key === 'Escape') {
                e.preventDefault();
                cancelInlineNote(id);
            } else if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                saveInlineNote(id);
            }
        }

        function saveInlineNote(id) {
            const disp = document.getElementById('note-display-' + id);
            const edit = document.getElementById('note-editor-' + id);
            const txt = document.getElementById('note-text-' + id);
            const inp = document.getElementById('note-input-' + id);
            const btn = document.getElementById('note-save-btn-' + id);
            if (!disp || !edit || !txt || !inp) return;

            const newNote = inp.value.trim();
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Saving...';
            }

            const formData = new FormData();
            formData.append('id', String(id));
            formData.append('notes', newNote);

            fetch('<?= site_url('influencer/update_notes_ajax') ?>', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Save';
                }
                if (data && data.status === 'success') {
                    const savedNote = (data.notes !== undefined ? data.notes : newNote).trim();
                    inp.value = savedNote;
                    inp.dataset.original = savedNote;
                    if (savedNote !== '') {
                        txt.textContent = savedNote;
                        txt.className = 'text-xs leading-relaxed whitespace-normal break-words line-clamp-2 flex-1 text-gray-700 font-medium';
                        disp.className = 'group/note flex items-start justify-between gap-2 px-2.5 py-1.5 rounded-lg border border-transparent hover:border-emerald-200 hover:bg-emerald-50/40 cursor-pointer transition-all';
                    } else {
                        txt.textContent = '+ Add note';
                        txt.className = 'text-xs leading-relaxed whitespace-normal break-words line-clamp-2 flex-1 text-gray-400 italic';
                        disp.className = 'group/note flex items-start justify-between gap-2 px-2.5 py-1.5 rounded-lg border border-dashed border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/40 cursor-pointer transition-all';
                    }
                    edit.classList.add('hidden');
                    disp.classList.remove('hidden');

                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'fixed top-4 right-4 bg-brand-green text-white px-4 py-2 rounded-lg shadow-lg z-[70] text-xs font-bold animate-fade-in-up';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle mr-1.5"></i> Note saved';
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 1800);
                } else {
                    alert((data && data.message) ? data.message : 'Failed to save note');
                }
            })
            .catch(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Save';
                }
                alert('Server error while saving note.');
            });
        }
    </script>
    <!-- Disqualification Reason Modal -->
    <div id="disqualificationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="toggleModal('disqualificationModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-8 relative animate-fade-in-up">
                
                <button onclick="toggleModal('disqualificationModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Disqualification Reason</h3>
                    <p class="text-sm text-gray-500 mb-6">Please specify why this influencer is not qualified.</p>
                </div>

                <div class="space-y-4">
                    <input type="hidden" id="disqualify_id">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Reason / Notes</label>
                        <textarea id="disqualify_reason" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-colors text-sm resize-none" placeholder="Enter reason..."></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="toggleModal('disqualificationModal')" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="button" onclick="submitDisqualification()" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleMyList() {
            const allList = document.getElementById('allListContainer');
            const myList = document.getElementById('myListContainer');
            const btn = document.getElementById('myListBtn');
            
            if (myList.classList.contains('hidden')) {
                // Show My List
                allList.classList.add('hidden');
                myList.classList.remove('hidden');
                
                // Update button
                btn.innerHTML = '<i class="fas fa-users mr-2 text-gray-400 group-hover:text-gray-600 transition-colors"></i> All List';
            } else {
                // Show All List
                myList.classList.add('hidden');
                allList.classList.remove('hidden');
                
                // Update button
                btn.innerHTML = '<i class="fas fa-list mr-2 text-gray-400 group-hover:text-gray-600 transition-colors"></i> My List';
            }
        }
    </script>
    <script>
        (function() {
            const assessUrl = <?php echo json_encode(site_url('influencer/update_assessment_ajax')); ?>;
            const deleteUrl = <?php echo json_encode(site_url('influencer/delete_selected')); ?>;

            window.performAssessmentUpdate = function(id, status, reason) {
                const influencerId = parseInt(id, 10);
                const s = String(status || '');
                if (!influencerId || !s) return;

                if (typeof window.showLoading === 'function') window.showLoading();
                const formData = new FormData();
                formData.append('id', String(influencerId));
                formData.append('assessment', s);
                if (reason) formData.append('notes', String(reason));

                fetch(assessUrl, { method: 'POST', credentials: 'same-origin', body: formData })
                    .then(async (res) => {
                        let data = null;
                        try { data = await res.json(); } catch (e) {}
                        if (!res.ok || !data || data.status !== 'success') {
                            const msg = (data && data.message) ? data.message : 'Assessment update failed';
                            throw new Error(msg);
                        }
                        window.location.reload();
                    })
                    .catch((err) => {
                        if (typeof window.hideLoading === 'function') window.hideLoading();
                        alert(err && err.message ? err.message : 'Assessment update failed');
                    });
            };

            window.quickAssess = function(id, status) {
                const s = String(status || '');
                if (s === 'Not Qualified') {
                    const idEl = document.getElementById('disqualify_id');
                    const reasonEl = document.getElementById('disqualify_reason');
                    if (idEl) idEl.value = String(id);
                    if (reasonEl) reasonEl.value = '';
                    if (typeof window.toggleModal === 'function') window.toggleModal('disqualificationModal');
                    return;
                }
                window.performAssessmentUpdate(id, s, '');
            };

            window.submitDisqualification = function() {
                const idEl = document.getElementById('disqualify_id');
                const reasonEl = document.getElementById('disqualify_reason');
                const id = idEl ? idEl.value : '';
                const reason = reasonEl ? reasonEl.value : '';
                if (!String(reason || '').trim()) {
                    alert('Please enter a reason.');
                    return;
                }
                window.performAssessmentUpdate(id, 'Not Qualified', reason);
            };

            window.confirmDeleteSingle = function(id) {
                const influencerId = parseInt(id, 10);
                if (!influencerId) return;

                const deleteCount = document.getElementById('deleteCount');
                if (deleteCount) deleteCount.textContent = '1';

                window.deleteAction = function() {
                    if (typeof window.showLoading === 'function') window.showLoading();
                    fetch(deleteUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'ids=' + encodeURIComponent(String(influencerId))
                    })
                    .then(async (res) => {
                        let data = null;
                        try { data = await res.json(); } catch (e) {}
                        if (!res.ok || !data || data.status !== 'success') {
                            const msg = (data && data.message) ? data.message : 'Delete failed';
                            throw new Error(msg);
                        }
                        window.location.reload();
                    })
                    .catch((err) => {
                        if (typeof window.hideLoading === 'function') window.hideLoading();
                        alert(err && err.message ? err.message : 'Delete failed');
                    });
                };

                if (typeof window.toggleModal === 'function') window.toggleModal('deleteConfirmationModal');
            };

            window.executeDelete = function() {
                if (window.deleteAction && typeof window.deleteAction === 'function') {
                    window.deleteAction();
                }
            };
        })();
    </script>
</body>
</html>
