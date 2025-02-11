<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5" style="padding-right: 50px;">
            <section class="section">
                <div class="section-header">
                    <h4>REPORT OUTSTANDING BALANCE - AMORTIZED COST</h4>
                </div>
                @if(session('pesan'))
                    <div class="alert alert-success">{{ session('pesan') }}</div>
                @endif
                <div class="table-responsive text-center">
                    <div class="d-flex align-items-center mb-2">
                        <button type="button" class="btn btn-primary dropdown-toggle me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-import"></i> Tanggal
                        </button>
                            <select class="form-select me-2" name="date" id="dateInput" 
                                style="width: 80px; height: 40px; font-size: 14px" 
                                onchange="updateReport()">
                                <!-- Will be populated by JavaScript -->
                            </select>

                        <select class="form-select me-2" name="month" style="width: 120px; font-size: 14px" id="monthSelect" onchange="changeMonth()">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>

                        <input type="number" class="form-select me-2" name="year" id="yearInput" 
                               style="width: 100px; font-size: 14px" 
                               value="{{ date('Y') }}" 
                               min="2000" 
                               max="2099">

                    <!-- Tombol Export -->
                    <div class="d-flex gap-2">
                        <form action="{{ route('securities.outstanding-balance-amortized-cost.execute-procedure') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahun" value="{{ request()->query('tahun', date('Y')) }}">
                            <input type="hidden" name="bulan" value="{{ request()->query('bulan', date('n')) }}">
                            <input type="hidden" name="tanggal" value="{{ request()->query('tanggal', date('j')) }}">
                            <button type="submit" class="btn btn-warning btn-icon-split">
                                <span class="icon ">
                                    <i class="fas fa-play"></i>
                                </span>
                                <span class="text">Execute</span>
                            </button>
                        </form>
                        <a href="#" class="btn btn-danger" id="exportPdf">
                            <i class="fas fa-file-pdf"></i> Export to PDF
                        </a>
                        <a href="#" id="exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                        <a href="#" id="exportcsv" class="btn btn-primary">
                            <i class="fas fa-file-csv"></i> Download CSV
                        </a>
                    </div>
                    </div>
                    <table class="table table-striped table-bordered custom-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No.</th>
                                <th style="width: 5%;">Branch Number</th>
                                <th style="width: 15%;">Account Number</th>
                                <th class="text-left" style="width: 20%;">Bond Id</th>
                                <th style="width: 15%; ">Issuer Name</th>
                                <th style="width: 15%; ">GL Account</th>
                                <th style="width: 15%; ">Bond Type</th>
                                <th style="width: 15%; ">GL Group</th>
                                <th style="width: 15%; ">Settlement Date</th>
                                <th style="width: 15%; ">Tenor (TTM)</th>
                                <th style="width: 15%; ">Maturity Date</th>
                                <th style="width: 15%; ">Coupon Rate</th>
                                <th style="width: 10%; ">Yield (YTM)</th>
                                <th style="width: 15%; ">EIR Amortized Cost Exposure</th>
                                <th style="width: 10%; ">EIR Amortized Cost Calculated</th>
                                <th style="width: 15%; ">Face Value</th>
                                <th style="width: 15%; ">Price</th>
                                <th style="width: 15%; ">Mark to Market (MTM)</th>
                                <th style="width: 15%; ">Carrying Amount</th>
                                <th style="width: 15%; ">Unamortized At Discount</th>
                                <th style="width: 15%; ">Unamortized At Premium</th>
                                <th style="width: 15%; ">Unamortized Brokerage Fee</th>
                                <th style="width: 15%; ">Cummlative Time Gap</th>
                                <th style="width: 15%; ">Unreleased Gain/Losses </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nourut = 0;
                                $sumFaceValue = 0;
                                $sumMtmPrice = 0;
                                $sumCarryingAmount = 0;
                                $sumAtdiscount = 0;
                                $sumAtpremium = 0;
                                $sumBrokerage = 0;
                                $sumTimegap = 0;
                                $sumGainLoss = 0;
                            @endphp
                            @foreach ($securities as $security)
                                @php 
                                    $nourut = $nourut + 1;
                                    $sumFaceValue = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->face_value));
                                    $sumMtmPrice = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->mtm_price));
                                    $sumCarryingAmount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->carrying_amount));
                                    $sumAtdiscount = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->unamortized_atdiscount));
                                    $sumAtpremium = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->unamortized_atpremium));
                                    $sumBrokerage = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->unamortized_brokerage));
                                    $sumTimegap = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_timegap));
                                    $sumGainLoss = $securities->sum(fn($item) => (float) str_replace(['$', ','], '', $item->cum_gain_losses));
                                @endphp
                                <tr>
                                    <td>{{ $nourut }}</td>
                                    <td class="text-center">{{$security->no_branch}}</td>
                                    <td>
                                        <div class="dropdown d-flex justify-content-end dropend">
                                            <a href="#" class="dropdown-toggle text-primary" data-bs-toggle="dropdown" style="text-decoration: none;">
                                                {{ $security->no_acc }}
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('report-expected-cashflow.view', ['no_acc' => $security->no_acc, 'id_pt' => $user->id_pt]) }}">
                                                        Expected Cashflow
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('report-calculated-accrual-coupon.view', ['no_acc' => $security->no_acc, 'id_pt' => $user->id_pt]) }}">
                                                        Calculated Accrual Coupon
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('report-amortised-cost.view', ['no_acc' => $security->no_acc, 'id_pt' => $user->id_pt]) }}">
                                                        Amortised Cost
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('report-amortised-initial-disc.view', ['no_acc' => $security->no_acc, 'id_pt' => $user->id_pt]) }}">
                                                        Amortised Initial At Discount
                                                    </a>
                                                </li>
                                                <li>
                                                        <a class="dropdown-item" href="{{ route('report-amortised-initial-prem.view', ['no_acc' => $security->no_acc, 'id_pt' => $user->id_pt]) }}">
                                                        Amortised Initial At Premium
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('report-amortised-initial-brokerage-fee.view', ['no_acc' => $security->no_acc, 'id_pt' => $user->id_pt]) }}">
                                                        Amortised Initial Brokerage Fee
                                                    </a>
                                                </li>
                                                <!-- <li>
                                                    <a class="dropdown-item" href="">
                                                        Amortised Initial Brokerage Fee
                                                    </a>
                                                </li> -->
                                            </ul>
                                        </div>
                                    </td>

                                    <td class="text-center">{{$security->bond_id}}</td>
                                    <td class="text-left">{{$security->issuer_name}}</td>
                                    <td class="text-center">{{$security->coa}}</td>
                                    <td class="text-center">{{$security->bond_type}}</td>
                                    <td class="text-center">{{$security->gl_group}}</td>
                                    <td>{{date('d/m/Y', strtotime($security->org_date_dt))}}</td>
                                    <td>{{ $security->tenor}} days</td>
                                    <td>{{ date('d/m/Y', strtotime($security->mtr_date_dt)) }}</td>
                                    <td>{{ number_format($security->coupon_rate*100,5) }}%</td>
                                    <td>{{ number_format($security->yield*100,5) }}%</td>
                                    <td>{{ number_format($security->eirex*100,14)}}%</td>
                                    <td>{{ number_format($security->eircalc*100,14)}}%</td>                                    
                                    <td class="text-right">{{number_format((float) str_replace(['$', ','], '',$security->face_value))}}</td>
                                    <td class="text-right">{{ number_format($security->price,5)}}</td>
                                    <td class="text-right">{{ number_format((float) str_replace(['$', ','], '', $security->mtm_price)) }}</td>
                                    <td class="text-right">{{number_format((float) str_replace(['$', ','], '',$security->carrying_amount))}}</td>
                                    <td class="text-right">{{ number_format((float) str_replace(['$', ','], '', $security->unamortized_atdiscount)) }}</td>
                                    <td class="text-right">{{ number_format((float) str_replace(['$', ','], '',$security->unamortized_atpremium)) }}</td>
                                    <td class="text-right">{{ number_format((float) str_replace(['$', ','], '',$security->unamortized_brokerage)) }}</td>
                                    <td class="text-right">{{ number_format((float) str_replace(['$', ','], '',$security->cum_timegap)) }}</td>
                                    <td class="text-right">{{ number_format((float) str_replace(['$', ','], '',$security->cum_gain_losses)) }}</td>
                                </tr>
                            @endforeach
                            <!-- Row Total / Average -->
                            <tr class="table-secondary font-weight-normal">
                                    <td colspan="11" class="text-center"><strong>TOTAL:</strong></td>
                                    <td class="text-center">{{ number_format($securities->avg('coupon_rate')*100,5).'%' }}</td>
                                    <td class="text-center">{{ number_format($securities->avg('yield')*100,5).'%' }}</td>
                                    <td class="text-center">{{ number_format($securities->avg('eirex')*100,14).'%' }}</td>
                                    <td class="text-center">{{ number_format($securities->avg('eircalc')*100,14).'%' }}</td>
                                    <td class="text-right">{{ number_format($sumFaceValue) }}</td>
                                    <td class="text-right">{{ number_format($securities->avg('price'), 5) }}</td>
                                    <td class="text-right">{{ number_format($sumMtmPrice) }}</td>
                                    <td class="text-right">{{ number_format($sumCarryingAmount) }}</td>
                                    <td class="text-right">{{ number_format($sumAtdiscount) }}</td>
                                    <td class="text-right">{{ number_format($sumAtpremium) }}</td>
                                    <td class="text-right">{{ number_format($sumBrokerage) }}</td>
                                    <td class="text-right">{{ number_format($sumTimegap) }}</td>
                                    <td class="text-right">{{ number_format($sumGainLoss) }}</td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="showing-entries">
                    Showing
                    {{$securities->firstItem()}}
                    to
                    {{$securities->lastItem()}}
                    of
                    {{$securities->total()}}
                    Results
                </div>
                <div class="d-flex align-items-center">
                    {{ $securities->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-4') }}
                    <label for="per_page" class="form-label mb-0" style="font-size: 0.8rem; margin-right: 15px; margin-left:30px;">Show</label>
                    <select id="per_page" class="form-select form-select-sm" onchange="changePerPage()" style="width: auto;">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    function changePerPage() {
        const perPage = document.getElementById('per_page').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page');
        window.location.href = url;
    }

    const reportUrl = "{{ route('securities.outstanding-balance-amortized-cost.index') }}";

    document.addEventListener('DOMContentLoaded', function() {
        const selectedMonth = "{{ $bulan }}";
        const selectedYear = "{{ $tahun }}";
        const selectedDate = "{{ $tanggal }}";
        
        document.getElementById('monthSelect').value = parseInt(selectedMonth);
        document.getElementById('yearInput').value = selectedYear;
        
        // Initialize days first
        updateDays();
        
        // Then set the selected date
        document.getElementById('dateInput').value = parseInt(selectedDate);
    });

    // Event listener untuk perubahan bulan atau tahun
    document.getElementById('monthSelect').addEventListener('change', function() {
        updateDays();
        updateReport();
    });
    document.getElementById('yearInput').addEventListener('change', function() {
        updateDays();
        updateReport();
    });

    function updateReport() {
        const month = document.getElementById('monthSelect').value.padStart(2, '0');
        const year = document.getElementById('yearInput').value;
        const date = document.getElementById('dateInput').value.padStart(2, '0');
        const branch = '{{ $user->id_pt }}';
        
        window.location.href = `${reportUrl}?bulan=${month}&tahun=${year}&tanggal=${date}&branch=${branch}`;
    }
    // Update export URL dynamically based on selected month and year
    document.getElementById('exportExcel').addEventListener('click', function (e) {
        e.preventDefault();
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearInput').value;
        const date = document.getElementById('dateInput').value;
        

        // Redirect to the export route with query parameters
        window.location.href = `{{ route('report-outstanding-amortized-cost.exportExcel', ['id_pt' => Auth::user()->id_pt]) }}?bulan=${month}&tahun=${year}&tanggal=${date}`;
    });
    document.getElementById('exportPdf').addEventListener('click', function (e) {
        e.preventDefault();
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearInput').value;
        const date = document.getElementById('dateInput').value;


        // Redirect to the export route with query parameters
        window.location.href = `{{ route('report-outstanding-amortized-cost.exportPDF', ['id_pt' => Auth::user()->id_pt]) }}?bulan=${month}&tahun=${year}&tanggal=${date}`;
    });
    document.getElementById('exportcsv').addEventListener('click', function (e) {
        e.preventDefault();
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearInput').value;
        const date = document.getElementById('dateInput').value;


        // Redirect to the export route with query parameters
        window.location.href = `{{ route('report-outstanding-amortized-cost.exportcsv', ['id_pt' => Auth::user()->id_pt]) }}?bulan=${month}&tahun=${year}&tanggal=${date}`;
    });

    function updateDays() {
        const month = parseInt(document.getElementById('monthSelect').value);
        const year = parseInt(document.getElementById('yearInput').value);
        const dateSelect = document.getElementById('dateInput');
        const currentSelectedDate = dateSelect.value; // Simpan tanggal yang dipilih saat ini
        
        // Clear existing options
        dateSelect.innerHTML = '';
        
        // Get number of days in the selected month
        const daysInMonth = new Date(year, month, 0).getDate();
        
        // Populate days
        for(let i = 1; i <= daysInMonth; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            dateSelect.appendChild(option);
        }
        
        // Restore selected date if it exists and is valid for the current month
        if (currentSelectedDate && currentSelectedDate <= daysInMonth) {
            dateSelect.value = currentSelectedDate;
        } else {
            // If the date is invalid for the new month, default to 1
            dateSelect.value = 1;
        }
    }

</script>


<!-- Custom CSS -->
<style>
    body {
        display: fixed;
        background-color: #f4f7fc;
        font-family: 'Arial', sans-serif;
    }
    .main-content {
        margin-left: 20px; /* Diperbarui untuk menghapus margin kiri */
        width: 100%; /* Diperbarui lebar */
        padding-top: fixed;
        padding-right: fixed;
    }
    /* STYLE PAGINATION */
    .showing-entries {
        font-size: 14px;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
    #per_page {
    width: 80px; /* Lebar default */
    min-width: 100px; /* Lebar minimum */
    max-width: 150px; /* Lebar maksimum */
    transition: all 0.3s ease; /* Efek transisi halus */
    border-radius: 5px; /* Sudut membulat */
    padding: 5px; /* Jarak dalam */
    cursor: pointer; /* Gaya kursor */
}

/* Tambahkan efek saat dropdown dibuka */
#per_page:focus {
    outline: none; /* Hilangkan outline default */
    box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5); /* Shadow saat aktif */
    border-color: #007bff; /* Warna border aktif */
}

