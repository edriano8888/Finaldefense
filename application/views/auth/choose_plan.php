<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Plan - Instroom</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_icon.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-gradient-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(31, 174, 91, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(15, 107, 62, 0.03) 0px, transparent 50%);
        }
        .plan-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .plan-card:hover {
            transform: translateY(-8px);
        }
    </style>
</head>
<body class="bg-gradient-mesh min-h-screen p-6 md:p-12">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-2 mb-6">
                <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-extrabold uppercase tracking-widest rounded-full">Step 2 of 2</span>
            </div>
            <h1 class="text-5xl font-extrabold text-gray-900 mb-6 tracking-tight">Ready to scale your brand?</h1>
            <p class="text-gray-500 text-xl max-w-2xl mx-auto leading-relaxed">Choose a plan that fits your current needs. You can always upgrade as your team grows.</p>
        </div>

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
            <!-- Solo Plan -->
            <div class="plan-card bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-xl flex flex-col">
                <div class="mb-10">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 mb-6">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Solo</h3>
                    <p class="text-gray-400 text-sm mb-6 font-medium">Perfect for individual creators.</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-5xl font-extrabold text-gray-900">$29</span>
                        <span class="text-gray-400 font-bold">/mo</span>
                    </div>
                </div>
                
                <div class="space-y-5 mb-10 flex-1">
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Included seats: 0
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Max extra seats: 5
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Max influencers: 100
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Max campaigns: 3
                    </div>
                </div>

                <button onclick="window.location.href='<?= base_url('influencer') ?>'" class="w-full py-5 rounded-2xl border-2 border-gray-100 text-gray-900 font-extrabold hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all">
                    Start Solo
                </button>
            </div>

            <!-- Team Plan -->
            <div class="plan-card bg-[#111827] rounded-[2.5rem] p-10 border-4 border-brand-green shadow-2xl relative flex flex-col transform md:scale-105 z-10 text-white">
                <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-brand-green text-white px-6 py-2 rounded-full text-[10px] font-extrabold uppercase tracking-[0.2em] shadow-lg">
                    Most Popular
                </div>
                <div class="mb-10">
                    <div class="w-12 h-12 bg-brand-green/10 rounded-2xl flex items-center justify-center text-brand-green mb-6">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold mb-2">Team</h3>
                    <p class="text-gray-400 text-sm mb-6 font-medium">Best for growing marketing teams.</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-5xl font-extrabold">$79</span>
                        <span class="text-gray-500 font-bold">/mo</span>
                    </div>
                </div>
                
                <div class="space-y-5 mb-10 flex-1">
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-300">
                        <i class="fas fa-check text-brand-green"></i> Included seats: 10
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-300">
                        <i class="fas fa-check text-brand-green"></i> Max extra seats: 25
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-300">
                        <i class="fas fa-check text-brand-green"></i> Max influencers: 500
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-300">
                        <i class="fas fa-check text-brand-green"></i> Max campaigns: 10
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-300">
                        <i class="fas fa-check text-brand-green"></i> API Access: Full
                    </div>
                </div>

                <button onclick="window.location.href='<?= base_url('influencer') ?>'" class="w-full py-5 rounded-2xl bg-brand-green text-white font-extrabold hover:bg-white hover:text-brand-dark transition-all shadow-xl shadow-brand-green/20">
                    Choose Team
                </button>
            </div>

            <!-- Agency Plan -->
            <div class="plan-card bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-xl flex flex-col">
                <div class="mb-10">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 mb-6">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Agency</h3>
                    <p class="text-gray-400 text-sm mb-6 font-medium">For large scale operations.</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-5xl font-extrabold text-gray-900">$199</span>
                        <span class="text-gray-400 font-bold">/mo</span>
                    </div>
                </div>
                
                <div class="space-y-5 mb-10 flex-1">
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Included seats: 30
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Unlimited influencers
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Unlimited campaigns
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Custom Branding
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-600">
                        <i class="fas fa-check text-brand-green"></i> Priority Support
                    </div>
                </div>

                <button onclick="window.location.href='<?= base_url('influencer') ?>'" class="w-full py-5 rounded-2xl border-2 border-gray-100 text-gray-900 font-extrabold hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all">
                    Go Agency
                </button>
            </div>
        </div>

        <div class="text-center mt-20">
            <p class="text-gray-400 font-medium mb-4">Not ready to commit?</p>
            <a href="<?= base_url('influencer') ?>" class="text-gray-900 font-extrabold hover:text-brand-green transition-colors border-b-2 border-gray-200 hover:border-brand-green pb-1">
                Start with a 14-day free trial
            </a>
        </div>
    </div>
</body>
</html>
