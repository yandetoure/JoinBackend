<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
   // Affiche toutes les discussions de l'utilisateur authentifié
   public function getAllDiscussions()
   {
       $userId = Auth::id();

       // Récupère les discussions avec les utilisateurs avec qui des messages ont été échangés
       $discussions = Message::where('sender_id', $userId)
                       ->orWhere('receiver_id', $userId)
                       ->with(['sender', 'receiver'])
                       ->get()
                       ->groupBy(function($message) use ($userId) {
                           return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
                       });

       return response()->json([
           'status' => true,
           'message' => "Liste des discussions",
           'data' => $discussions,
       ], 200);
   }

   public function sendMessage(Request $request)
   {
       // Valider les données d'entrée
       $request->validate([
           'receiver_id' => 'required|exists:users,id',
           'message' => 'required|string|max:5000',
       ]);

       try {
           // Créer un nouveau message
           $message = Message::create([
               'sender_id' => Auth::id(),
               'receiver_id' => $request->receiver_id,
               'message' => $request->message,
           ]);

           return response()->json([
               'status' => true,
               'message' => 'Message envoyé avec succès',
               'data' => $message,
           ], 201);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => 'Erreur lors de l\'envoi du message',
               'error' => $e->getMessage(),
           ], 500);
       }
   }

   // Affiche les messages échangés avec un utilisateur spécifique
   public function getMessages($userId)
   {
       $authUserId = Auth::id();

       $messages = Message::where(function($query) use ($authUserId, $userId) {
               $query->where('sender_id', $authUserId)
                     ->where('receiver_id', $userId);
           })
           ->orWhere(function($query) use ($authUserId, $userId) {
               $query->where('sender_id', $userId)
                     ->where('receiver_id', $authUserId);
           })
           ->get();

       return response()->json([
           'status' => true,
           'message' => "Messages échangés",
           'data' => $messages,
       ], 200);
   }

   // Modifier un message
   public function updateMessage(Request $request, $messageId)
   {
       $message = Message::findOrFail($messageId);

       if ($message->sender_id !== Auth::id()) {
           return response()->json([
               'status' => false,
               'message' => "Vous n'êtes pas autorisé à modifier ce message",
           ], 403);
       }

       $message->update([
           'updated_message' => $request->message,
           'modified_at' => now(),
       ]);

       return response()->json([
           'status' => true,
           'message' => "Message modifié avec succès",
           'data' => $message,
       ], 200);
   }

   // Supprimer un message
   public function deleteMessage($messageId)
   {
       $message = Message::findOrFail($messageId);

       if ($message->sender_id !== Auth::id()) {
           return response()->json([
               'status' => false,
               'message' => "Vous n'êtes pas autorisé à supprimer ce message",
           ], 403);
       }

       $message->update([
           'is_deleted' => true,
           'deleted_at' => now(),
       ]);

       return response()->json([
           'status' => true,
           'message' => "Message supprimé avec succès",
       ], 200);
   }


    // Restaurer un message supprimé
    public function restoreMessage($messageId)
    {
        $message = Message::onlyTrashed()->findOrFail($messageId);

        if ($message->sender_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => "Vous n'êtes pas autorisé à restaurer ce message",
            ], 403);
        }

        $message->restore();

        return response()->json([
            'status' => true,
            'message' => "Message restauré avec succès",
            'data' => $message,
        ], 200);
    }
}
