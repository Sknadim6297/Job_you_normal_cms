<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => JobCategory::withCount('jobs')->orderBy('sort_order')->paginate(15)->withQueryString(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            JobCategory::create($this->validated($request));
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['slug' => 'This category slug is already in use.']);
        }

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, JobCategory $category)
    {
        try {
            $category->update($this->validated($request, $category));
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['slug' => 'This category slug is already in use.']);
        }

        return back()->with('success', 'Category updated.');
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:job_categories,id'],
            'items.*.name' => ['required', 'string', 'max:100'],
            'items.*.sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'items.*.is_active' => ['nullable', 'boolean'],
        ]);

        $slugsById = [];
        $itemsBySlug = [];
        $errors = [];

        foreach ($data['items'] as $itemKey => $itemData) {
            $slug = Str::slug($itemData['name']);
            if ($slug === '' || strlen($slug) > 120) {
                $errors["items.{$itemKey}.name"] = 'The category name must produce a valid slug.';
                continue;
            }

            if (isset($itemsBySlug[$slug])) {
                $errors['items'] = 'Each category must have a unique name and slug.';
            }

            $slugsById[$itemData['id']] = $slug;
            $itemsBySlug[$slug] = true;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $categoryIds = array_column($data['items'], 'id');
        $slugConflict = JobCategory::query()
            ->whereIn('slug', array_values($slugsById))
            ->whereNotIn('id', $categoryIds)
            ->exists();

        if ($slugConflict) {
            throw ValidationException::withMessages(['items' => 'One or more category slugs are already in use.']);
        }

        try {
            DB::transaction(function () use ($data, $categoryIds, $slugsById): void {
                $categories = JobCategory::query()
                    ->whereKey($categoryIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($categories->count() !== count($data['items'])) {
                    throw ValidationException::withMessages(['items' => 'One or more categories no longer exist.']);
                }

                foreach ($categories as $category) {
                    $category->slug = 'pending-'.Str::uuid();
                    $category->save();
                }

                foreach ($data['items'] as $itemData) {
                    $category = $categories->get($itemData['id']);
                    $category->name = $itemData['name'];
                    $category->slug = $slugsById[$itemData['id']];
                    $category->sort_order = (int) $itemData['sort_order'];
                    $category->is_active = (bool) ($itemData['is_active'] ?? false);
                    $category->save();
                }
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['items' => 'One or more category slugs are already in use.']);
        }

        return back()->with('success', 'Category changes saved.');
    }

    public function destroy(JobCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?JobCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
        ]);

        $data['slug'] = Str::slug(($data['slug'] ?? '') ?: $data['name']);
        $uniqueSlug = Rule::unique('job_categories', 'slug');
        if ($category) {
            $uniqueSlug->ignore($category->getKey());
        }

        Validator::make(
            ['slug' => $data['slug']],
            ['slug' => ['required', 'string', 'max:120', $uniqueSlug]],
        )->validate();

        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
