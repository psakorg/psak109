<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Form Pengguna</title>
</head>
<body>

    <div class="container mt-5 p-4 border rounded shadow">
        <h2 class="mb-4 text-center">Form Tambah Pengguna</h2>
        <form method="POST" action="{{ route('superadmin.AddUser') }}">
            @csrf

            <div class="row">
                <!-- Nama Lengkap -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukkan Nama Lengkap Anda"/>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Nama PT -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="nama_pt" :value="__('Nama PT')" />
                    <x-text-input id="nama_pt" class="form-control" type="text" name="nama_pt" :value="old('nama_pt')" required autofocus autocomplete="nama_pt" placeholder="Masukkan Nama PT Anda" />
                    <x-input-error :messages="$errors->get('nama_pt')" class="mt-2" />
                </div>
            </div>

            <div class="row">
                <!-- Alamat PT -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="alamat_pt" :value="__('Alamat PT')" />
                    <x-text-input id="alamat_pt" class="form-control" type="text" name="alamat_pt" :value="old('alamat_pt')" required autofocus autocomplete="alamat_pt" placeholder="Masukkan Alamat PT Anda" />
                    <x-input-error :messages="$errors->get('alamat_pt')" class="mt-2" />
                </div>

                <!-- Nomor WhatsApp -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="nomor_wa" :value="__('Nomor WhatsApp')" />
                    <x-text-input id="nomor_wa" class="form-control" type="number" name="nomor_wa" :value="old('nomor_wa')" required autofocus autocomplete="nomor_wa" placeholder="Masukkan Nomor WhatsApp Anda" />
                    <x-input-error :messages="$errors->get('nomor_wa')" class="mt-2" />
                </div>
            </div>

            <div class="row">
                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="Masukkan Alamat Email Anda" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="role" :value="__('Role')" />
                    <select id="role" class="form-control" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                        <option value="user">User</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
            </div>

            <div class="row">
                <!-- Company Type (Dropdown) -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="company_type" :value="__('Tipe Perusahaan')" />
                    <select id="company_type" class="form-control" name="company_type" required>
                        <option value="">-- Pilih Tipe Perusahaan --</option>
                        <option value="Bank">Bank</option>
                        <option value="Perusahaan Pembiayaan">Perusahaan Pembiayaan</option>
                        <option value="Perusahaan Asuransi">Perusahaan Asuransi</option>
                    </select>
                    <x-input-error :messages="$errors->get('company_type')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="Masukkan Password Anda" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </div>

            <div class="row">
                <!-- Konfirmasi Password -->
                <div class="col-md-6 mb-3">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                    <x-text-input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan Ulang Password Anda"/>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('usermanajemen') }}" class="btn btn-secondary">
                    {{ __('Kembali') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Simpan') }}
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
