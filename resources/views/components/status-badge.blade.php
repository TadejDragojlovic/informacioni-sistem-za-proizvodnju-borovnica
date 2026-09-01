@props([
    'active' => false,
    'activeLabel' => 'Aktivan',
    'inactiveLabel' => 'Neaktivan',
])

<span @class([
    'inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide',
    'bg-emerald-100 text-emerald-800' => $active,
    'bg-gray-200 text-gray-700' => ! $active,
])>
    {{ $active ? $activeLabel : $inactiveLabel }}
</span>
