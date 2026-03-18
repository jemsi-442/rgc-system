<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Models\Role;
use App\Models\Jumuiya;
use App\Exports\MemberTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends Controller
{
    /**
     * Display a listing of members with search and filters
     */
    public function index(Request $request)
    {
        $query = Member::query();

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('member_number', 'like', '%' . $search . '%');
            });
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by marital status
        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        // Filter by special group
        if ($request->filled('special_group')) {
            $query->where('special_group', $request->special_group);
        }

        // Filter by age group
        if ($request->filled('age_group')) {
            $ageGroup = $request->age_group;
            $query->whereNotNull('date_of_birth');

            switch ($ageGroup) {
                case 'Watoto': // < 18 years
                    $query->where('date_of_birth', '>', Carbon::now()->subYears(18));
                    break;
                case 'Vijana': // 18-34 years
                    $query->whereBetween('date_of_birth', [
                        Carbon::now()->subYears(35),
                        Carbon::now()->subYears(18)
                    ]);
                    break;
                case 'Wazima': // 35-59 years
                    $query->whereBetween('date_of_birth', [
                        Carbon::now()->subYears(60),
                        Carbon::now()->subYears(35)
                    ]);
                    break;
                case 'Wazee': // >= 60 years
                    $query->where('date_of_birth', '<=', Carbon::now()->subYears(60));
                    break;
            }
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        // Filter by pending status (for notification link)
        if ($request->get('status') === 'pending') {
            $query->where('is_active', false);
        }

        // Order by latest
        $members = $query->orderBy('created_at', 'desc')->paginate(7);

        // Get statistics
        $stats = [
            'total' => Member::count(),
            'active' => Member::active()->count(),
            'inactive' => Member::where('is_active', false)->count(),
            'pending' => User::where('is_active', false)->count(),
            'male' => Member::where('gender', 'Mme')->count(),
            'female' => Member::where('gender', 'Mke')->count(),
        ];

        // Get dynamic filter options from database
        $specialGroups = Member::whereNotNull('special_group')
            ->distinct()
            ->pluck('special_group')
            ->filter()
            ->values();

        // Get all distinct genders
        $genders = Member::whereNotNull('gender')
            ->distinct()
            ->pluck('gender')
            ->filter()
            ->values();

        // Get all distinct marital statuses
        $maritalStatuses = Member::whereNotNull('marital_status')
            ->distinct()
            ->pluck('marital_status')
            ->filter()
            ->values();

        // Get all distinct age groups from members with date_of_birth
        $members_for_age = Member::whereNotNull('date_of_birth')->get();
        $ageGroups = $members_for_age->map(function($member) {
            return $member->age_group;
        })->filter()->unique()->values();

        // Handle AJAX request for table only
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('panel.members._table', compact('members'));
        }

        return view('panel.members.index', compact('members', 'stats', 'specialGroups', 'genders', 'maritalStatuses', 'ageGroups'));
    }

    /**
     * Show the form for creating a new member
     */
    public function create()
    {
        // Generate next member number for display - Format: RGC-YYYY-NNNN (4 digits)
        $year = date('Y');
        $lastMember = Member::where('member_number', 'like', "RGC-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastMember) {
            $parts = explode('-', $lastMember->member_number);
            $sequence = isset($parts[3]) ? intval($parts[3]) + 1 : 1;
        }

        $nextMemberNumber = 'RGC-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Get all roles, departments, and jumuiyas for selection
        $roles = \App\Models\Role::all();
        $departments = \App\Models\Department::active()->get();
        $jumuiyas = \App\Models\Jumuiya::with('leader')->where('is_active', true)->orderBy('name')->get();

        return view('panel.members.create', compact('nextMemberNumber', 'roles', 'departments', 'jumuiyas'));
    }

    /**
     * Store a newly created member in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:Mme,Mke',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:members,email',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:50',
            'block_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'neighbor_name' => 'nullable|string|max:100',
            'neighbor_phone' => 'nullable|string|max:20',
            'baptism_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'membership_date' => 'nullable|date',
            'marital_status' => 'nullable|in:Hajaoa/Hajaolewa,Ameoa/Ameolewa,Mjane/Mgane,Talaka',
            'spouse_name' => 'nullable|string|max:100',
            'spouse_phone' => 'nullable|string|max:20',
            'church_elder' => 'nullable|string|max:100',
            'pledge_number' => 'nullable|string|max:50',
            'special_group' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'role_id' => 'nullable|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'jumuiya_id' => 'nullable|exists:jumuiyas,id',
            'create_user_account' => 'nullable|boolean',
        ], [
            'first_name.required' => 'Tafadhali ingiza jina la kwanza',
            'first_name.max' => 'Jina la kwanza ni refu mno',
            'last_name.required' => 'Tafadhali ingiza jina la ukoo',
            'last_name.max' => 'Jina la ukoo ni refu mno',
            'date_of_birth.date' => 'Tarehe ya kuzaliwa si sahihi',
            'date_of_birth.before' => 'Tarehe ya kuzaliwa lazima iwe kabla ya leo',
            'gender.required' => 'Tafadhali chagua jinsia',
            'gender.in' => 'Jinsia si sahihi',
            'phone.required' => 'Tafadhali ingiza nambari ya simu',
            'phone.max' => 'Nambari ya simu ni ndefu mno',
            'email.email' => 'Barua pepe si sahihi',
            'email.unique' => 'Barua pepe tayari imetumika',
            'marital_status.in' => 'Hali ya ndoa si sahihi',
            'role_id.exists' => 'Jukumu liliochaguliwa halipo',
            'department_id.exists' => 'Idara iliyochaguliwa haipo',
            'jumuiya_id.exists' => 'Jumuiya iliyochaguliwa haipo',
        ]);

        // Auto-generate member number - Format: RGC-YYYY-NNNN (4 digits)
        $year = date('Y');
        $lastMember = Member::where('member_number', 'like', "RGC-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastMember) {
            // Extract sequence number from format RGC-YYYY-NNNN
            $parts = explode('-', $lastMember->member_number);
            $sequence = isset($parts[3]) ? intval($parts[3]) + 1 : 1;
        }

        $validated['member_number'] = 'RGC-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Set envelope number same as member number
        $validated['envelope_number'] = $validated['member_number'];

        $validated['is_active'] = true;

        // Create user account if requested or if role/department is assigned
        $user = null;
        if ($request->has('create_user_account') || $request->filled('role_id') || $request->filled('department_id')) {
            // Default to Mwanachama role if no role specified
            $roleId = $request->filled('role_id') ? $request->role_id : \App\Models\Role::where('slug', 'mwanachama')->first()->id;

            $user = \App\Models\User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['member_number'] . '@rgc.org', // Use member number as email
                'password' => \Hash::make($validated['last_name']), // Last name as default password
                'role_id' => $roleId,
                'department_id' => $request->department_id,
                'is_active' => true,
            ]);

            $validated['user_id'] = $user->id;
        }

        $member = Member::create($validated);

        $message = 'Muumini ameongezwa kikamilifu';
        if ($user) {
            $message .= '. Akaunti ya mtumiaji imetengenezwa. Username: ' . $validated['member_number'] . ', Password: ' . $validated['last_name'];
        }

        return redirect()->route('members.index')
            ->with('success', $message);
    }

    /**
     * Display the specified member with their contributions
     */
    public function show($id)
    {
        $member = Member::with(['incomes.category'])->findOrFail($id);

        // Get contribution summary
        $contributionSummary = $member->incomes()
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->with('category')
            ->get();

        $totalContributions = $member->incomes()->sum('amount');

        return view('panel.members.show', compact('member', 'contributionSummary', 'totalContributions'));
    }

    /**
     * Show the form for editing the specified member
     */
    public function edit($id)
    {
        $member = Member::findOrFail($id);

        return view('panel.members.edit', compact('member'));
    }

    /**
     * Update the specified member in storage
     */
    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:Mme,Mke',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:members,email,' . $id,
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:50',
            'block_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'neighbor_name' => 'nullable|string|max:100',
            'neighbor_phone' => 'nullable|string|max:20',
            'baptism_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'membership_date' => 'nullable|date',
            'marital_status' => 'nullable|in:Hajaoa/Hajaolewa,Ameoa/Ameolewa,Mjane/Mgane,Talaka',
            'spouse_name' => 'nullable|string|max:100',
            'spouse_phone' => 'nullable|string|max:20',
            'church_elder' => 'nullable|string|max:100',
            'pledge_number' => 'nullable|string|max:50',
            'special_group' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ], [
            'first_name.required' => 'Tafadhali ingiza jina la kwanza',
            'first_name.max' => 'Jina la kwanza ni refu mno',
            'last_name.required' => 'Tafadhali ingiza jina la ukoo',
            'last_name.max' => 'Jina la ukoo ni refu mno',
            'date_of_birth.date' => 'Tarehe ya kuzaliwa si sahihi',
            'date_of_birth.before' => 'Tarehe ya kuzaliwa lazima iwe kabla ya leo',
            'gender.required' => 'Tafadhali chagua jinsia',
            'gender.in' => 'Jinsia si sahihi',
            'phone.required' => 'Tafadhali ingiza nambari ya simu',
            'phone.max' => 'Nambari ya simu ni ndefu mno',
            'email.email' => 'Barua pepe si sahihi',
            'email.unique' => 'Barua pepe tayari imetumika',
            'marital_status.in' => 'Hali ya ndoa si sahihi',
        ]);

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Taarifa za muumini zimebadilishwa kikamilifu');
    }

    /**
     * Remove the specified member from storage (soft delete)
     */
    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Muumini amefutwa kikamilifu',
            ]);
        }

        return redirect()->route('members.index')
            ->with('success', 'Muumini amefutwa kikamilifu');
    }

    /**
     * Show member's contribution history
     */
    public function contributions($id)
    {
        $member = Member::findOrFail($id);

        $contributions = $member->incomes()
            ->with('category')
            ->orderBy('collection_date', 'desc')
            ->paginate(7);

        $totalContributions = $member->incomes()->sum('amount');

        // Get contributions grouped by year
        $yearlyContributions = $member->incomes()
            ->selectRaw('YEAR(collection_date) as year, SUM(amount) as total')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        return view('panel.members.contributions', compact(
            'member',
            'contributions',
            'totalContributions',
            'yearlyContributions'
        ));
    }

    /**
     * Show bulk import form
     */
    public function importForm()
    {
        return view('panel.members.import');
    }

    /**
     * Process bulk import of members
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Tafadhali chagua faili la kuingiza',
            'file.mimes' => 'Faili lazima liwe aina ya Excel au CSV',
            'file.max' => 'Ukubwa wa faili usizidi 10MB',
        ]);

        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file)[0];

            // Skip first 6 header rows (church info, title, instructions, etc.)
            // Data starts from row 7 (index 6) in the Excel template
            $data = array_slice($data, 6);

            $imported = 0;
            $errors = [];
            $year = date('Y');

            // Get default Mwanachama role for user accounts
            $defaultRole = Role::where('slug', 'mwanachama')->first();

            foreach ($data as $index => $row) {
                $rowNumber = $index + 7; // +7 because we skipped 6 header rows and Excel rows start at 1

                // Skip empty rows or rows that look like notes/instructions
                if (empty($row[0]) && empty($row[2])) {
                    continue;
                }

                // Skip if first cell contains instruction text (like the sample note row)
                $firstCell = trim($row[0] ?? '');
                if (strpos($firstCell, '↑') !== false || strpos($firstCell, 'Mfano') !== false) {
                    continue;
                }

                // Look up jumuiya by name
                $jumuiyaId = null;
                $jumuiyaName = $row[13] ?? null;
                if ($jumuiyaName) {
                    $jumuiya = Jumuiya::where('name', 'like', '%' . trim($jumuiyaName) . '%')->first();
                    $jumuiyaId = $jumuiya ? $jumuiya->id : null;
                }

                // Map Excel columns to database fields (new order matching template)
                $memberData = [
                    'first_name' => trim($row[0] ?? ''),
                    'middle_name' => trim($row[1] ?? ''),
                    'last_name' => trim($row[2] ?? ''),
                    'date_of_birth' => $row[3] ?? null,
                    'gender' => trim($row[4] ?? ''),
                    'id_number' => trim($row[5] ?? ''),
                    'phone' => trim($row[6] ?? ''),
                    'email' => trim($row[7] ?? '') ?: null,
                    'address' => trim($row[8] ?? ''),
                    'house_number' => trim($row[9] ?? ''),
                    'block_number' => trim($row[10] ?? ''),
                    'city' => trim($row[11] ?? ''),
                    'region' => trim($row[12] ?? ''),
                    'jumuiya_id' => $jumuiyaId,
                    'baptism_date' => $row[14] ?? null,
                    'confirmation_date' => $row[15] ?? null,
                    'marital_status' => trim($row[16] ?? ''),
                    'special_group' => trim($row[17] ?? ''),
                    'occupation' => trim($row[18] ?? ''),
                    'church_elder' => trim($row[19] ?? ''),
                    'spouse_name' => trim($row[20] ?? ''),
                    'spouse_phone' => trim($row[21] ?? ''),
                    'neighbor_name' => trim($row[22] ?? ''),
                    'neighbor_phone' => trim($row[23] ?? ''),
                ];

                // Validate row data
                $validator = Validator::make($memberData, [
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'date_of_birth' => 'required|date',
                    'gender' => 'required|in:Mme,Mke',
                    'phone' => 'required|string|max:20',
                    'email' => 'nullable|email|max:255|unique:members,email',
                    'marital_status' => 'required|in:Hajaoa/Hajaolewa,Ameoa/Ameolewa,Mjane/Mgane,Talaka',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Mstari {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Auto-generate member number - Format: RGC-YYYY-NNNN (4 digits)
                $lastMember = Member::where('member_number', 'like', "RGC-{$year}-%")
                    ->orderBy('id', 'desc')
                    ->first();

                $sequence = 1;
                if ($lastMember) {
                    // Extract sequence number from format RGC-YYYY-NNNN
                    $parts = explode('-', $lastMember->member_number);
                    $sequence = isset($parts[3]) ? intval($parts[3]) + 1 : 1;
                }

                $memberNumber = 'RGC-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                $memberData['member_number'] = $memberNumber;

                // Set envelope number same as member number
                $memberData['envelope_number'] = $memberNumber;

                $memberData['is_active'] = true;

                // Create User account with password = last name
                $user = User::create([
                    'name' => trim($memberData['first_name'] . ' ' . $memberData['last_name']),
                    'email' => $memberNumber . '@rgc.org',
                    'password' => Hash::make($memberData['last_name']),
                    'role_id' => $defaultRole ? $defaultRole->id : null,
                    'is_active' => true,
                ]);

                $memberData['user_id'] = $user->id;

                Member::create($memberData);
                $imported++;
            }

            if ($imported > 0) {
                $message = "Wanachama {$imported} wameongezwa kikamilifu. Kila muumini ana akaunti ya mtumiaji - Username: Namba ya Muumini, Password: Jina la Ukoo";
                if (count($errors) > 0) {
                    $message .= ". Kuna makosa " . count($errors) . " katika baadhi ya mistari.";
                }
                return redirect()->route('members.index')
                    ->with('success', $message)
                    ->with('import_errors', $errors);
            } else {
                return redirect()->back()
                    ->with('error', 'Hakuna muumini aliyeongezwa. Angalia makosa.')
                    ->with('import_errors', $errors);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Kuna tatizo katika kusoma faili: ' . $e->getMessage());
        }
    }

    /**
     * Download sample import template (Excel format with green headers)
     */
    public function downloadTemplate()
    {
        return Excel::download(new MemberTemplateExport, 'template_waumini_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Generate QR code for a member
     */
    public function generateQrCode($id)
    {
        $member = Member::findOrFail($id);

        // Generate URL that will be encoded in QR code
        $url = route('members.scan.show', ['member_number' => $member->member_number]);

        // Generate QR code as SVG
        $qrCode = QrCode::size(200)
            ->margin(2)
            ->generate($url);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Show QR scanner page
     */
    public function scanner()
    {
        return view('panel.members.scanner');
    }

    /**
     * Get member info by scanning QR code (via member number)
     */
    public function getMemberByNumber($memberNumber)
    {
        $member = Member::where('member_number', $memberNumber)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Muumini hajapatikana'
            ], 404);
        }

        // Get contribution summary
        $totalContributions = $member->incomes()->sum('amount');
        $contributionSummary = $member->incomes()
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->with('category')
            ->get();

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'member_number' => $member->member_number,
                'full_name' => $member->full_name,
                'gender' => $member->gender,
                'age' => $member->age,
                'age_group' => $member->age_group,
                'phone' => $member->phone,
                'email' => $member->email,
                'address' => $member->address,
                'city' => $member->city,
                'region' => $member->region,
                'marital_status' => $member->marital_status,
                'special_group' => $member->special_group,
                'membership_date' => $member->membership_date?->format('d/m/Y'),
                'baptism_date' => $member->baptism_date?->format('d/m/Y'),
                'confirmation_date' => $member->confirmation_date?->format('d/m/Y'),
                'is_active' => $member->is_active,
                'total_contributions' => number_format($totalContributions, 2),
                'contribution_summary' => $contributionSummary
            ]
        ]);
    }

    /**
     * Activate a member account
     */
    public function activate($id)
    {
        $member = Member::findOrFail($id);
        $member->update(['is_active' => true]);

        // Also activate associated user if exists
        if ($member->user) {
            $member->user->update(['is_active' => true]);
        }

        $message = 'Muumini ' . $member->full_name . ' ameanzishwa kikamilifu';

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Deactivate a member account
     */
    public function deactivate($id)
    {
        $member = Member::findOrFail($id);
        $member->update(['is_active' => false]);

        // Also deactivate associated user if exists
        if ($member->user) {
            $member->user->update(['is_active' => false]);
        }

        $message = 'Muumini ' . $member->full_name . ' amesimamishwa';

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Bulk activate members
     */
    public function bulkActivate(Request $request)
    {
        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
            'action' => 'required|in:activate,deactivate',
        ]);

        $isActive = $validated['action'] === 'activate';

        $members = Member::whereIn('id', $validated['member_ids'])->get();

        foreach ($members as $member) {
            $member->update(['is_active' => $isActive]);

            // Also update associated user
            if ($member->user) {
                $member->user->update(['is_active' => $isActive]);
            }
        }

        $action = $isActive ? 'wameanzishwa' : 'wamesimamishwa';
        return redirect()->back()
            ->with('success', 'Wanachama ' . count($validated['member_ids']) . ' ' . $action . ' kikamilifu');
    }

    /**
     * Search members for AJAX
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');

        $members = Member::where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('member_number', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            })
            ->where('is_active', true)
            ->limit(20)
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'member_number', 'phone']);

        return response()->json($members->map(function($member) {
            return [
                'id' => $member->id,
                'text' => $member->full_name . ' (' . $member->member_number . ')',
                'full_name' => $member->full_name,
                'member_number' => $member->member_number,
                'phone' => $member->phone,
            ];
        }));
    }
}