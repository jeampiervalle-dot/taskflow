<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $filter = $request->query('filter', 'all');

        $query = Notification::where('user_id', $userId);

        if ($filter === 'unread') {
            $query->where('read', false);
        } elseif ($filter === 'read') {
            $query->where('read', true);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(30)->appends(request()->query());

        $unreadCount = Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();

        $readCount = Notification::where('user_id', $userId)
            ->where('read', true)
            ->count();

        $totalCount = Notification::where('user_id', $userId)->count();

        return view('notificaciones', compact(
            'notifications',
            'unreadCount',
            'readCount',
            'totalCount',
            'filter'
        ));
    }

    public function markAsRead($id)
    {
        $notification = $this->findNotification($id);

        $notification->update(['read' => true]);

        return redirect()->route('notificaciones.index')->with('success', 'Notificación marcada como leída');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        return redirect()->route('notificaciones.index')->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    public function destroy($id)
    {
        $notification = $this->findNotification($id);

        $this->markRelatedTaskAsDismissed($notification);

        $notification->delete();

        return redirect()->route('notificaciones.index')->with('success', 'Notificación eliminada');
    }

    public function clearAll()
    {
        $userId = auth()->id();

        $expiredIds = Notification::where('user_id', $userId)
            ->whereIn('type', ['pendiente', 'vencida'])
            ->whereNotNull('task_id')
            ->pluck('task_id');

        if ($expiredIds->isNotEmpty()) {
            Task::whereIn('id', $expiredIds)
                ->where('user_id', $userId)
                ->update(['notification_dismissed_at' => now()]);
        }

        Notification::where('user_id', $userId)->delete();

        return redirect()->route('notificaciones.index')->with('success', 'Todas las notificaciones fueron eliminadas');
    }

    private function findNotification($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            abort(404, 'Notificación no encontrada');
        }

        return $notification;
    }

    private function markRelatedTaskAsDismissed(Notification $notification)
    {
        if (!in_array($notification->type, ['pendiente', 'vencida'], true)) {
            return;
        }

        if (empty($notification->task_id)) {
            return;
        }

        $task = Task::where('id', $notification->task_id)
            ->where('user_id', $notification->user_id)
            ->first();

        if ($task) {
            $task->update(['notification_dismissed_at' => now()]);
        }
    }
}
