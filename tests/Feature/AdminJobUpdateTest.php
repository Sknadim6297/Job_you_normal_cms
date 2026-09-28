<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\JobCategory;
use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminJobUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_without_slug_succeeds(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        $category = JobCategory::create([
            'name' => 'Technology',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $job = JobPosting::create([
            'job_category_id' => $category->id,
            'title' => 'Original title',
            'qualification' => '12th Pass',
            'author' => 'Old Author',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->put("/admin/jobs/{$job->id}", [
            'job_category_id' => $category->id,
            'title' => 'Updated title',
            'qualification' => 'Graduate',
            'image_url' => 'https://example.com/image.jpg',
            'excerpt' => 'Updated excerpt',
            'content' => 'Updated content',
            'author' => 'New Author',
            'published_at' => now()->toDateString(),
            'sort_order' => 5,
            'is_published' => true,
            'is_featured' => false,
        ]);

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'slug' => Str::slug('Updated title'),
        ]);
    }

    public function test_admin_search_parameters_are_preserved_by_shared_pagination(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'pagination-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        foreach (range(1, 16) as $number) {
            JobPosting::create([
                'title' => 'Engineer Search Result '.$number,
                'slug' => 'engineer-search-result-'.$number,
                'author' => 'ADMIN963',
                'is_published' => true,
            ]);
        }

        JobPosting::create([
            'title' => 'Unrelated posting',
            'slug' => 'unrelated-posting',
            'author' => 'ADMIN963',
            'is_published' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $firstPage = $this->get('/admin/jobs?search=Engineer&status=published');
        $firstPage->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => $jobs->perPage() === 15
                && $jobs->currentPage() === 1
                && $jobs->total() === 16
                && $jobs->count() === 15)
            ->assertSee('/admin/jobs?search=Engineer&amp;status=published&amp;page=2', false)
            ->assertDontSee('Unrelated posting');

        $secondPage = $this->get('/admin/jobs?search=Engineer&status=published&page=2');
        $secondPage->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => $jobs->currentPage() === 2
                && $jobs->total() === 16
                && $jobs->count() === 1)
            ->assertSee('/admin/jobs?search=Engineer&amp;status=published&amp;page=1', false);
    }
}
