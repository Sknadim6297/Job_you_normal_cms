<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->title }} | JobYou</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
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
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('8thpass') }}">8th Pass</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('10thpass') }}">10th Pass</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('12thpass') }}">12th Pass</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('govt-jobs') }}">Government Jobs</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-4 job-details-section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <h1 class="article-title">{{ $job->title }}</h1>

                    <div class="post-meta">
                        <span>by <strong>{{ $job->author ?: 'ADMIN963' }}</strong></span>
                        <span>—</span>
                        <span>{{ $job->published_at ? $job->published_at->format('F j, Y') : 'Recently' }}</span>
                    </div>

                    <div class="mb-4">
                        <img src="{{ \App\Support\ImageResolver::resolve($job->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $job->title }}" class="img-fluid rounded w-100" style="max-height: 420px; height: 420px; object-fit: cover;">
                    </div>

                    <div class="post-content">
                        {!! \App\Support\RichTextSanitizer::render($job->content ?: '<p>'.e($job->excerpt ?: 'No description available.').'</p>') !!}
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="latest-sidebar">
                        <h3 class="sidebar-title">Latest Updates</h3>

                        @forelse($latestJobs as $latest)
                            <a href="{{ route('job-details') }}?job={{ $latest->slug }}" class="latest-post">
                                <img src="{{ \App\Support\ImageResolver::resolve($latest->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $latest->title }}">
                                <div>
                                    <h5>{{ $latest->title }}</h5>
                                    <small><i class="bi bi-clock"></i> {{ $latest->published_at ? $latest->published_at->format('F j, Y') : 'Recently' }}</small>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted mb-0">No latest updates available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="related-posts py-4">
        <div class="container">
            <div class="related-heading">Related Posts</div>

            <div class="row g-4">
                @forelse($relatedJobs as $related)
                    <div class="col-lg-4 col-md-6">
                        <article class="related-card">
                            <a href="{{ route('job-details') }}?job={{ $related->slug }}" class="related-image">
                                <img src="{{ \App\Support\ImageResolver::resolve($related->image_url, asset('assets/img/placeholder-job.svg')) }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" alt="{{ $related->title }}">
                                <span class="related-badge">{{ strtoupper($related->qualification ?: 'JOB') }}</span>
                            </a>
                            <div class="related-content">
                                <h3><a href="{{ route('job-details') }}?job={{ $related->slug }}">{{ $related->title }}</a></h3>
                                <div class="related-date"><i class="bi bi-clock"></i> {{ $related->published_at ? $related->published_at->format('F j, Y') : 'Recently' }}</div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12 text-muted">No related jobs available.</div>
                @endforelse
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

