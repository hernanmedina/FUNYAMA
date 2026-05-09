``` mermaid

flowchart TD
    %% ====== CLIENTES ======
    subgraph Clientes ["Clientes"]
        browser["Navegador Web"]
    end

    %% ====== PRESENTACIÓN ======
    subgraph Presentacion ["Capa de Presentación"]
        livewire_ui["Componentes Livewire<br>(UI dinámica)"]
        blade_views["Vistas Blade<br>(Layouts y templates)"]
    end

    %% ====== APLICACIÓN ======
    subgraph Aplicacion ["Capa de Aplicación (Laravel)"]
        routes["Rutas (web/api)"]
        controllers["Controladores"]
        middleware["Middleware de roles"]
        services["Servicios / Lógica de negocio"]
        commands["Comandos Artisan<br>(Certificados)"]
        auth["Autenticación (Fortify/Jetstream)"]
    end

    %% ====== DOMINIO ======
    subgraph Dominio ["Capa de Dominio"]
        models["Modelos Eloquent<br>(User, Curso, etc.)"]
    end

    %% ====== DATOS ======
    subgraph Datos ["Capa de Datos"]
        db["MariaDB"]
        migrations["Migraciones"]
    end

    %% ====== INFRAESTRUCTURA ======
    subgraph Infraestructura ["Infraestructura"]
        docker["Docker / Docker Compose"]
        jenkins["Jenkins (CI/CD)"]
    end

    %% ====== RELACIONES ======
    browser --> livewire_ui
    browser --> blade_views

    livewire_ui --> routes
    blade_views --> routes

    routes --> middleware
    routes --> controllers

    controllers --> services
    controllers --> models

    middleware --> auth
    auth --> models

    services --> models
    commands --> models

    models --> db
    migrations --> db

    docker --> db
    docker --> routes

    jenkins --> docker

    %% ====== ESTILOS PASTEL ======
    classDef clientesStyle fill:#F1F8E9,stroke:#AED581,stroke-width:2px,color:#33691E
    classDef presentacionStyle fill:#E3F2FD,stroke:#64B5F6,stroke-width:2px,color:#0D47A1
    classDef aplicacionStyle fill:#E0F7FA,stroke:#4DD0E1,stroke-width:2px,color:#006064
    classDef dominioStyle fill:#FCE4EC,stroke:#F48FB1,stroke-width:2px,color:#880E4F
    classDef datosStyle fill:#FFF3E0,stroke:#FFB74D,stroke-width:2px,color:#E65100
    classDef infraStyle fill:#EDE7F6,stroke:#9575CD,stroke-width:2px,color:#311B92
    classDef nodeStyle fill:#FAFAFA,stroke:#B0BEC5,stroke-width:1.5px,color:#37474F

    class browser clientesStyle
    class livewire_ui,blade_views presentacionStyle
    class routes,controllers,middleware,services,commands,auth aplicacionStyle
    class models dominioStyle
    class db,migrations datosStyle
    class docker,jenkins infraStyle