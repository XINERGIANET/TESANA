<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ChargesExport;
use App\Models\ClientService;
use App\Models\PaymentMethod;

class ChargeController extends Controller
{
    public function index(Request $request){
        $client_services = ClientService::when($request->document, function($query, $document){
            return $query->whereHas('client', function($query) use ($document){
                return $query->where('document', $document);
            });
        })->when($request->name, function($query, $name){
            return $query->whereHas('client', function($query) use ($name){
                return $query->where('name', 'like', '%'.$name.'%');
            });
        })->when($request->status, function($query, $status){
            return $query->where('paid', $status == 'paid' ? 1 : 0);
        })->whereHas('client', function($query){
            return $query->where('deleted', 0);
        })->when($request->payment_date, function($query, $payment_date){
            return $query->whereDate('payment_date', $payment_date);
        })->latest('start_date');

        $total = $client_services->sum('debt');

        $client_services = $client_services->paginate(20);

        $payment_methods = PaymentMethod::active()->get();
        return view('charges.index', compact('client_services', 'payment_methods', 'total'));
    }

    public function excel(Request $request){
        return Excel::download(new ChargesExport, 'Cobranzas.xlsx');
    }
}
