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

        $branches = Branch::query()
            ->with(['region', 'district'])
            ->orderBy('name')
            ->paginate(9);

        return view('welcome', compact('sliders', 'branches'));
    }

    public function slide(HomeSlider $slider)
    {
        abort_unless($slider->image_path && Storage::disk('public')->exists($slider->image_path), 404);

        $response = Storage::disk('public')->response(
            $slider->image_path,
            basename($slider->image_path),
        );

        $response->setPublic();
        $response->setMaxAge(86400);
        $response->headers->set('Cache-Control', 'public, max-age=86400');

        return $response;
    }
}
