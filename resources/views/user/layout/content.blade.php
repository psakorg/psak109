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

</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="my-5">Dashboard User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stat boxes (Display data) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="deb_name_display">{{ $master->deb_name ?? 'N/A' }}</h3>
                            <p>Debitur</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
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
                    <div class="small-box bg-warning">
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
                    <div class="small-box bg-danger">
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
                                            <th style="text-align: right;">Outstanding Balance (RP)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalOutstanding = 0;
                                        @endphp
                                        @foreach($loans as $index => $loan)
                                        <tr class="{{ $loan->no_acc === $defaultNoAcc ? 'active-row' : '' }}">
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
                                            <td>Total Outstanding Balance:</td>
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
