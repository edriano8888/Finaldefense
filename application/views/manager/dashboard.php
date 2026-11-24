<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Panel - Instroom</title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo_icon.png'); ?>">
    <link rel="shortcut icon" href="<?php echo base_url('favicon.ico'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            dark: '#111827'
                        }
                    }
                }
            }
        }
    </script>
    <link href="<?php echo base_url('assets/css/manager.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden text-gray-800 font-sans">
    <?php $section = isset($active_section) ? $active_section : 'dashboard'; ?>
    <?php
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
        $available_brands = $this->Brand_model->get_all_brands();
    ?>
    <aside id="sidebar" class="w-64 bg-[#0F172A] border-r border-white/[0.06] flex-shrink-0 flex flex-col transition-all duration-300 hidden md:flex">
        <!-- Modern Manager Brand Header -->
        <div class="h-20 flex items-center px-6 border-b border-white/[0.08] bg-gradient-to-r from-emerald-950/30 via-transparent to-transparent">
            <div class="flex items-center gap-3.5">
                <!-- Glowing Logo Icon Badge -->
                <div class="relative group cursor-pointer">
                    <div class="absolute -inset-1.5 bg-gradient-to-r from-emerald-500 via-[#1FAE5B] to-teal-400 rounded-2xl blur-xs opacity-50 group-hover:opacity-85 transition duration-300"></div>
                    <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-br from-[#1E293B] to-[#0F172A] border border-white/15 flex items-center justify-center p-2 shadow-2xl shadow-black/80">
                        <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom Logo" class="w-full h-full object-contain filter drop-shadow">
                    </div>
                    <!-- Mini Manager Operations Badge -->
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-gradient-to-tr from-[#0F6B3E] to-[#1FAE5B] border-2 border-[#0F172A] flex items-center justify-center text-[7px] text-white shadow-xs" title="Manager Operations">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>

                <!-- Modern Typography & Status -->
                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-[0.95rem] font-black tracking-wider text-white font-sans uppercase">INSTROOM</span>
                        <span class="text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">MANAGER</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-gray-400 tracking-wider">Operations Control</span>
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a href="<?php echo base_url('manager'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'dashboard' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-chart-line w-6"></i>
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="<?php echo base_url('manager/campaign_planning'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'campaign_planning' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-bullseye w-6"></i>
                <span class="ml-2">Campaign Planning</span>
            </a>
            <a href="<?php echo base_url('manager/brands'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'brands' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-building w-6"></i>
                <span class="ml-2">Manage Brands</span>
            </a>
            <a href="<?php echo base_url('manager/team_management'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'team_management' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-user-friends w-6"></i>
                <span class="ml-2">Team Management</span>
            </a>
            <a href="<?php echo base_url('manager/influencer_review'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'influencer_review' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-user-check w-6"></i>
                <span class="ml-2">Influencer Review</span>
            </a>
            <a href="<?php echo base_url('manager/orders_discounts'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'orders_discounts' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-receipt w-6"></i>
                <span class="ml-2">Orders & Discounts</span>
            </a>
            <a href="<?php echo base_url('manager/performance'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'performance' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-chart-bar w-6"></i>
                <span class="ml-2">Performance</span>
            </a>
            <a href="<?php echo base_url('manager/reports'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $section === 'reports' ? 'bg-white/10 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'; ?>">
                <i class="fas fa-file-alt w-6"></i>
                <span class="ml-2">Reports</span>
            </a>
            <div class="border-t border-white/10 my-4"></div>
            <a href="<?php echo base_url('influencer'); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-arrow-left w-6"></i>
                <span class="ml-2">Back to App</span>
            </a>
        </nav>
    </aside>
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation -->
        <?php 
            $dynamic_child = isset($page_title) ? $page_title : (isset($child_title) ? $child_title : (isset($active_section) ? ucwords(str_replace('_', ' ', $active_section)) : 'Dashboard Overview'));
            $this->load->view('partials/header', ['parent' => 'Management', 'child' => $dynamic_child]); 
        ?>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 sm:p-6 md:p-8">
            <?php if (isset($content_view)): ?>
                <?php $this->load->view($content_view); ?>
            <?php endif; ?>
        </main>
    </div>
    <script>
        if (typeof window.toggleSidebar !== 'function') {
            window.toggleSidebar = function() {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar) return;
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('absolute');
                sidebar.classList.toggle('z-40');
                sidebar.classList.toggle('h-full');
            };
        }
    </script>
    <?php // Unified header scripts are loaded from partials/header.php ?>
    <?php $this->load->view('partials/toast'); ?>
</body>
</html>

