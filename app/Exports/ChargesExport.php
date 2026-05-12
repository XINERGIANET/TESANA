<?php

namespace App\Exports;

use App\Models\ClientService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ChargesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $request = request();
        return ClientService::when($request->document, function($query, $document){
            return $query->whereHas('client', function($query) use ($document){
                return $query->where('document', $document);
            });
        })->when($request->name, function($query, $name){
            return $query->whereHas('client', function($query) use ($name){
                return $query->where('name', 'like', '%'.$name.'%');
            });
        })->when($request->status, function($query, $status){
            return $query->where('paid', $status == 'paid' ? 1 : 0);
        })->whereHas('client', function($query){
            return $query->where('deleted', 0);
        })->latest('start_date')->get();
    }

    public function map($client_service): array
    {
        return [
            optional($client_service->client)->document,
            optional($client_service->client)->name,
            optional($client_service->service)->name,
            $client_service->start_date->format('d/m/Y'),
            $client_service->end_date->format('d/m/Y'),
            $client_service->total,
            $client_service->debt,
            $client_service->paid ? 'Si' : 'No'
        ];
    }

    public function headings(): array
    {
        return [
            'DNI',
            'Nombre',
            'Servicio',
            'Fecha inicial',
            'Fecha final',
            'Total',
            'Deuda',
            'Pagado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
