<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservationsExport;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Client;

class ReservationController extends Controller
{
    public function index(Request $request){
        $reservations = Reservation::latest('date')->paginate(20);
        return view('reservations.index', compact('reservations'));
    }

    public function create(){
        $client = session('client') ? session('client') : null;
        return view('reservations.create', compact('client'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
        ]);

        $client = Client::find($request->client_id);

        $validator->after(function($validator) use($client){
            if(!$client){
                $validator->errors()->add('document', 'El cliente ingresado no se encuentra registrado');
            }
        });

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $date = $request->reservation_date;
        $time = $request->reservation_time;
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");

        $start = $datetime->copy()->startOfHour(); // Ejemplo: 09:00:00
        $end = $datetime->copy()->endOfHour();     // Ejemplo: 09:59:59

        $count = Reservation::whereDate('reservation_date', $date)->whereBetween('reservation_time', [$start, $end])->count();
        

        if($count < 9){
            Reservation::create([
                'client_id' => $client->id,
                'date' => now(),
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
            ]);
        }else{
            return response()->json([
                'status' => false,
                'error' => 'Solo se aceptan hasta 9 reservas en una hora'
            ]);
        }

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Reservation $reservation){
        $reservation->delete();

        return response()->json([
            'status' => true
        ]);
    }

    public function search(Request $request){
        $client = session('client') ? session('client') : null;
        $reservations = [];

        if($client){
            $reservations = Reservation::where('client_id', $client->id)->paginate(20);
        }

        return view('reservations.search', compact('reservations'));
    }

    public function excel(Request $request){
        return Excel::download(new ReservationsExport, 'Reservaciones.xlsx');
    }
}
