<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\ClientData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ClientDataExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{


    public function collection()
    {
        $request = request();
        return ClientData::active()->latest('date')->get();
    }

    public function map($data): array
    {
        $result_1 = $data->calculo == 'grasa' ? $data->calc_1().' - '.$data->result_1() : '';
        $result_2 = $data->calculo == 'icc' ? $data->calc_2().' - '.$data->result_2() : '';
        $result_3 = $data->calculo == 'imc' ? $data->calc_3().' - '.$data->result_3() : '';
        return [
            optional($data->client)->name,
            $data->calculo,
            optional($data->date)->format('d/m/Y H:i'),
            $data->triceps,
            $data->subescapular,
            $data->suprailiaco,
            $data->abdominal,
            $data->muslo,
            $data->pantorrilla,
            $result_1,
            $data->abdominal_2,
            $data->cadera,
            $result_2,
            $data->peso,
            $data->talla,
            $result_3,
        ];
    }

    public function headings(): array
    {
        return [
            'CLIENTE',
            'CALCULO',
            'FECHA',
            'TRICIPITAL (TRICEPS)',
            'SUB ESCAPULAR',
            'SUPRA ILIACO',
            'ABDOMINAL',
            'CUADRICIPITAL (MUSLO)',
            'PERONEAL (PANTORRILLA)',
            'RESULTADO',
            'ABDOMINAL',
            'CADERA',
            'RESULTADO',
            'PESO',
            'TALLA',
            'RESULTADO',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
