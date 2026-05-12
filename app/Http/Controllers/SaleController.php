<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\PaymentMethod;

class SaleController extends Controller
{
    public function index(Request $request){
        $sales = Sale::active()->when($request->start_date, function($query, $start_date){
            return $query->whereDate('date', '>=', $start_date);
        })->when($request->end_date, function($query, $end_date){
            return $query->whereDate('date', '<=', $end_date);
        })->paginate(20);
        $clients = Client::active()->get();
        $products = Product::active()->get();
        $payment_methods = PaymentMethod::active()->get();
        return view('sales.index', compact('sales', 'clients', 'products', 'payment_methods'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'payment_method_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $product = Product::find($request->product_id);

        $total = floatval($product->price) * intval($request->quantity);

        Sale::create([
            'client_id' => $request->client_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total' => $total,
            'payment_method_id' => $request->payment_method_id,
            'date' => now()
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Sale $sale){
        return response()->json($sale);
    }

    public function update(Request $request, Sale $sale){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'payment_method_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $product = Product::find($request->product_id);

        $total = floatval($product->price) * intval($request->quantity);

        $sale->update([
            'client_id' => $request->client_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total' => $total,
            'payment_method_id' => $request->payment_method_id,
            'date' => now()
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Sale $sale){
        $sale->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }
}
