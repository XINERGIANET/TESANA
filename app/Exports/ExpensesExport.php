<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        return Expense::active()->latest('date')->get();
    }

    public function map($expense): array
    {
        return [
            optional($expense->cost)->name,
            $expense->description,
            $expense->amount,
            $expense->date->format('d/m/Y')
        ];
    }

    public function headings(): array
    {
        return [
            'Costo',
            'Descripción',
            'Monto',
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
