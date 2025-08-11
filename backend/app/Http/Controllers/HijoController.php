<?php

namespace App\Http\Controllers;

use App\Models\Hijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HijoController extends Controller
{
    public function index()
    {
        return response()->json(Hijo::with('padre')->orderByDesc('id')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'grado' => 'nullable|string',
            'grupo' => 'nullable|string',
            'codigo_qr' => 'required|string|unique:hijos,codigo_qr',
            'padre_id' => 'required|exists:usuarios,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $hijo = Hijo::create($validator->validated());
        return response()->json($hijo->load('padre'), 201);
    }

    public function show($id)
    {
        $hijo = Hijo::with('padre')->find($id);
        if (!$hijo) return response()->json(['error' => 'No encontrado'], 404);
        return response()->json($hijo);
    }

    public function update(Request $request, $id)
    {
        $hijo = Hijo::find($id);
        if (!$hijo) return response()->json(['error' => 'No encontrado'], 404);
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string',
            'grado' => 'sometimes|nullable|string',
            'grupo' => 'sometimes|nullable|string',
            'codigo_qr' => 'sometimes|string|unique:hijos,codigo_qr,' . $hijo->id,
            'padre_id' => 'sometimes|exists:usuarios,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $hijo->update($validator->validated());
        return response()->json($hijo->fresh()->load('padre'));
    }

    public function destroy($id)
    {
        $hijo = Hijo::find($id);
        if (!$hijo) return response()->json(['error' => 'No encontrado'], 404);
        $hijo->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
