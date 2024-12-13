<head>
    <!-- Menambahkan Judul di Tab Browser -->
    <title>Register Akun PSAK 109</title>
</head>
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukkan Nama Lengkap Anda"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Nama PT --}}
        <div class="mt-4">
            <x-input-label for="nama_pt" :value="__('Nama PT')" />
            <x-text-input id="nama_pt" class="block mt-1 w-full" type="text" name="nama_pt" :value="old('nama_pt')" required autofocus autocomplete="nama_pt" placeholder="Masukkan Nama PT Anda" />
            <x-input-error :messages="$errors->get('nama_pt')" class="mt-2" />
        </div>

        {{-- Alamat PT --}}
        <div class="mt-4">
            <x-input-label for="alamat_pt" :value="__('Alamat PT')" />
            <x-text-input id="alamat_pt" class="block mt-1 w-full" type="text" name="alamat_pt" :value="old('alamat_pt')" required autofocus autocomplete="alamat_pt" placeholder="Masukkan Alamat PT Anda" />
            <x-input-error :messages="$errors->get('alamat_pt')" class="mt-2" />
        </div>

        {{-- Company Type --}}
        <div class="mt-4">
            <x-input-label for="company_type" :value="__('Tipe Perusahaan')" />
            <select id="company_type" class="block mt-1 w-full" name="company_type" required>
                <option value="">--Pilih Tipe Perusahaan</option>
                <option value="Bank" {{ old('company_type') == 'swasta' ? 'selected' : '' }}>Bank</option>
                <option value="Perusahaan Pembiayaan" {{ old('company_type') == 'negeri' ? 'selected' : '' }}>Perusahaan Pembiayaan</option>
                <option value="Perusahaan Asuransi" {{ old('company_type') == 'ngo' ? 'selected' : '' }}>Perusahaan Asuransi</option>
            </select>
            <x-input-error :messages="$errors->get('company_type')" class="mt-2" />
        </div>

        {{-- Nomor Whatsapp --}}
        <div class="mt-4">
            <x-input-label for="nomor_wa" :value="__('Nomor WhatsApp')" />
            <x-text-input id="nomor_wa" class="block mt-1 w-full" type="number" name="nomor_wa" :value="old('nomor_wa')" required autofocus autocomplete="nomor_wa" placeholder="Masukkan Nomor WhatsApp Anda" />
            <x-input-error :messages="$errors->get('nomor_wa')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Masukkan Alamat Email Anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Masukkan Password Anda" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan Ulang Password Anda"/>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah Register ? Silahkan Login') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
