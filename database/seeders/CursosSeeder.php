<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Database\Seeder;

class CursosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Administrador::first();

        if (! $admin) {
            $this->command->error('No se encontró un administrador. Ejecuta primero AdminUserSeeder.');

            return;
        }

        $cursos = [
            [
                'codigo' => 'CUR-'.date('Y').'-001',
                'nombre' => 'Microsoft Word desde Cero',
                'slug' => 'microsoft-word-desde-cero',
                'descripcion' => 'Aprende a dominar Microsoft Word desde lo más básico hasta herramientas avanzadas para crear documentos profesionales.',
                'cronograma' => 'Semana 1: Fundamentos y edición | Semana 2: Formatos avanzados | Semana 3: Tablas, imágenes y secciones | Semana 4: Documentos profesionales y prácticas',
                'requisitos' => 'Uso básico de computadora. No se requieren conocimientos previos.',
                'objetivos' => 'Crear documentos profesionales, usar estilos, insertar tablas e imágenes, manejar portadas e índices automáticos.',
                'materiales_incluidos' => 'Ejercicios prácticos, plantillas de Word, videos explicativos, certificado',
                'cupo_total' => 30,
                'cupo_disponible' => 20,
                'duracion_horas' => 30,
                'duracion_texto' => '4 semanas',
                'precio_regular' => 120.00,
                'precio_descuento' => 90.00,
                'nivel' => 'principiante',
                'imagen_portada' => 'cursos/word-1.webp',
                'enlace_classroom' => 'https://classroom.google.com/word',
                'temario' => 'Módulo 1: Entorno y Primeros Pasos
                    1.1. Identificar la Cinta de Opciones (pestañas, grupos y botones).
                    1.2. Personalizar la Barra de Herramientas de Acceso Rápido.
                    1.3. Utilizar la regla, barra de estado y el zoom.
                    1.4. Cambiar entre las distintas vistas del documento.
                    Módulo 2: Gestión de Documentos
                    2.1. Crear un documento desde cero y desde plantillas.
                    2.2. Diferenciar Guardar vs. Guardar como.
                    2.3. Guardar archivos en formatos .docx y .PDF.
                    2.4. Recuperar documentos no guardados (auto-recuperación).
                    Módulo 3: Escritura y Edición Básica
                    3.1. Navegar por el documento con teclado (Inicio, Fin, Re Pág, Av Pág).
                    3.2. Seleccionar texto con ratón y teclado (Shift + flechas).
                    3.3. Dominar Copiar (Ctrl+C), Cortar (Ctrl+X) y Pegar (Ctrl+V).
                    3.4. Usar Pegado Especial (conservar solo texto).
                    3.5. Usar Deshacer (Ctrl+Z) y Rehacer (Ctrl+Y).
                    Módulo 4: Formato de Fuente (Caracteres)
                    4.1. Cambiar fuente, tamaño y color del texto.
                    4.2. Aplicar Negrita, Cursiva, Subrayado y Tachado.
                    4.3. Aplicar superíndices y subíndices (H₂O, x²).
                    4.4. Usar el cuentagotas para copiar formato rápidamente.
                    4.5. Borrar todo el formato de un texto.
                    Módulo 5: Formato de Párrafo
                    5.1. Aplicar alineaciones: Izquierda, Centrado, Derecha y Justificado.
                    5.2. Configurar sangrías (primera línea y francesa) desde la regla.
                    5.3. Ajustar interlineado y espaciado entre párrafos.
                    5.4. Crear listas con viñetas y numeración multinivel.
                    5.5. Aplicar bordes y sombreado a párrafos.
                    Módulo 6: Inserción de Elementos Gráficos
                    6.1. Insertar imágenes y ajustar el texto alrededor (Cuadrado, Estrecho).
                    6.2. Redimensionar, recortar y rotar imágenes.
                    6.3. Insertar formas (flechas, rectángulos, círculos).
                    6.4. Insertar SmartArt (diagramas de procesos y organigramas).
                    6.5. Insertar WordArt (textos decorativos).
                    Módulo 7: Tablas
                    7.1. Insertar tablas (cuadrícula y diálogo).
                    7.2. Seleccionar celdas, filas y columnas enteras.
                    7.3. Insertar y eliminar filas y columnas.
                    7.4. Combinar (fusionar) y dividir celdas.
                    7.5. Aplicar estilos de diseño, bordes y sombreado a tablas.
                    7.6. Convertir texto en tabla y viceversa.
                    Módulo 8: Encabezados y Pies de Página
                    8.1. Insertar encabezados y pies de página.
                    8.2. Insertar números de página automáticos.
                    8.3. Configurar "Diferente en primera página" (quitar de la portada).
                    8.4. Insertar marcas de agua ("Confidencial", "Borrador").
                    Módulo 9: Estilos, Esquemas e Índice
                    9.1. Aplicar estilos predefinidos (Título 1, Título 2, Normal).
                    9.2. Modificar un estilo para cambiar todo el documento a la vez.
                    9.3. Usar la Vista Esquema para organizar el documento.
                    9.4. Insertar una Tabla de Contenidos (Índice) automática y actualizarla.
                    Módulo 10: Corrección, Impresión y Colaboración
                    10.1. Usar el corrector ortográfico y gramatical (F7).
                    10.2. Buscar sinónimos con el Tesauro.
                    10.3. Configurar márgenes, orientación y tamaño de papel.
                    10.4. Activar el Control de Cambios y Aceptar/Rechazar revisiones.
                    10.5. Insertar comentarios sin modificar el texto.
                    10.6. Imprimir con vista previa y configuración de doble cara.
                    Módulo Bonus (Word Avanzado Básico)
                    B.1. Realizar una Combinación de Correspondencia (cartas masivas con Excel).
                    B.2. Insertar hipervínculos a páginas web.
                    B.3. Grabar una macro sencilla para tareas repetitivas.
                    B.4. Usar Copilot (IA) para resumir o cambiar el tono del documento.',
                'publicado' => true,
                'destacado' => true,
                'fecha_inicio' => now()->addDays(7)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],

            [
                'codigo' => 'CUR-'.date('Y').'-002',
                'nombre' => 'Microsoft Excel Básico a Intermedio',
                'slug' => 'microsoft-excel-basico-intermedio',
                'descripcion' => 'Domina Excel para tareas personales, académicas y laborales. Aprende fórmulas, funciones y análisis de datos.',
                'cronograma' => 'Semana 1: Fundamentos y formatos | Semana 2: Fórmulas y funciones | Semana 3: Tablas dinámicas | Semana 4: Gráficos y análisis de datos',
                'requisitos' => 'Conocer operaciones básicas de computadora.',
                'objetivos' => 'Crear hojas de cálculo eficientes, usar funciones esenciales, realizar análisis con tablas dinámicas, crear gráficos.',
                'materiales_incluidos' => 'Archivos de práctica, ejercicios guiados, videos, certificado',
                'cupo_total' => 25,
                'cupo_disponible' => 12,
                'duracion_horas' => 40,
                'duracion_texto' => '4 semanas',
                'precio_regular' => 150.00,
                'precio_descuento' => 110.00,
                'nivel' => 'intermedio',
                'imagen_portada' => 'cursos/excel.jpg',
                'enlace_classroom' => 'https://classroom.google.com/excel',
                'temario' => 'MÓDULO 1: ENTORNO Y CONCEPTOS FUNDAMENTALES
                    1.1. Identificar los elementos de la pantalla (Barra de título, Cinta de opciones, Barra de fórmulas, Barra de estado).
                    1.2. Diferenciar entre Libro de trabajo (archivo) y Hoja de cálculo (pestañas).
                    1.3. Identificar la estructura: Filas (números), Columnas (letras) y Celdas (intersección, ej. B4).
                    1.4. Navegar por la hoja usando teclado (flechas, Inicio, Ctrl+Inicio, Ctrl+Fin).
                    1.5. Desplazarse rápidamente entre hojas (Ctrl+Av Pág / Ctrl+Re Pág).
                    1.6. Personalizar la Barra de Herramientas de Acceso Rápido (agregar Guardar, Deshacer, Rehacer).
                    1.7. Ajustar el Zoom para ver más o menos celdas.
                    MÓDULO 2: GESTIÓN DE LIBROS Y HOJAS
                    2.1. Crear un libro nuevo en blanco y desde plantillas.
                    2.2. Guardar un libro (Ctrl+G / Cmd+G) y "Guardar como" en formatos (.xlsx, .xls, .csv, .pdf).
                    2.3. Abrir un libro existente desde el PC y desde la nube (OneDrive).
                    2.4. Insertar, eliminar, renombrar y cambiar el color de las pestañas de las hojas.
                    2.5. Mover o copiar una hoja a otro libro (arrastrar o clic derecho).
                    2.6. Recuperar un archivo no guardado (Auto-recuperación).
                    MÓDULO 3: INTRODUCCIÓN DE DATOS Y FORMATO BÁSICO
                    3.1. Escribir datos en una celda (texto, números y fechas).
                    3.2. Editar el contenido de una celda (F2 o doble clic).
                    3.3. Eliminar el contenido de una celda (Tecla Supr).
                    3.4. Aplicar formato de fuente (Negrita, Cursiva, Tamaño, Color).
                    3.5. Aplicar formatos numéricos esenciales: General, Número, Moneda ($), Fecha y Porcentaje (%).
                    3.6. Ajustar el ancho de columna y alto de fila (arrastrar o doble clic en el borde).
                    3.7. Combinar y centrar celdas (Merge & Center) y "Combinar a través".
                    3.8. Aplicar bordes y relleno de color a las celdas.
                    3.9. Usar el "Formato de número contable" vs "Moneda".
                    MÓDULO 4: MANEJO DE FILAS, COLUMNAS Y RANGOS
                    4.1. Seleccionar una celda, un rango (arrastrar o Shift+clic), una fila y una columna.
                    4.2. Seleccionar toda la hoja (Ctrl+E / Cmd+E o clic en la esquina superior izquierda).
                    4.3. Insertar y eliminar filas y columnas enteras.
                    4.4. Ocultar y mostrar filas y columnas.
                    4.5. Mover y copiar celdas (arrastrar con Ctrl o usando Copiar/Cortar y Pegar).
                    4.6. Usar "Pegado Especial": Pegar solo valores, solo formatos o solo fórmulas.
                    4.7. Usar el Relleno Rápido (Flash Fill) para separar o unir datos (ej. nombres y apellidos).
                    MÓDULO 5: FÓRMULAS BÁSICAS Y REFERENCIAS
                    5.1. Comprender la sintaxis de una fórmula: siempre empieza con = (ej. =A1+B1).
                    5.2. Realizar operaciones aritméticas: Suma (+), Resta (-), Multiplicación (*), División (/).
                    5.3. Usar el orden de las operaciones matemáticas (Paréntesis, Exponentes, Multiplicación/División, Suma/Resta).
                    5.4. Referencia Relativa: Copiar una fórmula y que las celdas se ajusten automáticamente.
                    5.5. Referencia Absoluta: Usar el símbolo 
                    5.6. Referencia Mixta: Fijar solo la fila ($A1) o solo la columna (A$1).
                    5.7. Ver, mostrar y ocultar las fórmulas en las celdas (Ctrl+Ñ / Ctrl+`).
                    MÓDULO 6: FUNCIONES ESENCIALES (NIVEL 1 - BÁSICO)
                    6.1. Usar la función SUMA (autosuma con botón ∑ o =SUMA(rango)).
                    6.2. Usar la función PROMEDIO (media aritmética).
                    6.3. Usar la función CONTAR (cuenta solo números) y CONTARA (cuenta todo excepto vacías).
                    6.4. Usar las funciones MÁXIMO y MÍNIMO (valor mayor y menor de un rango).
                    6.5. Usar la función SI (condicional simple: =SI(A1>10, "Aprobado", "Reprobado")).
                    6.6. Usar la función CONTAR.SI (contar celdas que cumplen una condición).
                    6.7. Usar la función SUMAR.SI (sumar celdas que cumplen una condición).
                    MÓDULO 7: FUNCIONES DE BÚSQUEDA Y REFERENCIA (NIVEL 2 - INTERMEDIO)
                    7.1. Usar la función BUSCARV (VLOOKUP) para buscar datos verticalmente en una tabla.
                    7.2. Usar la función BUSCARH (HLOOKUP) para buscar horizontalmente.
                    7.3. Dominar BUSCARX (XLOOKUP) - la función moderna que reemplaza a BUSCARV (buscar en cualquier dirección).
                    7.4. Usar la función INDICE y COINCIDIR (combinación clásica para búsquedas bidireccionales).
                    7.5. Usar la función SI.ERROR (para ocultar errores como #N/A o #¡DIV/0!).
                    MÓDULO 8: ORDENAR, FILTRAR Y VALIDACIÓN DE DATOS
                    8.1. Ordenar datos de forma ascendente y descendente (una columna).
                    8.2. Ordenar por varios niveles (ej. primero por Apellido, luego por Edad).
                    8.3. Aplicar filtros automáticos (AutoFiltro) para mostrar solo filas que cumplan condiciones.
                    8.4. Usar filtros avanzados por color, por texto y por números.
                    8.5. Quitar filtros y mostrar todos los datos de nuevo.
                    8.6. Usar la función "Quitar duplicados" para limpiar listas.
                    8.7. Crear una lista de validación de datos (menú desplegable para elegir opciones).
                    MÓDULO 9: TABLAS Y FORMATO DE TABLA (Ctrl+T)
                    9.1. Convertir un rango en Tabla (Ctrl+T / Cmd+T).
                    9.2. Identificar las ventajas de una Tabla (encabezados fijos, autofiltros, formato alternado).
                    9.3. Usar referencias estructuradas en fórmulas dentro de una Tabla (ej. [@Columna]).
                    9.4. Agregar una columna calculada (la fórmula se copia automáticamente).
                    9.5. Insertar filas de totales en la tabla (con funciones SUMAR, PROMEDIO, etc.).
                    9.6. Convertir una Tabla de nuevo a rango sin perder datos.
                    MÓDULO 10: GRÁFICOS Y VISUALIZACIÓN DE DATOS
                    10.1. Insertar un gráfico de columnas (barras verticales) y de barras (horizontales).
                    10.2. Insertar un gráfico de líneas (tendencias en el tiempo).
                    10.3. Insertar un gráfico circular (tarta) y un gráfico de anillos.
                    10.4. Cambiar el título del gráfico y los nombres de los ejes.
                    10.5. Modificar colores y estilos del gráfico.
                    10.6. Agregar etiquetas de datos y tabla de datos en el gráfico.
                    10.7. Mover el gráfico a una hoja nueva o incrustarlo en la misma hoja.
                    10.8. Crear Minigráficos (micrográficos dentro de una celda).
                    MÓDULO 11: TABLAS DINaÁMICAS (PIVOT TABLES) - AVANZADO
                    11.1. Crear una Tabla Dinámica a partir de un rango o Tabla (Insertar > Tabla dinámica).
                    11.2. Arrastrar campos a las zonas: Filas, Columnas, Valores y Filtros.
                    11.3. Cambiar el cálculo del campo de valor (Suma, Promedio, Conteo, Máx/Mín).
                    11.4. Agrupar fechas (por meses, trimestres, años) dentro de la tabla dinámica.
                    11.5. Actualizar una tabla dinámica cuando los datos origen cambian (clic derecho > Actualizar).
                    11.6. Insertar una Segmentación de datos (Slicer) para filtrar visualmente.
                    11.7. Insertar una Línea de tiempo (Timeline) para filtrar por fechas.
                    11.8. Dar formato y diseño profesional a la tabla dinámica.
                    MÓDULO 12: CONFIGURACIÓN DE PÁGINA E IMPRESIÓN
                    12.1. Configurar Márgenes, Orientación (Vertical/Horizontal) y Tamaño de papel.
                    12.2. Definir el Área de impresión (imprimir solo una selección).
                    12.3. Insertar Saltos de página manuales.
                    12.4. Repetir filas de encabezado en todas las páginas (Configurar página > Hoja > Filas a repetir).
                    12.5. Ajustar el contenido para que quepa en una sola página (Escalar / Ajustar a).
                    12.6. Ver la Vista previa de salto de página.
                    12.7. Agregar encabezados y pies de página personalizados (con fecha y número de página).
                    MÓDULO 13: SEGURIDAD, PROTECCIÓN Y COLABORACIÓN
                    13.1. Proteger una hoja para evitar que editen ciertas celdas.
                    13.2. Desbloquear celdas específicas para que el usuario solo edite esas (Bloquear vs Desbloquear).
                    13.3. Proteger un libro completo (para que no inserten ni eliminen hojas).
                    13.4. Compartir el libro para coautoría en tiempo real (OneDrive/SharePoint).
                    13.5. Insertar y responder comentarios y notas.
                    13.6. Guardar el archivo con contraseña de apertura.
                    MÓDULO 14: HERRAMIENTAS AVANZADAS Y POWER QUERY (BÁSICO)
                    14.1. Usar la función "Texto en columnas" para dividir datos (ej. separar nombre y apellido).
                    14.2. Usar la función "Quitar duplicados" en datos masivos.
                    14.3. Conectar Excel con Power Query (Obtener y transformar datos).
                    14.4. Importar datos desde un archivo CSV o TXT usando Power Query.
                    14.5. Realizar transformaciones básicas en Power Query (eliminar filas, cambiar tipo de dato).
                    14.6. Cargar los datos transformados de vuelta a una hoja o a una Tabla Dinámica.
                    MÓDULO 15: INTELIGENCIA ARTIFICIAL (COPILOT Y COMPLEMENTOS)
                    15.1. Usar Copilot en Excel (si está disponible) para crear fórmulas mediante lenguaje natural (ej. "dame el total de ventas").
                    15.2. Usar Ideas en Excel (Insights) para obtener análisis automáticos de los datos (tendencias, valores atípicos).
                    15.3. Insertar complementos (Add-ins) útiles como "People Graph" o "Power Map".
                    15.4. Generar gráficos o tablas dinámicas usando indicaciones de IA.
                    15.5. Pedir a Copilot que explique una fórmula compleja paso a paso.',
                'publicado' => true,
                'destacado' => true,
                'fecha_inicio' => now()->addDays(10)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],

            [
                'codigo' => 'CUR-'.date('Y').'-003',
                'nombre' => 'Microsoft PowerPoint Profesional',
                'slug' => 'microsoft-powerpoint-profesional',
                'descripcion' => 'Aprende a crear presentaciones atractivas, profesionales y comunicativas utilizando PowerPoint.',
                'cronograma' => 'Semana 1: Fundamentos de diseño | Semana 2: Plantillas y estilos | Semana 3: Animaciones y transiciones | Semana 4: Presentaciones efectivas',
                'requisitos' => 'Conocimientos básicos de informática.',
                'objetivos' => 'Crear diapositivas efectivas, aplicar diseño moderno, integrar multimedia y desarrollar presentaciones profesionales.',
                'materiales_incluidos' => 'Plantillas premium, ejercicios, videos, certificado',
                'cupo_total' => 30,
                'cupo_disponible' => 25,
                'duracion_horas' => 25,
                'duracion_texto' => '3 semanas',
                'precio_regular' => 130.00,
                'nivel' => 'principiante',
                'imagen_portada' => 'cursos/img curso.png',
                'enlace_classroom' => 'https://classroom.google.com/excel',
                'temario' => 'Módulo 1: Fundamentos del Diseño Profesional
                    1.1. Planificar la estructura narrativa (storyboard).
                    1.2. Aplicar principios de jerarquía, contraste y espacio en blanco.
                    1.3. Seleccionar paletas de color y tipografías adecuadas.
                    1.4. Identificar y corregir errores comunes (exceso de texto, malas animaciones).
                    Módulo 2: Patrón y Plantillas Personalizadas
                    2.1. Editar el Patrón de diapositivas (Slide Master) globalmente.
                    2.2. Personalizar los patrones de Notas y Documentos.
                    2.3. Crear una plantilla corporativa con logotipo y colores propios.
                    2.4. Guardar temas personalizados (colores, fuentes y efectos).
                    Módulo 3: Diseño Visual y Gestión de Objetos
                    3.1. Alinear, agrupar y manejar capas (enviar al fondo / traer al frente).
                    3.2. Aplicar rellenos degradados, texturas y transparencias a formas.
                    3.3. Editar imágenes (eliminar fondo, ajustar brillo/contraste).
                    3.4. Insertar iconos SVG y modelos 3D.
                    3.5. Gestionar objetos complejos usando el Panel de Selección.
                    Módulo 4: SmartArt, Tablas y Gráficos Avanzados
                    4.1. Crear diagramas de flujo y jerarquías con SmartArt.
                    4.2. Personalizar colores y formas dentro del SmartArt.
                    4.3. Diseñar tablas profesionales con formato condicional.
                    4.4. Insertar gráficos vinculados a Excel (actualización automática).
                    4.5. Crear infografías combinando formas, iconos y texto.
                    Módulo 5: Animaciones y Transiciones Avanzadas
                    5.1. Dominar el Panel de Animación (orden, duración y retraso).
                    5.2. Crear trayectorias de movimiento personalizadas.
                    5.3. Usar la transición Morph (transformación suave entre diapositivas).
                    5.4. Superponer varias animaciones en un mismo objeto.
                    Módulo 6: Multimedia e Interactividad
                    6.1. Insertar audio (narración, ajuste de volumen y fundidos).
                    6.2. Insertar y editar video (recorte y marcadores).
                    6.3. Grabar la pantalla para hacer tutoriales dentro de PowerPoint.
                    6.4. Crear hipervínculos y botones de acción (navegación no lineal).
                    6.5. Usar la función Zoom de diapositiva para presentaciones modulares.
                    Módulo 7: Integración y Automatización
                    7.1. Vincular tablas y gráficos dinámicos desde Excel.
                    7.2. Insertar objetos mediante Vinculación e Incrustación (OLE).
                    7.3. Instalar y usar complementos (Add-ins) como Office Timeline.
                    7.4. Grabar macros para automatizar tareas repetitivas.
                    7.5. Exportar la presentación a video (MP4) y a imágenes (JPG/PNG).
                    Módulo 8: Colaboración y Trabajo en Equipo
                    8.1. Editar en coautoría en tiempo real con OneDrive/Teams.
                    8.2. Insertar, responder y resolver comentarios.
                    8.3. Comparar y combinar diferentes versiones de una presentación.
                    8.4. Proteger con contraseña (apertura y modificación).
                    Módulo 9: Preparación y Exposición Profesional
                    9.1. Usar el Speaker Coach (Entrenador de orador) para ensayar.
                    9.2. Configurar la Vista del presentador con notas y temporizador.
                    9.3. Ensayar con temporizador y ajustar los tiempos.
                    9.4. Activar subtítulos y traducción en vivo durante la charla.
                    Módulo 10: Inteligencia Artificial (Copilot y Designer)
                    10.1. Usar PowerPoint Designer para sugerencias de diseño automáticas.
                    10.2. Crear una presentación completa desde un prompt con Copilot.
                    10.3. Usar Idea Coach para planificar la narrativa.
                    10.4. Generar resúmenes ejecutivos de la presentación con IA.
                    10.5. Verificar y editar el contenido generado por IA (buenas prácticas).',
                'publicado' => true,
                'destacado' => false,
                'fecha_inicio' => now()->addDays(14)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],

            [
                'codigo' => 'CUR-'.date('Y').'-004',
                'nombre' => 'Informática Básica para Todos',
                'slug' => 'informatica-basica-para-todos',
                'descripcion' => 'Curso completo para aprender el uso del computador, manejo de archivos, internet, correo electrónico y herramientas esenciales.',
                'cronograma' => 'Semana 1: Partes del computador y sistema operativo | Semana 2: Archivos y carpetas | Semana 3: Internet y seguridad | Semana 4: Herramientas básicas de productividad',
                'requisitos' => 'No se necesitan conocimientos previos.',
                'objetivos' => 'Operar un computador correctamente, navegar en internet de forma segura, usar herramientas básicas y gestionar archivos.',
                'materiales_incluidos' => 'Guías prácticas, videos, ejercicios, certificado',
                'cupo_total' => 20,
                'cupo_disponible' => 18,
                'duracion_horas' => 35,
                'duracion_texto' => '4 semanas',
                'precio_regular' => 100.00,
                'nivel' => 'principiante',
                'imagen_portada' => 'cursos/informatica.png',
                'enlace_classroom' => 'https://classroom.google.com/excel',
                'temario' => 'Módulo 1: Hardware y Conceptos Fundamentales
                    1.1. Diferenciar Dato vs. Información.
                    1.2. Identificar las partes internas del PC (CPU, RAM, HDD/SSD, Fuente).
                    1.3. Clasificar periféricos de entrada, salida y mixtos.
                    1.4. Comprender las unidades de medida (Bit, Byte, KB, MB, GB, TB).
                    1.5. Realizar el encendido y apagado correcto del equipo.
                    Módulo 2: Software y Sistema Operativo (Windows/macOS)
                    2.1. Manejar el Escritorio, Barra de Tareas y Menú Inicio.
                    2.2. Gestionar ventanas (abrir, cerrar, minimizar y cambiar entre ellas con Alt+Tab).
                    2.3. Navegar por el Explorador de Archivos / Finder (carpetas y unidades).
                    2.4. Realizar operaciones con archivos (crear, copiar, cortar, pegar, mover y eliminar).
                    2.5. Identificar extensiones de archivo (.docx, .pdf, .jpg, .mp3).
                    2.6. Instalar y desinstalar programas básicos.
                    Módulo 3: Internet y Navegación Web
                    3.1. Diferenciar entre Internet, Navegador y Buscador.
                    3.2. Realizar búsquedas avanzadas en Google (comillas, site:, filetype:).
                    3.3. Gestionar Marcadores / Favoritos.
                    3.4. Descargar archivos de internet de forma segura.
                    3.5. Comprender qué es y qué no es la navegación de incógnito.
                    Módulo 4: Correo Electrónico (Gmail/Outlook)
                    4.1. Crear una cuenta de correo segura y profesional.
                    4.2. Comprender la estructura (Para, CC, CCO, Asunto y Firma).
                    4.3. Redactar, responder, reenviar y eliminar correos.
                    4.4. Adjuntar archivos y descargar adjuntos recibidos.
                    4.5. Aplicar normas de Netiqueta básica.
                    Módulo 5: Seguridad Informática y Privacidad
                    5.1. Crear contraseñas robustas y entender la autenticación 2FA.
                    5.2. Identificar peligros (Phishing, malware, ransomware).
                    5.3. Configurar y actualizar el antivirus/Windows Defender.
                    5.4. Realizar copias de seguridad (Backups) en la nube y en disco externo.
                    Módulo 6: Almacenamiento en la Nube y Móviles
                    6.1. Sincronizar PC y móvil (Google Drive / OneDrive / iCloud).
                    6.2. Subir, descargar y compartir archivos con enlaces desde la nube.
                    6.3. Escanear documentos usando el móvil.',
                'publicado' => true,
                'destacado' => false,
                'fecha_inicio' => now()->addDays(5)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::updateOrCreate(
                ['codigo' => $curso['codigo']], // Buscar por codigo único
                $curso
            );
        }

        $this->command->info('✅ Cursos de prueba creados/actualizados exitosamente.');

        // Inscribir estudiantes en algunos cursos
        $this->inscribirEstudiantesEnCursos();
    }

    private function inscribirEstudiantesEnCursos()
    {
        $estudiantes = Estudiante::with('user')->get();
        $cursos = Curso::all();

        foreach ($estudiantes as $estudiante) {
            // Inscribir cada estudiante en 1-3 cursos aleatorios
            $cursosAleatorios = $cursos->random(rand(1, 3));

            foreach ($cursosAleatorios as $curso) {
                // Verificar que no esté ya inscrito y que haya cupo
                if (! $estudiante->cursos()->where('curso_id', $curso->codigo)->exists() &&
                    $curso->cupo_disponible > 0) {

                    $estudiante->cursos()->attach($curso->codigo, [
                        'estado' => rand(0, 1) ? 'inscrito' : 'en_progreso',
                        'progreso' => rand(0, 100),
                        'pago_realizado' => $curso->precio_descuento ?? $curso->precio_regular,
                        'estado_pago' => 'completo',
                        'fecha_inscripcion' => now()->subDays(rand(1, 30)),
                    ]);

                    // Actualizar cupo disponible
                    $curso->decrement('cupo_disponible');
                }
            }
        }

        $this->command->info('✅ Estudiantes inscritos en cursos exitosamente.');
    }
}
