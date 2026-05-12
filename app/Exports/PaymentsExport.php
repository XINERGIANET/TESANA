<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $request = request();
        return Payment::active()->when($request->document, function($query, $document){
            return $query->whereHas('client_service.client', function($query) use ($document){
                return $query->where('document', $document);
            });
        })->when($request->name, function($query, $name){
            return $query->whereHas('client_service.client', function($query) use ($name){
                return $query->where('name', 'like', '%'.$name.'%');
            });
        })->when($request->start_date, function($query, $start_date){
            return $query->whereDate('date', '>=', $start_date);
        })->when($request->end_date, function($query, $end_date){
            return $query->whereDate('date', '<=', $end_date);
        })->latest('date')->get();
    }

    public function map($payment): array
    {
        return [
            optional(optional($payment->client_service)->client)->name,
            optional(optional($payment->client_service)->service)->name,
            $payment->amount,
            optional($payment->payment_method)->name,
            $payment->date->format('d/m/Y'),
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Servicio',
            'Monto',
            'Método de pago',
            'Fecha de pago',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
