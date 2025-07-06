<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur API pour la gestion des classes
 */
class ClasseController extends Controller
{
    public function index(): JsonResponse
    {
        $classes = Classe::all();
        return response()->json(['success' => true, 'data' => $classes]);
    }

    public function show($id): JsonResponse
    {
        $classe = Classe::findOrFail($id);
        return response()->json(['success' => true, 'data' => $classe]);
    }

    public function store(Request $request): JsonResponse
    {
        $classe = Classe::create($request->all());
        return response()->json(['success' => true, 'data' => $classe], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $classe = Classe::findOrFail($id);
        $classe->update($request->all());
        return response()->json(['success' => true, 'data' => $classe]);
    }

    public function destroy($id): JsonResponse
    {
        $classe = Classe::findOrFail($id);
        $classe->delete();
        return response()->json(['success' => true, 'message' => 'Classe supprimée']);
    }
} 