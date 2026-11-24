<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compose Email - Instroom</title>
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

    // Prep variables for template insertion
    $brand_name = $active_brand_name;
    $raw_sender = $this->session->userdata('name') ?: ($this->session->userdata('user_name') ?: '');
    if (empty($raw_sender) && !empty($accounts) && !empty($accounts[0]->display_name) && strpos($accounts[0]->display_name, '@') === false) {
        $raw_sender = $accounts[0]->display_name;
    }
    if (empty($raw_sender)) {
        $raw_sender = 'Edrian Benedict';
    }

    $owner_name = $raw_sender;
    if (strpos($raw_sender, ',') !== false) {
        $p = explode(',', $raw_sender, 2);
        $first_part = trim($p[1]);
        $clean_first = preg_replace('/\s+[A-Z]\.?$/', '', $first_part);
        $owner_name = ucwords(strtolower($clean_first ?: $first_part));
    } else {
        $owner_name = ucwords(strtolower($raw_sender));
    }
?>

    <!-- Sidebar -->
    <?php $this->load->view('partials/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-[#F8FAFC]">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-20 sticky top-0 shadow-xs">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('email') ?>" class="text-gray-500 hover:text-gray-700 p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <h1 class="text-lg font-bold text-gray-900 tracking-tight">
                    Compose New Message
                </h1>
            </div>
        </header>

        <!-- Main Body -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Left Sub-Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 flex flex-col p-5 space-y-6 hidden lg:flex">
                <!-- Connected Email Account Card -->
                <div>
                    <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Folders</p>
                    <nav class="space-y-1">
                        <a href="<?= base_url('email') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 font-semibold text-xs transition-all">
                            <span class="flex items-center gap-2.5"><i class="fas fa-inbox text-gray-400 text-sm"></i> Inbox</span>
                        </a>
                        <a href="<?= base_url('email/drips') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 font-semibold text-xs transition-all">
                            <span class="flex items-center gap-2.5"><i class="fas fa-bolt text-gray-400 text-sm"></i> Drip Campaigns</span>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Conversation Compose Main Pane -->
            <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto flex justify-center">
                <div class="w-full max-w-3xl">
                    <form id="compose-form" class="bg-white rounded-2xl border border-gray-200 shadow-card overflow-hidden flex flex-col">
                        <!-- Form Inputs -->
                        <div class="p-6 space-y-4">
                            <!-- From Account Selection -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <label class="w-24 text-xs font-bold text-gray-400 uppercase tracking-wider">From</label>
                                <select name="account_id" class="flex-1 bg-gray-50 border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl px-3 py-2 text-xs font-semibold outline-none transition-all">
                                    <?php foreach ($accounts as $account): ?>
                                        <option value="<?= $account->id ?>" <?= $account->is_primary ? 'selected' : '' ?>><?= html_escape($account->email_address) ?> (<?= html_escape($account->provider) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- To Email Address -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <label class="w-24 text-xs font-bold text-gray-400 uppercase tracking-wider">To</label>
                                <input type="email" name="to" required value="<?= $to ?? '' ?>" class="flex-1 bg-gray-50 border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl px-3 py-2 text-xs font-semibold outline-none transition-all" placeholder="influencer@example.com">
                            </div>

                            <!-- Associated Influencer Link -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <label class="w-24 text-xs font-bold text-gray-400 uppercase tracking-wider">Influencer</label>
                                <select name="influencer_id" class="flex-1 bg-gray-50 border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl px-3 py-2 text-xs font-semibold outline-none transition-all">
                                    <option value="">Select associated influencer (optional)</option>
                                    <?php if (!empty($influencers)): ?>
                                        <?php foreach ($influencers as $inf): ?>
                                            <option value="<?= $inf->id ?>" <?= ($influencer_id ?? '') == $inf->id ? 'selected' : '' ?>><?= htmlspecialchars($inf->name ?? $inf->email ?? 'Unknown') ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Associated Campaign Link -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <label class="w-24 text-xs font-bold text-gray-400 uppercase tracking-wider">Campaign</label>
                                <select name="campaign_id" class="flex-1 bg-gray-50 border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl px-3 py-2 text-xs font-semibold outline-none transition-all">
                                    <option value="">Select associated campaign (optional)</option>
                                    <?php if (!empty($campaigns)): ?>
                                        <?php foreach ($campaigns as $camp): ?>
                                            <option value="<?= $camp->id ?>" <?= ($campaign_id ?? '') == $camp->id ? 'selected' : '' ?>><?= htmlspecialchars($camp->name ?? 'Unknown') ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Templates Quick Selector -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <label class="w-24 text-xs font-bold text-gray-400 uppercase tracking-wider">Template</label>
                                <select id="template-select" class="flex-1 bg-gray-50 border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl px-3 py-2 text-xs font-semibold outline-none transition-all">
                                    <option value="">No template (Start writing from scratch)</option>
                                    <?php if (!empty($templates)): ?>
                                        <?php foreach ($templates as $template): ?>
                                            <option value="<?= $template->id ?>" data-subject="<?= htmlspecialchars($template->subject) ?>" data-body="<?= htmlspecialchars($template->body_html ?: $template->body_plain) ?>"><?= htmlspecialchars($template->name) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Subject Header Line -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <label class="w-24 text-xs font-bold text-gray-400 uppercase tracking-wider">Subject</label>
                                <input type="text" name="subject" value="<?= $subject ?? '' ?>" class="flex-1 bg-gray-50 border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl px-3 py-2 text-xs font-semibold outline-none transition-all" placeholder="Enter message subject line...">
                            </div>

                            <!-- Message Text Body Box -->
                            <div class="pt-2">
                                <textarea id="email-body" name="body" class="w-full min-h-[250px] bg-gray-50 border border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 rounded-xl p-4 text-xs font-medium outline-none resize-none transition-all" placeholder="Type your email message text here..."></textarea>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                            <button type="button" onclick="history.back()" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-200 rounded-xl transition-all">
                                Cancel
                            </button>
                            <button type="button" id="send-btn" class="bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] hover:opacity-95 text-white px-5 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 shadow-md transition-all">
                                <i class="fas fa-paper-plane text-[10px]"></i>
                                Send Email
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Script handlers -->
    <script>
        // Safely encode lookup objects using json_encode to prevent JS syntax/parsing errors
        const INFLUENCER_LOOKUP = {
            <?php if (!empty($influencers)): ?>
                <?php foreach ($influencers as $inf): ?>
                    <?= json_encode($inf->id) ?>: <?= json_encode(!empty($inf->first_name) ? $inf->first_name : (!empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : ''))) ?>,
                <?php endforeach; ?>
            <?php endif; ?>
        };

        function updateTemplateFields() {
            try {
                const templateSelect = document.getElementById('template-select');
                if (!templateSelect) return;
                const option = templateSelect.options[templateSelect.selectedIndex];
                if (!option || !option.value) return;
                
                let subject = option.getAttribute('data-subject') || '';
                let body = option.getAttribute('data-body') || '';
                
                // Determine target influencer name
                const influencerSelect = document.querySelector('select[name="influencer_id"]');
                const infId = influencerSelect ? influencerSelect.value : '';
                const fullName = INFLUENCER_LOOKUP[infId] || 'there';
                const firstName = fullName !== 'there' ? fullName.split(' ')[0] : 'there';
                
                // Get active connection display details (fallback contact email)
                const fromSelect = document.querySelector('select[name="account_id"]');
                const activeEmail = fromSelect && fromSelect.options[fromSelect.selectedIndex] 
                    ? fromSelect.options[fromSelect.selectedIndex].text.split(' ')[0] 
                    : '';

                const rawFirst = firstName !== 'there' ? firstName : 'there';
                const firstTitle = rawFirst.charAt(0).toUpperCase() + rawFirst.slice(1).toLowerCase();
                const firstUpper = rawFirst.toUpperCase();
                const firstLower = rawFirst.toLowerCase();

                // Map all database placeholders to the frontend variables
                const vars = {
                    'brand_name': <?= json_encode($brand_name) ?>,
                    'influencer_name': firstTitle,
                    'first_name': firstTitle,
                    'FIRST_NAME': firstUpper,
                    'first_name_upper': firstUpper,
                    'first_name_caps': firstUpper,
                    'first_name_lower': firstLower,
                    'first_name_small': firstLower,
                    'first_name_title': firstTitle,
                    'name': firstTitle,
                    'user_name': firstTitle,
                    'username': firstTitle,
                    'sender_name': <?= json_encode($owner_name) ?>,
                    'owner_name': <?= json_encode($owner_name) ?>,
                    'sender_title': 'Partnership Manager',
                    'contact_email': activeEmail,
                    'contact_phone': '',
                    'platform': 'Instagram/TikTok',
                    'collaboration_details': 'exclusive gifted products and paid partnership collaboration',
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
                
                // Replace placeholders with longest keys first
                const sortedKeys = Object.keys(vars).sort((a, b) => b.length - a.length);
                for (const key of sortedKeys) {
                    const val = vars[key];
                    const bracketRegex = new RegExp('{{' + key + '}}', 'g');
                    subject = subject.replace(bracketRegex, val);
                    body = body.replace(bracketRegex, val);
                    
                    // Also replace raw keys (fallback case)
                    const rawRegex = new RegExp('{' + key + '}', 'g');
                    subject = subject.replace(rawRegex, val);
                    body = body.replace(rawRegex, val);
                }
                
                // Convert HTML line breaks to carriage returns for textarea styling
                let formattedBody = body.replace(/<br\s*\/?>/gi, '\n');
                
                // Strip remaining HTML tags
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = formattedBody;
                const textContent = tempDiv.textContent || tempDiv.innerText || '';
                
                document.querySelector('input[name="subject"]').value = subject;
                document.getElementById('email-body').value = textContent.trim();
            } catch (err) {
                console.error("Error in updateTemplateFields:", err);
            }
        }

        // Hook selection change events
        document.getElementById('template-select').addEventListener('change', updateTemplateFields);
        
        const influencerSelect = document.querySelector('select[name="influencer_id"]');
        if (influencerSelect) {
            influencerSelect.addEventListener('change', function() {
                const templateSelect = document.getElementById('template-select');
                if (templateSelect && templateSelect.value) {
                    updateTemplateFields();
                }
            });
        }

        // Trigger updates dynamically on changing from/accounts
        const fromSelect = document.querySelector('select[name="account_id"]');
        if (fromSelect) {
            fromSelect.addEventListener('change', function() {
                const templateSelect = document.getElementById('template-select');
                if (templateSelect && templateSelect.value) {
                    updateTemplateFields();
                }
            });
        }

        // Send Email submission logic
        document.getElementById('send-btn').addEventListener('click', async function() {
            const form = document.getElementById('compose-form');
            if (!form.reportValidity()) return;

            const formData = new FormData(form);
            const sendBtn = this;
            const originalHTML = sendBtn.innerHTML;

            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                const response = await fetch('<?= base_url('email/send_email') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alert('Email sent successfully!');
                    window.location.href = '<?= base_url('email') ?>';
                } else {
                    alert('Error: ' + (result.message || 'Failed to send email'));
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = originalHTML;
                }
            } catch (e) {
                alert('Error sending email: ' + e.message);
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalHTML;
            }
        });
    </script>
</body>
</html>
