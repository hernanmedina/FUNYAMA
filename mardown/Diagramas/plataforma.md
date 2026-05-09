``` mermaid

flowchart TD
    subgraph Platform ["Plataforma"]
        docker["Stack Docker<br>entorno de ejecución<br>[docker-compose.yml]"]
        build["Build Frontend<br>pipeline de assets<br>[app.js]"]
        ci["CI/CD<br>despliegue"]
    end

    subgraph StudentArea ["Área de Estudiantes"]
        student_dashboard["Panel estudiante<br>pantalla principal"]
        student_courses["Mis cursos<br>pantalla estudiante<br>[MisCursos.php]"]
        student_certs["Mis certificados<br>pantalla estudiante"]
        students_admin["Administración estudiantes<br>gestión<br>[Estudiantes.php]"]
    end

    subgraph AdminArea ["Área de Administración"]
        admin_dashboard["Panel administrador<br>pantalla admin<br>[DashboardAdmin.php]"]
        admin_courses["Gestión de cursos<br>admin cursos<br>[IndexCursos.php]"]
        admin_events["Gestión de eventos<br>admin eventos<br>[IndexEventos.php]"]
        event_calendar["Calendario de eventos<br>vista calendario"]
    end

    subgraph LaravelApp ["Aplicación Laravel"]
        public_index["Entrada principal<br>HTTP entry<br>[index.php]"]
        web_routes["Rutas web<br>mapa de rutas<br>[web.php]"]
        api_routes["Rutas API<br>mapa de rutas<br>[api.php]"]
        role_middleware["Control de roles<br>middleware<br>[RoleMiddleware.php]"]
        course_controller["Controlador Curso"]
        fortify_actions["Acciones Fortify<br>autenticación<br>[CreateNewUser.php]"]
        jetstream_delete["Eliminar usuario<br>acción cuenta<br>[DeleteUser.php]"]
        livewire_base["Interfaz Livewire<br>UI servidor<br>[Cursos.php]"]
        layouts["Plantillas<br>layout vistas<br>[AppLayout.php]"]
        cert_command["Generador certificados<br>comando artisan"]
    end

    subgraph DomainData ["Datos del Dominio"]
        user_model["Usuario<br>modelo auth<br>[User.php]"]
        domain_models["Modelos dominio<br>entidades<br>[Curso.php]"]
        migrations["Esquema<br>migraciones"]
    end

    %% Conexiones
    public_index -->|inicializa| web_routes
    web_routes -->|protege| role_middleware
    web_routes -->|dirige a| course_controller
    web_routes -->|monta| livewire_base
    api_routes -->|flujo auth| fortify_actions
    livewire_base -->|renderiza en| layouts
    fortify_actions -->|modifica| user_model
    jetstream_delete -->|elimina| user_model

    admin_dashboard -->|navega a| admin_courses
    admin_dashboard -->|navega a| admin_events
    admin_events -->|muestra| event_calendar

    student_dashboard -->|muestra| student_courses
    student_dashboard -->|muestra| student_certs
    students_admin -->|protegido por| role_middleware

    admin_courses -->|gestiona| domain_models
    students_admin -->|gestiona| domain_models
    student_courses -->|consulta| domain_models
    student_certs -->|consulta| domain_models
    domain_models -->|persistidos por| migrations
    cert_command -->|genera| domain_models
    docker -->|inicializa| migrations
    build -->|soporta| livewire_base
    ci -->|empaqueta| docker

    %% Estilos pastel
    classDef platformStyle fill:#E3F2FD,stroke:#64B5F6,stroke-width:2px,color:#1A237E
    classDef studentStyle fill:#E8F5E9,stroke:#81C784,stroke-width:2px,color:#1B5E20
    classDef adminStyle fill:#FFF8E1,stroke:#FFD54F,stroke-width:2px,color:#F57F17
    classDef laravelStyle fill:#E0F7FA,stroke:#4DD0E1,stroke-width:2px,color:#006064
    classDef domainStyle fill:#FCE4EC,stroke:#F48FB1,stroke-width:2px,color:#880E4F
    classDef nodeStyle fill:#FAFAFA,stroke:#B0BEC5,stroke-width:1.5px,color:#37474F

    class docker,build,ci platformStyle
    class student_dashboard,student_courses,student_certs,students_admin studentStyle
    class admin_dashboard,admin_courses,admin_events,event_calendar adminStyle
    class public_index,web_routes,api_routes,role_middleware,course_controller,fortify_actions,jetstream_delete,livewire_base,layouts,cert_command laravelStyle
    class user_model,domain_models,migrations domainStyle