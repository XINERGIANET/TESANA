<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\BankPayment;

class BankPaymentController extends Controller
{
    public function index(Request $request){
        $bank_payments = BankPayment::active()->paginate(20);
        return view('bank_payments.index', compact('bank_payments'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'bank' => 'required',
            'quotas' => 'required|integer',
            'date' => 'required|date'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        BankPayment::create($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, BankPayment $bank_payment){
        return response()->json([
            'id' => $bank_payment->id,
            'amount' => $bank_payment->amount,
            'bank' => $bank_payment->bank,
            'quotas' => $bank_payment->quotas,
            'date' => $bank_payment->date->format('Y-m-d')
        ]);
    }

    public function update(Request $request, BankPayment $bank_payment){
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'bank' => 'required',
            'quotas' => 'required|integer',
            'date' => 'required|date'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $bank_payment->update($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, BankPayment $bank_payment){
        $bank_payment->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }
}
