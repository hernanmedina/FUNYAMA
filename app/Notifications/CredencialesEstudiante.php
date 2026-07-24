<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CredencialesEstudiante extends Notification
{
    use Queueable;

    public $nombre;
    public $email;
    public $password;
    public $codigoEstudiante;

    public function __construct($nombre, $email, $password, $codigoEstudiante)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->codigoEstudiante = $codigoEstudiante;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('¡Bienvenido a Fundación YAMA! Tus credenciales de acceso')
            ->greeting('¡Hola ' . $this->nombre . '!')
            ->line('Has sido registrado exitosamente como estudiante en Fundación YAMA.')
            ->line('Tus credenciales de acceso son las siguientes:')
            ->line('**Email:** ' . $this->email)
            ->line('**Contraseña:** ' . $this->password)
            ->line('**Código de estudiante:** ' . $this->codigoEstudiante)
            ->action('Iniciar Sesión', url('/login'))
            ->line('Te recomendamos cambiar tu contraseña después de iniciar sesión.')
            ->line('¡Bienvenido a nuestra comunidad educativa!')
            ->salutation('Atentamente, Fundación YAMA');
    }
}
