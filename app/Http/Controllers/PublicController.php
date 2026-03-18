<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\HomeSlider;

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
}
