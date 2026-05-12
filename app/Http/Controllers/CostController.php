<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CostsExport;
use App\Models\Cost;

class CostController extends Controller
{
    public function index(Request $request){
        $costs = Cost::active()->paginate(20);
        return view('costs.index', compact('costs'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        Cost::create($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Cost $cost){
        return response()->json($cost);
    }

    public function update(Request $request, Cost $cost){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $cost->update($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Cost $cost){
        $cost->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function excel(Request $request){
        return Excel::download(new CostsExport, 'Costos.xlsx');
    }
}
