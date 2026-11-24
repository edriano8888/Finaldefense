<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($thread->subject ?: 'Email Conversation') ?> - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
                        'bubble': '0 2px 12px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02)',
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
    <style>
        /* Custom scrollbar for message stream */
        .chat-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scroll::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        .chat-scroll::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        .chat-scroll::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        .quoted-text-block {
            display: none;
        }
        .quoted-text-block.open {
            display: block;
        }
    </style>
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

    // Load user accounts for the connection view
    $accounts = $CI->Email_model->get_user_accounts($this->session->userdata('user_id'));

    // Dynamic participants calculation
    $participants = json_decode($thread->participants, true);
    $first_participant = is_array($participants) ? reset($participants) : 'Creator';

    $influencer_name = 'there';
    $influencer_full_name = '';
    $influencer_platform = 'Instagram';

    if (isset($influencer) && !empty($influencer)) {
        if (!empty($influencer->first_name)) {
            $influencer_name = trim($influencer->first_name);
        } elseif (!empty($influencer->full_name)) {
            $parts = explode(' ', trim($influencer->full_name));
            $influencer_name = !empty($parts[0]) ? $parts[0] : trim($influencer->full_name);
        } elseif (!empty($influencer->username)) {
            $influencer_name = trim($influencer->username);
        }
        $influencer_full_name = !empty($influencer->full_name) ? trim($influencer->full_name) : $influencer_name;
        if (!empty($influencer->platform)) {
            $influencer_platform = $influencer->platform;
        }
    }

    if (($influencer_name === 'there' || empty($influencer_name)) && !empty($first_participant) && strpos($first_participant, '@') !== false) {
        $found_prof = $CI->db->select('p.full_name, p.first_name, a.username, a.platform')
            ->from('influencer_profiles p')
            ->join('influencer_accounts a', 'a.profile_id = p.id', 'left')
            ->where('p.email', $first_participant)
            ->get()->row();
        if ($found_prof) {
            if (!empty($found_prof->first_name)) {
                $influencer_name = trim($found_prof->first_name);
            } elseif (!empty($found_prof->full_name)) {
                $parts = explode(' ', trim($found_prof->full_name));
                $influencer_name = !empty($parts[0]) ? $parts[0] : trim($found_prof->full_name);
            } elseif (!empty($found_prof->username)) {
                $influencer_name = trim($found_prof->username);
            }
            $influencer_full_name = !empty($found_prof->full_name) ? trim($found_prof->full_name) : $influencer_name;
            if (!empty($found_prof->platform)) {
                $influencer_platform = $found_prof->platform;
            }
        }
    }

    if (empty($influencer_name)) $influencer_name = 'there';
    if (empty($influencer_full_name)) $influencer_full_name = $influencer_name;
    $first_name = $influencer_name;

    $raw_sender = $this->session->userdata('name') ?: ($this->session->userdata('user_name') ?: '');
    if (empty($raw_sender) && !empty($account) && !empty($account->display_name) && strpos($account->display_name, '@') === false) {
        $raw_sender = $account->display_name;
    }
    if (empty($raw_sender)) {
        $raw_sender = 'Edrian Benedict';
    }

    $sender_name = $raw_sender;
    if (strpos($raw_sender, ',') !== false) {
        $p = explode(',', $raw_sender, 2);
        $first_part = trim($p[1]);
        $clean_first = preg_replace('/\s+[A-Z]\.?$/', '', $first_part);
        $sender_name = ucwords(strtolower($clean_first ?: $first_part));
    } else {
        $sender_name = ucwords(strtolower($raw_sender));
    }

    $brand_name = (!empty($active_brand_name) && $active_brand_name !== 'Armful Media') ? $active_brand_name : 'JBL';
    $owner_name = $sender_name;
    $sender_title = 'Partnership Manager';
    $contact_email = !empty($account) && !empty($account->email_address) ? $account->email_address : 'contact@armfulmedia.com';
    $collaboration_details = 'exclusive gifted products and paid partnership collaboration';
    $total_messages = count($messages);
