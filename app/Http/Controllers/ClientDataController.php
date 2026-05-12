<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientDataExport;
use App\Models\ClientData;

class ClientDataController extends Controller
{
    public function excel(Request $request){
        return Excel::download(new ClientDataExport, 'DatosAntropometricos.xlsx');
    }
}
