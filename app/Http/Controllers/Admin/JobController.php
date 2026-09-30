<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Support\RichTextSanitizer;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $jobs = JobPosting::with('category')
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%'))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('admin.jobs.form', ['job' => new JobPosting, 'categories' => JobCategory::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        try {
            JobPosting::create($data);
        } catch (UniqueConstraintViolationException) {
            $this->discardUploadedImage($request, $data);
            throw ValidationException::withMessages(['slug' => 'This job slug is already in use.']);
        }

        return redirect()->route('admin.jobs.index')->with('success', 'Job created.');
    }

    public function edit(JobPosting $job)
    {
        return view('admin.jobs.form', ['job' => $job, 'categories' => JobCategory::orderBy('name')->get()]);
    }

    public function update(Request $request, JobPosting $job)
    {
        $data = $this->validated($request, $job);

        try {
            $job->update($data);
        } catch (UniqueConstraintViolationException) {
            $this->discardUploadedImage($request, $data);
            throw ValidationException::withMessages(['slug' => 'This job slug is already in use.']);
        }

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated.');
    }

    public function destroy(JobPosting $job)
    {
        $job->delete();
        return back()->with('success', 'Job deleted.');
    }

    private function validated(Request $request, ?JobPosting $job = null): array
    {
        $data = $request->validate([
            'job_category_id' => ['nullable', 'exists:job_categories,id'],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200'],
            'qualification' => ['nullable', 'string', 'max:80'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'dimensions:max_width=5000,max_height=5000', 'max:2048'],
            'image_url' => $request->hasFile('image_file')
                ? ['nullable', 'string', 'max:500']
                : ['nullable', 'url:http,https', 'max:500'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'author' => ['required', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
        ]);

        if (array_key_exists('content', $data)) {
            $data['content'] = RichTextSanitizer::sanitize($data['content']);
        }
        $data['slug'] = Str::slug(($data['slug'] ?? '') ?: $data['title']);

        $uniqueSlug = Rule::unique('job_postings', 'slug');
        if ($job) {
            $uniqueSlug->ignore($job->getKey());
        }

        Validator::make(
            ['slug' => $data['slug']],
            ['slug' => ['required', 'string', 'max:200', $uniqueSlug]],
        )->validate();

        if ($request->hasFile('image_file')) {
            $uploaded = $request->file('image_file')->store('jobs', 'public');
            if (! $uploaded) {
                throw ValidationException::withMessages(['image_file' => 'The job image could not be stored.']);
            }

            $data['image_url'] = $uploaded;
        } elseif (! empty($data['image_url'])) {
            $data['image_url'] = trim($data['image_url']);
        } else {
            $data['image_url'] = $job?->image_url ?? null;
        }

        unset($data['image_file']);

        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        return $data;
    }

    private function discardUploadedImage(Request $request, array $data): void
    {
        $imagePath = $request->hasFile('image_file') ? ($data['image_url'] ?? null) : null;

        if ($imagePath && ! Storage::disk('public')->delete($imagePath)) {
            report(new \RuntimeException('Failed to remove a job image after a slug conflict.'));
        }
    }
}
