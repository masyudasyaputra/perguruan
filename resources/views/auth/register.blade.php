<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="province_id" :value="__('Provinsi (Pengprov)')" />
            <select id="province_id" name="province_id"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="">-- Pilih Provinsi --</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                        {{ $province->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('province_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="city_id" :value="__('Kota/Kabupaten (Pengcab)')" />
            <select id="city_id" name="city_id"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required disabled>
                <option value="">-- Pilih Kota --</option>
            </select>
            <x-input-error :messages="$errors->get('city_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="dojo_id" :value="__('Dojo')" />
            <select id="dojo_id" name="dojo_id"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required disabled>
                <option value="">-- Pilih Dojo --</option>
            </select>
            <x-input-error :messages="$errors->get('dojo_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="belt_level_id" :value="__('Tingkatan Sabuk')" />
            <select id="belt_level_id" name="belt_level_id"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="">-- Pilih Sabuk --</option>
                @foreach ($beltLevels as $belt)
                    <option value="{{ $belt->id }}" {{ old('belt_level_id') == $belt->id ? 'selected' : '' }}>
                        {{ $belt->name }} (Rp {{ number_format($belt->membership_fee) }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('belt_level_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_id');
            const citySelect = document.getElementById('city_id');
            const dojoSelect = document.getElementById('dojo_id');

            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                citySelect.disabled = true;
                dojoSelect.disabled = true;
                citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                dojoSelect.innerHTML = '<option value="">-- Pilih Dojo --</option>';

                if (provinceId) {
                    fetch(`/api/cities/${provinceId}`)
                        .then(response => response.json())
                        .then(data => {
                            citySelect.disabled = false;
                            data.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.text = city.name;
                                citySelect.add(option);
                            });
                        });
                }
            });

            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                dojoSelect.disabled = true;
                dojoSelect.innerHTML = '<option value="">-- Pilih Dojo --</option>';

                if (cityId) {
                    fetch(`/api/dojos/${cityId}`)
                        .then(response => response.json())
                        .then(data => {
                            dojoSelect.disabled = false;
                            data.forEach(dojo => {
                                const option = document.createElement('option');
                                option.value = dojo.id;
                                option.text = dojo.name;
                                dojoSelect.add(option);
                            });
                        });
                }
            });
        });
    </script>
</x-guest-layout>
