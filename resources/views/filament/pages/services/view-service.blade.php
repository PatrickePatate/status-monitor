<x-filament-panels::page>
    <p class="mt-0" >{{$this->data['description']??'Aucune description'}}</p>


    <div class=" bg-white rounded-lg shadow-sm">
        <div class="fi-section-content p-6">
            <p class="grid flex-1 text-sm text-gray-500 dark:text-white mb-2">
                Statut du service
            </p>
            <div class="flex items-center gap-3">
                <div class="flex-1 flex items-center gap-3"
                @switch($data['status'])
                    @case('AVAILABLE')
                        <x-filament::icon
                            icon="far-face-smile"
                            class="h-10 w-10"
                            style="color: rgb(var(--success-500));"
                        />
                        <span class="text-xl" style="color: rgb(var(--success-500));">
                            {{\App\Enums\ServiceStatus::from($data['status'])->label()}}
                        </span>
                        @break
                    @case('PARTIAL')

                        <x-filament::icon
                            icon="far-face-meh"
                            class="h-10 w-10"
                            style="color: rgb(var(--warning-500));"
                        />
                        <span class="text-xl" style="color: rgb(var(--warning-500));">
                            {{\App\Enums\ServiceStatus::from($data['status'])->label()}}
                        </span>
                        @break
                    @case('OUTAGE')
                        <x-filament::icon
                            icon="far-face-dizzy"
                            class="h-10 w-10"
                            style="color: rgb(var(--danger-500));"
                        />
                        <span class="text-xl" style="color: rgb(var(--danger-500));">
                            {{\App\Enums\ServiceStatus::from($data['status'])->label()}}
                        </span>

                        @break
                    @case('MAINTENANCE')
                        <x-filament::icon
                            icon="fas-hammer"
                            class="h-10 w-10"
                            style="color: rgb(var(--gray-500));"
                        />
                        <span class="text-xl" style="color: rgb(var(--gray-500));">
                            {{\App\Enums\ServiceStatus::from($data['status'])->label()}}
                        </span>

                        @break
                @endswitch
                </div>
                <livewire:set-service-status-form class="align-end" :service="$this->data['id']" />
            </div>
        </div>
    </div>
    <div class="fi-section-content p-6">
        <livewire:view-service-display-metrics :service="$this->data['id']" />
    </div>
    <div class="fi-section-content p-6">
        <livewire:view-service-display-last-errors :service="$this->data['id']" />
    </div>
    @push('styles')
        @vite(['resources/scss/page.scss'])
        <style>
            .service-availability-infos{
                display: flex;
                justify-content: space-between;
            }
            .metrics-settings-wrapper{
                margin-bottom: 2em;
            }
        </style>
    @endpush
    @push('scripts')
        @vite(['resources/js/page.js'])
    @endpush
</x-filament-panels::page>
