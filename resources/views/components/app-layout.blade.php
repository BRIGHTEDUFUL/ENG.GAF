@props(['heading' => null])

<x-layouts.app :heading="$heading">
    {{ $slot }}
</x-layouts.app>
