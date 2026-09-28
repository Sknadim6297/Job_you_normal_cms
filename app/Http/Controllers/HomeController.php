<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Schema::hasTable('site_settings') ? SiteSetting::values() : [];

        $navigation = Schema::hasTable('navigation_items')
            ? NavigationItem::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
            : collect();

        $categories = Schema::hasTable('job_categories')
            ? JobCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
            : collect();

        $jobs = Schema::hasTable('job_postings')
            ? JobPosting::query()
                ->with('category')
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->paginate(12)
            : new LengthAwarePaginator([], 0, 12);

        return view('frontend.index', [
            'settings' => $settings,
            'navigation' => $navigation,
            'categories' => $categories,
            'jobs' => $jobs,
        ]);
    }

    public function category(Request $request)
    {
        $pages = [
            '8thpass' => ['title' => '8th Pass', 'qualification' => '8th'],
            '10thpass' => ['title' => '10th Pass', 'qualification' => '10th'],
            '12thpass' => ['title' => '12th Pass', 'qualification' => '12th'],
            'govt-jobs' => ['title' => 'Government Jobs', 'qualification' => null],
        ];
        $page = $pages[$request->route()->getName()] ?? abort(404);

        $matchingJobs = JobPosting::query()->where('is_published', true);
        if ($page['qualification']) {
            $qualification = $page['qualification'];
            $matchingJobs->where(function ($query) use ($qualification) {
                $query->where('qualification', 'like', '%'.$qualification.'%')
                    ->orWhere('title', 'like', '%'.$qualification.'%');
            });
        }

        $featuredJobs = (clone $matchingJobs)
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $jobs = (clone $matchingJobs)
            ->when($featuredJobs->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $featuredJobs->modelKeys()))
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $latestJobs = JobPosting::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        return view('frontend.pages.8thpass', [
            'title' => $page['title'],
            'featuredJobs' => $featuredJobs,
            'jobs' => $jobs,
            'latestJobs' => $latestJobs,
        ]);
    }
}
