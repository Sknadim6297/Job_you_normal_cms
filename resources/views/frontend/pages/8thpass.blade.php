
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Job You' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/pagination.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg main-navbar sticky-top">
        <div class="container">
            @php
                $logoPath = \App\Models\SiteSetting::values()['logo_path'] ?? 'assets/img/logo.png';
            @endphp
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ \App\Support\ImageResolver::resolve($logoPath, asset('assets/img/logo.png')) }}" data-fallback-image="{{ asset('assets/img/logo.png') }}" alt="JobYou" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link fw-bold" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold {{ request()->path() === '8thpass' ? 'active' : '' }}" href="{{ route('8thpass') }}">8th Pass</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold {{ request()->path() === '10thpass' ? 'active' : '' }}" href="{{ route('10thpass') }}">10th Pass</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold {{ request()->path() === '12thpass' ? 'active' : '' }}" href="{{ route('12thpass') }}">12th Pass</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold {{ request()->path() === 'govt-jobs' ? 'active' : '' }}" href="{{ route('govt-jobs') }}">Government Jobs</a></li>
                </ul>
            </div>
        </div>
    </nav>

    @php
        $mainJob = $featuredJobs->first();
        $secondaryJobs = $featuredJobs->slice(1, 3);
        $topJob = $secondaryJobs->first();
        $bottomJobs = $secondaryJobs->slice(1, 2);
    @endphp

    <section class="featured-jobs py-3">
        <div class="container">
            <div class="row g-3">
                <div class="col-lg-6">
                    @if($mainJob)
                        <a href="{{ route('job-details') }}?job={{ $mainJob->slug }}" class="featured-card featured-large">
                            <img src="{{ \App\Support\ImageResolver::resolve($mainJob->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $mainJob->title }}">
                            <div class="featured-overlay"></div>
                            <div class="featured-content">
                                <span class="qualification">{{ strtoupper($mainJob->qualification ?: $title) }}</span>
                                <h3>{{ $mainJob->title }}</h3>
                            </div>
                        </a>
                    @endif
                </div>

                <div class="col-lg-6">
                    <div class="row g-3">
                        @if($topJob)
                            <div class="col-12">
                                <a href="{{ route('job-details') }}?job={{ $topJob->slug }}" class="featured-card featured-top">
                                    <img src="{{ \App\Support\ImageResolver::resolve($topJob->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $topJob->title }}">
                                    <div class="featured-overlay"></div>
                                    <div class="featured-content">
                                        <span class="qualification">{{ strtoupper($topJob->qualification ?: $title) }}</span>
                                        <h3>{{ $topJob->title }}</h3>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @foreach($bottomJobs as $job)
                            <div class="col-md-6">
                                <a href="{{ route('job-details') }}?job={{ $job->slug }}" class="featured-card featured-small">
                                    <img src="{{ \App\Support\ImageResolver::resolve($job->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $job->title }}">
                                    <div class="featured-overlay"></div>
                                    <div class="featured-content">
                                        <span class="qualification">{{ strtoupper($job->qualification ?: $title) }}</span>
                                        <h3>{{ $job->title }}</h3>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="category-section py-4">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <h1 class="category-title">{{ $title }}</h1>

                    @forelse($jobs as $job)
                        <div class="job-list-item">
                            <div class="row g-4 align-items-start">
                                <div class="col-md-6">
                                    <div class="job-list-image position-relative">
                                        <img src="{{ \App\Support\ImageResolver::resolve($job->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $job->title }}" class="img-fluid" loading="lazy" decoding="async">
                                        <span class="qualification-badge">{{ strtoupper($job->qualification ?: $title) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h2 class="job-list-title">{{ $job->title }}</h2>
                                    <div class="post-meta">
                                        <span>BY <strong>{{ strtoupper($job->author ?: 'ADMIN963') }}</strong></span>
                                        <span><i class="bi bi-clock"></i> {{ $job->published_at ? $job->published_at->format('F j, Y') : 'Recently' }}</span>
                                        <span><i class="bi bi-chat"></i> 0</span>
                                    </div>
                                    <p class="job-excerpt">{{ $job->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($job->content ?? ''), 180) }}</p>
                                    <a href="{{ route('job-details') }}?job={{ $job->slug }}" class="read-more-btn">READ MORE</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light">No jobs available for {{ $title }} right now.</div>
                    @endforelse

                    <x-pagination :paginator="$jobs" />
                </div>

                <div class="col-lg-4">
                    <div class="latest-sidebar">
                        <h3 class="sidebar-title">Latest Updates</h3>
                        @foreach($latestJobs as $job)
                            <a href="{{ route('job-details') }}?job={{ $job->slug }}" class="latest-post">
                                <img src="{{ \App\Support\ImageResolver::resolve($job->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $job->title }}" loading="lazy" decoding="async">
                                <div>
                                    <h5>{{ $job->title }}</h5>
                                    <small><i class="bi bi-clock"></i> {{ $job->published_at ? $job->published_at->format('F j, Y') : 'Recently' }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="main-footer w-100">
        <div class="container-fluid">
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">© 2026 Jobyou. All Rights Reserved.</div>
                    <div class="col-md-6 text-center text-md-end ">
                        <a href="#" class="text-decoration-none text-white">Contact Us</a>
                        <span class="footer-separator">|</span>
                        <a href="#" class="text-decoration-none text-white">Disclaimer</a>
                        <span class="footer-separator">|</span>
                        <a href="#" class="text-decoration-none text-white">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/image-fallback.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
