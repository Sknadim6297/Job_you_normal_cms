<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['seo_title'] ?? $settings['site_title'] ?? 'Job Portal' }}</title>
    <meta name="description" content="{{ $settings['seo_description'] ?? $settings['site_tagline'] ?? '' }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/pagination.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg main-navbar sticky-top">
        <div class="container">
            @php
                $logoPath = $settings['logo_path'] ?? 'assets/img/logo.png';
            @endphp
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ \App\Support\ImageResolver::resolve($logoPath, asset('assets/img/logo.png')) }}" data-fallback-image="{{ asset('assets/img/logo.png') }}" alt="{{ $settings['site_title'] ?? 'JobYou' }}">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    @foreach($navigation as $item)
                        <li class="nav-item"><a class="nav-link {{ $item->route_name === 'home' ? 'active' : '' }} fw-bold" href="{{ $item->destination() }}">{{ $item->label }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>

    <section class="govt-page-banner">
        <div class="govt-banner-overlay"></div>
        <div class="container">
            <div class="govt-banner-content">
                <div class="govt-breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>{{ $settings['banner_title'] ?? 'Government Jobs' }}</span></div>
                <h1>{{ $settings['banner_title'] ?? 'Government Jobs' }}</h1>
                <p>{{ $settings['banner_description'] ?? 'Latest Government Job Notifications, Recruitment & Career Opportunities' }}</p>
            </div>
        </div>
    </section>

    <section class="container job-section py-5">
        <div class="row g-4">
            @forelse($jobs as $job)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="job-card">
                        <div class="job-image position-relative">
                            <img src="{{ \App\Support\ImageResolver::resolve($job->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" class="img-fluid w-100" alt="{{ $job->title }}">
                            @if($job->qualification)<span class="position-absolute top-0 start-0 m-3 badge bg-primary px-3 py-2">{{ $job->qualification }}</span>@endif
                        </div>
                        <div class="job-body">
                            <h5 class="job-title">{{ $job->title }}</h5>
                            <a href="{{ route('job-details') }}?job={{ $job->slug }}" class="btn btn-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="text-center py-5"><h3>No jobs available</h3><p class="text-muted">Please check back soon for new opportunities.</p></div></div>
            @endforelse
        </div>
    </section>

    <div class="container pb-5"><x-pagination :paginator="$jobs" /></div>

    <footer class="main-footer w-100">
        <div class="container-fluid"><div class="footer-bottom"><div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">{{ $settings['footer_copyright'] ?? '© 2026 Jobyou. All Rights Reserved.' }}</div>
            <div class="col-md-6 text-center text-md-end"><a href="#" class="text-decoration-none text-white">Contact Us</a><span class="footer-separator">|</span><a href="#" class="text-decoration-none text-white">Disclaimer</a><span class="footer-separator">|</span><a href="#" class="text-decoration-none text-white">Privacy Policy</a></div>
        </div></div></div>
    </footer>
    <script src="{{ asset('assets/image-fallback.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
