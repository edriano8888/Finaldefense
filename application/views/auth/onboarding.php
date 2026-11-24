<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding - Instroom</title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo_icon.png'); ?>">
    <link rel="shortcut icon" href="<?php echo base_url('favicon.ico'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-gradient-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(31, 174, 91, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(15, 107, 62, 0.05) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-gradient-mesh min-h-screen flex items-center justify-center p-6">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
        <!-- Left Side: Visual/Info -->
        <div class="bg-[#111827] p-12 text-white flex flex-col justify-between hidden md:flex">
            <div>
                <div class="flex items-center gap-3 mb-12">
                    <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom" class="w-10 h-10 object-contain">
                    <span class="text-2xl font-extrabold tracking-tight">INSTROOM</span>
                </div>
                <h2 class="text-4xl font-bold leading-tight mb-6">Scale your influencer marketing.</h2>
                <p class="text-gray-400 text-lg leading-relaxed">Join thousands of brands using Instroom to manage their influencer pipelines and campaigns in one place.</p>
            </div>
            
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-brand-green">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold">Centralized Pipeline</p>
                        <p class="text-sm text-gray-500">Manage all collaborations in one view.</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-brand-green">
                        <i class="fas fa-chart-pie text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold">Real-time Analytics</p>
                        <p class="text-sm text-gray-500">Track ROI and performance easily.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="p-10 md:p-16 flex flex-col justify-center">
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4">
                    <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-extrabold uppercase tracking-widest rounded-full">Step 1 of 2</span>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Create your Brand</h1>
                <p class="text-gray-500">Tell us a bit about your business to customize your experience.</p>
            </div>

            <?php if($this->session->flashdata('error')): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-8 text-sm flex items-center gap-3 border border-red-100">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/onboarding') ?>" method="POST" class="space-y-6">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">Brand Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <input type="text" name="brand_name" required 
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none transition-all placeholder:text-gray-300 font-medium"
                            placeholder="e.g. SkinGlow Essentials">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">Industry</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-tag"></i>
                        </div>
                        <select name="industry" 
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none transition-all font-medium appearance-none">
                            <option value="">Select your industry</option>
                            <option value="Fashion">Fashion & Apparel</option>
                            <option value="Beauty">Beauty & Cosmetics</option>
                            <option value="Tech">Technology & SaaS</option>
                            <option value="Food">Food & Beverage</option>
                            <option value="Travel">Travel & Lifestyle</option>
                            <option value="Health">Health & Wellness</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-extrabold py-5 rounded-2xl shadow-xl shadow-green-600/20 transition-all transform hover:-translate-y-1 active:scale-[0.98] flex items-center justify-center gap-3">
                        Continue to Plans
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <p class="text-center text-gray-400 text-xs mt-6">By continuing, you agree to our <a href="#" class="text-gray-900 font-bold hover:underline">Terms of Service</a>.</p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
