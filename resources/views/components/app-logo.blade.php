@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Parenthood ART Bank" {{ $attributes }}>
    </flux:sidebar.brand>
@else
    <flux:brand name="Parenthood ART Bank" {{ $attributes }}>
    </flux:brand>
@endif
