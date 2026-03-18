<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;

class MapatoMatumiziController extends Controller
{
    // Sadaka data
    private function getSadakaData()
    {
        return collect([
            [
                'id' => 1,
                'tarehe' => '2025-01-05',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1250000,
                'mwezi' => 'JAN'
            ],
            [
                'id' => 2,
                'tarehe' => '2025-01-12',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1180000,
                'mwezi' => 'JAN'
            ],
            [
                'id' => 3,
                'tarehe' => '2025-01-19',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1320000,
                'mwezi' => 'JAN'
            ],
            [
                'id' => 4,
                'tarehe' => '2025-01-26',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1275000,
                'mwezi' => 'JAN'
            ],
            [
                'id' => 5,
                'tarehe' => '2025-02-02',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1410000,
                'mwezi' => 'FEB'
            ],
            [
                'id' => 6,
                'tarehe' => '2025-02-09',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1350000,
                'mwezi' => 'FEB'
            ],
            [
                'id' => 7,
                'tarehe' => '2025-02-16',
                'aina_sadaka' => 'M0001 – Mwaka Mpya',
                'kiasi' => 2850000,
                'mwezi' => 'FEB'
            ],
            [
                'id' => 8,
                'tarehe' => '2025-02-23',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1290000,
                'mwezi' => 'FEB'
            ],
            [
                'id' => 9,
                'tarehe' => '2025-03-02',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1380000,
                'mwezi' => 'MAR'
            ],
            [
                'id' => 10,
                'tarehe' => '2025-03-09',
                'aina_sadaka' => 'M0002 – Shukrani ya Wiki',
                'kiasi' => 1420000,
                'mwezi' => 'MAR'
            ]
        ]);
    }

    // Ahadi data
    private function getAhadiData()
    {
        return collect([
            [
                'id' => 1,
                'jina' => 'John Mrema',
                'namba_simu' => '0755123456',
                'kiasi_ahadi' => 5000000,
                'kiasi_lililolipwa' => 2500000,
                'aina_ahadi' => 'Kiwanja 2025',
                'mwezi' => 'JAN'
            ],
            [
                'id' => 2,
                'jina' => 'Anna Kileo',
                'namba_simu' => '0786456789',
                'kiasi_ahadi' => 3000000,
                'kiasi_lililolipwa' => 3000000,
                'aina_ahadi' => 'Usiku wa RGC',
                'mwezi' => 'JAN'
            ],
            [
                'id' => 3,
                'jina' => 'Robert Mwamba',
                'namba_simu' => '0712345678',
                'kiasi_ahadi' => 10000000,
                'kiasi_lililolipwa' => 4000000,
                'aina_ahadi' => 'Kiwanja 2025',
                'mwezi' => 'FEB'
            ],
            [
                'id' => 4,
                'jina' => 'Sarah Juma',
                'namba_simu' => '0765987654',
                'kiasi_ahadi' => 2000000,
                'kiasi_lililolipwa' => 1500000,
                'aina_ahadi' => 'Mavuno 2025',
                'mwezi' => 'FEB'
            ],
            [
                'id' => 5,
                'jina' => 'David Mushi',
                'namba_simu' => '0744112233',
                'kiasi_ahadi' => 7500000,
                'kiasi_lililolipwa' => 7500000,
                'aina_ahadi' => 'Ujenzi Kanisa',
                'mwezi' => 'MAR'
            ],
            [
                'id' => 6,
                'jina' => 'Grace Edward',
                'namba_simu' => '0733445566',
                'kiasi_ahadi' => 4000000,
                'kiasi_lililolipwa' => 1000000,
                'aina_ahadi' => 'Kiwanja 2025',
                'mwezi' => 'MAR'
            ],
            [
                'id' => 7,
                'jina' => 'Michael Kavishe',
                'namba_simu' => '0722778899',
                'kiasi_ahadi' => 6000000,
                'kiasi_lililolipwa' => 4500000,
                'aina_ahadi' => 'Usiku wa RGC',
                'mwezi' => 'MAR'
            ],
            [
                'id' => 8,
                'jina' => 'Elizabeth Moshi',
                'namba_simu' => '0799887766',
                'kiasi_ahadi' => 2500000,
                'kiasi_lililolipwa' => 2500000,
                'aina_ahadi' => 'Mavuno 2025',
                'mwezi' => 'APR'
            ]
        ]);
    }

