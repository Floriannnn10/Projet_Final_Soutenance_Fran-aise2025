<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JustificationAbsence;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur API pour la gestion des justifications d'absence
 */
class JustificationAbsenceController extends Controller
{
    public function index(): JsonResponse
    {
        $justifications = JustificationAbsence::with(['etudiant', 'presence'])->paginate(20);
        return response()->json(['success' => true, 'data' => $justifications]);
    }

    public function show($id): JsonResponse
    {
        $justification = JustificationAbsence::with(['etudiant', 'presence'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $justification]);
    }

    public function store(Request $request): JsonResponse
    {
        $justification = JustificationAbsence::create($request->all());
        return response()->json(['success' => true, 'data' => $justification], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $justification = JustificationAbsence::findOrFail($id);
        $justification->update($request->all());
        return response()->json(['success' => true, 'data' => $justification]);
    }

    public function destroy($id): JsonResponse
    {
        $justification = JustificationAbsence::findOrFail($id);
        $justification->delete();
        return response()->json(['success' => true, 'message' => 'Justification supprimée']);
    }
} 