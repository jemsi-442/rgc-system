<?php

namespace App\Exports;

use App\Models\Branch;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BranchRecordsExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(
        private readonly Branch $branch,
        private readonly ?string $dateFrom = null,
        private readonly ?string $dateTo = null,
    ) {
    }

    public function collection(): Collection
    {
        $offerings = $this->branch->offerings()
            ->when($this->dateFrom, fn ($query) => $query->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('date', '<=', $this->dateTo))
            ->orderByDesc('date')
            ->get()
            ->map(fn ($offering) => [
                'sort_date' => optional($offering->date)?->format('Y-m-d') ?? '',
                'record_type' => 'offering',
                'date' => optional($offering->date)?->format('Y-m-d') ?? '',
                'title' => __('Offering'),
                'details' => __('Recorded branch offering'),
                'amount' => (float) $offering->amount,
            ]);

        $expenses = $this->branch->expenses()
            ->when($this->dateFrom, fn ($query) => $query->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('date', '<=', $this->dateTo))
            ->orderByDesc('date')
            ->get()
            ->map(fn ($expense) => [
                'sort_date' => optional($expense->date)?->format('Y-m-d') ?? '',
                'record_type' => 'expense',
                'date' => optional($expense->date)?->format('Y-m-d') ?? '',
                'title' => __('Expense'),
                'details' => $expense->description ?: __('General expense'),
                'amount' => (float) $expense->amount,
            ]);

        $events = $this->branch->events()
            ->when($this->dateFrom, fn ($query) => $query->whereDate('event_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('event_date', '<=', $this->dateTo))
            ->orderByDesc('event_date')
            ->get()
            ->map(fn ($event) => [
                'sort_date' => optional($event->event_date)?->format('Y-m-d H:i:s') ?? '',
                'record_type' => 'event',
                'date' => optional($event->event_date)?->format('Y-m-d H:i') ?? '',
                'title' => $event->title,
                'details' => $event->description ?: __('Scheduled branch event'),
                'amount' => '',
            ]);

        $rows = $offerings
            ->concat($expenses)
            ->concat($events)
            ->sortByDesc('sort_date')
            ->values();

        $offeringTotal = (float) $offerings->sum('amount');
        $expenseTotal = (float) $expenses->sum('amount');
        $netBalance = $offeringTotal - $expenseTotal;

        return $rows
            ->map(fn (array $row) => [
                $row['record_type'],
                $row['date'],
                $row['title'],
                $row['details'],
                $row['amount'] === '' ? '' : number_format((float) $row['amount'], 2, '.', ''),
            ])
            ->concat(collect([
                ['', '', '', '', ''],
                ['summary', '', __('Rows exported'), (string) $rows->count(), ''],
                ['summary', '', __('Offerings total'), 'TZS', number_format($offeringTotal, 2, '.', '')],
                ['summary', '', __('Expenses total'), 'TZS', number_format($expenseTotal, 2, '.', '')],
                ['summary', '', __('Events total'), (string) $events->count(), ''],
                ['summary', '', __('Net balance'), 'TZS', number_format($netBalance, 2, '.', '')],
            ]));
    }

    public function headings(): array
    {
        return [
            'record_type',
            'date',
            'title',
            'details',
            'amount',
        ];
    }
}
