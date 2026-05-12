<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Exports\SessionsExport;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\ClientData;
use App\Models\Service;
use App\Models\PaymentMethod;
use App\Models\Attendance;

class ClientController extends Controller
{
    public function index(Request $request){

        $clients = Client::active()->when($request->document, function($query, $document){
            return $query->where('document', $document);
        })->when($request->name, function($query, $name){
            return $query->where('name', 'like', '%'.$name.'%');
        })->when($request->year, function($query, $year){
            return $query->whereYear('start_date', $year);
        })->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        })->when($request->sessions, function($query, $sessions){
            return $query->whereHas('service', function($query) use ($sessions){
                return $query->where('sessions', $sessions);
            });
        })->oldest('end_date')->paginate(20);

        $total = ClientService::active()->when($request->document, function($query, $document){
            return $query->whereHas('client', function($query) use ($document){
                return $query->where('document', $document);
            });
        })->when($request->name, function($query, $name){
            return $query->whereHas('client', function($query) use ($name){
                return $query->where('name', 'like', '%'.$name.'%');
            });
        })->when($request->year, function($query, $year){
            return $query->whereYear('start_date', $year);
        })->when($request->month, function($query, $month){
            return $query->whereMonth('start_date', $month);
        })->when($request->sessions, function($query, $sessions){
            return $query->whereHas('service', function($query) use ($sessions){
                return $query->where('sessions', $sessions);
            });
        })->sum('total');

        $services = Service::all();

        return view('clients.index', compact('clients', 'services', 'total'));
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'document' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $client = Client::active()->where('document', $request->document)->first();

        if($client){
            session()->put('client', $client);

            return response()->json([
                'status' => true
            ]);
        }else{
            return response()->json([
                'status' => false,
                'error' => 'Alumno no encontrado'
            ]);
        }
        
    }

    public function logout(){
        session()->forget('client');
        return redirect()->route('index_client');
    }

    public function sessions(Request $request){
        $clients = Client::active()->when($request->document, function($query, $document){
            return $query->where('document', $document);
        })->when($request->name, function($query, $name){
            return $query->where('name', 'like', '%'.$name.'%');
        })->orderBy('sessions')->orderBy('name')->paginate(20);

        return view('clients.sessions', compact('clients'));
    }

    public function sessionsExcel(Request $request){
       return Excel::download(new SessionsExport, 'SesionesPendientes.xlsx');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'name' => 'required',
            'birth_date' => 'nullable|date',
            'sex' => 'required',
            'email' => 'nullable|email',
            'phone' => 'required|integer',
            'emergency_phone' => 'nullable|integer',
            'service_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_date' => 'required|date',
        ]);

        $validator->after(function($validator) use ($request){
            $client = Client::active()->where('document', $request->document)->first();
            
            if($client){
                $validator->errors()->add('document', 'El documento ingresado ya se encuentra registrado');
            }
        });

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $service = Service::find($request->service_id);

        $date = now();

        $client = Client::create([
            'document' => $request->document,
            'name' => $request->name,
            'birth_date' => $request->birth_date,
            'sex' => $request->sex,
            'email' => $request->email,
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
            'service_id' => $request->service_id,
            'date' => $date,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_date' => $request->payment_date,
            'total' => $service->price,
            'sessions' => $service->sessions,
            'password' => $request->document,
        ]);

        ClientService::create([
            'client_id' => $client->id,
            'service_id' => $request->service_id,
            'date' => $date,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_date' => $request->payment_date,
            'total' => $service->price,
            'debt' => $service->price,
            'observation' => $request->observation
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Client $client){
        return response()->json([
            'id' => $client->id,
            'document' => $client->document,
            'name' => $client->name,
            'birth_date' => optional($client->birth_date)->format('Y-m-d'),
            'sex' => $client->sex,
            'email' => $client->email,
            'phone' => $client->phone,
            'emergency_phone' => $client->emergency_phone,
            'service_id' => $client->service_id,
            'start_date' => optional($client->start_date)->format('Y-m-d'),
            'end_date' => optional($client->end_date)->format('Y-m-d'),
            'payment_date' => optional($client->payment_date)->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, Client $client){
        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'name' => 'required',
            'birth_date' => 'nullable|date',
            'sex' => 'required',
            'email' => 'nullable|email',
            'phone' => 'required|integer',
            'emergency_phone' => 'nullable|integer',
            'service_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_date' => 'required|date',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $service = Service::find($request->service_id);

        
        if($service->sessions > $client->service->sessions){
            $sessions = $client->sessions + ($service->sessions - $client->service->sessions);
        }else{
            $sessions = $client->sessions;
        }

        $client->update([
            'document' => $request->document,
            'name' => $request->name,
            'birth_date' => $request->birth_date,
            'sex' => $request->sex,
            'email' => $request->email,
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
            'service_id' => $request->service_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_date' => $request->payment_date,
            'total' => $service->price,
            'sessions' => $sessions,
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Client $client){
        $client->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function api(Request $request)
    {
        // Obtener los IDs del último servicio asignado
        $clientsWithService = DB::table('client_services as c')
            ->select('c.client_id')
            ->whereRaw('c.id = (SELECT MAX(id) FROM client_services WHERE client_id = c.client_id)')
            ->pluck('c.client_id');
        
        // Consulta con validación de total_sessions y total_attendances
        $clients = Client::whereIn('id', $clientsWithService)
            ->where(function($q) use ($request){
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('document', 'like', "%{$request->q}%");
            })
            ->whereNotIn('id', function($query) {
                $query->select('cs.client_id')
                    ->from('client_services as cs')
                    ->join('services as s', 'cs.service_id', '=', 's.id')
                    ->join('attendances as a', 'a.client_id', '=', 'cs.client_id')
                    ->select('cs.client_id')
                    ->groupBy('cs.client_id')
                    ->havingRaw('SUM(s.sessions) <= COUNT(a.id)');  // Filtra clientes donde total_sessions <= total_attendances
            })
            ->get();
    
        return response()->json([
            'items' => $clients
        ]);
    }





    public function renew(Request $request, Client $client){
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_date' => 'required|date',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        DB::transaction(function () use ($request, $client) {

            $service = Service::find($request->service_id);

            $date = now()->format('Y-m-d H:i');

            $client_service = ClientService::where('client_id', $client->id)->where('service_id', $service->id)->where('date', $date)->first();

            if(!$client_service){

                $client->update([
                    'service_id' => $request->service_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'payment_date' => $request->payment_date,
                    'total' => $service->price,
                    // Solo el bono nuevo: no se arrastra el saldo anterior (evita 3+12=15 y “faltan 3” al cerrar el bono).
                    'sessions' => (int) $service->sessions,
                    'services' => intval($client->services) + 1
                ]);

                ClientService::create([
                    'client_id' => $client->id,
                    'service_id' => $request->service_id,
                    'date' => $date,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'payment_date' => $request->payment_date,
                    'total' => $service->price,
                    'debt' => $service->price,
                    'observation' => $request->observation
                ]);

                Attendance::where('client_id', $client->id)->update(['active' => 0]);

            }

        });

        

        return response()->json([
            'status' => true
        ]);
    }

    public function services(Request $request, Client $client){
        $services = ClientService::with('service')->where('client_id', $client->id)->orderBy('start_date')->get();
        $services = $services->map(function($client_service){
            return [
                'service' => optional($client_service->service)->name,
                'total' => $client_service->total,
                'start_date' => optional($client_service->start_date)->format('d/m/Y'),
                'end_date' => optional($client_service->end_date)->format('d/m/Y'),
                'payment_date' => optional($client_service->payment_date)->format('d/m/Y'),

            ];
        });
        return response()->json($services);
    }

    public function excel(Request $request){
        return Excel::download(new ClientsExport, 'Alumnos.xlsx');
    }

    public function data(Request $request, Client $client){
        $data_1 = $client->data()->where('calculo', 'grasa')->latest('date')->get();
        $data_2 = $client->data()->where('calculo', 'icc')->latest('date')->get();
        $data_3 = $client->data()->where('calculo', 'imc')->latest('date')->get();

        $data_1 = $data_1->map(function($data){
            return [
                'id' => $data->id,
                'client_id' => $data->client_id,
                'date' => optional($data->date)->format('d/m/Y H:i'),
                'triceps' => $data->triceps,
                'subescapular' => $data->subescapular,
                'suprailiaco' => $data->suprailiaco,
                'abdominal' => $data->abdominal,
                'muslo' => $data->muslo,
                'pantorrilla' => $data->pantorrilla,
                'calculo' => $data->calc_1(),
                'resultado' => $data->result_1()
            ];
        });

        $data_2 = $data_2->map(function($data){
            return [
                'id' => $data->id,
                'client_id' => $data->client_id,
                'date' => optional($data->date)->format('d/m/Y H:i'),
                'abdominal_2' => $data->abdominal_2,
                'cadera' => $data->cadera,
                'calculo' => $data->calc_2(),
                'resultado' => $data->result_2()
            ];
        });

        $data_3 = $data_3->map(function($data){
            return [
                'id' => $data->id,
                'client_id' => $data->client_id,
                'date' => optional($data->date)->format('d/m/Y H:i'),
                'peso' => $data->peso,
                'talla' => $data->talla,
                'calculo' => $data->calc_3(),
                'resultado' => $data->result_3()
            ];
        });

        return response()->json(['data_1' => $data_1, 'data_2' => $data_2, 'data_3' => $data_3]);
    }

    public function storeData(Request $request, Client $client){

        $validator = Validator::make($request->all(), [
            'calculo' => 'required',
            'triceps' => 'nullable|numeric',
            'subescapular' => 'nullable|numeric',
            'suprailiaco' => 'nullable|numeric',
            'abdominal' => 'nullable|numeric',
            'muslo' => 'nullable|numeric',
            'pantorrilla' => 'nullable|numeric',

            'abdominal_2' => 'nullable|numeric',
            'cadera' => 'nullable|numeric',

            'peso' => 'nullable|numeric',
            'talla' => 'nullable|numeric',
        ]);

        $validator->sometimes(['triceps', 'subescapular', 'suprailiaco', 'abdominal', 'muslo', 'pantorrilla'], 'required', function($input){
            return $input->calculo == 'grasa';
        });

        $validator->sometimes(['abdominal_2', 'cadera'], 'required', function($input){
            return $input->calculo == 'icc';
        });

        $validator->sometimes(['peso', 'talla'], 'required', function($input){
            return $input->calculo == 'imc';
        });

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $date = now()->format('Y-m-d H:i');

        $exists = ClientData::where('client_id', $client->id)->where('date', $date)->first();

        if(!$exists){

            ClientData::create([
                'client_id' => $client->id,
                'calculo' => $request->calculo,
                'date' => $date,
                'triceps' => $request->triceps,
                'subescapular' => $request->subescapular,
                'suprailiaco' => $request->suprailiaco,
                'abdominal' => $request->abdominal,
                'muslo' => $request->muslo,
                'pantorrilla' => $request->pantorrilla,
                'abdominal_2' => $request->abdominal_2,
                'cadera' => $request->cadera,
                'peso' => $request->peso,
                'talla' => $request->talla,
            ]);

        }

        return response()->json(['status' => true]);
    }

    public function reset(Request $request, Client $client){

        $client->update([
            'password' => $client->document
        ]);

        return response()->json(['status' => true]);

    }

    public function destroyData(Request $request, Client $client){
        $client_data = ClientData::findOrFail($request->id);
        $client_data->delete();

        return response()->json([
            'status' => true
        ]);
    }

}
