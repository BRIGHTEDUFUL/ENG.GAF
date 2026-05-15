<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-gaf-blue to-gaf-sky text-white font-semibold text-sm rounded-xl shadow-gaf hover:from-gaf-navy hover:to-gaf-blue hover:shadow-gaf-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 transition-all duration-200']) }}>
    {{ $slot }}
</button>
