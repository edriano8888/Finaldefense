<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <title>Influencer Discovery - Instroom</title>
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

        // Add Influencer AJAX
        function addInfluencer(influencerData, btnElement) {
            const originalText = btnElement.innerHTML;
            const originalClasses = btnElement.className;
            
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Saving...';
            btnElement.disabled = true;

            $.ajax({
                url: '<?php echo base_url("discovery/add"); ?>',
                type: 'POST',
                data: influencerData,
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
                error: function() {
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                    showToast('Error', 'Server error. Please try again.');
                }
            });
        }

        function showToast(title, message) {
            if (typeof window.showToast === 'function') {
                const isErr = (title && title.toLowerCase() === 'error');
                window.showToast(isErr ? 'Notice' : (title || 'Success'), message, isErr ? 'error' : 'success');
            }
        }
        
        function setPlatform(p) {
            const select = document.querySelector('select[name="platform"]');
            if (!select) return;
            select.value = p;
            updatePlatformTabs(p);
            updatePlatformIcon(p);
        }

        function updatePlatformTabs(active) {
            document.querySelectorAll('[data-platform-tab]').forEach(btn => {
                const val = btn.getAttribute('data-platform-tab');
                if (val === active) {
                    btn.classList.add('bg-brand-green','text-white');
                    btn.classList.remove('bg-white','text-gray-700','border-gray-200');
                } else {
                    btn.classList.add('bg-white','text-gray-700','border-gray-200');
                    btn.classList.remove('bg-brand-green','text-white');
                }
            });
        }

        function updatePlatformIcon(active) {
            const icon = document.getElementById('platformIcon');
            if (!icon) return;
            if (active === 'youtube') {
                icon.className = 'fab fa-youtube text-red-600';
            } else if (active === 'instagram') {
                icon.className = 'fab fa-instagram text-pink-600';
            } else if (active === 'tiktok') {
                icon.className = 'fab fa-tiktok text-black';
            } else {
                icon.className = 'fas fa-globe text-gray-400';
            }
        }

        function showPageLoading() {
            const el = document.getElementById('page-loading');
            if (el) el.classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const select = document.querySelector('select[name="platform"]');
            if (select) {
                updatePlatformTabs(select.value);
                updatePlatformIcon(select.value);
                select.addEventListener('change', (e) => {
                    updatePlatformTabs(e.target.value);
                    updatePlatformIcon(e.target.value);
                });
            }
        });
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
        <?php $this->load->view('partials/header', ['parent' => 'Influencer', 'child' => 'Discovery']); ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F8FAFC] relative">
            
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-[32rem] bg-gradient-to-b from-green-50/60 via-blue-50/30 to-transparent -z-10"></div>
            <div class="absolute top-20 right-20 w-96 h-96 bg-green-200/20 rounded-full blur-3xl -z-10 animate-pulse"></div>
            <div class="absolute top-40 left-10 w-64 h-64 bg-blue-200/20 rounded-full blur-3xl -z-10 animate-pulse" style="animation-delay: 2s"></div>
            
            <div class="max-w-7xl mx-auto px-6 py-16 md:py-24 relative">

                <!-- Hero Section -->
                <div class="text-center mb-16 relative">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-[2rem] shadow-xl mb-8 text-3xl text-brand-green transform -rotate-6 border border-green-100 hover:rotate-0 transition-transform duration-500">
                        <i class="fas fa-search-plus"></i>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-black text-brand-deep tracking-tight mb-4">
                        Discover the perfect <span class="text-brand-green">creators</span>
                    </h1>
                    <p class="text-lg text-gray-500 font-medium max-w-2xl mx-auto leading-relaxed">
                        Identify influencers who truly resonate with your audience and drive authentic brand growth across platforms.
                    </p>
                </div>

                <!-- Modern Search Component -->
                <div class="max-w-5xl mx-auto mb-20">
                    <form action="<?php echo base_url('discovery/search'); ?>" method="post" onsubmit="showPageLoading()" class="relative z-20">
                        <!-- Persistent Filters -->
                        <input type="hidden" name="min_followers" value="<?php echo isset($search_params['min_followers']) ? $search_params['min_followers'] : ''; ?>">
                        <input type="hidden" name="max_followers" value="<?php echo isset($search_params['max_followers']) ? $search_params['max_followers'] : ''; ?>">
                        <input type="hidden" name="location" value="<?php echo isset($search_params['location']) ? $search_params['location'] : ''; ?>">
                        <input type="hidden" name="min_engagement" value="<?php echo isset($search_params['min_engagement']) ? $search_params['min_engagement'] : ''; ?>">
                        <input type="hidden" name="has_email" value="<?php echo isset($search_params['has_email']) ? $search_params['has_email'] : ''; ?>">
                        <input type="hidden" name="gender" value="<?php echo isset($search_params['gender']) ? $search_params['gender'] : ''; ?>">

                        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(15,107,62,0.12)] border border-gray-100 p-3 flex flex-col md:flex-row items-stretch gap-2">
                            
                            <!-- Platform Selector -->
                            <div class="relative group min-w-[140px]">
                                <select name="platform" id="platformSelect" onchange="updateSearchUI(this.value)" class="appearance-none w-full h-full py-4 pl-12 pr-10 bg-gray-50/50 rounded-2xl cursor-pointer focus:outline-none text-gray-800 font-bold text-sm border border-transparent focus:border-green-100 transition-all">
                                    <option value="instagram" <?php echo (isset($search_params['platform']) && $search_params['platform'] == 'instagram') ? 'selected' : ''; ?>>Instagram</option>
                                    <option value="youtube" <?php echo (isset($search_params['platform']) && $search_params['platform'] == 'youtube') ? 'selected' : ''; ?>>YouTube</option>
                                    <option value="tiktok" <?php echo (isset($search_params['platform']) && $search_params['platform'] == 'tiktok') ? 'selected' : ''; ?>>TikTok</option>
                                </select>
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-lg">
                                    <i id="searchPlatformIcon" class="<?php 
                                        $p = isset($search_params['platform']) ? $search_params['platform'] : 'instagram';
                                        if($p == 'youtube') echo 'fab fa-youtube text-red-500';
                                        elseif($p == 'tiktok') echo 'fab fa-tiktok text-gray-900';
                                        else echo 'fab fa-instagram text-pink-500';
                                    ?>"></i>
                                </div>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[10px] text-gray-400">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            <!-- Search Input -->
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                                    <i class="fas fa-hashtag text-brand-green/30 text-lg"></i>
                                </div>
                                <input type="text" name="keyword" value="<?php echo isset($search_params['keyword']) ? $search_params['keyword'] : ''; ?>" 
                                    class="w-full h-full py-4 pl-12 pr-4 bg-transparent text-gray-900 placeholder-gray-400 font-medium focus:outline-none text-base" 
                                    placeholder="e.g. fashion, tech, #lifestyle, @username...">
                            </div>

                            <!-- Filter Button -->
                            <button type="button" id="main-filter-btn" onclick="toggleFilterModal()" class="bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-4 px-6 rounded-[1.8rem] transition-all shadow-sm flex items-center justify-center gap-2 group border border-transparent">
                                <i class="fas fa-filter text-brand-green"></i>
                                <span class="hidden md:inline" id="main-filter-label">Filters</span>
                            </button>

                            <!-- Action Button -->
                            <button type="submit" class="bg-brand-green hover:bg-brand-deep text-white font-black py-4 px-10 rounded-[1.8rem] transition-all shadow-lg shadow-green-200 flex items-center justify-center gap-3 group">
                                <span>Find Creators</span>
                                <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Recommended & Recent Tags -->
                    <div class="mt-10 space-y-4">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-2">Recommended:</span>
                            <?php if(!empty($recommended_searches)): ?>
                                <?php foreach($recommended_searches as $item): ?>
                                    <button type="button" onclick="setSearchKeyword('<?php echo htmlspecialchars($item['keyword'], ENT_QUOTES); ?>')" class="px-4 py-1.5 bg-white border border-gray-100 text-gray-600 rounded-xl hover:bg-green-50 hover:text-brand-green hover:border-green-200 transition-all text-xs font-bold shadow-sm">
                                        #<?php echo htmlspecialchars(ltrim($item['keyword'], '#')); ?>
                                    </button>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <?php if(!empty($recent_searches)): ?>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-2">Recent:</span>
                            <?php foreach($recent_searches as $item): ?>
                                <button type="button" onclick="setSearchKeyword('<?php echo htmlspecialchars($item['keyword'], ENT_QUOTES); ?>')" class="px-3 py-1 bg-gray-50 border border-transparent rounded-lg flex items-center gap-2 text-gray-500 hover:bg-white hover:border-gray-200 transition-all text-xs font-bold">
                                    <i class="fas fa-history text-[10px] opacity-50"></i> <?php echo htmlspecialchars($item['keyword']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if(isset($error)): ?>
                <div class="max-w-4xl mx-auto mb-10">
                    <div class="rounded-[2rem] border border-red-100 bg-red-50 text-red-700 p-6 flex items-center gap-4 animate-fade-in shadow-sm">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-red-500 shadow-sm border border-red-100 shrink-0">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-sm uppercase tracking-wider">Search Error</h4>
                            <p class="text-sm font-medium opacity-80"><?php echo $error; ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Features/Placeholder Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12 opacity-80">
                    <div class="bg-white/50 backdrop-blur-sm p-8 rounded-[2.5rem] border border-white shadow-sm hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h3 class="font-black text-gray-900 mb-2">Audience Insights</h3>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Deep dive into influencer audience demographics and engagement quality.</p>
                    </div>
                    <div class="bg-white/50 backdrop-blur-sm p-8 rounded-[2.5rem] border border-white shadow-sm hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-brand-green/10 rounded-2xl flex items-center justify-center text-brand-green mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="font-black text-gray-900 mb-2">Instant Discovery</h3>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">Find matching creators in seconds using our real-time API integrations.</p>
                    </div>
                    <div class="bg-white/50 backdrop-blur-sm p-8 rounded-[2.5rem] border border-white shadow-sm hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="font-black text-gray-900 mb-2">Verified Profiles</h3>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed">All data is fetched directly from platform APIs ensuring maximum accuracy.</p>
                    </div>
                </div>

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
    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate-fade-in-up">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
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
                                    <input type="number" id="min_followers_input" oninput="highlightFollowerTier()" placeholder="0" class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green">
                                </div>
                                <span class="text-gray-300">-</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">Max</span>
                                    <input type="number" id="max_followers_input" oninput="highlightFollowerTier()" placeholder="Any" class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green">
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-900">Location</label>
                            <div class="relative">
                                <i class="fas fa-map-marker-alt absolute left-3.5 top-3 text-gray-400"></i>
                                <input type="text" id="location_input" oninput="highlightLocationPill()" placeholder="Search locations (e.g., United States, London, Philippines)" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green">
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
                            <input type="range" id="engagement_slider" min="0" max="10" step="0.5" value="0" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand-green">
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
                                <input type="hidden" id="has_email_input" value="">
                            </div>

                            <!-- Influencer Gender -->
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-900">Influencer Gender</label>
                                <div class="flex p-1 bg-gray-100 rounded-lg">
                                    <button type="button" onclick="setGender('any')" id="gender_any" class="flex-1 py-2 text-xs font-medium rounded-md shadow-sm bg-white text-gray-900 transition-all">Any</button>
                                    <button type="button" onclick="setGender('male')" id="gender_male" class="flex-1 py-2 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 transition-all">Male</button>
                                    <button type="button" onclick="setGender('female')" id="gender_female" class="flex-1 py-2 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 transition-all">Female</button>
                                </div>
                                <input type="hidden" id="gender_input" value="any">
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
                    engagementDisplay.textContent = this.value + '%+';
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
            syncFormHiddenFilters();
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
                    engagementDisplay.textContent = (prev.min_engagement && parseFloat(prev.min_engagement) > 0) ? (prev.min_engagement + '%+') : 'Any';
                }
                setHasEmail(prev.has_email !== undefined ? prev.has_email : '');
                setGender(prev.gender || 'any');
                highlightFollowerTier();
                highlightLocationPill();
                syncFormHiddenFilters();
            } catch (e) {}
        }

        function syncFormHiddenFilters() {
            const minFollowers = document.getElementById('min_followers_input').value;
            const maxFollowers = document.getElementById('max_followers_input').value;
            const location = document.getElementById('location_input').value;
            const engagement = document.getElementById('engagement_slider').value;
            const hasEmail = document.getElementById('has_email_input').value;
            const gender = document.getElementById('gender_input').value;
            const form = document.querySelector('form[action*="discovery/search"]');
            if (!form) return;

            addHiddenInput(form, 'min_followers', minFollowers);
            addHiddenInput(form, 'max_followers', maxFollowers);
            addHiddenInput(form, 'location', location);
            addHiddenInput(form, 'min_engagement', engagement);
            addHiddenInput(form, 'has_email', hasEmail);
            addHiddenInput(form, 'gender', gender);

            let activeCount = 0;
            if (minFollowers !== '' || maxFollowers !== '') activeCount++;
            if (location.trim() !== '') activeCount++;
            if (parseFloat(engagement) > 0) activeCount++;
            if (hasEmail !== '') activeCount++;
            if (gender !== '' && gender !== 'any') activeCount++;

            const filterLabel = document.getElementById('main-filter-label');
            const filterBtn = document.getElementById('main-filter-btn');
            if (filterLabel) {
                filterLabel.textContent = activeCount > 0 ? `Filters (${activeCount})` : 'Filters';
            }
            if (filterBtn) {
                if (activeCount > 0) {
                    filterBtn.classList.add('border-brand-green', 'bg-green-50', 'text-brand-green');
                } else {
                    filterBtn.classList.remove('border-brand-green', 'bg-green-50', 'text-brand-green');
                }
            }
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
            syncFormHiddenFilters();
            addHiddenInput(form, 'exclude_saved', excludeSaved ? 'true' : 'false');
            
            toggleFilterModal();
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

        // Initialize filters from PHP params
        document.addEventListener('DOMContentLoaded', () => {
            <?php if(isset($search_params['min_followers'])): ?>
                document.getElementById('min_followers_input').value = '<?php echo $search_params['min_followers']; ?>';
            <?php endif; ?>
            <?php if(isset($search_params['max_followers'])): ?>
                document.getElementById('max_followers_input').value = '<?php echo $search_params['max_followers']; ?>';
            <?php endif; ?>
            <?php if(isset($search_params['location'])): ?>
                document.getElementById('location_input').value = '<?php echo $search_params['location']; ?>';
            <?php endif; ?>
            <?php if(isset($search_params['min_engagement'])): ?>
                const engVal = '<?php echo $search_params['min_engagement']; ?>';
                if(engagementSlider) {
                    engagementSlider.value = engVal;
                    if(engVal > 0) document.getElementById('engagement_display').textContent = engVal + '%+';
                }
            <?php endif; ?>
            <?php if(isset($search_params['has_email'])): ?>
                setHasEmail('<?php echo $search_params['has_email']; ?>');
            <?php endif; ?>
            <?php if(isset($search_params['gender'])): ?>
                setGender('<?php echo $search_params['gender']; ?>');
            <?php endif; ?>
        });
    </script>
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
    <script>
        function showPageLoading() {
            document.getElementById('page-loading').classList.remove('hidden');
        }

        function cancelSearch() {
            if (window.stop) {
                window.stop();
            } else if (document.execCommand) {
                document.execCommand('Stop');
            }
            const el = document.getElementById('page-loading');
            if (el) {
                el.classList.add('hidden');
            }
        }

        function setSearchKeyword(keyword) {
            const input = document.querySelector('input[name="keyword"]');
            if (input) {
                input.value = keyword;
                showPageLoading();
                input.form.submit();
            }
        }

        function updateSearchUI(platform) {
            const icon = document.getElementById('searchPlatformIcon');
            // Reset classes
            icon.className = '';
            
            if (platform === 'youtube') {
                icon.className = 'fab fa-youtube text-red-600';
            } else if (platform === 'tiktok') {
                icon.className = 'fab fa-tiktok text-black';
            } else {
                icon.className = 'fab fa-instagram text-pink-600';
            }
        }
    </script>


</body>
</html>
