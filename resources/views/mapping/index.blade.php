<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5" style="padding-right: 50px;">
            <section class="section">
                <div class="section-header">
                    <h4>Daftar Mapping</h4>
                </div>
                @if(session('pesan'))
                    <div class="alert alert-success">{{ session('pesan') }}</div>
                @endif
                <div class="table-responsive text-center" style="overflow-x: auto;">
                    <table class="table table-striped table-bordered custom-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 15%; white-space: nowrap;">User  ID</th>
                                <th style="width: 25%; white-space: nowrap;">Name</th>
                                <th style="width: 25%; white-space: nowrap;">Email</th>
                                <th style="width: 20%; white-space: nowrap;">LOB</th> <!-- Ganti LOB ID menjadi Description -->
                                <th style="width: 20%; white-space: nowrap;">Company Type</th>
                                <th style="width: 20%; white-space: nowrap;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $lastUseId = null; // Menyimpan user_id terakhir yang ditampilkan
                                $lastLobId = null; // Menyimpan lob_id terakhir yang ditampilkan
                                $lastCompanyType = null; // Menyimpan company_type terakhir yang ditampilkan
                            @endphp
                            @foreach ($mappings as $mapping)
                                @if ($lastUseId !== $mapping->user_id || $lastLobId !== $mapping->lob_id || $lastCompanyType !== $mapping->company_type)
                                    <tr>
                                        <td>{{ $mapping->user_id }}</td>
                                        <td>{{ isset($users[$mapping->user_id]) ? $users[$mapping->user_id]->name : 'N/A' }}</td>
                                        <td>{{ isset($users[$mapping->user_id]) ? $users[$mapping->user_id]->email : 'N/A' }}</td>
                                        <td>{{ $mapping->description }}</td> <!-- Menampilkan Description -->
                                        <td>{{ $mapping->company_type }}</td>
                                        <td>
                                            <a href="{{ route('mappings.show', $mapping->user_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye" style="margin-right: 5px;"></i> Lihat Selengkapnya
                                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        // Update nilai terakhir
                                        $lastUseId = $mapping->user_id;
                                        $lastLobId = $mapping->lob_id;
                                        $lastCompanyType = $mapping->company_type;
                                    @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
    .custom-table td a {
        text-decoration: none;
        color: #fff;
        font-size: 12px;
    }
    .custom-table td a.btn-info {
        background-color: #00bcd4;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .custom-table td a.btn-info:hover {
        background-color: #0097a7;
        transform: scale(1.05);
    }
</style>

<!-- Font Awesome Link -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
