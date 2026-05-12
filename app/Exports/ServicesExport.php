<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ServicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        $request = request();
        return Service::active()->get();
    }

    public function map($service): array
    {
        return [
            $service->name,
            $service->sessions,
            $service->price
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Sesiones',
            'Precio',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
