<?php

namespace App\Http\Controllers;

use App\Models\HomeSlider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeSliderController extends Controller
{
    public function index()
    {
        $sliders = HomeSlider::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('panel.sliders.index', [
            'sliders' => $sliders,
            'activeCount' => HomeSlider::query()->where('is_active', true)->count(),
            'inactiveCount' => HomeSlider::query()->where('is_active', false)->count(),
        ]);
    }

    public function create()
    {
        return view('panel.sliders.create', [
            'slider' => new HomeSlider([
                'is_active' => true,
                'sort_order' => (int) HomeSlider::query()->max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSlider($request, true);
        $path = $request->file('image')->store('sliders', 'public');

        HomeSlider::query()->create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image_path' => $path,
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'sort_order' => (int) ($validated['sort_order'] ?? ((int) HomeSlider::query()->max('sort_order') + 1)),
        ]);

        return redirect()->route('sliders.index')->with('status', __('Slide created successfully.'));
    }

    public function edit(HomeSlider $slider)
    {
        return view('panel.sliders.edit', compact('slider'));
    }

    public function update(Request $request, HomeSlider $slider): RedirectResponse
    {
        $validated = $this->validateSlider($request, false);
        $oldImagePath = $slider->image_path;
        $newPath = $oldImagePath;

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('sliders', 'public');
        }

        $slider->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image_path' => $newPath,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'sort_order' => (int) ($validated['sort_order'] ?? $slider->sort_order),
        ]);

        if ($request->hasFile('image') && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('sliders.index')->with('status', __('Slide updated successfully.'));
    }

    public function updateStatus(Request $request, HomeSlider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $slider->update([
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()->route('sliders.index')->with('status', __('Slide visibility updated.'));
    }

    public function updateSortOrder(Request $request, HomeSlider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $slider->update([
            'sort_order' => (int) $validated['sort_order'],
        ]);

        return redirect()->route('sliders.index')->with('status', __('Slide order updated.'));
    }

    public function destroy(HomeSlider $slider): RedirectResponse
    {
        if ($slider->image_path) {
            Storage::disk('public')->delete($slider->image_path);
        }

        $slider->delete();

        return redirect()->route('sliders.index')->with('status', __('Slide removed.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateSlider(Request $request, bool $requireImage): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => array_values(array_filter([
                $requireImage ? 'required' : 'nullable',
                'image',
                'max:4096',
            ])),
        ]);
    }
}
