<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Trainer;

class TrainerController extends Controller
{
    public function index(Request $request){
        $trainers = Trainer::active()->paginate(20);
        return view('trainers.index', compact('trainers'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'name' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        Trainer::create($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function edit(Request $request, Trainer $trainer){
        return response()->json($trainer);
    }

    public function update(Request $request, Trainer $trainer){
        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'name' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        $trainer->update($request->all());

        return response()->json([
            'status' => true
        ]);
    }

    public function destroy(Request $request, Trainer $trainer){
        $trainer->update([
            'deleted' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function api(Request $request){
        $trainers = Trainer::where('name', 'like', "%{$request->q}%")
            ->orWhere('document', 'like', "%{$request->q}%")
            ->get();
            
        return response()->json([
            'items' => $trainers
        ]);
    }

}
