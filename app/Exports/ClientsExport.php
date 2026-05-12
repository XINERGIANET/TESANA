<?php

namespace App\Exports;

use App\Models\ClientService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $request = request();
        return ClientService::with('client') // Cargar la relación con Client
            ->when($request->document, function ($query, $document) {
                return $query->whereHas('client', function ($query) use ($document) {
                    return $query->where('document', $document);
                });
            })
            ->when($request->name, function ($query, $name) {
                return $query->whereHas('client', function ($query) use ($name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                });
            })
            ->when($request->year, function ($query, $year) {
                return $query->whereYear('start_date', $year);
            })
            ->when($request->month, function ($query, $month) {
                return $query->whereMonth('start_date', $month);
            })
            ->when($request->sessions, function ($query, $sessions) {
                return $query->whereHas('service', function ($query) use ($sessions) {
                    return $query->where('sessions', $sessions);
                });
            })
            ->oldest('end_date')
            ->get();
    }

    public function map($client_service): array
    {
        return [
            // Datos de Client
            optional($client_service->client)->document, // Documento del cliente
            optional($client_service->client)->name,     // Nombre del cliente
            optional($client_service->client)->birth_date ? date('Y-m-d', strtotime($client_service->client->birth_date)) : null, // Fecha de nacimiento
            optional($client_service->client)->sex,      // Sexo del cliente
            optional($client_service->client)->email,    // Email del cliente
            optional($client_service->client)->phone,    // Teléfono del cliente
    
            // Datos de ClientService
            optional($client_service->service)->name,    // Nombre del servicio
            number_format($client_service->total, 2),    // Total formateado a 2 decimales
            optional($client_service->start_date)->format('d/m/Y'), // Fecha de inicio formateada
            optional($client_service->end_date)->format('d/m/Y'),   // Fecha de fin formateada
            optional($client_service->payment_date)->format('d/m/Y'), // Fecha de pago formateada
            optional($client_service->client)->profile(), // Perfil del cliente (asumo que es un método)
            $client_service->end_date && strtotime($client_service->end_date) >= strtotime(date('Y-m-d')) ? 'Si' : 'No', // Verificación de fecha de fin
        ];
    }

    public function headings(): array
    {
        return [
            // Encabezados para Client
            'Documento',
            'Nombre',
            'Fecha de Nacimiento',
            'Sexo',
            'Email',
            'Teléfono',

            // Encabezados para ClientService
            'Servicio',
            'Total',
            'Fecha Inicial',
            'Fecha Final',
            'Fecha de Pago',
            'Perfil',
            'Activo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]] // Estilo para la primera fila (encabezados)
        ];
    }
}