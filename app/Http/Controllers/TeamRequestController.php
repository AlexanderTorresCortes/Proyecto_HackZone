<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamRequest;
use App\Notifications\TeamRequestNotification;
use App\Notifications\TeamRequestResponseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamRequestController extends Controller
{
    // Crear una solicitud para unirse a un equipo
    public function store(Request $request, Team $team)
    {
        $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        // Verificar que el equipo es privado
        if (!$team->is_private) {
            return back()->with('error', 'Este equipo no requiere solicitud.');
        }

        // Verificar que el usuario no es miembro del equipo
        if ($team->members()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'Ya eres miembro de este equipo.');
        }

        // Verificar que no hay una solicitud pendiente
        $existingRequest = TeamRequest::where('team_id', $team->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'Ya tienes una solicitud pendiente para este equipo.');
        }

        // Crear la solicitud
        $teamRequest = TeamRequest::create([
            'team_id' => $team->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Notificar al creador del equipo
        $team->owner->notify(new TeamRequestNotification($teamRequest));

        return back()->with('success', 'Solicitud enviada correctamente.');
    }

    // Aceptar una solicitud
    public function accept(TeamRequest $teamRequest)
    {
        // Verificar que el usuario actual es el dueño del equipo
        if ($teamRequest->team->owner_id !== auth()->id()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // Verificar que la solicitud está pendiente
        if (!$teamRequest->isPending()) {
            return back()->with('error', 'Esta solicitud ya ha sido procesada.');
        }

        DB::transaction(function () use ($teamRequest) {
            // Actualizar el estado de la solicitud
            $teamRequest->update(['status' => 'accepted']);

            // Agregar al usuario al equipo
            $teamRequest->team->members()->attach($teamRequest->user_id, [
                'role' => 'member',
                'joined_at' => now(),
            ]);

            // Notificar al solicitante
            $teamRequest->user->notify(
                new TeamRequestResponseNotification($teamRequest, true)
            );
        });

        // Marcar la notificación como leída
        auth()->user()->notifications()
            ->where('data->team_request_id', $teamRequest->id)
            ->update(['read_at' => now()]);

        return back()->with('success', 'Solicitud aceptada correctamente.');
    }

    // Rechazar una solicitud
    public function reject(TeamRequest $teamRequest)
    {
        // Verificar que el usuario actual es el dueño del equipo
        if ($teamRequest->team->owner_id !== auth()->id()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // Verificar que la solicitud está pendiente
        if (!$teamRequest->isPending()) {
            return back()->with('error', 'Esta solicitud ya ha sido procesada.');
        }

        DB::transaction(function () use ($teamRequest) {
            // Actualizar el estado de la solicitud
            $teamRequest->update(['status' => 'rejected']);

            // Notificar al solicitante
            $teamRequest->user->notify(
                new TeamRequestResponseNotification($teamRequest, false)
            );
        });

        // Marcar la notificación como leída
        auth()->user()->notifications()
            ->where('data->team_request_id', $teamRequest->id)
            ->update(['read_at' => now()]);

        return back()->with('success', 'Solicitud rechazada.');
    }
}