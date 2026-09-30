@extends('admin.layouts.app')
@section('title', $job->exists ? 'Edit Job' : 'New Job')
@section('content')
<div class="d-flex justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="eyebrow mb-2">Content</div>
        <h1 class="h3 mb-1">{{ $job->exists ? 'Edit job posting' : 'New job posting' }}</h1>
        <p class="text-muted mb-0">Update the content displayed in the job cards.</p>
    </div>
    <a href="{{ route('admin.jobs.index') }}" class="btn btn-light">Back to postings</a>
</div>

<form method="POST" action="{{ $job->exists ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}" class="panel" enctype="multipart/form-data">
    @csrf
    @if($job->exists)
        @method('PUT')
    @endif

    <div class="panel-body">
        <div class="row g-3">
            <div class="col-lg-8">
                <label class="form-label">Job title</label>
                <input class="form-control" name="title" value="{{ old('title', $job->title) }}" required>
            </div>

            <div class="col-lg-4">
                <label class="form-label">Category</label>
                <select class="form-select" name="job_category_id">
                    <option value="">Uncategorized</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) old('job_category_id', $job->job_category_id) === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Qualification badge</label>
                <input class="form-control" name="qualification" value="{{ old('qualification', $job->qualification) }}" placeholder="10th Pass">
            </div>

            <div class="col-12">
                <label class="form-label">Image</label>
                <div class="border rounded p-3 bg-light-subtle">
                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <label class="btn btn-outline-primary btn-sm mb-0 cursor-pointer">
                            Upload Image
                            <input type="file" name="image_file" id="job-image-upload" accept="image/*" class="d-none">
                        </label>
                        <span id="job-image-file-name" class="text-muted small">No file selected</span>
                    </div>

                    <label class="form-label small" for="job-image-url">Or use an image URL</label>
                    <input class="form-control" type="url" name="image_url" id="job-image-url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">

                    <div class="mt-3">
                        <div class="small text-muted mb-2">Preview</div>
                        <div class="d-flex gap-3 align-items-center flex-wrap">
                            <div class="position-relative border rounded overflow-hidden bg-white" style="width: 180px; height: 110px;">
                                <img id="job-image-preview" src="{{ $job->image_url ? \App\Support\ImageResolver::resolve($job->image_url, asset('assets/img/placeholder-job.svg')) : asset('assets/img/placeholder-job.svg') }}" data-fallback-image="{{ asset('assets/img/placeholder-job.svg') }}" data-saved-image="{{ $job->image_url ? \App\Support\ImageResolver::resolve($job->image_url, asset('assets/img/placeholder-job.svg')) : '' }}" alt="Job preview" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            </div>
                            @if($job->image_url)
                                <div class="small text-muted">Current saved image</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Excerpt</label>
                <textarea class="form-control" name="excerpt" rows="3">{{ old('excerpt', $job->excerpt) }}</textarea>
            </div>

            <div class="col-12">
                <label class="form-label" id="job-content-label" for="job-content-fallback">Full content</label>
                <div class="rich-text-editor">
                    <div id="job-content-toolbar" class="ql-toolbar ql-snow" aria-label="Content formatting toolbar">
                        <span class="ql-formats">
                            <button class="ql-bold" type="button" aria-label="Bold"></button>
                            <button class="ql-italic" type="button" aria-label="Italic"></button>
                            <button class="ql-underline" type="button" aria-label="Underline"></button>
                        </span>
                        <span class="ql-formats">
                            <select class="ql-header" aria-label="Heading level">
                                <option selected></option>
                                <option value="2"></option>
                                <option value="3"></option>
                            </select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered" type="button" aria-label="Numbered list"></button>
                            <button class="ql-list" value="bullet" type="button" aria-label="Bullet list"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-link" type="button" aria-label="Insert link"></button>
                            <button class="ql-blockquote" type="button" aria-label="Blockquote"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-undo" type="button" aria-label="Undo">&#8630;</button>
                            <button class="ql-redo" type="button" aria-label="Redo">&#8631;</button>
                        </span>
                    </div>
                    <div id="job-content-editor" aria-labelledby="job-content-label" hidden></div>
                    <textarea class="form-control" id="job-content-fallback" name="content" rows="8">{{ \App\Support\RichTextSanitizer::render(old('content', $job->content)) }}</textarea>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Author</label>
                <input class="form-control" name="author" value="{{ old('author', $job->author ?: 'ADMIN963') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Published at</label>
                <input class="form-control" type="datetime-local" name="published_at" value="{{ old('published_at', $job->published_at?->format('Y-m-d\\TH:i')) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Display order</label>
                <input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $job->sort_order ?? 0) }}" min="0" max="9999" required>
            </div>

            <div class="col-md-6">
                <label class="form-check-label" for="is_published">Published</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $job->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_published">Show on frontend</label>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-check-label" for="is_featured">Featured</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $job->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Highlight on category pages</label>
                </div>
            </div>
        </div>
    </div>

    <div class="border-top p-3 text-end">
        <button class="btn btn-primary">{{ $job->exists ? 'Save changes' : 'Create job' }}</button>
    </div>
