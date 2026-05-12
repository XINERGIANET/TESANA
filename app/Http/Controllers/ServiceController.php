<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServicesExport;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index(Request $request){
        $services = Service::active()->paginate(20);
        return view('services.index', compact('services'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sessions' => 'required',
            'price' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        Service::create($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Service $service){
        return response()->json($service);
    }

    public function update(Request $request, Service $service){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sessions' => 'required',
            'price' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $service->update($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Service $service){
        $service->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function excel(Request $request){
        return Excel::download(new ServicesExport, 'Servicios.xlsx');
    }
}
