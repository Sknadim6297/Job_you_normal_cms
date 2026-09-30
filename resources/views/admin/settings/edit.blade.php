@extends('admin.layouts.app')
@section('title', 'Website Settings')
@section('content')
<div class="mb-4">
    <div class="eyebrow mb-2">Website</div>
    <h1 class="h3 mb-1">Website settings</h1>
    <p class="text-muted mb-0">Control the site identity, banner copy, footer, and SEO defaults.</p>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" class="panel" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="panel-body">
        <h2 class="h5 mb-3">Brand and homepage</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Site title</label>
                <input class="form-control" name="site_title" value="{{ old('site_title', $settings['site_title'] ?? '') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Tagline</label>
                <input class="form-control" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
            </div>

            <div class="col-12">
                <label class="form-label">Logo</label>
                <div class="border rounded p-3 bg-light-subtle">
                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <label class="btn btn-outline-primary btn-sm mb-0 cursor-pointer">
                            Upload Image
                            <input type="file" name="logo_upload" id="logo-upload" accept="image/*" class="d-none">
                        </label>
                        <span class="text-muted small">Or Image URL</span>
                    </div>

                    <input class="form-control" name="logo_path" id="logo-url" value="{{ old('logo_path', $settings['logo_path'] ?? 'assets/img/logo.png') }}" placeholder="https://example.com/logo.png">

                    <div class="mt-3">
                        <div class="small text-muted mb-2">Preview</div>
                        <div class="border rounded bg-white d-inline-block p-2">
                            <img id="logo-preview" src="{{ \App\Support\ImageResolver::resolve(old('logo_path', $settings['logo_path'] ?? 'assets/img/logo.png'), asset('assets/img/logo.png')) }}" data-fallback-image="{{ asset('assets/img/logo.png') }}" alt="Logo preview" style="width: 180px; height: 52px; object-fit: contain; display: block;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Banner heading</label>
                <input class="form-control" name="banner_title" value="{{ old('banner_title', $settings['banner_title'] ?? '') }}" required>
            </div>

            <div class="col-12">
                <label class="form-label">Banner description</label>
                <textarea class="form-control" name="banner_description" rows="2">{{ old('banner_description', $settings['banner_description'] ?? '') }}</textarea>
            </div>
        </div>

        <hr class="my-4">

        <h2 class="h5 mb-3">Footer and SEO</h2>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Footer copyright</label>
                <input class="form-control" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">SEO title</label>
                <input class="form-control" name="seo_title" value="{{ old('seo_title', $settings['seo_title'] ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">SEO description</label>
                <input class="form-control" name="seo_description" value="{{ old('seo_description', $settings['seo_description'] ?? '') }}">
            </div>
        </div>
    </div>

    <div class="border-top p-3 text-end">
        <button class="btn btn-primary"><i class="bi bi-check-circle"></i> Save settings</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadInput = document.getElementById('logo-upload');
        const urlInput = document.getElementById('logo-url');
        const preview = document.getElementById('logo-preview');

        if (!uploadInput || !urlInput || !preview) {
            return;
        }

        const applyPreview = function (value) {
            const rawValue = (value || '').trim();

            if (rawValue === '') {
                preview.src = preview.dataset.fallbackImage || '';
                return;
            }

            if (/^(javascript:|data:)/i.test(rawValue)) {
                preview.src = preview.dataset.fallbackImage || '';
                return;
            }

            preview.src = rawValue;
        };

        uploadInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        urlInput.addEventListener('input', function () {
            applyPreview(urlInput.value);
        });
    });
</script>
@endsection