</form>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <style>
        .rich-text-editor [hidden] { display: none !important; }
        .rich-text-editor .ql-toolbar.ql-snow { border-color: #dbe2ec; border-radius: 8px 8px 0 0; background: #f8fafc; }
        .rich-text-editor .ql-container.ql-snow { min-height: 220px; border-color: #dbe2ec; border-radius: 0 0 8px 8px; font: inherit; }
        .rich-text-editor .ql-editor { min-height: 220px; overflow-wrap: anywhere; }
        .rich-text-editor .ql-editor.ql-blank::before { color: #9aa6b8; font-style: normal; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[enctype="multipart/form-data"]');
            const contentField = document.getElementById('job-content-fallback');
            const editorElement = document.getElementById('job-content-editor');

            if (!form || !contentField || !editorElement || !window.Quill) {
                return;
            }

            const quill = new Quill(editorElement, {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: '#job-content-toolbar',
                        handlers: {
                            undo: function () { quill.history.undo(); },
                            redo: function () { quill.history.redo(); },
                        },
                    },
                    history: { delay: 1000, maxStack: 100, userOnly: true },
                },
            });

            if (contentField.value.trim() !== '') {
                quill.clipboard.dangerouslyPasteHTML(contentField.value, 'silent');
            }

            editorElement.hidden = false;
            contentField.hidden = true;

            form.addEventListener('submit', function () {
                contentField.value = quill.getSemanticHTML();
            });
        });
    </script>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadInput = document.getElementById('job-image-upload');
        const urlInput = document.getElementById('job-image-url');
        const preview = document.getElementById('job-image-preview');
        const fileName = document.getElementById('job-image-file-name');

        if (!uploadInput || !urlInput || !preview || !fileName) {
            return;
        }

        function isHttpImageUrl(value) {
            try {
                const url = new URL(value);
                return url.protocol === 'http:' || url.protocol === 'https:';
            } catch (error) {
                return false;
            }
        }

        if (isHttpImageUrl(urlInput.value.trim())) {
            fileName.textContent = 'Using image URL';
            preview.src = urlInput.value.trim();
        }

        uploadInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                fileName.textContent = 'No file selected';
                preview.src = preview.dataset.savedImage || preview.dataset.fallbackImage;
                return;
            }

            fileName.textContent = file.name;
            urlInput.value = '';
            const reader = new FileReader();
            reader.onload = function (e) {
                if (uploadInput.files && uploadInput.files[0] === file && !urlInput.value.trim()) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        });

        urlInput.addEventListener('input', function () {
            const url = urlInput.value.trim();

            if (url !== '') {
                fileName.textContent = 'Using image URL';
                uploadInput.value = '';
                if (isHttpImageUrl(url)) {
                    preview.src = url;
                } else {
                    preview.src = preview.dataset.savedImage || preview.dataset.fallbackImage;
                }
                return;
            }

            fileName.textContent = uploadInput.files && uploadInput.files[0] ? uploadInput.files[0].name : 'No file selected';
            preview.src = preview.dataset.savedImage || preview.dataset.fallbackImage;
        });
    });
</script>
@endsection
