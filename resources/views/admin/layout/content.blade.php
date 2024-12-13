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
        margin-right: 40px;
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
    flex-direction: row; /* Ubah ke row agar label berada di sebelah kanan ikon */
    align-items: center;
    justify-content: center;
    width: 130px; /* Sesuaikan lebar jika perlu */
    min-height: 60px;
    border-radius: 0.5rem;
    border: 2px solid #b5bfd9;
    background-color: #fff;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    transition: 0.15s ease;
    cursor: pointer;
    position: relative;
    padding: 0.5rem; /* Tambahkan padding agar lebih seimbang */
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
    .radio-icon {
    margin-right: 8px; /* Tambahkan margin untuk spasi antara ikon dan label */
}

    .radio-icon svg {
        width: 2rem;
        height: 2rem;
        fill: #494949;
    }
    .radio-label {
    color: #707070;
    transition: 0.375s ease;
    text-align: left; /* Sesuaikan text-align jika perlu */
    font-size: 13px;
    white-space: nowrap;
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
                    <h1 class="my-5" style="font-size: 40px">Dashboard</h1>
                    <h2 style="font-size:25px" >Nama PT : {{ $nama_pt}}</h2> <!-- Tambahkan ini untuk menampilkan nama PT -->

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
			<input  class="radio-input" type="radio" name="engine" value="simple" onchange="window.location.href='/admin/dashboard/simple_interest'">
			<span class="radio-tile">
				<span class="radio-icon">
                    <i class="fas fa-money-bill-wave"></i>
				</span>
				<span class="radio-label">Simple Interest</span>
			</span>
		</label>
		<label>
			<input class="radio-input" type="radio" name="engine" value="securities" onchange="window.location.href='/admin/dashboard/securities'">
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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stat boxes (Display data) -->
            <div class="row d-flex align-items-stretch mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info h-100">
                        <div class="inner" style="overflow: hidden;">
                            <h3 id="deb_name_display" style="font-size: 1.5em; white-space: normal; line-height: 1.7;">
                                {{ $master->deb_name ?? 'N/A' }}
                            </h3>
                            <p>Debitur</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success h-100">
                        <div class="inner">
                            <h3 id="interest_rate_display">{{ number_format($master->rate * 100 ?? 0, 2) }} %</h3>
                            <p>Interest Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning h-100">
                        <div class="inner">
                            <h3 id="eir_exposure_display">{{ number_format($loanWithNoacc->eirex * 100, 10) }} %</h3>
                            <p>EIR EXPOSURE</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger h-100">
                        <div class="inner">
                            <h3 id="eir_calculated_display">{{ number_format($loanWithNoacc->eircalc * 100, 10) }} %</h3>
                            <p>EIR CALCULATED</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Main row (Table) -->
            <div class="row">
                <section class="col-lg-7 connectedSortable">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Amortized
                            </h3>
                            <div class="card-tools">
                                <ul class="nav nav-pills ml-auto"></ul>
                            </div>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content p-0">
                                <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                                    <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        const revenueChartCanvas = document.getElementById('revenue-chart-canvas').getContext('2d');
                        let revenueChart = new Chart(revenueChartCanvas, {
                            type: 'line',
                            data: {
                                labels: @json($labels), // Menggunakan data labels dari controller
                                datasets: [{
                                    label: 'Amortised',
                                    backgroundColor: 'rgba(60,141,188,0.2)',
                                    borderColor: 'rgba(60,141,188,1)',
                                    pointRadius: 5,
                                    pointBackgroundColor: 'rgba(0, 0, 0, 0)', // Warna latar belakang titik (transparan)
                                    pointBorderColor: 'rgba(0, 0, 0, 0)', // Warna border titik (transparan)
                                    data: @json($data) // Menggunakan data dari controller
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                legend: { display: false },
                                scales: {
                                    xAxes: [{
                                        gridLines: { display: false },
                                        ticks: { autoSkip: true }
                                    }],
                                    yAxes: [{ gridLines: { display: false } }]
                                }
                            }
                        });

                        function fetchDebiturInfo(no_acc) {
                            // Menghapus kelas 'active-row' dari semua baris
                            const rows = document.querySelectorAll('.table tbody tr');
                            rows.forEach(row => row.classList.remove('active-row'));

                            // Menambahkan kelas 'active-row' pada baris yang diklik
                            const clickedRow = event.target.closest('tr'); // Menggunakan event yang diteruskan
                            if (clickedRow) {
                                clickedRow.classList.add('active-row');
                            }

                            $.ajax({
                                url: '/dashboard/debitur/' + no_acc, // Pastikan URL ini sesuai dengan route Anda
                                method: 'GET',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        // Update informasi debitur
                                        document.getElementById('deb_name_display').textContent = response.data.deb_name;
                                        document.getElementById('interest_rate_display').textContent = response.data.rate ? (response.data.rate * 100).toFixed(2) + ' %' : 'N/A';
                                        document.getElementById('eir_exposure_display').textContent = response.data.eirex ? (response.data.eirex * 100).toFixed(10) + ' %' : 'N/A';
                                        document.getElementById('eir_calculated_display').textContent = response.data.eircalc ? (response.data.eircalc * 100).toFixed(10) + ' %' : 'N/A';

                                        // Update chart dengan data baru
                                        updateChart(response.data.labels, response.data.data);
                                    } else {
                                        alert('Data not found');
                                    }
                                },
                                error: function() {
                                    alert('Failed to fetch data');
                                }
                            });
                        }

                        function updateChart(labels, data) {
                            revenueChart.data.labels = labels; // Update labels
                            revenueChart.data.datasets[0].data = data; // Update data
                            revenueChart.update(); // Refresh the chart
                        }
                    </script>
                </section>

                <!-- Konten untuk Simple Interest -->
                <section id="simple-content" class="col-lg-7 connectedSortable" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-money-bill-wave mr-1"></i>
                                Simple Interest
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Informasi mengenai Simple Interest akan ditampilkan di sini.</p>
                            <!-- Tambahkan konten yang relevan untuk Simple Interest -->
                        </div>
                    </div>
                </section>

                <!-- Konten untuk Securities -->
                <section id="securities-content" class="col-lg-7 connectedSortable" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-1"></i>
                                Securities
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Informasi mengenai Securities akan ditampilkan di sini.</p>
                            <!-- Tambahkan konten yang relevan untuk Securities -->
                        </div>
                    </div>
                </section>
                <!-- Table Debitur with clickable links -->
                <section class="col-lg-5 connectedSortable">
                    <div class="card bg-gradient-secondary">
                        <div class="card-header border-0">
                            <h3 class="card-title">Tabel Debitur</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Debitur</th>
                                            <th style="text-align: right;">Original Amount (RP)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalOutstanding = 0;
                                        @endphp
                                        @foreach($loans as $index => $loan)
                                        <tr class="{{ $loan->no_acc === $defaultNoAcc ? 'active-row' : '' }}"active-row="">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                   onclick="fetchDebiturInfo('{{ $loan->no_acc }}', event)"
                                                   class="btn btn-link {{ $loan->no_acc === $defaultNoAcc ? 'active-button' : 'text-primary' }}">
                                                    <i class="fas fa-user"></i> {{ $loan->deb_name }}
                                                </a>
                                            </td>
                                            <td style="text-align: right;">{{ number_format($loan->org_bal, 2, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $totalOutstanding += $loan->org_bal;
                                        @endphp
                                    @endforeach
                                    <tr>
                                            <td></td>
                                            <td>Total Original Amount:</td>
                                            <td style="text-align: right;">{{ number_format($totalOutstanding, 2, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    function changeDashboard() {
        const selectedValue = document.querySelector('input[name="engine"]:checked').value;

        // Sembunyikan semua konten dashboard
        document.getElementById('effective-content').style.display = 'none';
        document.getElementById('simple-content').style.display = 'none';
        document.getElementById('securities-content').style.display = 'none';

        // Tampilkan konten yang sesuai
        if (selectedValue === 'effective') {
            document.getElementById('effective-content').style.display = 'block';
        } else if (selectedValue === 'simple') {
            document.getElementById('simple-content').style.display = 'block';
        } else if (selectedValue === 'securities') {
            document.getElementById('securities-content').style.display = 'block';
        }
    }

    // Inisialisasi tampilan dashboard
    changeDashboard(); // Panggil fungsi untuk mengatur tampilan awal
</script>