    // Matumizi data
    private function getMatumiziData()
    {
        return collect([
            [
                'id' => 1,
                'tarehe' => '2025-01-10',
                'aina_matumizi' => 'Posho',
                'maelezo' => 'Posho ya waimbaji na waandishi wa vitabu',
                'kiasi' => 350000,
                'mwezi' => 'JAN'
            ],
            [
                'id' => 2,
                'tarehe' => '2025-01-15',
                'aina_matumizi' => 'Umeme',
                'maelezo' => 'Bili ya umeme ya kanisa',
                'kiasi' => 125000,
                'mwezi' => 'JAN'
            ],
            [
                'id' => 3,
                'tarehe' => '2025-01-20',
                'aina_matumizi' => 'Ujenzi',
                'maelezo' => 'Kununua mazao ya ujenzi',
                'kiasi' => 850000,
                'mwezi' => 'JAN'
            ],
            [
                'id' => 4,
                'tarehe' => '2025-02-05',
                'aina_matumizi' => 'Maji',
                'maelezo' => 'Bili ya maji',
                'kiasi' => 75000,
                'mwezi' => 'FEB'
            ],
            [
                'id' => 5,
                'tarehe' => '2025-02-12',
                'aina_matumizi' => 'Usafiri',
                'maelezo' => 'Malipo ya usafiri kwa mhubiri',
                'kiasi' => 200000,
                'mwezi' => 'FEB'
            ],
            [
                'id' => 6,
                'tarehe' => '2025-02-25',
                'aina_matumizi' => 'Chakula',
                'maelezo' => 'Chakula kwa mkutano wa baraza',
                'kiasi' => 180000,
                'mwezi' => 'FEB'
            ],
            [
                'id' => 7,
                'tarehe' => '2025-03-08',
                'aina_matumizi' => 'Posho',
                'maelezo' => 'Posho ya waimbaji',
                'kiasi' => 300000,
                'mwezi' => 'MAR'
            ],
            [
                'id' => 8,
                'tarehe' => '2025-03-15',
                'aina_matumizi' => 'Umeme',
                'maelezo' => 'Bili ya umeme',
                'kiasi' => 135000,
                'mwezi' => 'MAR'
            ],
            [
                'id' => 9,
                'tarehe' => '2025-03-22',
                'aina_matumizi' => 'Ujenzi',
                'maelezo' => 'Mishono ya ujenzi',
                'kiasi' => 920000,
                'mwezi' => 'MAR'
            ],
            [
                'id' => 10,
                'tarehe' => '2025-03-29',
                'aina_matumizi' => 'Nyingine',
                'maelezo' => 'Matumizi ya ofisi',
                'kiasi' => 150000,
                'mwezi' => 'MAR'
            ]
        ]);
    }

