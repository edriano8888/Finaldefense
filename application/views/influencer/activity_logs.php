<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Activity Logs - Instroom</title>
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
                        }
                    }
                }
            }
        }
    </script>
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
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-20 sticky top-0">
            <div class="hidden md:flex items-center text-gray-500 text-sm font-medium">
                <a href="<?php echo base_url('influencer/dashboard'); ?>" class="text-gray-400 hover:text-gray-600 transition-colors">Dashboard</a>
                <i class="fas fa-chevron-right text-xs mx-3 text-gray-300"></i>
                <span class="text-brand-deep bg-green-50 px-2 py-1 rounded-md">Activity Logs</span>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative" id="user-menu-container">
                    <button onclick="toggleUserMenu()" class="flex items-center gap-3 cursor-pointer group focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-brand-deep transition-colors"><?php echo isset($user_name) ? $user_name : 'User'; ?></p>
                            <p class="text-xs text-gray-500"><?php echo isset($user_role) ? ucfirst($user_role) : 'User'; ?></p>
                        </div>
                        <div class="relative">
                             <?php $nav_fallback = "https://ui-avatars.com/api/?name=" . urlencode(isset($user_name) ? $user_name : 'User') . "&background=1FAE5B&color=fff&rounded=true&bold=true"; ?>
                             <?php $nav_avatar = !empty($user_profile_image) ? base_url($user_profile_image) : $nav_fallback; ?>
                             <img src="<?php echo html_escape($nav_avatar); ?>" alt="User Avatar" class="w-9 h-9 rounded-full shadow-sm border-2 border-white object-cover">
                        </div>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 md:p-8 scroll-smooth relative">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-green-50/50 to-transparent -z-10"></div>
            
            <div class="max-w-7xl mx-auto relative">
                <div class="mb-8">
                    <h1 class="text-2xl font-extrabold text-brand-deep tracking-tight">System Activity Logs</h1>
                    <p class="text-gray-500 mt-1">Detailed history of actions performed in the system.</p>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-8 py-4 text-xs font-extrabold text-gray-400 uppercase tracking-widest">Action</th>
                                    <th class="px-6 py-4 text-xs font-extrabold text-gray-400 uppercase tracking-widest">Details</th>
                                    <th class="px-6 py-4 text-xs font-extrabold text-gray-400 uppercase tracking-widest">User</th>
                                    <th class="px-6 py-4 text-xs font-extrabold text-gray-400 uppercase tracking-widest">IP Address</th>
                                    <th class="px-6 py-4 text-xs font-extrabold text-gray-400 uppercase tracking-widest text-right">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): 
                                        $l_action = is_array($log) ? ($log['action'] ?? '') : ($log->action ?? '');
                                        $l_details = is_array($log) ? ($log['details'] ?? '') : ($log->details ?? '');
                                        $l_user = is_array($log) ? ($log['user'] ?? $log['user_name'] ?? '') : ($log->user ?? $log->user_name ?? '');
                                        $l_ip = is_array($log) ? ($log['ip'] ?? $log['ip_address'] ?? '') : ($log->ip ?? $log->ip_address ?? '');
                                        $l_time = is_array($log) ? ($log['time'] ?? '') : ($log->time ?? '');
                                        $l_date = is_array($log) ? ($log['date'] ?? '') : ($log->date ?? '');
                                    ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-8 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-brand-green">
                                                        <i class="fas fa-check-circle text-sm"></i>
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-900"><?php echo html_escape($l_action); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="text-sm text-gray-600 max-w-md truncate" title="<?php echo html_escape($l_details); ?>">
                                                    <?php echo html_escape($l_details); ?>
                                                </p>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-sm font-medium text-gray-700"><?php echo html_escape($l_user); ?></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <code class="text-xs font-mono text-gray-400 bg-gray-50 px-2 py-1 rounded"><?php echo html_escape($l_ip); ?></code>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex flex-col items-end">
                                                    <span class="text-sm font-bold text-gray-900"><?php echo html_escape($l_time); ?></span>
                                                    <span class="text-[10px] text-gray-400 font-medium"><?php echo html_escape($l_date); ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-8 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-history text-gray-300 text-2xl"></i>
                                                </div>
                                                <p class="text-gray-500 font-medium">No activity logs found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($pagination)): ?>
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100 flex items-center justify-between">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Total Logs: <?php echo number_format($total_rows); ?>
                            </p>
                            <?php echo $pagination; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            if (menu) menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
