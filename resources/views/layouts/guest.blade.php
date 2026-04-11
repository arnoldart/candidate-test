<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=merriweather:700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#FAFAFA]">
            <div>
                <a href="/" class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-[#3b7e5c] rounded-lg items-center justify-center flex text-white">
                        <i class="fa-solid fa-layer-group text-xl"></i>
                    </div>
                    <div class="flex flex-col uppercase tracking-wide leading-tight">
                        <span class="text-[17px] font-bold text-gray-900 tracking-normal leading-none">CLT Layup</span>
                        <span class="text-[10px] font-medium text-gray-500 tracking-widest mt-0.5 leading-none">MANAGER</span>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
