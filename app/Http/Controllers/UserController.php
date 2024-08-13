<?php

namespace App\Http\Controllers;

use app\Http\Controllers;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
 // Validation des données
 $validateUser = Validator::make($request->all(), [
    'first_name' => 'required|min:3|max:60',
    'last_name' => 'required|min:2|max:60',
    'pseudo' => 'nullable|min:3|max:20',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8',
]);

// Retourner les erreurs de validation si présentes
if ($validateUser->fails()) {
    return response()->json([
        'status' => false,
        'message' => 'Validation error',
        'errors' => $validateUser->errors()
    ], 400);
}

try {
    // Création de l'utilisateur si la validation passe
    $user = User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'pseudo' => $request->pseudo,
        'email' => $request->email,
        'password' => Hash::make($request->password),  // Hash le mot de passe avant de le sauvegarder
    ]);

    // Création du token API
    $token = $user->createToken('API TOKEN')->plainTextToken;

    // Retourner la réponse avec le token
    return response()->json([
        'status' => true,
        'message' => 'Le compte a été créé avec succès',
        'token' => $token
    ], 201);
} catch (\Exception $e) {
    return response()->json([
        'status' => false,
        'message' => 'Erreur lors de la création de l\'utilisateur',
        'error' => $e->getMessage()
    ], 500);
    }
    }

/**
     * Login a user.
     */
    public function login(Request $request)
    {
        try {
            // Validation des données
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            // Retourner les erreurs de validation si présentes
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            // Tentative de connexion
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => "L'email ou le mot de passe ne correspond pas",
                ], 401);
            }

            // Récupération de l'utilisateur authentifié
            $user = User::where('email', $request->email)->first();

            // Création du token
            $token = $user->createToken("API TOKEN")->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Vous êtes connecté',
                'token' => $token,
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile(){
        $userData = Auth::user();
        return response()->json([
            'status' => true,
            'message' => "Page d'information",
            'data' => $userData,
            'id' => Auth::user()->id,
        ], 200);
    }

    public function logout(){
        auth::user()->tokens()->delete();
        return response()->json([
           'status' => true,
           'message' => "Vous êtes déconnecté",
           'data' => [],
        ], 200);
    }
    public function getUsers()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

}
