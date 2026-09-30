<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
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
        JobPosting::create($this->validated($request));
        return redirect()->route('admin.jobs.index')->with('success', 'Job created.');
    }

    public function edit(JobPosting $job)
    {
        return view('admin.jobs.form', ['job' => $job, 'categories' => JobCategory::orderBy('name')->get()]);
    }

    public function update(Request $request, JobPosting $job)
    {
        $job->update($this->validated($request, $job));
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
            'image_url' => ['nullable', 'string', 'max:500'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'author' => ['required', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
        ]);

        if ($request->hasFile('image_file')) {
            $uploaded = $request->file('image_file')->store('jobs', 'public');
            $data['image_url'] = $uploaded ?: ($job?->image_url ?? null);
        } elseif ($request->filled('image_url')) {
            $data['image_url'] = $request->input('image_url');
        } else {
            $data['image_url'] = $job?->image_url ?? null;
        }

        if (! empty($data['image_url']) && ! filter_var($data['image_url'], FILTER_VALIDATE_URL) && ! str_starts_with($data['image_url'], 'storage/') && ! str_starts_with($data['image_url'], 'jobs/') && ! str_starts_with($data['image_url'], 'brand/')) {
            $data['image_url'] = $job?->image_url ?? null;
        }

        if (array_key_exists('content', $data)) {
            $data['content'] = RichTextSanitizer::sanitize($data['content']);
        }
        $data['slug'] = Str::slug(($data['slug'] ?? '') ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        return $data;
    }
}
