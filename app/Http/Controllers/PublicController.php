<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\HomeSlider;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    public function index()
    {
        $sliders = HomeSlider::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(5)
            ->get();

        $branchCount = Branch::query()
            ->where('status', 'active')
            ->count();

        return view('welcome', compact('sliders', 'branchCount'));
    }

    public function slide(HomeSlider $slider)
    {
        abort_unless($slider->is_active, 404);
        abort_unless($slider->image_path && Storage::disk('public')->exists($slider->image_path), 404);

        $response = Storage::disk('public')->response(
            $slider->image_path,
            basename($slider->image_path),
        );

        $response->setPublic();
        $response->setMaxAge(86400);
        $response->headers->set('Cache-Control', 'public, max-age=86400');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
