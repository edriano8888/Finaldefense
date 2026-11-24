<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instroom - Influencer Marketing System</title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo_icon.png'); ?>">
    <link rel="shortcut icon" href="<?php echo base_url('favicon.ico'); ?>">
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
                            dark: '#0A2E1C',
                            charcoal: '#1E1E1E',
                            offwhite: '#F8FAFC'
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.6s ease-out forwards',
                        'slide-up-delay-100': 'slideUp 0.6s ease-out 0.1s forwards',
                        'slide-up-delay-200': 'slideUp 0.6s ease-out 0.2s forwards',
                        'slide-up-delay-300': 'slideUp 0.6s ease-out 0.3s forwards',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        .gradient-text {
            background: linear-gradient(135deg, #1FAE5B 0%, #0F6B3E 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(31, 174, 91, 0.2);
        }
        .feature-icon {
            transition: all 0.3s ease;
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        /* 🚀 Hero Floating & Glow Animations */
        @keyframes float-hero {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-16px) rotate(1.2deg); }
        }
        .hero-floating-mockup {
            animation: float-hero 6s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50% { opacity: 0.75; transform: scale(1.04); }
        }
        .glow-backdrop {
            animation: pulse-glow 4.5s ease-in-out infinite;
        }
        @keyframes badge-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .floating-badge-1 {
            animation: badge-float 4s ease-in-out infinite;
        }
        .floating-badge-2 {
            animation: badge-float 5s ease-in-out 1.5s infinite;
        }

        /* 🌟 6 Feature Card Micro-Animations */
        @keyframes icon-pulse {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.15) rotate(5deg); }
        }
        @keyframes robot-bob {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-7px) rotate(-3deg); }
        }
        @keyframes shield-pulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(236, 72, 153, 0)); }
            50% { transform: scale(1.12); filter: drop-shadow(0 0 10px rgba(236, 72, 153, 0.4)); }
        }
        .animated-feature-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .animated-feature-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(15, 107, 62, 0.15);
        }
        .animated-feature-card:hover .icon-search {
            animation: icon-pulse 1.5s ease-in-out infinite;
        }
        .animated-feature-card:hover .icon-robot {
            animation: robot-bob 1.8s ease-in-out infinite;
        }
        .animated-feature-card:hover .icon-shield {
            animation: shield-pulse 2s ease-in-out infinite;
        }

        /* 🌟 What's Inside Tab Transitions & Animations */
        @keyframes tab-fade-in {
            0% { opacity: 0; transform: translateY(20px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .whats-inside-content:not(.hidden) {
            animation: tab-fade-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Mockup hover-tilt effects */
        .mockup-container {
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mockup-container:hover {
            transform: translateY(-8px) rotateX(1deg) rotateY(-1deg);
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.4), 0 0 50px 10px rgba(31, 174, 91, 0.1);
        }

        /* Pulse glow animation for badges and active elements */
        @keyframes element-pulse {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.05); opacity: 1; }
        }
        .pulse-element {
            animation: element-pulse 3s ease-in-out infinite;
        }

        /* Slide in list items sequentially */
        @keyframes list-slide-in {
            0% { opacity: 0; transform: translateX(-15px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        .whats-inside-content:not(.hidden) ul li {
            animation: list-slide-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; /* starts hidden and slides in */
        }
        .whats-inside-content:not(.hidden) ul li:nth-child(1) { animation-delay: 0.1s; }
        .whats-inside-content:not(.hidden) ul li:nth-child(2) { animation-delay: 0.2s; }
        .whats-inside-content:not(.hidden) ul li:nth-child(3) { animation-delay: 0.3s; }
        .whats-inside-content:not(.hidden) ul li:nth-child(4) { animation-delay: 0.4s; }

        /* Interactive mockup elements hover states */
        .mockup-card-interactive {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mockup-card-interactive:hover {
            transform: translateY(-4px) scale(1.03);
            background-color: rgba(255, 255, 255, 0.12) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        /* 🚀 Dual-Track Marquee Brand Logo Animations */
        @keyframes marquee-scroll-left {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }

        @keyframes marquee-scroll-right {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0%); }
        }

        .marquee-wrapper {
            overflow: hidden;
            position: relative;
            width: 100%;
            mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
        }

        .marquee-track-left {
            display: flex;
            gap: 2rem;
            width: max-content;
            animation: marquee-scroll-left 40s linear infinite;
        }

        .marquee-track-right {
            display: flex;
            gap: 2rem;
            width: max-content;
            animation: marquee-scroll-right 40s linear infinite;
        }

        .marquee-wrapper:hover .marquee-track-left,
        .marquee-wrapper:hover .marquee-track-right {
            animation-play-state: paused;
        }

        .brand-logo-card {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 70px;
            width: 190px;
            padding: 0.75rem 1.25rem;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            flex-shrink: 0;
        }

        .brand-logo-card:hover {
            transform: translateY(-4px) scale(1.04);
            border-color: rgba(31, 174, 91, 0.4);
            box-shadow: 0 12px 25px -4px rgba(31, 174, 91, 0.15), 0 0 15px rgba(31, 174, 91, 0.08);
            background: #ffffff;
        }

        .brand-logo-card img {
            max-height: 40px;
            width: auto;
            max-width: 155px;
            object-fit: contain;
            filter: grayscale(100%) opacity(0.75);
            transition: all 0.35s ease;
        }

        .brand-logo-card:hover img {
            filter: grayscale(0%) opacity(1);
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-brand-offwhite font-sans overflow-x-hidden">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom" class="w-10 h-10 object-contain">
                    <span class="text-2xl font-extrabold text-gray-900">Instroom</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-gray-600 hover:text-brand-green font-semibold transition-colors">Features</a>
                    <a href="#about" class="text-gray-600 hover:text-brand-green font-semibold transition-colors">About Us</a>
                    <a href="#whats-inside" class="text-gray-600 hover:text-brand-green font-semibold transition-colors">What's Inside</a>
                    
                    <!-- Resources Dropdown -->
                    <div class="relative group py-2">
                        <a href="#resources" class="flex items-center gap-1.5 text-gray-600 hover:text-brand-green font-semibold transition-colors cursor-pointer">
                            <span>Resources</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180 text-gray-400 group-hover:text-brand-green"></i>
                        </a>
                        
                        <!-- Dropdown Menu Box -->
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 p-2.5 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-200 z-50">
                            <a href="#resources" class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/70 transition-colors group/item">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100/60 text-brand-green flex items-center justify-center shrink-0 group-hover/item:bg-brand-green group-hover/item:text-white transition-colors">
                                    <i class="fas fa-toolbox text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 group-hover/item:text-brand-green transition-colors">Free Agency Tools</div>
                                    <div class="text-[11px] text-gray-500 font-medium">TikTok Downloader & Calculator</div>
                                </div>
                            </a>

                            <a href="#faq" class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/70 transition-colors group/item">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100/60 text-brand-green flex items-center justify-center shrink-0 group-hover/item:bg-brand-green group-hover/item:text-white transition-colors">
                                    <i class="fas fa-circle-question text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 group-hover/item:text-brand-green transition-colors">Questions & FAQs</div>
                                    <div class="text-[11px] text-gray-500 font-medium">Questions you're probably asking</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="#contact" class="text-gray-600 hover:text-brand-green font-semibold transition-colors">Contact</a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?php echo base_url('auth/login'); ?>" class="px-5 py-2.5 rounded-2xl text-gray-700 font-bold hover:bg-gray-100 transition-colors">Log In</a>
                    <a href="<?php echo base_url('auth/register'); ?>" class="px-5 py-2.5 rounded-2xl bg-brand-green text-white font-bold hover:bg-brand-deep shadow-lg shadow-green-200 transition-all duration-300 hover:scale-105">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-40 pb-24 px-4 sm:px-6 lg:px-8 overflow-hidden relative">
        <!-- Background Blobs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-brand-green opacity-10 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
        <div class="absolute top-40 -right-20 w-80 h-80 bg-emerald-600 opacity-10 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-200"></div>
        <div class="absolute -bottom-24 left-40 w-72 h-72 bg-green-700 opacity-10 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-400"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="animate-fade-in">
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 px-4 py-2 rounded-full mb-8 animate-bounce-slow">
                        <i class="fas fa-sparkles text-brand-green"></i>
                        <span class="text-sm font-bold text-brand-green">Trusted by 200+ Brands</span>
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                        Powering the <span class="gradient-text">Influencer</span> Economy
                    </h1>
                    <p class="text-xl text-gray-600 mb-10 leading-relaxed">
                        The all-in-one platform for Armful Media to manage campaigns, track analytics, and scale brand growth effortlessly with powerful automation and real-time insights.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <a href="<?php echo base_url('auth/register'); ?>" class="px-8 py-4 bg-gradient-to-r from-brand-green to-brand-deep text-white font-extrabold rounded-3xl shadow-xl shadow-green-300 hover:shadow-2xl transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2 group">
                            Get Started Free
                            <i class="fas fa-arrow-right text-lg group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <button onclick="openDemoModal()" class="px-8 py-4 bg-white text-gray-900 font-extrabold rounded-3xl shadow-lg border border-gray-200 hover:bg-gray-50 hover:border-brand-green/40 transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2 group cursor-pointer">
                            <i class="fas fa-play-circle text-brand-green text-xl group-hover:scale-110 transition-transform"></i>
                            <span>Watch Demo</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-8">
                        <div class="flex -space-x-4">
                            <div class="w-12 h-12 rounded-full border-4 border-white bg-gradient-to-br from-purple-400 to-pink-500"></div>
                            <div class="w-12 h-12 rounded-full border-4 border-white bg-gradient-to-br from-blue-400 to-indigo-500"></div>
                            <div class="w-12 h-12 rounded-full border-4 border-white bg-gradient-to-br from-green-400 to-teal-500"></div>
                            <div class="w-12 h-12 rounded-full border-4 border-white bg-gradient-to-br from-orange-400 to-red-500"></div>
                        </div>
                        <div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star-half-alt text-yellow-400"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">4.9/5 from 2,500+ reviews</p>
                        </div>
                    </div>
                </div>
                <!-- 🚀 ANIMATED FLOATING BROWSER MOCKUP WITH DISCOVERY PREVIEW -->
                <div class="relative animate-slide-up animate-delay-200">
                    <!-- Ambient Pulsing Glow Backdrop -->
                    <div class="absolute -inset-4 bg-gradient-to-r from-emerald-500/30 via-green-400/20 to-teal-500/30 rounded-[3.5rem] blur-2xl glow-backdrop -z-10"></div>

                    <!-- Floating 3D Browser Container -->
                    <div class="hero-floating-mockup relative bg-white/95 backdrop-blur-xl rounded-[2.5rem] p-3 md:p-4 shadow-2xl border border-gray-100/80">
                        
                        <!-- macOS Style Header Bar -->
                        <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50/90 rounded-2xl border border-gray-100 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            </div>
                            <div class="bg-white px-4 py-1 rounded-xl text-[11px] font-bold text-gray-500 border border-gray-200/80 shadow-2xs flex items-center gap-1.5">
                                <i class="fas fa-lock text-[9px] text-emerald-500"></i>
                                <span>instroom.com/discovery</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                <i class="fas fa-sparkles text-brand-green animate-spin-slow"></i>
                                <span class="text-[10px] font-black text-brand-deep uppercase">Live Engine</span>
                            </div>
                        </div>

                        <!-- Discovery Dashboard Screenshot Image -->
                        <div class="relative rounded-2xl overflow-hidden border border-gray-100 shadow-md group">
                            <img src="<?php echo base_url('assets/images/discovery_preview.png'); ?>" alt="Instroom Discovery Engine" class="w-full h-auto object-cover transform group-hover:scale-103 transition-transform duration-700">
                            
                            <!-- Animated Floating Overlay Badge 1 -->
                            <div class="floating-badge-1 absolute top-4 right-4 bg-white/95 backdrop-blur-md px-4 py-2 rounded-2xl shadow-xl border border-emerald-100/80 flex items-center gap-2 text-xs font-black text-gray-900">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>🔍 50,000+ Creators Vetted</span>
                            </div>

                            <!-- Animated Floating Overlay Badge 2 -->
                            <div class="floating-badge-2 absolute bottom-4 left-4 bg-gradient-to-r from-[#0F6B3E] to-[#1FAE5B] text-white px-4 py-2.5 rounded-2xl shadow-2xl border border-white/20 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center font-black text-xs">
                                    ⚡
                                </div>
                                <div>
                                    <p class="text-[9px] uppercase font-black tracking-wider text-emerald-200">Match Rate</p>
                                    <p class="text-xs font-black text-white">98.5% Creator Alignment</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By Brands Animated Section -->
    <section class="py-20 bg-gradient-to-b from-white via-slate-50/50 to-white border-y border-gray-100 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 mb-10 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-100/80 mb-3">
                <span class="w-2 h-2 rounded-full bg-brand-green animate-pulse"></span>
                <span class="text-xs font-black uppercase tracking-wider text-brand-deep">Trusted Brand Network</span>
            </div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Powering campaigns for 200+ top brands</h2>
            <p class="text-xs md:text-sm font-semibold text-gray-500 mt-1">Hover over any logo to pause the animation</p>
        </div>

        <div class="space-y-6">
            <!-- Row 1: Left Scrolling Marquee -->
            <div class="marquee-wrapper py-2">
                <div class="marquee-track-left">
                    <!-- First Copy of Brands Set 1 -->
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/okura_cookware.svg'); ?>" alt="Okura Cookware"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/yummii_yummii.svg'); ?>" alt="yummii yummii"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/zipput.svg'); ?>" alt="ZIPPUT"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/nonoise.svg'); ?>" alt="nonoise"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/comfit.svg'); ?>" alt="Comfit"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/awesome_maps.svg'); ?>" alt="Awesome Maps"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/dreamfully.svg'); ?>" alt="Dreamfully"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/flaske.svg'); ?>" alt="FLASKE"></div>

                    <!-- Second Copy for Seamless Loop -->
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/okura_cookware.svg'); ?>" alt="Okura Cookware"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/yummii_yummii.svg'); ?>" alt="yummii yummii"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/zipput.svg'); ?>" alt="ZIPPUT"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/nonoise.svg'); ?>" alt="nonoise"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/comfit.svg'); ?>" alt="Comfit"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/awesome_maps.svg'); ?>" alt="Awesome Maps"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/dreamfully.svg'); ?>" alt="Dreamfully"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/flaske.svg'); ?>" alt="FLASKE"></div>
                </div>
            </div>

            <!-- Row 2: Right Scrolling Marquee -->
            <div class="marquee-wrapper py-2">
                <div class="marquee-track-right">
                    <!-- First Copy of Brands Set 2 -->
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/alcanside.svg'); ?>" alt="ALCANSIDE"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/vital_reliefwear.svg'); ?>" alt="VITAL RELIEFWEAR"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/remodius.svg'); ?>" alt="REMODIUS"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/sylvian_grant.svg'); ?>" alt="SYLVIAN GRANT"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/tapout.svg'); ?>" alt="tap out"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/culistack.svg'); ?>" alt="CuliStack"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/beemzy.svg'); ?>" alt="BEEMZY"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/dadafunk.svg'); ?>" alt="dadafunk"></div>

                    <!-- Second Copy for Seamless Loop -->
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/alcanside.svg'); ?>" alt="ALCANSIDE"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/vital_reliefwear.svg'); ?>" alt="VITAL RELIEFWEAR"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/remodius.svg'); ?>" alt="REMODIUS"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/sylvian_grant.svg'); ?>" alt="SYLVIAN GRANT"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/tapout.svg'); ?>" alt="tap out"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/culistack.svg'); ?>" alt="CuliStack"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/beemzy.svg'); ?>" alt="BEEMZY"></div>
                    <div class="brand-logo-card"><img src="<?php echo base_url('assets/images/brands/dadafunk.svg'); ?>" alt="dadafunk"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-28 px-4 sm:px-6 lg:px-8 bg-brand-offwhite">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <div class="inline-block bg-green-100 text-brand-green px-4 py-2 rounded-full text-sm font-bold mb-6">
                    <i class="fas fa-star mr-2"></i>Features
                </div>
                <h2 class="text-5xl font-extrabold text-gray-900 mb-6">Everything You Need to Succeed</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Discover powerful tools designed to streamline your influencer marketing workflow</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- 🔍 Feature 1: Smart Discovery -->
                <div class="animated-feature-card bg-white p-9 rounded-[2.5rem] border border-gray-100 shadow-xl hover:border-emerald-300 relative group overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-500 transition-colors duration-300">
                            <i class="fas fa-search text-emerald-600 group-hover:text-white text-2xl icon-search transition-colors"></i>
                        </div>
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-800 text-[10px] font-black uppercase tracking-wider rounded-xl border border-emerald-100">
                            LIVE SEARCH
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight group-hover:text-emerald-700 transition-colors">Smart Discovery</h3>
                    <p class="text-xs md:text-sm text-gray-600 font-medium leading-relaxed mb-6">Find the perfect creators for your campaigns with our advanced search and filter system by niche, audience size, and engagement rates.</p>
                    <a href="<?php echo base_url('discovery'); ?>" class="inline-flex items-center gap-2 text-xs font-black text-emerald-600 group-hover:text-emerald-800 transition-all">
                        <span>Learn more</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- 📊 Feature 2: Real-time Analytics -->
                <div class="animated-feature-card bg-white p-9 rounded-[2.5rem] border border-gray-100 shadow-xl hover:border-blue-300 relative group overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center border border-blue-100 group-hover:bg-blue-500 transition-colors duration-300">
                            <i class="fas fa-chart-bar text-blue-600 group-hover:text-white text-2xl icon-search transition-colors"></i>
                        </div>
                        <span class="px-3 py-1 bg-blue-50 text-blue-800 text-[10px] font-black uppercase tracking-wider rounded-xl border border-blue-100">
                            REAL-TIME
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight group-hover:text-blue-700 transition-colors">Real-time Analytics</h3>
                    <p class="text-xs md:text-sm text-gray-600 font-medium leading-relaxed mb-6">Track campaign performance, engagement metrics, and ROI in real-time with beautiful, actionable dashboards.</p>
                    <a href="<?php echo base_url('analytics'); ?>" class="inline-flex items-center gap-2 text-xs font-black text-blue-600 group-hover:text-blue-800 transition-all">
                        <span>Learn more</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- 📋 Feature 3: Pipeline Management -->
                <div class="animated-feature-card bg-white p-9 rounded-[2.5rem] border border-gray-100 shadow-xl hover:border-amber-300 relative group overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center border border-amber-100 group-hover:bg-amber-500 transition-colors duration-300">
                            <i class="fas fa-clipboard-list text-amber-600 group-hover:text-white text-2xl icon-search transition-colors"></i>
                        </div>
                        <span class="px-3 py-1 bg-amber-50 text-amber-900 text-[10px] font-black uppercase tracking-wider rounded-xl border border-amber-100">
                            KANBAN WORKFLOW
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight group-hover:text-amber-700 transition-colors">Pipeline Management</h3>
                    <p class="text-xs md:text-sm text-gray-600 font-medium leading-relaxed mb-6">Organize your campaigns, track progress, and manage influencer relationships all in one place.</p>
                    <a href="<?php echo base_url('pipeline'); ?>" class="inline-flex items-center gap-2 text-xs font-black text-amber-700 group-hover:text-amber-900 transition-all">
                        <span>Learn more</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- 👥 Feature 4: Team Collaboration -->
                <div class="animated-feature-card bg-white p-9 rounded-[2.5rem] border border-gray-100 shadow-xl hover:border-indigo-300 relative group overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100 group-hover:bg-indigo-600 transition-colors duration-300">
                            <i class="fas fa-users-cog text-indigo-600 group-hover:text-white text-2xl icon-search transition-colors"></i>
                        </div>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-800 text-[10px] font-black uppercase tracking-wider rounded-xl border border-indigo-100">
                            MULTI-USER
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight group-hover:text-indigo-700 transition-colors">Team Collaboration</h3>
                    <p class="text-xs md:text-sm text-gray-600 font-medium leading-relaxed mb-6">Work together seamlessly with role-based access, comments, and shared workspaces.</p>
                    <a href="<?php echo base_url('manager/team_management'); ?>" class="inline-flex items-center gap-2 text-xs font-black text-indigo-600 group-hover:text-indigo-800 transition-all">
                        <span>Learn more</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-28 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#0B3B24] via-[#0F6B3E] to-[#0A4729] text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-white/10 text-emerald-300 px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider mb-6 border border-white/10 backdrop-blur-md">
                    <i class="fas fa-users text-emerald-300 mr-1"></i> About Armful Media
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white mb-6 tracking-tight">
                    The Agency Behind Instroom
                </h2>
                <p class="text-lg text-emerald-100/90 max-w-3xl mx-auto leading-relaxed font-medium">
                    Instroom is engineered by <strong>Armful Media</strong> — a leading full-service influencer marketing agency. We developed Instroom to replace fragmented spreadsheets with automated revenue thresholds, live ROI analytics, and relationship-driven ambassador management.
                </p>
            </div>

            <!-- 📸 Armful Media Agency Team Photo Banner -->
            <div class="relative max-w-5xl mx-auto rounded-[2.5rem] overflow-hidden shadow-2xl border-4 border-white/20 group mb-16">
                <img src="<?php echo base_url('assets/images/armful_media_team.jpg'); ?>" alt="Armful Media Team" class="w-full h-auto object-cover transform group-hover:scale-102 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent flex flex-col justify-end p-6 md:p-10 text-white">
                    <div class="inline-flex items-center gap-2 bg-emerald-500/80 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider text-white mb-3 max-w-max border border-white/20 shadow-md">
                        <i class="fas fa-camera"></i> The Armful Media Team
                    </div>
                    <h3 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight">Creators, Campaign Managers & Growth Strategists</h3>
                    <p class="text-emerald-100/90 text-sm md:text-base font-medium max-w-3xl mt-2 leading-relaxed">
                        Our dedicated team of campaign managers, media buyers, and software developers scaling top-tier brand partnerships across Southeast Asia.
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 pt-4">
                <div class="bg-white/10 backdrop-blur-xl p-8 rounded-3xl border border-white/15 hover:bg-white/15 transition-all shadow-xl">
                    <div class="w-14 h-14 bg-amber-400/20 text-amber-300 rounded-2xl flex items-center justify-center text-2xl font-black mb-6 border border-amber-300/30">
                        🤝
                    </div>
                    <h3 class="text-xl font-extrabold text-white mb-3">Relationships First</h3>
                    <p class="text-sm text-emerald-100/80 leading-relaxed">
                        Creators are worth more than a single campaign. Instroom gives long-term partnerships structure with tiered milestones (Gold, Silver, Bronze) and automated retainer tracking.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-xl p-8 rounded-3xl border border-white/15 hover:bg-white/15 transition-all shadow-xl">
                    <div class="w-14 h-14 bg-emerald-400/20 text-emerald-300 rounded-2xl flex items-center justify-center text-2xl font-black mb-6 border border-emerald-300/30">
                        📊
                    </div>
                    <h3 class="text-xl font-extrabold text-white mb-3">Data-Driven Budgeting</h3>
                    <p class="text-sm text-emerald-100/80 leading-relaxed">
                        When budget conversations come up, the answers are already in your live data. Know exactly who generates GMV revenue, who delivers on time, and who deserves a retainer.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-xl p-8 rounded-3xl border border-white/15 hover:bg-white/15 transition-all shadow-xl">
                    <div class="w-14 h-14 bg-purple-400/20 text-purple-300 rounded-2xl flex items-center justify-center text-2xl font-black mb-6 border border-purple-300/30">
                        ⚡
                    </div>
                    <h3 class="text-xl font-extrabold text-white mb-3">Enterprise Scalability</h3>
                    <p class="text-sm text-emerald-100/80 leading-relaxed">
                        Designed with client workspace access, granular permission levels, real-time status updates, and white-label readiness for brands and agencies.
                    </p>
                </div>
            </div>


        </div>
    </section>

    <!-- Brand Values Section -->
    <section class="py-28 px-4 sm:px-6 lg:px-8 bg-brand-offwhite border-b border-gray-100 relative overflow-hidden">
        <!-- Subtle back glow blob -->
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-brand-green/5 rounded-full filter blur-3xl"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                
                <!-- Left Column: Title & Description -->
                <div class="lg:col-span-4 space-y-6 animate-fade-in lg:sticky lg:top-24">
                    <div class="inline-flex items-center gap-2 bg-green-50 border border-green-100 px-4 py-2 rounded-full">
                        <span class="text-xs font-black text-brand-green uppercase tracking-widest">WHAT WE STAND FOR</span>
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-black text-gray-900 leading-tight tracking-tight">
                        The Core Values That <span class="gradient-text">Drive Instroom</span>
                    </h2>
                    <p class="text-gray-600 text-sm md:text-base font-semibold leading-relaxed">
                        We build agency tools with a clear focus on data, trust, adaptability, and continuous evolution to empower creator partnerships.
                    </p>
                    <div class="pt-4 hidden lg:block">
                        <div class="w-16 h-1 bg-gradient-to-r from-brand-green to-brand-deep rounded-full"></div>
                    </div>
                </div>

                <!-- Right Column: 2x2 Grid of values -->
                <div class="lg:col-span-8 grid sm:grid-cols-2 gap-6">
                    
                    <!-- Value 1: Expertise -->
                    <div class="bg-white p-8 rounded-3xl border-l-4 border-emerald-500 shadow-lg shadow-gray-100/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute top-4 right-6 text-5xl font-black text-emerald-50/70 group-hover:text-emerald-100/50 transition-colors">01</div>
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl mb-8 border border-emerald-100 group-hover:bg-emerald-500 group-hover:text-white transition-colors shadow-sm">
                            🔬
                        </div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">EXPERTISE</h3>
                        <h4 class="text-lg font-black text-gray-900 mb-2">Data-Backed Solutions</h4>
                        <p class="text-xs md:text-sm text-gray-500 leading-relaxed font-semibold">
                            We prioritize knowledge and experience, delivering solutions backed by data and results — not guesswork.
                        </p>
                    </div>

                    <!-- Value 2: Collaboration -->
                    <div class="bg-white p-8 rounded-3xl border-l-4 border-blue-500 shadow-lg shadow-gray-100/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute top-4 right-6 text-5xl font-black text-blue-50/70 group-hover:text-blue-100/50 transition-colors">02</div>
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-8 border border-blue-100 group-hover:bg-blue-500 group-hover:text-white transition-colors shadow-sm">
                            🤝
                        </div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">COLLABORATION</h3>
                        <h4 class="text-lg font-black text-gray-900 mb-2">Mutual Partnership</h4>
                        <p class="text-xs md:text-sm text-gray-500 leading-relaxed font-semibold">
                            We work in partnership with our clients, sharing insights and fostering mutual success at every stage.
                        </p>
                    </div>

                    <!-- Value 3: Agility -->
                    <div class="bg-white p-8 rounded-3xl border-l-4 border-amber-500 shadow-lg shadow-gray-100/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute top-4 right-6 text-5xl font-black text-amber-50/70 group-hover:text-amber-100/50 transition-colors">03</div>
                        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mb-8 border border-amber-100 group-hover:bg-amber-500 group-hover:text-white transition-colors shadow-sm">
                            ⚡
                        </div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">AGILITY</h3>
                        <h4 class="text-lg font-black text-gray-900 mb-2">Rapid Adaptability</h4>
                        <p class="text-xs md:text-sm text-gray-500 leading-relaxed font-semibold">
                            We stay adaptable, responding to market changes quickly to deliver the best solutions in real time.
                        </p>
                    </div>

                    <!-- Value 4: Innovation -->
                    <div class="bg-white p-8 rounded-3xl border-l-4 border-purple-500 shadow-lg shadow-gray-100/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute top-4 right-6 text-5xl font-black text-purple-50/70 group-hover:text-purple-100/50 transition-colors">04</div>
                        <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-8 border border-purple-100 group-hover:bg-purple-500 group-hover:text-white transition-colors shadow-sm">
                            🚀
                        </div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">INNOVATION</h3>
                        <h4 class="text-lg font-black text-gray-900 mb-2">Continuous Improvement</h4>
                        <p class="text-xs md:text-sm text-gray-500 leading-relaxed font-semibold">
                            We continuously improve, integrating new technologies and streamlining workflows to stay ahead.
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- What's Inside Section: System Overall Features -->
    <section id="whats-inside" class="py-28 px-4 sm:px-6 lg:px-8 bg-white relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <div class="inline-flex items-center gap-2 bg-emerald-50 text-brand-deep px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider mb-6 border border-emerald-100">
                    <i class="fas fa-cubes text-brand-green mr-1"></i> System Overall Architecture
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6 tracking-tight">What's Inside Instroom</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-medium leading-relaxed">
                    Instroom is built as a complete end-to-end influencer marketing operating system. Explore the 6 core modules powering every workspace:
                </p>
            </div>

            <!-- Premium Horizontal Tabs Navigation matching request -->
            <div class="border-b border-gray-200/80 mb-12">
                <div class="flex flex-wrap justify-center gap-2 md:gap-6 -mb-px">
                    <button id="wi-tab-pipeline" onclick="switchWhatsInsideTab('pipeline')" class="whats-inside-tab-btn px-5 py-4 text-xs md:text-base font-extrabold transition-all border-b-2 border-brand-green text-brand-green">
                        Pipeline
                    </button>
                    <button id="wi-tab-email" onclick="switchWhatsInsideTab('email')" class="whats-inside-tab-btn px-5 py-4 text-xs md:text-base font-extrabold transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-900">
                        Email
                    </button>
                    <button id="wi-tab-crm" onclick="switchWhatsInsideTab('crm')" class="whats-inside-tab-btn px-5 py-4 text-xs md:text-base font-extrabold transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-900">
                        Creator CRM
                    </button>
                    <button id="wi-tab-reporting" onclick="switchWhatsInsideTab('reporting')" class="whats-inside-tab-btn px-5 py-4 text-xs md:text-base font-extrabold transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-900">
                        Reporting
                    </button>
                    <button id="wi-tab-partners" onclick="switchWhatsInsideTab('partners')" class="whats-inside-tab-btn px-5 py-4 text-xs md:text-base font-extrabold transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-900">
                        Brand Partners
                    </button>
                </div>
            </div>

            <!-- Tab Contents Container -->
            <div class="bg-gray-50/50 rounded-[3rem] p-6 md:p-10 border border-gray-100">

                <!-- 1. Pipeline Content -->
                <div id="wi-content-pipeline" class="whats-inside-content grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-5 space-y-6">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl border border-blue-100 shadow-sm">
                            <i class="fas fa-columns"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Kanban Pipeline & Deal Tracker</h3>
                        <p class="text-sm md:text-base text-gray-600 leading-relaxed font-semibold">
                            Track collaborations dynamically across 6 custom stages. Manage creator outreach, contract negotiation, shipping logs, content reviews, and closed campaigns inside a unified drag-and-drop workflow.
                        </p>
                        <ul class="space-y-3 text-xs md:text-sm font-bold text-gray-700">
                            <li class="flex items-center gap-3"><i class="fas fa-check text-blue-600"></i> Interactive modal drawers with direct notes auto-save</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-blue-600"></i> Seamless drag-and-drop card transitions</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-blue-600"></i> Real-time shipping addresses and contract details</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-blue-600"></i> Tracking link and promo code generator integration</li>
                        </ul>
                    </div>
                    <!-- Mini Board Mockup -->
                    <div class="lg:col-span-7 bg-slate-900 p-6 rounded-[2.5rem] border border-slate-800 shadow-2xl relative overflow-hidden group mockup-container">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl"></div>
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center pb-3 border-b border-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span class="text-[10px] text-white/40 ml-2 font-bold uppercase tracking-widest">Pipeline Board</span>
                                </div>
                                <span class="text-[9px] px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-400 font-extrabold uppercase pulse-element">Instroom CRM</span>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-white/5 rounded-xl p-3 space-y-2">
                                    <div class="flex justify-between items-center text-[10px] font-black uppercase text-white/50">
                                        <span>Outreach</span>
                                        <span class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center">2</span>
                                    </div>
                                    <div class="bg-white/10 p-2.5 rounded-lg border border-white/5 space-y-1 mockup-card-interactive">
                                        <span class="text-[11px] font-bold text-white block">Jessie Doe</span>
                                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-white/10 text-white/70">Instagram</span>
                                    </div>
                                    <div class="bg-white/10 p-2.5 rounded-lg border border-white/5 space-y-1 mockup-card-interactive">
                                        <span class="text-[11px] font-bold text-white block">Marcus Vance</span>
                                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-white/10 text-white/70">TikTok</span>
                                    </div>
                                </div>
                                <div class="bg-white/5 rounded-xl p-3 space-y-2">
                                    <div class="flex justify-between items-center text-[10px] font-black uppercase text-white/50">
                                        <span>Negotiating</span>
                                        <span class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center">1</span>
                                    </div>
                                    <div class="bg-blue-500/10 p-2.5 rounded-lg border border-blue-500/20 space-y-1 border-l-2 border-l-blue-500 mockup-card-interactive pulse-element">
                                        <span class="text-[11px] font-bold text-white block">Elena Rostova</span>
                                        <div class="flex justify-between items-center text-[9px] text-blue-400 font-bold">
                                            <span>$450.00</span>
                                            <span class="px-1 py-0.5 rounded bg-blue-500/20 animate-pulse">Pending Sign</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white/5 rounded-xl p-3 space-y-2">
                                    <div class="flex justify-between items-center text-[10px] font-black uppercase text-white/50">
                                        <span>Shipping</span>
                                        <span class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center">1</span>
                                    </div>
                                    <div class="bg-white/10 p-2.5 rounded-lg border border-white/5 space-y-1 mockup-card-interactive">
                                        <span class="text-[11px] font-bold text-white block">David Kim</span>
                                        <div class="flex justify-between items-center text-[9px] text-white/60">
                                            <span>Box Set Kit</span>
                                            <span class="text-emerald-400 font-bold">Shipped</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Email Content -->
                <div id="wi-content-email" class="whats-inside-content grid lg:grid-cols-12 gap-8 lg:gap-12 items-center hidden">
                    <div class="lg:col-span-5 space-y-6">
                        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-2xl border border-rose-100 shadow-sm">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Automated Email Drips</h3>
                        <p class="text-sm md:text-base text-gray-600 leading-relaxed font-semibold">
                            Automate outreach campaigns to micro-influencers and creators. Hook up Gmail or Outlook, build customized follow-up drips that match same or new threads, and use custom variable tags to keep copy natural.
                        </p>
                        <ul class="space-y-3 text-xs md:text-sm font-bold text-gray-700">
                            <li class="flex items-center gap-3"><i class="fas fa-check text-rose-600"></i> Seamless Google/Microsoft OAuth 1-Click login</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-rose-600"></i> Shared templates with dynamic tokens (`{{first_name}}`, `{{username}}`)</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-rose-600"></i> Interval delay settings (+4 days, +7 days, etc.)</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-rose-600"></i> Full thread history linked automatically to creator CRM drawer</li>
                        </ul>
                    </div>
                    <!-- Step Timeline Mockup -->
                    <div class="lg:col-span-7 bg-slate-900 p-6 rounded-[2.5rem] border border-slate-800 shadow-2xl relative overflow-hidden group mockup-container">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-rose-500/10 rounded-full blur-2xl"></div>
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center pb-3 border-b border-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span class="text-[10px] text-white/40 ml-2 font-bold uppercase tracking-widest">Email Drip Campaign</span>
                                </div>
                                <span class="text-[9px] px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 font-extrabold uppercase pulse-element">Campaign Active</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex gap-3 items-start relative">
                                    <div class="w-0.5 h-10 bg-white/10 absolute left-3.5 top-7"></div>
                                    <div class="w-7 h-7 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center font-black text-xs z-10 border border-rose-500/30 pulse-element">1</div>
                                    <div class="bg-white/5 p-2.5 rounded-xl flex-grow border border-white/5 space-y-0.5 mockup-card-interactive">
                                        <div class="flex justify-between items-center text-[9px]">
                                            <span class="font-bold text-white">Outreach Email (New Thread)</span>
                                            <span class="text-rose-400 font-bold uppercase tracking-wider text-[8px]">Immediate</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-white/80 block">Subject: collaboration with {{brand_name}}!</span>
                                        <p class="text-[10px] text-white/50 line-clamp-1">Hi {{first_name}}, love your Instagram content about...</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-start">
                                    <div class="w-7 h-7 rounded-full bg-white/10 text-white/50 flex items-center justify-center font-black text-xs z-10 border border-white/5">2</div>
                                    <div class="bg-white/5 p-2.5 rounded-xl flex-grow border border-white/5 space-y-0.5 mockup-card-interactive">
                                        <div class="flex justify-between items-center text-[9px]">
                                            <span class="font-bold text-white/70">Follow-up Drip (Same Thread)</span>
                                            <span class="text-white/40 font-bold uppercase tracking-wider text-[8px]">+4 Days</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-white/60 block">Subject: Re: collaboration with {{brand_name}}!</span>
                                        <p class="text-[10px] text-white/40 line-clamp-1">Hi {{first_name}}, just following up on my previous note...</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 pt-1">
                                    <div class="bg-white/5 p-2.5 rounded-lg border border-white/5 text-center mockup-card-interactive">
                                        <span class="text-[9px] text-white/40 font-extrabold uppercase block">Open Rate</span>
                                        <span class="text-sm font-black text-emerald-400">88.5%</span>
                                    </div>
                                    <div class="bg-white/5 p-2.5 rounded-lg border border-white/5 text-center mockup-card-interactive pulse-element">
                                        <span class="text-[9px] text-white/40 font-extrabold uppercase block">Response</span>
                                        <span class="text-sm font-black text-blue-400">62.1%</span>
                                    </div>
                                    <div class="bg-white/5 p-2.5 rounded-lg border border-white/5 text-center mockup-card-interactive">
                                        <span class="text-[9px] text-white/40 font-extrabold uppercase block">Bounces</span>
                                        <span class="text-sm font-black text-rose-400">0.8%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Creator CRM Content -->
                <div id="wi-content-crm" class="whats-inside-content grid lg:grid-cols-12 gap-8 lg:gap-12 items-center hidden">
                    <div class="lg:col-span-5 space-y-6">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl border border-emerald-100 shadow-sm">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Smart Creator CRM & Discovery</h3>
                        <p class="text-sm md:text-base text-gray-600 leading-relaxed font-semibold">
                            Instantly query, analyze, and profile over 50,000 pre-vetted active creators. Refine searches by demographic filters, follower tiers, engagement scores, video views, and platform channels.
                        </p>
                        <ul class="space-y-3 text-xs md:text-sm font-bold text-gray-700">
                            <li class="flex items-center gap-3"><i class="fas fa-check text-emerald-600"></i> Multi-platform social filter combinations (TikTok, Insta, YT)</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-emerald-600"></i> One-click shortlist creation & quick list exports</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-emerald-600"></i> Dynamic creator profile details with performance metrics history</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-emerald-600"></i> Verified direct contact emails and channel niches roster</li>
                        </ul>
                    </div>
                    <!-- Discovery Filter Mockup -->
                    <div class="lg:col-span-7 bg-slate-900 p-6 rounded-[2.5rem] border border-slate-800 shadow-2xl relative overflow-hidden group mockup-container">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl"></div>
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center pb-3 border-b border-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span class="text-[10px] text-white/40 ml-2 font-bold uppercase tracking-widest">Creator Search Engine</span>
                                </div>
                                <span class="text-[9px] px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold uppercase pulse-element">Database Live</span>
                            </div>
                            <div class="space-y-3">
                                <div class="grid grid-cols-3 gap-2.5 bg-white/5 p-2.5 rounded-xl border border-white/5 mockup-card-interactive">
                                    <div class="space-y-0.5">
                                        <span class="text-[8px] text-white/40 font-bold uppercase tracking-wide">Platform</span>
                                        <div class="text-[10px] text-white font-extrabold flex items-center justify-between">
                                            <span>Instagram</span>
                                            <i class="fas fa-caret-down text-white/40 animate-bounce"></i>
                                        </div>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-[8px] text-white/40 font-bold uppercase tracking-wide">Followers</span>
                                        <div class="text-[10px] text-white font-extrabold">50k - 250k</div>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-[8px] text-white/40 font-bold uppercase tracking-wide">Min. ER %</span>
                                        <div class="text-[10px] text-white font-extrabold">3.50% +</div>
                                    </div>
                                </div>
                                <div class="bg-white/10 p-3 rounded-xl border border-white/10 flex items-center justify-between mockup-card-interactive pulse-element">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-amber-400 to-rose-500 flex items-center justify-center text-white font-black text-xs">AS</div>
                                        <div>
                                            <span class="text-xs font-bold text-white block">Alice Styles</span>
                                            <span class="text-[9px] text-white/40">@alicestyles • Fashion</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[11px] font-bold text-white block">125.8k followers</span>
                                        <span class="text-[9px] text-emerald-400 font-bold">4.82% ER (High)</span>
                                    </div>
                                </div>
                                <div class="bg-white/5 p-3 rounded-xl border border-white/5 flex items-center justify-between opacity-80 mockup-card-interactive">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-400 to-emerald-500 flex items-center justify-center text-white font-black text-xs">MV</div>
                                        <div>
                                            <span class="text-xs font-bold text-white block">Marcus Vance</span>
                                            <span class="text-[9px] text-white/40">@marcus.vlog • Travel</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[11px] font-bold text-white block">92.4k followers</span>
                                        <span class="text-[9px] text-emerald-400 font-bold">3.95% ER</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Reporting Content -->
                <div id="wi-content-reporting" class="whats-inside-content grid lg:grid-cols-12 gap-8 lg:gap-12 items-center hidden">
                    <div class="lg:col-span-5 space-y-6">
                        <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl border border-purple-100 shadow-sm">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Live GMV Sales & ROI Reporting</h3>
                        <p class="text-sm md:text-base text-gray-600 leading-relaxed font-semibold">
                            Optimize campaign costs and track sales revenue seamlessly. Check real-time return on investment (ROI), sales growth curves, and direct promo code usages generated within campaigns.
                        </p>
                        <ul class="space-y-3 text-xs md:text-sm font-bold text-gray-700">
                            <li class="flex items-center gap-3"><i class="fas fa-check text-purple-600"></i> Track campaign Gross Merchandise Value (GMV) metrics</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-purple-600"></i> Interactive ROI calculations per campaign or influencer</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-purple-600"></i> Generate affiliate click tracking URLs in one click</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-purple-600"></i> Export clean PDF executive reports for client stakeholders</li>
                        </ul>
                    </div>
                    <!-- Analytics Grid Mockup -->
                    <div class="lg:col-span-7 bg-slate-900 p-6 rounded-[2.5rem] border border-slate-800 shadow-2xl relative overflow-hidden group mockup-container">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl"></div>
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center pb-3 border-b border-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span class="text-[10px] text-white/40 ml-2 font-bold uppercase tracking-widest">ROI Sales Reporting</span>
                                </div>
                                <span class="text-[9px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-400 font-extrabold uppercase pulse-element">Analytics Live</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5 space-y-0.5 mockup-card-interactive pulse-element">
                                    <span class="text-[8px] text-white/40 font-extrabold uppercase tracking-widest">Sales (GMV)</span>
                                    <span class="text-xl font-black text-white">$24,850.00</span>
                                    <span class="text-[9px] text-emerald-400 font-bold block"><i class="fas fa-arrow-up text-[8px] mr-1 animate-bounce"></i> +18.4%</span>
                                </div>
                                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5 space-y-0.5 mockup-card-interactive">
                                    <span class="text-[8px] text-white/40 font-extrabold uppercase tracking-widest">Outreach ROI</span>
                                    <span class="text-xl font-black text-purple-400">3.4x ROI</span>
                                    <span class="text-[9px] text-white/40 font-bold block">Adspend: $7,300</span>
                                </div>
                            </div>
                            <div class="bg-white/5 p-3.5 rounded-xl border border-white/5 space-y-2 mockup-card-interactive">
                                <div class="text-[9px] font-black uppercase text-white/40 tracking-wider">Top Affiliate codes</div>
                                <div class="space-y-1.5 text-[11px]">
                                    <div class="flex justify-between items-center text-white/80 pb-1.5 border-b border-white/5">
                                        <span class="font-bold">ALICE20 <span class="text-[9px] text-white/40 ml-1">Alice Styles</span></span>
                                        <span class="font-black text-white">$12,450.00</span>
                                    </div>
                                    <div class="flex justify-between items-center text-white/80">
                                        <span class="font-bold">MARCUS15 <span class="text-[9px] text-white/40 ml-1">Marcus Vance</span></span>
                                        <span class="font-black text-white">$8,900.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Brand Partners Content -->
                <div id="wi-content-partners" class="whats-inside-content grid lg:grid-cols-12 gap-8 lg:gap-12 items-center hidden">
                    <div class="lg:col-span-5 space-y-6">
                        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl border border-amber-100 shadow-sm">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Brand Partners & Retainers</h3>
                        <p class="text-sm md:text-base text-gray-600 leading-relaxed font-semibold">
                            Track top-performing creators on long-term retainer agreements. Structure status tiers (Gold, Silver, Bronze) automatically based on collaboration performance history and budget milestones.
                        </p>
                        <ul class="space-y-3 text-xs md:text-sm font-bold text-gray-700">
                            <li class="flex items-center gap-3"><i class="fas fa-check text-amber-600"></i> Auto-tier milestones based on historical delivery ratings</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-amber-600"></i> Retainer roster with fixed-term contract tracking</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-amber-600"></i> Creator delivery scoring and individual content star ratings</li>
                            <li class="flex items-center gap-3"><i class="fas fa-check text-amber-600"></i> Quick brand switcher dropdown for multiple agency clients</li>
                        </ul>
                    </div>
                    <!-- Brand Retainer Roster Mockup -->
                    <div class="lg:col-span-7 bg-slate-900 p-6 rounded-[2.5rem] border border-slate-800 shadow-2xl relative overflow-hidden group mockup-container">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-amber-500/10 rounded-full blur-2xl"></div>
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center pb-3 border-b border-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span class="text-[10px] text-white/40 ml-2 font-bold uppercase tracking-widest">Brand Roster & Retainers</span>
                                </div>
                                <span class="text-[9px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 font-extrabold uppercase pulse-element">Ambassadors Active</span>
                            </div>
                            <div class="space-y-2.5">
                                <div class="bg-white/5 p-2.5 rounded-xl border border-white/5 flex items-center justify-between mockup-card-interactive pulse-element">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xs"><i class="fas fa-award"></i></div>
                                        <div>
                                            <span class="text-[11px] font-bold text-white block">Elena Rostova</span>
                                            <span class="text-[8px] text-amber-400 font-extrabold uppercase"><i class="fas fa-medal mr-0.5"></i> Gold Ambassador</span>
                                        </div>
                                    </div>
                                    <div class="text-right text-[11px]">
                                        <span class="text-white font-bold block">$1,200/mo retainer</span>
                                        <span class="text-white/40 text-[9px]">12/12 deliverables</span>
                                    </div>
                                </div>
                                <div class="bg-white/5 p-2.5 rounded-xl border border-white/5 flex items-center justify-between mockup-card-interactive">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded bg-slate-500/20 text-slate-300 flex items-center justify-center font-bold text-xs"><i class="fas fa-award"></i></div>
                                        <div>
                                            <span class="text-[11px] font-bold text-white block">David Kim</span>
                                            <span class="text-[8px] text-slate-300 font-extrabold uppercase"><i class="fas fa-medal mr-0.5"></i> Silver Ambassador</span>
                                        </div>
                                    </div>
                                    <div class="text-right text-[11px]">
                                        <span class="text-white font-bold block">$750/mo retainer</span>
                                        <span class="text-white/40 text-[9px]">8/10 deliverables</span>
                                    </div>
                                </div>
                                <div class="bg-white/5 p-2.5 rounded-xl border border-white/5 flex items-center justify-between mockup-card-interactive">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded bg-yellow-900/20 text-amber-700 flex items-center justify-center font-bold text-xs"><i class="fas fa-award"></i></div>
                                        <div>
                                            <span class="text-[11px] font-bold text-white block">Jessie Doe</span>
                                            <span class="text-[8px] text-amber-700 font-extrabold uppercase"><i class="fas fa-medal mr-0.5"></i> Bronze Ambassador</span>
                                        </div>
                                    </div>
                                    <div class="text-right text-[11px]">
                                        <span class="text-white font-bold block">$400/mo retainer</span>
                                        <span class="text-white/40 text-[9px]">3/4 deliverables</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Resources Section -->
    <section id="resources" class="py-24 px-4 sm:px-6 lg:px-8 bg-slate-900 text-white relative overflow-hidden">
        <!-- Glowing background items for rich premium dark-mode aesthetic -->
        <div class="absolute top-1/4 left-10 w-96 h-96 bg-brand-green opacity-20 rounded-full mix-blend-screen filter blur-3xl animate-blob"></div>
        <div class="absolute bottom-1/4 right-10 w-96 h-96 bg-emerald-600 opacity-15 rounded-full mix-blend-screen filter blur-3xl animate-blob animation-delay-200"></div>

        <div class="max-w-5xl mx-auto relative z-10">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-brand-green/10 border border-brand-green/20 px-4 py-2 rounded-full mb-4">
                    <span class="text-xs font-black text-brand-green uppercase tracking-widest">Free Agency Toolbox</span>
                </div>
                <h2 class="text-4xl lg:text-5xl font-black tracking-tight mb-4">
                    Free Creator & <span class="bg-gradient-to-r from-emerald-400 to-green-300 bg-clip-text text-transparent">Marketing Tools</span>
                </h2>
                <p class="text-gray-400 font-medium max-w-xl mx-auto text-sm md:text-base">
                    Access our premium developer and marketer tools to speed up content creation, research influencer engagement, and format outreach campaigns.
                </p>
            </div>

            <!-- Tab Buttons Container -->
            <div class="flex flex-wrap justify-center gap-3 mb-10">
                <button onclick="switchResourceTab('tiktok-downloader')" id="tab-tiktok-downloader" class="resource-tab-btn flex items-center gap-2.5 px-6 py-3.5 rounded-2xl font-extrabold text-sm transition-all duration-300 bg-brand-green text-white shadow-xl shadow-green-950/20">
                    <i class="fab fa-tiktok"></i>
                    <span>TikTok HD Downloader</span>
                </button>
                <button onclick="switchResourceTab('engagement-calculator')" id="tab-engagement-calculator" class="resource-tab-btn flex items-center gap-2.5 px-6 py-3.5 rounded-2xl font-extrabold text-sm transition-all duration-300 bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10">
                    <i class="fas fa-calculator"></i>
                    <span>Engagement Calculator</span>
                </button>
            </div>

            <!-- Tool 1: TikTok Downloader Container -->
            <div id="content-tiktok-downloader" class="resource-tool-content space-y-6">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 md:p-12 shadow-2xl relative">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="tiktok-url" class="block text-xs font-black text-emerald-400 uppercase tracking-widest">TikTok Video URL</label>
                            <div class="relative flex flex-col md:flex-row gap-3">
                                <div class="relative flex-grow">
                                    <div class="absolute inset-y-0 left-0 pl-4.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fab fa-tiktok text-lg text-emerald-400 pl-4.5"></i>
                                    </div>
                                    <input type="url" id="tiktok-url" placeholder="https://www.tiktok.com/@username/video/..." 
                                           class="block w-full pl-12 pr-4 py-4.5 bg-white/10 border-2 border-white/10 rounded-2xl focus:outline-none focus:border-brand-green transition-all font-bold text-white placeholder-gray-500 text-sm">
                                </div>
                                <button id="btn-tiktok-download" class="px-8 py-4.5 bg-gradient-to-r from-brand-green to-brand-deep hover:from-brand-deep hover:to-[#073D22] text-white font-black rounded-2xl shadow-xl shadow-green-900/30 transition-all duration-300 flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-[0.98]">
                                    <span id="btn-text">Extract HD Video</span>
                                    <i class="fas fa-arrow-down text-sm"></i>
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-500 font-semibold ml-1">Paste any valid TikTok video link to download it cleanly without the watermark.</p>
                        </div>

                        <!-- Loader (Hidden by default) -->
                        <div id="download-loading" class="hidden py-8 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 border-4 border-emerald-400 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-xs font-black text-emerald-400 uppercase tracking-widest animate-pulse">Fetching HD Video from TikTok...</p>
                        </div>

                        <!-- Error Alert (Hidden by default) -->
                        <div id="download-error" class="hidden bg-rose-900/30 border border-rose-500/30 text-rose-300 px-5 py-4 rounded-2xl flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-rose-500 text-lg"></i>
                            <p id="error-message" class="text-xs font-bold"></p>
                        </div>

                        <!-- Results Layout (Hidden by default) -->
                        <div id="download-result" class="hidden border-t border-white/10 pt-8 mt-8 animate-fade-in">
                            <div class="grid md:grid-cols-5 gap-8 items-start">
                                <!-- Left: Video Preview -->
                                <div class="md:col-span-2 relative rounded-2xl overflow-hidden border border-white/10 shadow-2xl bg-black">
                                    <video id="result-video" controls class="w-full h-auto aspect-[9/16] bg-black rounded-2xl" playsinline preload="metadata"></video>
                                </div>

                                <!-- Right: Metadata & Download Actions -->
                                <div class="md:col-span-3 space-y-5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img id="result-avatar" src="" class="w-12 h-12 rounded-full border-2 border-brand-green object-cover" referrerpolicy="no-referrer">
                                            <div>
                                                <h4 id="result-nickname" class="font-extrabold text-white leading-tight">Creator</h4>
                                                <p id="result-username" class="text-xs text-brand-green font-semibold">@username</p>
                                            </div>
                                        </div>
                                        <span id="result-provider" class="px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[11px] font-black rounded-full uppercase tracking-wider">
                                            ⚡ HD No-Watermark
                                        </span>
                                    </div>

                                    <div class="space-y-1">
                                        <h5 class="text-xs font-black text-gray-400 uppercase tracking-widest">Caption</h5>
                                        <p id="result-title" class="text-sm font-semibold text-gray-200 leading-relaxed"></p>
                                    </div>

                                    <!-- Stats Bar -->
                                    <div id="result-stats-bar" class="grid grid-cols-3 gap-2 bg-white/5 p-3 rounded-2xl border border-white/5 text-center">
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase">Likes</p>
                                            <p id="stat-likes" class="text-xs font-extrabold text-white">0</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase">Views</p>
                                            <p id="stat-views" class="text-xs font-extrabold text-emerald-400">0</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase">Comments</p>
                                            <p id="stat-comments" class="text-xs font-extrabold text-white">0</p>
                                        </div>
                                    </div>

                                    <!-- Download Action Buttons -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                        <a id="btn-dl-no-wm" href="" target="_blank" download class="px-5 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-extrabold rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] shadow-lg shadow-emerald-950/20 cursor-pointer">
                                            <i class="fas fa-download text-sm"></i>
                                            <span>Download HD Video</span>
                                        </a>
                                        <a id="btn-dl-audio" href="" target="_blank" download class="px-5 py-4 bg-white/10 hover:bg-white/15 text-white border border-white/10 font-extrabold rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] cursor-pointer">
                                            <i class="fas fa-music text-emerald-400 text-sm"></i>
                                            <span>Download MP3 Audio</span>
                                        </a>
                                    </div>
                                    <p class="text-[11px] text-gray-400 font-semibold italic text-center md:text-left mt-2">
                                        ✅ 100% Watermark-Free HD Download Ready. Click either button above to save directly to your device.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tool 2: Engagement Calculator Container -->
            <div id="content-engagement-calculator" class="resource-tool-content hidden space-y-6">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 md:p-12 shadow-2xl relative">
                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="space-y-5">
                            <h3 class="text-2xl font-black tracking-tight text-white">Engagement Rate Calculator</h3>
                            <p class="text-xs text-gray-400 font-medium leading-relaxed">
                                Enter the creator's metric indicators below. The platform will automatically evaluate engagement standards compared against current marketing standards.
                            </p>
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-black text-emerald-400 uppercase tracking-widest">Total Followers</label>
                                    <input type="number" id="calc-followers" placeholder="e.g. 100000" class="block w-full px-4 py-3.5 bg-white/10 border-2 border-white/10 rounded-2xl focus:outline-none focus:border-brand-green font-bold text-white text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-black text-emerald-400 uppercase tracking-widest">Avg. Likes per Video</label>
                                        <input type="number" id="calc-likes" placeholder="e.g. 5000" class="block w-full px-4 py-3.5 bg-white/10 border-2 border-white/10 rounded-2xl focus:outline-none focus:border-brand-green font-bold text-white text-sm">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-black text-emerald-400 uppercase tracking-widest">Avg. Comments</label>
                                        <input type="number" id="calc-comments" placeholder="e.g. 250" class="block w-full px-4 py-3.5 bg-white/10 border-2 border-white/10 rounded-2xl focus:outline-none focus:border-brand-green font-bold text-white text-sm">
                                    </div>
                                </div>
                                <button onclick="calculateEngagement()" class="w-full py-4 bg-gradient-to-r from-brand-green to-brand-deep hover:from-brand-deep hover:to-[#073D22] text-white font-black rounded-2xl shadow-xl shadow-green-950/20 transition-all flex items-center justify-center gap-2">
                                    <span>Calculate Engagement</span>
                                    <i class="fas fa-calculator text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white/5 rounded-3xl p-6 border border-white/5 flex flex-col justify-center items-center text-center space-y-4">
                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Calculated Score</span>
                            <div class="space-y-1">
                                <span id="calc-result-percentage" class="text-6xl font-black text-emerald-400">0.00%</span>
                                <div id="calc-rating-badge" class="hidden mt-2"><span class="px-3 py-1 text-xs font-black rounded-full">Good Engagement</span></div>
                            </div>
                            <p id="calc-result-text" class="text-xs text-gray-400 font-medium px-4 leading-relaxed">
                                Enter figures and click Calculate to view detailed metric evaluation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- FAQ Section (Resources & Help) -->
    <section id="faq" class="py-24 px-4 sm:px-6 lg:px-8 bg-white text-gray-900 border-t border-gray-100">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-16 tracking-tight text-center md:text-left">
                Questions you’re probably asking.
            </h2>

            <div class="divide-y divide-gray-100 border-t border-b border-gray-100">
                <!-- FAQ Item 1 -->
                <div class="py-6">
                    <button onclick="toggleFaq('faq-1')" class="w-full flex items-center justify-between text-left gap-4 font-bold text-lg md:text-xl text-gray-900 hover:text-brand-green transition-colors focus:outline-none cursor-pointer">
                        <span>Do I need the full platform, or can I start with just the Chrome Extension?</span>
                        <span id="faq-icon-faq-1" class="text-brand-green font-extrabold text-2xl shrink-0 transition-transform duration-300">+</span>
                    </button>
                    <div id="faq-ans-faq-1" class="hidden mt-4 text-gray-600 text-sm md:text-base leading-relaxed pl-1">
                        You can start with either! Our Chrome Extension allows you to source creators directly on Instagram, TikTok, and YouTube with one click. When you're ready to manage campaigns, track budgets, and automate email outreach, the full Instroom platform seamlessly syncs all your data.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="py-6">
                    <button onclick="toggleFaq('faq-2')" class="w-full flex items-center justify-between text-left gap-4 font-bold text-lg md:text-xl text-gray-900 hover:text-brand-green transition-colors focus:outline-none cursor-pointer">
                        <span>What happens after I get early access?</span>
                        <span id="faq-icon-faq-2" class="text-brand-green font-extrabold text-2xl shrink-0 transition-transform duration-300">+</span>
                    </button>
                    <div id="faq-ans-faq-2" class="hidden mt-4 text-gray-600 text-sm md:text-base leading-relaxed pl-1">
                        Once you sign up for early access, you'll receive immediate onboarding credentials, access to our full creator database, custom email sequence templates, and dedicated support to help set up your first campaign.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="py-6">
                    <button onclick="toggleFaq('faq-3')" class="w-full flex items-center justify-between text-left gap-4 font-bold text-lg md:text-xl text-gray-900 hover:text-brand-green transition-colors focus:outline-none cursor-pointer">
                        <span>Can I import my existing creator data from spreadsheets?</span>
                        <span id="faq-icon-faq-3" class="text-brand-green font-extrabold text-2xl shrink-0 transition-transform duration-300">+</span>
                    </button>
                    <div id="faq-ans-faq-3" class="hidden mt-4 text-gray-600 text-sm md:text-base leading-relaxed pl-1">
                        Yes! Instroom allows 1-click CSV imports for your existing influencer rosters, campaign histories, and contact lists so you don't lose any past work.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="py-6">
                    <button onclick="toggleFaq('faq-4')" class="w-full flex items-center justify-between text-left gap-4 font-bold text-lg md:text-xl text-gray-900 hover:text-brand-green transition-colors focus:outline-none cursor-pointer">
                        <span>Is my data secure, especially if I’m managing client campaigns?</span>
                        <span id="faq-icon-faq-4" class="text-brand-green font-extrabold text-2xl shrink-0 transition-transform duration-300">+</span>
                    </button>
                    <div id="faq-ans-faq-4" class="hidden mt-4 text-gray-600 text-sm md:text-base leading-relaxed pl-1">
                        Absolutely. Instroom uses enterprise-grade encryption, secure OAuth2 connections for Gmail/Outlook, and brand-isolated database scoping so your client data remains 100% private and protected.
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="py-6">
                    <button onclick="toggleFaq('faq-5')" class="w-full flex items-center justify-between text-left gap-4 font-bold text-lg md:text-xl text-gray-900 hover:text-brand-green transition-colors focus:outline-none cursor-pointer">
                        <span>What if I outgrow my plan?</span>
                        <span id="faq-icon-faq-5" class="text-brand-green font-extrabold text-2xl shrink-0 transition-transform duration-300">+</span>
                    </button>
                    <div id="faq-ans-faq-5" class="hidden mt-4 text-gray-600 text-sm md:text-base leading-relaxed pl-1">
                        Instroom scales dynamically with your agency or brand. You can upgrade your plan anytime to add unlimited team members, higher email send limits, and advanced API analytics.
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="py-6">
                    <button onclick="toggleFaq('faq-6')" class="w-full flex items-center justify-between text-left gap-4 font-bold text-lg md:text-xl text-gray-900 hover:text-brand-green transition-colors focus:outline-none cursor-pointer">
                        <span>You said the product is still being built. What does that mean for me?</span>
                        <span id="faq-icon-faq-6" class="text-brand-green font-extrabold text-2xl shrink-0 transition-transform duration-300">+</span>
                    </button>
                    <div id="faq-ans-faq-6" class="hidden mt-4 text-gray-600 text-sm md:text-base leading-relaxed pl-1">
                        As an early adopter, you get direct access to our core features, priority feature requests, dedicated technical support, and locked-in early access pricing for life as we release new AI discovery and reporting features.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-10 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom" class="w-12 h-12 object-contain">
                        <span class="text-3xl font-extrabold">Instroom</span>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">The all-in-one influencer marketing platform for Armful Media. Manage campaigns, track analytics, and scale your brand effortlessly.</p>
                    <div class="flex gap-4">
                        <a href="https://web.facebook.com/instroom.io/?_rdc=1&_rdr#" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gray-800 rounded-2xl flex items-center justify-center hover:bg-brand-green transition-all duration-300" title="Facebook">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="https://www.instagram.com/direct/t/106513624083798/" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gray-800 rounded-2xl flex items-center justify-center hover:bg-brand-green transition-all duration-300" title="Instagram">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="https://www.tiktok.com/@instroom.io" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gray-800 rounded-2xl flex items-center justify-center hover:bg-brand-green transition-all duration-300" title="TikTok">
                            <i class="fab fa-tiktok text-xl"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-6">Product</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#features" class="hover:text-brand-green transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">Integrations</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-6">Company</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-brand-green transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-6">Legal</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-brand-green transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-brand-green transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> Instroom by Armful Media. All rights reserved.</p>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-400 text-sm">Made with <i class="fas fa-heart text-red-500"></i> in Manila</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-lg');
            } else {
                nav.classList.remove('shadow-lg');
            }
        });

        // 🌟 100% Functional TikTok HD Video Downloader Script Integration
        document.addEventListener('DOMContentLoaded', function() {
            const downloadBtn = document.getElementById('btn-tiktok-download');
            const urlInput = document.getElementById('tiktok-url');
            const loadingDiv = document.getElementById('download-loading');
            const errorDiv = document.getElementById('download-error');
            const errorMessage = document.getElementById('error-message');
            const resultDiv = document.getElementById('download-result');

            // UI elements inside result
            const resultVideo = document.getElementById('result-video');
            const resultAvatar = document.getElementById('result-avatar');
            const resultNickname = document.getElementById('result-nickname');
            const resultUsername = document.getElementById('result-username');
            const resultTitle = document.getElementById('result-title');
            const resultProvider = document.getElementById('result-provider');
            const statLikes = document.getElementById('stat-likes');
            const statViews = document.getElementById('stat-views');
            const statComments = document.getElementById('stat-comments');
            const btnDlNoWm = document.getElementById('btn-dl-no-wm');
            const btnDlAudio = document.getElementById('btn-dl-audio');

            if (!downloadBtn) return;

            // Allow hitting Enter key in input field
            urlInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    downloadBtn.click();
                }
            });

            downloadBtn.addEventListener('click', function() {
                const url = urlInput.value.trim();
                if (!url) {
                    showError('Please enter a valid TikTok video URL.');
                    return;
                }

                // Clear states
                errorDiv.classList.add('hidden');
                resultDiv.classList.add('hidden');
                loadingDiv.classList.remove('hidden');
                downloadBtn.disabled = true;

                const baseUrl = '<?php echo base_url(); ?>';

                fetch(baseUrl + 'home/tiktok_api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({ 'url': url })
                })
                .then(response => response.json())
                .then(data => {
                    loadingDiv.classList.add('hidden');
                    downloadBtn.disabled = false;

                    if (data && data.status) {
                        // Set metadata
                        resultNickname.textContent = data.nickname || 'Creator';
                        resultUsername.textContent = data.username || '@username';
                        resultAvatar.src = data.avatar || 'https://ui-avatars.com/api/?name=TikTok&background=000&color=fff';
                        resultTitle.textContent = data.title || 'TikTok Video';
                        
                        if (resultProvider) {
                            resultProvider.textContent = '⚡ ' + (data.provider || 'HD No-Watermark');
                        }

                        if (statLikes) statLikes.textContent = data.likes || '0';
                        if (statViews) statViews.textContent = data.views || '0';
                        if (statComments) statComments.textContent = data.comments || '0';

                        // Use H.264 (avc1) watermark-free stream so video renders properly without black screen
                        const targetVideo = data.video_sd || data.video_hd;
                        
                        // Set cover poster and HTML5 video player source via stream proxy
                        if (data.cover) {
                            resultVideo.poster = data.cover;
                        } else {
                            resultVideo.removeAttribute('poster');
                        }
                        resultVideo.src = baseUrl + 'home/download_proxy?url=' + encodeURIComponent(targetVideo) + '&type=video&disposition=inline';
                        resultVideo.load();

                        // Set force attachment download links
                        btnDlNoWm.href = baseUrl + 'home/download_proxy?url=' + encodeURIComponent(targetVideo) + '&type=video&disposition=attachment';
                        
                        if (data.music) {
                            btnDlAudio.href = baseUrl + 'home/download_proxy?url=' + encodeURIComponent(data.music) + '&type=audio&disposition=attachment';
                            btnDlAudio.classList.remove('opacity-50', 'pointer-events-none');
                        } else {
                            btnDlAudio.href = '#';
                            btnDlAudio.classList.add('opacity-50', 'pointer-events-none');
                        }
                        
                        resultDiv.classList.remove('hidden');
                        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } else {
                        showError(data.message || 'Failed to extract video. Please ensure the TikTok link is valid and public.');
                    }
                })
                .catch(error => {
                    loadingDiv.classList.add('hidden');
                    downloadBtn.disabled = false;
                    showError('An error occurred while connecting to the server. Please try again.');
                    console.error('TikTok Extraction Error:', error);
                });
            });

            function showError(msg) {
                errorMessage.textContent = msg;
                errorDiv.classList.remove('hidden');
            }
        });

        // 🌟 FAQ Accordion Toggle
        function toggleFaq(id) {
            const ans = document.getElementById('faq-ans-' + id);
            const icon = document.getElementById('faq-icon-' + id);
            if (!ans) return;

            if (ans.classList.contains('hidden')) {
                ans.classList.remove('hidden');
                if (icon) {
                    icon.textContent = '−';
                    icon.classList.add('text-brand-deep');
                }
            } else {
                ans.classList.add('hidden');
                if (icon) {
                    icon.textContent = '+';
                    icon.classList.remove('text-brand-deep');
                }
            }
        }

        // 🌟 Switch Resource Toolbox Tabs
        function switchResourceTab(tabId) {
            // Hide all tool contents
            document.querySelectorAll('.resource-tool-content').forEach(el => {
                el.classList.add('hidden');
            });
            // Show selected tool content
            const activeContent = document.getElementById('content-' + tabId);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }

            // Reset all tab button styles
            document.querySelectorAll('.resource-tab-btn').forEach(btn => {
                btn.className = "resource-tab-btn flex items-center gap-2.5 px-6 py-3.5 rounded-2xl font-extrabold text-sm transition-all duration-300 bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10";
            });
            // Apply active styles to clicked button
            const activeBtn = document.getElementById('tab-' + tabId);
            if (activeBtn) {
                activeBtn.className = "resource-tab-btn flex items-center gap-2.5 px-6 py-3.5 rounded-2xl font-extrabold text-sm transition-all duration-300 bg-brand-green text-white shadow-xl shadow-green-950/20";
            }
        }

        // 🌟 Engagement Rate Calculator Logic
        function calculateEngagement() {
            const followers = parseFloat(document.getElementById('calc-followers').value) || 0;
            const likes = parseFloat(document.getElementById('calc-likes').value) || 0;
            const comments = parseFloat(document.getElementById('calc-comments').value) || 0;
            const resultPct = document.getElementById('calc-result-percentage');
            const resultBadge = document.getElementById('calc-rating-badge');
            const resultText = document.getElementById('calc-result-text');

            if (followers <= 0) {
                alert('Please enter total followers greater than 0.');
                return;
            }

            const er = ((likes + comments) / followers) * 100;
            resultPct.textContent = er.toFixed(2) + '%';
            resultBadge.classList.remove('hidden');

            let badgeHtml = '';
            let explanation = '';

            if (er >= 5) {
                badgeHtml = '<span class="px-3 py-1 text-xs font-black rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">🔥 Excellent Engagement</span>';
                explanation = 'This creator has outstanding engagement. Highly recommended for premium sponsorships and direct conversions!';
            } else if (er >= 2) {
                badgeHtml = '<span class="px-3 py-1 text-xs font-black rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">✨ Good Engagement</span>';
                explanation = 'Healthy interaction rate. Well above typical industry averages. Great for brand awareness campaigns.';
            } else if (er >= 1) {
                badgeHtml = '<span class="px-3 py-1 text-xs font-black rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">⭐ Average Engagement</span>';
                explanation = 'Average engagement. Suitable for micro-influencer scaling and standard ambassador contracts.';
            } else {
                badgeHtml = '<span class="px-3 py-1 text-xs font-black rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30">⚠️ Below Average</span>';
                explanation = 'Interaction rate is on the lower side. Suggest reviewing their content quality or follower authenticity before sponsorship.';
            }

            resultBadge.innerHTML = badgeHtml;
            resultText.textContent = explanation;
        }

        // 🌟 Whats Inside Tab Switching Logic
        function switchWhatsInsideTab(tabId) {
            // Hide all whats-inside tab contents
            document.querySelectorAll('.whats-inside-content').forEach(el => {
                el.classList.add('hidden');
            });
            // Show active content
            const activeContent = document.getElementById('wi-content-' + tabId);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }

            // Reset all tab button styles to inactive
            document.querySelectorAll('.whats-inside-tab-btn').forEach(btn => {
                btn.className = "whats-inside-tab-btn px-5 py-4 text-xs md:text-base font-extrabold transition-all border-b-2 border-transparent text-gray-500 hover:text-gray-900";
            });

            // Set active style to clicked button
            const activeBtn = document.getElementById('wi-tab-' + tabId);
            if (activeBtn) {
                activeBtn.className = "whats-inside-tab-btn px-5 py-4 text-xs md:text-base font-extrabold transition-all border-b-2 border-brand-green text-brand-green";
            }
        }

        // 🎬 Interactive Watch Demo Video Modal Functions
        function openDemoModal() {
            const modal = document.getElementById('demo-modal');
            const modalBox = document.getElementById('demo-modal-box');
            const iframe = document.getElementById('demo-video-iframe');
            
            // Set high quality product demo walkthrough embed
            iframe.src = "https://www.youtube-nocookie.com/embed/L_LUpnjgPso?autoplay=1&rel=0&modestbranding=1";
            
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            modalBox.classList.remove('scale-95');
            modalBox.classList.add('scale-100');
            document.body.classList.add('overflow-hidden');
        }

        function closeDemoModal() {
            const modal = document.getElementById('demo-modal');
            const modalBox = document.getElementById('demo-modal-box');
            const iframe = document.getElementById('demo-video-iframe');
            
            modal.classList.remove('opacity-100', 'pointer-events-auto');
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalBox.classList.remove('scale-100');
            modalBox.classList.add('scale-95');
            document.body.classList.remove('overflow-hidden');
            
            // Clear iframe src after transition to stop playback
            setTimeout(() => {
                iframe.src = "";
            }, 300);
        }

        function seekDemoChapter(seconds) {
            const iframe = document.getElementById('demo-video-iframe');
            iframe.src = "https://www.youtube-nocookie.com/embed/L_LUpnjgPso?autoplay=1&rel=0&modestbranding=1&start=" + seconds;
        }

        // Close demo modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDemoModal();
            }
        });
    </script>

    <!-- 🎬 Watch Demo Modal Overlay -->
    <div id="demo-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-10 opacity-0 pointer-events-none transition-all duration-300">
        <!-- Backdrop -->
        <div onclick="closeDemoModal()" class="absolute inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"></div>

        <!-- Modal Card Container -->
        <div class="relative w-full max-w-5xl bg-slate-900 rounded-[2.5rem] border border-slate-800 shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="demo-modal-box">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-950/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-green/20 border border-brand-green/30 rounded-2xl flex items-center justify-center text-brand-green shadow-md">
                        <i class="fas fa-play text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white tracking-tight">Instroom Platform Walkthrough</h3>
                        <p class="text-xs font-semibold text-slate-400">Interactive Creator Discovery & Campaign Suite</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        HD Demo Video
                    </span>
                    <button onclick="closeDemoModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors flex items-center justify-center font-bold text-base cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Video Player Aspect Box -->
            <div class="relative aspect-video bg-slate-950 flex items-center justify-center overflow-hidden">
                <iframe id="demo-video-iframe" class="w-full h-full border-0" src="" title="Instroom Demo Video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>

            <!-- Modal Footer & Interactive Chapter Timestamps -->
            <div class="p-5 md:p-6 bg-slate-950/90 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-xs font-semibold">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[11px]">Jump to Feature:</span>
                    <button onclick="seekDemoChapter(0)" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-brand-green/20 hover:text-brand-green text-slate-300 transition-all border border-slate-700/50 cursor-pointer">
                        <i class="fas fa-search text-[10px] text-brand-green mr-1"></i> Discovery Engine
                    </button>
                    <button onclick="seekDemoChapter(45)" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-brand-green/20 hover:text-brand-green text-slate-300 transition-all border border-slate-700/50 cursor-pointer">
                        <i class="fas fa-users text-[10px] text-blue-400 mr-1"></i> Creator CRM
                    </button>
                    <button onclick="seekDemoChapter(90)" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-brand-green/20 hover:text-brand-green text-slate-300 transition-all border border-slate-700/50 cursor-pointer">
                        <i class="fas fa-chart-line text-[10px] text-amber-400 mr-1"></i> Real-time ROI
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?php echo base_url('auth/register'); ?>" class="px-6 py-2.5 bg-brand-green hover:bg-brand-deep text-white text-xs font-extrabold rounded-xl shadow-lg shadow-green-900/30 transition-all hover:scale-105 flex items-center gap-2">
                        <span>Get Started Free</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>