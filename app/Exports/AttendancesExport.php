<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class AttendancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        return Attendance::when(request()->document, function($query, $document){
            return $query->whereHas('client', function($query) use ($document){
                return $query->where('document', $document);
            });
        })->when(request()->name, function($query, $name){
            return $query->whereHas('client', function($query) use ($name){
                return $query->where('name', 'like', '%'.$name.'%');
            });
        })->latest('date')->get();
    }

    public function map($attendance): array
    {
        return [
            optional($attendance->client)->name,
            $attendance->date->format('d/m/Y H:i')
        ];
    }

    public function headings(): array
    {
        return [
            'Cliente',
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
