<?php

namespace Tests\Feature;

use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_uploaded_image_when_available(): void
    {
        Storage::fake('public');

        $stored = Storage::disk('public')->putFile('jobs', UploadedFile::fake()->image('job-upload.jpg', 1200, 800));

        JobPosting::create([
            'title' => 'Uploaded Job',
            'slug' => 'uploaded-job',
            'qualification' => '10th Pass',
            'image_url' => $stored,
            'author' => 'ADMIN963',
            'excerpt' => 'Sample excerpt',
            'content' => 'Sample content',
            'published_at' => now(),
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('/storage/' . $stored, false);
    }

    public function test_home_page_uses_external_image_url_when_uploaded_image_is_missing(): void
    {
        JobPosting::create([
            'title' => 'External Job',
            'slug' => 'external-job',
            'qualification' => '12th Pass',
            'image_url' => 'https://images.example.com/external-job.jpg',
            'author' => 'ADMIN963',
            'excerpt' => 'Sample excerpt',
            'content' => 'Sample content',
            'published_at' => now(),
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('https://images.example.com/external-job.jpg');
    }
}
