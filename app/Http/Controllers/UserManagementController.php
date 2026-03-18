<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    private const ROLE_OPTIONS = [
        'super_admin',
        'regional_admin',
        'district_admin',
        'branch_admin',
        'bishop',
        'pastor',
        'accountant',
        'member',
    ];

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->string('q'));

        $users = User::query()
            ->with(['region:id,name', 'district:id,name', 'branch:id,name,type'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('role', 'like', '%' . $search . '%');
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('panel.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('panel.users.create', $this->formData());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $role = $this->validatedRole($request->string('role')->toString());
        $branchId = $request->integer('branch_id');

        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'password' => $request->string('password')->toString(),
            'role' => $role,
            'status' => 'active',
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
            'branch_id' => $branchId,
            'church_id' => $branchId,
        ]);

        $user->syncRoles([$role]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('User account created successfully.'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('panel.users.edit', array_merge($this->formData(), [
            'managedUser' => $user->load(['region', 'district', 'branch']),
        ]));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $request->validate([
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $role = $request->filled('role')
            ? $this->validatedRole($request->string('role')->toString())
            : $this->validatedRole($user->normalizedRoleName() ?? 'member');

        $branchId = $request->integer('branch_id');

        $payload = [
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'role' => $role,
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
            'branch_id' => $branchId,
            'church_id' => $branchId,
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->string('password')->toString();
        }

        $user->update($payload);
        $user->syncRoles([$role]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', __('User account updated successfully.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('User account deleted successfully.'));
    }

    private function formData(): array
    {
        return [
            'regions' => Region::query()->orderBy('name')->get(),
            'districts' => District::query()->orderBy('name')->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'roleOptions' => self::ROLE_OPTIONS,
        ];
    }

    private function validatedRole(string $role): string
    {
        $normalized = Str::of($role)
            ->lower()
            ->replace('-', '_')
            ->replace(' ', '_')
            ->value();

        if (! in_array($normalized, self::ROLE_OPTIONS, true)) {
            throw ValidationException::withMessages([
                'role' => __('Invalid role selected.'),
            ]);
        }

        return $normalized;
    }
}
