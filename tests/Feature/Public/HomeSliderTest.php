<?php

namespace Tests\Feature\Public;

use App\Models\HomeSlider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeSliderTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_slide_route_streams_an_uploaded_slider_image(): void
    {
        Storage::fake('public');

        $path = UploadedFile::fake()->image('hero.jpg')->store('sliders', 'public');

        $slider = HomeSlider::query()->create([
            'title' => 'National Convention',
            'subtitle' => 'Annual gathering update',
            'image_path' => $path,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('slides.show', $slider))
            ->assertOk();
    }

    public function test_public_slide_route_returns_not_found_when_the_file_is_missing(): void
    {
        Storage::fake('public');

        $slider = HomeSlider::query()->create([
            'title' => 'Missing media',
            'subtitle' => 'Should not render',
            'image_path' => 'sliders/missing.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('slides.show', $slider))
            ->assertNotFound();
    }

    public function test_homepage_uses_the_public_slide_route_for_slider_images(): void
    {
        Storage::fake('public');

        $path = UploadedFile::fake()->image('hero.jpg')->store('sliders', 'public');

        $slider = HomeSlider::query()->create([
            'title' => 'Youth Revival',
            'subtitle' => 'Branch activity spotlight',
            'image_path' => $path,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(route('slides.show', $slider), false);
    }
}
