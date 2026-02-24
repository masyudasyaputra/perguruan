<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistem Informasi Data Perguruan Karate</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Menggunakan CSS internal dari template asli yang Anda berikan */
            /* ... (Style Tailwind v4 yang Anda kirim tetap berlaku) ... */
        </style>
    @endif
</head>

<body
    class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col uppercase tracking-wide">
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Dashboard
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-block px-5 py-1.5 dark:text-[#f43] border-[#19140035] hover:border-[#f53003] border text-[#f53003] dark:border-[#3E3E3A] dark:hover:border-[#f43] rounded-sm text-sm leading-normal cursor-pointer transition-all">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            Daftar Anggota
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <div
        class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
            <div
                class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                <div class="mb-6">
                    <h1 class="mb-1 font-medium text-lg">Sistem Informasi Karate</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Pengelolaan data terpusat untuk standarisasi
                        ujian & manajemen organisasi perguruan.</p>
                </div>

                <h2 class="mb-2 font-medium">Modul Pengelolaan:</h2>
                <ul class="flex flex-col mb-4 lg:mb-6">
                    <li
                        class="flex items-start gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                        <span class="relative py-1 bg-white dark:bg-[#161615] pl-2">
                            <strong>Manajemen PB & Wilayah:</strong> Sinkronisasi data dari Pengurus Besar hingga Dojo.
                        </span>
                    </li>
                    <li
                        class="flex items-start gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                        <span class="relative py-1 bg-white dark:bg-[#161615] pl-2">
                            <strong>Sistem Ujian Kenaikan Tingkat:</strong> Penilaian transparan oleh tim Penguji
                            berlisensi.
                        </span>
                    </li>
                    <li
                        class="flex items-start gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                        <span class="relative py-1 bg-white dark:bg-[#161615] pl-2">
                            <strong>Database Sabuk & Ijazah:</strong> Rekam jejak digital riwayat tingkatan anggota
                            (Kyu/Dan).
                        </span>
                    </li>
                </ul>

                <div
                    class="mt-4 p-4 bg-[#fdfdfc] dark:bg-[#0a0a0a] border border-[#19140035] dark:border-[#3E3E3A] rounded-sm">
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">Integrasi: <span
                            class="text-black dark:text-white font-medium italic">DOJO - CABANG - PROVINSI -
                            PUSAT</span></p>
                </div>
            </div>

            <div
                class="relative flex-1 p-6 lg:p-20 bg-[#FDFDFC] dark:bg-[#1b1b18] border-[#19140035] dark:border-[#3E3E3A] border-t lg:border-t-0 lg:border-l rounded-tr-lg lg:rounded-tr-lg lg:rounded-bl-none flex flex-col justify-center">
                <div class="space-y-4">
                    <div class="h-2 w-16 bg-black dark:bg-white rounded-full"></div>
                    <h2 class="text-2xl font-semibold leading-tight tracking-tighter">OSS: ONE SYSTEM SOLUTION FOR
                        KARATE ORGANIZATION.</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] normal-case leading-relaxed">Sistem ini memastikan
                        setiap data anggota dan hasil penilaian ujian tersimpan dengan aman dan akuntabel.</p>

                    @guest
                        <div class="pt-4">
                            <a href="{{ route('login') }}"
                                class="text-[#1b1b18] dark:text-white underline underline-offset-4 font-medium hover:text-[#f53003]">Akses
                                Portal Utama &rarr;</a>
                        </div>
                    @endguest
                </div>
            </div>
        </main>
    </div>

    <footer class="mt-12 text-[11px] text-[#706f6c] dark:text-[#A1A09A]">
        &copy; 2026 DIGITAL KARATE MANAGEMENT SYSTEM
    </footer>
</body>

</html>
