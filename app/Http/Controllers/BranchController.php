<?php

namespace App\Http\Controllers;

use App\Exports\BranchExport;
use App\Exports\BranchImportTemplateExport;
use App\Exports\BranchRecordsExport;
use App\Http\Requests\StoreBranchImportRequest;
use App\Http\Requests\StoreBranchRequest;
use App\Imports\BranchRowsImport;
use App\Models\Branch;
use App\Models\District;
use App\Models\OfferingPayment;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Branch::class);

        $filters = $this->branchFilters($request);

        $branches = Branch::query()
            ->with(['region', 'district'])
            ->when($filters['region_id'], fn (Builder $query) => $query->where('region_id', $filters['region_id']))
            ->when($filters['district_id'], fn (Builder $query) => $query->where('district_id', $filters['district_id']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('panel.branches.index', [
            'branches' => $branches,
            'regions' => Region::query()->orderBy('name')->get(),
            'filters' => $filters,
            'selectedRegion' => $filters['region_id'] ? Region::query()->find($filters['region_id']) : null,
            'selectedDistrict' => $filters['district_id'] ? District::query()->find($filters['district_id']) : null,
        ]);
    }

    public function show(Branch $branch)
    {
        $this->authorize('view', $branch);

        $branch->load(['region', 'district'])
            ->loadCount([
                'users',
                'messages',
                'offerings',
                'expenses',
                'events',
                'offeringPayments',
                'offeringPayments as pending_payments_count' => fn (Builder $query) => $query->where('status', 'pending'),
                'offeringPayments as completed_payments_count' => fn (Builder $query) => $query->where('status', 'completed'),
            ])
            ->loadSum('offerings as offerings_total_amount', 'amount')
            ->loadSum('expenses as expenses_total_amount', 'amount')
            ->loadSum('offeringPayments as payment_requests_total_amount', 'amount')
            ->loadSum(['offeringPayments as completed_payments_total_amount' => fn (Builder $query) => $query->where('status', 'completed')], 'amount');

        $recentUsers = $branch->users()
            ->latest()
            ->take(6)
            ->get(['id', 'name', 'email']);

        $recentOfferings = $branch->offerings()
            ->latest('date')
            ->take(5)
            ->get(['id', 'amount', 'date']);

        $recentExpenses = $branch->expenses()
            ->latest('date')
            ->take(5)
            ->get(['id', 'amount', 'description', 'date']);

        $recentEvents = $branch->events()
            ->latest('event_date')
            ->take(5)
            ->get(['id', 'title', 'event_date']);

        $recentPayments = $branch->offeringPayments()
            ->latest()
            ->take(5)
            ->get([
                'id',
                'public_reference',
                'amount',
                'status',
                'payer_name',
                'description',
                'paid_at',
                'created_at',
                'checkout_url',
            ]);

        $netBalance = ((float) ($branch->offerings_total_amount ?? 0)) - ((float) ($branch->expenses_total_amount ?? 0));

        return view('panel.branches.show', compact(
            'branch',
            'recentUsers',
            'recentOfferings',
            'recentExpenses',
            'recentEvents',
            'recentPayments',
            'netBalance',
        ));
    }

    public function print(Branch $branch)
    {
        $data = $this->branchProfileData($branch);

        return response()->view('panel.branches.print', $data);
    }

    public function pdf(Branch $branch)
    {
        $data = $this->branchProfileData($branch);

        $pdf = Pdf::loadView('panel.branches.pdf', [
            ...$data,
            'logoDataUri' => $this->pdfLogoDataUri(),
        ])->setPaper('a4');

        return $pdf->download('branch-profile-' . $branch->id . '.pdf');
    }

    public function create()
    {
        $this->authorize('create', Branch::class);

        return view('panel.branches.create', $this->branchFormData());
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $this->authorize('create', Branch::class);

        $attributes = $this->validatedBranchAttributes($request);
        $attributes['slug'] = $this->makeUniqueSlug($attributes['name']);

        Branch::query()->create($attributes);

        return redirect()->route('branches.index')->with('status', __('Branch created successfully.'));
    }

    public function import(StoreBranchImportRequest $request): RedirectResponse
    {
        $this->authorize('create', Branch::class);

        try {
            $rows = Excel::toArray(new BranchRowsImport(), $request->file('branch_file'))[0] ?? [];
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'branch_file' => __('We could not read that file. Upload a valid CSV or Excel file using the official RGC branch template.'),
                ])
                ->withInput();
        }

        if ($rows === []) {
            return back()
                ->withErrors([
                    'branch_import' => __('The uploaded file has no branch rows. Download the template, fill it in, and upload it again.'),
                ])
                ->withInput();
        }

        $headings = array_map('strval', array_keys($rows[0] ?? []));
        $requiredHeadings = ['region', 'district', 'branch_name', 'branch_type'];
        $missingHeadings = array_values(array_diff($requiredHeadings, $headings));

        if ($missingHeadings !== []) {
            return back()
                ->withErrors([
                    'branch_import' => __('The file headings are not correct. Required columns: :columns.', ['columns' => implode(', ', $requiredHeadings)]),
                ])
                ->with('branch_import_errors', [
                    __('Missing columns: :columns', ['columns' => implode(', ', $missingHeadings)]),
                ])
                ->withInput();
        }

        [$preparedRows, $previewRows, $errors] = $this->prepareImportRows($rows);

        if ($preparedRows === [] && $errors === []) {
            $errors[] = __('The file did not contain any usable branch rows.');
        }

        if ($errors !== []) {
            return back()
                ->withErrors([
                    'branch_import' => __('Branch import could not continue. Fix the rows below and upload the file again.'),
                ])
                ->with('branch_import_errors', $errors)
                ->withInput();
        }

        $token = (string) Str::uuid();
        Cache::put($this->previewCacheKey($token), $preparedRows, now()->addMinutes(30));

        return redirect()
            ->route('branches.create')
            ->with('status', __('Import file is valid. Review the preview below, then confirm to save :count branches.', ['count' => count($preparedRows)]))
            ->with('branch_import_token', $token)
            ->with('branch_import_preview', $previewRows);
    }

    public function confirmImport(Request $request): RedirectResponse
    {
        $this->authorize('create', Branch::class);

        $validated = $request->validate([
            'import_token' => ['required', 'string'],
        ]);

        $preparedRows = Cache::pull($this->previewCacheKey($validated['import_token']));

        if (! is_array($preparedRows) || $preparedRows === []) {
            return redirect()
                ->route('branches.create')
                ->withErrors([
                    'branch_import' => __('The import preview has expired. Upload the file again to continue.'),
                ]);
        }

        DB::transaction(function () use ($preparedRows): void {
            foreach ($preparedRows as $attributes) {
                Branch::query()->create([
                    ...$attributes,
                    'slug' => $this->makeUniqueSlug($attributes['name']),
                ]);
            }
        });

        return redirect()->route('branches.index')->with('status', __('Imported :count branches successfully.', ['count' => count($preparedRows)]));
    }

    public function template(string $format = 'xlsx')
    {
        $this->authorize('create', Branch::class);

        if (! in_array($format, ['xlsx', 'csv'], true)) {
            abort(404);
        }

        return Excel::download(
            new BranchImportTemplateExport(false),
            "rgc-branch-import-template.{$format}",
            $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX,
        );
    }

    public function sampleTemplate(string $format = 'xlsx')
    {
        $this->authorize('create', Branch::class);

        if (! in_array($format, ['xlsx', 'csv'], true)) {
            abort(404);
        }

        return Excel::download(
            new BranchImportTemplateExport(true),
            "rgc-branch-import-sample.{$format}",
            $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX,
        );
    }

    public function export(Request $request, string $format = 'xlsx')
    {
        $this->authorize('viewAny', Branch::class);

        if (! in_array($format, ['xlsx', 'csv'], true)) {
            abort(404);
        }

        $filters = $this->branchFilters($request);

        return Excel::download(
            new BranchExport($filters['region_id'], $filters['district_id']),
            "rgc-branches-export.{$format}",
            $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX,
        );
    }

    public function exportRecords(Request $request, Branch $branch, string $format = 'xlsx')
    {
        $this->authorize('view', $branch);

        if (! in_array($format, ['xlsx', 'csv'], true)) {
            abort(404);
        }

        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'preset' => ['nullable', 'in:this_month,last_30_days,this_year,all_time'],
        ]);

        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;
        $preset = $filters['preset'] ?? null;

        if ($preset) {
            $today = Carbon::today();

            [$dateFrom, $dateTo] = match ($preset) {
                'this_month' => [$today->copy()->startOfMonth()->toDateString(), $today->toDateString()],
                'last_30_days' => [$today->copy()->subDays(29)->toDateString(), $today->toDateString()],
                'this_year' => [$today->copy()->startOfYear()->toDateString(), $today->toDateString()],
                'all_time' => [null, null],
            };
        }

        $filename = 'branch-records-' . $branch->id;

        if ($dateFrom || $dateTo) {
            $filename .= '-' . ($dateFrom ?: 'start') . '-to-' . ($dateTo ?: 'latest');
        }

        return Excel::download(
            new BranchRecordsExport($branch, $dateFrom, $dateTo),
            $filename . '.' . $format,
            $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX,
        );
    }

    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);

        return view('panel.branches.edit', $this->branchFormData($branch));
    }

    public function update(StoreBranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->authorize('update', $branch);

        $attributes = $this->validatedBranchAttributes($request);
        $attributes['slug'] = $this->makeUniqueSlug($attributes['name'], $branch->id);

        $branch->update($attributes);

        return redirect()->route('branches.index')->with('status', __('Branch updated successfully.'));
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->authorize('delete', $branch);
        $branch->delete();

        return redirect()->route('branches.index')->with('status', __('Branch removed.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function branchFormData(?Branch $branch = null): array
    {
        $importPreview = session('branch_import_preview', []);

        return [
            'branch' => $branch,
            'regions' => Region::query()->orderBy('name')->get(),
            'districts' => District::query()->orderBy('name')->get(),
            'branchTypes' => [
                'headquarters' => __('Headquarters'),
                'regional' => __('Regional'),
                'district' => __('District'),
                'local' => __('Local'),
            ],
            'branchStatuses' => [
                'active' => __('Active'),
                'inactive' => __('Inactive'),
            ],
            'requiredImportColumns' => ['region', 'district', 'branch_name', 'branch_type'],
            'optionalImportColumns' => ['address', 'phone', 'email', 'status'],
            'importPreview' => $importPreview,
            'importPreviewToken' => session('branch_import_token'),
            'importPreviewSummary' => $this->buildImportPreviewSummary($importPreview),
        ];
    }

    /**
     * @return array{region_id: ?int, district_id: ?int}
     */
    private function branchFilters(Request $request): array
    {
        $validated = $request->validate([
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
        ]);

        $regionId = isset($validated['region_id']) ? (int) $validated['region_id'] : null;
        $districtId = isset($validated['district_id']) ? (int) $validated['district_id'] : null;

        if ($districtId && $regionId) {
            $districtBelongsToRegion = District::query()
                ->whereKey($districtId)
                ->where('region_id', $regionId)
                ->exists();

            if (! $districtBelongsToRegion) {
                $districtId = null;
            }
        }

        return [
            'region_id' => $regionId,
            'district_id' => $districtId,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function branchProfileData(Branch $branch): array
    {
        $this->authorize('view', $branch);

        $branch->load(['region', 'district'])
            ->loadCount(['users', 'messages', 'offerings', 'expenses', 'events'])
            ->loadSum('offerings as offerings_total_amount', 'amount')
            ->loadSum('expenses as expenses_total_amount', 'amount');

        $recentUsers = $branch->users()
            ->latest()
            ->take(6)
            ->get(['id', 'name', 'email']);

        $recentOfferings = $branch->offerings()
            ->latest('date')
            ->take(5)
            ->get(['id', 'amount', 'date']);

        $recentExpenses = $branch->expenses()
            ->latest('date')
            ->take(5)
            ->get(['id', 'amount', 'description', 'date']);

        $recentEvents = $branch->events()
            ->latest('event_date')
            ->take(5)
            ->get(['id', 'title', 'event_date']);

        $recentPayments = $branch->offeringPayments()
            ->latest()
            ->take(5)
            ->get([
                'id',
                'public_reference',
                'amount',
                'status',
                'payer_name',
                'description',
                'paid_at',
                'created_at',
                'checkout_url',
            ]);

        $netBalance = ((float) ($branch->offerings_total_amount ?? 0)) - ((float) ($branch->expenses_total_amount ?? 0));

        return compact(
            'branch',
            'recentUsers',
            'recentOfferings',
            'recentExpenses',
            'recentEvents',
            'recentPayments',
            'netBalance',
        );
    }

    private function pdfLogoDataUri(): ?string
    {
        $path = public_path('images/rgc_logo.png');

        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedBranchAttributes(StoreBranchRequest $request): array
    {
        return [
            'name' => trim($request->string('name')->toString()),
            'type' => $request->string('branch_type')->toString(),
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
            'address' => $request->filled('address') ? trim((string) $request->input('address')) : null,
            'phone' => $request->filled('phone') ? trim((string) $request->input('phone')) : null,
            'email' => $request->filled('email') ? trim((string) $request->input('email')) : null,
            'status' => $request->input('status', 'active'),
        ];
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) !== '' ? Str::slug($name) : 'branch';
        $slug = $baseSlug;
        $counter = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Branch::query()
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>, 2: array<int, string>}
     */
    private function prepareImportRows(array $rows): array
    {
        $preparedRows = [];
        $previewRows = [];
        $errors = [];
        $seenBranches = [];
        $hasIncomingHeadquarters = false;
        $existingHeadquarters = Branch::query()->where('type', 'headquarters')->exists();

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 2;
            $normalized = $this->normalizeImportRow($row);

            if ($this->isImportRowBlank($normalized)) {
                continue;
            }

            $regionName = $this->extractImportValue($normalized, 'region');
            $districtName = $this->extractImportValue($normalized, 'district');
            $branchName = $this->extractImportValue($normalized, 'branch_name');
            $branchType = $this->normalizeBranchType($this->extractImportValue($normalized, 'branch_type'));
            $address = $this->extractImportValue($normalized, 'address');
            $phone = $this->extractImportValue($normalized, 'phone');
            $email = $this->extractImportValue($normalized, 'email');
            $status = $this->normalizeStatus($this->extractImportValue($normalized, 'status'));

            if ($regionName === '' || $districtName === '' || $branchName === '' || $branchType === null) {
                $errors[] = __('Row :row must include region, district, branch_name, and a valid branch_type.', ['row' => $rowNumber]);
                continue;
            }

            $region = Region::query()
                ->whereRaw('LOWER(name) = ?', [$this->normalizeLookup($regionName)])
                ->first();

            if (! $region) {
                $errors[] = __('Row :row references region ":region", but that region does not exist in Tanzania master data.', [
                    'row' => $rowNumber,
                    'region' => $regionName,
                ]);
                continue;
            }

            $district = District::query()
                ->where('region_id', $region->id)
                ->whereRaw('LOWER(name) = ?', [$this->normalizeLookup($districtName)])
                ->first();

            if (! $district) {
                $errors[] = __('Row :row uses district ":district", but it does not belong to region ":region".', [
                    'row' => $rowNumber,
                    'district' => $districtName,
                    'region' => $region->name,
                ]);
                continue;
            }

            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = __('Row :row has an invalid email address.', ['row' => $rowNumber]);
                continue;
            }

            $status = $status ?? 'active';

            if (! in_array($status, ['active', 'inactive'], true)) {
                $errors[] = __('Row :row has an invalid status. Use active or inactive only.', ['row' => $rowNumber]);
                continue;
            }

            if ($branchType === 'headquarters') {
                if ($existingHeadquarters || $hasIncomingHeadquarters) {
                    $errors[] = __('Row :row cannot create another headquarters branch because one already exists.', ['row' => $rowNumber]);
                    continue;
                }

                $hasIncomingHeadquarters = true;
            }

            $duplicateKey = strtolower($district->id . '|' . $branchName);

            if (isset($seenBranches[$duplicateKey])) {
                $errors[] = __('Row :row duplicates branch ":branch" inside district ":district" within the import file.', [
                    'row' => $rowNumber,
                    'branch' => $branchName,
                    'district' => $district->name,
                ]);
                continue;
            }

            $existsInDatabase = Branch::query()
                ->where('district_id', $district->id)
                ->whereRaw('LOWER(name) = ?', [$this->normalizeLookup($branchName)])
                ->exists();

            if ($existsInDatabase) {
                $errors[] = __('Row :row cannot import branch ":branch" because it already exists in district ":district".', [
                    'row' => $rowNumber,
                    'branch' => $branchName,
                    'district' => $district->name,
                ]);
                continue;
            }

            $seenBranches[$duplicateKey] = true;

            $preparedRows[] = [
                'name' => $branchName,
                'type' => $branchType,
                'region_id' => $region->id,
                'district_id' => $district->id,
                'address' => $address !== '' ? $address : null,
                'phone' => $phone !== '' ? $phone : null,
                'email' => $email !== '' ? $email : null,
                'status' => $status,
            ];

            $previewRows[] = [
                'row_number' => $rowNumber,
                'branch_name' => $branchName,
                'branch_type' => $branchType,
                'district' => $district->name,
                'region' => $region->name,
                'address' => $address !== '' ? $address : null,
                'phone' => $phone !== '' ? $phone : null,
                'email' => $email !== '' ? $email : null,
                'status' => $status,
            ];
        }

        return [$preparedRows, $previewRows, $errors];
    }

    private function previewCacheKey(string $token): string
    {
        return 'branch-import-preview:' . $token;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeImportRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim((string) $key));
            $normalized[$normalizedKey] = $value;
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function isImportRowBlank(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function extractImportValue(array $row, string $key): string
    {
        return trim((string) ($row[$key] ?? ''));
    }

    private function normalizeLookup(string $value): string
    {
        return Str::lower(trim($value));
    }

    private function normalizeBranchType(string $value): ?string
    {
        $normalized = Str::lower(trim($value));

        return in_array($normalized, ['headquarters', 'regional', 'district', 'local'], true)
            ? $normalized
            : null;
    }

    private function normalizeStatus(string $value): ?string
    {
        $normalized = Str::lower(trim($value));

        if ($normalized === '') {
            return null;
        }

        return in_array($normalized, ['active', 'inactive'], true)
            ? $normalized
            : null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $importPreview
     * @return array<string, mixed>
     */
    private function buildImportPreviewSummary(array $importPreview): array
    {
        $rows = collect($importPreview);

        if ($rows->isEmpty()) {
            return [
                'total' => 0,
                'regions' => 0,
                'districts' => 0,
                'type_breakdown' => [],
            ];
        }

        return [
            'total' => $rows->count(),
            'regions' => $rows->pluck('region')->filter()->unique()->count(),
            'districts' => $rows->pluck('district')->filter()->unique()->count(),
            'type_breakdown' => $rows
                ->groupBy('branch_type')
                ->map(fn (Collection $group, string $type) => [
                    'label' => __(Str::headline($type)),
                    'count' => $group->count(),
                ])
                ->values()
                ->all(),
        ];
    }
}
