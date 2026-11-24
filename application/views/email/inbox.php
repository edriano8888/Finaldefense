<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                            dark: '#0B3B22',
                            light: '#ECFDF5',
                            accent: '#10B981',
                            gray: '#F8FAFC',
                            border: '#E2E8F0',
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(15, 107, 62, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02)',
                        'card-hover': '0 12px 28px -4px rgba(15, 107, 62, 0.12), 0 6px 12px -2px rgba(0, 0, 0, 0.04)',
                        'glow': '0 0 20px rgba(31, 174, 91, 0.25)',
                    }
                }
            }
        }

        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            if (menu) menu.classList.toggle('hidden');
        }

        function toggleBrandMenu() {
            const menu = document.getElementById('brand-menu');
            if (menu) menu.classList.toggle('hidden');
        }
    </script>
    <link href="<?php echo base_url('assets/css/influencer.css'); ?>" rel="stylesheet">
</head>
<body class="bg-[#F8FAFC] h-screen flex overflow-hidden text-gray-800 font-sans">
<?php
    $CI =& get_instance();
    if (!isset($CI->Brand_model)) {
        $CI->load->model('Brand_model');
    }
    $active_brand_id = (int)$this->session->userdata('brand_id');
    $active_brand = ($active_brand_id && isset($CI->Brand_model)) ? $CI->Brand_model->get_brand($active_brand_id) : null;
    $active_brand_name = $active_brand ? $active_brand->name : 'Brand';
    $available_brands = isset($CI->Brand_model) ? $CI->Brand_model->get_all_brands() : [];
