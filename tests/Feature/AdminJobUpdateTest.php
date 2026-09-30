<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
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

    public function test_admin_category_and_navigation_lists_paginate_large_collections(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'list-pagination-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        foreach (range(1, 16) as $number) {
            JobCategory::create([
                'name' => 'Pagination Category '.$number,
                'sort_order' => $number,
                'is_active' => true,
            ]);

            NavigationItem::create([
                'label' => 'Pagination Link '.$number,
                'route_name' => 'home',
                'sort_order' => $number,
                'is_active' => true,
            ]);
        }

        $this->actingAs($admin, 'admin');

        $this->get('/admin/categories?page=2')
            ->assertOk()
            ->assertViewHas('categories', fn ($categories) => $categories->perPage() === 15
                && $categories->currentPage() === 2
                && $categories->total() === 16
                && $categories->count() === 1)
            ->assertSee('aria-label="Job listings pagination"', false);

        $this->get('/admin/navigation?page=2')
            ->assertOk()
            ->assertViewHas('items', fn ($items) => $items->perPage() === 15
                && $items->currentPage() === 2
                && $items->total() === 16
                && $items->count() === 1)
            ->assertSee('aria-label="Job listings pagination"', false);
    }

    public function test_navigation_items_support_bulk_order_status_updates_and_cleanup(): void
    {
        $admin = Admin::create([
            'name' => 'Navigation Admin',
            'email' => 'navigation-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $this->post('/admin/navigation', [
            'label' => 'Career Advice',
            'route_name' => 'home',
            'sort_order' => 2,
            'is_active' => true,
        ])->assertRedirect();

        $item = NavigationItem::query()->where('label', 'Career Advice')->firstOrFail();

        $this->put('/admin/navigation/'.$item->id, [
            'label' => 'Career Advice Updated',
            'route_name' => 'home',
            'sort_order' => 3,
            'is_active' => false,
        ])->assertRedirect();

        $this->assertDatabaseHas('navigation_items', [
            'id' => $item->id,
            'label' => 'Career Advice Updated',
            'sort_order' => 3,
            'is_active' => false,
        ]);

        $this->post('/admin/navigation/bulk-update', [
            'items' => [
                $item->id => [
                    'id' => $item->id,
                    'label' => 'Career Advice Updated',
                    'route_name' => 'home',
                    'url' => '',
                    'sort_order' => 1,
                    'is_active' => true,
                ],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('navigation_items', [
            'id' => $item->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->delete('/admin/navigation/'.$item->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('navigation_items', ['id' => $item->id]);
    }

    public function test_navigation_admin_form_uses_named_routes_without_manual_url_fields(): void
    {
        $admin = Admin::create([
            'name' => 'Navigation Form Admin',
            'email' => 'navigation-form-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $this->get('/admin/navigation')
            ->assertOk()
            ->assertSee('Route name', false)
            ->assertDontSee('placeholder="https://example.com"', false)
            ->assertDontSee('name="url"', false);
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

    public function test_job_form_loads_existing_content_into_editor(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('jobs/editor-preview.png', 'image-content');
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
        $this->actingAs($admin, 'admin')
            ->get('https://jobs.example.test/admin/jobs/'.$job->id.'/edit')
            ->assertOk()
            ->assertSee('job-content-toolbar')
            ->assertSee('quill@2.0.3')
            ->assertSee('https://jobs.example.test/storage/jobs/editor-preview.png', false)
            ->assertSee('&lt;p&gt;Existing &lt;strong&gt;formatted&lt;/strong&gt; content&lt;/p&gt;', false);
    }

    public function test_login_uses_configured_logo_and_shows_placeholders(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('brand/jobyou-logo.png', 'logo-content');
        SiteSetting::updateOrCreate(['key' => 'logo_path'], ['value' => 'brand/jobyou-logo.png']);

        $this->get('https://jobs.example.test/admin/login')
            ->assertOk()
            ->assertSee('https://jobs.example.test/storage/brand/jobyou-logo.png', false)
            ->assertSee('JobYou')
            ->assertSee('Admin workspace')
            ->assertSee('placeholder="Enter your email address"', false)
            ->assertSee('placeholder="Enter your password"', false);
    }

    public function test_logo_upload_is_saved_as_the_final_logo_path_only(): void
    {
        Storage::fake('public');

        $admin = Admin::create([
            'name' => 'Settings Admin',
            'email' => 'settings-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->from('/admin/settings')->put('/admin/settings', [
            'site_title' => 'Test site title',
            'site_tagline' => 'A useful tagline',
            'banner_title' => 'Latest jobs',
            'footer_copyright' => '© 2026 Test',
            'logo_upload' => UploadedFile::fake()->image('site-logo.png', 200, 80),
        ]);

        $response->assertRedirect('/admin/settings');
        $this->assertDatabaseHas('site_settings', ['key' => 'site_title', 'value' => 'Test site title']);
        $this->assertDatabaseHas('site_settings', ['key' => 'logo_path']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'logo_upload']);

        $storedPath = SiteSetting::where('key', 'logo_path')->value('value');
        $this->assertStringStartsWith('brand/', $storedPath);
        $this->assertTrue(Storage::disk('public')->exists($storedPath));
    }
    public function test_external_logo_url_is_rendered_as_an_image_src_without_raw_link_output(): void
    {
        $admin = Admin::create([
            'name' => 'Logo Admin',
            'email' => 'logo-admin@example.com',
            'password' => 'secret123',
            'status' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $this->from('/admin/settings')->put('/admin/settings', [
            'site_title' => 'Example site',
            'banner_title' => 'Latest jobs',
            'footer_copyright' => '© 2026 Test',
            'logo_path' => 'https://example.com/logo.png',
        ]);

        $this->get('/admin/settings')
            ->assertOk()
            ->assertSee('id="logo-preview"', false)
            ->assertSee('src="https://example.com/logo.png"', false)
            ->assertDontSeeHtml('<a href="https://example.com/logo.png"');
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
        $this->assertTrue(Storage::disk('public')->exists($job->image_url));
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

    public function test_admin_login_is_rate_limited(): void
    {
        Admin::create([
            'name' => 'Test Admin',
            'email' => 'throttle-admin@example.com',
            'password' => 'strong-test-password',
            'status' => true,
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->post('/admin/login', [
                'email' => 'throttle-admin@example.com',
                'password' => 'incorrect-password',
            ])->assertRedirect();
        }

        $this->post('/admin/login', [
            'email' => 'throttle-admin@example.com',
            'password' => 'incorrect-password',
        ])->assertTooManyRequests();
    }

    public function test_disabled_admin_sessions_are_denied_and_security_headers_are_present(): void
    {
        $admin = Admin::create([
            'name' => 'Disabled Admin',
            'email' => 'disabled-admin@example.com',
            'password' => 'strong-test-password',
            'status' => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertRedirect(route('admin.login'));

        $this->get('/admin/login')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_admin_seeder_rejects_the_weak_default_password(): void
    {
        config([
            'admin.email' => 'seed-admin@example.test',
            'admin.password' => 'password',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        app(AdminSeeder::class)->run();
    }

    public function test_admin_seeder_hashes_a_configured_strong_password(): void
    {
        config([
            'admin.name' => 'Configured Admin',
            'admin.email' => 'configured-admin@example.test',
            'admin.password' => 'configured-strong-password',
        ]);

        app(AdminSeeder::class)->run();

        $admin = Admin::where('email', 'configured-admin@example.test')->firstOrFail();

        $this->assertSame('Configured Admin', $admin->name);
        $this->assertTrue(Hash::check('configured-strong-password', $admin->password));
    }
}
