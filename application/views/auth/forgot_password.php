<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Instroom</title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo_icon.png'); ?>">
    <link rel="shortcut icon" href="<?php echo base_url('favicon.ico'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
                            offwhite: '#F8FAFC'
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.6s ease-out forwards',
                        'float': 'float 5s ease-in-out infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.08)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.92)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="bg-brand-offwhite h-screen flex overflow-hidden font-sans">

    <!-- Left Side - Branding & Agency Visuals -->
    <div class="hidden lg:flex w-[55%] relative items-center justify-center overflow-hidden bg-gradient-to-br from-[#062415] via-[#0A3C23] to-[#04190E] p-12">
        <!-- Background Glow Blobs -->
        <div class="absolute top-0 -left-10 w-[30rem] h-[30rem] bg-brand-green opacity-25 rounded-full mix-blend-screen filter blur-3xl animate-blob"></div>
        <div class="absolute bottom-0 right-0 w-[30rem] h-[30rem] bg-emerald-600 opacity-20 rounded-full mix-blend-screen filter blur-3xl animate-blob animation-delay-200"></div>
        
        <!-- Grid Overlay -->
        <div class="absolute inset-0 opacity-15" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 36px 36px;"></div>

        <div class="relative z-10 max-w-xl text-left">
            <!-- Brand Badge Header -->
            <div class="flex items-center gap-4 mb-10 animate-fade-in">
                <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom" class="w-14 h-14 object-contain">
                <div>
                    <span class="text-2xl font-black text-white tracking-tight block">Instroom</span>
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Powered by Armful Media</span>
                </div>
            </div>

            <!-- Headline -->
            <div class="mb-10 animate-slide-up">
                <h1 class="text-5xl font-black text-white leading-[1.15] tracking-tight mb-5">
                    Account Recovery & <span class="bg-gradient-to-r from-emerald-400 to-green-300 bg-clip-text text-transparent">Security Center</span>
                </h1>
                <p class="text-base md:text-lg text-emerald-100/80 leading-relaxed font-medium">
                    Reset your password safely via PHPMailer Gmail SMTP. Receive instant single-use reset links to regain access to your workspace.
                </p>
            </div>

            <!-- System Highlights Feature Cards -->
            <div class="grid grid-cols-2 gap-5 animate-slide-up animate-delay-200">
                <div class="glass-panel p-5 rounded-3xl space-y-2">
                    <div class="w-10 h-10 bg-emerald-400/20 text-emerald-300 rounded-xl flex items-center justify-center text-lg font-black">
                        📧
                    </div>
                    <h4 class="text-white font-extrabold text-sm">Gmail PHPMailer</h4>
                    <p class="text-emerald-100/60 text-xs font-medium">Instant SMTP email dispatch.</p>
                </div>
                <div class="glass-panel p-5 rounded-3xl space-y-2">
                    <div class="w-10 h-10 bg-amber-400/20 text-amber-300 rounded-xl flex items-center justify-center text-lg font-black">
                        🔒
                    </div>
                    <h4 class="text-white font-extrabold text-sm">Token Encryption</h4>
                    <p class="text-emerald-100/60 text-xs font-medium">Secure single-use token expiry.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Forgot Password Form -->
    <div class="w-full lg:w-[45%] flex items-center justify-center p-6 md:p-12 bg-white relative overflow-y-auto">
        <div class="w-full max-w-md animate-slide-up">
            
            <!-- Top Nav Back Link -->
            <div class="flex items-center justify-between mb-8">
                <a href="<?php echo base_url('auth/login'); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-gray-50 text-gray-700 text-xs font-bold hover:bg-gray-100 border border-gray-100 transition-all">
                    <i class="fas fa-arrow-left text-brand-green"></i>
                    Back to Sign In
                </a>
                <span class="text-xs font-bold text-gray-400">Account Recovery</span>
            </div>

            <!-- Mobile Brand Header -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <img src="<?php echo base_url('assets/images/logo_icon.png'); ?>" alt="Instroom" class="w-12 h-12 object-contain">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Instroom</h3>
                    <p class="text-xs font-bold text-emerald-600">Armful Media</p>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-2 tracking-tight">Forgot Password?</h2>
                <p class="text-sm text-gray-500 font-medium">Enter your email and we'll send you an instant reset link.</p>
            </div>

            <!-- Flash Alerts -->
            <?php if($this->session->flashdata('error')): ?>
                <div class="bg-rose-50 border border-rose-100 text-rose-800 px-5 py-4 mb-6 rounded-2xl flex items-center gap-3 animate-fade-in shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500 text-lg"></i>
                    <p class="text-xs font-bold"><?php echo $this->session->flashdata('error'); ?></p>
                </div>
            <?php endif; ?>

            <?php if($this->session->flashdata('success')): ?>
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 px-5 py-4 mb-6 rounded-2xl flex items-center gap-3 animate-fade-in shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                    <div class="text-xs leading-relaxed"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?php echo base_url('auth/forgot_password'); ?>" method="POST" class="space-y-5">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-black text-gray-500 uppercase tracking-wider ml-1">Account Email</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-green transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input type="email" name="email" id="email" required 
                            class="block w-full pl-11 pr-4 py-3.5 bg-gray-50/80 border-2 border-gray-100 rounded-2xl focus:outline-none focus:bg-white focus:border-brand-green transition-all font-bold text-gray-800 placeholder-gray-400 text-sm" 
                            placeholder="user@armfulmedia.com">
                    </div>
                </div>

                <button class="w-full bg-gradient-to-r from-brand-green to-brand-deep hover:from-brand-deep hover:to-[#073D22] text-white font-black py-4 px-6 rounded-2xl shadow-xl shadow-green-200 transition-all duration-300 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-3 group mt-6" type="submit">
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Send Reset Email</span>
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-gray-100">
                <p class="text-xs font-bold text-gray-500">
                    Remember your password? 
                    <a href="<?php echo base_url('auth/login'); ?>" class="text-brand-green hover:underline font-extrabold ml-1">Sign in here</a>
                </p>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">&copy; <?php echo date('Y'); ?> Instroom by Armful Media</p>
            </div>
        </div>
    </div>

</body>
</html>