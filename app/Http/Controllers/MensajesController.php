<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\User;
use App\Models\Equipo;

class MensajesController extends Controller
{
    /**
     * Mostrar la vista principal de mensajes
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus mensajes');
        }

        $userId = Auth::id();
        $filtro = $request->get('filtro', 'all'); // 'all' o 'teams'

        // Obtener chats privados del usuario
        $chatsPrivados = Chat::where('tipo', 'privado')
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->with(['user1', 'user2', 'ultimoMensaje.usuario'])
            ->orderBy('ultimo_mensaje_at', 'desc')
            ->get();

        // Obtener chats de equipo del usuario - CORREGIDO
        $chatsEquipo = Chat::where('tipo', 'equipo')
            ->whereHas('miembros', function($query) use ($userId) {
                $query->where('chat_miembros.user_id', $userId); // Especificar la tabla
            })
            ->with(['equipo', 'miembros', 'ultimoMensaje.usuario'])
            ->orderBy('ultimo_mensaje_at', 'desc')
            ->get();

        // Debug: Ver qué chats de equipo se encontraron
        Log::info('Chats de equipo encontrados:', [
            'user_id' => $userId,
            'count' => $chatsEquipo->count(),
            'chats' => $chatsEquipo->pluck('id', 'nombre')->toArray()
        ]);

        // Filtrar según la selección
        if ($filtro === 'teams') {
            $chats = $chatsEquipo;
        } else {
            $chats = $chatsPrivados->concat($chatsEquipo)->sortByDesc('ultimo_mensaje_at');
        }

        // Obtener usuarios disponibles (excepto el actual)
        $usuarios = User::where('id', '!=', $userId)
            ->orderBy('name', 'asc')
            ->get();

        // Chat activo y mensajes
        $chatActivo = null;
        $mensajes = collect();

        if ($chats->isNotEmpty()) {
            $chatActivo = $chats->first();
            $mensajes = $chatActivo->mensajes()->with('usuario')->get();

            // Marcar mensajes como leídos
            $this->marcarMensajesComoLeidos($chatActivo, $userId);
        }

        return view('mensajes.index', compact('chats', 'usuarios', 'chatActivo', 'mensajes', 'filtro', 'chatsPrivados', 'chatsEquipo'));
    }

    /**
     * Ver un chat específico
     */
    public function ver($chatId)
    {
        $userId = Auth::id();

        $chat = Chat::where('id', $chatId)->firstOrFail();

        // Verificar permisos
        if (!$chat->perteneceUsuario($userId)) {
            abort(403, 'No tienes acceso a este chat');
        }

        // Cargar relaciones necesarias
        if ($chat->esPrivado()) {
            $chat->load(['user1', 'user2']);
        } else {
            $chat->load(['equipo', 'miembros']);
        }

        $mensajes = $chat->mensajes()->with('usuario')->get();
        $this->marcarMensajesComoLeidos($chat, $userId);

        // Obtener todos los chats - CORREGIDO
        $chatsPrivados = Chat::where('tipo', 'privado')
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->with(['user1', 'user2', 'ultimoMensaje.usuario'])
            ->orderBy('ultimo_mensaje_at', 'desc')
            ->get();

        $chatsEquipo = Chat::where('tipo', 'equipo')
            ->whereHas('miembros', function($query) use ($userId) {
                $query->where('chat_miembros.user_id', $userId); // Especificar la tabla
            })
            ->with(['equipo', 'miembros', 'ultimoMensaje.usuario'])
            ->orderBy('ultimo_mensaje_at', 'desc')
            ->get();

        $chats = $chatsPrivados->concat($chatsEquipo)->sortByDesc('ultimo_mensaje_at');

        $usuarios = User::where('id', '!=', $userId)->orderBy('name', 'asc')->get();

        return view('mensajes.index', compact('chats', 'usuarios', 'chat', 'mensajes', 'chatsPrivados', 'chatsEquipo'))
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

        // Verificar permisos
        if (!$chat->perteneceUsuario($userId)) {
            return response()->json(['error' => 'No tienes permiso para enviar mensajes en este chat'], 403);
        }

        // Crear el mensaje
        $mensaje = Mensaje::create([
            'chat_id' => $request->chat_id,
            'user_id' => $userId,
            'mensaje' => $request->mensaje,
            'leido' => false
        ]);

        // Actualizar timestamp del chat
        $chat->update(['ultimo_mensaje_at' => now()]);

        return response()->json([
            'success' => true,
            'mensaje' => $mensaje->load('usuario')
        ]);
    }

    /**
     * Iniciar un nuevo chat privado
     */
    public function iniciarChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $userId = Auth::id();
        $otroUserId = $request->user_id;

        if ($userId == $otroUserId) {
            return redirect()->back()->with('error', 'No puedes crear un chat contigo mismo');
        }

        // Buscar chat existente
        $chat = Chat::where('tipo', 'privado')
            ->where(function($query) use ($userId, $otroUserId) {
                $query->where('user1_id', $userId)->where('user2_id', $otroUserId);
            })->orWhere(function($query) use ($userId, $otroUserId) {
                $query->where('user1_id', $otroUserId)->where('user2_id', $userId);
            })->first();

        // Crear chat si no existe
        if (!$chat) {
            $chat = Chat::create([
                'tipo' => 'privado',
                'user1_id' => $userId,
                'user2_id' => $otroUserId,
                'ultimo_mensaje_at' => now()
            ]);
        }

        return redirect()->route('mensajes.ver', $chat->id);
    }

    /**
     * Marcar mensajes como leídos
     */
    private function marcarMensajesComoLeidos($chat, $userId)
    {
        Mensaje::where('chat_id', $chat->id)
            ->where('user_id', '!=', $userId)
            ->where('leido', false)
            ->update(['leido' => true]);
    }

    /**
     * Obtener mensajes de un chat (AJAX)
     */
    public function obtenerMensajes($chatId)
    {
        $userId = Auth::id();
        $chat = Chat::findOrFail($chatId);

        if (!$chat->perteneceUsuario($userId)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $mensajes = $chat->mensajes()->with('usuario')->get();

        return response()->json([
            'success' => true,
            'mensajes' => $mensajes
        ]);
    }
}