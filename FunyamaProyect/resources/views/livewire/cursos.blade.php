<div class="flex flex-col items-center gap-6 w-full">

    @foreach($cursos as $curso)
        <div class="w-full max-w-3xl">
            <x-curso-card
                :curso="$curso"
            />
        </div>
    @endforeach

</div>
