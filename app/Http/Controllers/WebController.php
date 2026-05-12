<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
use App\Models\Income;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Payment;

class WebController extends Controller
{
    public function index(Request $request){

        if(auth()->user()->isRole('client')){
            return redirect()->route('index_client');
        }

        $services = Service::all();

        $expired = Client::active()
        ->whereRaw("DATEDIFF(end_date, '".now()->format('Y-m-d')."') >= 0")
        ->whereRaw("DATEDIFF(end_date, '".now()->format('Y-m-d')."') <= 5")->count();

        $year = request()->year ? request()->year : now()->format('Y');

        $serviceSales = ClientService::active()->whereYear('start_date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        })->sum('total');
        
        $productSales = Sale::active()->whereYear('date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('date', $month);
        })->sum('total');

        $incomes = Income::active()->whereYear('date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('date', $month);
        })->sum('amount');
        
        $totalIncomes = $serviceSales + $productSales + $incomes;

        $expenses = Expense::active()->whereYear('date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('date', $month);
        })->sum('amount');

        $totalExpenses = $expenses;

        $clients = Client::active()->whereYear('start_date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        })->count();

        $new_clients = Client::active()->whereYear('start_date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        })->where('services', '=', 1)->count();

        $recurrent_clients = Client::active()->whereYear('start_date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        })->where('services', '>', 1)->count();

        $clients_session = Client::active()->whereYear('start_date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        });

        $clients_active_current_month = Client::active()
        ->whereMonth('start_date', now()->month)
        ->whereYear('start_date', now()->year)
        ->get();

        $clients_1_session = (clone $clients_session)->whereHas('service', function($query){
            return $query->where('sessions', 1);
        })->count();

        $clients_8_sessions = (clone $clients_session)->whereHas('service', function($query){
            return $query->where('sessions', 8);
        })->count();

        $clients_12_sessions = (clone $clients_session)->whereHas('service', function($query){
            return $query->where('sessions', 12);
        })->count();

        $total_session = ClientService::active()->whereYear('start_date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        });

        $total_1_session = (clone $total_session)->whereHas('service', function($query){
            return $query->where('sessions', 1);
        })->sum('total');

        $total_8_sessions = (clone $total_session)->whereHas('service', function($query){
            return $query->where('sessions', 8);
        })->sum('total');

        $total_12_sessions = (clone $total_session)->whereHas('service', function($query){
            return $query->where('sessions', 12);
        })->sum('total');


        $tomorrow = Carbon::tomorrow();
        $tomorrow_month = $tomorrow->month;
        $tomorrow_day = $tomorrow->day;

        $birthdays = Client::whereMonth('birth_date', $tomorrow_month)->whereDay('birth_date', $tomorrow_day)->get();
        
        return view('index', compact('services', 'expired', 'year', 'serviceSales', 'productSales', 'incomes', 'totalIncomes', 'totalExpenses', 'clients', 'new_clients', 'recurrent_clients', 'clients_1_session', 'clients_8_sessions', 'clients_12_sessions', 'total_1_session', 'total_8_sessions', 'total_12_sessions', 'birthdays', 'clients_active_current_month'));
    }

    public function index_client(){
        $client = session('client') ? session('client') : null;
        $client_services = [];
        $data_1 = [];
        $data_2 = [];
        $data_3 = [];
        

        if($client){
            $client_services = ClientService::with('service')->where('client_id', $client->id)->latest('start_date')->get();

            $data_1 = $client->data()->where('calculo', 'grasa')->latest('date')->get();
            $data_2 = $client->data()->where('calculo', 'icc')->latest('date')->get();
            $data_3 = $client->data()->where('calculo', 'imc')->latest('date')->get();
        }

        return view('index_client', compact('client', 'client_services', 'data_1', 'data_2', 'data_3'));
    }

    public function cashFlow(){
        $year = request()->year ? request()->year : now()->format('Y');
        $totals = [
            'sales' => [0,0,0,0,0,0,0,0,0,0,0,0,0],
            'incomes' => [0,0,0,0,0,0,0,0,0,0,0,0,0],
            'expenses' => [0,0,0,0,0,0,0,0,0,0,0,0,0]
        ];
        return view('cash_flow', compact('year', 'totals'));
    }

    public function clientesVigentes(Request $request)
    {
        // Definir mes y año según filtro o fecha actual
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        // Obtener todos los clientes activos (no eliminados) y vigentes (fecha actual entre start y end)
        $clientes_vigentes = Client::active()
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->whereDate('end_date', '>=', now()->toDateString()) // aún vigentes
            ->with(['service']) // para traer datos del servicio
            ->get();

        return view('index', compact('clientes_vigentes', 'month', 'year'));
    }
}
