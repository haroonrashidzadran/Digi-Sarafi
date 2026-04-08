<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Digi Sarafi - Professional Hawala & Exchange Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            50: '#fbfaf4',
                            100: '#f5f2e3',
                            200: '#ebe4c7',
                            300: '#dfd4a6',
                            400: '#d4c285',
                            500: '#c9b064',
                            600: '#a28c36',
                            700: '#7a6a2a',
                            800: '#52481d',
                            900: '#2a2610',
                        },
                        emerald: {
                            600: '#059669',
                            700: '#047857',
                        },
                        slate: {
                            850: '#1e293b',
                        }
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        arabic: ['Noto Sans Arabic', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
        .gold-gradient {
            background: linear-gradient(135deg, #c9b064 0%, #d4c285 50%, #c9b064 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .pulse-gold {
            animation: pulse-gold 2s ease-in-out infinite;
        }
        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(201, 176, 100, 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(201, 176, 100, 0); }
        }
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">
    <!-- Decorative Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gold-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gold-500/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-10 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 gold-gradient rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-transfer text-gray-900 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Digi Sarafi</h1>
                        <p class="text-xs text-gold-400 tracking-widest uppercase">Hawala Management System</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <a href="#features" class="text-gray-300 hover:text-gold-400 transition-colors text-sm font-medium">Features</a>
                    <a href="#modules" class="text-gray-300 hover:text-gold-400 transition-colors text-sm font-medium">Modules</a>
                    <a href="{{ route('login') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-sm font-medium transition-all">
                        <i class="fa-solid fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 gold-gradient text-gray-900 rounded-lg text-sm font-bold hover:opacity-90 transition-all">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative z-10">
        <div class="max-w-7xl mx-auto px-6 py-20">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gold-500/10 border border-gold-500/20 mb-6">
                        <span class="w-2 h-2 bg-gold-400 rounded-full pulse-gold"></span>
                        <span class="text-gold-400 text-sm font-medium">Trusted by 500+ Sarafi Businesses</span>
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-bold leading-tight mb-6">
                        Modern <span class="text-transparent bg-clip-text gold-gradient">Hawala</span> Management
                    </h1>
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                        Enterprise-grade Sarafi management system designed for Afghan financial operations. 
                        Track transfers, manage multi-currency exchanges, and settle with partners — all in one secure platform.
                    </p>
                    <div class="flex flex-wrap gap-4 mb-12">
                        <a href="{{ route('register') }}" class="px-8 py-4 gold-gradient text-gray-900 rounded-xl text-lg font-bold hover:opacity-90 transition-all flex items-center gap-3">
                            <i class="fa-solid fa-rocket"></i>
                            Start Free Trial
                        </a>
                        <a href="#demo" class="px-8 py-4 glass-card rounded-xl text-lg font-medium hover:bg-white/10 transition-all flex items-center gap-3">
                            <i class="fa-solid fa-play text-gold-400"></i>
                            Watch Demo
                        </a>
                    </div>
                    <div class="flex items-center gap-8">
                        <div class="flex -space-x-3">
                            <img src="https://i.pravatar.cc/100?img=1" class="w-10 h-10 rounded-full border-2 border-gray-900" alt="User">
                            <img src="https://i.pravatar.cc/100?img=2" class="w-10 h-10 rounded-full border-2 border-gray-900" alt="User">
                            <img src="https://i.pravatar.cc/100?img=3" class="w-10 h-10 rounded-full border-2 border-gray-900" alt="User">
                            <img src="https://i.pravatar.cc/100?img=4" class="w-10 h-10 rounded-full border-2 border-gray-900" alt="User">
                        </div>
                        <div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-star text-gold-400"></i>
                                <i class="fa-solid fa-star text-gold-400"></i>
                                <i class="fa-solid fa-star text-gold-400"></i>
                                <i class="fa-solid fa-star text-gold-400"></i>
                                <i class="fa-solid fa-star text-gold-400"></i>
                            </div>
                            <p class="text-sm text-gray-400">Trusted by 2,500+ users</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-gold-500/20 to-emerald-500/20 rounded-3xl blur-2xl"></div>
                    <div class="relative glass-card rounded-3xl p-8 floating">
                        <!-- Dashboard Preview -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <span class="text-xs text-gray-400">Dashboard Preview</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-emerald-600/20 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-arrow-trend-up text-emerald-500"></i>
                                    </div>
                                    <span class="text-gray-400 text-sm">Today's Volume</span>
                                </div>
                                <p class="text-2xl font-bold">AFN 45.2M</p>
                                <p class="text-xs text-emerald-500">+12.5% from yesterday</p>
                            </div>
                            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gold-500/20 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-money-bill-transfer text-gold-400"></i>
                                    </div>
                                    <span class="text-gray-400 text-sm">Transfers</span>
                                </div>
                                <p class="text-2xl font-bold">1,247</p>
                                <p class="text-xs text-gold-400">87 pending</p>
                            </div>
                        </div>
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10 mb-4">
                            <div class="flex items-center justify-between mb-4">
                                <span class="font-medium">Currency Rates</span>
                                <a href="#" class="text-gold-400 text-sm hover:underline">Update</a>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🇺🇸</span>
                                        <span>USD/AFN</span>
                                    </div>
                                    <span class="font-bold text-emerald-500">68.50</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🇵🇰</span>
                                        <span>PKR/AFN</span>
                                    </div>
                                    <span class="font-bold text-emerald-500">0.24</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🇦🇪</span>
                                        <span>AED/AFN</span>
                                    </div>
                                    <span class="font-bold text-emerald-500">18.65</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="font-medium">Partner Balances</span>
                                <a href="#" class="text-gold-400 text-sm hover:underline">View All</a>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                            <i class="fa-solid fa-building-columns text-blue-400 text-xs"></i>
                                        </div>
                                        <span>Kabul Exchange</span>
                                    </div>
                                    <span class="font-bold text-red-400">-AFN 2.5M</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                                            <i class="fa-solid fa-building-columns text-purple-400 text-xs"></i>
                                        </div>
                                        <span>Peshawar Sarafi</span>
                                    </div>
                                    <span class="font-bold text-emerald-500">+AFN 1.8M</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="relative z-10 py-20 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Everything You Need to Run Your Sarafi</h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    A complete solution built specifically for Afghan financial operations, 
                    combining traditional hawala practices with modern technology.
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="glass-card rounded-2xl p-8 hover:bg-white/10 transition-all group">
                    <div class="w-14 h-14 bg-gold-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-money-bill-transfer text-gold-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Hawala Transfers</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Complete transfer lifecycle management with OTP verification, 
                        partner tracking, and real-time status updates.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card rounded-2xl p-8 hover:bg-white/10 transition-all group">
                    <div class="w-14 h-14 bg-emerald-600/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-arrows-rotate text-emerald-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Multi-Currency Exchange</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Support for AFN, USD, PKR, IRR, AED with manual rate management 
                        and automatic profit calculation.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card rounded-2xl p-8 hover:bg-white/10 transition-all group">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-handshake text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Partner Management</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Track partner balances, trust levels, settlement history, 
                        and exposure across multiple currencies.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="glass-card rounded-2xl p-8 hover:bg-white/10 transition-all group">
                    <div class="w-14 h-14 bg-purple-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-book-open text-purple-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Double-Entry Accounting</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Enterprise-grade accounting with balanced journal entries, 
                        audit trails, and immutable records.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="glass-card rounded-2xl p-8 hover:bg-white/10 transition-all group">
                    <div class="w-14 h-14 bg-amber-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chart-line text-amber-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Real-time Reports</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Daily cash reports, P&L statements, partner exposure, 
                        and currency position dashboards.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="glass-card rounded-2xl p-8 hover:bg-white/10 transition-all group">
                    <div class="w-14 h-14 bg-rose-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-shield-halved text-rose-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Bank-Grade Security</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Role-based access control, OTP for transfers, 
                        and complete audit logging for compliance.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="relative z-10 py-20 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">System Modules</h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Six integrated modules working together to streamline your operations.
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Module 1 -->
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-2xl p-6 border border-white/10 hover:border-gold-500/30 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-gold-500/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-users text-gold-400 text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-emerald-600/20 text-emerald-500 rounded-full text-xs font-medium">Active</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Customer Management</h3>
                    <p class="text-gray-400 text-sm">Track customers, preferences, and transaction history.</p>
                </div>

                <!-- Module 2 -->
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-2xl p-6 border border-white/10 hover:border-emerald-500/30 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-emerald-600/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-handshake text-emerald-500 text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-emerald-600/20 text-emerald-500 rounded-full text-xs font-medium">Active</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Partner Network</h3>
                    <p class="text-gray-400 text-sm">Manage hawala partners across Afghanistan and abroad.</p>
                </div>

                <!-- Module 3 -->
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-2xl p-6 border border-white/10 hover:border-blue-500/30 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-cash-register text-blue-400 text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-emerald-600/20 text-emerald-500 rounded-full text-xs font-medium">Active</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Cash Sessions</h3>
                    <p class="text-gray-400 text-sm">Daily cash opening/closing with balance tracking.</p>
                </div>

                <!-- Module 4 -->
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-2xl p-6 border border-white/10 hover:border-purple-500/30 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-file-invoice text-purple-400 text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-emerald-600/20 text-emerald-500 rounded-full text-xs font-medium">Active</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">General Ledger</h3>
                    <p class="text-gray-400 text-sm">Complete double-entry accounting with audit trail.</p>
                </div>

                <!-- Module 5 -->
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-2xl p-6 border border-white/10 hover:border-amber-500/30 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-chart-pie text-amber-400 text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-emerald-600/20 text-emerald-500 rounded-full text-xs font-medium">Active</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Reports & Analytics</h3>
                    <p class="text-gray-400 text-sm">Financial reports, tax summaries, and business insights.</p>
                </div>

                <!-- Module 6 -->
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-2xl p-6 border border-white/10 hover:border-rose-500/30 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-rose-500/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-gear text-rose-400 text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-emerald-600/20 text-emerald-500 rounded-full text-xs font-medium">Active</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2">System Settings</h3>
                    <p class="text-gray-400 text-sm">Configure currencies, rates, branches, and users.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="relative z-10 py-16 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-gold-400 mb-2">$2.5B+</div>
                    <div class="text-gray-400">Transactions Processed</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-emerald-500 mb-2">500+</div>
                    <div class="text-gray-400">Active Sarafis</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-400 mb-2">15+</div>
                    <div class="text-gray-400">Countries Supported</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-400 mb-2">99.9%</div>
                    <div class="text-gray-400">Uptime Guarantee</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative z-10 py-20">
        <div class="max-w-4xl mx-auto px-6">
            <div class="glass-card rounded-3xl p-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-gold-500/10 to-emerald-600/10"></div>
                <div class="relative z-10">
                    <h2 class="text-4xl font-bold mb-4">Ready to Modernize Your Sarafi?</h2>
                    <p class="text-xl text-gray-300 mb-8 max-w-xl mx-auto">
                        Join hundreds of Afghan financial businesses already using Digi Sarafi 
                        to streamline their operations.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('register') }}" class="px-8 py-4 gold-gradient text-gray-900 rounded-xl text-lg font-bold hover:opacity-90 transition-all">
                            <i class="fa-solid fa-rocket mr-2"></i>Start Free Trial
                        </a>
                        <a href="#contact" class="px-8 py-4 border border-white/20 rounded-xl text-lg font-medium hover:bg-white/10 transition-all">
                            <i class="fa-solid fa-phone mr-2"></i>Contact Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-white/10 py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 gold-gradient rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-transfer text-gray-900"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Digi Sarafi</h4>
                        <p class="text-xs text-gray-400">Afghan Cosmos Projects</p>
                    </div>
                </div>
                <div class="flex items-center gap-6 text-sm text-gray-400">
                    <a href="#" class="hover:text-gold-400 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-gold-400 transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-gold-400 transition-colors">Support</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            <div class="text-center mt-8 pt-8 border-t border-white/10 text-sm text-gray-500">
                &copy; 2024 Digi Sarafi. All rights reserved. Built by <span class="text-gold-400">Afghan Cosmos</span>
            </div>
        </div>
    </footer>
</body>
</html>
