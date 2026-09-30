<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        JobCategory::create($this->validated($request));
        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, JobCategory $category)
    {
        $category->update($this->validated($request));
        return back()->with('success', 'Category updated.');
    }

    public function destroy(JobCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
