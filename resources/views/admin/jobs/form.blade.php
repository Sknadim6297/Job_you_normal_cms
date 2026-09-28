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
                    <input class="form-control" type="url" name="image_url" id="job-image-url" value="{{ old('image_url', $job->image_url) }}" placeholder="https://example.com/image.jpg">

                    <div class="mt-3">
                        <div class="small text-muted mb-2">Preview</div>
                        <div class="d-flex gap-3 align-items-center flex-wrap">
                            <div class="position-relative border rounded overflow-hidden bg-white" style="width: 180px; height: 110px;">
                                <img id="job-image-preview" src="{{ old('image_url', $job->image_url) ? \App\Support\ImageResolver::resolve(old('image_url', $job->image_url), asset('assets/img/placeholder-job.svg')) : asset('assets/img/placeholder-job.svg') }}" alt="Job preview" style="width: 100%; height: 100%; object-fit: cover; display: block;">
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
                <label class="form-label">Full content</label>
                <textarea class="form-control" name="content" rows="8">{{ old('content', $job->content) }}</textarea>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadInput = document.getElementById('job-image-upload');
        const urlInput = document.getElementById('job-image-url');
        const preview = document.getElementById('job-image-preview');
        const fileName = document.getElementById('job-image-file-name');

        if (!uploadInput || !urlInput || !preview || !fileName) {
            return;
        }

        uploadInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            fileName.textContent = file.name;
            urlInput.value = '';
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        urlInput.addEventListener('input', function () {
            if (urlInput.value.trim() !== '') {
                fileName.textContent = 'Using image URL';
                uploadInput.value = '';
                preview.src = urlInput.value.trim();
            }
        });
    });
</script>
@endsection
