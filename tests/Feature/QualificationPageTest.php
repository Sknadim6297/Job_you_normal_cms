<?php

namespace Tests\Feature;

use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualificationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_8th_pass_page_displays_latest_dynamic_jobs(): void
    {
        JobPosting::create([
            'title' => 'Dynamic 8th Pass Job',
            'slug' => 'dynamic-8th-pass-job',
            'qualification' => '8th Pass',
            'image_url' => 'https://example.com/job.jpg',
            'excerpt' => 'Dynamic excerpt',
            'content' => 'Dynamic content',
            'author' => 'ADMIN963',
            'published_at' => now(),
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->get('/8thpass');

        $response->assertOk();
        $response->assertSee('Dynamic 8th Pass Job');
    }

    public function test_category_pagination_preserves_query_and_uses_the_current_domain(): void
    {
        foreach (range(1, 60) as $number) {
            JobPosting::create([
                'title' => 'Pagination 8th Pass Job '.$number,
                'slug' => 'pagination-8th-pass-job-'.$number,
                'qualification' => '8th Pass',
                'author' => 'ADMIN963',
                'published_at' => now()->subDays($number),
                'sort_order' => $number,
                'is_published' => true,
            ]);
        }

        $firstPage = $this->get('https://jobs.example.test/8thpass?search=qualifying&page=1');
        $firstPage->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => $jobs->perPage() === 12
                && $jobs->currentPage() === 1
                && $jobs->firstItem() === 1
                && $jobs->lastItem() === 12
                && $jobs->total() === 56
                && $jobs->count() === 12)
            ->assertSee('https://jobs.example.test/8thpass?search=qualifying&amp;page=2', false)
            ->assertDontSee('127.0.0.1:8000');
        $this->assertStringContainsString('Showing 1 to 12 of 56 results', preg_replace('/\s+/', ' ', strip_tags($firstPage->getContent())));
        $this->assertSame(1, substr_count($firstPage->getContent(), 'aria-label="Job listings pagination"'));
        $this->assertStringContainsString('aria-disabled="true"', $firstPage->getContent());

        $secondPage = $this->get('https://jobs.example.test/8thpass?search=qualifying&page=2');
        $secondPage->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => $jobs->currentPage() === 2
                && $jobs->firstItem() === 13
                && $jobs->lastItem() === 24
                && $jobs->count() === 12)
            ->assertSee('https://jobs.example.test/8thpass?search=qualifying&amp;page=1', false)
            ->assertSee('https://jobs.example.test/8thpass?search=qualifying&amp;page=3', false);

        $middlePage = $this->get('https://jobs.example.test/8thpass?search=qualifying&page=3');
        $middlePage->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => $jobs->currentPage() === 3
                && $jobs->firstItem() === 25
                && $jobs->lastItem() === 36
                && $jobs->count() === 12);
        $this->assertStringContainsString('Showing 25 to 36 of 56 results', preg_replace('/\s+/', ' ', strip_tags($middlePage->getContent())));

        $lastPage = $this->get('https://jobs.example.test/8thpass?search=qualifying&page=5');
        $lastPage->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => $jobs->currentPage() === 5
                && $jobs->firstItem() === 49
                && $jobs->lastItem() === 56
                && $jobs->count() === 8)
            ->assertSee('https://jobs.example.test/8thpass?search=qualifying&amp;page=4', false);
        $this->assertStringContainsString('Showing 49 to 56 of 56 results', preg_replace('/\s+/', ' ', strip_tags($lastPage->getContent())));
        $this->assertStringContainsString('aria-disabled="true"', $lastPage->getContent());
        $this->assertSame(1, substr_count($lastPage->getContent(), 'aria-label="Job listings pagination"'));
    }

    public function test_homepage_pagination_uses_twelve_records_and_preserves_query_parameters(): void
    {
        foreach (range(1, 25) as $number) {
            JobPosting::create([
                'title' => 'Homepage Pagination Job '.$number,
                'slug' => 'homepage-pagination-job-'.$number,
                'author' => 'ADMIN963',
                'published_at' => now()->subDays($number),
                'sort_order' => $number,
                'is_published' => true,
            ]);
        }

        $response = $this->get('https://jobs.example.test/?campaign=summer&page=2');

        $response->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => $jobs->perPage() === 12
                && $jobs->currentPage() === 2
                && $jobs->firstItem() === 13
                && $jobs->lastItem() === 24
                && $jobs->total() === 25
                && $jobs->count() === 12)
            ->assertSee('https://jobs.example.test?campaign=summer&amp;page=1', false)
            ->assertSee('https://jobs.example.test?campaign=summer&amp;page=3', false)
            ->assertDontSee('127.0.0.1:8000');

        $this->assertStringContainsString('Showing 13 to 24 of 25 results', preg_replace('/\s+/', ' ', strip_tags($response->getContent())));
        $this->assertSame(1, substr_count($response->getContent(), 'aria-label="Job listings pagination"'));
    }

    public function test_other_frontend_category_routes_render_page_two(): void
    {
        $categories = [
            '10thpass' => '10th Pass',
            '12thpass' => '12th Pass',
            'govt-jobs' => null,
        ];

        foreach ($categories as $route => $qualification) {
            foreach (range(1, 18) as $number) {
                JobPosting::create([
                    'title' => $qualification
                        ? 'Pagination '.$qualification.' Job '.$number
                        : 'Pagination Government Job '.$number,
                    'slug' => 'pagination-'.$route.'-job-'.$number,
                    'qualification' => $qualification,
                    'author' => 'ADMIN963',
                    'published_at' => now()->subDays($number),
                    'sort_order' => $number,
                    'is_published' => true,
                ]);
            }

            $response = $this->get('https://jobs.example.test/'.$route.'?search=public&page=2');
            $expectedTotal = $route === 'govt-jobs' ? 50 : 14;
            $expectedLastItem = $route === 'govt-jobs' ? 24 : 14;
            $expectedPageCount = $route === 'govt-jobs' ? 12 : 2;

            $response->assertOk()
                ->assertViewHas('jobs', fn ($jobs) => $jobs->perPage() === 12
                    && $jobs->currentPage() === 2
                    && $jobs->firstItem() === 13
                    && $jobs->lastItem() === $expectedLastItem
                    && $jobs->total() === $expectedTotal
                    && $jobs->count() === $expectedPageCount)
                ->assertSee('https://jobs.example.test/'.$route.'?search=public&amp;page=1', false)
                ->assertSee('https://jobs.example.test/'.$route.'?search=public&amp;page=2', false)
                ->assertDontSee('127.0.0.1:8000');

            $this->assertSame(1, substr_count($response->getContent(), 'aria-label="Job listings pagination"'));
            $this->assertStringContainsString('job-pagination__link is-current', $response->getContent());
        }
    }
}
