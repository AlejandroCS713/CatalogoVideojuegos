<?php

namespace App\Console\Commands;

use App\Models\users\User;
use Illuminate\Console\Command;
use App\Jobs\SendBulkEmailToUser;
use Illuminate\Support\Facades\Log;

class SendBulkEmailCommand extends Command
{
    protected $signature = 'bulk:email {message : El contenido del email a enviar}';

    protected $description = 'Envía un correo electrónico masivo a todos los usuarios no administradores usando la cola.';

    public function handle(): int
    {
        $messageContent = $this->argument('message');

        if (empty($messageContent)) {
            $this->error('El mensaje no puede estar vacío.');
            return Command::FAILURE;
        }

        $this->info("Iniciando el proceso de encolado de correos masivos...");
        Log::info("Comando bulk:email iniciado.");

        $userQuery = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        });

        $userCount = $userQuery->count();

        if ($userCount === 0) {
            $this->warn('No se encontraron usuarios (no administradores) para enviar correos.');
            Log::warning("Comando bulk:email: No se encontraron usuarios destino.");
            return Command::SUCCESS;
        }

        $this->info("Se enviará el correo a {$userCount} usuarios.");

        $progressBar = $this->output->createProgressBar($userCount);
        $progressBar->start();

        $processedCount = 0;
        $userQuery->chunkById(200, function ($users) use ($messageContent, $progressBar, &$processedCount) {
            foreach ($users as $user) {
                try {
                    SendBulkEmailToUser::dispatch($user, $messageContent);
                    $processedCount++;
                } catch (\Exception $e) {
                    $this->error("\nError encolando email para {$user->email}: " . $e->getMessage());
                    Log::error("Comando bulk:email: Error despachando Job para {$user->id} ({$user->email}): " . $e->getMessage());
                }
            }
            $progressBar->advance(count($users));
        });

        $progressBar->finish();
        $this->info("\n\n¡Éxito! Se han encolado {$processedCount} correos para ser enviados en segundo plano.");
        Log::info("Comando bulk:email finalizado. {$processedCount} jobs despachados a la cola.");

        return Command::SUCCESS;
    }
}
