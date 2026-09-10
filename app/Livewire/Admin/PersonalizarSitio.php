<?php

namespace App\Livewire\Admin;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PersonalizarSitio extends Component
{
    use WithFileUploads;

    // Estadísticas de la sección principal
    public string $stat_estudiantes = '';

    public string $stat_cursos = '';

    public string $stat_experiencia = '';

    public string $stat_satisfaccion = '';

    // Tarjeta "Años Transformando Vidas"
    public string $about_anios = '';

    public string $about_titulo = '';

    public string $about_descripcion = '';

    // Lista de beneficios (3 ítems)
    public array $beneficios = ['', '', ''];

    // Datos de contacto del pie de página
    public string $contacto_email = '';

    public string $contacto_whatsapp_1 = '';

    public string $contacto_whatsapp_2 = '';

    // Logo del sitio
    public $logo;

    public ?string $logo_actual = null;

    // Pago por Nequi
    public string $nequi_numero = '';

    public string $nequi_titular = '';

    public string $nequi_instrucciones = '';

    public $nequi_qr;

    public ?string $nequi_qr_actual = null;

    public function mount(): void
    {
        $this->stat_estudiantes = (string) Configuracion::obtener('stat_estudiantes', '235+');
        $this->stat_cursos = (string) Configuracion::obtener('stat_cursos', '20+');
        $this->stat_experiencia = (string) Configuracion::obtener('stat_experiencia', '15+');
        $this->stat_satisfaccion = (string) Configuracion::obtener('stat_satisfaccion', '98%');

        $this->about_anios = (string) Configuracion::obtener('about_anios', '15+');
        $this->about_titulo = (string) Configuracion::obtener('about_titulo', 'Años Transformando Vidas');
        $this->about_descripcion = (string) Configuracion::obtener(
            'about_descripcion',
            'Más de una década comprometidos con la excelencia educativa y el desarrollo comunitario.'
        );

        $this->beneficios = [
            (string) Configuracion::obtener('beneficio_1', 'Educación accesible para todos'),
            (string) Configuracion::obtener('beneficio_2', 'Instructores altamente calificados'),
            (string) Configuracion::obtener('beneficio_3', 'Comunidad de apoyo y crecimiento'),
        ];

        $this->contacto_email = (string) Configuracion::obtener('contacto_email', 'fundacionyamacapacitaciones@gmail.com');
        $this->contacto_whatsapp_1 = (string) Configuracion::obtener('contacto_whatsapp_1', '323 373 1395');
        $this->contacto_whatsapp_2 = (string) Configuracion::obtener('contacto_whatsapp_2', '321 882 1641');

        $this->logo_actual = Configuracion::obtener('logo_sitio');

        $this->nequi_numero = (string) Configuracion::obtener('nequi_numero', '');
        $this->nequi_titular = (string) Configuracion::obtener('nequi_titular', '');
        $this->nequi_instrucciones = (string) Configuracion::obtener(
            'nequi_instrucciones',
            'Escanea el código QR desde tu aplicación Nequi o envía el pago al número indicado. Luego comparte el comprobante con la fundación.'
        );
        $this->nequi_qr_actual = Configuracion::obtener('nequi_qr');
    }

    protected function rules(): array
    {
        return [
            'stat_estudiantes' => 'required|string|max:50',
            'stat_cursos' => 'required|string|max:50',
            'stat_experiencia' => 'required|string|max:50',
            'stat_satisfaccion' => 'required|string|max:50',
            'about_anios' => 'required|string|max:50',
            'about_titulo' => 'required|string|max:255',
            'about_descripcion' => 'required|string|max:500',
            'beneficios' => 'required|array|size:3',
            'beneficios.*' => 'required|string|max:255',
            'contacto_email' => 'required|email|max:255',
            'contacto_whatsapp_1' => 'required|string|max:50',
            'contacto_whatsapp_2' => 'required|string|max:50',
            'logo' => 'nullable|image|max:3072',
            'nequi_numero' => 'nullable|string|max:50',
            'nequi_titular' => 'nullable|string|max:255',
            'nequi_instrucciones' => 'nullable|string|max:500',
            'nequi_qr' => 'nullable|image|max:3072',
        ];
    }

