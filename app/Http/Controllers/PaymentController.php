<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;
use App\Models\Payment;
use App\Models\ClientService;

class PaymentController extends Controller
{
    public function index(Request $request){
        $payments = Payment::active()->when($request->document, function($query, $document){
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
        })->latest('date');
        $total = $payments->sum('amount');
        $payments = $payments->paginate(20);
        return view('payments.index', compact('payments', 'total'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'client_service_id' => 'required',
            'amount' => 'required|numeric',
            'payment_method_id' => 'required'
        ]);

        $client_service = ClientService::find($request->client_service_id);
        
        $validator->after(function($validator) use ($request, $client_service){
            
            if(!$client_service){
                $validator->errors()->add('client_service_id', 'El servicio del cliente no se encuentra');
            }

            if($request->amount > $client_service->debt){
                $validator->errors()->add('amount', 'El mounto a pagar debe ser menor a la deuda');
            }
        });

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }
        
        Payment::create([
            'client_service_id' => $request->client_service_id,
            'amount' => $request->amount,
            'payment_method_id' => $request->payment_method_id,
            'date' => now()
        ]);

        $debt = floatval($client_service->debt) - floatval($request->amount);
        $paid = floatval($client_service->debt) == floatval($request->amount) ? 1 : 0;

        $client_service->update([
            'debt' => $debt,
            'paid' => $paid
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function excel(Request $request){
        return Excel::download(new PaymentsExport, 'Pagos.xlsx');
    }

    // public function edit(Request $request, Cost $cost){
    //     return response()->json($cost);
    // }

    // public function update(Request $request, Cost $cost){
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'type' => 'required'
    //     ]);

    //     if($validator->fails()){
    //         return response()->json([
    //             'status' => false,
    //             'error' => $validator->errors()->first()
    //         ]);
    //     }

    //     $cost->update($request->all());

    //     return response()->json([
    //         'status' => true
    //     ]);
    // }

    public function destroy(Request $request, Payment $payment){

        DB::transaction(function () use ($payment) {

            $payment->update([
                'deleted' => 1
            ]);

            $client_service = $payment->client_service;
            $client_service->update([
                'debt' => $client_service->debt + $payment->amount,
                'paid' => 0
            ]);

        });

        return response()->json([
            'status' => true
        ]);
    }
}
