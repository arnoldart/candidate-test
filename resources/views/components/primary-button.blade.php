<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#447A60] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#36614D] focus:bg-[#36614D] active:bg-[#2D5040] focus:outline-none focus:ring-2 focus:ring-[#447A60] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
