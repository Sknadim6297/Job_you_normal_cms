<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_formatted_job_content_is_sanitized_before_storage(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'rich-text-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);
        $job = JobPosting::create([
            'title' => 'Existing Job',
            'slug' => 'existing-job',
            'author' => 'ADMIN963',
            'is_published' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->put('/admin/jobs/'.$job->id, [
            'title' => 'Existing Job',
            'author' => 'ADMIN963',
            'sort_order' => 0,
            'content' => '<h2 onclick="alert(1)">Formatted role</h2><script>alert(2)</script><p><a href="javascript:alert(3)">Unsafe link</a></p>',
        ]);

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'content' => '<h2>Formatted role</h2><p><a>Unsafe link</a></p>',
        ]);

        $this->put('/admin/jobs/'.$job->id, [
            'title' => 'Existing Job',
            'author' => 'ADMIN963',
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'content' => '<h2>Formatted role</h2><p><a>Unsafe link</a></p>',
        ]);
    }

    public function test_job_form_loads_existing_content_into_editor_and_login_has_logo_and_placeholders(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('jobs/editor-preview.png', 'image-content');
        Storage::disk('public')->put('brand/jobyou-logo.png', 'logo-content');

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'editor-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);
        $job = JobPosting::create([
            'title' => 'Editor Job',
            'slug' => 'editor-job',
            'image_url' => 'jobs/editor-preview.png',
            'content' => '<p>Existing <strong>formatted</strong> content</p>',
            'author' => 'ADMIN963',
            'is_published' => true,
        ]);
        SiteSetting::updateOrCreate(['key' => 'logo_path'], ['value' => 'brand/jobyou-logo.png']);

        $this->actingAs($admin, 'admin')
            ->get('https://jobs.example.test/admin/jobs/'.$job->id.'/edit')
            ->assertOk()
            ->assertSee('job-content-toolbar')
            ->assertSee('quill@2.0.3')
            ->assertSee('https://jobs.example.test/storage/jobs/editor-preview.png', false)
            ->assertSee('&lt;p&gt;Existing &lt;strong&gt;formatted&lt;/strong&gt; content&lt;/p&gt;', false);

        auth('admin')->logout();

        $this->get('https://jobs.example.test/admin/login')
            ->assertOk()
            ->assertSee('https://jobs.example.test/storage/brand/jobyou-logo.png', false)
            ->assertSee('JobYou')
            ->assertSee('Admin workspace')
            ->assertSee('placeholder="Enter your email address"', false)
            ->assertSee('placeholder="Enter your password"', false);
    }

    public function test_uploaded_job_image_is_stored_and_rendered_on_public_pages(): void
    {
        Storage::fake('public');

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'image-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->post('/admin/jobs', [
            'title' => 'New Uploaded Job',
            'qualification' => '10th Pass',
            'author' => 'ADMIN963',
            'sort_order' => 1,
            'is_published' => true,
            'image_file' => UploadedFile::fake()->image('job-card.png', 1000, 700),
        ]);

        $job = JobPosting::where('title', 'New Uploaded Job')->firstOrFail();
        Storage::disk('public')->assertExists($job->image_url);
        $this->assertStringStartsWith('jobs/', $job->image_url);
        $response->assertRedirect(route('admin.jobs.index'));

        $this->get('https://jobs.example.test/admin/jobs/'.$job->id.'/edit')
            ->assertOk()
            ->assertSee('https://jobs.example.test/storage/'.$job->image_url, false);

        $this->get('https://jobs.example.test/?page=1')
            ->assertOk()
            ->assertSee('https://jobs.example.test/storage/'.$job->image_url, false);

        $this->get('https://jobs.example.test/job-details?job='.$job->slug)
            ->assertOk()
            ->assertSee('https://jobs.example.test/storage/'.$job->image_url, false);
    }
}
