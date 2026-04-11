@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 bg-white text-gray-900 focus:border-[#447A60] focus:ring-[#447A60] rounded-md shadow-sm']) }}>
