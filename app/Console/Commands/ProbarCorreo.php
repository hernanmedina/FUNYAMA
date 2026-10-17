<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProbarCorreo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:probar-correo {email : Dirección de destino}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba para verificar la configuración de mail';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $this->info('Mailer: '.config('mail.default'));
        $this->info('Host: '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port'));
        $this->info('Enviando correo de prueba a '.$email.'...');

        try {
            Mail::raw(
                'Este es un correo de prueba enviado desde Fundación YAMA el '.now()->format('d/m/Y H:i:s').'.',
                function ($message) use ($email) {
                    $message->to($email)->subject('Prueba de configuración de correo');
                }
            );

            $this->info('Correo enviado correctamente.');

            if (config('mail.default') === 'smtp' && config('mail.mailers.smtp.port') === 1025) {
                $this->line('Revisa la interfaz de Mailpit en: http://127.0.0.1:8025');
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al enviar el correo: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
