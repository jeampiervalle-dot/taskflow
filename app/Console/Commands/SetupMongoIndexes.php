<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupMongoIndexes extends Command
{
    protected $signature = 'mongo:setup-indexes';
    protected $description = 'Crea los índices de MongoDB necesarios para taskflow';

    public function handle(): int
    {
        $db = DB::connection('mongodb')->getDatabase();

        $this->info('Creando índices en MongoDB...');

        $db->selectCollection('users')->createIndex(
            ['email' => 1],
            ['unique' => true, 'name' => 'users_email_unique']
        );
        $this->line('  ✔ users.email (único)');

        $db->selectCollection('tasks')->createIndex(
            ['user_id' => 1],
            ['name' => 'tasks_user_id']
        );
        $this->line('  ✔ tasks.user_id');

        $db->selectCollection('tasks')->createIndex(
            ['user_id' => 1, 'status' => 1, 'date' => 1, 'time' => 1],
            ['name' => 'tasks_user_status_date_time']
        );
        $this->line('  ✔ tasks (user_id, status, date, time) — para next task');

        $db->selectCollection('tasks')->createIndex(
            ['user_id' => 1, 'updated_at' => -1],
            ['name' => 'tasks_user_updated']
        );
        $this->line('  ✔ tasks (user_id, updated_at) — para listado');

        $db->selectCollection('notifications')->createIndex(
            ['user_id' => 1, 'created_at' => -1],
            ['name' => 'notifications_user_created']
        );
        $this->line('  ✔ notifications (user_id, created_at)');

        $this->info('');
        $this->info('Índices creados. Resumen por colección:');

        foreach (['users', 'tasks', 'notifications'] as $col) {
            $this->line("  · {$col}:");
            foreach ($db->selectCollection($col)->listIndexes() as $idx) {
                $this->line('      - ' . $idx->getName());
            }
        }

        return self::SUCCESS;
    }
}
