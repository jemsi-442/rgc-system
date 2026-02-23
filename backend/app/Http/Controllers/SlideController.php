<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSlideRequest;
use App\Models\Slide;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
    }

    public function publicIndex(): JsonResponse
    {
        $slides = Slide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $slides,
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Slide::orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreSlideRequest $request): JsonResponse
    {
        $path = $request->file('image')->store('slides', 'public');

        $slide = Slide::create([
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'image_path' => $path,
            'sort_order' => $request->integer('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->activityLogService->log($request, 'slide.created', Slide::class, $slide->id, $slide->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $slide,
        ], 201);
    }

    public function update(Request $request, Slide $slide): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slide->image_path);
            $validated['image_path'] = $request->file('image')->store('slides', 'public');
        }

        $slide->update($validated);
        $this->activityLogService->log($request, 'slide.updated', Slide::class, $slide->id, $slide->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $slide,
        ]);
    }

    public function destroy(Request $request, Slide $slide): JsonResponse
    {
        Storage::disk('public')->delete($slide->image_path);
        $slide->delete();

        $this->activityLogService->log($request, 'slide.deleted', Slide::class, $slide->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Slide deleted successfully',
        ]);
    }
}
