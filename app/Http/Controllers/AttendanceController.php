<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\Client;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::when(!$request->old, function ($query) {
            return $query->where('active', 1);
        })
            ->when($request->document, function ($query, $document) {
                return $query->whereHas('client', function ($query) use ($document) {
                    return $query->where('document', $document);
                });
            })
            ->when($request->name, function ($query, $name) {
                return $query->whereHas('client', function ($query) use ($name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                });
            })
            ->when($request->start_date, function ($query, $start_date) {
                return $query->whereDate('date', '>=', $start_date);
            })
            ->when($request->end_date, function ($query, $end_date) {
                return $query->whereDate('date', '<=', $end_date);
            })
            ->latest('date') // Ordenar por fecha más reciente primero
            ->paginate(20);

        return view('attendances.index', compact('attendances'));
    }

    public function create()
    {
        return view('attendances.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'date' => 'required|date'
        ]);

        $client = Client::find($request->client_id);

        $validator->after(function ($validator) use ($request, $client) {
            if (!$client) {
                $validator->errors()->add('client_id', 'El cliente no se encuentra registrado');
            }

            if ($client) {
                if (strtotime($client->end_date) < strtotime($request->date)) {
                    $validator->errors()->add('client_id', 'El cliente ingresado no tiene un servicio activo');
                }

                if ($client->sessions < 1) {
                    $validator->errors()->add('client_id', 'El cliente ya realizó todas sus sesiones');
                }

                $attendances = Attendance::where('client_id', $client->id)->whereDate('date', $request->date)->get();

                if ($attendances->count() > 0) {
                    $validator->errors()->add('client_id', 'Sólo se puede registrar 1 asistencia por cliente por día');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }



        DB::transaction(function () use ($request, $client) {

            $date = $request->date . ' ' . now()->format('H:i');

            $exists = Attendance::where('client_id', $client->id)->where('date', $date)->first();

            if (!$exists) {

                if ($client) {
                    $client->update([
                        'sessions' => intval($client->sessions) - 1
                    ]);
                }

                Attendance::create([
                    'client_id' => $client->id,
                    'date' => $date
                ]);
            }
        });


        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Attendance $attendance)
    {
        $attendance->delete();

        return response()->json([
            'status' => true
        ]);
    }

    public function search(Request $request)
    {
        $client = session('client') ? session('client') : null;
        $attendances = [];

        if ($client) {
            $attendances = Attendance::where('client_id', $client->id)
                ->when(!$request->old, function ($query) {
                    return $query->where('active', 1); // Solo asistencias activas si no se marca "Asistencias anteriores"
                })
                ->latest('date') // Ordenar por fecha más reciente primero
                ->paginate(20);
        }

        return view('attendances.search', compact('attendances'));
    }

    
    public function excel(Request $request)
    {
        return Excel::download(new AttendancesExport, 'ReporteAsistencias.xlsx');
    }
}
