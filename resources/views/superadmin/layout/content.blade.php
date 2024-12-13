<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="my-5">Dashboard Super Admin</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>150</h3>
                            <p>Debitur</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>Rp.100.000.00<sup style="font-size: 20px"></sup></h3>
                            <p>Outstanding</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>15.0715686%</h3>
                            <p>EIR EXPOSURE</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>15.2629881%</h3>
                            <p>EIR CALCULATED</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-7 connectedSortable">
                    <!-- Custom tabs (Charts with tabs) -->
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
                                <!-- Area Chart -->
                                <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                                    <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        // Line Chart
                        const revenueChartCanvas = document.getElementById('revenue-chart-canvas').getContext('2d');
                        new Chart(revenueChartCanvas, {
                            type: 'line',
                            data: {
                                labels: ['01/01/2021', '01/03/2021', '01/05/2021', '01/07/2021', '01/08/2021', '01/09/2021', '01/10/2021', '01/11/2021', '01/12/2021'],
                                datasets: [{
                                    label: 'Amortised',  // Changed label here
                                    backgroundColor: 'rgba(60,141,188,0.2)',
                                    borderColor: 'rgba(60,141,188,1)',
                                    pointRadius: false,
                                    data: [0, 10000, 20000, 40000, 60000, 70000, 90000, 80000, 100000]
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
                    </script>
                </section>
                <!-- Right col -->
                <section class="col-lg-5 connectedSortable">
                    <!-- Map card -->
                    <div class="card bg-gradient-secondary">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-dollar-sign -alt mr-1"></i>
                                Tabel Debitur
                            </h3>
                            <!-- card tools -->
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.card-tools -->
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Debitur</th>
                                            <th class="text-end">Outstanding Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td >John Doe</td>
                                            <td class="text-end">Rp 1.000.000</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Jane Smith</td>
                                            <td class="text-end">Rp 500.000</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Michael Johnson</td>
                                            <td class="text-end">Rp 750.000</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Total Outstanding Balance:</td>
                                            <td class="text-end">Rp 2.250.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </section>
            </div>
            <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
