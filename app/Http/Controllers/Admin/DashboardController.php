<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\NavigationItem;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'stats' => [
                'jobs' => JobPosting::count(),
                'published' => JobPosting::where('is_published', true)->count(),
                'categories' => JobCategory::count(),
                'navigation' => NavigationItem::where('is_active', true)->count(),
            ],
            'recentJobs' => JobPosting::with('category')->latest()->limit(6)->get(),
            'admins' => Admin::count(),
        ]);
    }
}
