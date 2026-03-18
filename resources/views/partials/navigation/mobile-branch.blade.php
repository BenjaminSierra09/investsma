@foreach ($items as $item)
    @php
        $padding = 0.75 + ($level * 0.75);
        $isTopLevel = $level === 0;
    @endphp

    <a
        href="{{ $item->url }}"
        class="block rounded-lg py-2 text-sm hover:bg-amber-50 {{ $isTopLevel ? 'font-semibold text-zinc-700' : 'text-zinc-500' }}"
        style="padding-left: {{ $padding }}rem;"
    >
        {{ $item->label }}
    </a>

    @if ($item->children->isNotEmpty())
        <div class="space-y-1 border-l border-amber-100/80 ml-2">
            @include('partials.navigation.mobile-branch', [
                'items' => $item->children,
                'level' => $level + 1,
            ])
        </div>
    @endif
@endforeach
