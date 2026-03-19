@extends('layouts.app')

@section('content')
<div class="branch-admin-layout">
    <div class="form-shell branch-form-shell">
        <div class="form-panel">
            <div class="form-page-header">
                <div>
                    <span class="section-kicker">{{ __('Branch Setup') }}</span>
                    <h1 class="mt-4 text-2xl font-semibold">{{ __('Create Branch') }}</h1>
                    <p class="mt-2 text-sm text-black/65">{{ __('Create one branch manually or prepare a clean batch import file that follows the Tanzania region and district hierarchy exactly.') }}</p>
                </div>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">{{ __('Back to branches') }}</a>
            </div>

            <div class="branch-admin-banner mt-6">
                <div>
                    <strong>{{ __('Headquarters safeguard') }}</strong>
                    <p>{{ __('Only one headquarters branch is allowed in the system. Toangoma - Temeke - Dar es Salaam remains the seeded headquarters baseline.') }}</p>
                </div>
                <div>
                    <strong>{{ __('Location integrity') }}</strong>
                    <p>{{ __('Every branch must sit under the correct region and district. Inconsistent combinations are rejected automatically.') }}</p>
                </div>
            </div>

            @if($importPreviewToken && !empty($importPreview))
                <section class="branch-preview-card mt-6">
                    <div class="branch-preview-header">
                        <div>
                            <span class="section-kicker">{{ __('Import Preview') }}</span>
                            <h2 class="mt-3 text-xl font-semibold">{{ __('Review branches before saving') }}</h2>
                            <p class="mt-2 text-sm text-black/65">{{ __('Nothing has been saved yet. Confirm the rows below only if everything looks right.') }}</p>
                        </div>
                        <form method="POST" action="{{ route('branches.import.confirm') }}" class="branch-preview-confirm">
                            @csrf
                            <input type="hidden" name="import_token" value="{{ $importPreviewToken }}">
                            <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Confirm and save :count branches', ['count' => count($importPreview)]) }}</button>
                        </form>
                    </div>

                    <div class="branch-preview-summary mt-5">
                        <article class="branch-preview-stat">
                            <span>{{ __('Rows ready') }}</span>
                            <strong>{{ $importPreviewSummary['total'] }}</strong>
                            <p>{{ __('Validated branch rows waiting for confirmation.') }}</p>
                        </article>
                        <article class="branch-preview-stat">
                            <span>{{ __('Regions covered') }}</span>
                            <strong>{{ $importPreviewSummary['regions'] }}</strong>
                            <p>{{ __('Distinct Tanzania regions represented in this import.') }}</p>
                        </article>
                        <article class="branch-preview-stat">
                            <span>{{ __('Districts covered') }}</span>
                            <strong>{{ $importPreviewSummary['districts'] }}</strong>
                            <p>{{ __('Districts that will receive new branch records.') }}</p>
                        </article>
                    </div>

                    @if(!empty($importPreviewSummary['type_breakdown']))
                        <div class="branch-preview-breakdown mt-4">
                            @foreach($importPreviewSummary['type_breakdown'] as $typeItem)
                                <span>{{ $typeItem['label'] }}: <strong>{{ $typeItem['count'] }}</strong></span>
                            @endforeach
                        </div>
                    @endif

                    <div class="table-wrap mt-4">
                        <table class="responsive-table w-full text-sm branch-preview-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Row') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('District') }}</th>
                                    <th>{{ __('Region') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Contacts') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importPreview as $previewRow)
                                    <tr>
                                        <td>{{ $previewRow['row_number'] }}</td>
                                        <td>
                                            <div class="font-semibold text-black">{{ $previewRow['branch_name'] }}</div>
                                            @if($previewRow['address'])
                                                <div class="text-xs text-black/60">{{ $previewRow['address'] }}</div>
                                            @endif
                                        </td>
                                        <td>{{ __(Illuminate\Support\Str::headline($previewRow['branch_type'])) }}</td>
                                        <td>{{ $previewRow['district'] }}</td>
                                        <td>{{ $previewRow['region'] }}</td>
                                        <td>{{ __(Illuminate\Support\Str::headline($previewRow['status'])) }}</td>
                                        <td>
                                            <div class="text-xs text-black/65">
                                                <div>{{ $previewRow['phone'] ?: __('No phone') }}</div>
                                                <div>{{ $previewRow['email'] ?: __('No email') }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            <form class="mt-6 form-stack" method="POST" action="{{ route('branches.store') }}">
                @csrf
                <section class="form-section">
                    <div class="form-section-heading">
                        <h2>{{ __('Branch location') }}</h2>
                        <p>{{ __('Choose the correct region and district first so the branch is stored under the right governance scope.') }}</p>
                    </div>

                    <div class="form-grid-responsive">
                        <div>
                            <label class="field-label" for="region_id">{{ __('Region') }}</label>
                            <select class="select-rgc" id="region_id" name="region_id" data-region-select required>
                                <option value="">{{ __('Select region') }}</option>
                                @foreach($regions as $r)
                                    <option value="{{ $r->id }}" @selected(old('region_id') == $r->id)>{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="district_id">{{ __('District') }}</label>
                            <select
                                class="select-rgc"
                                id="district_id"
                                name="district_id"
                                data-district-select
                                data-empty-option-label="{{ __('Select district') }}"
                                data-selected-value="{{ old('district_id') }}"
                                required
                            >
                                <option value="">{{ __('Select district') }}</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <div class="form-section-heading">
                        <h2>{{ __('Branch identity') }}</h2>
                        <p>{{ __('Name the branch clearly and choose the branch type before saving.') }}</p>
                    </div>

                    <div class="form-grid-responsive">
                        <div class="md:col-span-2">
                            <label class="field-label" for="name">{{ __('Branch name') }}</label>
                            <input class="input-rgc" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('Example: Mbagala Central Branch') }}" required>
                        </div>
                        <div>
                            <label class="field-label" for="branch_type">{{ __('Branch type') }}</label>
                            <select class="select-rgc" id="branch_type" name="branch_type" required>
                                @foreach($branchTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('branch_type', 'local') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="status">{{ __('Status') }}</label>
                            <select class="select-rgc" id="status" name="status" required>
                                @foreach($branchStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <div class="form-section-heading">
                        <h2>{{ __('Branch contact details') }}</h2>
                        <p>{{ __('Add branch contact information now so leadership, reporting, and future communications stay organised.') }}</p>
                    </div>

                    <div class="form-grid-responsive">
                        <div class="md:col-span-2">
                            <label class="field-label" for="address">{{ __('Address') }}</label>
                            <input class="input-rgc" id="address" name="address" value="{{ old('address') }}" placeholder="{{ __('Street, ward, district, city') }}">
                        </div>
                        <div>
                            <label class="field-label" for="phone">{{ __('Phone') }}</label>
                            <input class="input-rgc" id="phone" name="phone" value="{{ old('phone') }}" placeholder="{{ __('+255700000000') }}">
                        </div>
                        <div>
                            <label class="field-label" for="email">{{ __('Email') }}</label>
                            <input class="input-rgc" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('branch@rgc.or.tz') }}">
                        </div>
                    </div>
                </section>

                <div class="form-actions">
                    <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Save Branch') }}</button>
                </div>
            </form>
        </div>
    </div>

    <aside class="card-rgc branch-import-panel">
        <span class="section-kicker">{{ __('Bulk Import') }}</span>
        <h2 class="mt-4 text-2xl font-semibold">{{ __('Upload branches from CSV or Excel') }}</h2>
        <p class="mt-3 text-sm leading-7 text-black/68">{{ __('Download the official template first. Every row must include region, district, branch_name, and branch_type. Optional columns are address, phone, email, and status.') }}</p>

        <div class="branch-import-actions mt-5">
            <a class="btn-rgc" href="{{ route('branches.template', 'xlsx') }}">{{ __('Download blank XLSX template') }}</a>
            <a class="btn-rgc-alt" href="{{ route('branches.template', 'csv') }}">{{ __('Download blank CSV template') }}</a>
        </div>

        <div class="branch-import-actions mt-3">
            <a class="btn-rgc-alt w-full" href="{{ route('branches.template.sample', 'xlsx') }}">{{ __('Download filled sample XLSX') }}</a>
            <a class="btn-rgc-alt w-full" href="{{ route('branches.template.sample', 'csv') }}">{{ __('Download filled sample CSV') }}</a>
        </div>

        <div class="branch-template-card mt-5">
            <strong>{{ __('Required columns') }}</strong>
            <div class="branch-template-tags mt-3">
                @foreach($requiredImportColumns as $column)
                    <span>{{ $column }}</span>
                @endforeach
            </div>
            <strong class="mt-4 block">{{ __('Optional columns') }}</strong>
            <div class="branch-template-tags mt-3">
                @foreach($optionalImportColumns as $column)
                    <span>{{ $column }}</span>
                @endforeach
            </div>
        </div>

        <div class="branch-template-card mt-4">
            <strong>{{ __('Accepted values') }}</strong>
            <ul class="branch-import-rules mt-3">
                <li>{{ __('branch_type: headquarters, regional, district, local') }}</li>
                <li>{{ __('status: active or inactive') }}</li>
                <li>{{ __('Region and district names must match Tanzania master data already seeded in the system') }}</li>
                <li>{{ __('Branch names must not duplicate another branch inside the same district') }}</li>
            </ul>
        </div>

        @if($errors->has('branch_import') || $errors->has('branch_file') || session('branch_import_errors'))
            <div class="branch-import-errors mt-5">
                <strong>{{ __('Import needs attention') }}</strong>
                @if($errors->has('branch_import'))
                    <p>{{ $errors->first('branch_import') }}</p>
                @endif
                @if($errors->has('branch_file'))
                    <p>{{ $errors->first('branch_file') }}</p>
                @endif
                @if(session('branch_import_errors'))
                    <ul>
                        @foreach(session('branch_import_errors') as $importError)
                            <li>{{ $importError }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form class="mt-5 branch-import-form" method="POST" action="{{ route('branches.import') }}" enctype="multipart/form-data">
            @csrf
            <label class="field-label" for="branch_file">{{ __('Branch import file') }}</label>
            <div class="branch-upload-box">
                <input class="input-rgc" id="branch_file" type="file" name="branch_file" accept=".csv,.xls,.xlsx" required>
                <p>{{ __('Upload the completed template. The system validates every row before saving anything.') }}</p>
            </div>
            <button class="btn-rgc mt-4 w-full" type="submit">{{ __('Validate and preview import') }}</button>
        </form>
    </aside>
</div>
@endsection