    protected function messages(): array
    {
        return [
            'stat_estudiantes.required' => 'El valor de estudiantes beneficiados es obligatorio.',
            'stat_cursos.required' => 'El valor de cursos disponibles es obligatorio.',
            'stat_experiencia.required' => 'El valor de años de experiencia es obligatorio.',
            'stat_satisfaccion.required' => 'El valor de satisfacción es obligatorio.',
            'about_anios.required' => 'El número de años es obligatorio.',
            'about_titulo.required' => 'El título de la tarjeta es obligatorio.',
            'about_descripcion.required' => 'La descripción de la tarjeta es obligatoria.',
            'beneficios.*.required' => 'Todos los beneficios deben tener texto.',
            'contacto_email.required' => 'El correo de contacto es obligatorio.',
            'contacto_email.email' => 'El correo de contacto debe ser una dirección válida.',
            'contacto_whatsapp_1.required' => 'El primer número de WhatsApp es obligatorio.',
            'contacto_whatsapp_2.required' => 'El segundo número de WhatsApp es obligatorio.',
            'logo.image' => 'El logo debe ser una imagen válida.',
            'logo.max' => 'El logo no debe superar los 3 MB.',
            'nequi_qr.image' => 'El código QR de Nequi debe ser una imagen válida.',
            'nequi_qr.max' => 'El código QR de Nequi no debe superar los 3 MB.',
        ];
    }

    public function guardar(): void
    {
        $this->validate();

        Configuracion::establecer('stat_estudiantes', $this->stat_estudiantes, 'texto', 'estadisticas');
        Configuracion::establecer('stat_cursos', $this->stat_cursos, 'texto', 'estadisticas');
        Configuracion::establecer('stat_experiencia', $this->stat_experiencia, 'texto', 'estadisticas');
        Configuracion::establecer('stat_satisfaccion', $this->stat_satisfaccion, 'texto', 'estadisticas');

        Configuracion::establecer('about_anios', $this->about_anios, 'texto', 'nosotros');
        Configuracion::establecer('about_titulo', $this->about_titulo, 'texto', 'nosotros');
        Configuracion::establecer('about_descripcion', $this->about_descripcion, 'texto', 'nosotros');

        foreach ($this->beneficios as $indice => $beneficio) {
            Configuracion::establecer('beneficio_'.($indice + 1), $beneficio, 'texto', 'nosotros');
        }

        Configuracion::establecer('contacto_email', $this->contacto_email, 'texto', 'contacto');
        Configuracion::establecer('contacto_whatsapp_1', $this->contacto_whatsapp_1, 'texto', 'contacto');
        Configuracion::establecer('contacto_whatsapp_2', $this->contacto_whatsapp_2, 'texto', 'contacto');

        if ($this->logo) {
            if ($this->logo_actual && Storage::disk('public')->exists($this->logo_actual)) {
                Storage::disk('public')->delete($this->logo_actual);
            }

            $ruta = $this->logo->store('sitio', 'public');
            Configuracion::establecer('logo_sitio', $ruta, 'imagen', 'general');
            $this->logo_actual = $ruta;
            $this->logo = null;
        }

        Configuracion::establecer('nequi_numero', $this->nequi_numero, 'texto', 'pagos');
        Configuracion::establecer('nequi_titular', $this->nequi_titular, 'texto', 'pagos');
        Configuracion::establecer('nequi_instrucciones', $this->nequi_instrucciones, 'texto', 'pagos');

        if ($this->nequi_qr) {
            if ($this->nequi_qr_actual && Storage::disk('public')->exists($this->nequi_qr_actual)) {
                Storage::disk('public')->delete($this->nequi_qr_actual);
            }

            $rutaQr = $this->nequi_qr->store('pagos', 'public');
            Configuracion::establecer('nequi_qr', $rutaQr, 'imagen', 'pagos');
            $this->nequi_qr_actual = $rutaQr;
            $this->nequi_qr = null;
        }

        session()->flash('mensaje', 'La personalización del sitio se guardó correctamente.');

        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.personalizar-sitio')->layout('layouts.app');
    }
}
