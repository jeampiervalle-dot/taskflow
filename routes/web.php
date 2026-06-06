<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;


// Crear tarea
Route::post('/tasks', [TaskController::class, 'store'])
    ->middleware('auth')
    ->name('tasks.store');


// Redirección principal
Route::get('/', function () {
    return redirect('/dashboard');
});


Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');

    // 🔥 NUEVO: editar y eliminar tareas
    Route::resource('tasks', TaskController::class);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');


    // Notificaciones
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notificaciones.index');
    Route::patch('/notificaciones/{id}/read', [NotificationController::class, 'markAsRead'])->name('notificaciones.read');
    Route::patch('/notificaciones/read-all', [NotificationController::class, 'markAllAsRead'])->name('notificaciones.readAll');
    Route::delete('/notificaciones/{id}', [NotificationController::class, 'destroy'])->name('notificaciones.destroy');
    Route::delete('/notificaciones', [NotificationController::class, 'clearAll'])->name('notificaciones.clearAll');


    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';