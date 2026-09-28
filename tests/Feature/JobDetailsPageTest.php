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
}
