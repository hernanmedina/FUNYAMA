<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitacionUsuario extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string  $nombre  Nombre completo del invitado.
     * @param  string  $rol  Rol asignado ('admin' o 'instructor').
     * @param  string  $token  Token de un solo uso para establecer la contraseña.
     */
    public function __construct(
        public string $nombre,
        public string $rol,
        public string $token,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $etiquetaRol = $this->rol === 'admin' ? 'administrador' : 'docente';

        return (new MailMessage)
            ->subject('Invitación a Fundación YAMA')
            ->greeting('¡Hola '.$this->nombre.'!')
            ->line('Has sido invitado a la plataforma de Fundación YAMA como '.$etiquetaRol.'.')
            ->line('Para activar tu cuenta, establece tu contraseña con el siguiente enlace:')
            ->action('Establecer contraseña', url('/establecer-password/'.$this->token))
            ->line('Si no esperabas esta invitación, puedes ignorar este correo.')
            ->salutation('Atentamente, Fundación YAMA');
    }
}
