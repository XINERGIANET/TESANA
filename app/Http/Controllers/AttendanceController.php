<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\Client;
use App\Support\ClientAttendances;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::with('client')->when(!$request->old, function ($query) {
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
            ->latest('date')
            ->paginate(20);

        $attendances->getCollection()->transform(function ($attendance) {
            $attendance->client_service = ClientAttendances::resolveService($attendance);

            return $attendance;
        });

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
                ClientAttendances::sync($client);

                if (strtotime($client->end_date) < strtotime($request->date)) {
                    $validator->errors()->add('client_id', 'El cliente ingresado no tiene un servicio activo');
                }

                $usage = ClientAttendances::currentUsage($client);

                if ($usage['limit'] < 1) {
                    $validator->errors()->add('client_id', 'El cliente ingresado no tiene un servicio con sesiones configuradas');
                }

                if ($usage['limit'] > 0 && $usage['remaining'] < 1) {
                    if (!$validator->errors()->has('client_id')) {
                        $validator->errors()->add('client_id', 'El cliente ya alcanzo el limite de asistencias de su servicio (' . $usage['limit'] . ')');
                    }

                    if ($client->sessions > 0) {
                        $client->update(['sessions' => 0]);
                    }
                }

                $attendances = Attendance::where('client_id', $client->id)->whereDate('date', $request->date)->get();

                if ($attendances->count() > 0) {
                    $validator->errors()->add('client_id', 'Solo se puede registrar 1 asistencia por cliente por dia');
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
                Attendance::create([
                    'client_id' => $client->id,
                    'date' => $date,
                    'active' => 1
                ]);

                ClientAttendances::sync($client);
            }
        });

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Attendance $attendance)
    {
        return response()->json([
            'id' => $attendance->id,
            'client_id' => $attendance->client_id,
            'client' => optional($attendance->client)->name,
            'date' => optional($attendance->date)->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'date' => 'required|date'
        ]);

        $client = Client::find($request->client_id);

        $validator->after(function ($validator) use ($request, $client, $attendance) {
            if (!$client) {
                $validator->errors()->add('client_id', 'El cliente no se encuentra registrado');
                return;
            }

            $exists = Attendance::where('client_id', $client->id)
                ->whereDate('date', $request->date)
                ->where('id', '<>', $attendance->id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('client_id', 'Solo se puede registrar 1 asistencia por cliente por dia');
            }

            if ($attendance->client_id != $client->id && !ClientAttendances::hasAvailableSession($client)) {
                $usage = ClientAttendances::currentUsage($client);
                $validator->errors()->add('client_id', 'El cliente ya alcanzo el limite de asistencias de su servicio (' . $usage['limit'] . ')');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        DB::transaction(function () use ($request, $attendance, $client) {
            $previousClient = $attendance->client;
            $time = optional($attendance->date)->format('H:i') ?: now()->format('H:i');

            $attendance->update([
                'client_id' => $client->id,
                'date' => $request->date . ' ' . $time,
            ]);

            if ($previousClient) {
                ClientAttendances::sync($previousClient);
            }

            ClientAttendances::sync($client);
        });

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Attendance $attendance)
    {
        $client = $attendance->client;

        $attendance->delete();

        if ($client) {
            ClientAttendances::sync($client);
        }

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
                    return $query->where('active', 1);
                })
                ->latest('date')
                ->paginate(20);
        }

        return view('attendances.search', compact('attendances'));
    }

    public function excel(Request $request)
    {
        return Excel::download(new AttendancesExport, 'ReporteAsistencias.xlsx');
    }

}
