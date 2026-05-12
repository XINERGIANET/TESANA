<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ReservationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        $request = request();
        return Reservation::latest('date')->get();
    }

    public function map($reservation): array
    {
        return [
            optional($reservation->client)->name,
            $reservation->reservation_date->format('d/m/Y'),
            $reservation->reservation_time->format('H:i')
        ];
    }

    public function headings(): array
    {
        return [
            'Alumno',
            'Fecha',
            'Hora',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
