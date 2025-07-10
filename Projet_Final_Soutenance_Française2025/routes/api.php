<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AlerteDroppeController;
use App\Http\Controllers\Api\EtudiantController;
use App\Http\Controllers\Api\PresenceController;
use App\Http\Controllers\Api\ClasseController;
use App\Http\Controllers\Api\MatiereController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\JustificationAbsenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// ROUTES D'AUTHENTIFICATION (PUBLIQUES)
// ============================================================================
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentification réussie',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Identifiants invalides'
    ], 401);
});

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Déconnexion réussie'
    ]);
})->middleware('auth:sanctum');

// Route pour récupérer l'utilisateur connecté
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user()
    ]);
});

// Route de santé publique
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API IFRAN - Système de gestion des présences',
        'version' => '1.0.0',
        'status' => 'healthy',
        'timestamp' => now()->toISOString()
    ]);
});

// ============================================================================
// ROUTES PROTÉGÉES PAR AUTHENTIFICATION
// ============================================================================
Route::middleware(['auth:sanctum'])->group(function () {

    // ============================================================================
    // ROUTES POUR LES ALERTES DROPPÉ
    // ============================================================================
    Route::prefix('alertes')->group(function () {
        Route::get('/etudiants/droppes', [AlerteDroppeController::class, 'getEtudiantsDroppes']);
        Route::post('/detection', [AlerteDroppeController::class, 'declencherDetection']);
        Route::post('/notifications', [AlerteDroppeController::class, 'envoyerNotifications']);
    });

    // Routes pour les étudiants
    Route::get('/etudiants/search', [EtudiantController::class, 'search']);
    Route::get('/etudiants/{id}/statistiques', [EtudiantController::class, 'statistiques']);
    Route::get('/etudiants/{id}', [EtudiantController::class, 'show']);
    Route::get('/etudiants', [EtudiantController::class, 'index']);
    Route::post('/etudiants', [EtudiantController::class, 'store']);
    Route::put('/etudiants/{id}', [EtudiantController::class, 'update']);
    Route::delete('/etudiants/{id}', [EtudiantController::class, 'destroy']);

    // ============================================================================
    // ROUTES POUR LES PRÉSENCES
    // ============================================================================
    Route::prefix('presences')->group(function () {
        Route::get('/', [PresenceController::class, 'index']);
        Route::post('/', [PresenceController::class, 'store']);
        Route::get('/{id}', [PresenceController::class, 'show']);
        Route::put('/{id}', [PresenceController::class, 'update']);
        Route::delete('/{id}', [PresenceController::class, 'destroy']);
    });

    // ============================================================================
    // ROUTES POUR LES CLASSES
    // ============================================================================
    Route::prefix('classes')->group(function () {
        Route::get('/', [ClasseController::class, 'index']);
        Route::post('/', [ClasseController::class, 'store']);
        Route::get('/{id}', [ClasseController::class, 'show']);
        Route::put('/{id}', [ClasseController::class, 'update']);
        Route::delete('/{id}', [ClasseController::class, 'destroy']);
    });

    // ============================================================================
    // ROUTES POUR LES MATIÈRES
    // ============================================================================
    Route::prefix('matieres')->group(function () {
        Route::get('/', [MatiereController::class, 'index']);
        Route::post('/', [MatiereController::class, 'store']);
        Route::get('/{id}', [MatiereController::class, 'show']);
        Route::put('/{id}', [MatiereController::class, 'update']);
        Route::delete('/{id}', [MatiereController::class, 'destroy']);
    });

    // ============================================================================
    // ROUTES POUR LES NOTIFICATIONS
    // ============================================================================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::put('/{id}', [NotificationController::class, 'update']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    // ============================================================================
    // ROUTES POUR LES JUSTIFICATIONS D'ABSENCE
    // ============================================================================
    Route::prefix('justifications')->group(function () {
        Route::get('/', [JustificationAbsenceController::class, 'index']);
        Route::post('/', [JustificationAbsenceController::class, 'store']);
        Route::get('/{id}', [JustificationAbsenceController::class, 'show']);
        Route::put('/{id}', [JustificationAbsenceController::class, 'update']);
        Route::delete('/{id}', [JustificationAbsenceController::class, 'destroy']);
    });

    // ============================================================================
    // ROUTES DE STATISTIQUES GLOBALES
    // ============================================================================
    Route::prefix('statistiques')->group(function () {
        Route::get('/globales', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_etudiants' => \App\Models\Etudiant::count(),
                    'total_classes' => \App\Models\Classe::count(),
                    'total_matieres' => \App\Models\Matiere::count(),
                    'total_presences' => \App\Models\Presence::count(),
                    'total_notifications' => \App\Models\Notification::count(),
                ],
                'message' => 'Statistiques globales récupérées avec succès'
            ]);
        });
    });

});
