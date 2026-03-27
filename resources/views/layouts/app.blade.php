<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'MPX PDPA Studio'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    forest: {
                        50:  '#f0f7f2',
                        100: '#dcede2',
                        200: '#bbdbc8',
                        300: '#8ec3a6',
                        400: '#5da47e',
                        500: '#3a8762',
                        600: '#2a6b4d',
                        700: '#1f543c',
                        800: '#174330',
                        900: '#0f3020',   // sidebar bg
                        950: '#091d14',
                    },
                    crimson: {
                        50:  '#fff1f1',
                        100: '#ffe1e1',
                        200: '#ffc7c7',
                        300: '#ffa0a0',
                        400: '#ff6b6b',
                        500: '#f83b3b',
                        600: '#e51d1d',
                        700: '#c0272d',   // Incognito red
                        800: '#9e1d22',
                        900: '#821e21',
                    },
                },
                fontFamily: {
                    sans: ['Sarabun', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
            }
        }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }

        /* Sidebar nav links */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 10px;
            font-size: 13.5px; font-weight: 500;
            color: #2d6a4f;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .nav-item:hover { background: rgba(21,87,46,0.09); color: #15572e; }
        .nav-item.active {
            background: linear-gradient(135deg, #15572e, #2a6b4d);
            color: #fff;
            box-shadow: 0 2px 10px rgba(21,87,46,0.25);
        }
        .nav-item.active svg { opacity: 1; }
        .nav-item svg { opacity: 0.65; }
        .nav-item.active svg, .nav-item:hover svg { opacity: 1; }

        /* Cards */
        .card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e8f0eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
        }
        .card-hover:hover {
            border-color: #5da47e;
            box-shadow: 0 4px 20px rgba(21,87,46,0.12);
            transform: translateY(-1px);
        }
        .card-hover { transition: all 0.2s ease; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
        .badge-green  { background: #dcf5e7; color: #166534; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-orange { background: #ffedd5; color: #9a3412; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 99px; }

        /* Table */
        .data-table th { background: #f8faf9; font-size: 11px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #64748b; padding: 11px 16px; }
        .data-table td { padding: 13px 16px; font-size: 13.5px; }
        .data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.12s; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #f8fdf9; }

        /* Stat card gradient */
        .stat-primary { background: linear-gradient(135deg, #15572e 0%, #1f7049 100%); color: white; }
        .stat-crimson { background: linear-gradient(135deg, #c0272d 0%, #e53e3e 100%); color: white; }
        .stat-amber   { background: linear-gradient(135deg, #b45309 0%, #d97706 100%); color: white; }
        .stat-slate   { background: linear-gradient(135deg, #334155 0%, #475569 100%); color: white; }

        /* Button styles */
        .btn-primary { background: linear-gradient(135deg, #15572e, #2a6b4d); color: white; border: none; padding: 9px 18px; border-radius: 9px; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary:hover { background: linear-gradient(135deg, #1f7049, #3a8762); box-shadow: 0 4px 12px rgba(21,87,46,0.3); transform: translateY(-1px); }
        .btn-danger  { background: linear-gradient(135deg, #c0272d, #e53e3e); color: white; border: none; padding: 9px 18px; border-radius: 9px; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-danger:hover { box-shadow: 0 4px 12px rgba(192,39,45,0.3); transform: translateY(-1px); }
        .btn-outline { background: white; color: #334155; border: 1.5px solid #e2e8f0; padding: 8px 16px; border-radius: 9px; font-size: 13.5px; font-weight: 500; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-outline:hover { background: #f8fdf9; border-color: #5da47e; color: #15572e; }

        /* Input */
        .form-input { width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: 13.5px; font-family: 'Sarabun', sans-serif; transition: all 0.15s; outline: none; color: #1e293b; }
        .form-input:focus { border-color: #3a8762; box-shadow: 0 0 0 3px rgba(58,135,98,0.12); }
        .form-input::placeholder { color: #94a3b8; }

        /* Alert banners */
        .alert-critical { background: linear-gradient(135deg, #fff1f1, #ffe4e4); border: 1.5px solid #fca5a5; border-radius: 12px; padding: 14px 18px; }
        .alert-warning  { background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1.5px solid #fcd34d; border-radius: 12px; padding: 14px 18px; }

        /* Page fade-in */
        main { animation: fadeUp 0.2s ease forwards; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-50 min-h-screen" style="background: #f0f5f2;">

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="fixed inset-0 z-20 hidden lg:hidden" style="background: rgba(0,0,0,0.5);" onclick="toggleSidebar()"></div>

<div class="flex h-screen overflow-hidden">

    <!-- ═══════════════════════════════ SIDEBAR ═══════════════════════════════ -->
    <aside id="sidebar"
        class="flex flex-col fixed lg:static inset-y-0 left-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex-shrink-0"
        style="width:240px; background:rgba(220,245,230,0.72); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); border-right:1px solid rgba(255,255,255,0.65); box-shadow:2px 0 24px rgba(21,87,46,0.08);">

        <!-- Logo -->
        <div class="px-4 py-3 flex-shrink-0" style="border-bottom:1px solid rgba(21,87,46,0.12);">
            {{-- App name + version --}}
            <div class="mb-2">
                <div class="font-bold leading-tight" style="font-size:13px;color:#111111;">
                    {{ config('app.name') }}
                </div>
                <div class="font-mono" style="color:#111111;font-size:9px;margin-top:1px;">
                    v{{ config('app.version') }} B{{ config('app.build') }}
                </div>
            </div>

            {{-- Powered by --}}
            <div class="flex items-center gap-2">
                <span style="color:#111111;font-size:9px;white-space:nowrap;">Powered by</span>
                <img src="{{ asset('images/partner-incog-mpx2.png') }}" alt="Incognito Lab x MPX"
                     class="object-contain" style="height:28px;max-width:130px;">
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <div class="pt-4 pb-1.5 px-2">
                <p class="text-xs font-semibold uppercase tracking-widest" style="color:rgba(21,87,46,0.5);font-size:10px;">PDPA Management</p>
            </div>

            <a href="{{ route('consent.index') }}" class="nav-item {{ request()->routeIs('consent.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Consent Management
            </a>

            <a href="{{ route('rights.index') }}" class="nav-item {{ request()->routeIs('rights.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Data Subject Right
                @php $overdue = \App\Models\RightsRequest::where('organization_id', auth()->user()->organization_id)->overdue()->count(); @endphp
                @if($overdue > 0)
                <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-md" style="background: #c0272d; color: white; font-size: 10px;">{{ $overdue }}</span>
                @endif
            </a>

            <a href="{{ route('breach.index') }}" class="nav-item {{ request()->routeIs('breach.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Data Breach
                @php $openBreaches = \App\Models\BreachIncident::where('organization_id', auth()->user()->organization_id)->whereNotIn('status',['resolved','closed'])->count(); @endphp
                @if($openBreaches > 0)
                <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-md" style="background: #c0272d; color: white; font-size: 10px;">{{ $openBreaches }}</span>
                @endif
            </a>

            <a href="{{ route('ropa.index') }}" class="nav-item {{ request()->routeIs('ropa.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                ROPA
                @php $ropaReview = \App\Models\RopaRecord::where('organization_id', auth()->user()->organization_id)->whereNotNull('next_review_date')->where('next_review_date','<',now())->where('status','!=','archived')->count(); @endphp
                @if($ropaReview > 0)
                <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-md" style="background: #b45309; color: white; font-size: 10px;">{{ $ropaReview }}</span>
                @endif
            </a>

            <a href="{{ route('assessment.index') }}" class="nav-item {{ request()->routeIs('assessment.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                DPIA / Assessment
                @php $highRisk = \App\Models\Assessment::where('organization_id', auth()->user()->organization_id)->where('risk_level','very_high')->whereNotIn('status',['approved','archived'])->count(); @endphp
                @if($highRisk > 0)
                <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-md" style="background: #c0272d; color: white; font-size: 10px;">{{ $highRisk }}</span>
                @endif
            </a>

            <a href="{{ route('privacy.index') }}" class="nav-item {{ request()->routeIs('privacy.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Privacy Notice
            </a>

            <a href="{{ route('dpo.index') }}" class="nav-item {{ request()->routeIs('dpo.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                DPO Tasks
                @php $overdueCount = \App\Models\DpoTask::where('organization_id', auth()->user()->organization_id)->overdue()->count(); @endphp
                @if($overdueCount > 0)
                <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-md" style="background:#c0272d; color:white; font-size:10px;">{{ $overdueCount }}</span>
                @endif
            </a>

            <a href="{{ route('training.index') }}" class="nav-item {{ request()->routeIs('training.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Training
            </a>


            <a href="{{ route('parties.index') }}" class="nav-item {{ request()->routeIs('parties.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                External Parties
                @php
                $noDpaCount = \App\Models\ExternalParty::active()
                    ->whereIn('relationship_type',['data_processor','data_controller','joint_controller'])
                    ->whereDoesntHave('dpas', fn($q) => $q->where('status','active'))
                    ->count();
                @endphp
                @if($noDpaCount > 0)
                    <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full" style="background:#d97706;color:#fff;">{{ $noDpaCount }}</span>
                @endif
            </a>
            <a href="{{ route('data-map.index') }}" class="nav-item {{ request()->routeIs('data-map.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Data Map
            </a>

            {{-- ── Settings (Admin Only) ──────────────────────────────────── --}}
            @if(auth()->user()->isAdmin())
            <div class="pt-4 pb-1.5 px-2">
                <p class="text-xs font-semibold uppercase tracking-widest" style="color:rgba(21,87,46,0.5);font-size:10px;">⚙️ Settings</p>
            </div>

            <a href="{{ route('settings.accounts.index') }}" class="nav-item {{ request()->routeIs('settings.accounts.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Account Setup
                @php
                $totalUsers = \App\Models\User::count();
                @endphp
                <span class="ml-auto text-xs px-1.5 py-0.5 rounded-full" style="background:rgba(255,255,255,0.1);color:#a7c8b5;">{{ $totalUsers }}</span>
            </a>

            <a href="{{ route('logs.index') }}" class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                System Logs
                @php
                $unresolvedSec = \App\Models\SecurityLog::where('organization_id', auth()->user()->organization_id)
                    ->whereIn('severity',['critical','high'])->where('is_resolved',false)->count();
                @endphp
                @if($unresolvedSec > 0)
                    <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full" style="background:#c0272d;color:#fff;">{{ $unresolvedSec }}</span>
                @endif
            </a>
            @endif
        </nav>

        <!-- User footer -->
        <div class="px-4 py-3 flex-shrink-0" style="border-top:1px solid rgba(21,87,46,0.12);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-sm"
                     style="background:linear-gradient(135deg,#15572e,#2a6b4d);color:#fff;box-shadow:0 2px 6px rgba(21,87,46,0.25);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color:#15572e;">{{ auth()->user()->name }}</p>
                    <p class="text-xs truncate" style="color:rgba(21,87,46,0.55);">{{ auth()->user()->getRoleLabel() }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-7 h-7 rounded-lg flex items-center justify-center transition"
                            style="color:rgba(21,87,46,0.45);"
                            onmouseover="this.style.background='rgba(192,39,45,0.1)';this.style.color='#c0272d'"
                            onmouseout="this.style.background='transparent';this.style.color='rgba(21,87,46,0.45)'"
                            title="ออกจากระบบ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ═══════════════════════════════ MAIN ═══════════════════════════════ -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        <!-- Topbar -->
        <header class="flex-shrink-0 flex items-center justify-between px-6 py-3.5" style="background: white; border-bottom: 1px solid #e8f0eb; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center transition hover:bg-gray-100" style="color: #64748b;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-base font-bold" style="color: #0f3020;">@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @php
                    $critical = \App\Models\BreachIncident::where('organization_id', auth()->user()->organization_id)
                        ->where('severity','critical')->whereNotIn('status',['resolved','closed'])->count();
                @endphp
                @if($critical > 0)
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold animate-pulse" style="background: linear-gradient(135deg, #c0272d, #e53e3e); color: white;">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Critical Breach ×{{ $critical }}
                </div>
                @endif

                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium" style="background: #f0f7f2; color: #15572e;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ now()->locale('th')->translatedFormat('j M Y') }}
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        @if(session('success'))
        <div class="mx-5 mt-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background: #f0fdf4; border: 1.5px solid #86efac; color: #166534;">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mx-5 mt-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background: #fff1f2; border: 1.5px solid #fca5a5; color: #991b1b;">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-5">
            @yield('content')
        </main>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
}
</script>
</body>
</html>
