<?php

namespace App\Http\Controllers;

use App\Models\HomeSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeSliderController extends Controller
{
    public function index()
    {
        $sliders = HomeSlider::query()->orderBy('sort_order')->paginate(20);

        return view('panel.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('panel.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('image')->store('sliders', 'public');

        HomeSlider::query()->create([
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'image_path' => $path,
            'is_active' => true,
            'sort_order' => (int) HomeSlider::query()->max('sort_order') + 1,
        ]);

        return redirect()->route('sliders.index')->with('status', __('Slider added.'));
    }

    public function destroy(HomeSlider $slider)
    {
        Storage::disk('public')->delete($slider->image_path);
        $slider->delete();

        return back()->with('status', __('Slider removed.'));
    }
}
