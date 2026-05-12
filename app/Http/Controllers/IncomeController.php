<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomesExport;
use App\Models\Income;
use App\Models\PaymentMethod;

class IncomeController extends Controller
{
    public function index(Request $request){
        $incomes = Income::active()->latest('date')->paginate(20);
        $payment_methods = PaymentMethod::active()->get();
        return view('incomes.index', compact('incomes', 'payment_methods'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'amount' => 'required|numeric',
            'payment_method_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $request->merge(['date' => now()]);

        Income::create($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Income $income){
        return response()->json($income);
    }

    public function update(Request $request, Income $income){
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'amount' => 'required|numeric',
            'payment_method_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $income->update($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Income $income){
        $income->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function excel(Request $request){
        return Excel::download(new IncomesExport, 'ReporteIngresos.xlsx');
    }
}
