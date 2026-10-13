<?php

namespace App\Exports;

use App\Models\Port;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PortExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Port::with('company')->withCount('containers')->orderBy('id')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Port Name',
            'Port Code',
            'Type',
            'Country',
            'Shipping Company',
            'Company Code',
            'Containers Count',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param Port $port
     * @return array
     */
    public function map($port): array
    {
        return [
            $port->id,
            $port->name,
            $port->code,
            $port->type ?? 'Sea',
            $port->country ?? 'N/A',
            $port->company->name ?? 'N/A',
            $port->company->code ?? 'N/A',
            $port->containers_count ?? 0,
            $port->is_active ? 'Active' : 'Inactive',
            $port->created_at ? $port->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }
}