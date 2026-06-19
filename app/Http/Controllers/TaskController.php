<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Notification;
use Carbon\Carbon;


 
class TaskController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $this->cleanupLegacyDuplicates($userId);

        $tasks = Task::where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

        $nextTask = Task::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->first();

        $showToast = false;

        if ($nextTask) {
            $this->syncTaskStateNotification($nextTask);
        } else {
            Notification::where('user_id', $userId)
                ->whereIn('type', ['pendiente', 'vencida'])
                ->delete();
        }

        $hasUnreadAuto = Notification::where('user_id', $userId)
            ->whereIn('type', ['pendiente', 'vencida'])
            ->where('read', false)
            ->exists();

        if ($hasUnreadAuto) {
            $showToast = true;
        }

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();

        return view('dashboard', compact(
            'tasks',
            'notifications',
            'nextTask',
            'showToast',
            'unreadCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
        ], [
            'title.required' => 'El título es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'date.required' => 'La fecha es obligatoria.',
            'time.required' => 'La hora es obligatoria.',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'user_id' => auth()->id(),
        ]);

        $this->upsertTaskNotification(
            auth()->id(),
            $task->id,
            'creada',
            'Tarea creada',
            "Se creó la tarea: '{$task->title}'"
        );

        return redirect()->route('dashboard')->with('success', 'Tarea creada');
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->has('status')) {
            $task->update([
                'status' => $request->status,
                'notification_dismissed_at' => null,
                'last_state_notified' => null,
            ]);

            Notification::where('user_id', auth()->id())
                ->where('task_id', $task->id)
                ->whereIn('type', ['pendiente', 'vencida'])
                ->delete();

            $this->upsertTaskNotification(
                auth()->id(),
                $task->id,
                'completada',
                'Tarea terminada',
                "Se completó la tarea: '{$task->title}'"
            );

            return redirect()->route('dashboard')->with('success', 'Tarea completada');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $newDateTime = Carbon::parse($request->date . ' ' . $request->time, auth()->user()->timezone ?? config('app.timezone'));
        $now = Carbon::now(auth()->user()->timezone ?? config('app.timezone'));

        $status = $task->status;
        if ($task->status === 'vencida' && $newDateTime->isFuture()) {
            $status = 'pending';
        }

        $dateTimeChanged = ($task->date !== $request->date || $task->time !== $request->time);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'status' => $status,
            'notification_dismissed_at' => $dateTimeChanged ? null : $task->notification_dismissed_at,
            'last_state_notified' => $dateTimeChanged ? null : $task->last_state_notified,
        ]);

        $this->upsertTaskNotification(
            auth()->id(),
            $task->id,
            'actualizada',
            'Tarea actualizada',
            "Se actualizó la tarea: '{$task->title}'"
        );

        return redirect()->route('dashboard')->with('success', 'Tarea actualizada');
    }

    public function destroy($id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $title = $task->title;
        $taskId = $task->id;

        $task->delete();

        Notification::where('user_id', auth()->id())
            ->where('task_id', $taskId)
            ->delete();

        $this->upsertTaskNotification(
            auth()->id(),
            null,
            'eliminada',
            'Tarea eliminada',
            "Se eliminó la tarea: '{$title}'"
        );

        return redirect()->route('dashboard')->with('success', 'Tarea eliminada');
    }


    
    private function syncTaskStateNotification(Task $task): void
    {
        $userId = $task->user_id;
        $tz = auth()->user()->timezone ?? config('app.timezone');

        $fechaLimite = Carbon::parse($task->date . ' ' . $task->time, $tz);
        $ahora = Carbon::now($tz);
        $diffHoras = $ahora->diffInMinutes($fechaLimite, false) / 60;

        if ($diffHoras <= 0) {
            $newState = 'overdue';
            if ($task->status !== 'vencida') {
                $task->update(['status' => 'vencida']);
            }
        } elseif ($diffHoras <= 72) {
            $newState = 'near';
        } else {
            $newState = 'pending';
        }

        if ($newState === 'pending') {
            Notification::where('user_id', $userId)
                ->where('task_id', $task->id)
                ->whereIn('type', ['pendiente', 'vencida'])
                ->delete();
            $task->update(['last_state_notified' => null]);
            return;
        }

        if ($task->notification_dismissed_at !== null) {
            return;
        }

        if ($task->last_state_notified === $newState) {
            return;
        }

        $previousType = $newState === 'overdue' ? 'pendiente' : 'vencida';
        $newType = $newState === 'overdue' ? 'vencida' : 'pendiente';

        Notification::where('user_id', $userId)
            ->where('task_id', $task->id)
            ->where('type', $previousType)
            ->delete();

        if ($newState === 'overdue') {
            $message = "La tarea '{$task->title}' ha expirado sin completarse.";
            $title = 'Tarea vencida';
        } else {
            $horasTexto = $diffHoras < 24
                ? round($diffHoras, 1) . " horas"
                : round($diffHoras / 24) . " días";
            $message = "Tarea pendiente: '{$task->title}' - Faltan {$horasTexto}.";
            $title = 'Tarea pendiente';
        }

        $this->upsertTaskNotification($userId, $task->id, $newType, $title, $message);

        $task->update(['last_state_notified' => $newState]);
    }

  
    private function upsertTaskNotification($userId, $taskId, $type, $title, $message)
    {
        $query = Notification::where('user_id', $userId)
            ->where('type', $type);

        if ($taskId !== null) {
            $query->where('task_id', $taskId);
        } else {
            $query->whereNull('task_id');
        }

        $existing = $query->first();

        if ($existing) {
            $existing->update([
                'title' => $title,
                'message' => $message,
            ]);
            return $existing;
        }

        return Notification::create([
            'user_id' => $userId,
            'task_id' => $taskId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'read' => false,
        ]);
    }

    
    private function cleanupLegacyDuplicates($userId)
    {
        $legacy = Notification::where('user_id', $userId)
            ->whereNull('task_id')
            ->get();

        $grouped = [];
        foreach ($legacy as $notif) {
            $key = $notif->title;
            $grouped[$key][] = $notif;
        }

        foreach ($grouped as $title => $items) {
            if (count($items) <= 1) {
                continue;
            }
            usort($items, function ($a, $b) {
                $aTime = $a->created_at ? $a->created_at->getTimestamp() : 0;
                $bTime = $b->created_at ? $b->created_at->getTimestamp() : 0;
                return $bTime <=> $aTime;
            });
            $keep = array_shift($items);
            foreach ($items as $dup) {
                $dup->delete();
            }
        }
    }
}
