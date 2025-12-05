<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\User;

class MensajesController extends Controller
{
    /**
     * Mostrar la vista principal de mensajes
     */
    public function index()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus mensajes');
        }

        $userId = Auth::id();

        // Obtener todos los chats del usuario con sus últimos mensajes
        $chats = Chat::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1', 'user2', 'ultimoMensaje.usuario'])
            ->orderBy('ultimo_mensaje_at', 'desc')
            ->get();

        // Obtener todos los usuarios registrados (excepto el usuario actual) para poder iniciar nuevos chats
        $usuarios = User::where('id', '!=', $userId)
            ->orderBy('name', 'asc')
            ->get();

        // Debug temporal
        \Log::info('MensajesController - Usuario actual ID: ' . $userId);
        \Log::info('MensajesController - Total usuarios en BD: ' . User::count());
        \Log::info('MensajesController - Usuarios disponibles: ' . $usuarios->count());
        \Log::info('MensajesController - IDs de usuarios: ', $usuarios->pluck('id')->toArray());

        // Si hay un chat activo seleccionado, cargar sus mensajes
        $chatActivo = null;
        $mensajes = [];

        if ($chats->isNotEmpty()) {
            $chatActivo = $chats->first();
            $mensajes = $chatActivo->mensajes()->with('usuario')->get();

            // Marcar los mensajes del chat como leídos
            Mensaje::where('chat_id', $chatActivo->id)
                ->where('user_id', '!=', $userId)
                ->where('leido', false)
                ->update(['leido' => true]);
        }

        return view('mensajes.index', compact('chats', 'usuarios', 'chatActivo', 'mensajes'));
    }

    /**
     * Ver un chat específico
     */
    public function ver($chatId)
    {
        $userId = Auth::id();

        // Verificar que el chat existe y que el usuario pertenece a él
        $chat = Chat::where('id', $chatId)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->with(['user1', 'user2'])
            ->firstOrFail();

        // Obtener los mensajes del chat
        $mensajes = $chat->mensajes()->with('usuario')->get();

        // Marcar mensajes como leídos
        Mensaje::where('chat_id', $chat->id)
            ->where('user_id', '!=', $userId)
            ->where('leido', false)
            ->update(['leido' => true]);

        // Obtener todos los chats del usuario
        $chats = Chat::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1', 'user2', 'ultimoMensaje.usuario'])
            ->orderBy('ultimo_mensaje_at', 'desc')
            ->get();

        // Obtener todos los usuarios
        $usuarios = User::where('id', '!=', $userId)
            ->orderBy('name', 'asc')
            ->get();

        return view('mensajes.index', compact('chats', 'usuarios', 'chat', 'mensajes'))
            ->with('chatActivo', $chat);
    }

    /**
     * Enviar un nuevo mensaje
     */
    public function enviar(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'mensaje' => 'required|string|max:1000',
        ]);

        $userId = Auth::id();
        $chat = Chat::findOrFail($request->chat_id);

        // Verificar que el usuario pertenece al chat
        if ($chat->user1_id != $userId && $chat->user2_id != $userId) {
            return response()->json(['error' => 'No tienes permiso para enviar mensajes en este chat'], 403);
        }

        // Crear el mensaje
        $mensaje = Mensaje::create([
            'chat_id' => $request->chat_id,
            'user_id' => $userId,
            'mensaje' => $request->mensaje,
            'leido' => false
        ]);

        // Actualizar el último mensaje del chat
        $chat->update([
            'ultimo_mensaje_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => $mensaje->load('usuario')
        ]);
    }

    /**
     * Iniciar un nuevo chat con un usuario
     */
    public function iniciarChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $userId = Auth::id();
        $otroUserId = $request->user_id;

        // Verificar que no intenta crear un chat consigo mismo
        if ($userId == $otroUserId) {
            return redirect()->back()->with('error', 'No puedes crear un chat contigo mismo');
        }

        // Verificar si ya existe un chat entre estos usuarios
        $chat = Chat::where(function($query) use ($userId, $otroUserId) {
            $query->where('user1_id', $userId)->where('user2_id', $otroUserId);
        })->orWhere(function($query) use ($userId, $otroUserId) {
            $query->where('user1_id', $otroUserId)->where('user2_id', $userId);
        })->first();

        // Si no existe, crear uno nuevo
        if (!$chat) {
            $chat = Chat::create([
                'user1_id' => $userId,
                'user2_id' => $otroUserId,
                'ultimo_mensaje_at' => now()
            ]);
        }

        return redirect()->route('mensajes.ver', $chat->id);
    }

    /**
     * Obtener mensajes de un chat (para AJAX)
     */
    public function obtenerMensajes($chatId)
    {
        $userId = Auth::id();

        // Verificar que el usuario pertenece al chat
        $chat = Chat::where('id', $chatId)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        $mensajes = $chat->mensajes()->with('usuario')->get();

        return response()->json([
            'success' => true,
            'mensajes' => $mensajes
        ]);
    }
}
