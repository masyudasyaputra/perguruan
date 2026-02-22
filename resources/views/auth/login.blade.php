<x-guest-layout>
    {{-- Wrapper utama untuk centering sempurna --}}
    <div class="min-h-[80vh] flex flex-col justify-center items-center w-full px-4">
        
        {{-- Card Form --}}
        <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-[2.5rem] border border-slate-200 shadow-xl shadow-slate-200/50">
            
            <div class="mb-8 text-center">
                {{-- Logo --}}
                <div class="flex justify-center mb-6">
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-indigo-600 drop-shadow-sm" />
                    </a>
                </div>
                
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Selamat Datang</h2>
                <p class="text-sm text-slate-500 mt-2 font-medium">Masuk dengan Email atau WhatsApp</p>
            </div>

            <x-auth-session-status class="mb-4 p-4 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 text-sm font-medium" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Input Login (Email atau WA) --}}
                <div class="space-y-1">
                    <x-input-label for="login" :value="__('Email atau No. WhatsApp')" class="text-xs font-bold uppercase ml-1 text-slate-500" />
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <x-text-input id="login" class="block w-full pl-11 !rounded-2xl border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-indigo-500/20" 
                            type="text" 
                            name="login" 
                            :value="old('login')" 
                            required 
                            autofocus 
                            placeholder="Email atau 0812xxxx" />
                    </div>
                    {{-- Menampilkan error untuk kedua kemungkinan key --}}
                    <x-input-error :messages="$errors->get('login')" class="mt-1" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-1" />
                </div>

                {{-- Password --}}
                <div class="space-y-1">
                    <div class="flex items-center justify-between px-1">
                        <x-input-label for="password" :value="__('Kata Sandi')" class="text-xs font-bold uppercase text-slate-500" />
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-text-input id="password" class="block w-full pl-11 !rounded-2xl border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-indigo-500/20" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password" 
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center px-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-slate-600 font-medium">{{ __('Ingat saya') }}</span>
                    </label>
                </div>

                {{-- Tombol Masuk --}}
                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-indigo-100 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>{{ __('Masuk Sekarang') }}</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </div>

                <div class="text-center mt-6">
                    <p class="text-sm text-slate-500 font-medium">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-indigo-600 font-black hover:underline ml-1">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>