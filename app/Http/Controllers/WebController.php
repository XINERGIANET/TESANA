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

        $filteredClients = Client::active()->with('service')->whereYear('start_date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        })->orderBy('start_date')->get();
        
        $filteredSales = Sale::active()->with(['product', 'client'])->whereYear('date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('date', $month);
        })->orderBy('date')->get();

        $filteredIncomes = Income::active()->with('payment_method')->whereYear('date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('date', $month);
        })->orderBy('date')->get();

        $filteredExpenses = Expense::active()->with('cost')->whereYear('date', $year)->when($request->month, function($query, $month){
            return $query->whereMonth('date', $month);
        })->orderBy('date')->get();

        $serviceSales = $filteredClients->sum('total');
        $productSales = $filteredSales->sum('total');
        $incomes = $filteredIncomes->sum('amount');
        
        $totalIncomes = $serviceSales + $productSales + $incomes;

        $expenses = $filteredExpenses->sum('amount');

        $totalExpenses = $expenses;

        $clients = $filteredClients->count();

        $newClientsCollection = $filteredClients->where('services', 1)->values();
        $new_clients = $newClientsCollection->count();

        $recurrentClientsCollection = $filteredClients->filter(function($client){
            return intval($client->services) > 1;
        })->values();
        $recurrent_clients = $recurrentClientsCollection->count();

        $clients_active_current_month = Client::active()
        ->whereMonth('start_date', now()->month)
        ->whereYear('start_date', now()->year)
        ->get();

        $clients1SessionCollection = $filteredClients->filter(function($client){
            return optional($client->service)->sessions == 1;
        })->values();
        $clients_1_session = $clients1SessionCollection->count();

        $clients8SessionCollection = $filteredClients->filter(function($client){
            return optional($client->service)->sessions == 8;
        })->values();
        $clients_8_sessions = $clients8SessionCollection->count();

        $clients12SessionCollection = $filteredClients->filter(function($client){
            return optional($client->service)->sessions == 12;
        })->values();
        $clients_12_sessions = $clients12SessionCollection->count();

        $total_1_session = $clients1SessionCollection->sum('total');
        $total_8_sessions = $clients8SessionCollection->sum('total');
        $total_12_sessions = $clients12SessionCollection->sum('total');

        $formatDate = function($date){
            return $date ? Carbon::parse($date)->format('d/m/Y') : '-';
        };

        $formatMoney = function($amount){
            return 'S/' . number_format((float) $amount, 2);
        };

        $mapClientRows = function($collection) use ($formatDate, $formatMoney){
            return $collection->map(function($client) use ($formatDate, $formatMoney){
                return [
                    'Alumno' => $client->name,
                    'Servicio' => optional($client->service)->name ?? '-',
                    'Inicio' => $formatDate($client->start_date),
                    'Monto' => $formatMoney($client->total),
                ];
            })->values()->all();
        };

        $incomeRows = collect();

        foreach($filteredClients as $client){
            $incomeRows->push([
                'Tipo' => 'Alumno',
                'Detalle' => $client->name . ' - ' . (optional($client->service)->name ?? 'Servicio'),
                'Fecha' => $formatDate($client->start_date),
                'Monto' => $formatMoney($client->total),
            ]);
        }

        foreach($filteredSales as $sale){
            $incomeRows->push([
                'Tipo' => 'Producto',
                'Detalle' => optional($sale->product)->name ?? ('Venta #' . $sale->id),
                'Fecha' => $formatDate($sale->date),
                'Monto' => $formatMoney($sale->total),
            ]);
        }

        foreach($filteredIncomes as $income){
            $incomeRows->push([
                'Tipo' => 'Ingreso adicional',
                'Detalle' => $income->description ?: 'Sin descripción',
                'Fecha' => $formatDate($income->date),
                'Monto' => $formatMoney($income->amount),
            ]);
        }

        $expenseRows = $filteredExpenses->map(function($expense) use ($formatDate, $formatMoney){
            return [
                'Costo' => optional($expense->cost)->name ?? 'Sin categoría',
                'Detalle' => $expense->description ?: 'Sin descripción',
                'Fecha' => $formatDate($expense->date),
                'Monto' => $formatMoney($expense->amount),
            ];
        })->values()->all();

        $rentability = ($totalExpenses == 0 || $totalIncomes == 0) ? 0 : number_format($totalExpenses / $totalIncomes, 2);

        $dashboardDetails = [
            'clients' => [
                'title' => 'Detalle de alumnos',
                'summary' => [
                    ['label' => 'Total de alumnos', 'value' => (string) $clients],
                ],
                'headers' => ['Alumno', 'Servicio', 'Inicio', 'Monto'],
                'rows' => $mapClientRows($filteredClients),
                'empty' => 'No hay alumnos para el filtro seleccionado.',
            ],
            'incomes' => [
                'title' => 'Detalle de ingresos',
                'summary' => [
                    ['label' => 'Alumnos', 'value' => $formatMoney($serviceSales)],
                    ['label' => 'Productos', 'value' => $formatMoney($productSales)],
                    ['label' => 'Otros ingresos', 'value' => $formatMoney($incomes)],
                    ['label' => 'Total', 'value' => $formatMoney($totalIncomes)],
                ],
                'headers' => ['Tipo', 'Detalle', 'Fecha', 'Monto'],
                'rows' => $incomeRows->values()->all(),
                'empty' => 'No hay ingresos para el filtro seleccionado.',
            ],
            'expenses' => [
                'title' => 'Detalle de egresos',
                'summary' => [
                    ['label' => 'Total de egresos', 'value' => $formatMoney($totalExpenses)],
                ],
                'headers' => ['Costo', 'Detalle', 'Fecha', 'Monto'],
                'rows' => $expenseRows,
                'empty' => 'No hay egresos para el filtro seleccionado.',
            ],
            'rentability' => [
                'title' => 'Detalle de rentabilidad',
                'summary' => [
                    ['label' => 'Ingresos', 'value' => $formatMoney($totalIncomes)],
                    ['label' => 'Egresos', 'value' => $formatMoney($totalExpenses)],
                    ['label' => '% Rentabilidad', 'value' => $rentability . ' %'],
                ],
                'headers' => ['Concepto', 'Valor'],
                'rows' => [
                    ['Concepto' => 'Fórmula usada', 'Valor' => 'Egresos / Ingresos'],
                    ['Concepto' => 'Resultado actual', 'Valor' => $rentability . ' %'],
                ],
                'empty' => 'No hay datos para calcular la rentabilidad.',
            ],
            'cash' => [
                'title' => 'Detalle de caja',
                'summary' => [
                    ['label' => 'Ingresos', 'value' => $formatMoney($totalIncomes)],
                    ['label' => 'Egresos', 'value' => $formatMoney($totalExpenses)],
                    ['label' => 'Caja', 'value' => $formatMoney($totalIncomes - $totalExpenses)],
                ],
                'headers' => ['Concepto', 'Monto'],
                'rows' => [
                    ['Concepto' => 'Total ingresos', 'Monto' => $formatMoney($totalIncomes)],
                    ['Concepto' => 'Total egresos', 'Monto' => $formatMoney($totalExpenses)],
                    ['Concepto' => 'Caja final', 'Monto' => $formatMoney($totalIncomes - $totalExpenses)],
                ],
                'empty' => 'No hay movimientos para el filtro seleccionado.',
            ],
            'new_clients' => [
                'title' => 'Detalle de alumnos nuevos',
                'summary' => [
                    ['label' => 'Alumnos nuevos', 'value' => (string) $new_clients],
                ],
                'headers' => ['Alumno', 'Servicio', 'Inicio', 'Monto'],
                'rows' => $mapClientRows($newClientsCollection),
                'empty' => 'No hay alumnos nuevos para el filtro seleccionado.',
            ],
            'recurrent_clients' => [
                'title' => 'Detalle de alumnos recurrentes',
                'summary' => [
                    ['label' => 'Alumnos recurrentes', 'value' => (string) $recurrent_clients],
                ],
                'headers' => ['Alumno', 'Servicio', 'Inicio', 'Monto'],
                'rows' => $mapClientRows($recurrentClientsCollection),
                'empty' => 'No hay alumnos recurrentes para el filtro seleccionado.',
            ],
            'sessions_1' => [
                'title' => 'Detalle de 1 sesión',
                'summary' => [
                    ['label' => 'Cantidad', 'value' => (string) $clients_1_session],
                    ['label' => 'Total', 'value' => $formatMoney($total_1_session)],
                ],
                'headers' => ['Alumno', 'Servicio', 'Inicio', 'Monto'],
                'rows' => $mapClientRows($clients1SessionCollection),
                'empty' => 'No hay alumnos de 1 sesión para el filtro seleccionado.',
            ],
            'sessions_8' => [
                'title' => 'Detalle de 8 sesiones',
                'summary' => [
                    ['label' => 'Cantidad', 'value' => (string) $clients_8_sessions],
                    ['label' => 'Total', 'value' => $formatMoney($total_8_sessions)],
                ],
                'headers' => ['Alumno', 'Servicio', 'Inicio', 'Monto'],
                'rows' => $mapClientRows($clients8SessionCollection),
                'empty' => 'No hay alumnos de 8 sesiones para el filtro seleccionado.',
            ],
            'sessions_12' => [
                'title' => 'Detalle de 12 sesiones',
                'summary' => [
                    ['label' => 'Cantidad', 'value' => (string) $clients_12_sessions],
                    ['label' => 'Total', 'value' => $formatMoney($total_12_sessions)],
                ],
                'headers' => ['Alumno', 'Servicio', 'Inicio', 'Monto'],
                'rows' => $mapClientRows($clients12SessionCollection),
                'empty' => 'No hay alumnos de 12 sesiones para el filtro seleccionado.',
            ],
        ];
        
        $tomorrow = Carbon::tomorrow();
        $tomorrow_month = $tomorrow->month;
        $tomorrow_day = $tomorrow->day;
        $birthdays = Client::whereMonth('birth_date', $tomorrow_month)->whereDay('birth_date', $tomorrow_day)->get();

        return view('index', compact('services', 'expired', 'year', 'serviceSales', 'productSales', 'incomes', 'totalIncomes', 'totalExpenses', 'clients', 'new_clients', 'recurrent_clients', 'clients_1_session', 'clients_8_sessions', 'clients_12_sessions', 'total_1_session', 'total_8_sessions', 'total_12_sessions', 'birthdays', 'clients_active_current_month', 'dashboardDetails'));
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
