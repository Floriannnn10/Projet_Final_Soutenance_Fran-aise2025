<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur API pour la gestion des matières
 */
class MatiereController extends Controller
{
    public function index(): JsonResponse
    {
        $matieres = Matiere::all();
        return response()->json(['success' => true, 'data' => $matieres]);
    }

    public function show($id): JsonResponse
    {
        $matiere = Matiere::findOrFail($id);
        return response()->json(['success' => true, 'data' => $matiere]);
    }

    public function store(Request $request): JsonResponse
    {
        $matiere = Matiere::create($request->all());
        return response()->json(['success' => true, 'data' => $matiere], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $matiere = Matiere::findOrFail($id);
        $matiere->update($request->all());
        return response()->json(['success' => true, 'data' => $matiere]);
    }

    public function destroy($id): JsonResponse
    {
        $matiere = Matiere::findOrFail($id);
        $matiere->delete();
        return response()->json(['success' => true, 'message' => 'Matière supprimée']);
    }
} 