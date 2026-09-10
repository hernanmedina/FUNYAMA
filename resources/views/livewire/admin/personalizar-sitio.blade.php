<div>
    <div class="w-full max-w-[1200px] mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Personalizar Sitio</h1>
                <p class="text-gray-600 mt-2">Modifica las estadísticas, la tarjeta informativa y el logo que se muestran en la página principal.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        @if (session()->has('mensaje'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800">
                {{ session('mensaje') }}
            </div>
        @endif

        <form wire:submit="guardar" class="space-y-8">
            <!-- Estadísticas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Estadísticas de la página principal</h2>
                    <p class="text-sm text-gray-500 mt-1">Los cuatro números que aparecen debajo del banner principal.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estudiantes Beneficiados</label>
                        <input type="text" wire:model="stat_estudiantes"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('stat_estudiantes') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cursos Disponibles</label>
                        <input type="text" wire:model="stat_cursos"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('stat_cursos') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Años de Experiencia</label>
                        <input type="text" wire:model="stat_experiencia"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('stat_experiencia') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satisfacción</label>
                        <input type="text" wire:model="stat_satisfaccion"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('stat_satisfaccion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Tarjeta Años Transformando Vidas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Tarjeta "Años Transformando Vidas"</h2>
                    <p class="text-sm text-gray-500 mt-1">La tarjeta con degradado que aparece en la sección "Nosotros".</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de años</label>
                        <input type="text" wire:model="about_anios"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('about_anios') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" wire:model="about_titulo"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('about_titulo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea wire:model="about_descripcion" rows="3"
                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('about_descripcion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Beneficios -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Lista de beneficios</h2>
                    <p class="text-sm text-gray-500 mt-1">Los tres ítems con check que aparecen junto a la tarjeta.</p>
                </div>
                <div class="p-6 space-y-4">
                    @foreach ($beneficios as $indice => $beneficio)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Beneficio {{ $indice + 1 }}</label>
                            <input type="text" wire:model="beneficios.{{ $indice }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error("beneficios.$indice") <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Datos de contacto -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Datos de contacto del pie de página</h2>
                    <p class="text-sm text-gray-500 mt-1">El correo y los números de WhatsApp que aparecen en la sección "Contacto".</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                        <input type="email" wire:model="contacto_email"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('contacto_email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp 1</label>
                        <input type="text" wire:model="contacto_whatsapp_1"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('contacto_whatsapp_1') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp 2</label>
                        <input type="text" wire:model="contacto_whatsapp_2"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('contacto_whatsapp_2') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Pago por Nequi -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Pago por Nequi (código QR)</h2>
                    <p class="text-sm text-gray-500 mt-1">Configura el código QR y los datos de la cuenta Nequi que verán los estudiantes para realizar sus pagos.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Nequi</label>
                        <input type="text" wire:model="nequi_numero" placeholder="300 123 4567"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('nequi_numero') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titular de la cuenta</label>
                        <input type="text" wire:model="nequi_titular" placeholder="Fundación Yama"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('nequi_titular') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones para el pago</label>
                        <textarea wire:model="nequi_instrucciones" rows="3"
                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('nequi_instrucciones') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-3 flex flex-col md:flex-row md:items-center gap-6">
                        <div class="flex-shrink-0">
                            @if ($nequi_qr)
                                <img src="{{ $nequi_qr->temporaryUrl() }}" alt="Vista previa del código QR"
                                     class="h-40 w-40 object-contain border border-gray-200 rounded-lg bg-white p-2">
                            @elseif ($nequi_qr_actual)
                                <img src="{{ Storage::disk('public')->url($nequi_qr_actual) }}" alt="Código QR actual"
                                     class="h-40 w-40 object-contain border border-gray-200 rounded-lg bg-white p-2">
                            @else
                                <div class="h-40 w-40 flex items-center justify-center border border-dashed border-gray-300 rounded-lg text-gray-400 text-xs text-center p-2">
                                    Sin código QR
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del código QR</label>
                            <input type="file" wire:model="nequi_qr" accept="image/*"
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                            <p class="text-xs text-gray-500 mt-2">Sube la imagen del QR generado en tu app Nequi. Formatos: JPG, PNG, WEBP. Máximo 3 MB.</p>
                            @error('nequi_qr') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="nequi_qr" class="text-sm text-blue-600 mt-2">Subiendo imagen...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Logo del sitio</h2>
                    <p class="text-sm text-gray-500 mt-1">Se muestra en el encabezado y en la portada de la página principal.</p>
                </div>
                <div class="p-6 flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex-shrink-0">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" alt="Vista previa del logo"
                                 class="h-24 w-24 object-contain border border-gray-200 rounded-lg bg-white p-2">
                        @elseif ($logo_actual)
                            <img src="{{ Storage::disk('public')->url($logo_actual) }}" alt="Logo actual"
                                 class="h-24 w-24 object-contain border border-gray-200 rounded-lg bg-white p-2">
                        @else
                            <div class="h-24 w-24 flex items-center justify-center border border-dashed border-gray-300 rounded-lg text-gray-400 text-xs text-center">
                                Sin logo
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" wire:model="logo" accept="image/*"
                               class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        <p class="text-xs text-gray-500 mt-2">Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo 3 MB.</p>
                        @error('logo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="logo" class="text-sm text-blue-600 mt-2">Subiendo imagen...</div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-6 py-3 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-3 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
