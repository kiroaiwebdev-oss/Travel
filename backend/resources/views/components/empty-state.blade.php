@props(['icon' => 'inbox', 'text' => 'Nothing here yet.', 'title' => null])
<div class="text-center py-10">
    <div class="mx-auto w-12 h-12 grid place-items-center rounded-2xl bg-slate-100 text-muted mb-3">
        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
    </div>
    @if ($title)<p class="font-semibold">{{ $title }}</p>@endif
    <p class="text-sm text-muted">{{ $text }}</p>
</div>
