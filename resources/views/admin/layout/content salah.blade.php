<style>
    .active-row {
        font-weight: bold; /* Menebalkan teks (opsional) */
        color: white;
    }
    .btn-link{
        color: #ffffff;
    }
    .text-primary {
        color: white !important; /* Mengubah warna teks tombol menjadi putih */
        text-decoration: none; /* Menghilangkan garis bawah (opsional) */
    }
    /* From Uiverse.io by Yaya12085 */
    .radio-inputs {
        display: flex;
        justify-content: center;
        align-items: center;
        max-width: 350px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        margin-left: auto; /* Untuk memindahkan ke sebelah kanan */
    }

    .radio-inputs > * {
        margin: 6px;
        margin-top: 60px;
    }

    .radio-input:checked + .radio-tile {
        border-color: #2260ff;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        color: #2260ff;
    }

    .radio-input:checked + .radio-tile:before {
        transform: scale(1);
        opacity: 1;
        background-color: #2260ff;
        border-color: #2260ff;
    }

    .radio-input:checked + .radio-tile .radio-icon svg {
        fill: #2260ff;
    }

    .radio-input:checked + .radio-tile .radio-label {
        color: #2260ff;
    }

    .radio-input:focus + .radio-tile {
        border-color: #2260ff;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1), 0 0 0 4px #b5c9fc;
    }

    .radio-input:focus + .radio-tile:before {
        transform: scale(1);
        opacity: 1;
    }

    .radio-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 80px;
        min-height: 80px;
        border-radius: 0.5rem;
        border: 2px solid #b5bfd9;
        background-color: #fff;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        transition: 0.15s ease;
        cursor: pointer;
        position: relative;
    }

    .radio-tile:before {
        content: "";
        position: absolute;
        display: block;
        width: 0.75rem;
        height: 0.75rem;
        border: 2px solid #b5bfd9;
        background-color: #fff;
        border-radius: 50%;
        top: 0.25rem;
        left: 0.25rem;
        opacity: 0;
        transform: scale(0);
        transition: 0.25s ease;
    }

    .radio-tile:hover {
        border-color: #2260ff;
    }

    .radio-tile:hover:before {
        transform: scale(1);
        opacity: 1;
    }

    .radio-icon svg {
        width: 2rem;
        height: 2rem;
        fill: #494949;
    }

    .radio-label {
        color: #707070;
        transition: 0.375s ease;
        text-align: center;
        font-size: 13px;
    }

    .radio-input {
        clip: rect(0 0 0 0);
        -webkit-clip-path: inset(100%);
        clip-path: inset(100%);
        height: 1px;
        overflow: hidden;
        position: absolute;
        white-space: nowrap;
        width: 1px;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="my-5">Dashboard Admin</h1>
                </div>

                <div class="radio-inputs">
                        <label>
                            <input checked="" class="radio-input" type="radio" name="engine" value="effective" onchange="changeDashboard()">
                                <span class="radio-tile">
                                    <span class="radio-icon">
                                        <i class="fas fa-signal"></i>
                                    </span>
                                    <span class="radio-label">Effective</span>
                                </span>
                        </label>
                        <label>
                            <input  class="radio-input" type="radio" name="engine" value="simple" onchange="changeDashboard()">
                            <span class="radio-tile">
                                <span class="radio-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>
                                <span class="radio-label">Simple Interest</span>
                            </span>
                        </label>
                        <label>
                            <input class="radio-input" type="radio" name="engine" value="securities" onchange="changeDashboard()">
                            <span class="radio-tile">
                                <span class="radio-icon">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <span class="radio-label">securities</span>
                            </span>
                        </label>
                </div>
            </div>
        </div>
    </div>
    <!-- Konten Dinamis -->
    <section id="dynamic-content" class="content">
        <!-- Konten akan dimuat di sini -->
</section>
</div>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function changeDashboard() {
        // Ambil nilai radio button yang dipilih
        const selectedValue = document.querySelector('input[name="engine"]:checked').value;

        // Tentukan URL konten yang akan dimuat berdasarkan pilihan
        let url = '';
        if (selectedValue === 'effective') {
            url = '/dashboard/effective';
        } else if (selectedValue === 'simple') {
            url = '/dashboard/simpleinterest';
        } else if (selectedValue === 'securities') {
            url = '/dashboard/securities';
        }

        // Muat konten menggunakan AJAX
        $('#dynamic-content').load(url, function(response, status, xhr) {
            if (status == "error") {
                const msg = "Sorry but there was an error: ";
                $('#dynamic-content').html(msg + xhr.status + " " + xhr.statusText);
            }
        });
    }

    // Muat konten awal saat halaman pertama kali dibuka
    $(document).ready(function() {
        changeDashboard(); // Memanggil fungsi untuk memuat konten default (effective)
    });
</script>