#per_page:focus {
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        background-color: #f0f8ff;
        transform: scale(1.05);
    }

    #per_page option {
        transition: background-color 0.2s ease;
    }

    #per_page option:hover {
        background-color: #73b9ff;
    }
    /* STYLE PAGINATION */

    .section-header h4 {
        font-size: 26px;
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .custom-table {
        width: 100%; /* Full width to use available space */
        margin: 20px auto;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        border-radius: 12px;
        font-size: 10px;
    }
    .custom-table th, .custom-table td {
        padding: 8px 12px;
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
        font-size: 10px;
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
    .custom-table td a.btn-info i {
        margin-right: 5px;
        vertical-align: middle;
    }
    .custom-table td a.btn-info:hover {
        background-color: #0097a7;
        transform: scale(1.05);
    }

    /* Penambahan style terkait popup menu mulai dari sini */

    .no-data-message h5 {
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .no-data-message p {
        color: #888;
        margin-bottom: 0;
    }
    
    .dropdown-menu li {
        position: relative;
    }
    
    .dropdown-submenu {
        display: none;
        position: absolute;
        left: 100%;
        top: 0;
    }
    
    .dropdown-menu > li:hover > .dropdown-submenu {
        display: block;
    }

    .dropdown-item {
        padding: 8px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .dropdown-item i {
        margin-right: 8px;
        width: 20px;
        line-height: 1;
        vertical-align: middle;
    }

    .dropdown-item i.float-end {
        margin-right: 0;
        margin-left: 8px;
    }

    .modal-content {
        border-radius: 8px;
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control, .form-select {
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 8px 12px;
        font-size: 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .text-success {
        color: #28a745;
    }

    .text-danger {
        color: #dc3545;
    }

    #entityLabel, #accountLabel {
        margin-left: 10px;
        font-size: 14px;
        border-radius: 4px;
        display: inline-block;
        min-width: 150px;
    }

    .alert {
        margin-bottom: 0;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .clickable-account {
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
    }

    .clickable-account:hover {
        color: #0056b3;
    }

    .dropdown-menu {
        min-width: 200px;
        padding: 0.5rem 0;
        margin: 0;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 0.25rem;
    }

    /* .dropdown-item {
        padding: 8px 20px;
        color: #212529;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #16181b;
    } */

    .dropdown-submenu {
        display: none;
        position: absolute;
        left: 100%;
        top: -7px;
        z-index: 1001;
        min-width: 200px;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 0.25rem;
    }

    .dropdown-menu > li:hover > .dropdown-submenu {
        display: block;
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 20px;
        line-height: 1;
        vertical-align: middle;
        /* color: #212529; Memastikan ikon juga hitam */
    }

    .dropdown-item i.float-end {
        margin-right: 0;
        margin-left: 8px;
    }

    .container {
        overflow: visible !important;
    }

    .section {
        overflow: visible !important;
    }

    .dropdown {
        position: relative;
    }

    /* Penambahan style terkait popup menu berakhir disini */

    .dropdown-menu {
        min-width: 200px;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,.15);
        z-index: 1021; /* Memastikan dropdown muncul di atas elemen lain */
    }

    .table-responsive {
        overflow-x: visible !important; /* Mengubah overflow-x menjadi visible */
        overflow-y: visible !important; /* Mengubah overflow-y menjadi visible */
        z-index: 1; /* Memastikan tabel memiliki z-index yang lebih rendah */
    }

    .text-primary:hover {
        color: #0056b3 !important;
        text-decoration: underline !important;
    }

    /* Memastikan container dan section tidak memotong dropdown */
    .container, .section {
        overflow: visible !important;
    }

    /* Add this CSS rule */
    .dropdown-menu .dropdown-item {
        color: #212529 !important; /* Dark gray/almost black color */
        text-decoration: none;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #16181b !important;
    }

</style>

<!-- Font Awesome Link -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous">
function changePerPage() {
        const perPage = document.getElementById('per_page').value;
        window.location.href = `?per_page=${perPage}`; // Redirect dengan parameter per_page
    }
</script>

<script>
function showModal(type) {
    console.log('showModal function called with type:', type);
    const reportTypeSelect = document.getElementById('reportType');
    const accountNumberInput = document.getElementById('accountNumber');
    const accountNumberLabel = document.getElementById('accountNumberLabel');
    const accountLabel = document.getElementById('accountLabel');
    const outstandingDateInputs = document.getElementById('outstandingDateInputs');
    reportTypeSelect.innerHTML = ''; 
    
    // Show/hide input fields based on report type
    if (type.includes('outstanding')) {
        // Hide account number input and all related elements
        accountNumberInput.style.display = 'none';
        accountNumberLabel.style.display = 'none';
        accountLabel.style.display = 'none';
        accountNumberInput.removeAttribute('required');
        outstandingDateInputs.style.display = 'block';
        
        // Clear any existing account number value
        accountNumberInput.value = '';
        accountLabel.textContent = '';
        
        // Set default values for month and year
        document.getElementById('modalMonth').value = document.getElementById('monthSelect').value;
        document.getElementById('modalYear').value = document.getElementById('yearInput').value;
        document.getElementById('modalDate').value = document.getElementById('dateInput').value;
    } else {
        accountNumberInput.style.display = 'block';
        accountNumberLabel.style.display = 'block';
        accountLabel.style.display = 'block';
        accountNumberInput.setAttribute('required', 'required');
        outstandingDateInputs.style.display = 'none';
    }
    
    let options;
    switch(type) {
        case 'report_expected_cashflow':
            options = `
                <option value="report_expected_cashflow">Effective</option>
            `;
            break;
            
        case 'amortised_cost_effective':
        case 'amortised_cost_simple':
            options = `
                <option value="amortised_cost_effective">Effective</option>
                <option value="amortised_cost_simple">Simple Interest</option>
            `;
            break;
            
        case 'amortised_initial_cost_effective':
        case 'amortised_initial_cost_simple':
            options = `
                <option value="amortised_initial_cost_effective">Effective</option>
                <option value="amortised_initial_cost_simple">Simple Interest</option>
            `;
            break;
            
        case 'amortised_initial_fee_effective':
        case 'amortised_initial_fee_simple':
            options = `
                <option value="amortised_initial_fee_effective">Effective</option>
                <option value="amortised_initial_fee_simple">Simple Interest</option>
            `;
            break;
            
        case 'expected_cashflow_effective':
        case 'expected_cashflow_simple':
            options = `
                <option value="expected_cashflow_effective">Effective</option>
                <option value="expected_cashflow_simple">Simple Interest</option>
            `;
            break;
            
        // case 'initial_recognition_effective':
        // case 'initial_recognition_simple':
        //     options = `
        //         <option value="initial_recognition_effective">Effective</option>
        //         <option value="initial_recognition_simple">Simple Interest</option>
        //     `;
        //     break;
            
        case 'outstanding_effective':
        case 'outstanding_simple':
            options = `
                <option value="outstanding_effective">Effective</option>
                <option value="outstanding_simple">Simple Interest</option>
            `;
            break;
            
        case 'journal_effective':
        case 'journal_simple':
            options = `
                <option value="journal_effective">Effective</option>
                <option value="journal_simple">Simple Interest</option>
            `;
            break;
            
        default:
            console.error('Tipe report tidak valid:', type);
            options = '<option value="">Pilih tipe report</option>';
            break;
    }
    
    reportTypeSelect.innerHTML = options;
    reportTypeSelect.value = type; // Set nilai sesuai tipe yang dipilih
    
    $('#reportModal').modal('show');
}
</script>

<script>
function showModalWithAccount(accountNumber, reportType) {
    const branch = '{{ $user->id_pt }}';
    let url;
    
    switch(reportType) {
        case 'expected_cashflow':
            url = `/securities/expected-cashflow/view/${accountNumber}/${branch}`;
            break;
        case 'calculated_accrual_coupon':
            url = `/securities/calculated-accrual-coupon/view/${accountNumber}/${branch}`;
            break;
        case 'amortised_cost':
            url = `/securities/amortised-cost/view/${accountNumber}/${branch}`;
            break;
        case 'amortised_initial_disc':
            url = `/securities/amortised-initial-disc/view/${accountNumber}/${branch}`;
            break;
        case 'amortised_initial_prem':
            url = `/securities/amortised-initial-prem/view/${accountNumber}/${branch}`;
            break;
        case 'amortised_initial_brok':
            url = `/securities/amortised-initial-brok/view/${accountNumber}/${branch}`;
            break;
    }
    
    if (url) {
        window.location.href = url;
    }
}
</script>

<!-- Add this before closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('swal'))
            Swal.fire({
                title: "{{ session('swal.title') }}",
                text: "{{ session('swal.text') }}",
                icon: "{{ session('swal.icon') }}",
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
</body>
</html>
