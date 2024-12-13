<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5" style="padding-right: 50px;">
            <section class="section">
                <div class="section-header">
                    <h4>Detail Mapping </h4>
                </div>
                @if(session('pesan'))
                    <div class="alert alert-success">{{ session('pesan') }}</div>
                @endif

                <div class="user-info mb-4">
                    <p><strong>Name:</strong> {{ $user->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
                    <p><strong>User ID:</strong>{{ $mappingDetails->first()->user_id }}</p>
                </div>

                <div class="table-responsive text-center" style="overflow-x: auto;">
                    <table class="table table-striped table-bordered custom-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 15%; white-space: nowrap;">Modul ID</th>
                                <th style="width: 25%; white-space: nowrap;">Nama Modul</th> <!-- Tambahkan kolom untuk Nama Modul -->
                                <th style="width: 25%; white-space: nowrap;">Effective</th>
                                <th style="width: 25%; white-space: nowrap;">Simple Interest</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mappingDetails as $detail)
                                <tr>
                                    <td>{{ $detail->modul_id }}</td>
                                    <td>{{ $detail->nama_modul }}</td> <!-- Tampilkan Nama Modul -->
                                    <td>{{ $detail->effective == '1' ? 'Iya' : 'Tidak' }}</td>
                                    <td>{{ $detail->simple_interest == '1' ? 'Iya' : 'Tidak' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('mappings.index') }}" class="btn btn-secondary">Kembali</a>
            </section>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    body {
        background-color: #f4f7fc;
        font-family: 'Arial', sans-serif;
        overflow-x: hidden; /* Menyembunyikan scroll horizontal pada body */
    }
    .main-content {
        width: 100%;
        padding-top: 20px;
    }
    .section-header h4 {
        font-size: 26px;
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .custom-table {
        width: 100%;
        margin: 20px auto;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        border-radius: 12px;
        font-size: 12px;
    }
    .custom-table th, .custom-table td {
        padding: 10px 12px;
        text-align: center;
        vertical-align: middle;
    }
    .custom-table thead {
        background-color: #4a90e2;
        color: #fff;
    }
    .custom-table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .custom-table tbody tr:hover {
        background-color: #e1f5fe;
        transition: background-color 0.3s ease;
    }
    .custom-table th {
        text-transform: uppercase;
        font-weight: 500;
        font-size: 12px;
        white-space: nowrap;
    }
    .form-control {
        height: 38px; /* Atur tinggi input agar lebih ringkas */
    }
</style>

<!-- Font Awesome Link -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
