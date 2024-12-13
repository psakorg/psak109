<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        /* Menambahkan margin-top agar tidak mepet dengan navbar */
        .container {
            margin-top: 120px; /* Adjust sesuai kebutuhan */
            max-width: 1300px; /* Lebar container diperbesar */
        }

        /* Lebar card yang lebih besar agar tabel tidak terpotong */
        .card {
            min-width: 1250px; /* Lebar card sedikit diperbesar */
        }

        /* Mengatur kolom agar lebih kompak */
        table th, table td {
            font-size: 0.85rem; /* Ukuran font lebih kecil */
            padding: 8px; /* Mengurangi padding */
            text-align: center; /* Semua teks dalam kolom rata tengah */
        }

        /* Kolom tertentu yang lebih besar */
        table th:nth-child(2), table td:nth-child(2), /* Nama Lengkap */
        table th:nth-child(5), table td:nth-child(5), /* Company Type */
        table th:nth-child(7), table td:nth-child(7)  /* Email */ {
            width: 200px;
        }

        /* Lebar kolom yang lebih kecil */
        table th, table td {
            width: 80px; /* Lebar kolom default lebih kecil */
        }

        /* Tambahan margin dan padding agar tombol tidak terlalu mepet */
        .btn {
            margin: 1px;
            font-size: 0.9rem; /* Ukuran tombol lebih kecil */
            padding: 5px 10px; /* Mengurangi ukuran tombol */
            transition: background-color 0.3s, transform 0.3s; /* Menambahkan transisi smooth */
        }

        /* Efek hover pada tombol */
        .btn:hover {
            transform: scale(1.05); /* Meningkatkan ukuran sedikit saat di-hover */
        }

        /* Kontrol overflow teks pada kolom */
        td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis; /* Menggunakan elipsis (...) untuk memotong teks */
        }

        /* Tambahan untuk tombol spesifik */
        .btn-primary:hover {
            background-color: #007bff; /* Warna biru lebih terang */
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc3545; /* Warna merah lebih terang */
            color: white;
        }

        .btn-primary.ml-auto:hover {
            background-color: #007bff; /* Warna biru lebih terang untuk tombol tambah user */
            color: white;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Manajemen Pengguna</h3>
                        <a href="{{ route('load.admin.add.user') }}" class="btn btn-primary ml-auto">
                            <i class="fas fa-plus"></i> Tambah User
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Success message -->
                        @if (Session::has('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif

                        <!-- Fail message -->
                        @if (Session::has('fail'))
                            <div class="alert alert-danger">
                                {{ Session::get('fail') }}
                            </div>
                        @endif

                        <!-- Tabel pengguna -->
                        <table class="table table-bordered ">
                            <thead class="thead-light">
                                <tr>
                                    <th>S/N</th>
                                    <th>Nama Lengkap</th>
                                    <th>Nama PT</th>
                                    <th>Alamat PT</th>
                                    <th>Company Type</th>
                                    <th>Nomor WhatsApp</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Tanggal Registrasi</th>
                                    <th>Terakhir Diperbarui</th>
                                    <th colspan="2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (auth()->check() && auth()->user()->role == 'admin')
                                    <!-- Admin row -->
                                    <tr>
                                        <td>1</td>
                                        <td>{{ auth()->user()->name }}</td>
                                        <td>{{ auth()->user()->nama_pt ?? 'N/A' }}</td>
                                        <td>{{ auth()->user()->alamat_pt ?? 'N/A' }}</td>
                                        <td>{{ auth()->user()->company_type ?? 'N/A' }}</td>
                                        <td>{{ auth()->user()->nomor_wa }}</td>
                                        <td>{{ auth()->user()->email }}</td>
                                        <td>{{ ucfirst(auth()->user()->role) }}</td>
                                        <td>{{ auth()->user()->created_at->format('d M Y') }}</td>
                                        <td>{{ auth()->user()->updated_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.edit.user', ['user_id' => auth()->user()->user_id]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.delete.user', ['user_id' => auth()->user()->user_id]) }}" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Other users -->
                                    @foreach ($all_users->where('user_id', '!=', auth()->user()->user_id) as $index => $user)
                                        @if ($user->role != 'superadmin')
                                            <tr>
                                                <td>{{ $index + 2 }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->nama_pt ?? 'N/A' }}</td>
                                                <td>{{ $user->alamat_pt ?? 'N/A' }}</td>
                                                <td>{{ $user->company_type ?? 'N/A' }}</td>
                                                <td>{{ $user->nomor_wa }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ ucfirst($user->role) }}</td>
                                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                                <td>{{ $user->updated_at->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.edit.user', ['user_id' => $user->user_id]) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-pencil-alt"></i> Edit
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.delete.user', ['user_id' => $user->user_id]) }}" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    <!-- No users found -->
                                    @if ($all_users->where('user_id', '!=', auth()->user()->user_id)->where('role', '!=', 'superadmin')->isEmpty())
                                        <tr>
                                            <td colspan="12" class="text-center">No User Found!</td>
                                        </tr>
                                    @endif
                                @else
                                    <tr>
                                        <td colspan="12" class="text-center">Access Denied! Only Admin can view this page.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
