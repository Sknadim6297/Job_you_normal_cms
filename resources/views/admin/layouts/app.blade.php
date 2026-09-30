<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - JobYou</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/pagination.css') }}">
    <style>
        :root { --ink: #172033; --muted: #748096; --line: #e8edf4; --brand: #2563eb; --surface: #f6f8fc; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--surface); color: var(--ink); font-family: 'Poppins', sans-serif; font-size: .9rem; }
        .admin-shell { min-height: 100vh; }
        .sidebar { position: fixed; inset: 0 auto 0 0; width: 250px; background: #111827; color: #cbd5e1; padding: 22px 14px; z-index: 10; }
        .brand { display: block; padding: 0 12px 24px; }
        .brand img { display: block; width: 150px; height: 42px; object-fit: contain; object-position: left center; }
        .nav-label { color: #71809a; font-size: .68rem; letter-spacing: .09em; text-transform: uppercase; padding: 16px 12px 8px; }
        .side-link { display: flex; align-items: center; gap: 11px; color: #aebbd0; text-decoration: none; padding: 11px 12px; border-radius: 9px; margin: 3px 0; transition: .2s; }
        .side-link:hover, .side-link.active { color: #fff; background: rgba(37, 99, 235, .25); }
        .side-link i { font-size: 1rem; width: 20px; }
        .main-area { margin-left: 250px; min-height: 100vh; }
        .topbar { height: 72px; background: #fff; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; padding: 0 34px; }
        .page-wrap { padding: 32px 34px 48px; max-width: 1500px; }
        .eyebrow { color: var(--brand); font-size: .72rem; text-transform: uppercase; letter-spacing: .1em; font-weight: 700; }
        h1, h2, h3, h4, h5 { letter-spacing: -.02em; }
        .panel { background: #fff; border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 5px 20px rgba(23, 32, 51, .035); }
        .panel-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 20px 22px; border-bottom: 1px solid var(--line); }
        .panel-body { padding: 22px; }
        .stat-card { padding: 20px; height: 100%; }
        .stat-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 11px; background: #eff6ff; color: var(--brand); font-size: 1.15rem; }
        .stat-value { font-size: 1.75rem; font-weight: 700; margin-top: 16px; }
        .stat-label { color: var(--muted); font-size: .78rem; }
        .table { margin: 0; vertical-align: middle; }
        .table th { color: var(--muted); font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; }
        .table td, .table th { padding: 15px 22px; border-color: var(--line); }
        .badge-soft { color: #166534; background: #dcfce7; }
        .form-label { display: block; margin-bottom: .45rem; font-weight: 600; color: var(--ink); }
        .form-control, .form-select { display: block; width: 100%; min-height: 44px; background: #fff; color: var(--ink); border: 1px solid #dbe2ec; border-radius: 8px; padding: .65rem .8rem; }
        textarea.form-control { min-height: 110px; resize: vertical; }
        .form-control::placeholder { color: #9aa6b8; opacity: 1; }
        .form-control:focus, .form-select:focus { border-color: #93b4ff; box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .12); }
        .btn { border-radius: 8px; font-weight: 500; }
        .mobile-menu { display: none; }
        @media (max-width: 900px) { .sidebar { transform: translateX(-100%); transition: .2s; } .sidebar.open { transform: translateX(0); } .main-area { margin-left: 0; } .topbar, .page-wrap { padding-left: 18px; padding-right: 18px; } .mobile-menu { display: inline-flex; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-shell">
    @php
        $adminSettings = \App\Models\SiteSetting::values();
        $adminLogoPath = $adminSettings['logo_path'] ?? 'assets/img/logo.png';
        $adminSiteTitle = $adminSettings['site_title'] ?? 'JobYou';
    @endphp
    <aside class="sidebar" id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand" aria-label="{{ $adminSiteTitle }}">
            <img src="{{ \App\Support\ImageResolver::resolve($adminLogoPath, asset('assets/img/logo.png')) }}" data-fallback-image="{{ asset('assets/img/logo.png') }}" alt="{{ $adminSiteTitle }}">
        </a>
        <div class="nav-label">Workspace</div>
        <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a class="side-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" href="{{ route('admin.jobs.index') }}"><i class="bi bi-briefcase"></i> Job postings</a>
        <a class="side-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
        <div class="nav-label">Website</div>
        <a class="side-link {{ request()->routeIs('admin.navigation.*') ? 'active' : '' }}" href="{{ route('admin.navigation.index') }}"><i class="bi bi-list-nested"></i> Navigation</a>
        <a class="side-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><i class="bi bi-sliders"></i> Website settings</a>
        <div class="mt-auto pt-4">
            <a class="side-link" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View website</a>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar">
            <button class="btn btn-light mobile-menu" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
            <div class="text-muted d-none d-md-block">Content management workspace</div>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-muted">{{ auth('admin')->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="btn btn-outline-secondary btn-sm" title="Sign out"><i class="bi bi-box-arrow-right"></i></button></form>
            </div>
        </header>
        <div class="page-wrap">
            @if(session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif
            @if($errors->any()) <div class="alert alert-danger border-0 shadow-sm"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/image-fallback.js') }}" defer></script>
@stack('scripts')
</body>
</html>
