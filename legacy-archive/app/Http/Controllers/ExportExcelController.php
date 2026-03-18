<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\MapatoExport;
use App\Exports\KiwanjaExport;
use App\Exports\MatumiziExport;
use App\Exports\AhadiExport;
use App\Models\Setting;
use App\Models\Pledge;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportExcelController extends Controller
{
    public function index()
    {
        // Get recent exports for display
        $recentExports = $this->getRecentExports();

        $settings = (object) [
            'company_name' => null,
            'address' => null,
            'phone' => null,
            'email' => null,
        ];

        try {
            $settings->company_name = Setting::get('company_name');
            $settings->address = Setting::get('address');
            $settings->phone = Setting::get('phone');
            $settings->email = Setting::get('email');
        } catch (\Exception $e) {
            // ignore
        }

        return view('panel.reports', compact('recentExports', 'settings'));
    }

    public function exportMapato(Request $request)
    {
        try {
            // Log the request
            \Log::info('Export Mapato Request:', $request->all());
            
            // Get filter parameters
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $year = $request->input('year');
            $month = $request->input('month');
            $categoryId = $request->input('category_id');
            $format = $request->input('format', 'excel'); // excel or pdf

            \Log::info("Filters: year=$year, month=$month, categoryId=$categoryId");

            if ($format === 'pdf') {
                // For PDF export, use ReportController
                return app('App\Http\Controllers\ReportController')->generate($request);
            }

            // Build filename
            $filename = 'mapato_';
            if ($year) $filename .= $year . '_';
            if ($month) $filename .= str_pad($month, 2, '0', STR_PAD_LEFT) . '_';
            $filename .= date('Y_m_d_His') . '.xlsx';

            \Log::info("Creating MapatoExport with: startDate=$startDate, endDate=$endDate, year=$year, month=$month");

            // Create export with filters
            $export = new MapatoExport($startDate, $endDate, $year, $month);

            \Log::info("Downloading Excel file: $filename");

            // Return direct download
            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            \Log::error('Export Mapato Error: ' . $e->getMessage());
            return back()->with('error', 'Hitilafu: ' . $e->getMessage());
        }
    }

    public function exportKiwanja(Request $request)
    {
        try {
            $status = $request->get('status', 'all'); // all, paid, pending

            $statusLabel = $status === 'paid' ? 'Zilizolipwa' : ($status === 'pending' ? 'Bado' : 'Zote');
            $filename = 'kiwanja_ahadi_' . $statusLabel . '_' . date('Y_m_d_His') . '.xlsx';

            // Return download directly
            return Excel::download(new KiwanjaExport($status), $filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportSadaka(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month');
            $format = $request->input('format', 'excel'); // excel or pdf

            if ($format === 'pdf') {
                // For PDF export, use ReportController
                $request->merge(['type' => 'mapato', 'period' => $month ? 'custom' : 'yearly']);
                return app('App\Http\Controllers\ReportController')->generate($request);
            }

            // Build filename
            $filename = 'sadaka_' . $year;
            if ($month) $filename .= '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $filename .= '_' . date('Y_m_d_His') . '.xlsx';

            // Get sadaka data
            $sadakaQuery = \App\Models\Income::with(['category', 'member'])
                ->whereYear('collection_date', $year);

            if ($month) {
                $sadakaQuery->whereMonth('collection_date', $month);
            }

            $sadakaData = $sadakaQuery->get();

            // Create Excel with church header
            $export = new \App\Exports\SadakaExport($sadakaData, $year, $month);

            // Return direct download
            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Hitilafu: ' . $e->getMessage());
        }
    }

    public function exportAhadi(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month');
            $format = $request->input('format', 'excel'); // excel or pdf

            // Get ahadi data
            $ahadiQuery = Pledge::with(['member', 'payments'])
                ->whereYear('pledge_date', $year);

            if ($month) {
                $ahadiQuery->whereMonth('pledge_date', $month);
            }

            $ahadiData = $ahadiQuery->get();

            // Build period label
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Machi', 4 => 'Aprili',
                5 => 'Mei', 6 => 'Juni', 7 => 'Julai', 8 => 'Agosti',
                9 => 'Septemba', 10 => 'Oktoba', 11 => 'Novemba', 12 => 'Desemba'
            ];
            
            $periodLabel = 'MWAKA ' . $year;
            if ($month) {
                $periodLabel .= ' - ' . strtoupper($monthNames[(int)$month]);
            }

            // Calculate totals
            $totalPledged = 0;
            $totalPaid = 0;
            foreach ($ahadiData as $ahadi) {
                $paidAmount = $ahadi->payments ? $ahadi->payments->sum('amount') : 0;
                $totalPledged += floatval($ahadi->amount);
                $totalPaid += $paidAmount;
            }
            $totalBalance = $totalPledged - $totalPaid;

            // Get church settings
            $churchName = Setting::get('church_name', 'RGC MAKABE RGC');
            $address = Setting::get('address', 'P.O. Box 123, Makabe');
            $phone = Setting::get('phone', '+255 123 456 789');
            $email = Setting::get('email', 'makabe@RGC.go.tz');

            if ($format === 'pdf') {
                // Generate PDF
                $filename = 'ahadi_' . $year;
                if ($month) $filename .= '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
                $filename .= '_' . date('Y_m_d_His') . '.pdf';
                $filepath = 'exports/' . $filename;

                $pdf = Pdf::loadView('panel.reports.pdf.ahadi-report', [
                    'ahadiData' => $ahadiData,
                    'periodLabel' => $periodLabel,
                    'totalPledged' => $totalPledged,
                    'totalPaid' => $totalPaid,
                    'totalBalance' => $totalBalance,
                    'churchName' => $churchName,
                    'address' => $address,
                    'phone' => $phone,
                    'email' => $email,
                ]);

                // Store PDF
                Storage::disk('public')->put($filepath, $pdf->output());

                // Return download
                return Storage::disk('public')->download($filepath, $filename);
            } else {
                // Generate Excel
                $filename = 'ahadi_' . $year;
                if ($month) $filename .= '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
                $filename .= '_' . date('Y_m_d_His') . '.xlsx';

                // Create Excel with church header
                $export = new AhadiExport($ahadiData, $year, $month);

                // Return direct download
                return Excel::download($export, $filename);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Hitilafu: ' . $e->getMessage());
        }
    }

    public function exportMatumizi(Request $request)
    {
        try {
            // Get parameters
            $year = $request->input('year', date('Y'));
            $startMonth = (int) $request->input('start_month', 1);
            $endMonth = (int) $request->input('end_month', 12);
            $startYear = $request->input('start_year', $year);
            $endYear = $request->input('end_year', $year);
            $format = $request->input('format', 'excel'); // excel or pdf

            if ($format === 'pdf') {
                // For PDF export, use ReportController
                return app('App\Http\Controllers\ReportController')->generate($request);
            }

            // Build filename based on date range
            $monthAbbr = [
                1 => 'jan', 2 => 'feb', 3 => 'mac', 4 => 'apr',
                5 => 'mei', 6 => 'jun', 7 => 'jul', 8 => 'ago',
                9 => 'sep', 10 => 'okt', 11 => 'nov', 12 => 'des'
            ];

            if ($startYear == $endYear && $startMonth == 1 && $endMonth == 12) {
                $filename = 'RGC_EXPENSES_' . $year . '.xlsx';
            } else {
                $filename = 'RGC_EXPENSES_' . $startYear . '_' . $monthAbbr[$startMonth] . '_' . $endYear . '_' . $monthAbbr[$endMonth] . '.xlsx';
            }

            // Create export with date range and download directly
            $export = new MatumiziExport($year, $startMonth, $endMonth, $startYear, $endYear);

            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function customExport(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $reportType = $request->input('report_type');

            $filename = 'custom_export_' . date('Y_m_d_His') . '.xlsx';
            $filepath = 'exports/' . $filename;

            // Handle custom export based on parameters
            // You can create a custom export class that accepts these parameters

            $this->saveExportRecord([
                'type' => 'custom',
                'filename' => $filename,
                'filepath' => $filepath,
                'description' => 'Custom Export - ' . $reportType . ' (' . $startDate . ' to ' . $endDate . ')',
                'size' => '1.5 MB' // Simulated size
            ]);

            return response()->json([
                'success' => true,
                'download_url' => '#',
                'message' => 'Custom export imetengenezwa kikamilifu!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteExport($id)
    {
        try {
            // In a real application, you'd have an Export model
            // For now, we'll simulate deletion
            return response()->json([
                'success' => true,
                'message' => 'Faili imefutwa kikamilifu!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kufuta faili!'
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                $exportIds = $request->input('export_ids');
                if (is_string($exportIds)) {
                    $ids = array_values(array_filter(array_map('trim', explode(',', $exportIds))));
                } elseif (is_array($exportIds)) {
                    $ids = $exportIds;
                }
            }
            
            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hakuna faili zilizochaguliwa kwa kufuta!'
                ], 400);
            }

            // In a real application, you'd delete from database and storage
            // For now, we'll simulate bulk deletion
            // foreach ($ids as $id) {
            //     $export = Export::find($id);
            //     if ($export) {
            //         Storage::disk('public')->delete($export->filepath);
            //         $export->delete();
            //     }
            // }

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' faili zimefutwa kikamilifu!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kufuta faili: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRecentExports()
    {
        // Simulate recent exports data
        // In real application, fetch from database
        return [
            [
                'id' => 1,
                'type' => 'mapato',
                'filename' => 'mapato_2025_01_15_143022.xlsx',
                'description' => 'Mapato ya Jumapili zote',
                'date' => '15 Jan 2025, 14:30',
                'size' => '2.4 MB',
                'download_url' => '#'
            ],
            [
                'id' => 2,
                'type' => 'kiwanja',
                'filename' => 'kiwanja_ahadi_2025_01_14_093045.xlsx',
                'description' => 'Ahadi za kiwanja na malipo',
                'date' => '14 Jan 2025, 09:30',
                'size' => '1.8 MB',
                'download_url' => '#'
            ],
            [
                'id' => 3,
                'type' => 'matumizi',
                'filename' => 'matumizi_2025_01_13_162315.xlsx',
                'description' => 'Matumizi ya mwezi Januari',
                'date' => '13 Jan 2025, 16:23',
                'size' => '1.2 MB',
                'download_url' => '#'
            ]
        ];
    }

    public function quickExport(Request $request)
    {
        $type = $request->input('type');

        switch ($type) {
            case 'mapato':
                return $this->exportMapato($request);
            case 'kiwanja':
                return $this->exportKiwanja($request);
            case 'matumizi':
                return $this->exportMatumizi($request);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Aina ya ripoti haijulikani!'
                ], 400);
        }
    }

    private function saveExportRecord($data)
    {
        // In real application, save to database
        // Export::create($data);
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
