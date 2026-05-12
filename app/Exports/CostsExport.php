<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Cost;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class CostsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        $request = request();
        return Cost::active()->get();
    }

    public function map($cost): array
    {
        return [
            $cost->name,
            $cost->type,
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Tipo'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
