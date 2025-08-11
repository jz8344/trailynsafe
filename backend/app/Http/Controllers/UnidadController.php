<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnidadController extends Controller
{
    public function index()
    {
        return response()->json(Unidad::orderByDesc('id')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matricula' => 'required|string|unique:unidades,matricula',
            'modelo' => 'nullable|string',
            'capacidad' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);
        $unidad = Unidad::create($validator->validated());
        return response()->json($unidad, 201);
    }

    public function update(Request $request, $id)
    {
        $unidad = Unidad::find($id);
        if (!$unidad) return response()->json(['error' => 'No encontrada'], 404);
        $validator = Validator::make($request->all(), [
            'matricula' => 'sometimes|string|unique:unidades,matricula,' . $unidad->id,
            'modelo' => 'sometimes|nullable|string',
            'capacidad' => 'sometimes|integer|min:1',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);
        $unidad->update($validator->validated());
        return response()->json($unidad->fresh());
    }

    public function destroy($id)
    {
        $unidad = Unidad::find($id);
        if (!$unidad) return response()->json(['error' => 'No encontrada'], 404);
        $unidad->delete();
        return response()->json(['message' => 'Eliminada']);
    }
}
