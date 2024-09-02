<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer les posts avec les informations de l'utilisateur
        $posts = Post::where('is_deleted', false)
            ->with('user') // Ajout de la relation avec l'utilisateur
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Ajouter l'URL de l'image à chaque post
        foreach ($posts as $post) {
            $post->image_url = $post->image ? asset('storage/images/' . $post->image) : null;
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Liste des posts',
            'data' => $posts,
        ], 200);
    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
    
        try {
            // Validation des données du formulaire
            $request->validate([
                'text' => 'required|string',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validation pour les images
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        }
    
        // Définir le répertoire de stockage
        $directory = 'public/images';
    
        // Vérifier si le répertoire existe, sinon le créer
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
    
        // Initialisation du chemin de l'image
        $imagePath = null;
    
        // Gestion du téléchargement et du stockage de l'image
        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('images', 'public'); // Stockage de l'image
                $imagePath = str_replace('public/', '', $imagePath); // Supprimer le préfixe 'public/' pour l'URL
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur lors du stockage de l\'image.',
                ], 500);
            }
        }
    
        // Création du post avec les données validées
        $post = Post::create([
            'text' => $request->text,
            'image' => $imagePath, // Enregistrez le chemin de l'image dans la base de données
            'user_id' => $userId,
        ]);
    
        // Ajout de l'URL de l'image dans la réponse
        $post->image_url = $post->image ? asset('storage/images/' . $post->image) : null;
    
        // Retour de la réponse JSON
        return response()->json([
            'status' => true,
            'message' => 'Post created successfully.',
            'data' => $post,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);

        if ($post->is_deleted) {
            abort(404);
        }

        $post->image_url = $post->image ? asset('storage/images/' . $post->image) : null;

        return response()->json([
            'status' => true,
            'message' => 'Post details',
            'data' => $post,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'text' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validation pour les images
            'updated_message' => 'nullable|string',
        ]);

        $post = Post::findOrFail($id);

        $imagePath = $post->image;
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($post->image) {
                Storage::delete('public/images/' . $post->image);
            }

            $imagePath = $request->file('image')->store('public/images'); // Stockage de la nouvelle image
            $imagePath = str_replace('public/', '', $imagePath); // Supprimer le préfixe 'public/' pour l'URL
        }

        $post->update([
            'text' => $request->text,
            'image' => $imagePath,
            'updated_message' => $request->updated_message,
            'modified_at' => now(),
        ]);

        $post->image_url = $post->image ? asset('storage/images/' . $post->image) : null;

        return response()->json([
            'status' => true,
            'message' => 'Post updated successfully.',
            'data' => $post,
        ], 200);
    }

    /**
     * Soft delete a post.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['is_deleted' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully.',
        ], 200);
    }

    /**
     * Restore a soft deleted post.
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();

        return response()->json([
            'status' => true,
            'message' => 'Post restored successfully.',
            'data' => $post,
        ], 200);
    }
}
