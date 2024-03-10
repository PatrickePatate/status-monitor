<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section>
        <p
            class="grid flex-1 text-sm  text-gray-500 dark:text-white"
        >
            Statut général des services
        </p>

        <h1 class="leading-6 font-semibold dark:text-gray-400" style="color: rgb(var(--{{$global_status->color()}}-500));">
            {{$global_status->label()}}
        </h1>

    </x-filament::section>
</x-filament-widgets::widget>
