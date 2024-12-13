<head>
    <!-- Menambahkan Judul di Tab Browser -->
    <title>Login PSAK 109</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<x-guest-layout>

    <!-- Alert Session Status -->
    @if (session('status'))
        <div class="mb-4 p-4 text-green-700 bg-green-100 border border-green-300 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 text-red-600 bg-red-100 border-2 border-red-400 rounded-lg font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <!-- Kontainer Konten Formulir -->
        <div id="form-content">
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan Alamat Email Anda"/>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="current-password" placeholder="Masukkan Password Anda" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />

                <!-- Alert untuk Password Minimal Karakter -->
                <small id="password-alert" class="text-danger mt-1" style="display: none;">Password harus memiliki minimal 8 karakter.</small>
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-start mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('Belum Punya Akun?') }}
                </a>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Lupa Password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3" id="login-button">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </div>
    </form>

    <!-- Loader Overlay (disembunyikan secara default) -->
    <div class="loader-overlay" id="loader-overlay" style="display: none;">
        <div class="loader"></div>
    </div>
</x-guest-layout>

<!-- Tambahkan JavaScript -->
<script>
    document.getElementById('login-form').addEventListener('submit', function(event) {
        // Ambil password dari input
        const passwordInput = document.getElementById('password');
        const passwordAlert = document.getElementById('password-alert');

        // Cek panjang password
        if (passwordInput.value.length < 8) {
            // Tampilkan alert jika password kurang dari 8 karakter
            passwordAlert.style.display = 'block';
            event.preventDefault(); // Mencegah pengiriman form
        } else {
            // Sembunyikan alert jika password valid
            passwordAlert.style.display = 'none';
            // Tampilkan loader overlay
            document.getElementById('loader-overlay').style.display = 'flex';
            // Nonaktifkan tombol login
            document.getElementById('login-button').disabled = true;
        }
    });
</script>

<style>

    /* Loader Overlay Styles */
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8); /* Background semi-transparan */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    /* Loader Styles - Custom Animation */
    .loader {
        width: 48px;
        height: 48px;
        margin: auto;
        position: relative;
    }

    .loader:before {
        content: '';
        width: 48px;
        height: 5px;
        background: #f0808050;
        position: absolute;
        top: 60px;
        left: 0;
        border-radius: 50%;
        animation: shadow324 0.5s linear infinite;
    }

    .loader:after {
        content: '';
        width: 100%;
        height: 100%;
        background: #25e2ff;
        position: absolute;
        top: 0;
        left: 0;
        border-radius: 4px;
        animation: jump7456 0.5s linear infinite;
    }

    /* Ensure no padding or border on loader and pseudo-elements */
    .loader, .loader:before, .loader:after {
        padding: 0;
        margin: 0;
        border: none;
    }

    @keyframes jump7456 {
        15% {
            border-bottom-right-radius: 3px;
        }

        25% {
            transform: translateY(9px) rotate(22.5deg);
        }

        50% {
            transform: translateY(18px) scale(1, .9) rotate(45deg);
            border-bottom-right-radius: 40px;
        }

        75% {
            transform: translateY(9px) rotate(67.5deg);
        }

        100% {
            transform: translateY(0) rotate(90deg);
        }
    }

    @keyframes shadow324 {
        0%, 100% {
            transform: scale(1, 1);
        }

        50% {
            transform: scale(1.2, 1);
        }
    }
</style>