?>

    <!-- Sidebar -->
    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-[#F8FAFC]">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-20 sticky top-0 shadow-xs">
            <div class="flex items-center gap-3">
                <button class="md:hidden text-gray-500 hover:text-gray-700 p-2" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <i class="fas fa-inbox text-brand-green text-lg animate-pulse"></i>
                    Inbox Workspace
                </h1>
                <?php if ($unread_count > 0): ?>
                    <span id="unread-count-badge" class="bg-emerald-100 text-brand-deep text-xs font-extrabold px-3 py-1 rounded-full border border-emerald-200 shadow-xs"><?= $unread_count ?> Unread</span>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3">
                <?php if ($unread_count > 0): ?>
                    <button type="button" onclick="markAllRead(event)" id="mark-all-read-btn" class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-xl transition-all shadow-xs cursor-pointer" title="Mark all messages as read and clear sidebar notification">
                        <i class="fas fa-check-double text-brand-green"></i>
                        <span>Mark All Read</span>
                    </button>
                <?php endif; ?>
                <button onclick="syncInbox()" id="sync-btn" class="inline-flex items-center gap-2 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 px-3.5 py-2.5 rounded-xl transition-all shadow-xs" title="Sync inbox from Gmail">
                    <i class="fas fa-sync text-brand-green" id="sync-icon"></i>
                    Sync Inbox
                </button>
                <a href="<?= base_url('email/drips') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 px-3.5 py-2 rounded-xl transition-all shadow-xs">
                    <i class="fas fa-paper-plane text-brand-green"></i>
                    Email Drips
                </a>
                <a href="<?= base_url('email/compose') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] hover:opacity-95 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md">
                    <i class="fas fa-plus text-[10px]"></i>
                    New Email
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Left Sub-Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 flex flex-col p-5 space-y-6">
                <!-- Connected Email Account Card -->
                <div>
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Connection Status</p>
                    <?php if (empty($accounts)): ?>
                        <div class="p-4 bg-emerald-50/50 border border-emerald-200 rounded-2xl text-center">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-2.5">
                                <i class="fas fa-envelope-open text-brand-green"></i>
                            </div>
                            <p class="text-xs text-gray-700 font-bold mb-1">No email connected</p>
                            <p class="text-[10px] text-gray-500 mb-3">Connect your Gmail to begin outreach</p>
                            <a href="<?= base_url('email/connect_gmail') ?>" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold py-2.5 rounded-xl shadow-xs transition-all">
                                <i class="fab fa-google text-red-500"></i> Connect Gmail
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($accounts as $account): ?>
                                <div class="flex items-center gap-3 p-3 rounded-xl border <?= $active_account && $active_account->id === $account->id ? 'bg-emerald-50/80 border-emerald-200 shadow-xs' : 'bg-gray-50/50 border-gray-200' ?>">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#0F6B3E] to-[#1FAE5B] text-white flex items-center justify-center text-xs font-black shadow-xs">
                                        <?= strtoupper(substr($account->email_address[0] ?? 'E', 0, 1)) ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate"><?= html_escape($account->display_name ?: $account->email_address) ?></p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                            <span class="text-[9px] text-emerald-600 font-bold capitalize">Connected • <?= html_escape($account->provider) ?></span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="confirmDisconnectAccount(<?= $account->id ?>, '<?= html_escape($account->email_address) ?>')" 
                                            class="text-gray-400 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition-all text-xs flex-shrink-0" 
                                            title="Disconnect Account">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?= base_url('email/connect_gmail') ?>" class="mt-3.5 w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-[10px] font-bold py-2 rounded-xl transition-all shadow-xs">
                            <i class="fab fa-google text-red-500"></i> + Add Another Account
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Folder Navigation -->
                <nav class="space-y-1">
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Folders</p>
                    <a href="<?= base_url('email') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-emerald-50/80 text-brand-deep font-bold text-xs border border-emerald-100 shadow-xs">
                        <span class="flex items-center gap-2.5"><i class="fas fa-inbox text-brand-green text-sm"></i> Inbox</span>
                        <?php if ($unread_count > 0): ?>
                            <span class="bg-brand-green text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('email/drips') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 font-semibold text-xs transition-all">
                        <span class="flex items-center gap-2.5"><i class="fas fa-bolt text-amber-500 text-sm"></i> Drip Campaigns</span>
                    </a>
                </nav>

                <!-- Quick Templates -->
                <div class="flex-1 flex flex-col min-h-0">
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Quick Templates</p>
                    <div class="space-y-1 overflow-y-auto pr-1 flex-1">
                        <?php if (!empty($templates)): ?>
                            <?php foreach (array_slice($templates, 0, 6) as $template): ?>
                                <a href="<?= base_url('email/compose?template_id=' . $template->id) ?>" class="group flex items-center gap-2 text-xs font-semibold text-gray-600 hover:text-brand-deep p-2.5 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all">
                                    <i class="far fa-file-alt text-gray-400 group-hover:text-brand-green transition-colors text-sm"></i>
                                    <span class="truncate"><?= htmlspecialchars($template->name) ?></span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-[10px] text-gray-400 italic">No templates created yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>

            <!-- Thread List Main Area -->
            <main class="flex-1 bg-white overflow-y-auto flex flex-col">


                <!-- Filters and Search Controls -->
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <!-- Filter Tabs -->
                        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl w-fit">
                            <button onclick="filterThreads('all')" id="filter-all" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-gray-800 shadow-xs">
                                All
                            </button>
                            <button onclick="filterThreads('unread')" id="filter-unread" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-600 hover:text-gray-800">
                                Unread
                            </button>
                            <button onclick="filterThreads('starred')" id="filter-starred" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-600 hover:text-gray-800">
                                Starred
                            </button>
                        </div>

                        <!-- Local Live Search Box -->
                        <div class="relative w-full sm:max-w-xs">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </span>
                            <input type="text" id="thread-search" class="w-full bg-white border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl pl-9 pr-4 py-2 text-xs font-medium transition-all outline-none" placeholder="Search sender, subject, or email...">
                        </div>
                    </div>
                </div>

                <!-- Inbox Threads List -->
                <?php if (empty($threads)): ?>
                    <div class="flex flex-col items-center justify-center flex-1 p-8 text-center bg-gray-50/20">
                        <div class="w-20 h-20 rounded-3xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-brand-green text-3xl mb-4 shadow-sm">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Your Inbox is Empty</h3>
                        <?php if (empty($accounts)): ?>
                            <p class="text-xs text-gray-500 max-w-sm mb-6 leading-relaxed">No email threads found. Connect your Gmail account to begin managing your outreach.</p>
                            <div class="flex items-center justify-center">
                                <a href="<?= base_url('email/connect_gmail') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] text-white text-xs font-bold px-5 py-3 rounded-xl shadow-md hover:opacity-95 transition-all">
                                    <i class="fab fa-google"></i> Connect Gmail Account
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-xs text-gray-500 max-w-md mb-6 leading-relaxed">Account <strong><?= html_escape($active_account->email_address ?? 'Gmail') ?></strong> is connected, but no emails have been synced yet. Click below to sync your inbox from Gmail.</p>
                            <div class="flex items-center justify-center">
                                <button onclick="syncInbox()" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] text-white text-xs font-bold px-5 py-3 rounded-xl shadow-md hover:opacity-95 transition-all">
                                    <i class="fas fa-sync"></i> Sync Inbox Now
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-100" id="threads-container">
                        <?php foreach ($threads as $thread): 
                            $participants = json_decode($thread->participants, true);
                            $first_participant = is_array($participants) ? reset($participants) : 'Creator';
                        ?>
                            <a href="<?= base_url('email/thread/' . $thread->thread_id) ?>" 
                               class="block hover:bg-emerald-50/20 transition-all group thread-row"
                               data-sender="<?= html_escape($first_participant) ?>"
                               data-email="<?= html_escape($first_participant) ?>"
                               data-subject="<?= html_escape($thread->subject ?: '(No Subject)') ?>"
                               data-preview="<?= html_escape($thread->last_preview ?? '') ?>"
                               data-is-read="<?= $thread->is_read ? '1' : '0' ?>"
                               data-is-starred="<?= $thread->is_starred ? '1' : '0' ?>">
                                <div class="p-5 flex items-start gap-4 transition-colors <?= !$thread->is_read ? 'bg-emerald-50/10 font-medium' : '' ?>">
                                    <!-- Star & Avatar -->
                                    <div class="flex items-center gap-3.5 pt-0.5 flex-shrink-0">
                                        <button type="button" class="text-gray-300 hover:text-amber-400 transition-colors">
                                            <i class="<?= $thread->is_starred ? 'fas fa-star text-amber-400' : 'far fa-star' ?> text-sm"></i>
                                        </button>
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0F6B3E] to-[#1FAE5B] text-white font-extrabold text-sm flex items-center justify-center shadow-xs">
                                            <?= strtoupper(substr($first_participant, 0, 1)) ?>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="font-bold text-sm text-gray-900 group-hover:text-brand-deep transition-colors truncate"><?= htmlspecialchars($first_participant) ?></span>
                                                <?php if (!empty($thread->message_count) && (int)$thread->message_count > 1): ?>
                                                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full flex items-center gap-1 shrink-0" title="<?= (int)$thread->message_count ?> messages in conversation">
                                                        <i class="fas fa-comments text-[9px]"></i> <?= (int)$thread->message_count ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($thread->pipeline_stage)): ?>
                                                    <?php 
                                                        $stage_val = trim($thread->pipeline_stage);
                                                        $badge_cls = 'bg-gray-50 text-gray-700 border-gray-200';
                                                        if (stripos($stage_val, 'replied') !== false) {
                                                            $badge_cls = 'bg-blue-50 text-blue-700 border-blue-200';
                                                        } elseif (stripos($stage_val, 'contacted') !== false) {
                                                            $badge_cls = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                                        } elseif (stripos($stage_val, 'progress') !== false) {
                                                            $badge_cls = 'bg-purple-50 text-purple-700 border-purple-200';
                                                        } elseif (stripos($stage_val, 'closed') !== false) {
                                                            $badge_cls = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                                        }
                                                    ?>
                                                    <span class="text-[9px] font-black tracking-wide uppercase px-2 py-0.5 rounded border flex-shrink-0 <?= $badge_cls ?>"><?= html_escape($stage_val) ?></span>
                                                <?php endif; ?>
                                                <?php if (!$thread->is_read): ?>
                                                    <span class="w-2 h-2 bg-brand-green rounded-full flex-shrink-0 animate-pulse unread-dot"></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <span class="text-[10px] font-bold text-gray-400"><?= date('M j, g:i a', strtotime($thread->last_message_at ?: date('Y-m-d H:i:s'))) ?></span>
                                                <button type="button" 
                                                        onclick="deleteThreadRow(event, '<?= $thread->thread_id ?>')" 
                                                        class="opacity-80 group-hover:opacity-100 text-gray-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all text-xs" 
                                                        title="Delete email thread from system">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <h4 class="text-xs font-bold text-gray-800 truncate mb-1"><?= htmlspecialchars($thread->subject ?: '(No Subject)') ?></h4>
                                        <p class="text-xs text-gray-500 truncate leading-relaxed max-w-2xl">
                                            <?php if (isset($thread->last_direction) && $thread->last_direction === 'outgoing'): ?>
                                                <span class="font-bold text-emerald-700 mr-1">You:</span>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($thread->last_preview ?? '') ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Live Search & Filtering JavaScript -->
    <script>
        function filterThreads(type) {
            // Update filter tab UI active states
            const buttons = ['all', 'unread', 'starred'];
            buttons.forEach(b => {
                const btn = document.getElementById('filter-' + b);
                if (!btn) return;
                if (b === type) {
                    btn.className = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-gray-800 shadow-xs";
                } else {
                    btn.className = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-600 hover:text-gray-800";
                }
            });
            
            const query = document.getElementById('thread-search').value.toLowerCase().trim();
            const rows = document.querySelectorAll('.thread-row');
            
            rows.forEach(row => {
                const sender = row.getAttribute('data-sender').toLowerCase();
                const email = row.getAttribute('data-email').toLowerCase();
                const subject = row.getAttribute('data-subject').toLowerCase();
                const preview = row.getAttribute('data-preview').toLowerCase();
                const isRead = row.getAttribute('data-is-read') === '1';
                const isStarred = row.getAttribute('data-is-starred') === '1';
                
                let matchesQuery = !query || sender.includes(query) || email.includes(query) || subject.includes(query) || preview.includes(query);
                let matchesType = true;
                
                if (type === 'unread') {
                    matchesType = !isRead;
                } else if (type === 'starred') {
                    matchesType = isStarred;
                }
                
                if (matchesQuery && matchesType) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        document.getElementById('thread-search').addEventListener('input', function() {
            const activeBtn = document.querySelector('[id^="filter-"].bg-white');
            const activeType = activeBtn ? activeBtn.id.replace('filter-', '') : 'all';
            filterThreads(activeType);
        });

        async function syncInbox() {
            const btn = document.getElementById('sync-btn');
            const icon = document.getElementById('sync-icon');
            if (icon) icon.classList.add('fa-spin');
            if (btn) btn.disabled = true;

            showToast('Syncing Inbox...', 'Checking your connected Gmail account for latest emails and replies.', 'info', 2000);

            try {
                const response = await fetch('<?= base_url('email/sync_inbox') ?>');
                const result = await response.json();
                if (result.status === 'success') {
                    if (result.synced_count > 0) {
                        showToast('Inbox Updated!', `Synced ${result.synced_count} new email(s). Refreshing...`, 'success', 1500);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showToast('Inbox Up to Date', 'Your inbox is already up to date. No new emails found.', 'success', 3500);
                    }
                } else {
                    if (result.reconnect_url || (result.message && result.message.toLowerCase().includes('reconnect'))) {
                        showToast('Reconnection Needed', 'Gmail authorization expired. Click here to reconnect.', 'error', 7000, result.reconnect_url || '<?= base_url('email/connect_gmail') ?>');
                    } else {
                        showToast('Sync Notice', result.message || 'Unable to sync inbox at this moment.', 'error', 4500);
                    }
                }
            } catch (e) {
                console.error(e);
                showToast('Sync Error', 'Network or connection issue while syncing.', 'error', 4000);
            } finally {
                if (icon) icon.classList.remove('fa-spin');
                if (btn) btn.disabled = false;
            }
        }

        // Auto background sync on page load
        window.addEventListener('DOMContentLoaded', () => {
            fetch('<?= base_url('email/sync_inbox') ?>')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.synced_count > 0) {
                        window.location.reload();
                    }
                })
                .catch(e => console.error("Auto-sync error:", e));
        });

        async function confirmDisconnectAccount(accountId, email) {
            if (!confirm(`Are you sure you want to disconnect ${email} from Instroom?\nThis will remove the connected Gmail account.`)) {
                return;
            }
            try {
                const response = await fetch('<?= base_url('email/disconnect_account/') ?>' + accountId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                if (result.status === 'success') {
                    window.location.reload();
                } else {
                    alert(result.message || 'Failed to disconnect account');
                }
            } catch (e) {
                window.location.href = '<?= base_url('email/disconnect_account/') ?>' + accountId;
            }
        }

        async function deleteThreadRow(event, threadId) {
            event.preventDefault();
            event.stopPropagation();
            if (!confirm('Are you sure you want to delete this email thread from the system? (This deletes it locally only, not from Gmail)')) {
                return;
            }
            try {
                const response = await fetch('<?= base_url('email/delete_thread/') ?>' + threadId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                if (result.status === 'success') {
                    const row = event.target.closest('.thread-row');
                    if (row) {
                        row.style.transition = 'opacity 0.3s, transform 0.3s';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 300);
                    } else {
                        window.location.reload();
                    }
                } else {
                    showToast('Action Failed', result.message || 'Failed to delete thread', 'error');
                }
            } catch (e) {
                window.location.href = '<?= base_url('email/delete_thread/') ?>' + threadId;
            }
        }

        // Mark All Read via AJAX with Modern Floating Toast
        async function markAllRead(event) {
            if (event) event.preventDefault();
            const btn = document.getElementById('mark-all-read-btn');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'pointer-events-none');
            }

            try {
                const response = await fetch('<?= base_url('email/mark_all_read') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();
                if (result && result.success) {
                    if (window.showToast) {
                        window.showToast('All Messages Read', 'All inbox messages marked as read.', 'success', 3500);
                    }

                    // Remove unread dots
                    document.querySelectorAll('.unread-dot').forEach(el => el.remove());

                    // Reset rows unread styling
                    document.querySelectorAll('.thread-row').forEach(row => {
                        row.dataset.isRead = '1';
                        const inner = row.querySelector('.bg-emerald-50\\/10');
                        if (inner) {
                            inner.classList.remove('bg-emerald-50/10', 'font-medium');
                        }
                    });

                    // Remove unread badge counters
                    const unreadBadge = document.getElementById('unread-count-badge');
                    if (unreadBadge) unreadBadge.remove();

                    document.querySelectorAll('.badge-unread').forEach(b => b.remove());

                    // Smoothly animate out Mark All Read button
                    if (btn) {
                        btn.classList.add('transition-all', 'duration-300', 'opacity-0', 'scale-95');
                        setTimeout(() => btn.remove(), 300);
                    }
                } else {
                    if (window.showToast) {
                        window.showToast('Notice', 'Unable to mark all as read right now.', 'error', 3500);
                    }
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'pointer-events-none');
                    }
                }
            } catch (err) {
                // Graceful fallback to GET redirect
                window.location.href = '<?= base_url('email/mark_all_read') ?>';
            }
        }
    </script>
</body>
</html>
