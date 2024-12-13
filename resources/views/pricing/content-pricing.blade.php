<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfigurasi Akuntansi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            color: #343a40;
            font-size: 14px; /* Ukuran teks lebih kecil */
        }

        .container {
            display: flex;
            flex-grow: 1;
        }

        .content {
            margin-left: 270px;
            margin-top: 20px; /* Menambahkan jarak margin dari atas */
            flex-grow: 1;
            padding: 15px; /* Mengurangi padding agar lebih ringkas */
            overflow-x: auto;
        }


        h1 {
            text-align: center;
            margin-bottom: 15px; /* Mengurangi margin bawah */
            color: #495057;
            font-size: 20px; /* Ukuran font lebih kecil */
            font-weight: bold;
        }
        .alert {
        position: relative;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            border: none;
            background: none;
            font-size: 20px;
            cursor: pointer;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 8px; /* Mengurangi jarak antar elemen */
        }

        label {
            font-weight: bold;
            color: #495057;
            width: 140px; /* Mengurangi lebar label */
            font-size: 14px; /* Ukuran teks label lebih kecil */
        }

        input[type="text"], select {
            padding: 8px; /* Mengurangi padding */
            font-size: 14px; /* Ukuran teks lebih kecil */
            border-radius: 4px; /* Border radius lebih kecil */
            border: 1px solid #ced4da;
            width: 100%;
            max-width: 320px; /* Lebar maksimal sedikit lebih kecil */
            transition: all 0.3s ease;
        }

        select {
            cursor: pointer;
        }

        input[type="text"]:focus, select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 4px; /* Mengurangi radius pada tabel */
            overflow: hidden;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px; /* Mengurangi padding pada sel */
            text-align: center;
            font-size: 14px; /* Ukuran teks pada tabel lebih kecil */
        }

        th {
            background-color: #4481c2;
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        td input[type="radio"] {
            transform: scale(1.1); /* Ukuran radio button sedikit lebih kecil */
            cursor: pointer;
        }

        .save-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 110px; /* Lebar tombol lebih kecil */
            padding: 8px; /* Padding tombol lebih kecil */
            margin: 12px auto; /* Mengurangi margin pada tombol */
            background-color: #007bff;
            color: white;
            text-align: center;
            font-size: 16px; /* Ukuran teks pada tombol lebih kecil */
            border-radius: 4px; /* Radius tombol lebih kecil */
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .save-button:hover {
            background-color: #0056b3;
            transform: scale(1.03);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 10px;
            }

            h1 {
                font-size: 18px;
            }

            label {
                width: 120px;
            }

            input[type="text"], select {
                max-width: 100%;
            }

            .save-button {
                width: 100%;
            }
        }
    </style>

    <!-- Link Font Awesome untuk ikon -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <div class="container">
        <div class="content">
            <h1>Tabel PSAK 71</h1>
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                        <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
                    </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                            <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
                        </div>
                    @endif

        <form action="{{ route('mapping.save') }}" method="POST">
         @csrf
            <div class="form-group">
                <label for="company_type">Tipe Perusahaan:</label>
                <input type="text" class="form-control" id="company_type" value="{{ Auth::user()->company_type }}" disabled>
                <input type="hidden" name="company_type" value="{{ Auth::user()->company_type }}">
                @error('company_type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="bisnis_type">Tipe Bisnis:</label>
                <select id="bisnis_type" name="bisnis_type" required>
                    <option value="">--Pilih Tipe Bisnis--</option>
                    <option value="L001">KREDIT TANPA AGUNAN</option>
                    <option value="L002">KREDIT PEMILIKAN MOBIL</option>
                    <option value="L003">KREDIT PEMILIKAN RUMAH</option>
                    <option value="L004">INSURANCE</option>
                    <option value="L005">USAHA KECIL DAN MENENGAH</option>
                    <option value="L006">KREDIT ANGSURAN BERJANGKA</option>
                </select>
                @error('bisnis_type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>



                <div id="modules-Bank" class="modul">
                    <table class="table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Modules</th>
                                <th>Effective</th>
                                <th>Simple Interest</th>
                            </tr>
                        </thead>
                        @php
                        // Mendefinisikan array modul secara manual
                        $moduls = [
                            ['modul_id' => 'M0001', 'nama_modul' => 'Interest Deferred Restructuring'],
                            ['modul_id' => 'M0002', 'nama_modul' => 'Expenses Off market'],
                            ['modul_id' => 'M0003', 'nama_modul' => 'Amortized Cos'],
                            ['modul_id' => 'M0004', 'nama_modul' => 'Amortized Fee'],
                            ['modul_id' => 'M0005', 'nama_modul' => 'Calculated Accrual Interest'],
                            ['modul_id' => 'M0006', 'nama_modul' => 'Expected Cash Flow'],
                            ['modul_id' => 'M0007', 'nama_modul' => 'Outstanding Balance'],
                            ['modul_id' => 'M0008', 'nama_modul' => 'Opening Balance'],
                        ];
                    @endphp

                    @foreach($moduls as $index => $modul)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $modul['nama_modul'] }}</td>
                            <td>
                                <input type="checkbox" name="module_{{ $modul['modul_id'] }}" value="1" id="effective_{{ $modul['modul_id'] }}" onclick="updateInterestValue('{{ $modul['modul_id'] }}', 'effective')">

                            </td>
                            <td>
                                <input type="checkbox" name="module_{{ $modul['modul_id'] }}" value="0" id="simple_interest_{{ $modul['modul_id'] }}" onclick="updateInterestValue('{{ $modul['modul_id'] }}', 'simple')">

                            </td>
                        </tr>
                    @endforeach
                </tbody>
                    </table>
                    <button class="btn btn-primary save-button" type="submit">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<script>
    function updateInterestValue(modulId, type) {
        // Jika Effective dipilih, Simple Interest akan otomatis diset ke 0
        if (type === 'effective') {
            document.getElementById('simple_interest_' + modulId).checked = false;
        }
        // Jika Simple Interest dipilih, Effective akan otomatis diset ke 0
        else {
            document.getElementById('effective_' + modulId).checked = false;
        }
    }
</script>
