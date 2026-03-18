@foreach ($items as $item)
    @php
        $padding = 0.75 + ($level * 0.75);
    @endphp

    <a
        href="{{ $item->url }}"
        class="block rounded-lg py-2 text-sm text-zinc-700 hover:bg-amber-50 hover:text-amber-800"
        style="padding-left: {{ $padding }}rem;"
    >
        {{ $item->label }}
    </a>

    @if ($item->children->isNotEmpty())
        <div class="space-y-1 border-l border-amber-100/80 ml-2">
            @include('partials.navigation.desktop-branch', [
                'items' => $item->children,
                'level' => $level + 1,
            ])
        </div>
    @endif
@endforeach
