<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchRequest;
use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Branch::class);

        $branches = Branch::query()
            ->with(['region', 'district'])
            ->latest()
            ->paginate(20);

        return view('panel.branches.index', compact('branches'));
    }

    public function create()
    {
        $this->authorize('create', Branch::class);

        return view('panel.branches.create', [
            'regions' => Region::query()->orderBy('name')->get(),
            'districts' => District::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBranchRequest $request)
    {
        $this->authorize('create', Branch::class);

        Branch::query()->create([
            'name' => $request->string('name')->toString(),
            'type' => $request->string('branch_type')->toString(),
            'slug' => Str::slug($request->string('name')->toString()),
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
            'status' => 'active',
        ]);

        return redirect()->route('branches.index')->with('status', 'Branch created successfully.');
    }

    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);

        return view('panel.branches.edit', [
            'branch' => $branch,
            'regions' => Region::query()->orderBy('name')->get(),
            'districts' => District::query()->orderBy('name')->get(),
        ]);
    }

    public function update(StoreBranchRequest $request, Branch $branch)
    {
        $this->authorize('update', $branch);

        $branch->update([
            'name' => $request->string('name')->toString(),
            'type' => $request->string('branch_type')->toString(),
            'slug' => Str::slug($request->string('name')->toString()),
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
        ]);

        return redirect()->route('branches.index')->with('status', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('delete', $branch);
        $branch->delete();

        return redirect()->route('branches.index')->with('status', 'Branch removed.');
    }
}
