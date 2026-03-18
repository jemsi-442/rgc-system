<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::withCount('members', 'users')->orderBy('name')->paginate(7);
        return view('panel.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Tafadhali ingiza jina la idara',
            'name.unique' => 'Idara hii tayari ipo',
            'name.max' => 'Jina la idara ni refu mno',
            'description.max' => 'Maelezo ni marefu mno',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Idara imeongezwa kikamilifu');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $department = Department::withCount('members', 'users')->findOrFail($id);
        $members = $department->members()->with('user')->paginate(7);
        $users = $department->users()->with('role')->paginate(7);

        return view('panel.departments.show', compact('department', 'members', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $department = Department::findOrFail($id);
        return view('panel.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Tafadhali ingiza jina la idara',
            'name.unique' => 'Idara hii tayari ipo',
            'name.max' => 'Jina la idara ni refu mno',
            'description.max' => 'Maelezo ni marefu mno',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Idara imebadilishwa kikamilifu');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);

        // Check if department has members or users
        if ($department->members()->count() > 0 || $department->users()->count() > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'Idara hii haiwezi kufutwa kwa sababu ina wanachama au watumiaji');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Idara imefutwa kikamilifu');
    }
}
