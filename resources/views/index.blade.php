@php
    $html = file_get_contents(base_path('jobyou/index.html'));
    $html = str_replace('style.css', asset('assets/style.css'), $html);
    $html = str_replace('img/logo.png', asset('assets/img/logo.png'), $html);
    $html = str_replace('index.html', route('home'), $html);
    $html = str_replace('8thpass.html', route('8thpass'), $html);
    $html = str_replace('10thpass.html', route('10thpass'), $html);
    $html = str_replace('12thpass.html', route('12thpass'), $html);
    $html = str_replace('govt-jobs.html', route('govt-jobs'), $html);
    $html = str_replace('job-details.html', route('job-details'), $html);
@endphp
{!! $html !!}
