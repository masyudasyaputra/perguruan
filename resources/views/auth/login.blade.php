{{-- File: resources/views/auth/login.blade.php --}}
<x-guest-layout>
    {{-- Di mobile padding dikecilkan (py-4), di desktop tetap (md:py-10) --}}
    <div class="px-6 py-4 md:px-12 md:py-10 flex flex-col justify-center min-h-full">

        {{-- Header Form - Margin dikurangi di mobile (mb-4) --}}
        <div class="mb-4 md:mb-10 text-center shrink-0">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 text-white rounded-full text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] mb-2 md:mb-4">
                <span class="w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse shadow-[0_0_5px_#dc2626]"></span>
                Secure Access
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-white tracking-tighter uppercase leading-none">
                Portal <span class="gold-gradient-text">Masuk</span>
            </h2>
            <div class="h-1 w-10 bg-gradient-to-r from-[#bf953f] to-transparent mx-auto mt-2 md:mt-4 rounded-full"></div>
        </div>

        {{-- Status Sesi --}}
        @if (session('status'))
            <x-auth-session-status
                class="mb-4 p-3 rounded-xl bg-white/5 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold uppercase tracking-widest text-center backdrop-blur-sm"
                :status="session('status')" />
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-3 md:space-y-6">
            @csrf

            {{-- Input Login --}}
            <div class="space-y-1 md:space-y-2">
                <x-input-label for="login" :value="__('Identitas')"
                    class="text-[9px] md:text-[10px] font-black uppercase tracking-widest ml-1 text-slate-500" />
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-[#bf953f] transition-colors duration-300">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <x-text-input id="login"
                        class="block w-full pl-10 md:pl-12 py-3 md:py-4 !rounded-xl md:!rounded-2xl border-white/5 bg-white/5 focus:bg-white/10 focus:border-[#bf953f]/50 focus:ring focus:ring-[#bf953f]/5 transition-all duration-300 text-xs md:text-sm font-semibold text-white placeholder:text-slate-600"
                        type="text" name="login" :value="old('login')" required autofocus
                        placeholder="Email / WhatsApp" />
                </div>
                <x-input-error :messages="$errors->get('login')" class="mt-1 text-[9px] font-bold uppercase text-red-500 px-1" />
            </div>

            {{-- Password --}}
            <div class="space-y-1 md:space-y-2">
                <div class="flex items-center justify-between px-1">
                    <x-input-label for="password" :value="__('Sandi Akses')"
                        class="text-[9px] md:text-[10px] font-black uppercase tracking-widest text-slate-500" />
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-[9px] font-black uppercase tracking-widest text-[#bf953f] hover:text-white transition-colors">
                            Lupa?
                        </a>
                    @endif
                </div>
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-[#bf953f] transition-colors duration-300">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <x-text-input id="password"
                        class="block w-full pl-10 md:pl-12 py-3 md:py-4 !rounded-xl md:!rounded-2xl border-white/5 bg-white/5 focus:bg-white/10 focus:border-[#bf953f]/50 focus:ring focus:ring-[#bf953f]/5 transition-all duration-300 text-xs md:text-sm font-semibold text-white placeholder:text-slate-600"
                        type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 px-1 text-[9px] font-bold uppercase text-red-500" />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between px-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-white/10 bg-white/5 text-red-700 shadow-sm focus:ring-red-700/20 w-3 h-3 md:w-4 md:h-4 transition-all cursor-pointer"
                        name="remember">
                    <span
                        class="ms-2 text-[9px] md:text-[10px] text-slate-500 font-black uppercase tracking-widest group-hover:text-slate-300 transition-colors">{{ __('Tetap Masuk') }}</span>
                </label>
            </div>

            {{-- Tombol Masuk --}}
            <div class="pt-2 md:pt-4">
                <button type="submit"
                    class="w-full py-3 md:py-4 bg-gradient-to-r from-red-800 to-red-950 hover:from-red-700 hover:to-red-900 text-white rounded-xl md:rounded-2xl font-black uppercase tracking-[0.3em] text-[9px] md:text-[10px] shadow-2xl border border-white/5 transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-3 group">
                    <span>{{ __('Otorisasi Masuk') }}</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </div>

            {{-- Register Link --}}
            <div class="text-center mt-4 md:mt-8">
                <p class="text-[9px] md:text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                    Belum Terdaftar?
                    <a href="{{ route('register') }}"
                        class="gold-gradient-text font-black ml-1 transition-colors hover:brightness-150">
                        Buat Akun
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
