<div class="flex flex-col items-center gap-6 w-full">

    @foreach($cursos as $curso)
        <div class="w-full max-w-3xl">
            <x-curso-card
                :nombre="$curso->nombre"
                :descripcion="$curso->descripcion"
                :cronograma="$curso->cronograma"
                :requisitos="$curso->requisitos"
                :cupo="$curso->cupo"
            />
        </div>
    @endforeach

</div>
