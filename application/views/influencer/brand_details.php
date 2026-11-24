<?php
$default_sop = "### 📋 Content Guidelines\n"
             . "- **DO**: Show the product/logo clearly within the first 3 seconds of the video.\n"
             . "- **DO**: Use the brand hashtags and tag the official brand account in the caption.\n"
             . "- **DON'T**: Feature or mention competing brands in the post.\n"
             . "- **DON'T**: Mention sensitive topics or pricing discounts unless pre-approved.\n\n"
             . "### 🔄 Review & Approval Workflow\n"
             . "1. **Draft Submission**: Submit video/image drafts via Instroom at least 72 hours before the scheduled posting time.\n"
             . "2. **Quality Review**: Agency managers will check video framing, audio copyrights, and caption tags.\n"
             . "3. **Final Sign-off**: Brand manager will approve the draft or request revisions.\n"
             . "4. **Posting**: Publish only after receiving the \"Approved\" status in the pipeline.";

$display_sop = !empty($brand_sop) ? $brand_sop : $default_sop;

if (!function_exists('parse_sop_to_html')) {
    function parse_sop_to_html($text) {
        $lines = explode("\n", $text);
        $html = '';
        $in_list = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Headers (### Header)
            if (strpos($line, '###') === 0) {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                $header_text = trim(substr($line, 3));
                $html .= '<h4 class="text-base font-extrabold text-gray-900 mt-6 mb-3 flex items-center gap-2">' . htmlspecialchars($header_text) . '</h4>';
            } 
            // List items with bold prefixes (e.g., - **DO**: or - **DON'T**:)
            elseif (strpos($line, '-') === 0) {
                if (!$in_list) {
                    $html .= '<ul class="space-y-3 pl-1">';
                    $in_list = true;
                }
                $item_text = trim(substr($line, 1));
                
                // Format bold tags
                $item_text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-extrabold text-gray-900">$1</strong>', $item_text);
                
                // Check for DO vs DONT
                if (stripos($item_text, 'DO') !== false && stripos($item_text, 'DON\'T') === false) {
                    $html .= '<li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed"><span class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-black mt-0.5"><i class="fas fa-check"></i></span><span>' . $item_text . '</span></li>';
                } elseif (stripos($item_text, 'DON\'T') !== false || stripos($item_text, 'DONT') !== false) {
                    $html .= '<li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed"><span class="flex-shrink-0 w-5 h-5 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-[10px] font-black mt-0.5"><i class="fas fa-times"></i></span><span>' . $item_text . '</span></li>';
                } else {
                    $html .= '<li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed"><span class="flex-shrink-0 w-2 h-2 rounded-full bg-gray-400 mt-2 font-black"></span><span>' . $item_text . '</span></li>';
                }
            }
            // Ordered List (1. **Draft Submission**)
            elseif (preg_match('/^\d+\./', $line)) {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                // Extract number and text
                $parts = explode('.', $line, 2);
                $num = trim($parts[0]);
                $item_text = trim($parts[1]);
                
                // Format bold tags
                $item_text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-extrabold text-gray-900">$1</strong>', $item_text);
                
                $html .= '<div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-md transition-all duration-300 mt-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-xl bg-brand-green/10 text-brand-green flex items-center justify-center font-black text-sm">' . $num . '</span>
                            <div class="text-sm text-gray-600 leading-relaxed">' . $item_text . '</div>
                          </div>';
            }
            // Normal paragraph
            else {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                $line = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-extrabold text-gray-900">$1</strong>', $line);
                $html .= '<p class="text-sm text-gray-600 leading-relaxed my-2">' . $line . '</p>';
            }
        }
        
        if ($in_list) {
            $html .= '</ul>';
        }
        
        return $html;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($brand_name) ?> | Brand Profile & Strategy</title>
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
                            dark: '#111827',
                            gray: '#F9FAFB',
                            border: '#E5E7EB',
                            blue: '#3B82F6',
                            purple: '#8B5CF6',
                            rose: '#F43F5E',
                            amber: '#F59E0B'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#F8FAFC] h-screen flex overflow-hidden text-slate-800 font-sans">

    <?php $this->load->view('partials/sidebar'); ?>
    <?php $this->load->view('partials/toast'); ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-white">
        <!-- Top Sticky Header -->
        <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 z-20 sticky top-0 shadow-sm">
            <div class="flex items-center text-slate-500 text-sm font-medium">
                <a href="<?= base_url('ambassadors') ?>" class="text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-users text-xs"></i> Ambassadors
                </a>
                <i class="fas fa-chevron-right text-[10px] mx-3 text-slate-300"></i>
                <span class="text-brand-deep font-bold bg-emerald-50 text-emerald-800 px-3 py-1 rounded-full text-xs flex items-center gap-1.5 border border-emerald-100">
                    <i class="fas fa-building text-[10px] text-brand-green"></i> <?= htmlspecialchars($brand_name) ?>
                </span>
            </div>

            <!-- Brand Switcher & Quick Actions -->
            <div class="flex items-center gap-3">
                <!-- In-page Brand Switcher Dropdown -->
                <div class="relative" id="brand-switcher-wrapper">
                    <button type="button" onclick="toggleBrandDropdown()" id="brand-switcher-btn" class="flex items-center gap-2 px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition-all shadow-sm">
                        <i class="fas fa-layer-group text-brand-green"></i>
                        <span>Switch Brand</span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                    </button>
                    <div id="brand-switcher-menu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Available Brands</p>
                        </div>
                        <div class="max-h-60 overflow-y-auto py-1">
                            <?php if(!empty($all_brands)): ?>
                                <?php foreach($all_brands as $b): ?>
                                    <a href="<?= base_url('ambassadors/brand_details/' . $b->id) ?>" class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 hover:bg-emerald-50 hover:text-brand-deep transition-colors <?= ($b->id == $brand_id) ? 'bg-emerald-50/60 font-bold text-brand-deep' : '' ?>">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-[10px]">
                                                <?= strtoupper(substr($b->name, 0, 1)) ?>
                                            </span>
                                            <span class="truncate"><?= htmlspecialchars($b->name) ?></span>
                                        </div>
                                        <?php if($b->id == $brand_id): ?>
                                            <i class="fas fa-check text-brand-green text-xs"></i>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="px-4 py-2 text-xs text-slate-400">No other brands</p>
                            <?php endif; ?>
                        </div>
                        <div class="border-t border-slate-100 mt-1 pt-1 px-2">
                            <button onclick="openEditBrandModal(true)" class="w-full text-left px-3 py-2 text-xs font-bold text-brand-deep hover:bg-emerald-50 rounded-xl transition-colors flex items-center gap-2">
                                <i class="fas fa-plus-circle text-brand-green"></i> Add / Edit Brand
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Set Active Brand Button -->
                <?php if ($this->session->userdata('brand_id') != $brand_id): ?>
                    <a href="<?= base_url('auth/switch_brand/' . $brand_id) ?>" class="px-3 py-1.5 bg-brand-green hover:bg-brand-deep text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-1.5" title="Set as your active workspace brand">
                        <i class="fas fa-check-circle"></i> Set Active
                    </a>
                <?php else: ?>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                        <i class="fas fa-circle text-[6px] text-brand-green animate-pulse"></i> Active Workspace
                    </span>
                <?php endif; ?>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50/60 p-6 md:p-8">
            <div class="max-w-6xl mx-auto space-y-8">
                
                <!-- Brand Header Banner -->
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-gradient-to-br from-brand-green/10 to-transparent rounded-full blur-2xl pointer-events-none"></div>

                    <div class="flex flex-col md:flex-row items-center gap-6 w-full md:w-auto text-center md:text-left">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-3xl bg-gradient-to-tr from-[#0F6B3E] to-[#1FAE5B] flex items-center justify-center text-white text-3xl font-extrabold shadow-lg shadow-emerald-500/20 tracking-wider">
                                <?= (strlen($brand_name) > 3) ? strtoupper(substr($brand_name, 0, 3)) : strtoupper($brand_name) ?>
                            </div>
                            <button onclick="openEditBrandModal()" class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-white text-slate-600 shadow-md border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors" title="Edit Brand Profile">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                                <h1 class="text-2xl md:text-3xl font-black text-slate-900"><?= htmlspecialchars($brand_name) ?></h1>
                                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[11px] font-black rounded-full border border-emerald-100 uppercase tracking-wider">
                                    <?= htmlspecialchars(!empty($brand->industry) ? $brand->industry : 'Brand Partner') ?>
                                </span>
                            </div>
                            <p class="text-slate-500 text-xs md:text-sm mt-1.5">Strategic Promotion Plan, Creator SOP & Ambassador Management</p>
                            
                            <!-- Badges -->
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mt-3.5">
                                <?php if(!empty($brand->contact_person)): ?>
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg flex items-center gap-1.5">
                                        <i class="fas fa-user-tie text-slate-400 text-[10px]"></i> <?= htmlspecialchars($brand->contact_person) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if(!empty($brand->email)): ?>
                                    <a href="mailto:<?= htmlspecialchars($brand->email) ?>" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg flex items-center gap-1.5 transition-colors">
                                        <i class="fas fa-envelope text-slate-400 text-[10px]"></i> <?= htmlspecialchars($brand->email) ?>
                                    </a>
                                <?php endif; ?>
                                <?php if(!empty($brand->budget_range)): ?>
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg border border-amber-100 flex items-center gap-1">
                                        <i class="fas fa-wallet text-amber-500 text-[10px]"></i> Budget: $<?= htmlspecialchars($brand->budget_range) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-3 w-full md:w-auto">
                        <button onclick="openEditBrandModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                            <i class="fas fa-cog text-slate-400"></i> Edit Brand
                        </button>
                        <button onclick="openProposalModal()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-deep to-brand-green hover:opacity-95 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-2">
                            <i class="fas fa-file-invoice"></i> View Full Proposal
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left 2 Cols: Matched Creators & Brand SOP -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <!-- Assigned Creators Card -->
                        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm">
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                                <div>
                                    <div class="flex items-center gap-2.5">
                                        <h2 class="text-lg font-black text-slate-900">Assigned Creators</h2>
                                        <span class="px-2 py-0.5 bg-emerald-50 text-brand-deep text-xs font-black rounded-full border border-emerald-100">
                                            <?= count($assigned_creators) ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 font-medium mt-0.5">Creators actively matched with <?= htmlspecialchars($brand_name) ?></p>
                                </div>
                                <button onclick="openMatchCreatorModal()" class="px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-deep text-xs font-extrabold transition-all border border-emerald-100 flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-plus text-xs"></i> Match New Creator
                                </button>
                            </div>

                            <!-- List of Assigned Creators -->
                            <?php if(!empty($assigned_creators)): ?>
                                <div class="space-y-3.5">
                                    <?php foreach($assigned_creators as $inf): ?>
                                        <div class="p-4 rounded-2xl bg-white border border-slate-100 hover:border-emerald-300 hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group">
                                            <div class="flex items-center gap-4">
                                                <div class="relative">
                                                    <img src="<?= !empty($inf->profile_image) ? htmlspecialchars($inf->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode(!empty($inf->username) ? $inf->username : ($inf->full_name ?? 'Creator')) . '&background=0F6B3E&color=fff' ?>" class="w-12 h-12 rounded-2xl object-cover shadow-sm border border-slate-100">
                                                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-white border border-slate-100 flex items-center justify-center text-[10px] text-brand-green shadow-xs">
                                                        <?php 
                                                            $p = strtolower($inf->platform ?? 'instagram');
                                                            if (strpos($p, 'tiktok') !== false) echo '<i class="fab fa-tiktok"></i>';
                                                            elseif (strpos($p, 'youtube') !== false) echo '<i class="fab fa-youtube text-red-500"></i>';
                                                            else echo '<i class="fab fa-instagram text-pink-500"></i>';
                                                        ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="font-bold text-slate-900 group-hover:text-brand-deep transition-colors text-sm">
                                                            <?= htmlspecialchars(!empty($inf->username) ? '@' . $inf->username : ($inf->full_name ?? 'Creator')) ?>
                                                        </h4>
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider <?= ($inf->pipeline_status == 'Won' || $inf->pipeline_status == 'Active') ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                                                            <?= htmlspecialchars($inf->pipeline_status ?? 'Active') ?>
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-slate-400 mt-0.5">
                                                        <?= htmlspecialchars($inf->full_name ?? 'Lifestyle Creator') ?> 
                                                        <?php if(!empty($inf->followers)): ?>
                                                            &bull; <?= number_format($inf->followers) ?> followers
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-50">
                                                <div class="text-right sm:pr-4 sm:border-r border-slate-100">
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Est. GMV</p>
                                                    <p class="text-sm font-black text-slate-800">$<?= number_format((float)($inf->gmv ?? 3500), 0) ?></p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <!-- Direct Email Link -->
                                                    <?php $recip_email = !empty($inf->email) ? $inf->email : ($inf->username . '@creator.mail'); ?>
                                                    <a href="<?= base_url('email/compose?recipient=' . urlencode($recip_email) . '&subject=' . urlencode('Collaboration with ' . $brand_name)) ?>" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-brand-deep hover:text-white text-slate-500 flex items-center justify-center transition-all" title="Send Email">
                                                        <i class="fas fa-envelope text-xs"></i>
                                                    </a>
                                                    <!-- Pipeline Link -->
                                                    <a href="<?= base_url('influencer/pipeline') ?>" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-500 flex items-center justify-center transition-all" title="View in Pipeline">
                                                        <i class="fas fa-filter text-xs"></i>
                                                    </a>
                                                    <!-- Unlink Creator -->
                                                    <button type="button" onclick="confirmUnlinkCreator(<?= (int)$inf->collab_id ?>, '<?= htmlspecialchars(addslashes($inf->username ?? 'Creator')) ?>')" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-rose-50 hover:text-rose-600 text-slate-400 flex items-center justify-center transition-all" title="Unlink from Brand">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 px-4 rounded-2xl bg-slate-50/60 border-2 border-dashed border-slate-200">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-brand-green mx-auto flex items-center justify-center text-xl mb-3">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">No Creators Assigned Yet</h4>
                                    <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1 mb-4">Start assigning high-impact creators to <?= htmlspecialchars($brand_name) ?> to track deliverables and GMV.</p>
                                    <button onclick="openMatchCreatorModal()" class="px-5 py-2.5 rounded-xl bg-brand-deep hover:bg-brand-green text-white text-xs font-bold transition-all shadow-sm">
                                        <i class="fas fa-plus mr-1"></i> Match First Creator
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Brand SOP Card -->
                        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm relative">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-brand-green flex items-center justify-center text-lg shadow-sm">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-black text-slate-900 text-base md:text-lg">Standard Operating Procedure (SOP)</h3>
                                            <?php if (empty($brand_sop)): ?>
                                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-black uppercase rounded-md tracking-wider border border-amber-100">Default Template</span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 bg-emerald-50 text-brand-green text-[10px] font-black uppercase rounded-md tracking-wider border border-emerald-100">Customized</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-slate-400 font-medium">Creator guidelines & approval workflow for <?= htmlspecialchars($brand_name) ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="copySopToClipboard()" class="text-xs font-extrabold text-slate-700 hover:text-brand-deep flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all shadow-xs" title="1-Click Copy SOP to Clipboard">
                                        <i class="fas fa-copy text-brand-green"></i> Copy Guidelines
                                    </button>
                                    <button type="button" onclick="openSopModal()" class="text-xs font-extrabold text-white bg-brand-deep hover:bg-brand-green flex items-center gap-1.5 px-4 py-2 rounded-xl transition-all shadow-sm">
                                        <i class="fas fa-edit"></i> Edit SOP
                                    </button>
                                </div>
                            </div>

                            <div class="prose max-w-none text-slate-700 bg-slate-50/50 p-6 rounded-2xl border border-slate-100" id="sop-rendered-content">
                                <?php echo parse_sop_to_html($display_sop); ?>
                            </div>
                        </div>

                    </div>

                    <!-- Right Col: Goals & Client Feedback -->
                    <div class="space-y-8">
                        
                        <!-- Brand Goals & Quota Card -->
                        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-base font-black text-slate-900 flex items-center gap-2.5">
                                    <i class="fas fa-bullseye text-brand-green"></i> Campaign Goals
                                </h3>
                                <button onclick="openGoalsModal()" class="text-xs font-bold text-slate-400 hover:text-brand-deep transition-colors" title="Update Goals">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">
                                        <span>Target Revenue</span>
                                        <span class="text-brand-deep font-black">65% Reached</span>
                                    </div>
                                    <div class="flex items-baseline justify-between mb-2">
                                        <p class="text-2xl font-black text-slate-900">$250,000</p>
                                        <p class="text-xs text-slate-400 font-medium">Goal: Q1/Q2</p>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden p-0.5">
                                        <div class="bg-gradient-to-r from-brand-deep to-brand-green h-full rounded-full transition-all duration-500" style="width: 65%"></div>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-wider mb-2">Deliverables Quota</p>
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                            <p class="text-base font-black text-slate-900">24</p>
                                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">Reels</p>
                                        </div>
                                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                            <p class="text-base font-black text-slate-900">12</p>
                                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">TikToks</p>
                                        </div>
                                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                            <p class="text-base font-black text-slate-900">4</p>
                                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">YouTube</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-semibold">Forecast Multiplier</span>
                                    <span class="font-extrabold text-brand-deep bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                        <?= htmlspecialchars($calculated_roi) ?>x ROI
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Client Feedback & Sync Call Card -->
                        <div class="bg-gradient-to-br from-[#111827] to-[#1E293B] rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-base font-bold flex items-center gap-2">
                                        <i class="fas fa-comment-dots text-emerald-400 text-sm"></i> Client Feedback
                                    </h3>
                                    <button onclick="openGoalsModal()" class="text-xs text-white/50 hover:text-white transition-colors" title="Edit Notes">
                                        <i class="fas fa-pen text-[10px]"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-white/70 leading-relaxed italic mb-6 bg-white/5 p-4 rounded-2xl border border-white/10" id="brand-notes-display">
                                    <?= !empty($brand->notes) ? nl2br(htmlspecialchars($brand->notes)) : '"The recent creator pairings have improved our CPA. Focus on authentic creator story-telling and product unboxing videos."' ?>
                                </p>
                                
                                <div class="space-y-2">
                                    <button onclick="openSyncCallModal()" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold py-3 rounded-xl text-xs transition-all shadow-md flex items-center justify-center gap-2">
                                        <i class="fas fa-calendar-plus"></i> Schedule Sync Call
                                    </button>
                                    <?php if(!empty($brand->email)): ?>
                                        <a href="mailto:<?= htmlspecialchars($brand->email) ?>?subject=<?= urlencode('Sync Call Agenda - ' . $brand_name) ?>" class="w-full bg-white/10 hover:bg-white/20 border border-white/15 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 block text-center">
                                            <i class="fas fa-paper-plane text-[10px]"></i> Email Client Directly
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <i class="fas fa-quote-right absolute -bottom-2 -right-2 text-white/5 text-7xl pointer-events-none"></i>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- 1. EDIT BRAND DETAILS MODAL -->
    <div id="edit-brand-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-xl mx-4 overflow-hidden shadow-2xl border border-slate-100 scale-95 transition-transform duration-300" id="edit-brand-container">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-deep/10 text-brand-deep flex items-center justify-center text-sm">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Edit Brand Profile</h3>
                        <p class="text-xs text-slate-400">Update company identity and contact information</p>
                    </div>
                </div>
                <button onclick="closeEditBrandModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <?php echo form_open('ambassadors/update_brand/' . $brand_id, ['class' => 'p-6 space-y-4', 'id' => 'form-edit-brand']); ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Brand Name *</label>
                        <input type="text" name="name" required value="<?= htmlspecialchars($brand_name) ?>" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Industry / Niche</label>
                        <input type="text" name="industry" value="<?= htmlspecialchars($brand->industry ?? '') ?>" placeholder="e.g. Footwear, Tech, Beauty" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Contact Person</label>
                        <input type="text" name="contact_person" value="<?= htmlspecialchars($brand->contact_person ?? '') ?>" placeholder="e.g. Jane Doe" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Contact Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($brand->email ?? '') ?>" placeholder="e.g. partner@brand.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($brand->phone ?? '') ?>" placeholder="e.g. +1 555-0199" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Budget Range ($)</label>
                        <input type="text" name="budget_range" value="<?= htmlspecialchars($brand->budget_range ?? '') ?>" placeholder="e.g. 5,000 - 15,000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Campaign Notes / Feedback</label>
                    <textarea name="notes" rows="3" placeholder="Strategic requirements or special notes..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-medium text-slate-800 leading-relaxed"><?= htmlspecialchars($brand->notes ?? '') ?></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeEditBrandModal()" class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-600 text-xs font-bold hover:bg-slate-100 border border-slate-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-deep hover:bg-brand-green text-white text-xs font-bold transition-all shadow-md">
                        Save Brand Details
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MATCH NEW CREATOR MODAL -->
    <div id="match-creator-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-xl mx-4 overflow-hidden shadow-2xl border border-slate-100 scale-95 transition-transform duration-300" id="match-creator-container">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-green/10 text-brand-green flex items-center justify-center text-sm">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Match Creator to <?= htmlspecialchars($brand_name) ?></h3>
                        <p class="text-xs text-slate-400">Select an existing creator from your network</p>
                    </div>
                </div>
                <button onclick="closeMatchCreatorModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <?php echo form_open('ambassadors/match_creator/' . $brand_id, ['class' => 'p-6 space-y-4', 'id' => 'form-match-creator']); ?>
                <!-- Search Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Filter Creators</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-3 text-slate-400 text-xs"></i>
                        <input type="text" id="creator-search-input" onkeyup="filterAvailableCreators()" placeholder="Search by name, handle, or platform..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                </div>

                <!-- Selectable Creator List -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Select Creator *</label>
                    <div class="max-h-60 overflow-y-auto space-y-2 border border-slate-100 rounded-2xl p-2 bg-slate-50/50" id="creators-radio-list">
                        <?php if(!empty($available_creators)): ?>
                            <?php foreach($available_creators as $idx => $c): ?>
                                <label class="creator-item flex items-center justify-between p-3 rounded-xl bg-white border border-slate-100 hover:border-emerald-300 hover:bg-emerald-50/40 cursor-pointer transition-all">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="account_id" value="<?= $c->account_id ?>" <?= ($idx === 0) ? 'checked' : '' ?> class="text-brand-green focus:ring-brand-green">
                                        <img src="<?= !empty($c->profile_image) ? htmlspecialchars($c->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode(!empty($c->username) ? $c->username : ($c->full_name ?? 'Creator')) . '&background=0F6B3E&color=fff' ?>" class="w-9 h-9 rounded-xl object-cover">
                                        <div>
                                            <p class="text-xs font-bold text-slate-800 creator-name"><?= htmlspecialchars(!empty($c->username) ? '@' . $c->username : ($c->full_name ?? 'Creator')) ?></p>
                                            <p class="text-[10px] text-slate-400"><?= htmlspecialchars($c->full_name ?? '') ?> &bull; <span class="capitalize"><?= htmlspecialchars($c->platform ?? 'Platform') ?></span></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                            <?= number_format($c->followers ?? 0) ?> followers
                                        </span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="p-4 text-center text-xs text-slate-400">No creators found in database.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Initial Stage</label>
                        <select name="pipeline_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                            <option value="Active">Active Partner</option>
                            <option value="Identified">Identified</option>
                            <option value="In Review">In Review</option>
                            <option value="Contract Sent">Contract Sent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Collaboration Type</label>
                        <select name="campaign_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                            <option value="Brand Ambassador">Brand Ambassador</option>
                            <option value="Product Seeding">Product Seeding</option>
                            <option value="Sponsored Reel">Sponsored Reel</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeMatchCreatorModal()" class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-600 text-xs font-bold hover:bg-slate-100 border border-slate-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-deep hover:bg-brand-green text-white text-xs font-bold transition-all shadow-md">
                        Confirm Match
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. EDIT SOP MODAL -->
    <div id="sop-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-2xl mx-4 overflow-hidden shadow-2xl border border-slate-100 scale-95 transition-transform duration-300" id="sop-modal-container">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-deep/10 text-brand-deep flex items-center justify-center text-sm">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Edit Standard Operating Procedure</h3>
                        <p class="text-xs text-slate-400">Customize guidelines for creators working with <?= htmlspecialchars($brand_name) ?></p>
                    </div>
                </div>
                <button onclick="closeSopModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <?php echo form_open('ambassadors/update_sop/' . $brand_id, ['class' => 'p-6 space-y-4']); ?>
                <input type="hidden" name="brand_name" value="<?= htmlspecialchars($brand_name) ?>">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">SOP Content & Guidelines (Markdown Format)</label>
                    <textarea name="sop_text" id="sop-textarea" rows="12" required
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:bg-white focus:border-brand-green transition-all font-mono text-slate-800 text-xs leading-relaxed placeholder-slate-400"
                        placeholder="Define guidelines, DOs/DON'Ts, and workflow steps..."><?= htmlspecialchars($brand_sop ? $brand_sop : $default_sop) ?></textarea>
                </div>
                
                <div class="flex items-center justify-between p-3.5 bg-emerald-50 rounded-2xl border border-emerald-100 text-xs text-emerald-800 font-bold">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i>
                        <span>Supports headers (###), bullet lists (- **DO**), and numbered steps (1. Step).</span>
                    </div>
                    <button type="button" onclick="insertSopTemplate()" class="text-[11px] text-brand-deep hover:underline">Insert Default Template</button>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeSopModal()" class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-700 text-xs font-bold hover:bg-slate-100 border border-slate-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-deep hover:bg-brand-green text-white text-xs font-bold transition-all shadow-md">
                        Save Brand SOP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. SCHEDULE SYNC CALL MODAL -->
    <div id="sync-call-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-md mx-4 overflow-hidden shadow-2xl border border-slate-100 scale-95 transition-transform duration-300" id="sync-call-container">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-brand-green flex items-center justify-center text-sm">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Schedule Sync Call</h3>
                        <p class="text-xs text-slate-400">Set meeting with <?= htmlspecialchars($brand_name) ?></p>
                    </div>
                </div>
                <button onclick="closeSyncCallModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <?php echo form_open('ambassadors/schedule_sync_call/' . $brand_id, ['class' => 'p-6 space-y-4']); ?>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Meeting Date</label>
                        <input type="date" name="meeting_date" required value="<?= date('Y-m-d', strtotime('+2 days')) ?>" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Time</label>
                        <input type="time" name="meeting_time" required value="14:00" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-semibold text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Agenda / Discussion Points</label>
                    <textarea name="agenda" rows="3" required placeholder="e.g. Q2 Creator Campaign Review & Budget Allocation..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-xs font-medium text-slate-800 leading-relaxed">Review Q2 influencer deliverables and discuss expanding TikTok creator roster.</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeSyncCallModal()" class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-600 text-xs font-bold hover:bg-slate-100 border border-slate-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-deep hover:bg-brand-green text-white text-xs font-bold transition-all shadow-md">
                        Confirm Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. VIEW FULL PROPOSAL MODAL -->
    <div id="proposal-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-slate-100 scale-95 transition-transform duration-300" id="proposal-container">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-deep/10 text-brand-deep flex items-center justify-center text-sm">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Executive Brand Brief & Proposal</h3>
                        <p class="text-xs text-slate-400"><?= htmlspecialchars($brand_name) ?> &bull; Strategic Overview</p>
                    </div>
                </div>
                <button onclick="closeProposalModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-xs text-slate-600 leading-relaxed flex-1">
                <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                    <div>
                        <h4 class="font-black text-slate-900 text-sm"><?= htmlspecialchars($brand_name) ?> Partnership Overview</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Industry: <?= htmlspecialchars($brand->industry ?? 'General') ?> &bull; Managed by Instroom</p>
                    </div>
                    <span class="px-3 py-1 bg-brand-green text-white font-extrabold rounded-lg text-xs">Active Strategy</span>
                </div>

                <div>
                    <h5 class="font-black text-slate-800 uppercase tracking-wider mb-2">1. Partnership Objectives</h5>
                    <p>Drive consumer engagement and direct GMV expansion through vetted high-converting influencers. Leverage authentic product storytelling and multi-channel distribution across TikTok, Instagram, and YouTube.</p>
                </div>

                <div>
                    <h5 class="font-black text-slate-800 uppercase tracking-wider mb-2">2. Creator Roster Summary</h5>
                    <p>Currently <?= count($assigned_creators) ?> active creators assigned. Estimated campaign ROI multiplier is <?= htmlspecialchars($calculated_roi) ?>x with an average forecast GMV per post of $3,500.</p>
                </div>

                <div>
                    <h5 class="font-black text-slate-800 uppercase tracking-wider mb-2">3. Brand SOP Highlights</h5>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                        <p>&bull; Clear product placement in first 3 seconds</p>
                        <p>&bull; Draft review required 72 hours before posting</p>
                        <p>&bull; Exclusive feature without competitor mentions</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <a href="<?= base_url('ambassadors/proposal') ?>" class="text-xs font-bold text-brand-deep hover:underline flex items-center gap-1.5">
                    <i class="fas fa-external-link-alt"></i> Open Full Proposal Page
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                        <i class="fas fa-print mr-1"></i> Print Brief
                    </button>
                    <button type="button" onclick="closeProposalModal()" class="px-5 py-2 rounded-xl bg-brand-deep hover:bg-brand-green text-white text-xs font-bold transition-all">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Handlers & Micro-interactions -->
    <script>
        // Brand Dropdown Switcher
        function toggleBrandDropdown() {
            const menu = document.getElementById('brand-switcher-menu');
            menu.classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('brand-switcher-wrapper');
            const menu = document.getElementById('brand-switcher-menu');
            if (wrapper && !wrapper.contains(e.target) && menu) {
                menu.classList.add('hidden');
            }
        });

        // 1-Click Clipboard Copy SOP
        function copySopToClipboard() {
            const sopText = <?= json_encode($display_sop) ?>;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(sopText).then(() => {
                    showToast('Brand SOP and Creator Guidelines copied to clipboard!', 'success');
                }).catch(() => {
                    fallbackCopy(sopText);
                });
            } else {
                fallbackCopy(sopText);
            }
        }

        function fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast('Brand Guidelines copied to clipboard!', 'success');
        }

        // Generic Modal Helper
        function showModal(modalId, containerId) {
            const modal = document.getElementById(modalId);
            const container = document.getElementById(containerId);
            if (!modal) return;
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                if (container) container.classList.remove('scale-95');
            }, 10);
        }

        function hideModal(modalId, containerId) {
            const modal = document.getElementById(modalId);
            const container = document.getElementById(containerId);
            if (!modal) return;
            modal.classList.add('opacity-0');
            if (container) container.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 250);
        }

        // Edit Brand Modal
        function openEditBrandModal() {
            showModal('edit-brand-modal', 'edit-brand-container');
        }
        function closeEditBrandModal() {
            hideModal('edit-brand-modal', 'edit-brand-container');
        }

        // Match Creator Modal
        function openMatchCreatorModal() {
            showModal('match-creator-modal', 'match-creator-container');
        }
        function closeMatchCreatorModal() {
            hideModal('match-creator-modal', 'match-creator-container');
        }

        // SOP Modal
        function openSopModal() {
            showModal('sop-modal', 'sop-modal-container');
        }
        function closeSopModal() {
            hideModal('sop-modal', 'sop-modal-container');
        }

        function insertSopTemplate() {
            const def = <?= json_encode($default_sop) ?>;
            document.getElementById('sop-textarea').value = def;
            showToast('Loaded default SOP template!', 'info');
        }

        // Sync Call Modal
        function openSyncCallModal() {
            showModal('sync-call-modal', 'sync-call-container');
        }
        function closeSyncCallModal() {
            hideModal('sync-call-modal', 'sync-call-container');
        }

        // Proposal Modal
        function openProposalModal() {
            showModal('proposal-modal', 'proposal-container');
        }
        function closeProposalModal() {
            hideModal('proposal-modal', 'proposal-container');
        }

        // Search Filter for Available Creators in modal
        function filterAvailableCreators() {
            const query = document.getElementById('creator-search-input').value.toLowerCase();
            const items = document.querySelectorAll('#creators-radio-list .creator-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });
        }

        // Unlink Creator with Toast Confirmation
        function confirmUnlinkCreator(collabId, creatorName) {
            if (confirm('Unlink @' + creatorName + ' from this brand?')) {
                const unlinkUrl = '<?= base_url('ambassadors/unmatch_creator/') ?>' + collabId + '/<?= $brand_id ?>';
                fetch(unlinkUrl, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    showToast(data.message || 'Creator unlinked successfully.', 'success');
                    setTimeout(() => location.reload(), 800);
                })
                .catch(() => {
                    location.href = unlinkUrl;
                });
            }
        }

        // Goals Modal Trigger (Reuses Edit Brand for updating notes)
        function openGoalsModal() {
            openEditBrandModal();
        }

        // AJAX Form Intercepts for Instant Floating Toast Feedback
        document.addEventListener('DOMContentLoaded', () => {
            const forms = ['form-edit-brand', 'form-match-creator'];
            forms.forEach(id => {
                const f = document.getElementById(id);
                if (f) {
                    f.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(f);
                        const submitBtn = f.querySelector('button[type="submit"]');
                        if (submitBtn) submitBtn.disabled = true;

                        fetch(f.action, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showToast(data.message, 'success');
                                setTimeout(() => location.reload(), 700);
                            } else {
                                showToast(data.message || 'Operation completed.', data.status || 'info');
                                setTimeout(() => location.reload(), 900);
                            }
                        })
                        .catch(() => {
                            f.submit(); // Fallback to traditional POST
                        });
                    });
                }
            });
        });
    </script>
</body>
</html>
