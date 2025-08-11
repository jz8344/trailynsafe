<?php

namespace App\Http\Controllers;

use App\Models\Chofer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChoferController extends Controller
{
    public function index()
    {
        return response()->json(Chofer::with('usuario')->orderByDesc('id')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|exists:usuarios,id',
            'licencia' => 'nullable|string',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);
        if (Chofer::where('usuario_id', $request->usuario_id)->exists()) {
            return response()->json(['error' => 'El usuario ya es chofer'], 400);
        }
        $chofer = Chofer::create($validator->validated());
        return response()->json($chofer->load('usuario'), 201);
    }

    public function update(Request $request, $id)
    {
        $chofer = Chofer::find($id);
        if (!$chofer) return response()->json(['error' => 'No encontrado'], 404);
        $validator = Validator::make($request->all(), [
            'licencia' => 'sometimes|nullable|string',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);
        $chofer->update($validator->validated());
        return response()->json($chofer->fresh()->load('usuario'));
    }

    public function destroy($id)
    {
        $chofer = Chofer::find($id);
        if (!$chofer) return response()->json(['error' => 'No encontrado'], 404);
        $chofer->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
