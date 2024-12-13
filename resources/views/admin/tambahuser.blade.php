<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Form Pengguna</title>
    <style>
        body {
            background-color: #f8f9fa; /* Warna latar belakang yang lembut */
        }
        .container {
            background-color: white; /* Warna latar belakang form */
            border-radius: 10px; /* Sudut yang lebih halus */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Bayangan yang lembut */
        }
        h2 {
            color: #000000; /* Warna judul yang menarik */
        }
        .form-control:focus {
            border-color: #007bff; /* Warna border saat fokus */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Bayangan saat fokus */
        }
        .btn-primary {
            background-color: #007bff; /* Warna tombol */
            border-color: #007bff; /* Border tombol */
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Warna tombol saat hover */
            border-color: #0056b3; /* Border tombol saat hover */
        }
        .btn-secondary {
            background-color: #6c757d; /* Warna tombol kembali */
            border-color: #6c757d; /* Border tombol kembali */
        }
        .btn-secondary:hover {
            background-color: #5a6268; /* Warna tombol kembali saat hover */
            border-color: #5a6268; /* Border tombol kembali saat hover */
        }
        small.text-danger {
            font-weight: bold; /* Menebalkan pesan error */
        }
    </style>
</head>
<body>

    <div class="container mt-5 p-4">
        <h2 class="mb-4 text-center">Form Tambah Pengguna</h2>
        <form method="POST" action="{{ route('AddUserAdmin') }}">
            @csrf

            <div class="row">
                <!-- Nama Lengkap -->
                <div class="col-md-6 mb-3">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan Nama Lengkap Anda"/>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Nama PT -->
                <div class="col-md-6 mb-3">
                    <label for="nama_pt">Nama PT</label>
                    <input id="nama_pt" class="form-control" type="text" name="nama_pt" value="{{ auth()->user()->nama_pt }}" disabled />
                    @error('nama_pt') <small class="text-danger">{{ $message }}</small> @enderror
                    <input type="hidden" name="nama_pt" value="{{ auth()->user()->nama_pt }}"> <!-- Mengirimkan nilai tersembunyi -->
                </div>
            </div>

            <div class="row">
                <!-- Alamat PT -->
                <div class="col-md-6 mb-3">
                    <label for="alamat_pt">Alamat PT</label>
                    <input id="alamat_pt" class="form-control" type="text" name="alamat_pt" value="{{ auth()->user()->alamat_pt }}" disabled />
                    @error('alamat_pt') <small class="text-danger">{{ $message }}</small> @enderror
                    <input type="hidden" name="alamat_pt" value="{{ auth()->user()->alamat_pt }}"> <!-- Mengirimkan nilai tersembunyi -->
                </div>

                <!-- Nomor WhatsApp -->
                <div class="col-md-6 mb-3">
                    <label for="nomor_wa">Nomor WhatsApp</label>
                    <input id="nomor_wa" class="form-control" type="number" name="nomor_wa" value="{{ old('nomor_wa') }}" required placeholder="Masukkan Nomor WhatsApp Anda" />
                    @error('nomor_wa') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row">
                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label for="email">Email</label>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required placeholder="Masukkan Alamat Email Anda" />
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Role -->
                <div class="col-md-6 mb-3">
                    <label for="role">Role</label>
                    <select id="role" class="form-control" name="role" required>
                        <option value="user">User</option>
                        <!-- Tambahkan opsi lain jika diperlukan -->
                    </select>
                    @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row">
                <!-- Company Type -->
                <div class="col-md-6 mb-3">
                    <label for="company_type">Tipe Perusahaan</label>
                    <input id="company_type" class="form-control" type="text" name="company_type" value="{{ auth()->user()->company_type }}" disabled />
                    @error('company_type') <small class="text-danger">{{ $message }}</small> @enderror
                    <input type="hidden" name="company_type" value="{{ auth()->user()->company_type }}"> <!-- Mengirimkan nilai tersembunyi -->
                </div>

                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password">Password</label>
                    <input id="password" class="form-control" type="password" name="password" required placeholder="Masukkan Password Anda" />
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row">
                <!-- Konfirmasi Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required placeholder="Masukkan Ulang Password Anda"/>
                    @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <!-- Tombol Kembali -->
                <a href="{{ route('admin.usermanajemen') }}" class="btn btn-secondary">Kembali</a>
                <!-- Tombol Simpan -->
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
