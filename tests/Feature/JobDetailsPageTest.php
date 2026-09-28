<?php

namespace Tests\Feature;

use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobDetailsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_details_page_uses_selected_job_data(): void
    {
        JobPosting::create([
            'title' => 'Jal Shakti Vibhag Recruitment 2026',
            'slug' => 'jal-shakti-vibhag-recruitment-2026',
            'qualification' => '8th Pass',
            'image_url' => 'https://example.com/job.jpg',
            'excerpt' => 'Dynamic excerpt',
            'content' => '<p>Dynamic details content.</p>',
            'author' => 'ADMIN963',
            'published_at' => now(),
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->get('/job-details?job=jal-shakti-vibhag-recruitment-2026');

        $response->assertOk();
        $response->assertSee('Jal Shakti Vibhag Recruitment 2026');
        $response->assertSee('Dynamic details content.');
    }

    public function test_job_details_renders_safe_rich_text_and_falls_back_for_a_missing_image(): void
    {
        JobPosting::create([
            'title' => 'Formatted Job Details',
            'slug' => 'formatted-job-details',
            'qualification' => 'Graduate',
            'content' => '<h2 onclick="alert(1)">Requirements</h2><p><strong>Bold text</strong> and <a href="https://example.test/apply" onclick="alert(2)">safe link</a>.</p><script>alert(3)</script><p><a href="javascript:alert(4)">unsafe link</a></p>',
            'author' => 'ADMIN963',
            'published_at' => now(),
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->get('/job-details?job=formatted-job-details');

        $response->assertOk()
            ->assertSee('<h2>Requirements</h2>', false)
            ->assertSee('<strong>Bold text</strong>', false)
            ->assertSee('href="https://example.test/apply"', false)
            ->assertSee('assets/img/placeholder-job.svg', false)
            ->assertDontSee('onclick', false)
            ->assertDontSee('<script>', false)
            ->assertDontSee('javascript:', false);
    }

    public function test_plain_text_job_content_is_rendered_as_text(): void
    {
        JobPosting::create([
            'title' => 'Plain Text Job Details',
            'slug' => 'plain-text-job-details',
            'content' => "Plain content line one\nPlain content line two",
            'author' => 'ADMIN963',
            'is_published' => true,
        ]);

        $response = $this->get('/job-details?job=plain-text-job-details');

        $response->assertOk()
            ->assertSee('Plain content line one')
            ->assertSee('Plain content line two')
            ->assertSee('<br />', false)
            ->assertDontSee('&lt;p&gt;Plain content');
    }
}