    // Main index method
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'sadaka');
        $page = $request->get('page', 1);
        $perPage = 8;

        // Get data based on tab
        switch ($tab) {
            case 'ahadi':
                $data = $this->getAhadiData();
                $totalAhadi = $data->sum('kiasi_ahadi');
                $totalMalipo = $data->sum('kiasi_lililolipwa');
                break;
            case 'matumizi':
                $data = $this->getMatumiziData();
                break;
            case 'sadaka':
            default:
                $data = $this->getSadakaData();
                break;
        }

        // Paginate data
        $paginatedData = $data->forPage($page, $perPage);
        $total = $data->count();
        $lastPage = ceil($total / $perPage);

        // Create paginator instance
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Return view with data
        return view('pages.mapato-matumizi', [
            'sadaka' => $tab === 'sadaka' ? $paginator : $this->createEmptyPaginator(),
            'ahadi' => $tab === 'ahadi' ? $paginator : $this->createEmptyPaginator(),
            'matumizi' => $tab === 'matumizi' ? $paginator : $this->createEmptyPaginator(),
            'totalAhadi' => $totalAhadi ?? 0,
            'totalMalipo' => $totalMalipo ?? 0,
            'current_tab' => $tab
        ]);
    }

    // Store methods for forms
    public function storeSadaka(Request $request)
    {
        // Validate sadaka data
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'aina_sadaka' => 'required|string',
            'kiasi' => 'required|numeric|min:0'
        ]);

        // Find income category based on aina_sadaka
        $category = IncomeCategory::where('name', 'LIKE', '%' . $validated['aina_sadaka'] . '%')->first();
        if (!$category) {
            // Default to first active category if not found
            $category = IncomeCategory::active()->first();
        }

        // Create income record
        Income::create([
            'income_category_id' => $category->id,
            'collection_date' => $validated['tarehe'],
            'amount' => $validated['kiasi'],
            'receipt_number' => 'RCP' . date('Ymd') . rand(100, 999),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('mapato.matumizi', ['tab' => 'sadaka'])
            ->with('success', 'Sadaka imehifadhiwa kikamilifu!');
    }

    public function storeAhadi(Request $request)
    {
        // Simulate storing ahadi
        $request->validate([
            'jina' => 'required|string',
            'namba_simu' => 'nullable|string',
            'kiasi_ahadi' => 'required|numeric|min:0',
            'aina_ahadi' => 'required|string'
        ]);

        // In real application, you would save to database here
        
        return redirect()->route('mapato.matumizi', ['tab' => 'ahadi'])
            ->with('success', 'Ahadi imehifadhiwa kikamilifu!');
    }

    public function storeMalipo(Request $request)
    {
        // Simulate storing malipo
        $request->validate([
            'ahadi_id' => 'required',
            'tarehe_malipo' => 'required|date',
            'kiasi_lililolipwa' => 'required|numeric|min:0',
            'njia_malipo' => 'required|string'
        ]);

        // In real application, you would save to database here
        
        return redirect()->route('mapato.matumizi', ['tab' => 'ahadi'])
            ->with('success', 'Malipo yamehifadhiwa kikamilifu!');
    }

    public function storeMatumizi(Request $request)
    {
        // Validate matumizi data
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'aina_matumizi' => 'required|string',
            'maelezo' => 'nullable|string',
            'kiasi' => 'required|numeric|min:0'
        ]);

        // Find expense category based on aina_matumizi
        $category = ExpenseCategory::where('name', 'LIKE', '%' . $validated['aina_matumizi'] . '%')->first();
        if (!$category) {
            // Default to first active category if not found
            $category = ExpenseCategory::active()->first();
        }

        // Get year and month from tarehe
        $date = \Carbon\Carbon::parse($validated['tarehe']);

        // Create expense record
        Expense::create([
            'expense_category_id' => $category->id,
            'year' => $date->year,
            'month' => $date->month,
            'amount' => $validated['kiasi'],
            'notes' => $validated['maelezo'] ?? null,
            'receipt_number' => 'EXP' . date('Ymd') . rand(100, 999),
            'payee' => 'Matumizi Mbalimbali',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('mapato.matumizi', ['tab' => 'matumizi'])
            ->with('success', 'Matumizi yamehifadhiwa kikamilifu!');
    }

    // Export methods
    public function exportMapato()
    {
        // Simulate export functionality
        $data = $this->getSadakaData();
        
        return response()->json([
            'message' => 'Ripoti ya mapato imeandaliwa kwa ajili ya download',
            'records' => $data->count(),
            'total' => $data->sum('kiasi')
        ]);
    }

    public function exportKiwanja()
    {
        // Simulate export functionality
        $data = $this->getAhadiData();
        
        return response()->json([
            'message' => 'Ripoti ya ahadi za kiwanja imeandaliwa kwa ajili ya download',
            'records' => $data->count(),
            'total_ahadi' => $data->sum('kiasi_ahadi'),
            'total_malipo' => $data->sum('kiasi_lililolipwa')
        ]);
    }

    public function exportMatumizi()
    {
        // Simulate export functionality
        $data = $this->getMatumiziData();
        
        return response()->json([
            'message' => 'Ripoti ya matumizi imeandaliwa kwa ajili ya download',
            'records' => $data->count(),
            'total' => $data->sum('kiasi')
        ]);
    }

    // Helper method to create empty paginator
    private function createEmptyPaginator()
    {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            collect([]),
            0,
            8,
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    // Detail view methods
    public function showSadaka($id)
    {
        $sadaka = $this->getSadakaData()->firstWhere('id', $id);
        
        if (!$sadaka) {
            return response()->json(['error' => 'Sadaka haijapatikana'], 404);
        }

        return response()->json([
            'success' => true,
            'sadaka' => $sadaka
        ]);
    }

    public function showAhadi($id)
    {
        $ahadi = $this->getAhadiData()->firstWhere('id', $id);
        
        if (!$ahadi) {
            return response()->json(['error' => 'Ahadi haijapatikana'], 404);
        }

        return response()->json([
            'success' => true,
            'ahadi' => $ahadi
        ]);
    }

    public function showMatumizi($id)
    {
        $matumizi = $this->getMatumiziData()->firstWhere('id', $id);
        
        if (!$matumizi) {
            return response()->json(['error' => 'Matumizi hayajapatikana'], 404);
        }

        return response()->json([
            'success' => true,
            'matumizi' => $matumizi
        ]);
    }
}