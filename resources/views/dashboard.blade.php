<!DOCTYPE html>
<html lang="tr" x-data="ariRotaApp()" x-init="initApp()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ArıRota — Ege Bölgesi akıllı kovan konumlandırma, canlı flora haritası ve gezginci arıcılık platformu.">
    <title>ArıRota | Akıllı Kovan Konumlandırma Platformu</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        obsidian: { DEFAULT: '#090A0F', 50: '#1A1D2E', 100: '#12141D', 200: '#0D0F18' },
                        amber: { honey: '#F59E0B', glow: '#FACC15', deep: '#D97706' },
                        emerald: { flora: '#10B981', glow: '#34D399' },
                        crimson: { alert: '#EF4444', glow: '#F87171' },
                        sky: { metric: '#3B82F6', glow: '#60A5FA' },
                    },
                    fontFamily: { inter: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    boxShadow: {
                        'honey-glow': '0 0 20px rgba(245, 158, 11, 0.4)',
                        'green-glow': '0 0 20px rgba(16, 185, 129, 0.35)',
                        'red-glow': '0 0 20px rgba(239, 68, 68, 0.35)',
                        'blue-glow': '0 0 15px rgba(59, 130, 246, 0.3)',
                    }
                }
            }
        }
    </script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #090A0F; color: #E2E8F0; font-family: 'Inter', sans-serif; overflow: hidden; }

        /* Honeycomb Background Pattern */
        .hex-pattern {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='100'%3E%3Cpath d='M28 66L0 50V16L28 0l28 16v34L28 66zm0-8l20-12V20L28 8 8 20v26l20 12z' fill='%23F59E0B' fill-opacity='0.04'/%3E%3C/svg%3E");
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #12141D; }
        ::-webkit-scrollbar-thumb { background: #F59E0B; border-radius: 2px; }

        /* Leaflet Map Dark Override */
        #map { height: 100%; width: 100%; z-index: 1; }
        .leaflet-container { background: #090A0F !important; }
        .leaflet-tile-pane {
            filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);
        }

        /* Custom Marker Styles */
        .custom-marker { position: relative; }
        .marker-pulse {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; cursor: pointer; position: relative;
            border: 2px solid transparent; transition: all 0.3s ease;
        }
        .marker-pulse::before {
            content: ''; position: absolute; inset: -4px; border-radius: 50%;
            border: 2px solid; animation: pulse 2s infinite; opacity: 0.6;
        }
        .marker-green { background: rgba(16,185,129,0.25); border-color: #10B981; }
        .marker-green::before { border-color: #10B981; }
        .marker-yellow { background: rgba(245,158,11,0.25); border-color: #F59E0B; }
        .marker-yellow::before { border-color: #F59E0B; }
        .marker-red { background: rgba(239,68,68,0.25); border-color: #EF4444; }
        .marker-red::before { border-color: #EF4444; }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.4); opacity: 0.2; }
            100% { transform: scale(1); opacity: 0.6; }
        }

        /* Custom Popup */
        .leaflet-popup-content-wrapper {
            background: #12141D !important; border: 1px solid #F59E0B !important;
            border-radius: 12px !important; color: #E2E8F0 !important;
            box-shadow: 0 0 20px rgba(245,158,11,0.3) !important;
        }
        .leaflet-popup-tip { background: #F59E0B !important; }
        .leaflet-popup-content { margin: 12px 16px !important; }

        /* Glass Card */
        .glass-card {
            background: rgba(18, 20, 29, 0.95);
            border: 1px solid rgba(245, 158, 11, 0.15);
            backdrop-filter: blur(16px);
        }

        /* Status Banner */
        .status-safe {
            background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
            border: 1px solid rgba(16,185,129,0.4);
            box-shadow: 0 0 20px rgba(16,185,129,0.15);
        }
        .status-warn {
            background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05));
            border: 1px solid rgba(245,158,11,0.4);
            box-shadow: 0 0 20px rgba(245,158,11,0.15);
        }
        .status-danger {
            background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05));
            border: 1px solid rgba(239,68,68,0.4);
            box-shadow: 0 0 20px rgba(239,68,68,0.15);
        }

        /* Progress Bar */
        .progress-bar-track {
            background: rgba(255,255,255,0.08);
            border-radius: 999px; overflow: hidden; height: 6px;
        }
        .progress-bar-fill {
            height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, #F59E0B, #FACC15);
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .progress-bar-fill.fill-green { background: linear-gradient(90deg, #10B981, #34D399); }
        .progress-bar-fill.fill-yellow { background: linear-gradient(90deg, #F59E0B, #FACC15); }
        .progress-bar-fill.fill-red { background: linear-gradient(90deg, #EF4444, #F87171); }

        /* Pill Filter Button */
        .pill-btn {
            padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.05);
            color: #94A3B8; cursor: pointer; transition: all 0.2s ease; white-space: nowrap;
        }
        .pill-btn:hover { border-color: rgba(245,158,11,0.5); color: #F59E0B; }
        .pill-btn.active {
            background: rgba(245,158,11,0.2); border-color: #F59E0B;
            color: #FACC15; box-shadow: 0 0 12px rgba(245,158,11,0.25);
        }

        /* Toggle Switch */
        .toggle-track {
            width: 36px; height: 20px; border-radius: 999px; position: relative;
            background: rgba(255,255,255,0.1); cursor: pointer; transition: background 0.3s;
            flex-shrink: 0;
        }
        .toggle-track.on { background: rgba(245,158,11,0.6); }
        .toggle-thumb {
            width: 14px; height: 14px; border-radius: 50%; background: #fff;
            position: absolute; top: 3px; left: 3px; transition: left 0.3s ease;
            box-shadow: 0 1px 4px rgba(0,0,0,0.4);
        }
        .toggle-track.on .toggle-thumb { left: 19px; }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 1000;
            background: rgba(9,10,15,0.85); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
        }
        .modal-box {
            background: #12141D; border: 1px solid rgba(245,158,11,0.25);
            border-radius: 20px; width: 90%; max-width: 560px;
            box-shadow: 0 0 40px rgba(245,158,11,0.15);
            max-height: 90vh; overflow-y: auto;
        }

        /* Pricing Card */
        .pricing-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px; padding: 24px; transition: all 0.3s;
        }
        .pricing-card.featured {
            border-color: #F59E0B;
            background: rgba(245,158,11,0.08);
            box-shadow: 0 0 24px rgba(245,158,11,0.2);
        }
        .pricing-card:hover { transform: translateY(-3px); }

        /* Input styles */
        .input-dark {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px; padding: 10px 14px;
            color: #E2E8F0; width: 100%; font-size: 14px;
            transition: border-color 0.2s;
            outline: none;
        }
        .input-dark:focus { border-color: #F59E0B; box-shadow: 0 0 0 3px rgba(245,158,11,0.15); }
        .input-dark::placeholder { color: #64748B; }

        /* Bottom strip cards */
        .info-card {
            background: rgba(18,20,29,0.9); border: 1px solid rgba(245,158,11,0.1);
            border-radius: 14px; padding: 16px; transition: all 0.3s;
        }
        .info-card:hover { border-color: rgba(245,158,11,0.35); transform: translateY(-2px); }

        /* Metric Card */
        .metric-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px; padding: 14px;
            text-align: center; transition: all 0.2s;
        }
        .metric-card:hover { border-color: rgba(245,158,11,0.3); }

        /* Sidebar scroll */
        .sidebar-scroll { overflow-y: auto; max-height: calc(100vh - 88px - 120px); }
        .sidebar-scroll::-webkit-scrollbar { width: 3px; }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.4s ease forwards; }

        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .shimmer-text {
            background: linear-gradient(90deg, #F59E0B, #FACC15, #F59E0B);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s linear infinite;
        }

        /* Radar badge blink */
        @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0.4; } }
        .blink { animation: blink 1.5s ease-in-out infinite; }

        /* Number spinner */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .count-anim { animation: countUp 0.5s ease forwards; }

        /* Map container full height */
        #map-container { position: relative; }

        /* Hover heat layer marker */
        .heat-layer { opacity: 0.35 !important; }

        /* Upload box */
        .upload-box {
            border: 2px dashed rgba(245,158,11,0.3);
            border-radius: 12px; padding: 24px; text-align: center;
            cursor: pointer; transition: all 0.2s;
        }
        .upload-box:hover { border-color: #F59E0B; background: rgba(245,158,11,0.05); }
    </style>
</head>

<body class="flex flex-col h-screen">
    <div class="hex-pattern"></div>

    <!-- ======================================================
         TOP NAVIGATION BAR
    ====================================================== -->
    <nav class="relative z-50 glass-card border-b border-amber-honey/20 px-4 py-2.5 flex items-center gap-4" style="min-height:64px">

        <!-- Logo -->
        <div class="flex items-center gap-2.5 flex-shrink-0">
            <div class="relative">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg" style="background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.5);box-shadow:0 0 16px rgba(245,158,11,0.3)">
                    <span class="text-lg">🐝</span>
                </div>
                <div class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 blink border border-obsidian"></div>
            </div>
            <div>
                <div class="text-lg font-black tracking-tight shimmer-text">ArıRota</div>
                <div class="flex items-center gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 blink"></div>
                    <span class="text-xs text-slate-500 font-mono">Ege Bölgesi Canlı Radar v1.0</span>
                </div>
            </div>
        </div>

        <!-- Center Filters -->
        <div class="flex-1 flex items-center gap-3 justify-center flex-wrap">
            <!-- Season Pills -->
            <div class="flex items-center gap-1.5">
                <button @click="setFilter('tumu')" :class="activeFilter === 'tumu' ? 'active' : ''" class="pill-btn">
                     Tümü
                </button>
                <button @click="setFilter('ilkbahar')" :class="activeFilter === 'ilkbahar' ? 'active' : ''" class="pill-btn">
                     Narenciye
                </button>
                <button @click="setFilter('yaz')" :class="activeFilter === 'yaz' ? 'active' : ''" class="pill-btn">
                     Kestane & Kekik
                </button>
                <button @click="setFilter('sonbahar')" :class="activeFilter === 'sonbahar' ? 'active' : ''" class="pill-btn">
                     Çam Balı
                </button>
            </div>

            <!-- Divider -->
            <div class="w-px h-6 bg-white/10 hidden lg:block"></div>

            <!-- Layer Toggles -->
            <div class="hidden lg:flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer" @click="toggleLayer('yogunluk')">
                    <div class="toggle-track" :class="layers.yogunluk ? 'on' : ''">
                        <div class="toggle-thumb"></div>
                    </div>
                    <span class="text-xs text-slate-400"> Isı Çemberleri</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer" @click="toggleLayer('ilaclama')">
                    <div class="toggle-track" :class="layers.ilaclama ? 'on' : ''">
                        <div class="toggle-thumb"></div>
                    </div>
                    <span class="text-xs text-slate-400"> İlaçlama Alanları</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer" @click="toggleLayer('radar')">
                    <div class="toggle-track" :class="layers.radar ? 'on' : ''">
                        <div class="toggle-thumb"></div>
                    </div>
                    <span class="text-xs text-slate-400"> Rüzgar/Nem Radar</span>
                </label>
            </div>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-2 flex-shrink-0">
            <button @click="showCheckinModal = true"
                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-obsidian transition-all duration-200 hover:scale-105"
                style="background:linear-gradient(135deg,#F59E0B,#FACC15);box-shadow:0 0 14px rgba(245,158,11,0.4)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Kovan Konumu Ekle
            </button>
            <button @click="showPricingModal = true"
                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-amber-300 border transition-all duration-200 hover:scale-105 hover:bg-amber-honey/10"
                style="border-color:rgba(245,158,11,0.4);background:rgba(245,158,11,0.08)">
                💎 PRO / Birlik
            </button>
        </div>
    </nav>

    <!-- ======================================================
         MAIN WORKSPACE
    ====================================================== -->
    <div class="flex-1 flex relative z-10 overflow-hidden" style="height: calc(100vh - 64px - 88px)">

        <!-- ========== LEFT: MAP ========== -->
        <div id="map-container" class="flex-1 relative">
            <div id="map"></div>

            <!-- Map Overlay Badge -->
            <div class="absolute top-3 left-3 z-20 flex items-center gap-2 px-3 py-2 rounded-xl glass-card text-xs">
                <div class="w-2 h-2 rounded-full bg-emerald-400 blink"></div>
                <span class="text-slate-400">Canlı • <span class="text-amber-300 font-semibold" x-text="visibleCount + ' Aktif Bölge'"></span></span>
                <span class="text-slate-600">|</span>
                <span class="text-emerald-500 font-semibold">🌐 Open-Meteo API</span>
                <span class="text-slate-600">|</span>
                <span class="text-slate-500">OSM Dark Radar</span>
            </div>

            <!-- Loading Overlay -->
            <div x-show="mapLoading" class="absolute inset-0 z-30 flex items-center justify-center" style="background:rgba(9,10,15,0.7)">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-2 border-amber-honey border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-amber-300 text-sm font-semibold">Harita yükleniyor...</span>
                </div>
            </div>
        </div>

        <!-- ========== RIGHT: TELEMETRY PANEL ========== -->
        <div class="w-80 glass-card border-l border-amber-honey/10 flex flex-col overflow-hidden" style="border-left:1px solid rgba(245,158,11,0.12)">

            <!-- Panel Header -->
            <div class="px-4 py-3 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-honey blink"></div>
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-widest">Telemetri Paneli</span>
                </div>
                <span class="text-xs text-slate-600 font-mono" x-text="currentTime"></span>
            </div>

            <!-- No selection state -->
            <div x-show="!selectedLocation" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2)">
                    <span class="text-3xl">🗺️</span>
                </div>
                <p class="text-slate-400 text-sm font-medium mb-1">Bölge Seçin</p>
                <p class="text-slate-600 text-xs">Harita üzerindeki bir bölgeye tıklayın</p>
            </div>

            <!-- Selected Location Data -->
            <div x-show="selectedLocation" class="flex-1 sidebar-scroll p-4 space-y-4 fade-in-up">

                <!-- Region Title -->
                <template x-if="selectedLocation">
                    <div>
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-1">
                            <div>
                                <h2 class="text-sm font-bold text-white leading-tight" x-text="selectedLocation.baslik"></h2>
                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                        style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:#FACC15">
                                        <span x-text="selectedLocation.flora_icon"></span>
                                        <span x-text="selectedLocation.flora"></span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs"
                                        style="background:rgba(100,116,139,0.15);border:1px solid rgba(100,116,139,0.2);color:#94A3B8">
                                        ⛰️ <span x-text="selectedLocation.rakım + 'm'"></span>
                                    </span>
                                </div>
                            </div>
                            <span class="text-2xl mt-0.5" x-text="selectedLocation.mevsim_label.split(' ')[0]"></span>
                        </div>

                        <!-- ====== BIG DECISION CARD ====== -->
                        <div class="rounded-2xl p-4 mt-3"
                            :class="{
                                'status-safe': selectedLocation.durum === 'uygun',
                                'status-warn': selectedLocation.durum === 'uyari',
                                'status-danger': selectedLocation.durum === 'riskli'
                            }">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xl">
                                    <span x-show="selectedLocation.durum === 'uygun'">✅</span>
                                    <span x-show="selectedLocation.durum === 'uyari'">⚠️</span>
                                    <span x-show="selectedLocation.durum === 'riskli'">❌</span>
                                </span>
                                <span class="text-xs font-black tracking-wider uppercase"
                                    :class="{
                                        'text-emerald-400': selectedLocation.durum === 'uygun',
                                        'text-amber-400': selectedLocation.durum === 'uyari',
                                        'text-red-400': selectedLocation.durum === 'riskli'
                                    }">
                                    <span x-show="selectedLocation.durum === 'uygun'">UÇUŞA VE KONAKLAMAYA UYGUN</span>
                                    <span x-show="selectedLocation.durum === 'uyari'">ORTA RİSK — DİKKATLİ OLUN</span>
                                    <span x-show="selectedLocation.durum === 'riskli'">RİSKLİ BÖLGE — ÖNERİLMEZ</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed" x-text="selectedLocation.durum_neden"></p>

                            <!-- Alert Badges -->
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span x-show="selectedLocation.firtina" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                    style="background:rgba(239,68,68,0.2);color:#F87171;border:1px solid rgba(239,68,68,0.3)">
                                     Şiddetli Rüzgar
                                </span>
                                <span x-show="selectedLocation.ilaclama_riski" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                    style="background:rgba(245,158,11,0.2);color:#FACC15;border:1px solid rgba(245,158,11,0.3)">
                                     İlaçlama Riski
                                </span>
                                <span x-show="!selectedLocation.firtina && !selectedLocation.ilaclama_riski && selectedLocation.durum === 'uygun'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                    style="background:rgba(16,185,129,0.15);color:#34D399;border:1px solid rgba(16,185,129,0.25)">
                                     Nektar Akımı Yüksek
                                </span>
                            </div>
                        </div>

                        <!-- ====== CANLI HAVA DURUMU KAYNAGI ====== -->
                        <div class="flex items-center justify-between mt-3 px-1">
                            <div class="flex items-center gap-1.5">
                                <template x-if="selectedLocation.canli">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                        style="background:rgba(16,185,129,0.15);color:#34D399;border:1px solid rgba(16,185,129,0.25)">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 blink inline-block"></span>
                                        Canlı • Open-Meteo
                                    </span>
                                </template>
                                <template x-if="!selectedLocation.canli">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                        style="background:rgba(100,116,139,0.15);color:#94A3B8;border:1px solid rgba(100,116,139,0.2)">
                                        📡 Çevrimdışı Veri
                                    </span>
                                </template>
                            </div>
                            <button @click="refreshWeather(selectedLocation)"
                                :disabled="weatherLoading"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold text-amber-300 transition-all hover:bg-amber-500/10 disabled:opacity-50"
                                style="border:1px solid rgba(245,158,11,0.2)">
                                <span x-show="!weatherLoading">🔄 Güncelle</span>
                                <span x-show="weatherLoading" class="flex items-center gap-1">
                                    <span class="w-3 h-3 border-2 border-amber-400 border-t-transparent rounded-full animate-spin inline-block"></span>
                                    Yükleniyor
                                </span>
                            </button>
                        </div>
                        <div x-show="selectedLocation.api_zaman" class="mt-1 px-1">
                            <span class="text-xs text-slate-600 font-mono" x-text="'Son güncelleme: ' + formatApiTime(selectedLocation.api_zaman)"></span>
                        </div>

                        <!-- ====== METEOROLOJI METRİKLERİ ====== -->
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            <!-- Sıcaklık -->
                            <div class="metric-card">
                                <div class="text-xl font-black text-orange-400" x-text="selectedLocation.sicaklik + '°C'"></div>
                                <div class="text-xs text-slate-500 mt-0.5">Sıcaklık</div>
                                <div class="text-xs mt-1 font-medium"
                                    :class="(selectedLocation.sicaklik >= 18 && selectedLocation.sicaklik <= 28) ? 'text-emerald-400' : 'text-red-400'">
                                    <span x-text="(selectedLocation.sicaklik >= 18 && selectedLocation.sicaklik <= 28) ? '✓ İdeal' : '✗ Sınırda'"></span>
                                </div>
                            </div>
                            <!-- Nem -->
                            <div class="metric-card">
                                <div class="text-xl font-black text-sky-400" x-text="'%' + selectedLocation.nem"></div>
                                <div class="text-xs text-slate-500 mt-0.5">Nem Oranı</div>
                                <div class="text-xs mt-1 font-medium text-sky-300">Nektar Kal.</div>
                            </div>
                            <!-- Rüzgar -->
                            <div class="metric-card">
                                <div class="text-xl font-black"
                                    :class="selectedLocation.ruzgar < 30 ? 'text-emerald-400' : 'text-red-400'"
                                    x-text="selectedLocation.ruzgar + ' km/s'"></div>
                                <div class="text-xs text-slate-500 mt-0.5">Rüzgar</div>
                                <div class="text-xs mt-1 font-medium"
                                    :class="selectedLocation.ruzgar < 30 ? 'text-emerald-400' : 'text-red-400'">
                                    <span x-text="selectedLocation.ruzgar < 30 ? '✓ Uygun' : '✗ Tehlikeli'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Nem Progress Bar -->
                        <div class="mt-2 px-1">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>Nektar Kalitesi (Nem)</span>
                                <span class="text-sky-400 font-mono" x-text="'%' + selectedLocation.nem"></span>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill"
                                    :class="selectedLocation.nem >= 40 && selectedLocation.nem <= 75 ? 'fill-green' : 'fill-yellow'"
                                    :style="'width:' + selectedLocation.nem + '%'"></div>
                            </div>
                        </div>

                        <!-- ====== KAPASİTE ====== -->
                        <div class="mt-3 glass-card rounded-xl p-3" style="border:1px solid rgba(255,255,255,0.07)">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-300">Kovan Kapasitesi</span>
                                <span class="text-xs font-mono font-bold"
                                    :class="{
                                        'text-emerald-400': selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max < 0.6,
                                        'text-amber-400': selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max >= 0.6 && selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max < 0.85,
                                        'text-red-400': selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max >= 0.85
                                    }"
                                    x-text="'%' + Math.round((selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max) * 100) + ' Dolu'">
                                </span>
                            </div>
                            <div class="progress-bar-track mb-2">
                                <div class="progress-bar-fill"
                                    :class="{
                                        'fill-green': selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max < 0.6,
                                        'fill-yellow': selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max >= 0.6 && selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max < 0.85,
                                        'fill-red': selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max >= 0.85
                                    }"
                                    :style="'width:' + Math.round((selectedLocation.kapasite_mevcut / selectedLocation.kapasite_max) * 100) + '%'">
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500" x-text="selectedLocation.kapasite_mevcut + ' / ' + selectedLocation.kapasite_max + ' Kovan'"></span>
                                <span class="text-emerald-400 font-semibold" x-text="'👤 ' + selectedLocation.onayli_arici + ' Onaylı Arıcı'"></span>
                            </div>
                            <!-- Ghost Data badge -->
                            <div class="mt-2 flex items-center gap-1.5 px-2 py-1.5 rounded-lg" style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.15)">
                                <span class="text-sky-400 text-xs">⏱️</span>
                                <span class="text-xs text-slate-500">Veriler 30 günlük GPS doğrulaması ile aktif</span>
                            </div>
                        </div>

                        <!-- ====== TOPLULUK İHBAR ====== -->
                        <button @click="openReport()"
                            class="w-full mt-3 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 transition-all hover:scale-105"
                            style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1)">
                             Bölge Durumunu Güncelle / İhbar Et
                        </button>

                    </div>
                </template>

            </div>
        </div>
    </div>

    <!-- ======================================================
         BOTTOM STRIP (How It Works)
    ====================================================== -->
    <div class="relative z-10 border-t border-white/5 px-4 py-3 grid grid-cols-3 gap-3" style="height:88px;background:#090A0F">
        <div class="info-card flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.2)">
                <span class="text-base">🌐</span>
            </div>
            <div>
                <div class="text-xs font-bold text-emerald-400">Canlı API & Flora Eşleşmesi</div>
                <div class="text-xs text-slate-600 mt-0.5">Open-Meteo API + Rakım/Mevsim Algoritması</div>
            </div>
        </div>
        <div class="info-card flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.2)">
                <span class="text-base">🔐</span>
            </div>
            <div>
                <div class="text-xs font-bold text-amber-400">Sahte Veri & Sabotaj Koruması</div>
                <div class="text-xs text-slate-600 mt-0.5">AKS Belge Doğrulaması + Anlık GPS & Kamera Kanıtı</div>
            </div>
        </div>
        <div class="info-card flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.2)">
                <span class="text-base">👻</span>
            </div>
            <div>
                <div class="text-xs font-bold text-sky-400">Hayalet Veri (TTL) Temizliği</div>
                <div class="text-xs text-slate-600 mt-0.5">30 Gün Sonra Otomatik Silinen Konum Pinleri</div>
            </div>
        </div>
    </div>

    <!-- ======================================================
         MODAL 1: KOVAN KONUMU EKLE (Check-in)
    ====================================================== -->
    <div x-show="showCheckinModal" x-transition.opacity class="modal-overlay" @click.self="showCheckinModal = false">
        <div class="modal-box fade-in-up">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-black text-white">📍 Kovan Konumu Ekle</h3>
                        <p class="text-xs text-slate-500 mt-0.5">AKS Onaylı Kayıt Sistemi</p>
                    </div>
                    <button @click="showCheckinModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:text-white hover:bg-white/10 transition-all">✕</button>
                </div>

                <!-- AKS Number -->
                <div class="mb-4">
                    <label class="text-xs font-semibold text-slate-400 mb-1.5 block">AKS (Arıcılık Kayıt Sistemi) Numarası</label>
                    <div class="relative">
                        <input type="text" x-model="checkin.aks_no" @input="validateAKS()"
                            placeholder="ör. TR-AKS-2024-00382" class="input-dark pr-24">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <span x-show="aksValidated" class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,0.2);color:#34D399;border:1px solid rgba(16,185,129,0.3)">✓ Doğrulandı</span>
                        </div>
                    </div>
                </div>

                <!-- GPS -->
                <div class="mb-4 grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-slate-400 mb-1.5 block">Enlem</label>
                        <input type="text" x-model="checkin.lat" placeholder="38.1580" class="input-dark">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 mb-1.5 block">Boylam</label>
                        <input type="text" x-model="checkin.lng" placeholder="27.4220" class="input-dark">
                    </div>
                </div>
                <button @click="getDeviceGPS()"
                    class="w-full mb-4 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold text-slate-300 transition-all hover:bg-white/10"
                    style="border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.04)">
                    <span x-show="!gettingGPS"> Cihaz GPS Konumunu Al</span>
                    <span x-show="gettingGPS" class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 border-2 border-amber-honey border-t-transparent rounded-full animate-spin inline-block"></span>
                        Konum alınıyor...
                    </span>
                </button>

                <!-- Kovan Sayısı Slider -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold text-slate-400">Kovan Sayısı</label>
                        <span class="text-sm font-black text-amber-300 font-mono" x-text="checkin.kovan_sayisi + ' Kovan'"></span>
                    </div>
                    <input type="range" min="10" max="500" step="10" x-model="checkin.kovan_sayisi"
                        class="w-full" style="accent-color:#F59E0B">
                    <div class="flex justify-between text-xs text-slate-600 mt-1">
                        <span>10</span><span>500</span>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div class="mb-4">
                    <label class="text-xs font-semibold text-slate-400 mb-1.5 block">📷 Anlık Kovan Fotoğrafı (Zorunlu)</label>
                    <div class="upload-box" @click="triggerFileUpload()">
                        <div x-show="!checkin.photo_name">
                            <div class="text-2xl mb-2">📷</div>
                            <p class="text-xs text-slate-400 font-semibold">Fotoğraf eklemek için tıklayın</p>
                            <p class="text-xs text-slate-600 mt-0.5">JPG, PNG • Maks. 5MB</p>
                        </div>
                        <div x-show="checkin.photo_name" class="flex items-center gap-2 justify-center">
                            <span class="text-lg">✅</span>
                            <span class="text-xs text-emerald-400 font-semibold" x-text="checkin.photo_name"></span>
                        </div>
                        <input type="file" id="photo-upload" class="hidden" accept="image/*" @change="handlePhotoUpload($event)">
                    </div>
                </div>

                <!-- Ghost Data Notice -->
                <div class="flex items-start gap-2 p-3 rounded-xl mb-6" style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.15)">
                    <span class="text-sky-400 mt-0.5">👻</span>
                    <p class="text-xs text-slate-400">Bu kayıt <strong class="text-sky-400">30 gün sonra otomatik olarak askıya alınacaktır</strong> (Hayalet Veri Koruması). Yenileme için GPS ve fotoğraf kanıtı gereklidir.</p>
                </div>

                <!-- Submit -->
                <button @click="submitCheckin()"
                    :disabled="checkinLoading"
                    class="w-full py-3 rounded-xl font-black text-sm text-obsidian transition-all hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    style="background:linear-gradient(135deg,#F59E0B,#FACC15);box-shadow:0 0 20px rgba(245,158,11,0.3)">
                    <span x-show="!checkinLoading"> Konumu Kaydet & Haritaya Ekle</span>
                    <span x-show="checkinLoading" class="flex items-center justify-center gap-2">
                        <span class="w-4 h-4 border-2 border-obsidian border-t-transparent rounded-full animate-spin inline-block"></span>
                        AKS Doğrulanıyor...
                    </span>
                </button>

                <!-- Success Message -->
                <div x-show="checkinSuccess" x-transition class="mt-3 p-3 rounded-xl text-center" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25)">
                    <p class="text-emerald-400 font-bold text-sm"> Kovan konumu haritaya eklendi!</p>
                    <p class="text-slate-500 text-xs mt-0.5">Yeni pin harita üzerinde görülebilir.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================
         MODAL 2: TİCARİ PAKETLER
    ====================================================== -->
    <div x-show="showPricingModal" x-transition.opacity class="modal-overlay" @click.self="showPricingModal = false">
        <div class="modal-box" style="max-width:680px">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="text-lg font-black text-white">ArıRota Ticari Paketler</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Gezginci arıcılar ve arıcı birlikleri için</p>
                    </div>
                    <button @click="showPricingModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:text-white hover:bg-white/10 transition-all">✕</button>
                </div>

                <!-- Pricing Grid -->
                <div class="grid grid-cols-1 gap-4 mt-5">
                    <!-- Free -->
                    <div class="pricing-card flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(100,116,139,0.15);border:1px solid rgba(100,116,139,0.2)">
                                <span class="text-xl">🐝</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-bold text-white">Başlangıç</span>
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(100,116,139,0.2);color:#94A3B8">Ücretsiz</span>
                            </div>
                            <p class="text-xs text-slate-500">Harita görüntüleme • 2 aktif pin • Topluluk verileri</p>
                        </div>
                        <button class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 border border-white/10 hover:border-white/20 transition-all">Aktif Plan</button>
                    </div>

                    <!-- Pro -->
                    <div class="pricing-card featured flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.4)">
                                <span class="text-xl">⭐</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-bold text-white">Profesyonel Arıcı</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:rgba(245,158,11,0.25);color:#FACC15;border:1px solid rgba(245,158,11,0.3)">⭐ En Popüler</span>
                            </div>
                            <p class="text-xs text-slate-400 mb-1">Zirai İlaçlama SMS Uyarısı • 14 Günlük Flora Tahmini • Sınırsız Pin</p>
                            <div class="flex flex-wrap gap-1">
                                <span class="text-xs px-1.5 py-0.5 rounded text-emerald-400" style="background:rgba(16,185,129,0.1)">✓ GPS Kanıt Sistemi</span>
                                <span class="text-xs px-1.5 py-0.5 rounded text-sky-400" style="background:rgba(59,130,246,0.1)">✓ Rüzgar Radar</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-xl font-black text-amber-300">199 ₺</div>
                            <div class="text-xs text-slate-500">/ay</div>
                            <button class="mt-2 px-3 py-1.5 rounded-xl text-xs font-bold text-obsidian" style="background:linear-gradient(135deg,#F59E0B,#FACC15)">Başla</button>
                        </div>
                    </div>

                    <!-- Enterprise -->
                    <div class="pricing-card flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.25)">
                                <span class="text-xl">🏛️</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-bold text-white">Birlik / Kurumsal B2B</span>
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(139,92,246,0.2);color:#A78BFA">Lisans</span>
                            </div>
                            <p class="text-xs text-slate-500">Beyaz etiket • API erişimi • Tüm birlik üyeleri • Yönetim paneli • SLA desteği</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-sm font-bold text-slate-300">Teklif Al</div>
                            <button class="mt-2 px-3 py-1.5 rounded-xl text-xs font-bold text-purple-300 border border-purple-400/30 hover:bg-purple-400/10 transition-all">İletişim</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================
         TOAST NOTIFICATION
    ====================================================== -->
    <div x-show="toast.show" x-transition
        class="fixed bottom-24 right-4 z-50 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2"
        :style="toast.type === 'success' ? 'background:rgba(16,185,129,0.9);color:#fff' : 'background:rgba(239,68,68,0.9);color:#fff'">
        <span x-text="toast.icon"></span>
        <span x-text="toast.message"></span>
    </div>

    <!-- ======================================================
         REPORT MODAL
    ====================================================== -->
    <div x-show="showReportModal" x-transition.opacity class="modal-overlay" @click.self="showReportModal = false">
        <div class="modal-box" style="max-width:440px">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-black text-white">🚩 Bölge Durumunu Güncelle</h3>
                    <button @click="showReportModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:text-white hover:bg-white/10 transition-all">✕</button>
                </div>
                <div class="space-y-3 mb-5">
                    <button @click="report.sorun = 'Zirai İlaçlama'" :class="report.sorun === 'Zirai İlaçlama' ? 'border-red-400 text-red-300 bg-red-900/20' : ''"
                        class="w-full p-3 rounded-xl text-xs font-semibold text-slate-400 border border-white/10 text-left hover:border-red-400/50 transition-all">
                        ☠️ Zirai İlaçlama Tespit Edildi
                    </button>
                    <button @click="report.sorun = 'Kapasite Aşımı'" :class="report.sorun === 'Kapasite Aşımı' ? 'border-amber-400 text-amber-300 bg-amber-900/20' : ''"
                        class="w-full p-3 rounded-xl text-xs font-semibold text-slate-400 border border-white/10 text-left hover:border-amber-400/50 transition-all">
                        🏠 Kapasite Aşımı / Yoğunluk Problemi
                    </button>
                    <button @click="report.sorun = 'Yanlış Flora Bilgisi'" :class="report.sorun === 'Yanlış Flora Bilgisi' ? 'border-sky-400 text-sky-300 bg-sky-900/20' : ''"
                        class="w-full p-3 rounded-xl text-xs font-semibold text-slate-400 border border-white/10 text-left hover:border-sky-400/50 transition-all">
                        🌿 Yanlış/Güncel Olmayan Flora Bilgisi
                    </button>
                    <button @click="report.sorun = 'Tehlikeli Hava'" :class="report.sorun === 'Tehlikeli Hava' ? 'border-purple-400 text-purple-300 bg-purple-900/20' : ''"
                        class="w-full p-3 rounded-xl text-xs font-semibold text-slate-400 border border-white/10 text-left hover:border-purple-400/50 transition-all">
                        🌪️ Tehlikeli Hava Koşulları
                    </button>
                </div>
                <button @click="submitReport()"
                    class="w-full py-3 rounded-xl font-bold text-sm text-white transition-all"
                    style="background:linear-gradient(135deg,#EF4444,#F87171)">
                    🚩 İhbarı Gönder
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================
         JAVASCRIPT
    ====================================================== -->
    <script>
    // Pass Laravel data to JS
    const LOCATIONS_DATA = @json($locations);

    function ariRotaApp() {
        return {
            // State
            activeFilter: 'tumu',
            layers: { yogunluk: false, ilaclama: false, radar: false },
            selectedLocation: null,
            mapLoading: true,
            weatherLoading: false,
            visibleCount: 0,
            currentTime: '',

            // Modals
            showCheckinModal: false,
            showPricingModal: false,
            showReportModal: false,

            // Check-in form
            checkin: { aks_no: '', lat: '', lng: '', kovan_sayisi: 100, photo_name: '' },
            aksValidated: false,
            gettingGPS: false,
            checkinLoading: false,
            checkinSuccess: false,

            // Report
            report: { sorun: '' },

            // Toast
            toast: { show: false, message: '', type: 'success', icon: '✅' },

            // Leaflet
            map: null,
            markers: [],
            circles: [],
            dynamicMarkers: [],
            heatLayers: [],
            pestLayers: [],
            radarLayers: [],
            allLocations: LOCATIONS_DATA,

            // ---- LIFECYCLE ----
            initApp() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
                this.$nextTick(() => this.initMap());
            },

            updateTime() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            },

            // ---- MAP INIT ----
            initMap() {
                this.map = L.map('map', {
                    center: [38.20, 27.80],
                    zoom: 8,
                    zoomControl: false,
                    attributionControl: false,
                });

                // OpenStreetMap tile layer (with CSS dark radar filter)
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(this.map);

                // Custom zoom controls
                L.control.zoom({ position: 'bottomright' }).addTo(this.map);

                // Attribution
                L.control.attribution({ position: 'bottomleft', prefix: '© ArıRota | © OpenStreetMap' }).addTo(this.map);

                // Render markers
                this.renderMarkers(this.allLocations);

                setTimeout(() => { this.mapLoading = false; }, 800);
            },

            // ---- RENDER MARKERS ----
            renderMarkers(locations) {
                // Clear existing
                this.markers.forEach(m => this.map.removeLayer(m));
                this.circles.forEach(c => this.map.removeLayer(c));
                this.heatLayers.forEach(h => this.map.removeLayer(h));
                this.pestLayers.forEach(h => this.map.removeLayer(h));
                this.radarLayers.forEach(h => this.map.removeLayer(h));
                this.markers = []; this.circles = [];
                this.heatLayers = []; this.pestLayers = []; this.radarLayers = [];

                this.visibleCount = locations.length;

                locations.forEach(loc => {
                    const colorMap = { yesil: '#10B981', sari: '#F59E0B', kirmizi: '#EF4444' };
                    const color = colorMap[loc.renk] || '#F59E0B';
                    const pct = loc.kapasite_mevcut / loc.kapasite_max;

                    // Capacity circle
                    const circleColor = pct < 0.6 ? '#10B981' : pct < 0.85 ? '#F59E0B' : '#EF4444';
                    const circle = L.circle([loc.lat, loc.lng], {
                        radius: 18000,
                        color: circleColor,
                        fillColor: circleColor,
                        fillOpacity: 0.08,
                        weight: 1.5,
                        opacity: 0.5,
                        dashArray: pct >= 0.85 ? '6,4' : null,
                    }).addTo(this.map);
                    this.circles.push(circle);

                    // Heat layer (togglable)
                    if (this.layers.yogunluk) {
                        const heatCircle = L.circle([loc.lat, loc.lng], {
                            radius: 25000,
                            color: color,
                            fillColor: color,
                            fillOpacity: 0.15 * pct,
                            weight: 0,
                            className: 'heat-layer',
                        }).addTo(this.map);
                        this.heatLayers.push(heatCircle);
                    }

                    // Pesticide layer (togglable)
                    if (this.layers.ilaclama && loc.ilaclama_riski) {
                        const pestMarker = L.circle([loc.lat, loc.lng], {
                            radius: 8000,
                            color: '#EF4444',
                            fillColor: '#EF4444',
                            fillOpacity: 0.2,
                            weight: 2,
                            dashArray: '4,4',
                        }).addTo(this.map);
                        this.pestLayers.push(pestMarker);
                    }

                    // Radar layer (togglable)
                    if (this.layers.radar) {
                        const radarCircle = L.circle([loc.lat, loc.lng], {
                            radius: 35000,
                            color: '#3B82F6',
                            fillColor: '#3B82F6',
                            fillOpacity: 0.06,
                            weight: 1,
                            dashArray: '3,6',
                        }).addTo(this.map);
                        this.radarLayers.push(radarCircle);
                    }

                    // Custom marker icon
                    const markerClass = loc.renk === 'yesil' ? 'marker-green' : loc.renk === 'kirmizi' ? 'marker-red' : 'marker-yellow';
                    const icon = L.divIcon({
                        className: '',
                        html: `<div class="marker-pulse ${markerClass}">🐝</div>`,
                        iconSize: [28, 28],
                        iconAnchor: [14, 14],
                        popupAnchor: [0, -20],
                    });

                    const marker = L.marker([loc.lat, loc.lng], { icon }).addTo(this.map);

                    // Popup
                    const statusEmoji = loc.durum === 'uygun' ? '✅' : loc.durum === 'uyari' ? '⚠️' : '❌';
                    const statusText = loc.durum === 'uygun' ? 'Uygun' : loc.durum === 'uyari' ? 'Orta Risk' : 'Riskli';
                    const statusColor = loc.durum === 'uygun' ? '#10B981' : loc.durum === 'uyari' ? '#F59E0B' : '#EF4444';
                    marker.bindPopup(`
                        <div style="min-width:190px">
                            <div style="font-weight:800;font-size:13px;color:#fff;margin-bottom:6px">${loc.baslik}</div>
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                                <span style="font-size:11px;padding:2px 8px;border-radius:999px;background:rgba(245,158,11,0.2);color:#FACC15;border:1px solid rgba(245,158,11,0.3)">${loc.flora_icon} ${loc.flora}</span>
                            </div>
                            <div style="font-size:11px;color:#94A3B8;margin-bottom:6px">⛰️ ${loc.rakım}m rakım</div>
                            <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:${statusColor}">${statusEmoji} ${statusText}</div>
                            <div style="font-size:10px;color:#475569;margin-top:4px">Kovan: ${loc.kapasite_mevcut}/${loc.kapasite_max} (%${Math.round(loc.kapasite_mevcut/loc.kapasite_max*100)})</div>
                        </div>
                    `, { maxWidth: 250 });

                    // Click: fly to location & update sidebar
                    marker.on('click', () => {
                        this.map.flyTo([loc.lat, loc.lng], 11, { animate: true, duration: 1.0 });
                        this.selectedLocation = null;
                        this.$nextTick(() => { this.selectedLocation = loc; });
                    });

                    this.markers.push(marker);
                });
            },

            // ---- FILTERS ----
            setFilter(filter) {
                this.activeFilter = filter;
                const filtered = filter === 'tumu'
                    ? this.allLocations
                    : this.allLocations.filter(l => l.mevsim === filter);
                this.renderMarkers(filtered);
                if (this.selectedLocation && filter !== 'tumu') {
                    const stillVisible = filtered.find(l => l.id === this.selectedLocation.id);
                    if (!stillVisible) this.selectedLocation = null;
                }
            },

            // ---- LAYERS ----
            toggleLayer(layer) {
                this.layers[layer] = !this.layers[layer];
                // Re-render to apply/remove toggled layers
                const filtered = this.activeFilter === 'tumu'
                    ? this.allLocations
                    : this.allLocations.filter(l => l.mevsim === this.activeFilter);
                this.renderMarkers(filtered);
            },

            // ---- CHECK-IN ----
            validateAKS() {
                this.aksValidated = this.checkin.aks_no.length >= 10;
            },

            getDeviceGPS() {
                this.gettingGPS = true;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.checkin.lat = pos.coords.latitude.toFixed(6);
                            this.checkin.lng = pos.coords.longitude.toFixed(6);
                            this.gettingGPS = false;
                            this.showToast('✅', 'GPS konumu alındı!', 'success');
                        },
                        () => {
                            // Fallback: simulate Aegean coords
                            this.checkin.lat = (37.8 + Math.random() * 0.8).toFixed(6);
                            this.checkin.lng = (27.5 + Math.random() * 1.5).toFixed(6);
                            this.gettingGPS = false;
                            this.showToast('⚠️', 'Demo: Yakın bölge koordinatları kullanıldı', 'success');
                        },
                        { timeout: 5000 }
                    );
                } else {
                    this.checkin.lat = '38.1580';
                    this.checkin.lng = '27.4220';
                    this.gettingGPS = false;
                }
            },

            triggerFileUpload() {
                document.getElementById('photo-upload').click();
            },

            handlePhotoUpload(event) {
                const file = event.target.files[0];
                if (file) this.checkin.photo_name = file.name;
            },

            async submitCheckin() {
                if (!this.checkin.aks_no || !this.checkin.lat || !this.checkin.lng) {
                    this.showToast('❌', 'Lütfen tüm alanları doldurun', 'error');
                    return;
                }
                this.checkinLoading = true;
                // Simulate API call with delay
                await new Promise(r => setTimeout(r, 1800));

                // Add new marker to map
                const newLat = parseFloat(this.checkin.lat);
                const newLng = parseFloat(this.checkin.lng);
                if (!isNaN(newLat) && !isNaN(newLng)) {
                    const newIcon = L.divIcon({
                        className: '',
                        html: `<div class="marker-pulse marker-green" style="border-color:#FACC15;background:rgba(250,204,21,0.25);">🍯</div>`,
                        iconSize: [28, 28],
                        iconAnchor: [14, 14],
                    });
                    const newMarker = L.marker([newLat, newLng], { icon: newIcon }).addTo(this.map);
                    newMarker.bindPopup(`
                        <div>
                            <div style="font-weight:800;font-size:13px;color:#FACC15">Yeni Kovan Noktası</div>
                            <div style="font-size:11px;color:#94A3B8;margin-top:4px">AKS: ${this.checkin.aks_no}</div>
                            <div style="font-size:11px;color:#10B981">Kovan Sayısı: ${this.checkin.kovan_sayisi}</div>
                            <div style="font-size:10px;color:#EF4444;margin-top:4px">⏳ 30 gün TTL aktif</div>
                        </div>
                    `).openPopup();
                    this.dynamicMarkers.push(newMarker);
                    this.map.flyTo([newLat, newLng], 12);
                }

                this.checkinLoading = false;
                this.checkinSuccess = true;
                this.showToast('🐝', 'Kovan konumu haritaya eklendi!', 'success');

                setTimeout(() => {
                    this.showCheckinModal = false;
                    this.checkinSuccess = false;
                    this.checkin = { aks_no: '', lat: '', lng: '', kovan_sayisi: 100, photo_name: '' };
                    this.aksValidated = false;
                }, 2500);
            },

            // ---- REPORT ----
            openReport() {
                if (!this.selectedLocation) return;
                this.report.sorun = '';
                this.showReportModal = true;
            },

            async submitReport() {
                if (!this.report.sorun) {
                    this.showToast('❌', 'Lütfen bir sorun seçin', 'error');
                    return;
                }
                await new Promise(r => setTimeout(r, 800));
                this.showReportModal = false;
                this.showToast('🚩', 'İhbarınız alındı! Ekip 24 saat içinde inceleyecek.', 'success');
            },

            // ---- LIVE WEATHER (Open-Meteo) ----
            async refreshWeather(loc) {
                if (!loc || this.weatherLoading) return;
                this.weatherLoading = true;

                try {
                    // İlk olarak Open-Meteo'yu doğrudan client-side çağır (CORS destekleniyor)
                    const url = `https://api.open-meteo.com/v1/forecast?latitude=${loc.lat}&longitude=${loc.lng}&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code&timezone=Europe/Istanbul`;
                    const res = await fetch(url);
                    const data = await res.json();

                    if (data && data.current) {
                        const c = data.current;
                        loc.sicaklik = Math.round(c.temperature_2m);
                        loc.nem = Math.round(c.relative_humidity_2m);
                        loc.ruzgar = Math.round(c.wind_speed_10m);
                        loc.weather_code = c.weather_code || 0;
                        loc.api_zaman = c.time;
                        loc.canli = true;

                        // Durumu yeniden hesapla
                        this.recomputeStatus(loc);

                        // Alpine reaktivitesini tetikle
                        this.selectedLocation = null;
                        await this.$nextTick();
                        this.selectedLocation = loc;

                        // allLocations içinde de güncelle
                        const idx = this.allLocations.findIndex(l => l.id === loc.id);
                        if (idx !== -1) this.allLocations[idx] = { ...loc };

                        this.showToast('🌐', 'Canlı hava verisi güncellendi (Open-Meteo)', 'success');
                    }
                } catch (err) {
                    console.warn('Open-Meteo fetch hatası:', err);
                    this.showToast('⚠️', 'Hava durumu güncellenemedi', 'error');
                } finally {
                    this.weatherLoading = false;
                }
            },

            recomputeStatus(loc) {
                const pct = loc.kapasite_mevcut / loc.kapasite_max;
                const firtina = loc.ruzgar > 30 || (loc.weather_code && loc.weather_code >= 95);
                loc.firtina = firtina;

                if (firtina || pct >= 0.85 || loc.ilaclama_riski) {
                    loc.durum = 'riskli';
                    loc.renk = 'kirmizi';
                    const nedenler = [];
                    if (loc.ruzgar > 30) nedenler.push(`Şiddetli rüzgar (${loc.ruzgar} km/s)`);
                    if (loc.weather_code >= 95) nedenler.push('Fırtına uyarısı aktif');
                    if (pct >= 0.85) nedenler.push(`Aşırı kovan yığılması (%${Math.round(pct * 100)})`);
                    if (loc.ilaclama_riski) nedenler.push('Zirai ilaçlama riski');
                    loc.durum_neden = nedenler.join(', ') + '.';
                } else if (pct >= 0.60 || loc.sicaklik > 35 || loc.sicaklik < 10 || loc.nem > 85) {
                    loc.durum = 'uyari';
                    loc.renk = 'sari';
                    const nedenler = [];
                    if (pct >= 0.60) nedenler.push(`Kapasite %${Math.round(pct * 100)} dolu`);
                    if (loc.sicaklik > 35) nedenler.push(`Yüksek sıcaklık (${loc.sicaklik}°C)`);
                    if (loc.sicaklik < 10) nedenler.push(`Düşük sıcaklık (${loc.sicaklik}°C)`);
                    if (loc.nem > 85) nedenler.push(`Yüksek nem (%${loc.nem})`);
                    loc.durum_neden = nedenler.join(', ') + '. Dikkatli olunmalı.';
                } else {
                    loc.durum = 'uygun';
                    loc.renk = 'yesil';
                    loc.durum_neden = 'Nektar akımı uygun, hava koşulları ideal.';
                }
            },

            formatApiTime(timeStr) {
                if (!timeStr) return '';
                try {
                    const d = new Date(timeStr);
                    return d.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' }) + ' — ' +
                           d.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' });
                } catch (e) {
                    return timeStr;
                }
            },

            // ---- TOAST ----
            showToast(icon, message, type) {
                this.toast = { show: true, message, type, icon };
                setTimeout(() => { this.toast.show = false; }, 3500);
            },
        };
    }
    </script>

</body>
</html>
