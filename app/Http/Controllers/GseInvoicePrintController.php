<?php

namespace App\Http\Controllers;

use App\Models\GseInvoiceRecap;
use App\Models\Invoice_gse as GseInvoice;
use App\Traits\Terbilang;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GseInvoicePrintController extends Controller
{
    use Terbilang;

    public function print(GseInvoice $invoice)
    {
        $invoice->load([
            'branch.airport',
            'airline',
            'gseType:id,type_name',
        ]);

        $invoiceRecaps = GseInvoiceRecap::query()
            ->where('gse_invoice_id', $invoice->getKey())
            ->join('gse_recaps', 'gse_recaps.id', '=', 'gse_invoice_recap.gse_recap_id')
            ->leftJoin('gse_types', 'gse_types.id', '=', 'gse_recaps.gse_type_id')
            ->select('gse_invoice_recap.*')
            ->with([
                'recap.gseType:id,type_name',
                'recap.branch:id,name',
                'recap.airline:id,name',
                'recap.equipment:id,registration',
                'recap.gpuDetail:id,gse_recap_id,start_time,end_time',
                'recap.pushbackDetail:id,gse_recap_id,start_ps,end_ps,ata,atd',
            ])
            ->orderByRaw($this->gseServiceOrderSql())
            ->orderBy('gse_types.type_name')
            ->orderBy('gse_recaps.service_date')
            ->orderBy('gse_recaps.flight_number')
            ->orderBy('gse_recaps.er_number')
            ->get();

        $summaryRows = $this->summaryRows($invoiceRecaps);
        $totalQty = (float) $summaryRows->sum('quantity');
        $totalPreTax = (float) $summaryRows->sum('amount');

        return view('print.gse-invoice', [
            'invoice' => $invoice,
            'invoiceRecaps' => $invoiceRecaps,
            'summaryRows' => $summaryRows,
            'totalQty' => $totalQty,
            'totalPreTax' => $totalPreTax,
            'finalTerbilang' => $this->Terbilang($totalPreTax),
            'currentDate' => Carbon::now()->format('d F Y'),
        ]);
    }

    private function summaryRows(Collection $invoiceRecaps): Collection
    {
        return $invoiceRecaps
            ->groupBy(function (GseInvoiceRecap $invoiceRecap): string {
                $service = $invoiceRecap->recap?->gseType?->type_name ?? '-';

                return implode('|', [
                    $this->serviceSortKey($service),
                    $service,
                    $invoiceRecap->charge_type ?: '-',
                    (string) (float) $invoiceRecap->service_rate,
                ]);
            })
            ->map(function (Collection $rows): array {
                /** @var GseInvoiceRecap $first */
                $first = $rows->first();
                $service = $first->recap?->gseType?->type_name ?? '-';
                $chargeType = $first->charge_type ?: '-';
                $serviceRate = (float) $first->service_rate;

                return [
                    'sort' => $this->serviceSortKey($service),
                    'description' => trim($service),
                    'quantity' => (float) $rows->sum(fn (GseInvoiceRecap $row): float => (float) $row->quantity),
                    'service_rate' => $serviceRate,
                    'amount' => (float) $rows->sum(fn (GseInvoiceRecap $row): float => (float) $row->amount),
                ];
            })
            ->sortBy(fn (array $row): string => $row['sort'] . '|' . $row['description'])
            ->values();
    }

    private function serviceSortKey(string $serviceName): int
    {
        $serviceName = strtolower($serviceName);

        return match (true) {
            str_contains($serviceName, 'att') => 0,
            str_contains($serviceName, 'gpu') => 1,
            default => 2,
        };
    }

    private function gseServiceOrderSql(): string
    {
        return "
            CASE
                WHEN LOWER(gse_types.type_name) LIKE '%att%' THEN 0
                WHEN LOWER(gse_types.type_name) LIKE '%gpu%' THEN 1
                ELSE 2
            END
        ";
    }
}
