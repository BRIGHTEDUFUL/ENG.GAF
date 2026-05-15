@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-white border border-sky-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 transition-all duration-200 focus:border-gaf-sky focus:ring-2 focus:ring-sky-100 focus:outline-none disabled:bg-sky-50 disabled:text-gray-500']) }}>
