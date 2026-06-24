<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $tasks = Task::where('user_id', $userId)->get();
        $total = $tasks->count();
        $completed = $tasks->where('status', 'completed')->count();
        $pending = $tasks->where('status', 'pending')->count();
        $expired = $tasks->where('status', 'vencida')->count();
        $percent = $total > 0 ? round(($completed / $total) * 100) : 0;

        $nextTask = Task::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->first();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('read', false)->count();

        $now = now();
        $calMonth = $now->month;
        $calYear = $now->year;
        $calMonthName = ucfirst($now->translatedFormat('F'));

        $allTasksJson = $tasks->map(fn($t) => [
            'title' => $t->title,
            'date'  => $t->date,
            'time'  => $t->time,
            'status' => $t->status,
        ])->values();

        return view('home', compact(
            'tasks', 'total', 'completed', 'pending', 'expired', 'percent',
            'nextTask', 'unreadCount',
            'calMonth', 'calYear', 'calMonthName', 'allTasksJson'
        ));
    }
}
