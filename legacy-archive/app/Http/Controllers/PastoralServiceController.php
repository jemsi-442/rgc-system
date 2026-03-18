<?php

namespace App\Http\Controllers;

use App\Models\PastoralService;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PastoralServiceController extends Controller
{
    /**
     * Display a listing of pastoral services
     * Members see only their own services, Admin/Pastor see all
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = PastoralService::with(['member', 'approver']);

        // If user is a regular member, show only their services
        if ($user->isMwanachama() && $user->member) {
            $query->where('member_id', $user->member->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('preferred_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('preferred_date', '<=', $request->end_date);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(7);

        // Get statistics
        $stats = [
            'total' => PastoralService::count(),
            'pending' => PastoralService::pending()->count(),
            'approved' => PastoralService::approved()->count(),
            'rejected' => PastoralService::rejected()->count(),
            'completed' => PastoralService::completed()->count(),
        ];

        // If member, get only their stats
        if ($user->isMwanachama() && $user->member) {
            $stats = [
                'total' => PastoralService::where('member_id', $user->member->id)->count(),
                'pending' => PastoralService::where('member_id', $user->member->id)->pending()->count(),
                'approved' => PastoralService::where('member_id', $user->member->id)->approved()->count(),
                'rejected' => PastoralService::where('member_id', $user->member->id)->rejected()->count(),
                'completed' => PastoralService::where('member_id', $user->member->id)->completed()->count(),
            ];
        }

        $serviceTypes = [
            'Ubatizo',
            'Uthibitisho',
            'Ndoa',
            'Wakfu',
            'Mazishi',
            'Ushauri wa Kichungaji',
            'Nyingine'
        ];

        return view('panel.pastoral-services.index', compact('services', 'stats', 'serviceTypes'));
    }

    /**
     * Show the form for creating a new service request
     */
    public function create()
    {
        $user = Auth::user();

        // Get user's member record or all members for admin
        if ($user->isMwanachama() && $user->member) {
            $members = collect([$user->member]);
        } else {
            $members = Member::where('is_active', true)
                           ->orderBy('first_name')
                           ->get();
        }

        $serviceTypes = [
            'Ubatizo' => 'Ubatizo (Baptism)',
            'Uthibitisho' => 'Uthibitisho (Confirmation)',
            'Ndoa' => 'Ndoa (Marriage)',
            'Wakfu' => 'Wakfu (Dedication)',
            'Mazishi' => 'Mazishi (Funeral)',
            'Ushauri wa Kichungaji' => 'Ushauri wa Kichungaji (Pastoral Counseling)',
            'Nyingine' => 'Nyingine (Other)'
        ];

        return view('panel.pastoral-services.create', compact('members', 'serviceTypes'));
    }

    /**
     * Store a newly created service request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'service_type' => 'required|in:Ubatizo,Uthibitisho,Ndoa,Wakfu,Mazishi,Ushauri wa Kichungaji,Nyingine',
            'preferred_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:2000',
        ], [
            'member_id.required' => 'Tafadhali chagua muumini',
            'member_id.exists' => 'Muumini hapatikani',
            'service_type.required' => 'Tafadhali chagua aina ya huduma',
            'service_type.in' => 'Aina ya huduma si sahihi',
            'preferred_date.date' => 'Tarehe si sahihi',
            'preferred_date.after' => 'Tarehe lazima iwe kesho au baadaye',
            'description.max' => 'Maelezo ni marefu mno',
        ]);

        $validated['status'] = 'Inasubiri';
        $validated['created_by'] = Auth::id();

        PastoralService::create($validated);

        return redirect()->route('pastoral-services.index')
            ->with('success', 'Ombi la huduma limewasilishwa kikamilifu');
    }

    /**
     * Display the specified service
     */
    public function show($id)
    {
        $service = PastoralService::with(['member', 'approver', 'creator'])->findOrFail($id);

        // Check if member can view this service
        $user = Auth::user();
        if ($user->isMwanachama() && $user->member && $service->member_id != $user->member->id) {
            abort(403, 'Huna ruhusa ya kuangalia ombi hili');
        }

        return view('panel.pastoral-services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service
     * Only pending services can be edited
     */
    public function edit($id)
    {
        $service = PastoralService::findOrFail($id);
        $user = Auth::user();

        // Check permissions
        if ($user->isMwanachama() && $user->member && $service->member_id != $user->member->id) {
            abort(403, 'Huna ruhusa ya kuhariri ombi hili');
        }

        // Only pending services can be edited
        if ($service->status != 'Inasubiri') {
            return redirect()->route('pastoral-services.show', $service->id)
                ->with('error', 'Ombi hili haliwezi kuhariribwa');
        }

        if ($user->isMwanachama() && $user->member) {
            $members = collect([$user->member]);
        } else {
            $members = Member::where('is_active', true)->orderBy('first_name')->get();
        }

        $serviceTypes = [
            'Ubatizo' => 'Ubatizo (Baptism)',
            'Uthibitisho' => 'Uthibitisho (Confirmation)',
            'Ndoa' => 'Ndoa (Marriage)',
            'Wakfu' => 'Wakfu (Dedication)',
            'Mazishi' => 'Mazishi (Funeral)',
            'Ushauri wa Kichungaji' => 'Ushauri wa Kichungaji (Pastoral Counseling)',
            'Nyingine' => 'Nyingine (Other)'
        ];

        return view('panel.pastoral-services.edit', compact('service', 'members', 'serviceTypes'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, $id)
    {
        $service = PastoralService::findOrFail($id);
        $user = Auth::user();

        // Check permissions
        if ($user->isMwanachama() && $user->member && $service->member_id != $user->member->id) {
            abort(403, 'Huna ruhusa ya kuhariri ombi hili');
        }

        // Only pending services can be updated
        if ($service->status != 'Inasubiri') {
            return redirect()->route('pastoral-services.show', $service->id)
                ->with('error', 'Ombi hili haliwezi kuhariribwa');
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'service_type' => 'required|in:Ubatizo,Uthibitisho,Ndoa,Wakfu,Mazishi,Ushauri wa Kichungaji,Nyingine',
            'preferred_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:2000',
        ], [
            'member_id.required' => 'Tafadhali chagua muumini',
            'member_id.exists' => 'Muumini hapatikani',
            'service_type.required' => 'Tafadhali chagua aina ya huduma',
            'service_type.in' => 'Aina ya huduma si sahihi',
            'preferred_date.date' => 'Tarehe si sahihi',
            'preferred_date.after' => 'Tarehe lazima iwe kesho au baadaye',
            'description.max' => 'Maelezo ni marefu mno',
        ]);

        $validated['updated_by'] = Auth::id();
        $service->update($validated);

        return redirect()->route('pastoral-services.show', $service->id)
            ->with('success', 'Ombi limeharibiwa kikamilifu');
    }

    /**
     * Approve a service request (Admin/Pastor only)
     */
    public function approve(Request $request, $id)
    {
        $service = PastoralService::findOrFail($id);

        if ($service->status != 'Inasubiri') {
            return redirect()->back()->with('error', 'Ombi hili tayari limeshughulikiwa');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $service->update([
            'status' => 'Imeidhinishwa',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('pastoral-services.show', $service->id)
            ->with('success', 'Ombi limeidhinishwa kikamilifu');
    }

    /**
     * Reject a service request (Admin/Pastor only)
     */
    public function reject(Request $request, $id)
    {
        $service = PastoralService::findOrFail($id);

        if ($service->status != 'Inasubiri') {
            return redirect()->back()->with('error', 'Ombi hili tayari limeshughulikiwa');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ], [
            'admin_notes.required' => 'Tafadhali ingiza sababu ya kukataa',
        ]);

        $service->update([
            'status' => 'Imekataliwa',
            'admin_notes' => $validated['admin_notes'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('pastoral-services.show', $service->id)
            ->with('success', 'Ombi limekataliwa');
    }

    /**
     * Mark service as completed (Admin/Pastor only)
     */
    public function complete($id)
    {
        $service = PastoralService::findOrFail($id);

        if ($service->status != 'Imeidhinishwa') {
            return redirect()->back()->with('error', 'Ombi hili halijaidhinishwa');
        }

        $service->update([
            'status' => 'Imekamilika',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('pastoral-services.show', $service->id)
            ->with('success', 'Huduma imekamilika kikamilifu');
    }

    /**
     * Remove the specified service
     */
    public function destroy($id)
    {
        $service = PastoralService::findOrFail($id);
        $user = Auth::user();

        // Check permissions - only creator can delete pending services
        if ($user->isMwanachama() && $user->member && $service->member_id != $user->member->id) {
            abort(403, 'Huna ruhusa ya kufuta ombi hili');
        }

        // Only pending services can be deleted
        if ($service->status != 'Inasubiri') {
            return redirect()->back()->with('error', 'Ombi hili haliwezi kufutwa');
        }

        $service->delete();

        return redirect()->route('pastoral-services.index')
            ->with('success', 'Ombi limefutwa kikamilifu');
    }

    /**
     * Show report page with statistics
     */
    public function report(Request $request)
    {
        $user = Auth::user();

        // Only admin/pastor can view reports
        if ($user->isMwanachama()) {
            abort(403, 'Huna ruhusa ya kuangalia ripoti');
        }

        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Get date ranges
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        // Weekly statistics
        $weeklyStats = [
            'total' => PastoralService::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'completed' => PastoralService::whereBetween('created_at', [$startOfWeek, $endOfWeek])->where('status', 'Imekamilika')->count(),
            'pending' => PastoralService::whereBetween('created_at', [$startOfWeek, $endOfWeek])->where('status', 'Inasubiri')->count(),
            'approved' => PastoralService::whereBetween('created_at', [$startOfWeek, $endOfWeek])->where('status', 'Imeidhinishwa')->count(),
        ];

        // Monthly statistics
        $monthlyStats = [
            'total' => PastoralService::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'completed' => PastoralService::whereBetween('created_at', [$startOfMonth, $endOfMonth])->where('status', 'Imekamilika')->count(),
            'pending' => PastoralService::whereBetween('created_at', [$startOfMonth, $endOfMonth])->where('status', 'Inasubiri')->count(),
            'approved' => PastoralService::whereBetween('created_at', [$startOfMonth, $endOfMonth])->where('status', 'Imeidhinishwa')->count(),
        ];

        // Yearly statistics
        $yearlyStats = [
            'total' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->count(),
            'completed' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->where('status', 'Imekamilika')->count(),
            'pending' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->where('status', 'Inasubiri')->count(),
            'approved' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->where('status', 'Imeidhinishwa')->count(),
        ];

        // Services by type for the year
        $servicesByType = PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->select('service_type', DB::raw('count(*) as total'))
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get();

        // Monthly breakdown for the year
        $monthlyBreakdown = PastoralService::whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN status = "Imekamilika" THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Fill in missing months
        $monthlyData = [];
        $swahiliMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Machi', 4 => 'Aprili',
            5 => 'Mei', 6 => 'Juni', 7 => 'Julai', 8 => 'Agosti',
            9 => 'Septemba', 10 => 'Oktoba', 11 => 'Novemba', 12 => 'Desemba'
        ];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month' => $i,
                'name' => $swahiliMonths[$i],
                'total' => $monthlyBreakdown->get($i)?->total ?? 0,
                'completed' => $monthlyBreakdown->get($i)?->completed ?? 0,
            ];
        }

        // Recent completed services
        $recentCompleted = PastoralService::with('member')
            ->where('status', 'Imekamilika')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $serviceTypes = [
            'Ubatizo', 'Uthibitisho', 'Ndoa', 'Wakfu',
            'Mazishi', 'Ushauri wa Kichungaji', 'Nyingine'
        ];

        return view('panel.pastoral-services.report', compact(
            'weeklyStats', 'monthlyStats', 'yearlyStats',
            'servicesByType', 'monthlyData', 'recentCompleted',
            'serviceTypes', 'year', 'month', 'swahiliMonths'
        ));
    }

    /**
     * Export pastoral services to PDF
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        // Only admin/pastor can export
        if ($user->isMwanachama()) {
            abort(403, 'Huna ruhusa ya ku-export');
        }

        $period = $request->get('period', 'month');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $swahiliMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Machi', 4 => 'Aprili',
            5 => 'Mei', 6 => 'Juni', 7 => 'Julai', 8 => 'Agosti',
            9 => 'Septemba', 10 => 'Oktoba', 11 => 'Novemba', 12 => 'Desemba'
        ];

        $query = PastoralService::with('member');

        // Filter by period
        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                $periodLabel = 'Wiki hii (' . now()->startOfWeek()->format('d/m') . ' - ' . now()->endOfWeek()->format('d/m/Y') . ')';
                break;
            case 'month':
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
                $periodLabel = $swahiliMonths[(int)$month] . ' ' . $year;
                break;
            case 'year':
                $query->whereYear('created_at', $year);
                $periodLabel = 'Mwaka ' . $year;
                break;
            default:
                $periodLabel = 'Zote';
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type if provided
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        $services = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total' => $services->count(),
            'completed' => $services->where('status', 'Imekamilika')->count(),
            'approved' => $services->where('status', 'Imeidhinishwa')->count(),
            'pending' => $services->where('status', 'Inasubiri')->count(),
            'rejected' => $services->where('status', 'Imekataliwa')->count(),
        ];

        // Group services by type for summary
        $servicesByType = $services->groupBy('service_type')->map(function ($items) {
            return $items->count();
        });

        // Get yearly statistics (for all periods - always show yearly summary)
        $currentYear = date('Y');
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $yearlyStats = [
            'total' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->count(),
            'completed' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->where('status', 'Imekamilika')->count(),
            'approved' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->where('status', 'Imeidhinishwa')->count(),
            'pending' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->where('status', 'Inasubiri')->count(),
            'rejected' => PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])->where('status', 'Imekataliwa')->count(),
        ];

        // Yearly services by type
        $yearlyServicesByType = PastoralService::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->select('service_type', DB::raw('count(*) as total'))
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get();

        // Get church settings
        $churchName = \App\Models\Setting::get('church_name', 'KANISA LA KIINJILI LA KILUTHERI TANZANIA');
        $diocese = \App\Models\Setting::get('diocese', 'DAYOSISI YA MASHARIKI NA PWANI');
        $parish = \App\Models\Setting::get('parish', 'USHARIKA WA MAKABE');

        // Generate filename
        $filename = 'ripoti_huduma_kichungaji_' . $period . '_' . date('Y-m-d_His') . '.pdf';

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('panel.pastoral-services.pdf-report', [
            'services' => $services,
            'periodLabel' => $periodLabel,
            'period' => $period,
            'stats' => $stats,
            'servicesByType' => $servicesByType,
            'yearlyStats' => $yearlyStats,
            'yearlyServicesByType' => $yearlyServicesByType,
            'currentYear' => $currentYear,
            'churchName' => $churchName,
            'diocese' => $diocese,
            'parish' => $parish,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'generatedBy' => $user->name,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Export pastoral services to PDF with AJAX support
     */
    public function exportPDF(Request $request)
    {
        $user = Auth::user();

        // Only admin/pastor can export
        if ($user->isMwanachama()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'Huna ruhusa ya ku-export'], 403);
            }
            abort(403, 'Huna ruhusa ya ku-export');
        }

        $period = $request->get('period', 'month');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $swahiliMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Machi', 4 => 'Aprili',
            5 => 'Mei', 6 => 'Juni', 7 => 'Julai', 8 => 'Agosti',
            9 => 'Septemba', 10 => 'Oktoba', 11 => 'Novemba', 12 => 'Desemba'
        ];

        $query = PastoralService::with('member');

        // Filter by period
        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                $periodLabel = 'Wiki ya ' . now()->format('d/m/Y');
                break;
            case 'month':
                $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
                $periodLabel = $swahiliMonths[intval($month)] . ' ' . $year;
                break;
            case 'year':
                $query->whereYear('created_at', $year);
                $periodLabel = 'Mwaka ' . $year;
                break;
            default:
                $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
                $periodLabel = $swahiliMonths[intval($month)] . ' ' . $year;
                break;
        }

        $services = $query->orderBy('created_at', 'desc')->get();
        $servicesByType = $services->groupBy('service_type');

        // Generate yearly statistics
        $yearlyServices = PastoralService::whereYear('created_at', $year)->get();
        $yearlyServicesByType = $yearlyServices->groupBy('service_type');
        $yearlyStats = [
            'total' => $yearlyServices->count(),
            'pending' => $yearlyServices->where('status', 'Inasubiri')->count(),
            'approved' => $yearlyServices->where('status', 'Imeidhinishwa')->count(),
            'completed' => $yearlyServices->where('status', 'Imekamilika')->count(),
            'rejected' => $yearlyServices->where('status', 'Imekataliwa')->count(),
        ];

        // Get church settings
        $churchName = Setting::get('church_name', 'RGC MAKABE RGC');
        $diocese = Setting::get('diocese', 'RGC DAYOSI YA KINONDONI');
        $parish = Setting::get('parish', 'JUMUIYA YA MAKABE');

        $filename = 'huduma_za_kichungaji_' . $period . '_' . $year . '.pdf';

        $pdf = \PDF::loadView('panel.pastoral-services.pdf-report', [
            'services' => $services,
            'servicesByType' => $servicesByType,
            'periodLabel' => $periodLabel,
            'yearlyStats' => $yearlyStats,
            'yearlyServicesByType' => $yearlyServicesByType,
            'currentYear' => $year,
            'churchName' => $churchName,
            'diocese' => $diocese,
            'parish' => $parish,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'generatedBy' => $user->name,
        ]);

        $pdf->setPaper('A4', 'landscape');

        if ($request->ajax() || $request->expectsJson()) {
            // Store PDF and return download URL for AJAX
            $storedPath = 'exports/pastoral-services/' . $filename;
            \Storage::disk('public')->put($storedPath, $pdf->output());
            
            return response()->json([
                'success' => true,
                'message' => 'Ripoti ya PDF imetengenezwa kikamilifu',
                'download_url' => route('reports.download', ['filename' => $filename]),
                'filename' => $filename
            ]);
        }

        return $pdf->download($filename);
    }
}
