<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class IncomesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        return Income::active()->latest('date')->get();
    }

    public function map($income): array
    {
        return [
            $income->description,
            $income->amount,
            optional($income->payment_method)->name,
            $income->date->format('d/m/Y')
        ];
    }

    public function headings(): array
    {
        return [
            'Descripción',
            'Monto',
            'Método de pago',
            'Fecha',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
