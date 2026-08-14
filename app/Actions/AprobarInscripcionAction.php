<?php

namespace App\Actions;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\User;
use App\Notifications\CredencialesEstudiante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AprobarInscripcionAction
{
    /**
     * Aprueba una solicitud de inscripción: crea el usuario, el estudiante,
     * lo inscribe en el curso y envía las credenciales por correo.
     *
     * @return array{success: bool, message: string, user?: User, estudiante?: Estudiante}
     */
    public function execute(Solicitud $solicitud, string $codigoEstudiante, string $respuesta = ''): array
    {
        $datos = $solicitud->datos_adicionales;

        // Verificar que el curso exista y tenga cupos
        $curso = Curso::where('codigo', $datos['codigo_curso'])->first();
        if (! $curso) {
            return ['success' => false, 'message' => 'El curso asociado a esta solicitud ya no existe.'];
        }

        if ($curso->cupo_disponible <= 0) {
            return ['success' => false, 'message' => 'El curso "'.$curso->nombre.'" ya no tiene cupos disponibles.'];
        }

        DB::beginTransaction();
        try {
            // 1. Usar el documento_ID como contraseña
            $password = (string) ($datos['documento_ID'] ?? Str::random(10));

            // 2. Verificar si ya existe un usuario con ese email
            $user = User::where('email', $solicitud->email_contacto)->first();

            if (! $user) {
                // Crear el usuario
                $user = User::create([
                    'name' => $datos['nombre'] ?? $solicitud->email_contacto,
                    'apellido' => $datos['apellido'] ?? '',
                    'email' => $solicitud->email_contacto,
                    'documento_ID' => $datos['documento_ID'] ?? 'S/N',
                    'password' => Hash::make($password),
                    'telefono' => $solicitud->telefono ?? '',
                    'direccion' => $datos['direccion'] ?? '',
                    'role' => 'estu',
                ]);
            } else {
                // Si ya existe, solo actualizamos algunos datos
                $user->update([
                    'name' => $datos['nombre'] ?? $user->name,
                    'apellido' => $datos['apellido'] ?? $user->apellido,
                    'telefono' => $solicitud->telefono ?? $user->telefono,
                ]);
            }

            // 3. Verificar si ya existe un estudiante asociado a ese usuario
            $estudiante = Estudiante::where('user_id', $user->id)->first();

            if (! $estudiante) {
                // Crear el estudiante con el código generado
                $estudiante = Estudiante::create([
                    'codigo' => $codigoEstudiante,
                    'user_id' => $user->id,
                    'fecha_nacimiento' => ! empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
                    'genero' => ! empty($datos['genero']) ? $datos['genero'] : null,
                    'nivel_educativo' => ! empty($datos['nivel_educativo']) ? $datos['nivel_educativo'] : null,
                    'intereses' => 'Inscrito vía solicitud - Curso: '.($datos['nombre_curso'] ?? ''),
                    'fecha_registro' => now(),
                    'activo' => true,
                ]);
            }

            // 4. Inscribir al estudiante en el curso (si no está ya inscrito)
            $yaInscrito = $estudiante->cursos()->where('curso_id', $curso->codigo)->exists();

            if (! $yaInscrito) {
                $estudiante->cursos()->attach($curso->codigo, [
                    'estado' => 'inscrito',
                    'pago_realizado' => 0,
                    'estado_pago' => 'pendiente',
                    'fecha_inscripcion' => now(),
                    'progreso' => 0,
                ]);

                // Actualizar cupo disponible
                $curso->decrement('cupo_disponible');
            }

            // 5. Marcar solicitud como resuelta
            $admin = auth()->user()?->administrador;
            $solicitud->marcarComoResuelta(
                $respuesta ?: 'Solicitud aprobada. Se ha creado tu cuenta y se te ha inscrito en el curso.',
                $admin?->idAdmin
            );

            DB::commit();

            // 6. Enviar correo con credenciales
            try {
                $user->notify(new CredencialesEstudiante(
                    $user->name,
                    $user->email,
                    $password,
                    $estudiante->codigo
                ));
            } catch (\Exception $e) {
                // Si falla el envío de correo, no detenemos el proceso
                Log::error('Error al enviar credenciales: '.$e->getMessage());
            }

            return [
                'success' => true,
                'message' => 'Solicitud aprobada. Estudiante creado e inscrito exitosamente. Se han enviado las credenciales por correo.',
                'user' => $user,
                'estudiante' => $estudiante,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en AprobarInscripcionAction: '.$e->getMessage(), [
                'solicitud_id' => $solicitud->idSolicitud,
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'message' => 'Error al procesar la solicitud: '.$e->getMessage()];
        }
    }
}
