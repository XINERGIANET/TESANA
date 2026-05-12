<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;
use App\Models\Expense;
use App\Models\Cost;

class ExpenseController extends Controller
{
    public function index(Request $request){
        $expenses = Expense::active()->when($request->year, function($query, $year){
            return $query->whereYear('date', $year);
        })->when($request->date, function($query, $date){
            return $query->whereDate('date', $date);
        })->paginate(20);
        $costs = Cost::active()->get();
        return view('expenses.index', compact('expenses', 'costs'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'cost_id' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $request->merge(['date' => now()]);

        Expense::create($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Expense $expense){
        return response()->json([
            'id' => $expense->id,
            'cost_id' => $expense->cost_id,
            'description' => $expense->description,
            'amount' => $expense->amount,
            'date' => $expense->date->format('Y-m-d')
        ]);
    }

    public function update(Request $request, Expense $expense){
        $validator = Validator::make($request->all(), [
            'cost_id' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $expense->update($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Expense $expense){
        $expense->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function excel(Request $request){
        return Excel::download(new ExpensesExport, 'ReporteEgresos.xlsx');
    }
}
