<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SessionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        $request = request();
        return Client::active()->when($request->document, function($query, $document){
            return $query->where('document', $document);
        })->when($request->name, function($query, $name){
            return $query->where('name', 'like', '%'.$name.'%');
        })->where('sessions', '>', 0)->orderBy('sessions')->orderBy('name')->get();
    }

    public function map($client): array
    {
        return [
            $client->document,
            $client->name,
            optional($client->service)->name,
            $client->sessions
        ];
    }

    public function headings(): array
    {
        return [
            'DNI',
            'Nombre',
            'Servicio',
            'Sesiones pendientes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
