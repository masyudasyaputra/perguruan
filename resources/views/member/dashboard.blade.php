@php
    // QR LOCAL (ANTI CORS) => hasilnya aman untuk html2canvas + toDataURL
    // Pastikan package: simplesoftwareio/simple-qrcode
    $qrPngBase64 = base64_encode(
        QrCode::format('png')
            ->size(120)
            ->margin(0)
            ->generate(url('/member/' . $user->id)),
    );
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl md:text-2xl text-slate-800 tracking-tight uppercase italic">
            Digital ID Member
        </h2>
    </x-slot>

    <div x-data="{
        showPreview: false,
        photoScale: 100,
        nameSize: 18,
        verticalGap: 24,
        cardColor: '#dc2626',
        isDownloading: false,
    
        waitImagesLoaded(rootEl) {
            const imgs = Array.from(rootEl.querySelectorAll('img'));
            return Promise.all(
                imgs.map((img) => {
                    if (img.complete && img.naturalWidth > 0) return Promise.resolve();
                    return new Promise((resolve) => {
                        img.onload = () => resolve();
                        img.onerror = () => resolve();
                    });
                })
            );
        },
    
        async downloadNow() {
            this.isDownloading = true;
    
            try {
                const original = document.getElementById('captureArea');
                if (!original) throw new Error('captureArea tidak ditemukan');
    
                // 1) pastikan font siap
                if (document.fonts?.ready) await document.fonts.ready;
    
                // 2) pastikan gambar pada original sudah siap
                await this.waitImagesLoaded(original);
    
                // 3) buat export wrapper offscreen (netral, tidak kena sticky/scale)
                const wrapper = document.createElement('div');
                wrapper.id = 'export-wrapper';
                wrapper.style.position = 'fixed';
                wrapper.style.left = '-10000px';
                wrapper.style.top = '0';
                wrapper.style.width = '320px';
                wrapper.style.height = '400px';
                wrapper.style.zIndex = '999999';
                wrapper.style.background = 'transparent';
                wrapper.style.transform = 'none';
                wrapper.style.overflow = 'visible';
                document.body.appendChild(wrapper);
    
                // 4) clone kartu
                const clone = original.cloneNode(true);
                clone.id = 'captureAreaExport';
    
                // paksa ukuran & hilangkan transform apapun
                clone.style.width = '320px';
                clone.style.height = '400px';
                clone.style.transform = 'none';
                clone.style.position = 'relative';
                clone.style.left = '0';
                clone.style.top = '0';
                clone.style.margin = '0';
                clone.style.borderRadius = '32px';
                clone.style.overflow = 'hidden';
    
                wrapper.appendChild(clone);
    
                // 5) pastikan gambar pada clone siap
                await this.waitImagesLoaded(clone);
    
                // 6) render canvas
                const canvas = await html2canvas(clone, {
                    backgroundColor: null,
                    scale: 4,
                    useCORS: true,
                    allowTaint: false,
                    scrollX: 0,
                    scrollY: 0,
                    width: 320,
                    height: 400,
                    windowWidth: 320,
                    windowHeight: 400,
                    letterRendering: true,
                });
    
                // 7) bersihkan wrapper
                document.body.removeChild(wrapper);
    
                // 8) download
                const link = document.createElement('a');
                link.download = 'ID-{{ strtoupper(str_replace(' ', '-', $user->name)) }}.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
    
                this.showPreview = false;
            } catch (err) {
                console.error('Render error:', err);
                alert('Gagal download: ' + (err?.message ?? err));
            } finally {
                this.isDownloading = false;
            }
        }
    }" class="py-6 md:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">

                {{-- SISI KIRI: KARTU UTAMA --}}
                <div class="lg:col-span-5 xl:col-span-4 flex flex-col items-center">
                    <div class="w-full max-w-[320px]">
                        <div class="relative group cursor-pointer" @click="showPreview = true">
                            <div
                                class="absolute -inset-1 bg-red-600 rounded-[2.2rem] blur opacity-20 group-hover:opacity-40 transition duration-500">
                            </div>

                            <div class="relative bg-slate-900 rounded-[2rem] overflow-hidden text-white shadow-2xl aspect-[4/5] w-full border border-slate-800 transition-all duration-500"
                                :style="`background: linear-gradient(135deg, ${cardColor}, #000);`">
                                <div class="relative h-full w-full flex flex-col items-center justify-between p-6">
                                    <div class="w-full flex justify-between items-center">
                                        <div class="bg-white/10 p-1.5 rounded-lg border border-white/20">
                                            <x-application-logo class="h-3 w-auto fill-current text-white" />
                                        </div>
                                        <span
                                            class="text-[7px] font-black tracking-widest uppercase bg-white/20 px-2 py-1 rounded">
                                            Official Member
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-center w-full">
                                        <div
                                            class="rounded-full border-4 border-white/20 shadow-xl overflow-hidden mb-3 shrink-0 w-20 h-20">
                                            @if ($user->photo)
                                                <img src="{{ asset('storage/' . $user->photo) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    class="h-full w-full bg-red-900 flex items-center justify-center font-black text-2xl uppercase">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>

                                        <h3 class="font-black uppercase tracking-tight text-center leading-tight mb-1"
                                            style="font-size: 16px;">
                                            {{ $user->name }}
                                        </h3>

                                        <p class="text-[8px] font-black text-red-100 uppercase tracking-widest italic">
                                            Sabuk {{ $user->beltLevel->name ?? '-' }}
                                        </p>
                                    </div>

                                    <div
                                        class="w-full bg-black/40 rounded-xl p-3 border border-white/10 backdrop-blur-sm">
                                        <div class="flex justify-between items-end">
                                            <div class="text-left space-y-1.5">
                                                <div>
                                                    <p class="text-[5px] text-white/40 uppercase font-black">Dojo</p>
                                                    <p class="text-[9px] font-bold uppercase truncate max-w-[100px]">
                                                        {{ $user->dojo->name ?? 'Pusat' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-[5px] text-white/40 uppercase font-black">ID Member
                                                    </p>
                                                    <p class="text-[9px] font-bold uppercase font-mono italic">
                                                        #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- QR LOCAL base64 --}}
                                            <div class="bg-white p-1 rounded-md shrink-0">
                                                <img src="data:image/png;base64,{{ $qrPngBase64 }}" class="h-8 w-8"
                                                    alt="QR">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                    <p
                                        class="text-white font-black uppercase tracking-[0.2em] text-[10px] border-2 border-white/50 px-4 py-2 rounded-full">
                                        Buka Editor
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button @click="showPreview = true"
                            class="mt-6 w-full py-4 bg-slate-900 rounded-2xl text-xs font-black text-white hover:bg-black transition-all shadow-lg flex items-center justify-center uppercase tracking-widest">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Sesuaikan Kartu
                        </button>
                    </div>
                </div>

                {{-- SISI KANAN: BIODATA --}}
                <div class="lg:col-span-7 xl:col-span-8">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 p-8 md:p-12">
                        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                            <div
                                class="h-12 w-12 bg-red-600 text-white rounded-xl flex items-center justify-center text-lg font-black italic shadow-lg">
                                OSS
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Data Anggota</h3>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                    Federasi Karate-Do Indonesia
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 md:gap-10">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-red-600 uppercase tracking-widest">Nama
                                    Atlet</label>
                                <p class="font-bold text-slate-800 text-lg uppercase leading-tight">{{ $user->name }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-red-600 uppercase tracking-widest">ID
                                    Registrasi</label>
                                <p class="font-bold text-slate-800 text-lg">
                                    #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-red-600 uppercase tracking-widest">Asal
                                    Dojo</label>
                                <p class="font-black text-slate-800 text-lg uppercase">
                                    {{ $user->dojo->name ?? 'Pusat' }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <label
                                    class="text-[9px] font-black text-red-600 uppercase tracking-widest">Tingkatan</label>
                                <p class="font-bold text-slate-800 text-lg uppercase italic">
                                    Sabuk {{ $user->beltLevel->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- MODAL PREVIEW & EDITOR --}}
        <div x-show="showPreview" x-cloak class="fixed inset-0 z-[100] flex flex-col bg-slate-900">
            {{-- Header --}}
            <div
                class="flex items-center justify-between px-6 py-4 bg-white/5 border-b border-white/10 backdrop-blur-md">
                <h4 class="text-white font-black uppercase italic tracking-tighter">ID Editor</h4>
                <button @click="showPreview = false" class="text-white/70 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto flex flex-col lg:flex-row bg-slate-100">

                {{-- Area Preview --}}
                <div
                    class="lg:w-1/2 p-6 md:p-12 flex items-center justify-center bg-slate-200/50 sticky top-0 z-10 lg:relative border-b lg:border-b-0 lg:border-r border-slate-300">
                    <div class="scale-[0.7] sm:scale-100 transition-transform origin-center">
                        {{-- ELEMENT YANG DI-CAPTURE --}}
                        <div id="captureArea"
                            class="relative overflow-hidden text-white shadow-2xl rounded-[2rem] flex-shrink-0"
                            :style="`width: 320px; height: 400px; background: linear-gradient(135deg, ${cardColor}, #000);`">
                            <div class="relative h-[400px] w-[320px] flex flex-col items-center justify-between transition-all"
                                :style="`padding: ${verticalGap}px`">
                                <div class="w-full flex justify-between items-center px-1">
                                    <div class="bg-white/10 p-2 rounded-lg border border-white/20">
                                        <x-application-logo class="h-4 w-auto fill-current text-white" />
                                    </div>
                                    <span
                                        class="text-[8px] font-black tracking-widest uppercase bg-white/20 px-2 py-1 rounded">
                                        Official Member
                                    </span>
                                </div>

                                <div class="flex flex-col items-center w-full">
                                    <div class="rounded-full border-4 border-white/20 shadow-2xl overflow-hidden mb-4 shrink-0"
                                        :style="`width: ${photoScale}px; height: ${photoScale}px`">
                                        @if ($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full bg-red-900 flex items-center justify-center font-black text-white uppercase"
                                                :style="`font-size: ${photoScale/3}px`">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <h3 class="font-black uppercase leading-tight text-center w-full mb-1 tracking-tight px-2"
                                        :style="`font-size: ${nameSize}px`">
                                        {{ $user->name }}
                                    </h3>

                                    <p class="text-[8px] font-black text-red-100 uppercase tracking-[0.3em] italic">
                                        Sabuk {{ $user->beltLevel->name ?? '-' }}
                                    </p>
                                </div>

                                <div class="w-full bg-black/40 rounded-2xl p-4 border border-white/10 backdrop-blur-md">
                                    <div class="flex justify-between items-end">
                                        <div class="space-y-2 text-left">
                                            <div>
                                                <p class="text-[6px] text-white/40 uppercase font-black mb-0.5">Dojo</p>
                                                <p class="text-[9px] font-bold uppercase truncate max-w-[120px]">
                                                    {{ $user->dojo->name ?? 'Pusat' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-[6px] text-white/40 uppercase font-black mb-0.5">ID
                                                    Member</p>
                                                <p class="text-[9px] font-bold uppercase font-mono tracking-tighter">
                                                    #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- QR LOCAL base64 --}}
                                        <div class="bg-white p-1 rounded-lg shrink-0 shadow-xl">
                                            <img src="data:image/png;base64,{{ $qrPngBase64 }}" class="h-9 w-9"
                                                alt="QR">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kontrol Panel --}}
                <div class="lg:w-1/2 p-8 md:p-12 bg-white rounded-t-[3rem] lg:rounded-none relative z-20">
                    <div class="max-w-md mx-auto space-y-10">

                        <div>
                            <div class="flex justify-between mb-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ukuran
                                    Foto</label>
                                <span class="text-[10px] font-bold text-red-600" x-text="photoScale + 'px'"></span>
                            </div>
                            <input type="range" min="70" max="110" x-model="photoScale"
                                class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-red-600">
                        </div>

                        <div>
                            <div class="flex justify-between mb-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ukuran
                                    Nama</label>
                                <span class="text-[10px] font-bold text-red-600" x-text="nameSize + 'px'"></span>
                            </div>
                            <input type="range" min="14" max="22" x-model="nameSize"
                                class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-red-600">
                        </div>

                        <div>
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Warna
                                Tema</label>
                            <div class="flex flex-wrap items-center gap-3">
                                <input type="color" x-model="cardColor"
                                    class="w-12 h-10 rounded-lg cursor-pointer border-none p-0 overflow-hidden shadow-sm">
                                <template x-for="c in ['#dc2626', '#1e293b', '#065f46', '#7c3aed']">
                                    <button @click="cardColor = c" :style="`background: ${c}`"
                                        class="w-8 h-8 rounded-full border-2 border-white shadow-sm hover:scale-110 transition-transform"
                                        :class="cardColor === c ? 'ring-2 ring-red-500 ring-offset-2' : ''"></button>
                                </template>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button @click="downloadNow()" :disabled="isDownloading"
                                class="w-full py-5 bg-slate-900 rounded-2xl text-sm font-black text-white hover:bg-black transition-all shadow-xl flex items-center justify-center uppercase tracking-[0.2em] disabled:opacity-50">
                                <span x-show="!isDownloading" class="flex items-center italic">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download ID Card PNG
                                </span>

                                <span x-show="isDownloading" class="flex items-center">
                                    <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memproses Gambar...
                                </span>
                            </button>

                            <p
                                class="text-center text-[9px] text-slate-400 mt-4 uppercase font-bold tracking-widest leading-relaxed">
                                Pastikan foto sudah muncul sempurna sebelum menekan tombol download.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 18px;
            width: 18px;
            border-radius: 50%;
            background: #dc2626;
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
</x-app-layout>