?>

    <!-- Sidebar -->
    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-[#F8FAFC]">
        
        <!-- Modern Header -->
        <header class="h-16 bg-white border-b border-gray-200/90 flex items-center justify-between px-6 z-20 sticky top-0 shadow-xs backdrop-blur-md bg-white/95">
            <div class="flex items-center gap-3.5 min-w-0">
                <a href="<?= base_url('email') ?>" class="text-gray-500 hover:text-brand-deep p-2 rounded-xl hover:bg-emerald-50 transition-all flex items-center justify-center shrink-0" title="Back to Inbox">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>

                <!-- Participant Avatar & Details Pill -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#0F6B3E] to-[#1FAE5B] text-white font-extrabold text-sm flex items-center justify-center shadow-xs shrink-0">
                        <?= strtoupper(substr($first_participant, 0, 1)) ?>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h1 class="text-sm font-extrabold text-gray-900 truncate">
                                <?= htmlspecialchars($first_participant) ?>
                            </h1>
                            <span class="inline-flex items-center gap-1 text-[10px] font-black bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-100 shrink-0">
                                <i class="fas fa-comments text-[9px] text-emerald-600"></i>
                                <?= $total_messages ?> <?= $total_messages === 1 ? 'message' : 'messages' ?>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 truncate font-medium">
                            <?= htmlspecialchars($thread->subject ?: '(No Subject)') ?>
                        </p>
                    </div>
                </div>

                <!-- Pipeline Status Badge -->
                <?php if (!empty($thread->pipeline_stage)): ?>
                    <?php 
                        $stage_val = trim($thread->pipeline_stage);
                        $badge_cls = 'bg-gray-50 text-gray-800 border-gray-200';
                        $dot_cls = 'bg-gray-500';
                        if (stripos($stage_val, 'replied') !== false) {
                            $badge_cls = 'bg-blue-50 text-blue-800 border-blue-200';
                            $dot_cls = 'bg-blue-500';
                        } elseif (stripos($stage_val, 'contacted') !== false) {
                            $badge_cls = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                            $dot_cls = 'bg-emerald-500';
                        } elseif (stripos($stage_val, 'progress') !== false) {
                            $badge_cls = 'bg-purple-50 text-purple-800 border-purple-200';
                            $dot_cls = 'bg-purple-500';
                        } elseif (stripos($stage_val, 'closed') !== false) {
                            $badge_cls = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                            $dot_cls = 'bg-emerald-600';
                        }
                    ?>
                    <span class="hidden md:inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full border shrink-0 <?= $badge_cls ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $dot_cls ?>"></span>
                        <?= html_escape($stage_val) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="syncCurrentThread()" class="text-gray-400 hover:text-brand-deep p-2 rounded-xl hover:bg-emerald-50 transition-colors" title="Sync latest replies">
                    <i class="fas fa-sync-alt text-sm" id="sync-icon"></i>
                </button>
                <button onclick="toggleStar()" class="star-btn text-gray-400 hover:text-yellow-500 p-2 rounded-xl hover:bg-yellow-50 transition-colors" title="Star conversation">
                    <i class="<?= $thread->is_starred ? 'fas fa-star text-amber-400' : 'far fa-star' ?> text-sm"></i>
                </button>
                <button onclick="deleteCurrentThread()" class="text-gray-400 hover:text-red-600 p-2 rounded-xl hover:bg-red-50 transition-colors" title="Delete conversation">
                    <i class="fas fa-trash-alt text-sm"></i>
                </button>
            </div>
        </header>

        <!-- Main Body -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Left Sub-Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200/90 flex flex-col p-5 space-y-6 hidden lg:flex">
                <!-- Connected Email Account Card -->
                <div>
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2.5">Connected Account</p>
                    <div class="flex items-center gap-3 p-3 rounded-2xl border bg-emerald-50/60 border-emerald-200/80 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#0F6B3E] to-[#1FAE5B] text-white flex items-center justify-center text-xs font-black shadow-xs shrink-0">
                            <?= strtoupper(substr($account->email_address[0] ?? 'E', 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate"><?= html_escape($account->display_name ?: $account->email_address) ?></p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[9px] text-emerald-700 font-bold capitalize"><?= html_escape($account->provider) ?> Connected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="space-y-1">
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2.5">Folders</p>
                    <a href="<?= base_url('email') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-emerald-50/60 hover:text-brand-deep font-bold text-xs transition-all">
                        <span class="flex items-center gap-2.5"><i class="fas fa-inbox text-gray-400 text-sm"></i> Inbox</span>
                        <span class="text-[10px] bg-gray-100 text-gray-600 font-black px-2 py-0.5 rounded-full">All</span>
                    </a>
                    <a href="<?= base_url('email/drips') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 font-semibold text-xs transition-all">
                        <span class="flex items-center gap-2.5"><i class="fas fa-bolt text-amber-500 text-sm"></i> Drip Sequences</span>
                    </a>
                </nav>

                <!-- Influencer Context Card (If linked) -->
                <?php if (isset($influencer)): ?>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5 space-y-2.5">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Creator Profile</p>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-brand-deep font-extrabold text-xs flex items-center justify-center shrink-0">
                                <?= strtoupper(substr($influencer->username ?? 'C', 0, 1)) ?>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 truncate">@<?= htmlspecialchars($influencer->username ?? 'creator') ?></p>
                                <p class="text-[10px] text-gray-500 capitalize"><?= htmlspecialchars($influencer->platform ?? 'Social') ?> Creator</p>
                            </div>
                        </div>
                        <?php if (!empty($influencer->followers_count)): ?>
                            <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/60 font-semibold text-gray-600">
                                <span>Followers:</span>
                                <span class="text-gray-900 font-extrabold"><?= number_format((int)$influencer->followers_count) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Quick Templates -->
                <div class="flex-1 flex flex-col min-h-0">
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2.5">Quick Reply Templates</p>
                    <div class="space-y-1.5 overflow-y-auto pr-1 flex-1">
                        <?php if (!empty($templates)): ?>
                            <?php foreach (array_slice($templates, 0, 5) as $t): ?>
                                <button type="button" onclick="insertTemplate(<?= $t->id ?>)" class="w-full text-left group flex items-center gap-2 text-xs font-semibold text-gray-600 hover:text-brand-deep p-2 rounded-xl hover:bg-emerald-50/50 border border-transparent hover:border-emerald-100 transition-all">
                                    <i class="far fa-file-alt text-gray-400 group-hover:text-brand-green transition-colors text-xs shrink-0"></i>
                                    <span class="truncate"><?= htmlspecialchars($t->name) ?></span>
                                </button>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-[10px] text-gray-400 italic">No templates available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>

            <!-- Conversation Main Stream & Composer -->
            <main class="flex-1 bg-[#F8FAFC] flex flex-col overflow-hidden relative">
                
                <!-- Messages Timeline Stream -->
                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 chat-scroll" id="messages-container">
                    
                    <!-- Date Divider -->
                    <div class="flex items-center justify-center my-2">
                        <span class="bg-gray-200/70 text-gray-600 text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full shadow-2xs">
                            Conversation History
                        </span>
                    </div>

                    <?php 
                    foreach ($messages as $index => $msg): 
                        $isOutgoing = ($msg->direction === 'outgoing');
                        $senderDisplay = $isOutgoing ? 'You' : ($msg->from_name ?: $first_participant);
                        $formattedDate = date('M j, Y • g:i A', strtotime($msg->sent_at ?: ($msg->received_at ?: date('Y-m-d H:i:s'))));

                        // Split quoted email history if present in plain text
                        $rawBody = $msg->body_plain ?: strip_tags($msg->body_html ?: '');
                        $hasQuote = false;
                        $mainText = $rawBody;
                        $quoteText = '';

                        if (preg_match('/^(.*?)(On\s+[A-Za-z]+,\s+[A-Za-z]+\s+\d+.*wrote:.*)$/s', $rawBody, $qMatches)) {
                            $hasQuote = true;
                            $mainText = trim($qMatches[1]);
                            $quoteText = trim($qMatches[2]);
                        }
                    ?>
                        <div class="flex flex-col <?= $isOutgoing ? 'items-end' : 'items-start' ?> group">
                            
                            <!-- Sender Meta & Avatar Row -->
                            <div class="flex items-center gap-2 mb-1.5 <?= $isOutgoing ? 'flex-row-reverse' : '' ?>">
                                <div class="w-7 h-7 rounded-lg <?= $isOutgoing ? 'bg-gradient-to-br from-[#0F6B3E] to-[#1FAE5B] text-white' : 'bg-slate-200 text-slate-700' ?> flex items-center justify-center text-xs font-black shadow-xs shrink-0">
                                    <?= $isOutgoing ? 'Y' : strtoupper(substr($senderDisplay, 0, 1)) ?>
                                </div>
                                <div class="flex items-center gap-2 <?= $isOutgoing ? 'flex-row-reverse' : '' ?>">
                                    <span class="text-xs font-black <?= $isOutgoing ? 'text-emerald-900' : 'text-slate-900' ?>">
                                        <?= htmlspecialchars($senderDisplay) ?>
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400">
                                        <?= $formattedDate ?>
                                    </span>
                                    <?php if ($isOutgoing): ?>
                                        <span class="inline-flex items-center gap-1 text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                            <i class="fas fa-check-double text-[8px] text-emerald-500"></i> Sent
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-[9px] font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                            <i class="fas fa-arrow-down text-[8px] text-blue-500"></i> Reply
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Modern Message Bubble Card -->
                            <div class="w-full max-w-3xl <?= $isOutgoing ? 'ml-auto' : 'mr-auto' ?>">
                                <div class="rounded-3xl p-5 shadow-bubble transition-all border <?= $isOutgoing ? 'bg-white border-emerald-200/90 text-gray-800 ring-1 ring-emerald-500/10' : 'bg-white border-slate-200/90 text-gray-800' ?>">
                                    
                                    <!-- Email Headers Sub-bar -->
                                    <div class="flex flex-wrap items-center justify-between pb-3 mb-3 border-b border-gray-100 text-[11px] text-gray-500 font-semibold gap-2">
                                        <div class="flex items-center gap-1.5 truncate">
                                            <span class="text-gray-400 uppercase text-[9px] font-extrabold tracking-wider">Subject:</span>
                                            <span class="text-gray-900 font-bold truncate"><?= htmlspecialchars($msg->subject ?: '(No Subject)') ?></span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 shrink-0">
                                            <?= $isOutgoing ? 'To: ' . htmlspecialchars($msg->to_addresses ?: $first_participant) : 'From: ' . htmlspecialchars($msg->from_address) ?>
                                        </div>
                                    </div>

                                    <!-- Message Body -->
                                    <div class="text-sm leading-relaxed text-gray-800 font-normal">
                                        <?php if ($msg->body_html && !$hasQuote): ?>
                                            <div class="prose prose-sm max-w-none text-gray-800 leading-relaxed overflow-x-auto"><?= $msg->body_html ?></div>
                                        <?php else: ?>
                                            <p class="whitespace-pre-wrap leading-relaxed text-[13.5px] font-medium text-slate-800"><?= htmlspecialchars($mainText ?: $rawBody) ?></p>
                                        <?php endif; ?>

                                        <!-- Collapsible Quoted Email Accordion -->
                                        <?php if ($hasQuote): ?>
                                            <div class="mt-4 pt-3 border-t border-slate-100">
                                                <button type="button" onclick="toggleQuotedText(this)" class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-slate-500 hover:text-brand-deep bg-slate-50 hover:bg-emerald-50 px-2.5 py-1 rounded-lg border border-slate-200/70 transition-all cursor-pointer">
                                                    <i class="fas fa-ellipsis-h text-[10px]"></i>
                                                    <span>Show quoted conversation</span>
                                                </button>
                                                <div class="quoted-text-block mt-3 pl-3 border-l-2 border-slate-300 text-xs text-slate-500 whitespace-pre-wrap font-mono leading-relaxed bg-slate-50/50 p-2.5 rounded-r-xl">
                                                    <?= htmlspecialchars($quoteText) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modern Docked Reply Composer Box -->
                <div class="p-5 border-t border-gray-200/80 bg-white/95 backdrop-blur-md shadow-lg sticky bottom-0 z-10">
                    <form id="reply-form" class="bg-white rounded-2xl p-4 shadow-xs border border-gray-200/90 flex flex-col gap-3 focus-within:border-brand-green focus-within:ring-2 focus-within:ring-emerald-500/10 transition-all">
                        <input type="hidden" name="account_id" value="<?= $account->id ?>">
                        <input type="hidden" name="thread_id" value="<?= $thread->thread_id ?>">
                        <input type="hidden" name="to" value="<?= htmlspecialchars($first_participant) ?>">
                        <input type="hidden" name="subject" value="Re: <?= htmlspecialchars(preg_replace('/^(\s*(re|fwd|fw):\s*)+/i', '', $thread->subject ?: '')) ?>">
                        <input type="hidden" name="influencer_id" value="<?= $thread->influencer_id ?? '' ?>">
                        <input type="hidden" name="campaign_id" value="<?= $thread->campaign_id ?? '' ?>">

                        <!-- Quick Template Chips Selector Bar -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1.5 border-b border-gray-100 no-scrollbar">
                            <span class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider shrink-0 mr-1">
                                <i class="fas fa-magic text-brand-green mr-1"></i> Quick Reply:
                            </span>
                            <?php if (!empty($templates)): ?>
                                <?php foreach (array_slice($templates, 0, 4) as $tpl): ?>
                                    <button type="button" 
                                            onclick="insertTemplateText(<?= htmlspecialchars(json_encode($tpl->body_html ?: $tpl->body_plain)) ?>)" 
                                            class="text-[11px] font-bold text-gray-600 bg-gray-50 hover:bg-emerald-50 hover:text-brand-deep border border-gray-200/80 hover:border-emerald-200 rounded-xl px-3 py-1 transition-all shrink-0">
                                        <?= htmlspecialchars($tpl->name) ?>
                                    </button>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Reply Textarea -->
                        <textarea id="reply-body" 
                                  name="body" 
                                  class="w-full border-0 focus:ring-0 resize-none text-sm text-gray-800 placeholder-gray-400 outline-none min-h-[95px] max-h-[220px] leading-relaxed" 
                                  placeholder="Write your reply to <?= htmlspecialchars($first_participant) ?>... (Press Ctrl + Enter to send)" 
                                  required></textarea>

                        <!-- Bottom Actions Dock -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2.5 border-t border-gray-100">
                            
                            <!-- Template dropdown selector -->
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <span class="text-xs text-gray-400 font-bold shrink-0">Template:</span>
                                <select id="template-select" class="text-xs border border-gray-200 rounded-xl px-3 py-1.5 focus:border-brand-green outline-none max-w-[220px] bg-white font-medium text-gray-700">
                                    <option value="">Choose a template...</option>
                                    <?php if (!empty($templates)): ?>
                                        <?php foreach ($templates as $t): ?>
                                            <option value="<?= $t->id ?>" data-subject="<?= htmlspecialchars($t->subject) ?>" data-body="<?= htmlspecialchars($t->body_html ?: $t->body_plain) ?>">
                                                <?= htmlspecialchars($t->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Right Controls: Keyboard hint & Send Button -->
                            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                                <span class="hidden md:inline-flex items-center text-[10px] text-gray-400 font-bold gap-1">
                                    <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded text-gray-500 font-mono text-[9px]">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded text-gray-500 font-mono text-[9px]">Enter</kbd> to send
                                </span>

                                <button type="submit" id="send-btn" class="bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] hover:opacity-95 text-white px-5 py-2 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all h-9 cursor-pointer shrink-0">
                                    <i class="fas fa-paper-plane text-xs"></i>
                                    <span>Send Reply</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>

    <!-- Script handling -->
    <script>
        // Scroll messages thread to bottom on load
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        // Toggle Quoted Email text
        function toggleQuotedText(btn) {
            const block = btn.nextElementSibling;
            if (block) {
                block.classList.toggle('open');
                const isOpened = block.classList.contains('open');
                btn.querySelector('span').innerText = isOpened ? 'Hide quoted conversation' : 'Show quoted conversation';
            }
        }

        // Insert template text directly
        function insertTemplateText(bodyText) {
            const textarea = document.getElementById('reply-body');
            if (!textarea || !bodyText) return;

            const rawFirst = <?= json_encode($first_name) ?> || 'there';
            const firstTitle = rawFirst.charAt(0).toUpperCase() + rawFirst.slice(1).toLowerCase();
            const firstUpper = rawFirst.toUpperCase();
            const firstLower = rawFirst.toLowerCase();

            // Handle modifiers like {{first_name:upper}}, {{first_name:lower}}, {{first_name:title}}
            bodyText = bodyText.replace(/\{\{\s*first_name\s*[:|]\s*(upper|caps)\s*\}\}/gi, firstUpper);
            bodyText = bodyText.replace(/\{\{\s*first_name\s*[:|]\s*(lower|small)\s*\}\}/gi, firstLower);
            bodyText = bodyText.replace(/\{\{\s*first_name\s*[:|]\s*title\s*\}\}/gi, firstTitle);

            // Replace exact casing tags
            bodyText = bodyText.replace(/\{\{\s*(FIRST_NAME|first_name_upper|first_name_caps)\s*\}\}/g, firstUpper);
            bodyText = bodyText.replace(/\{\{\s*(first_name_lower|first_name_small)\s*\}\}/g, firstLower);
            bodyText = bodyText.replace(/\{\{\s*first_name_title\s*\}\}/g, firstTitle);

            const vars = {
                'influencer_name': firstTitle,
                'first_name': firstTitle,
                'name': firstTitle,
                'full_name': <?= json_encode($influencer_full_name) ?>,
                'user_name': <?= json_encode($influencer_name) ?>,
                'username': <?= json_encode($influencer_name) ?>,
                'sender_name': <?= json_encode($sender_name) ?>,
                'owner_name': <?= json_encode($sender_name) ?>,
                'brand_name': <?= json_encode($brand_name) ?>,
                'sender_title': <?= json_encode($sender_title) ?>,
                'contact_email': <?= json_encode($contact_email) ?>,
                'contact_phone': '',
                'platform': <?= json_encode($influencer_platform) ?>,
                'collaboration_details': <?= json_encode($collaboration_details) ?>,
                'discount_code': 'WELCOME10',
                'discount_amount': '10%',
                'valid_until': '30 days',
                'affiliate_link': 'http://instroom.online/affiliate',
                'shipping_address': 'influencer address',
                'brand_hashtag': 'instroom',
                'brand_handle': 'instroom_family',
                'tracking_link': 'http://instroom.online/tracking',
                'custom_line': 'your recent content'
            };

            for (const [key, val] of Object.entries(vars)) {
                // Replace {{key}} case-insensitively with optional spaces
                const doubleBrackets = new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'gi');
                bodyText = bodyText.replace(doubleBrackets, val);

                // Also replace single {key}
                const singleBrackets = new RegExp('\\{\\s*' + key + '\\s*\\}', 'gi');
                bodyText = bodyText.replace(singleBrackets, val);
            }

            // Convert HTML line breaks to carriage returns for textarea styling
            let formattedBody = bodyText.replace(/<br\s*[\/]?>/gi, '\n');
            formattedBody = formattedBody.replace(/<\/p>/gi, '\n\n');

            // Strip remaining HTML tags and decode HTML entities
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = formattedBody;
            const finalValue = tempDiv.textContent || tempDiv.innerText || '';

            textarea.value = finalValue.trim();
            textarea.focus();
            textarea.scrollTop = 0;
        }

        // Star toggle action helper
        async function toggleStar() {
            try {
                const icon = document.querySelector('.star-btn i');
                const isCurrentlyStarred = icon.classList.contains('fas');
                icon.className = isCurrentlyStarred ? 'far fa-star text-sm' : 'fas fa-star text-amber-400 text-sm';
                
                await fetch('<?= base_url("email/toggle_starred/" . $thread->thread_id) ?>', {
                    method: 'POST',
                    credentials: 'same-origin'
                });
            } catch(e) {}
        }

        // Sync current thread
        async function syncCurrentThread() {
            const icon = document.getElementById('sync-icon');
            if (icon) icon.classList.add('fa-spin');
            showToast('Syncing Conversation...', 'Checking for new replies from creator...', 'info', 2000);
            try {
                const res = await fetch('<?= base_url('email/sync_inbox') ?>');
                const data = await res.json();
                if (data.status === 'success') {
                    if (data.synced_count > 0) {
                        showToast('New Messages Found!', 'Refreshing conversation...', 'success', 1500);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast('Conversation Up to Date', 'No new messages found from creator.', 'success', 3500);
                    }
                } else {
                    showToast('Sync Notice', data.message || 'Unable to sync conversation.', 'error', 4000);
                }
            } catch(e) {
                showToast('Sync Error', 'Network error while checking replies.', 'error', 3500);
            }
            if (icon) icon.classList.remove('fa-spin');
        }

        // Load templates dynamically from dropdown select or sidebar buttons
        function insertTemplate(id) {
            const select = document.getElementById('template-select');
            if (!select) return;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value == id) {
                    select.selectedIndex = i;
                    select.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }

        const templateSelect = document.getElementById('template-select');
        if (templateSelect) {
            templateSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (!option || !option.value) return;
                
                let body = option.getAttribute('data-body') || '';
                insertTemplateText(body);
            });
        }

        // Ctrl + Enter to send reply
        const replyTextarea = document.getElementById('reply-body');
        if (replyTextarea) {
            replyTextarea.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('reply-form').dispatchEvent(new Event('submit'));
                }
            });
        }

        // Send Reply AJAX logic
        document.getElementById('reply-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('send-btn');
            const originalHTML = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> <span>Sending...</span>';
            
            const formData = new FormData(this);
            try {
                const response = await fetch('<?= base_url('email/send_email') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to reply'));
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (e) {
                alert('Error: ' + e.message);
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        });

        async function deleteCurrentThread() {
            if (!confirm('Are you sure you want to delete this email conversation from the system?')) {
                return;
            }
            try {
                const response = await fetch('<?= base_url("email/delete_thread/" . $thread->thread_id) ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                if (result.status === 'success') {
                    window.location.href = '<?= base_url('email') ?>';
                } else {
                    showToast('Action Failed', result.message || 'Failed to delete thread', 'error');
                }
            } catch (e) {
                window.location.href = '<?= base_url("email/delete_thread/" . $thread->thread_id) ?>';
            }
        }

    </script>
</body>
</html>
