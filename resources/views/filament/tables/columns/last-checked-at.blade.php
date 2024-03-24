<span data-local-date="{{ $getState() }}" class="fi-ta-text-item-label text-sm leading-6" style="color: rgb(var(@if(\Carbon\Carbon::parse($getState())->diffInMinutes(\Carbon\Carbon::now()) > 5)--danger-500 @else --gray-950 @endif));">
    {{ $getState() }}
</span>

