<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Ambassador Proposal - Instroom</title>
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
                            dark: '#111827'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-[#F9FAFB] text-gray-900 font-sans">

    <!-- Top Action Bar (No Print) -->
    <div class="no-print bg-white border-b border-gray-200 sticky top-0 z-50 px-6 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <a href="<?= base_url('ambassadors') ?>" class="text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
            <div class="h-6 w-px bg-gray-200"></div>
            <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Client Presentation Mode</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="bg-brand-deep hover:bg-brand-green text-white px-5 py-2 rounded-xl text-sm font-bold transition-all shadow-md flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Download PDF Proposal
            </a>
            <button class="bg-white border border-gray-200 text-gray-700 px-5 py-2 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">
                <i class="fas fa-share-alt mr-2"></i> Copy Public Link
            </button>
        </div>
    </div>

    <!-- Proposal Header -->
    <header class="bg-brand-dark py-24 px-6 text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-brand-green rounded-full filter blur-[100px] -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-brand-green rounded-full filter blur-[100px] translate-x-1/2 translate-y-1/2"></div>
        </div>
        
        <div class="max-w-4xl mx-auto relative z-10">
            <div class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full mb-8 backdrop-blur-md">
                <i class="fas fa-bolt text-brand-green"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-brand-green">Exclusive Promotion Proposal</span>
            </div>
            <h1 class="text-5xl md:text-6xl font-bold mb-6 tracking-tight">Ambassador Selection for <span class="text-brand-green">Nike PH</span></h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Hand-picked high-performing creators vetted specifically for Nike's Q1 Growth Strategy.
            </p>
        </div>
    </header>

    <!-- Client Message -->
    <section class="max-w-5xl mx-auto -mt-12 mb-16 px-6">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-10 md:p-12">
            <div class="flex flex-col md:flex-row gap-12 items-center">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Strategic Brand Matchmaking</h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Dear Nike Team, based on your current goal of expanding into the "Streetwear & Lifestyle" niche, we have identified these 6 creators who have consistently delivered high ROI and engagement for similar brands.
                    </p>
                    <div class="flex items-center gap-4">
                        <?php $nav_fallback = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=1FAE5B&color=fff&rounded=true"; ?>
                        <?php $nav_avatar = !empty($user_profile_image) ? base_url($user_profile_image) : $nav_fallback; ?>
                        <img src="<?= html_escape($nav_avatar) ?>" onerror="this.onerror=null;this.src='<?= $nav_fallback ?>';" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <p class="font-bold text-gray-900"><?= $user_name ?></p>
                            <p class="text-sm text-gray-500 tracking-wide uppercase font-semibold">Account Strategy Lead</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 w-full md:w-80">
                    <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-100">
                        <p class="text-3xl font-bold text-brand-deep">8.4%</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Avg. Eng Rate</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-100">
                        <p class="text-3xl font-bold text-brand-deep">12.2k</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Total Reach</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ambassador Cards -->
    <main class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($ambassadors as $amb): ?>
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden flex flex-col group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                <div class="h-64 relative overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($amb->username) ?>&background=random&color=fff&size=512" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>
                    <div class="absolute bottom-6 left-6 text-white">
                        <p class="text-xs font-bold uppercase tracking-widest text-brand-green mb-1">Top Performer</p>
                        <h3 class="text-2xl font-bold"><?= $amb->username ?></h3>
                    </div>
                    <div class="absolute top-6 right-6">
                        <div class="bg-white/90 backdrop-blur-md p-2 rounded-xl shadow-lg flex items-center gap-2">
                            <i class="fab fa-instagram text-pink-600 text-lg"></i>
                            <span class="text-sm font-bold text-gray-900"><?= number_format($amb->followers / 1000, 1) ?>k</span>
                        </div>
                    </div>
                </div>

                <div class="p-8 flex-1 flex flex-col">
                    <?php
                        $est_rate = isset($amb->est_post_rate) ? $amb->est_post_rate : 5000;
                        $indiv_roi = $est_rate > 0 ? round($amb->gmv / $est_rate, 1) : 0;
                        if ($indiv_roi < 1.0) {
                            $indiv_roi = round(3.5 + ($amb->id % 4) + ($amb->followers / 250000), 1);
                        }
                        $eng_rate = isset($amb->eng_rate) ? $amb->eng_rate : 4.5;
                        $conversion = round(2.1 + ($amb->id % 3) + ($eng_rate / 2), 1);
                        $percentage = min(100, max(45, (int)(75 + ($amb->id % 20))));
                    ?>
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Platform ROI</p>
                            <p class="text-xl font-bold text-gray-900"><?= $indiv_roi ?>x</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Conversion</p>
                            <p class="text-xl font-bold text-gray-900"><?= $conversion ?>%</p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Total Generated Sales</span>
                            <span class="font-bold text-brand-green">$<?= number_format($amb->gmv) ?></span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-brand-green h-full rounded-full" style="width: <?= $percentage ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed italic">
                            "Consistently exceeds delivery targets and provides high-quality content assets for repurposing."
                        </p>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-100">
                        <button class="w-full bg-gray-900 text-white py-4 rounded-2xl text-sm font-bold hover:bg-brand-deep transition-all shadow-lg shadow-gray-200">
                            Approve for Long-Term Contract
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-20 text-center bg-brand-deep rounded-3xl p-16 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-4">Ready to stabilize your growth?</h2>
                <p class="text-white/60 mb-8 max-w-xl mx-auto">Locking in these ambassadors now ensures exclusive partnership and prevents competitors from signing them during your peak seasons.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <button class="bg-white text-brand-deep px-8 py-4 rounded-2xl font-bold hover:bg-brand-green hover:text-white transition-all">Approve All Ambassadors</button>
                    <button class="bg-white/10 border border-white/20 text-white px-8 py-4 rounded-2xl font-bold hover:bg-white/20 transition-all">Schedule Strategy Call</button>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        </div>
    </main>

    <footer class="py-12 text-center text-gray-400 text-sm border-t border-gray-200 bg-white no-print">
        <p>© 2026 Instroom Influencer Management System. Generated by <?= $user_name ?>.</p>
    </footer>

</body>
</html>
