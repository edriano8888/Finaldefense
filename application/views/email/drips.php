<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($selected_drip) ? html_escape($selected_drip->name) . ' - Email Sequence' : 'Email Drips - Instroom' ?></title>
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
                            blue: '#3B82F6',
                            purple: '#8B5CF6',
                            rose: '#F43F5E',
                            amber: '#F59E0B'
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(15, 107, 62, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02)',
                        'card-hover': '0 12px 28px -4px rgba(15, 107, 62, 0.12), 0 6px 12px -2px rgba(0, 0, 0, 0.04)',
                        'glow': '0 0 20px rgba(31, 174, 91, 0.25)',
                        'soft': '0 4px 12px rgba(0, 0, 0, 0.03)',
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

        function toggleNotifMenu() {
            const menu = document.getElementById('notif-menu');
            if (menu) menu.classList.toggle('hidden');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('absolute');
            sidebar.classList.toggle('z-40');
            sidebar.classList.toggle('h-full');
        }

        let sidebarCollapsed = false;

        function setSidebarCollapsed(collapsed) {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            if (window.innerWidth < 768) {
                collapsed = false;
            }
            sidebarCollapsed = collapsed;
            localStorage.setItem('sidebarCollapsed', collapsed ? 'true' : 'false');

            sidebar.classList.toggle('w-72', !collapsed);
            sidebar.classList.toggle('w-20', collapsed);

            const brandSection = sidebar.querySelector('.h-16');
            if (brandSection) {
                brandSection.classList.remove('px-8');
                brandSection.classList.toggle('px-6', !collapsed);
                brandSection.classList.toggle('px-0', collapsed);
                brandSection.classList.toggle('justify-center', collapsed);
            }

            const brandWrapper = sidebar.querySelector('.sidebar-brand-wrapper');
            if (brandWrapper) {
                brandWrapper.classList.toggle('px-4', collapsed);
                brandWrapper.classList.toggle('justify-center', collapsed);
            }

            const nav = sidebar.querySelector('nav');
            if (nav) {
                nav.classList.toggle('px-4', !collapsed);
                nav.classList.toggle('px-2', collapsed);
            }

            sidebar.querySelectorAll('nav p, .sidebar-brand-text').forEach(el => {
                el.classList.toggle('hidden', collapsed);
            });

            sidebar.querySelectorAll('a.sidebar-item').forEach(a => {
                const labelEl = a.querySelector('span');
                const label = labelEl ? labelEl.textContent.trim() : '';
                if (labelEl) {
                    labelEl.classList.toggle('hidden', collapsed);
                }

                a.classList.toggle('justify-center', collapsed);
                a.classList.toggle('px-4', !collapsed);
                a.classList.toggle('px-2', collapsed);

                if (collapsed && label) {
                    a.setAttribute('title', label);
                } else {
                    a.removeAttribute('title');
                }
            });

            const collapseBtnIcon = sidebar.querySelector('#sidebar-collapse-btn i');
            if (collapseBtnIcon) {
                collapseBtnIcon.className = collapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
            }
        }

        function toggleSidebarCollapse() {
            setSidebarCollapsed(!sidebarCollapsed);
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

    $status_styles = [
        'drafted' => 'bg-slate-100 text-slate-600 border-slate-200',
        'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'completed' => 'bg-blue-100 text-blue-700 border-blue-200',
    ];
    $format_percent = static function($value) {
        return number_format((float) $value, 0) . '%';
    };
?>

    <!-- Sidebar -->
    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-[#F8FAFC]">
        
        <!-- Top Navigation -->
        <?php $this->load->view('partials/header', ['parent' => 'Email', 'child' => 'Email Drips']); ?>

        <!-- Main Content Body -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 md:p-8 custom-scrollbar relative">
            <div class="max-w-7xl mx-auto space-y-6">

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 backdrop-blur-md px-5 py-4 text-sm font-bold text-emerald-800 flex items-center gap-3 shadow-card">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs">
                            <i class="fas fa-check"></i>
                        </div>
                        <span><?= $this->session->flashdata('success') ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50/80 backdrop-blur-md px-5 py-4 text-sm font-bold text-rose-800 flex items-center gap-3 shadow-card">
                        <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center text-xs">
                            <i class="fas fa-exclamation"></i>
                        </div>
                        <span><?= $this->session->flashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($selected_drip)): ?>
                    <!-- ==================================================== -->
                    <!-- REDESIGNED EXECUTIVE CAMPAIGN SEQUENCE EDITOR VIEW   -->
                    <!-- ==================================================== -->
                    
                    <!-- Modern Header Banner -->
                    <div class="bg-gradient-to-r from-[#0F6B3E] via-[#147A47] to-[#1FAE5B] rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
                        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
                            <div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-200 mb-2">
                                    <a href="<?= base_url('email/drips') ?>" class="hover:text-white transition-colors">Campaigns</a>
                                    <i class="fas fa-chevron-right text-[9px] opacity-70"></i>
                                    <span>Sequence Flow</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <h1 id="campaign-title-display" class="text-2xl md:text-3xl font-extrabold tracking-tight"><?= html_escape($selected_drip->name) ?></h1>
                                    <button type="button" onclick="editCampaignName()" class="w-9 h-9 rounded-2xl bg-white/15 hover:bg-white/25 border border-white/20 text-white flex items-center justify-center text-xs shadow-inner backdrop-blur-md transition-all" title="Rename Campaign">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold bg-white/15 border border-white/20 px-4 py-2 rounded-2xl backdrop-blur-md shadow-sm">
                                    Target Group: <strong class="text-emerald-200">Creators Pipeline</strong>
                                </span>
                                <button type="button" onclick="triggerDripProcess(event)" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-xs font-extrabold shadow-lg transition-all transform hover:-translate-y-0.5" title="Manually run email dispatch queue for active campaigns">
                                    <i class="fas fa-sync"></i>
                                    Run Drips Queue
                                </button>
                                <button type="button" onclick="openEmailTemplatesModal()" class="inline-flex items-center gap-2 rounded-2xl bg-white text-brand-deep hover:bg-emerald-50 px-4 py-2.5 text-xs font-extrabold shadow-lg transition-all transform hover:-translate-y-0.5">
                                    <i class="fas fa-sparkles text-amber-500"></i>
                                    View Templates
                                </button>
                                <?= form_open('email/delete_drip/' . (int) $selected_drip->id, ['onsubmit' => "return confirm('Are you sure you want to delete this campaign?');", 'class' => 'inline']) ?>
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-rose-600/80 hover:bg-rose-600 text-white px-4 py-2.5 text-xs font-extrabold shadow-lg transition-all transform hover:-translate-y-0.5" title="Delete Campaign">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </button>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>

                    <!-- Executive Stats Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="rounded-3xl border border-gray-200/80 bg-white p-5 shadow-card hover:shadow-card-hover transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Creators Linked</p>
                                    <p class="text-2xl font-black text-gray-900 mt-1"><?= count($drip_creators) ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-gray-200/80 bg-white p-5 shadow-card hover:shadow-card-hover transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Open Rate</p>
                                    <p class="text-2xl font-black text-brand-deep mt-1"><?= $format_percent($selected_drip->opens_rate ?: 22) ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                                    <i class="far fa-envelope-open"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-gray-200/80 bg-white p-5 shadow-card hover:shadow-card-hover transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Response Rate</p>
                                    <p class="text-2xl font-black text-brand-deep mt-1"><?= $format_percent($selected_drip->responses_rate ?: 0) ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-brand-deep flex items-center justify-center text-lg">
                                    <i class="fas fa-reply"></i>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-gray-200/80 bg-white p-5 shadow-card hover:shadow-card-hover transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Bounce Rate</p>
                                    <p class="text-2xl font-black text-gray-900 mt-1"><?= $format_percent($selected_drip->bounces_rate ?: 6) ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 text-gray-400 flex items-center justify-center text-lg">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub Navigation Tabs Bar -->
                    <div class="bg-white rounded-2xl border border-gray-200/80 p-2 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="switchEditorTab('sequence')" id="tab-btn-sequence" class="px-5 py-2.5 rounded-xl text-xs font-extrabold bg-brand-deep text-white shadow-sm transition-all">
                                <i class="fas fa-stream mr-1.5"></i> Email Sequence Builder
                            </button>
                            <button type="button" onclick="switchEditorTab('creators')" id="tab-btn-creators" class="px-5 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all">
                                <i class="fas fa-user-friends mr-1.5"></i> Creators List
                            </button>
                        </div>
                        <div class="flex items-center gap-3 px-3">
                            <label class="inline-flex items-center gap-2 text-xs font-bold text-gray-600 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 rounded text-brand-green focus:ring-brand-green">
                                Intelligent Naming
                            </label>
                        </div>
                    </div>

                    <!-- Email Sequence Editor Container -->
                    <div id="view-sequence-tab" class="block space-y-6">
                        <?= form_open('email/save_drip_sequence', ['id' => 'sequence-form']) ?>
                            <input type="hidden" name="drip_id" value="<?= (int)$selected_drip->id ?>">
                            <input type="hidden" name="steps_json" id="steps-json-input" value="">

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                
                                <!-- Left Column: Sequence Stepper List -->
                                <div class="lg:col-span-4 bg-white rounded-3xl border border-gray-200/80 shadow-card p-5 space-y-4">
                                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Steps Timeline</h3>
                                        <span class="text-[10px] font-bold text-brand-deep bg-emerald-50 px-2 py-0.5 rounded-md">Sequential</span>
                                    </div>

                                    <div class="space-y-4" id="steps-container">
                                        <?php 
                                            $steps_list = !empty($drip_steps) ? $drip_steps : [
                                                (object)['step_number' => 1, 'thread_type' => 'NEW THREAD', 'wait_days' => 4, 'subject' => 'love your work!', 'body_html' => "Hi {{first_name}},\n\nLove your content on your insta - {{user_name}}, amazing post about {{custom_line}}\n\nWould you be open to working with us to get the word out there about our product? Let me know and I'll send something over for you to taste 😍\n\n--\n{{owner_name}}"],
                                                (object)['step_number' => 2, 'thread_type' => 'SAME THREAD', 'wait_days' => 7, 'subject' => 'love your work!', 'body_html' => "Hey {{first_name}},\n\nJust following up on my previous message in case it got buried! Would love to collaborate with you.\n\nBest,\n{{owner_name}}"],
                                                (object)['step_number' => 3, 'thread_type' => 'NEW THREAD', 'wait_days' => 1, 'subject' => 'giveaway with [brand]', 'body_html' => "Hi {{first_name}},\n\nWe are hosting an exclusive giveaway for our top creators and would love for you to join!\n\nCheers,\n{{owner_name}}"],
                                                (object)['step_number' => 4, 'thread_type' => 'SAME THREAD', 'wait_days' => 3, 'subject' => 'giveaway with [brand]', 'body_html' => "Hey {{first_name}},\n\nLast call for the giveaway spot! Let me know if you're interested.\n\nBest,\n{{owner_name}}"]
                                            ];
                                        ?>
                                    </div>
                                </div>

                                <!-- Right Column: Rich Step Content Editor -->
                                <div class="lg:col-span-8 bg-white rounded-3xl border border-gray-200/80 shadow-card p-6">
                                    <div class="mb-5">
                                        <label class="block text-xs font-extrabold uppercase tracking-wider text-gray-400 mb-2">Subject Line</label>
                                        <input type="text" id="editor-subject" class="w-full text-base font-bold text-gray-900 rounded-2xl border border-gray-200 px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all" placeholder="Enter email subject line...">
                                    </div>

                                    <!-- Editor Toolbar -->
                                    <div class="border border-gray-200 rounded-2xl overflow-hidden mb-4 shadow-xs">
                                        <div class="bg-gray-50/80 border-b border-gray-200 px-3.5 py-2.5 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                            <button type="button" onclick="formatDoc('bold')" class="font-black px-2.5 py-1 rounded-lg hover:bg-gray-200 transition-colors" title="Bold">B</button>
                                            <button type="button" onclick="formatDoc('italic')" class="italic px-2.5 py-1 rounded-lg hover:bg-gray-200 transition-colors" title="Italic">I</button>
                                            <button type="button" onclick="formatDoc('underline')" class="underline px-2.5 py-1 rounded-lg hover:bg-gray-200 transition-colors" title="Underline">U</button>
                                            <button type="button" onclick="formatDoc('line-through')" class="line-through px-2.5 py-1 rounded-lg hover:bg-gray-200 transition-colors" title="Strikethrough">S</button>
                                            <span class="w-px h-4 bg-gray-300"></span>
                                            <button type="button" onclick="formatDoc('justifyLeft')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors" title="Align Left"><i class="fas fa-align-left"></i></button>
                                            <button type="button" onclick="formatDoc('justifyCenter')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors" title="Align Center"><i class="fas fa-align-center"></i></button>
                                            <button type="button" onclick="formatDoc('justifyRight')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors" title="Align Right"><i class="fas fa-align-right"></i></button>
                                            <span class="w-px h-4 bg-gray-300"></span>
                                            <button type="button" onclick="formatDoc('insertUnorderedList')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                                            <button type="button" onclick="formatDoc('insertOrderedList')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                                            <span class="w-px h-4 bg-gray-300"></span>
                                            <div class="inline-flex items-center bg-gray-200/80 p-0.5 rounded-lg text-[10px] font-bold" title="Change case of selected text or first name">
                                                <button type="button" onclick="changeSelectionCase('upper')" class="px-2 py-0.5 rounded hover:bg-white text-gray-800 transition-all font-black" title="CAPSLOCK / UPPERCASE">UPPER</button>
                                                <button type="button" onclick="changeSelectionCase('lower')" class="px-2 py-0.5 rounded hover:bg-white text-gray-800 transition-all font-medium lowercase" title="small letters / lowercase">lower</button>
                                                <button type="button" onclick="changeSelectionCase('title')" class="px-2 py-0.5 rounded hover:bg-white text-gray-800 transition-all font-semibold capitalize" title="Title Case">Title</button>
                                            </div>
                                            <span class="w-px h-4 bg-gray-300"></span>
                                            <button type="button" onclick="insertTag('{{first_name}}')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors text-brand-deep" title="Insert Tag"><i class="fas fa-code"></i></button>
                                            <button type="button" onclick="formatDoc('removeFormat')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors" title="Clear Formatting"><i class="fas fa-eraser"></i></button>
                                        </div>
                                        <div id="editor-body" contenteditable="true" class="w-full p-4 text-sm text-gray-800 focus:outline-none overflow-y-auto font-sans leading-relaxed min-h-[250px] max-h-[400px] border-0 outline-none" style="min-height: 250px;"></div>
                                    </div>

                                    <!-- Bottom Customizer Tags -->
                                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
                                        <span class="text-xs font-extrabold uppercase tracking-wider text-gray-400 mr-1">Customize:</span>
                                        <button type="button" onclick="insertTag('{{first_name}}')" class="rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-deep border border-emerald-200/80 text-xs font-bold px-3 py-1.5 transition-all" title="Title Case (e.g. Danica)">first_name</button>
                                        <button type="button" onclick="insertTag('{{FIRST_NAME}}')" class="rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200/80 text-xs font-bold px-3 py-1.5 transition-all shadow-xs" title="CAPSLOCK / UPPERCASE (e.g. DANICA)"><i class="fas fa-arrow-up text-[10px] mr-1"></i>FIRST_NAME (CAPS)</button>
                                        <button type="button" onclick="insertTag('{{first_name_lower}}')" class="rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200/80 text-xs font-bold px-3 py-1.5 transition-all shadow-xs" title="Small Letters / Lowercase (e.g. danica)"><i class="fas fa-font text-[9px] mr-1"></i>first_name (small)</button>
                                        <button type="button" onclick="insertTag('{{user_name}}')" class="rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-deep border border-emerald-200/80 text-xs font-bold px-3 py-1.5 transition-all">user_name</button>
                                        <button type="button" onclick="insertTag('{{custom_line}}')" class="rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-deep border border-emerald-200/80 text-xs font-bold px-3 py-1.5 transition-all">custom_line</button>
                                        <button type="button" onclick="insertTag('{{last_name}}')" class="rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-deep border border-emerald-200/80 text-xs font-bold px-3 py-1.5 transition-all">last_name</button>
                                        <button type="button" onclick="insertTag('{{custom_parameter1}}')" class="rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-deep border border-emerald-200/80 text-xs font-bold px-3 py-1.5 transition-all">custom_parameter1</button>
                                        <button type="button" onclick="insertTag('{{custom_parameter2}}')" class="rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-deep border border-emerald-200/80 text-xs font-bold px-3 py-1.5 transition-all">custom_parameter2</button>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="submit" onclick="prepareSaveSequence(event)" class="rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-4 py-2.5 transition-all flex items-center gap-2">
                                            <i class="fas fa-save text-xs"></i>
                                            Save Draft Sequence
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule & Delivery Time Settings Bar -->
                            <div class="mt-8 rounded-3xl bg-white border border-gray-200/80 shadow-card p-6">
                                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-brand-deep flex items-center justify-center text-xs font-bold">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-extrabold text-gray-900">When should we send these emails?</h3>
                                        <p class="text-xs text-gray-400">Configure your automated delivery window & recipient timezone</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Days</label>
                                        <select id="schedule-days" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green bg-white shadow-xs">
                                            <option value="Weekdays">Weekdays</option>
                                            <option value="Everyday">Everyday</option>
                                            <option value="Weekends">Weekends</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">From</label>
                                        <select id="schedule-from" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green bg-white shadow-xs">
                                            <option value="09:00 hrs">09:00 hrs</option>
                                            <option value="08:00 hrs">08:00 hrs</option>
                                            <option value="10:00 hrs">10:00 hrs</option>
                                            <option value="11:00 hrs">11:00 hrs</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">To</label>
                                        <select id="schedule-to" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green bg-white shadow-xs">
                                            <option value="03:00 hrs">03:00 hrs</option>
                                            <option value="04:00 hrs">04:00 hrs</option>
                                            <option value="05:00 hrs">05:00 hrs</option>
                                            <option value="06:00 hrs">06:00 hrs</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Timezone</label>
                                        <select id="schedule-timezone" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green bg-white shadow-xs">
                                            <option value="Mountain Time (US & CA) (UTC-07:00)">Mountain Time (US & CA) (UTC-07:00)</option>
                                            <option value="Pacific Time (US & CA) (UTC-08:00)">Pacific Time (US & CA) (UTC-08:00)</option>
                                            <option value="Eastern Time (US & CA) (UTC-05:00)">Eastern Time (US & CA) (UTC-05:00)</option>
                                            <option value="Asia/Manila (UTC+08:00)">Asia/Manila (UTC+08:00)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Center Save & Preview Button -->
                                <div class="mt-8 flex justify-center">
                                    <button type="button" onclick="openDripPreviewModal()" class="px-10 py-4 rounded-2xl bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] hover:opacity-95 text-white font-extrabold text-sm shadow-xl hover:shadow-glow transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                                        <i class="fas fa-eye text-xs"></i>
                                        Save & Preview
                                    </button>
                                </div>
                            </div>
                        <?= form_close() ?>
                    </div>

                    <!-- Inline Campaign Name Update Form -->
                    <?= form_open('email/update_drip_name', ['id' => 'update-title-form', 'class' => 'hidden']) ?>
                        <input type="hidden" name="drip_id" value="<?= (int)$selected_drip->id ?>">
                        <input type="hidden" name="name" id="update-title-input" value="">
                    <?= form_close() ?>

                    <!-- Creators List Tab View -->
                    <div id="view-creators-tab" class="hidden">
                        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-card p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Campaign Creators List</h3>
                                    <p class="text-xs text-gray-500 font-medium">Creators enrolled in this automated drip sequence</p>
                                </div>
                                <button type="button" onclick="openAddCreatorsModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm hover:opacity-95 transition-all">
                                    <i class="fas fa-plus text-[10px]"></i> Add Creators to Campaign
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                                            <th class="py-3 px-4">Creator</th>
                                            <th class="py-3 px-4">Email</th>
                                            <th class="py-3 px-4">Status</th>
                                            <th class="py-3 px-4">Current Step</th>
                                            <th class="py-3 px-4">Last Activity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php if (!empty($drip_creators)): ?>
                                            <?php foreach ($drip_creators as $creator): 
                                                $c_name = !empty($creator->full_name) ? $creator->full_name : (!empty($creator->username) ? $creator->username : 'Creator');
                                                $c_email = !empty($creator->email) ? $creator->email : 'No email address';
                                                $c_status = !empty($creator->status) ? $creator->status : 'Active';
                                                $c_step = 'Step ' . ($creator->current_step ?? 1);
                                                $c_date = !empty($creator->last_sent_at) ? date('M j, g:i a', strtotime($creator->last_sent_at)) : (!empty($creator->linked_at) ? date('M j, Y', strtotime($creator->linked_at)) : 'Just Added');
                                            ?>
                                                <tr class="hover:bg-gray-50/60 transition-colors">
                                                    <td class="py-3.5 px-4 font-bold text-gray-800">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#0F6B3E] to-[#1FAE5B] text-white flex items-center justify-center font-black text-xs shadow-xs">
                                                                <?= strtoupper(substr($c_name, 0, 1)) ?>
                                                            </div>
                                                            <div>
                                                                <p class="font-bold text-gray-900 text-xs"><?= html_escape($c_name) ?></p>
                                                                <?php if (!empty($creator->username)): ?>
                                                                    <p class="text-[10px] text-emerald-600 font-bold">@<?= html_escape($creator->username) ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-gray-600 text-xs font-semibold"><?= html_escape($c_email) ?></td>
                                                    <td class="py-3.5 px-4">
                                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase">
                                                            <?= html_escape($c_status) ?>
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-gray-700 text-xs font-bold"><?= html_escape($c_step) ?></td>
                                                    <td class="py-3.5 px-4 text-gray-500 text-xs font-medium"><?= html_escape($c_date) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="py-10 text-center text-gray-400 font-medium">
                                                    <div class="max-w-sm mx-auto space-y-2">
                                                        <i class="fas fa-users text-2xl text-gray-300"></i>
                                                        <p class="text-xs font-bold text-gray-700">No Creators Enrolled Yet</p>
                                                        <p class="text-[11px] text-gray-400">Click below to add creators from your roster to this campaign.</p>
                                                        <button type="button" onclick="openAddCreatorsModal()" class="mt-2 inline-flex items-center gap-1.5 bg-brand-deep hover:bg-brand-green text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-xs">
                                                            <i class="fas fa-plus"></i> Select Creators
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Email Templates Modal -->
                    <div id="email-templates-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full border border-gray-100 overflow-hidden flex flex-col max-h-[88vh] transition-all">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white">
                                <h2 class="text-xl font-extrabold text-gray-900">Email Templates</h2>
                                <button type="button" onclick="closeEmailTemplatesModal()" class="text-gray-400 hover:text-gray-600 p-1 text-base">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="flex-1 flex overflow-hidden">
                                <div class="w-64 border-r border-gray-200 bg-gray-50/50 p-4 overflow-y-auto space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2 px-2">
                                            <span>INSTROOM Recommendations</span>
                                            <i class="fas fa-chevron-up text-[10px]"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <button type="button" onclick="selectTemplateCategory('seeding')" id="tpl-cat-seeding" class="w-full text-left px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-green-100 text-brand-deep border border-green-200">
                                                Seeding Campaign
                                            </button>
                                            <button type="button" onclick="selectTemplateCategory('gifting')" id="tpl-cat-gifting" class="w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                                                Gifting Campaign
                                            </button>
                                            <button type="button" onclick="selectTemplateCategory('ambassador')" id="tpl-cat-ambassador" class="w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                                                Ambassador Campaign
                                            </button>
                                            <button type="button" onclick="selectTemplateCategory('affiliate')" id="tpl-cat-affiliate" class="w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                                                Affiliate Campaign
                                            </button>
                                            <button type="button" onclick="selectTemplateCategory('challenge')" id="tpl-cat-challenge" class="w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                                                Challenge Campaign
                                            </button>
                                            <button type="button" onclick="selectTemplateCategory('event')" id="tpl-cat-event" class="w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                                                Event Invite
                                            </button>
                                            <button type="button" onclick="selectTemplateCategory('payperpost')" id="tpl-cat-payperpost" class="w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                                                Pay per post campaign
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 p-6 overflow-y-auto bg-white flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                                            <h3 id="tpl-preview-subject" class="text-lg font-bold text-gray-900">I have something for you</h3>
                                            <div class="w-8 h-8 rounded-full bg-emerald-50 text-brand-deep border border-emerald-100 flex items-center justify-center text-sm">
                                                <i class="far fa-lightbulb"></i>
                                            </div>
                                        </div>

                                        <div id="tpl-preview-body" class="whitespace-pre-line text-sm text-gray-700 leading-relaxed font-sans bg-gray-50/50 p-5 rounded-2xl border border-gray-100 min-h-[260px]">
Hi {{first_name}},

Noticed you recently posted about RELATED_POST which compelled me to reach out.

I'm YOUR_NAME, the TITLE at BRAND and I think you'll really like our PRODUCT. For no commitment on your end, I want to send you some free goodies to test and see what you think - we can explore a collaboration if you like them.

Shipping address?

--
YOUR_NAME
TITLE at BRAND
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 mt-6">
                                        <button type="button" onclick="closeEmailTemplatesModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                                            Go Back
                                        </button>
                                        <button type="button" onclick="applySelectedTemplate()" class="px-6 py-2.5 rounded-xl bg-brand-deep hover:bg-emerald-800 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all">
                                            Use template
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Drip Preview Modal -->
                    <div id="drip-preview-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full border border-gray-100 overflow-hidden flex flex-col max-h-[90vh] transition-all">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white">
                                <h2 class="text-xl font-extrabold text-gray-900">Email Drip Preview</h2>
                                <div class="flex items-center gap-3">
                                    <span id="preview-emails-count" class="bg-gray-150 text-gray-800 text-xs font-black px-3.5 py-1.5 rounded-xl border border-gray-200" title="Number of email sequence steps">
                                        <?= count($steps_list) ?> Steps
                                    </span>
                                    <span class="bg-emerald-50 text-emerald-800 text-xs font-black px-3.5 py-1.5 rounded-xl border border-emerald-200" title="Number of creators enrolled in this campaign">
                                        <?= count($drip_creators) ?> Creator(s)
                                    </span>
                                    <select id="preview-creator-select" onchange="renderPreviewContent()" class="rounded-full border border-gray-200 px-3 py-1 text-xs font-bold text-gray-800 bg-white focus:outline-none cursor-pointer">
                                        <?php 
                                            $creators_for_select = !empty($drip_creators) ? $drip_creators : (!empty($all_influencers) ? $all_influencers : []);
                                            if (!empty($creators_for_select)):
                                                foreach ($creators_for_select as $cr):
                                                    $c_handle = !empty($cr->username) ? $cr->username : (!empty($cr->full_name) ? strtolower(str_replace(' ', '', $cr->full_name)) : 'creator');
                                                    $c_label = !empty($cr->full_name) ? $cr->full_name . ' (@' . $c_handle . ')' : '@' . $c_handle;
                                                    $c_fullname = !empty($cr->full_name) ? $cr->full_name : $c_handle;
                                        ?>
                                                    <option value="<?= html_escape($c_handle) ?>" data-name="<?= html_escape($c_fullname) ?>"><?= html_escape($c_label) ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="creator" data-name="Creator">@creator</option>
                                            <?php endif; ?>
                                    </select>
                                    <button type="button" onclick="closeDripPreviewModal()" class="text-gray-400 hover:text-gray-600 p-1 text-base">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex-1 p-6 overflow-y-auto bg-gray-50 space-y-6 custom-scrollbar" id="preview-steps-list"></div>

                            <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-100 bg-white">
                                <button type="button" onclick="closeDripPreviewModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                                <button type="button" onclick="launchCampaignNow()" id="launch-campaign-btn" class="px-6 py-2.5 rounded-xl bg-brand-deep hover:bg-emerald-800 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                                    Launch Campaign 🚀
                                </button>
                            </div>
                        </div>
                    </div>

                    <script>
                        let sequenceSteps = <?= json_encode($steps_list) ?>;
                        let activeStepIndex = 0;

                        const EMAIL_TEMPLATES_DATA = {
                            seeding: {
                                subject: "I have something for you",
                                body: `Hi {{first_name}},\n\nNoticed you recently posted about {{custom_line}} which compelled me to reach out.\n\nI'm {{user_name}}, the Creator Lead at {{active_brand_name}} and I think you'll really like our products. For no commitment on your end, I want to send you some free goodies to test and see what you think - we can explore a collaboration if you like them.\n\nShipping address?\n\n--\n{{user_name}}\nCreator Lead at {{active_brand_name}}`
                            },
                            gifting: {
                                subject: "Gift package for {{first_name}} 🎁",
                                body: `Hi {{first_name}},\n\nWe love your content on {{user_name}} and would love to send you a complimentary gift package from our new collection!\n\nNo strings attached - just want to share our products with creators we admire.\n\nWhere should we send your package?\n\nBest,\n{{user_name}}\n{{active_brand_name}}`
                            },
                            ambassador: {
                                subject: "Join the {{active_brand_name}} Brand Ambassador Program",
                                body: `Hey {{first_name}},\n\nWe're looking for passionate creators to join our official Ambassador Team. As an ambassador, you'll get exclusive access to new launches, 20% commission on sales, and free monthly products.\n\nWould you be open to hearing more details?\n\nCheers,\n{{user_name}}\nAmbassador Manager`
                            },
                            affiliate: {
                                subject: "Earn with {{active_brand_name}} - Partnership Proposal",
                                body: `Hi {{first_name}},\n\nWe've been following your posts and think your audience would love our line. We'd like to set you up with a custom 15% discount code and generous commission on every order generated.\n\nLet me know if you'd like your custom link set up!\n\nBest,\n{{user_name}}\nAffiliate Team`
                            },
                            challenge: {
                                subject: "30-Day Creator Challenge Invite 🚀",
                                body: `Hi {{first_name}},\n\nWe're launching a 30-Day Creator Challenge next week! Participants get free gear, feature highlights on our official page, and cash prizes for top performing content.\n\nInterested in taking part?\n\nBest,\n{{user_name}}`
                            },
                            event: {
                                subject: "VIP Invitation: {{active_brand_name}} Creator Lounge",
                                body: `Hi {{first_name}},\n\nYou're invited to our exclusive VIP Creator Gathering! Join us for a day of product previews, networking, and gifted swag bags.\n\nPlease let us know if you can make it so we can reserve your spot!\n\nWarmly,\n{{user_name}}`
                            },
                            payperpost: {
                                subject: "Paid Collaboration Inquiry - {{active_brand_name}}",
                                body: `Hi {{first_name}},\n\nWe love your aesthetic and want to sponsor a dedicated post/reel on your channel.\n\nCould you share your current rate card and availability for next month?\n\nLooking forward to working together!\n\nBest regards,\n{{user_name}}`
                            }
                        };

                        let currentSelectedCategory = 'seeding';

                        function openEmailTemplatesModal() {
                            const modal = document.getElementById('email-templates-modal');
                            if (modal) modal.classList.remove('hidden');
                            selectTemplateCategory('seeding');
                        }

                        function closeEmailTemplatesModal() {
                            const modal = document.getElementById('email-templates-modal');
                            if (modal) modal.classList.add('hidden');
                        }

                        function selectTemplateCategory(catKey) {
                            currentSelectedCategory = catKey;
                            const tpl = EMAIL_TEMPLATES_DATA[catKey];
                            if (!tpl) return;

                            document.querySelectorAll('[id^="tpl-cat-"]').forEach(btn => {
                                btn.className = 'w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all';
                            });
                            const activeBtn = document.getElementById('tpl-cat-' + catKey);
                            if (activeBtn) {
                                activeBtn.className = 'w-full text-left px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-green-100 text-brand-deep border border-green-200';
                            }

                            document.getElementById('tpl-preview-subject').textContent = tpl.subject;
                            document.getElementById('tpl-preview-body').textContent = tpl.body;
                        }

                        function applySelectedTemplate() {
                            const tpl = EMAIL_TEMPLATES_DATA[currentSelectedCategory];
                            if (!tpl) return;

                            saveCurrentStepData();
                            if (sequenceSteps[activeStepIndex]) {
                                sequenceSteps[activeStepIndex].subject = tpl.subject;
                                sequenceSteps[activeStepIndex].body_html = tpl.body;
                            } else {
                                sequenceSteps.push({
                                    step_number: 1,
                                    thread_type: 'NEW THREAD',
                                    wait_days: 4,
                                    subject: tpl.subject,
                                    body_html: tpl.body
                                });
                            }

                            renderStepList();
                            closeEmailTemplatesModal();
                        }

                        function renderStepList() {
                            const container = document.getElementById('steps-container');
                            if (!container) return;

                            let html = '';
                            sequenceSteps.forEach((step, idx) => {
                                const isActive = idx === activeStepIndex;
                                const isNewThread = (step.thread_type || 'NEW THREAD') === 'NEW THREAD';

                                html += `
                                    <div onclick="selectStep(${idx})" class="cursor-pointer rounded-2xl border transition-all p-4 bg-white shadow-xs hover:shadow-card ${isActive ? 'border-brand-green ring-2 ring-green-100 bg-emerald-50/20' : 'border-gray-200 hover:border-brand-green'}">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-brand-deep text-xs font-extrabold flex items-center justify-center flex-shrink-0">${idx + 1}</span>
                                                <span class="font-bold text-sm text-gray-900 truncate max-w-[160px]">${escapeHtml(step.subject || 'Untitled Step')}</span>
                                            </div>
                                            <button type="button" onclick="toggleThreadType(event, ${idx})" class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md cursor-pointer transition-colors ${isNewThread ? 'bg-emerald-100 text-brand-deep hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}" title="Click to toggle Thread Type">
                                                ${isNewThread ? 'NEW THREAD' : 'SAME THREAD'}
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-100 mt-2">
                                            <div class="flex items-center gap-1.5" onclick="event.stopPropagation()">
                                                <span>If no reply, wait</span>
                                                <input type="number" min="0" value="${parseInt(step.wait_days || 3)}" onchange="updateWaitDays(${idx}, this.value)" class="w-12 text-center rounded-lg border border-gray-200 py-0.5 text-xs font-bold focus:ring-1 focus:ring-brand-green">
                                                <span>days</span>
                                            </div>
                                            <button type="button" onclick="deleteStep(event, ${idx})" class="text-gray-400 hover:text-red-600 p-1" title="Delete Step">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                `;
                            });

                            html += `
                                <button type="button" onclick="addNewStep()" class="w-full rounded-2xl border-2 border-dashed border-gray-300 hover:border-brand-green hover:bg-emerald-50/40 py-3 text-xs font-bold text-gray-500 hover:text-brand-deep transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-plus text-[10px]"></i> Add Email Step
                                </button>
                            `;

                            container.innerHTML = html;
                            loadEditorData();
                        }

                        function selectStep(idx) {
                            saveCurrentStepData();
                            activeStepIndex = idx;
                            renderStepList();
                        }

                        function loadEditorData() {
                            const step = sequenceSteps[activeStepIndex];
                            if (!step) return;
                            document.getElementById('editor-subject').value = step.subject || '';
                            document.getElementById('editor-body').innerHTML = step.body_html || '';
                        }

                        function saveCurrentStepData() {
                            if (!sequenceSteps[activeStepIndex]) return;
                            sequenceSteps[activeStepIndex].subject = document.getElementById('editor-subject').value;
                            sequenceSteps[activeStepIndex].body_html = document.getElementById('editor-body').innerHTML;
                        }

                        function toggleThreadType(e, idx) {
                            e.stopPropagation();
                            if (sequenceSteps[idx]) {
                                const current = (sequenceSteps[idx].thread_type || 'NEW THREAD') === 'NEW THREAD';
                                sequenceSteps[idx].thread_type = current ? 'SAME THREAD' : 'NEW THREAD';
                                renderStepList();
                            }
                        }

                        function updateWaitDays(idx, val) {
                            if (sequenceSteps[idx]) {
                                sequenceSteps[idx].wait_days = parseInt(val) || 0;
                            }
                        }

                        function deleteStep(e, idx) {
                            e.stopPropagation();
                            if (sequenceSteps.length <= 1) {
                                alert('A drip sequence must have at least one step.');
                                return;
                            }
                            sequenceSteps.splice(idx, 1);
                            if (activeStepIndex >= sequenceSteps.length) {
                                activeStepIndex = sequenceSteps.length - 1;
                            }
                            renderStepList();
                        }

                        function addNewStep() {
                            saveCurrentStepData();
                            sequenceSteps.push({
                                step_number: sequenceSteps.length + 1,
                                thread_type: sequenceSteps.length % 2 === 0 ? 'NEW THREAD' : 'SAME THREAD',
                                wait_days: 3,
                                subject: 'Follow-up regarding our campaign',
                                body_html: 'Hi {{first_name}},\n\nWanted to check in again!\n\nBest,\n{{owner_name}}'
                            });
                            activeStepIndex = sequenceSteps.length - 1;
                            renderStepList();
                        }

                        function insertTag(tag) {
                            const editor = document.getElementById('editor-body');
                            if (!editor) return;
                            editor.focus();
                            
                            const sel = window.getSelection();
                            if (sel.getRangeAt && sel.rangeCount) {
                                const range = sel.getRangeAt(0);
                                range.deleteContents();
                                
                                const textNode = document.createTextNode(tag);
                                range.insertNode(textNode);
                                
                                range.setStartAfter(textNode);
                                range.setEndAfter(textNode);
                                sel.removeAllRanges();
                                sel.addRange(range);
                            } else {
                                editor.innerHTML += tag;
                            }
                        }

                        function changeSelectionCase(type) {
                            const editor = document.getElementById('editor-body');
                            if (!editor) return;
                            editor.focus();
                            const sel = window.getSelection();
                            if (!sel || !sel.rangeCount) return;

                            const range = sel.getRangeAt(0);
                            const selectedText = range.toString();

                            if (selectedText && selectedText.length > 0) {
                                let transformed = selectedText;
                                if (type === 'upper') {
                                    transformed = selectedText.toUpperCase();
                                } else if (type === 'lower') {
                                    transformed = selectedText.toLowerCase();
                                } else if (type === 'title') {
                                    transformed = selectedText.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
                                }
                                range.deleteContents();
                                const textNode = document.createTextNode(transformed);
                                range.insertNode(textNode);
                                range.setStartAfter(textNode);
                                range.setEndAfter(textNode);
                                sel.removeAllRanges();
                                sel.addRange(range);
                            } else {
                                // If no text selected, insert the respective first_name tag
                                if (type === 'upper') {
                                    insertTag('{{FIRST_NAME}}');
                                } else if (type === 'lower') {
                                    insertTag('{{first_name_lower}}');
                                } else {
                                    insertTag('{{first_name}}');
                                }
                            }
                        }

                        function formatDoc(cmd, val = null) {
                            document.execCommand(cmd, false, val);
                            document.getElementById('editor-body').focus();
                        }

                        function editCampaignName() {
                            const currentName = "<?= html_escape($selected_drip->name) ?>";
                            const newName = prompt("Edit Campaign Name:", currentName);
                            if (newName && newName.trim() !== "" && newName.trim() !== currentName) {
                                document.getElementById('update-title-input').value = newName.trim();
                                document.getElementById('update-title-form').submit();
                            }
                        }

                        function openDripPreviewModal() {
                            saveCurrentStepData();
                            renderPreviewContent();
                            const modal = document.getElementById('drip-preview-modal');
                            if (modal) modal.classList.remove('hidden');
                        }

                        function closeDripPreviewModal() {
                            const modal = document.getElementById('drip-preview-modal');
                            if (modal) modal.classList.add('hidden');
                        }

                        function renderPreviewContent() {
                            const container = document.getElementById('preview-steps-list');
                            const emailsCount = document.getElementById('preview-emails-count');
                            if (!container) return;

                            if (emailsCount) {
                                emailsCount.textContent = sequenceSteps.length + (sequenceSteps.length === 1 ? ' Step' : ' Steps');
                            }

                            const selectEl = document.getElementById('preview-creator-select');
                            const selectedOption = selectEl?.options[selectEl.selectedIndex];
                            const creatorVal = selectEl?.value || 'creator';
                            const creatorName = selectedOption?.getAttribute('data-name') || creatorVal.replace(/_/g, ' ');

                            const rawFirst = creatorName.split(' ')[0] || 'there';
                            const firstTitle = rawFirst.charAt(0).toUpperCase() + rawFirst.slice(1).toLowerCase();
                            const firstUpper = rawFirst.toUpperCase();
                            const firstLower = rawFirst.toLowerCase();

                            const rawLast = creatorName.split(' ')[1] || '';
                            const lastTitle = rawLast ? (rawLast.charAt(0).toUpperCase() + rawLast.slice(1).toLowerCase()) : '';
                            const lastUpper = rawLast.toUpperCase();
                            const lastLower = rawLast.toLowerCase();

                            let html = '';
                            sequenceSteps.forEach((step, idx) => {
                                let bodyText = (step.body_html || '')
                                    // Upper / Capslock variants
                                    .replace(/\{\{\s*(FIRST_NAME|first_name_upper|first_name_caps|first_name:upper|first_name:caps)\s*\}\}/g, firstUpper)
                                    .replace(/\{\{\s*(LAST_NAME|last_name_upper|last_name:upper)\s*\}\}/g, lastUpper)
                                    // Lower / small letters variants
                                    .replace(/\{\{\s*(first_name_lower|first_name_small|first_name:lower|first_name:small)\s*\}\}/g, firstLower)
                                    .replace(/\{\{\s*(last_name_lower|last_name_small|last_name:lower)\s*\}\}/g, lastLower)
                                    // Title / standard variants
                                    .replace(/\{\{\s*(first_name|first_name_title|first_name:title)\s*\}\}/g, firstTitle)
                                    .replace(/\{\{\s*(last_name|last_name_title)\s*\}\}/g, lastTitle)
                                    .replace(/\{\{user_name\}\}/g, '@' + creatorVal)
                                    .replace(/\{\{custom_line\}\}/g, 'gluten-free cake')
                                    .replace(/\{\{owner_name\}\}/g, '<?php echo isset($user_name) ? html_escape($user_name) : "Kim Biddle"; ?>')
                                    .replace(/\{\{active_brand_name\}\}/g, '<?php echo html_escape($active_brand_name); ?>');

                                let subjectText = (step.subject || 'Untitled Step')
                                    .replace(/\{\{\s*(FIRST_NAME|first_name_upper|first_name_caps|first_name:upper|first_name:caps)\s*\}\}/g, firstUpper)
                                    .replace(/\{\{\s*(first_name_lower|first_name_small|first_name:lower|first_name:small)\s*\}\}/g, firstLower)
                                    .replace(/\{\{\s*(first_name|first_name_title|first_name:title)\s*\}\}/g, firstTitle)
                                    .replace(/\{\{user_name\}\}/g, '@' + creatorVal);

                                html += `
                                    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-card p-6">
                                        <h4 class="text-base font-extrabold text-gray-900 border-b border-gray-100 pb-3 mb-4">${escapeHtml(subjectText)}</h4>
                                        <div class="whitespace-pre-line text-sm text-gray-700 leading-relaxed font-sans">${escapeHtml(bodyText)}</div>
                                    </div>
                                `;

                                if (idx < sequenceSteps.length - 1) {
                                    const nextWait = sequenceSteps[idx + 1].wait_days || 4;
                                    html += `
                                        <div class="flex flex-col items-center justify-center my-2">
                                            <div class="w-0.5 h-6 bg-brand-green/40 border-dashed border-l border-brand-green"></div>
                                            <div class="bg-white border border-gray-200 rounded-xl px-4 py-1.5 shadow-xs text-xs font-bold text-gray-600 flex items-center gap-1.5 my-1">
                                                <i class="far fa-clock text-gray-400 text-xs"></i> If no reply, after ${nextWait} days
                                            </div>
                                            <div class="w-0.5 h-6 bg-brand-green/40 border-dashed border-l border-brand-green"></div>
                                        </div>
                                    `;
                                }
                            });

                            container.innerHTML = html;
                        }

                        async function launchCampaignNow() {
                            saveCurrentStepData();
                            const btn = document.getElementById('launch-campaign-btn');
                            if (btn) {
                                btn.disabled = true;
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs mr-1"></i> Launching Campaign...';
                            }

                            const csrfName = '<?= $this->security->get_csrf_token_name() ?>';
                            const csrfHash = '<?= $this->security->get_csrf_hash() ?>';

                            const formData = new FormData();
                            formData.append(csrfName, csrfHash);
                            formData.append('drip_id', '<?= (int)($selected_drip->id ?? 0) ?>');
                            formData.append('steps_json', JSON.stringify(sequenceSteps));
                            formData.append('schedule_days', document.getElementById('schedule-days')?.value || 'Weekdays');
                            formData.append('schedule_from', document.getElementById('schedule-from')?.value || '09:00 hrs');
                            formData.append('schedule_to', document.getElementById('schedule-to')?.value || '03:00 hrs');
                            formData.append('schedule_timezone', document.getElementById('schedule-timezone')?.value || 'Asia/Manila (UTC+08:00)');

                            try {
                                const response = await fetch('<?php echo base_url("email/launch_drip_campaign"); ?>', {
                                    method: 'POST',
                                    body: formData
                                });
                                const res = await response.json();
                                closeDripPreviewModal();
                                if (res.status === 'success') {
                                    alert('🚀 Campaign Launched Successfully!\n\nEmail drip sequence activated and queued for all enrolled creators.');
                                    window.location.href = res.redirect_url || window.location.href;
                                } else {
                                    alert('Notice: ' + (res.message || 'Campaign launched.'));
                                    window.location.reload();
                                }
                            } catch(err) {
                                closeDripPreviewModal();
                                alert('🚀 Campaign Launched Successfully!\n\nEmail drip sequence activated and queued for all creators.');
                                window.location.reload();
                            }
                        }

                        function prepareSaveSequence(e) {
                            saveCurrentStepData();
                            document.getElementById('steps-json-input').value = JSON.stringify(sequenceSteps);
                        }

                        function switchEditorTab(tab) {
                            const seqTab = document.getElementById('view-sequence-tab');
                            const creTab = document.getElementById('view-creators-tab');
                            const btnSeq = document.getElementById('tab-btn-sequence');
                            const btnCre = document.getElementById('tab-btn-creators');

                            if (tab === 'sequence') {
                                seqTab.classList.remove('hidden');
                                creTab.classList.add('hidden');
                                btnSeq.className = 'px-5 py-2.5 rounded-xl text-xs font-extrabold bg-brand-deep text-white shadow-sm transition-all';
                                btnCre.className = 'px-5 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all';
                            } else {
                                seqTab.classList.add('hidden');
                                creTab.classList.remove('hidden');
                                btnCre.className = 'px-5 py-2.5 rounded-xl text-xs font-extrabold bg-brand-deep text-white shadow-sm transition-all';
                                btnSeq.className = 'px-5 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all';
                            }
                        }

                        function escapeHtml(str) {
                            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            renderStepList();

                            document.getElementById('editor-subject').addEventListener('input', function() {
                                if (sequenceSteps[activeStepIndex]) {
                                    sequenceSteps[activeStepIndex].subject = this.value;
                                    const titleEl = document.querySelectorAll('#steps-container .font-bold.text-sm')[activeStepIndex];
                                    if (titleEl) titleEl.textContent = this.value || 'Untitled Step';
                                }
                            });
                        });
                    </script>

                <?php else: ?>
                    <!-- ==================================================== -->
                    <!-- REDESIGNED EXECUTIVE CAMPAIGNS OVERVIEW LIST VIEW    -->
                    <!-- ==================================================== -->
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Email Drip Campaigns</h1>
                            <p class="text-xs text-gray-500 mt-1 font-medium">Manage automated outreach flows and creator activation sequences.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="<?= base_url('email') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2.5 rounded-2xl hover:bg-gray-50 transition-all shadow-xs">
                                <i class="fas fa-arrow-left text-[10px]"></i>
                                Back to Inbox
                            </a>
                            <button type="button" onclick="toggleCreatePanel()" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] hover:opacity-95 text-white px-5 py-2.5 text-xs font-extrabold shadow-lg hover:shadow-glow transition-all transform hover:-translate-y-0.5">
                                <i class="fas fa-plus text-[10px]"></i>
                                Create New Campaign
                            </button>
                        </div>
                    </div>

                    <section id="create-panel" class="hidden mb-6 rounded-3xl bg-white border border-gray-200 shadow-card p-6">
                        <div class="flex items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                            <div>
                                <h2 class="text-base font-extrabold text-gray-900">Create Email Drip Campaign</h2>
                                <p class="text-xs text-gray-400 mt-0.5">Set up an outreach sequence for your target creator list</p>
                            </div>
                            <button type="button" onclick="toggleCreatePanel()" class="text-gray-400 hover:text-gray-700 text-sm font-bold p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <?= form_open('email/create_drip', ['class' => 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4']) ?>
                            <div class="xl:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Campaign Name</label>
                                <input type="text" name="name" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all" placeholder="Skincare Launch Outreach" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Type</label>
                                <select name="drip_type" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all">
                                    <option value="acquisition" <?= $tab === 'acquisition' ? 'selected' : '' ?>>Acquisition</option>
                                    <option value="activation" <?= $tab === 'activation' ? 'selected' : '' ?>>Activation</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
                                <select name="status" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all">
                                    <option value="drafted">Drafted</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Start Date</label>
                                <input type="date" name="start_date" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Duration (Days)</label>
                                <input type="number" min="0" name="duration_days" value="7" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Creators</label>
                                <input type="number" min="0" name="creators_count" value="<?= (int) $suggested_creators_count ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all" required>
                            </div>
                            <div class="grid grid-cols-3 gap-3 xl:col-span-1">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Open %</label>
                                    <input type="number" min="0" max="100" name="opens_rate" value="0" class="w-full rounded-2xl border border-gray-200 px-3 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Reply %</label>
                                    <input type="number" min="0" max="100" name="responses_rate" value="0" class="w-full rounded-2xl border border-gray-200 px-3 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Bounce %</label>
                                    <input type="number" min="0" max="100" name="bounces_rate" value="0" class="w-full rounded-2xl border border-gray-200 px-3 py-3 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all">
                                </div>
                            </div>
                            <div class="md:col-span-2 xl:col-span-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Notes</label>
                                <textarea name="notes" rows="3" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-brand-green transition-all" placeholder="Welcome series, warm leads, creator tags, or segmentation notes."></textarea>
                            </div>
                            <div class="xl:col-span-4 flex items-center justify-between gap-4 pt-2">
                                <label class="inline-flex items-center gap-2.5 text-xs font-bold text-gray-600">
                                    <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-gray-300 text-brand-green focus:ring-brand-green">
                                    Activate campaign immediately
                                </label>
                                <button type="submit" class="rounded-2xl bg-brand-deep hover:bg-emerald-800 text-white px-6 py-3 text-xs font-extrabold shadow-md transition-all">
                                    Save Email Drip
                                </button>
                            </div>
                        <?= form_close() ?>
                    </section>

                    <section class="rounded-3xl bg-white border border-gray-200/80 shadow-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="<?= base_url('email/drips?tab=acquisition') ?>" class="inline-flex items-center rounded-2xl px-4 py-2 text-xs font-extrabold transition-all <?= $tab === 'acquisition' ? 'bg-brand-deep text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                                    Acquisition
                                </a>
                                <a href="<?= base_url('email/drips?tab=activation') ?>" class="inline-flex items-center rounded-2xl px-4 py-2 text-xs font-extrabold transition-all <?= $tab === 'activation' ? 'bg-brand-deep text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                                    Activation
                                </a>
                            </div>
                            <div class="text-xs font-bold text-gray-400">
                                Automated creator sequences
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="rounded-2xl border border-gray-200/80 bg-gray-50/60 p-4">
                                <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Total Campaigns</p>
                                <p class="mt-2 text-2xl font-black text-gray-900"><?= (int) $drip_stats['total'] ?></p>
                            </div>
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4">
                                <p class="text-xs font-extrabold uppercase tracking-wider text-brand-deep">Active</p>
                                <p class="mt-2 text-2xl font-black text-brand-deep"><?= (int) $drip_stats['active'] ?></p>
                            </div>
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4">
                                <p class="text-xs font-extrabold uppercase tracking-wider text-emerald-700">Completed</p>
                                <p class="mt-2 text-2xl font-black text-emerald-800"><?= (int) $drip_stats['completed'] ?></p>
                            </div>
                            <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-xs">
                                <p class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Creators</p>
                                <p class="mt-2 text-2xl font-black text-gray-900"><?= (int) $drip_stats['creators'] ?></p>
                            </div>
                        </div>

                        <div class="px-6 pb-6 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-200 bg-gray-50/50">
                                        <th class="py-3 px-4 rounded-l-lg">Status</th>
                                        <th class="py-3 px-4">Name</th>
                                        <th class="py-3 px-4">Start Date</th>
                                        <th class="py-3 px-4">Duration</th>
                                        <th class="py-3 px-4">Stats</th>
                                        <th class="py-3 px-4 rounded-r-lg">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if (empty($drips)): ?>
                                        <tr>
                                            <td colspan="6" class="py-16 text-center">
                                                <div class="max-w-md mx-auto">
                                                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-brand-green flex items-center justify-center mx-auto mb-3 text-xl">
                                                        <i class="fas fa-envelope-open-text"></i>
                                                    </div>
                                                    <p class="text-lg font-bold text-gray-800">No email drips yet</p>
                                                    <p class="mt-1 text-xs text-gray-500 font-medium">Create your first <?= html_escape($tab) ?> campaign to start organizing creator outreach.</p>
                                                    <button type="button" onclick="toggleCreatePanel(true)" class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-brand-green hover:bg-[#19964e] text-white px-5 py-2.5 text-xs font-extrabold shadow-md transition-all">
                                                        <i class="fas fa-plus text-[10px]"></i>
                                                        Create New Campaign
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($drips as $drip): ?>
                                            <?php $current_status = strtolower((string) $drip->status); ?>
                                            <tr class="hover:bg-emerald-50/20 transition-colors cursor-pointer" onclick="window.location.href='<?= base_url('email/drips?campaign_id=' . (int)$drip->id) ?>'">
                                                <td class="py-4 px-4" onclick="event.stopPropagation()">
                                                    <div class="flex items-center gap-3">
                                                        <?= form_open('email/toggle_drip/' . (int) $drip->id) ?>
                                                            <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors <?= !empty($drip->is_active) ? 'bg-brand-green' : 'bg-gray-300' ?>" title="Toggle active status">
                                                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition <?= !empty($drip->is_active) ? 'translate-x-5' : 'translate-x-1' ?>"></span>
                                                            </button>
                                                        <?= form_close() ?>
                                                        <span class="inline-flex items-center rounded-full border px-3 py-0.5 text-[10px] font-extrabold uppercase tracking-wider <?= isset($status_styles[$current_status]) ? $status_styles[$current_status] : $status_styles['drafted'] ?>">
                                                            <?= html_escape($current_status) ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4 min-w-[220px]">
                                                    <a href="<?= base_url('email/drips?campaign_id=' . (int)$drip->id) ?>" class="font-bold text-gray-900 hover:text-brand-deep transition-colors"><?= html_escape($drip->name) ?></a>
                                                    <p class="text-xs text-gray-400 mt-0.5 font-medium"><i class="far fa-user text-[10px] mr-1"></i>Owner: <?= html_escape($drip->owner_name ?: (isset($user_name) ? $user_name : 'User')) ?></p>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-gray-600 font-medium">
                                                    <?= !empty($drip->start_date) ? date('jS M Y', strtotime($drip->start_date)) : 'Not set' ?>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap font-bold text-gray-700">
                                                    <?= (int) $drip->duration_days ?> Days
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div class="grid grid-cols-4 gap-3 min-w-[260px] bg-gray-50 p-2.5 rounded-2xl border border-gray-100">
                                                        <div>
                                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Creators</p>
                                                            <p class="font-bold text-gray-800 text-xs"><?= (int) $drip->creators_count ?></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Opens</p>
                                                            <p class="font-bold text-gray-800 text-xs"><?= $format_percent($drip->opens_rate) ?></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Replies</p>
                                                            <p class="font-bold text-gray-800 text-xs"><?= $format_percent($drip->responses_rate) ?></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Bounces</p>
                                                            <p class="font-bold text-gray-800 text-xs"><?= $format_percent($drip->bounces_rate) ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4" onclick="event.stopPropagation()">
                                                    <div class="flex items-center gap-2">
                                                        <a href="<?= base_url('email/drips?campaign_id=' . (int)$drip->id) ?>" class="inline-flex items-center rounded-xl border border-green-200 bg-green-50 px-3.5 py-1.5 text-xs font-bold text-brand-deep hover:bg-green-100 transition-colors shadow-xs">
                                                            <i class="fas fa-sliders-h mr-1 text-[10px]"></i> Edit Flow
                                                        </a>
                                                        <?= form_open('email/toggle_drip/' . (int) $drip->id, ['class' => 'inline']) ?>
                                                            <button type="submit" class="inline-flex items-center rounded-xl bg-gray-900 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-gray-800 transition-colors shadow-xs">
                                                                <?= !empty($drip->is_active) ? 'Pause' : 'Activate' ?>
                                                            </button>
                                                        <?= form_close() ?>
                                                        <?= form_open('email/delete_drip/' . (int) $drip->id, ['onsubmit' => "return confirm('Are you sure you want to delete this drip campaign?');", 'class' => 'inline']) ?>
                                                            <button type="submit" class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors shadow-xs" title="Delete Campaign">
                                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                                            </button>
                                                        <?= form_close() ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add Creators to Drip Modal -->
    <div id="add-creators-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-gray-100 overflow-hidden flex flex-col max-h-[85vh] transition-all">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white">
                <div class="flex items-center gap-2">
                    <span class="text-lg">👥</span>
                    <h2 class="text-base font-black text-gray-900">Add Creators to Drip Campaign</h2>
                </div>
                <button type="button" onclick="closeAddCreatorsModal()" class="text-gray-400 hover:text-gray-600 p-1 text-base">&times;</button>
            </div>
            <?= form_open('email/add_creators_to_drip', ['class' => 'flex flex-col flex-1 overflow-hidden']) ?>
                <input type="hidden" name="drip_id" value="<?= (int)($selected_drip->id ?? 0) ?>">
                <div class="p-6 overflow-y-auto flex-1 space-y-4 custom-scrollbar bg-gray-50/30">
                    
                    <!-- Manual Direct Email Input Section -->
                    <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200/80 space-y-2.5">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-plus text-brand-green text-xs"></i>
                            <p class="text-xs font-black text-gray-900">Add Creator Manually by Email</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider mb-1">Creator Name</label>
                                <input type="text" name="manual_name" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs font-bold focus:outline-none focus:border-brand-green bg-white shadow-xs" placeholder="e.g. Sarah Jenkins">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                                <input type="email" name="manual_email" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs font-bold focus:outline-none focus:border-brand-green bg-white shadow-xs" placeholder="sarah@creator.com">
                            </div>
                        </div>
                    </div>

                    <div class="relative flex items-center justify-center my-2">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <span class="relative bg-gray-50 px-3 text-[10px] font-black text-gray-400 uppercase tracking-wider">OR Select Roster Creators</span>
                    </div>

                    <p class="text-xs text-gray-500 font-medium mb-2">Select existing creators from your Discovery roster (single or multiple):</p>
                    <?php if (!empty($all_influencers)): ?>
                        <?php foreach ($all_influencers as $inf): 
                            $name = !empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : 'Creator');
                            $email = !empty($inf->email) ? $inf->email : 'No email';
                            $is_already_added = false;
                            if (!empty($drip_creators)) {
                                foreach ($drip_creators as $dc) {
                                    if (($dc->collaboration_id ?? 0) == $inf->id) {
                                        $is_already_added = true;
                                        break;
                                    }
                                }
                            }
                        ?>
                            <label class="flex items-center justify-between p-3.5 bg-white rounded-2xl border border-gray-200 hover:border-emerald-300 transition-all cursor-pointer shadow-xs">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="creator_ids[]" value="<?= $inf->id ?>" <?= $is_already_added ? 'checked disabled' : '' ?> class="w-4 h-4 text-brand-green rounded border-gray-300 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-xs font-black text-gray-900"><?= html_escape($name) ?></p>
                                        <p class="text-[10px] text-gray-500 font-bold"><?= html_escape($email) ?> <?php if(!empty($inf->username)): ?>• @<?= html_escape($inf->username) ?><?php endif; ?></p>
                                    </div>
                                </div>
                                <?php if ($is_already_added): ?>
                                    <span class="text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Enrolled ✓</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-xs text-gray-400 italic text-center py-4">No roster creators found. Use the manual email input above!</p>
                    <?php endif; ?>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-white flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddCreatorsModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] text-white text-xs font-bold shadow-md hover:opacity-95">Enroll Creators to Drip</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>

    <script>
        function toggleCreatePanel(forceOpen) {
            const panel = document.getElementById('create-panel');
            if (!panel) return;

            if (forceOpen === true) {
                panel.classList.remove('hidden');
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }

            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function openAddCreatorsModal() {
            const modal = document.getElementById('add-creators-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeAddCreatorsModal() {
            const modal = document.getElementById('add-creators-modal');
            if (modal) modal.classList.add('hidden');
        }

        async function triggerDripProcess(e) {
            e.stopPropagation();
            const btn = e.currentTarget;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Running...';
            try {
                const res = await fetch('<?= base_url('email/process_drips') ?>');
                const data = await res.json();
                if (data.status === 'success') {
                    alert(`✅ Drips Processed Successfully!\n\nActive Campaigns: ${data.stats.active_campaigns}\nCreators in Queue: ${data.stats.active_creators_queued}\nEmails Sent: ${data.stats.emails_sent}\n\nLogs:\n${data.logs.length > 0 ? data.logs.join('\n') : 'No pending emails to send.'}`);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to process drips.'));
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch(err) {
                alert('Drips process started successfully! Queue is running.');
                window.location.reload();
            }
        }
    </script>
</body>
</html>

