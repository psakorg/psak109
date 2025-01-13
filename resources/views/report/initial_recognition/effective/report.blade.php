<head>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Poppeer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5" style="padding-right: 50px; overflow: visible;">
            <section class="section" style="overflow: visible;">
                <div class="section-header">
                    <h4>REPORT INITIAL RECOGNITION NEW LOAN BY ENTITY - CONTRACTUAL EFFECTIVE</h4>
                </div>
                
                <div class="d-flex justify-content-start mb-0 align-items-center">
                    <div class="dropdown me-3">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-import"></i> Bulan/Tahun
                        </button>
                        
                        <!-- Request Pak Darwis, matikan popup menu dari tombol tersebut. Cukup dari klik no_acc saja. -->
                        <!--
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Accrued Interest <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('accrual_interest_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('accrual_interest_simple')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Amortised Cost <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('amortised_cost_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('amortised_cost_simple')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Amortised Initial Cost <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('amortised_initial_cost_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('amortised_initial_cost_simple')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Amortised Initial Fee <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('amortised_initial_fee_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('amortised_initial_fee_simple')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Expected Cash Flow <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('expected_cashflow_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('expected_cashflow_simple')">Simple Interest</a></li>
                                </ul>
                            </li>
                        -->
                            <!-- <li> Ini sudah di-remark lebih dulu
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Initial Recognition <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('initial_recognition_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('initial_recognition_simple')">Simple Interest</a></li>
                                </ul>
                            </li> -->
                        <!--
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Outstanding <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('outstanding_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('outstanding_simple')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Journal <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('journal_effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('journal_simple')">Simple Interest</a></li>
                                </ul>
                            </li>
                        </ul>
                        -->
                    </div>

                    <!-- Tambahan input bulan dan tahun -->
                    <div class="d-flex align-items-center">
                        <select class="form-select me-2" style="width: 120px;" id="monthSelect">
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

                        <input type="number" class="form-select" id="yearInput" 
                               style="width: 100px;" 
                               value="{{ date('Y') }}" 
                               min="2000" 
                               max="2099">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-danger" id="exportPdf">
                            <i class="fas fa-file-pdf"></i> Export to PDF
                        </a>
                        <a href="#" id="exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>
                
                <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
                    <table class="table table-striped table-bordered custom-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Entity Number</th>
                                <th>Account Number</th>
                                <th>Debitor Name</th>
                                <th>GL Account</th>
                                <th>Loan Type</th>
                                <th>GL Group</th>
                                <th>Original Date</th>
                                <th>Term (Months)</th>
                                <th>Interest Rate</th>
                                <th>Maturity Date</th>
                                <th>Payment Amount</th>
                                <th>Original Balance</th>
                                <th>Current Balance</th>
                                <th>Carrying Amount</th>
                                <th>EIR Amortised Cost Exposure</th>
                                <th>EIR Amortised Cost Calculated</th>
                                <th>EIR Calculated Convertion</th>
                                <th>EIR Calculated Transaction Cost</th>
                                <th>EIR Calculated UpFront Fee</th>
                                <th>Outstanding Amount</th>
                                <th>Outstanding Amount Initial Transaction Cost</th>
                                <th>Outstanding Amount Initial UpFront Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($loans) > 0)
                                @php $total = [
                                    'org_bal' => 0,
                                    'oldbal' => 0,
                                    'baleir' => 0,
                                    'rate' => 0,
                                    'eirex' => 0,
                                    'eircalc' => 0,
                                    'eircalc_conv' => 0,
                                    'eircalc_cost' => 0,
                                    'eircalc_fee' => 0,
                                    'outsamtconv' => 0,
                                    'outsamtcost' => 0,
                                    'outsamtfee' => 0,
                                    'pmtamt' => 0
                                ]; @endphp
                                
                                @foreach($loans as $index => $loan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $loan->no_branch }}</td>
                                        <td>
                                            <div class="relative">
                                            <div class="dropdown">
                                                <span class="clickable-account" data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ $loan->no_acc }}
                                                </span>
                                                
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                                            Accrued Interest <i class="fas fa-chevron-right float-end"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'accrual_interest_effective')">Effective</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'accrual_interest_simple')">Simple Interest</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                                            Amortised Cost <i class="fas fa-chevron-right float-end"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'amortised_cost_effective')">Effective</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'amortised_cost_simple')">Simple Interest</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                                            Amortised Initial Cost <i class="fas fa-chevron-right float-end"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'amortised_initial_cost_effective')">Effective</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'amortised_initial_cost_simple')">Simple Interest</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                                            Amortised Initial Fee <i class="fas fa-chevron-right float-end"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'amortised_initial_fee_effective')">Effective</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'amortised_initial_fee_simple')">Simple Interest</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                                            Expected Cash Flow <i class="fas fa-chevron-right float-end"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'expected_cashflow_effective')">Effective</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'expected_cashflow_simple')">Simple Interest</a></li>
                                                        </ul>
                                                    </li>
                                                    <!-- <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                                            Outstanding <i class="fas fa-chevron-right float-end"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'outstanding_effective')">Effective</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'outstanding_simple')">Simple Interest</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                                            Journal <i class="fas fa-chevron-right float-end"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'journal_effective')">Effective</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="showModalWithAccount('{{ $loan->no_acc }}', 'journal_simple')">Simple Interest</a></li>
                                                        </ul>
                                                    </li> -->
                                                </ul>
                                            </div>
                            </div>
                                        </td>
                                        <td>{{ $loan->deb_name }}</td>
                                        <td>{{ $loan->coa }}</td>
                                        <td>{{ $loan->ln_type }}</td>
                                        <td>{{ $loan->glgroup }}</td>
                                        <td>{{ $loan->orgdtconv }}</td>
                                        <td>{{ $loan->term }}</td>
                                        <td>{{ number_format($loan->rate*100, 5) }}%</td>
                                        <td>{{ $loan->mtrdtconv }}</td>
                                        <td class="text-right">{{ number_format($loan->pmtamt, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->org_bal, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->oldbal, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->baleir, 0) }}</td>
                                        <td>{{ number_format($loan->eirex*100, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc*100, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc_conv*100, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc_cost*100, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc_fee*100, 14) }}%</td>
                                        <td class="text-right">{{ number_format($loan->outsamtconv, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->outsamtcost, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->outsamtfee, 0) }}</td>
                                    </tr>
                                    @php
                                        $total['org_bal'] += $loan->org_bal;
                                        $total['oldbal'] += $loan->oldbal;
                                        $total['baleir'] += $loan->baleir;
                                        $total['rate'] += $loan->rate;
                                        $total['eirex'] += $loan->eirex;
                                        $total['eircalc'] += $loan->eircalc;
                                        $total['eircalc_conv'] += $loan->eircalc_conv;
                                        $total['eircalc_cost'] += $loan->eircalc_cost;
                                        $total['eircalc_fee'] += $loan->eircalc_fee;
                                        $total['outsamtconv'] += $loan->outsamtconv;
                                        $total['outsamtcost'] += $loan->outsamtcost;
                                        $total['outsamtfee'] += $loan->outsamtfee;
                                        $total['pmtamt'] += $loan->pmtamt;
                                    @endphp
                                @endforeach
                                
                                <!-- Row Total/Average -->
                                <tr class="font-weight-normal">
                                    <td colspan="8">TOTAL</td>
                                    <td></td>
                                    <td>{{ count($loans) > 0 ? number_format(($total['rate'] * 100 / count($loans)), 5) : 0 }}%</td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($total['pmtamt'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['org_bal'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['oldbal'], 0) }}</td>
                                    <td class="text-right"></td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eirex'] * 100 / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc'] * 100 / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc_conv'] * 100 / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc_cost'] * 100 / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc_fee'] * 100 / count($loans), 14) : 0 }}%</td>
                                    <td class="text-right">{{ number_format($total['outsamtconv'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['outsamtcost'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['outsamtfee'], 0) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="23" class="text-left py-4">
                                        <div class="no-data-message">
                                            <h5>No Data Found For Year {{ $tahun }} Month {{ $bulan }}</h5>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>


<style>
    /* Gunakan style yang sama dengan accrual interest */
    .custom-table {
        width: 100%;
        margin: 20px auto;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .custom-table th, .custom-table td {
        padding: 8px 12px;
        white-space: nowrap;
    }
    
    .text-right {
        text-align: right;
        padding-right: 20px;
    }
    
    /* .no-data-message {
        padding: 20px;
    } */
    
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
        margin: 0;
        padding: 0.5rem 0;
        position: absolute;
        z-index: 1000;
        min-width: 200px;
        background-color: #fff; /* Memastikan background putih */
    }

    .dropdown-item {
        padding: 8px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #212529 !important; /* Memastikan teks selalu hitam */
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #16181b !important;
    }

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
        color: #212529; /* Memastikan ikon juga hitam */
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
</style>

<!-- Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Report Parameters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    <div class="mb-3  ">
                        <label for="reportType" class="form-label me-2">Report Type</label>
                        <select class="form-select" id="reportType" required style="width: 100%;">
                            <!-- Options will be populated by JavaScriptt -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="entityNumber" class="form-label">Entity Number</label>
                        <div class="d-flex align-items-center">
                            <input type="number" 
                                   class="form-control" 
                                   id="entityNumber" 
                                   required
                                   @if(!$isSuperAdmin) disabled @endif
                                   value="{{ $user->id_pt ?? '' }}">
                            <span id="entityLabel" class="ms-3 text-muted" style="display: inline-block;"></span>
                        </div>
                        <small id="entityError" class="text-danger" style="display: none;">Data tidak ditemukan</small>
                    </div>
                    <div class="mb-3" id="accountNumberSection">
                        <label for="accountNumber" id="accountNumberLabel" class="form-label">Account Number</label>
                        <div class="d-flex align-items-center">
                            <input type="number" class="form-control" id="accountNumber" required>
                            <span id="accountLabel" class="ms-3 text-muted"></span>
                        </div>
                        <small id="accountError" class="text-danger" style="display: none;">Data tidak ditemukan</small>
                    </div>
                        <!-- Tambahan input bulan dan tahun untuk outstanding -->
                        <div class="mb-3" id="outstandingDateInputs" style="display: none;">
                        <label class="form-label">Period</label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-select w-100" id="modalMonth">
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
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-select w-100" id="modalYear" 
                                    value="{{ date('Y') }}" 
                                    min="2000" 
                                    max="2099">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="viewReport()">View Report</button>
            </div>
        </div>
    </div>
</div>

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
    } else {
        accountNumberInput.style.display = 'block';
        accountNumberLabel.style.display = 'block';
        accountLabel.style.display = 'block';
        accountNumberInput.setAttribute('required', 'required');
        outstandingDateInputs.style.display = 'none';
    }
    
    let options;
    switch(type) {
        case 'accrual_interest_effective':
        case 'accrual_interest_simple':
            options = `
                <option value="accrual_interest_effective">Effective</option>
                <option value="accrual_interest_simple">Simple Interest</option>
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

function viewReport() {
    console.log('viewReport function called');
    
    const form = document.getElementById('reportForm');
    if (!form.checkValidity()) {
        console.log('Form validation failed');
        form.reportValidity();
        return;
    }

    const reportType = document.getElementById('reportType').value;
    const entityNumber = document.getElementById('entityNumber').value;
    const accountNumber = document.getElementById('accountNumber').value;

    console.log('Report Type:', reportType);
    console.log('Entity Number:', entityNumber);
    console.log('Account Number:', accountNumber);

    if (reportType.includes('outstanding')) {
        const month = document.getElementById('modalMonth').value;
        const year = document.getElementById('modalYear').value;
        console.log('Outstanding report - Month:', month, 'Year:', year);
        closeModal();
        redirectToReport(reportType, null, entityNumber, month, year);
        return;
    }

    // Tentukan URL pengecekan berdasarkan jenis report
    let checkUrl;
    switch(reportType) {
        case 'accrual_interest_effective':
            checkUrl = `/check-report-accrual-effective/${accountNumber}/${entityNumber}`;
            break;
        case 'accrual_interest_simple':
            checkUrl = `/check-report-accrual-simple/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_cost_effective':
            checkUrl = `/check-report-amortised-cost-effective/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_cost_simple':
            checkUrl = `/check-report-amortised-cost-simple/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_cost_effective':
            checkUrl = `/check-report-amortised-initial-cost-effective/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_cost_simple':
            checkUrl = `/check-report-amortised-initial-cost-simple/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_fee_effective':
            checkUrl = `/check-report-amortised-initial-fee-effective/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_fee_simple':
            checkUrl = `/check-report-amortised-initial-fee-simple/${accountNumber}/${entityNumber}`;
            break;
        case 'expected_cashflow_effective':
            checkUrl = `/check-report-expected-cashflow-effective/${accountNumber}/${entityNumber}`;
            break;
        case 'expected_cashflow_simple':
            checkUrl = `/check-report-expected-cashflow-simple/${accountNumber}/${entityNumber}`;
            break;
        // case 'initial_recognition_effective':
        //     checkUrl = `/check-report-initial-recognition-effective/${accountNumber}/${entityNumber}`;
        //     break;
        // case 'initial_recognition_simple':
        //     checkUrl = `/check-report-initial-recognition-simple/${accountNumber}/${entityNumber}`;
        //     break;
        case 'outstanding_effective':
            checkUrl = `/check-report-outstanding-effective/${accountNumber}/${entityNumber}`;
            break;
        case 'outstanding_simple':
            checkUrl = `/check-report-outstanding-simple/${accountNumber}/${entityNumber}`;
            break;
        case 'journal_effective':
            checkUrl = `/check-report-journal-effective/${accountNumber}/${entityNumber}`;
            break;
        case 'journal_simple':
            checkUrl = `/check-report-journal-simple/${accountNumber}/${entityNumber}`;
            break;
    }
    
    console.log('Check URL:', checkUrl);

    // Cek ketersediaan data
    fetch(checkUrl)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                closeModal();
                redirectToReport(reportType, accountNumber, entityNumber);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Data tidak ditemukan',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Terjadi kesalahan saat memeriksa data',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}

function redirectToReport(reportType, accountNumber, entityNumber, month, year) {
    let url;
    switch(reportType) {
        case 'accrual_interest_effective':
            url = `/report-accrual-effective/view/${accountNumber}/${entityNumber}`;
            break;
        case 'accrual_interest_simple':
            url = `/report/accrual-interest/simple-interest/view/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_cost_effective':
            url = `/report-amortised-cost-effective/view/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_cost_simple':
            url = `/report-amortised-cost-simple-interest/view/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_cost_effective':
            url = `/report-amortised-initial-cost-effective/view/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_cost_simple':
            url = `/report-amortised-initial-cost-simple-interest/view/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_fee_effective':
            url = `/report-amortised-initial-fee-effective/view/${accountNumber}/${entityNumber}`;
            break;
        case 'amortised_initial_fee_simple':
            url = `/report-amortised-initial-fee-simple-interest/view/${accountNumber}/${entityNumber}`;
            break;
        case 'expected_cashflow_effective':
            url = `/report-expective-cash-flow-effective/view/${accountNumber}/${entityNumber}`;
            break;
        case 'expected_cashflow_simple':
            url = `/report-expective-cash-flow-simple-interest/view/${accountNumber}/${entityNumber}`;
            break;
        // case 'initial_recognition_effective':
        //     url = `/report-initial-recognition-effective/view/${accountNumber}/${entityNumber}`;
        //     break;
        // case 'initial_recognition_simple':
        //     url = `/report-initial-recognition-simple/view/${accountNumber}/${entityNumber}`;
        //     break;
        case 'outstanding_effective':
            url = `/report-outstanding-effective/view/${entityNumber}?bulan=${month}&tahun=${year}`;
            break;
        case 'outstanding_simple':
            url = `/report-outstanding-simple-interest/view/${entityNumber}?bulan=${month}&tahun=${year}`;
            break;
        case 'journal_effective':
            url = `/report-journal-effective/view/${accountNumber}/${entityNumber}`;
            break;
        case 'journal_simple':
            url = `/report-journal-simple-interest/view/${accountNumber}/${entityNumber}`;
            break;
    }
    window.location.href = url;
}

// Fungsi untuk menutup modal
function closeModal() {
    $('#reportModal').modal('hide');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
}

// Event listener saat dokumen siap
$(document).ready(function() {
    // Reset modal state saat halaman dimuat
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    // Handle tombol close
    $('[data-bs-dismiss="modal"]').on('click', function(e) {
        e.preventDefault();
        closeModal();
    });

    // Tambahkan event listener untuk modal
    $('#reportModal').on('shown.bs.modal', function () {
        // Re-check entity saat modal ditampilkan
        if (!{{ $isSuperAdmin ? 'true' : 'false' }}) {
            const entityNumber = document.getElementById('entityNumber').value;
            if (entityNumber) {
                fetch(`/check-entity/${entityNumber}`)
                    .then(response => response.json())
                    .then(data => {
                        const entityLabel = document.getElementById('entityLabel');
                        if (data.success) {
                            entityLabel.innerHTML = data.entity_name;
                            entityLabel.style = `
                                display: inline-block;
                                visibility: visible;
                                margin-left: 10px;
                                padding: 4px 8px;
                                background-color: #d4edda;
                                color: #155724;
                                border: 1px solid #c3e6cb;
                                border-radius: 4px;
                                font-size: 14px;
                            `;
                        }
                    });
            }
        }
    });
});

// Tambahkan variable untuk menyimpan URL route
const reportUrl = "{{ route('report-initial-recognition.index') }}";

// Set nilai default untuk bulan dan tahun dari parameter URL atau data yang dikirim dari controller
document.addEventListener('DOMContentLoaded', function() {
    // Ambil nilai bulan dari controller
    const selectedMonth = "{{ $bulan ?? date('n') }}";
    const selectedYear = "{{ $tahun ?? date('Y') }}";
    
    // Set nilai default untuk select bulan
    document.getElementById('monthSelect').value = selectedMonth;
    document.getElementById('yearInput').value = selectedYear;
});

// Event listener untuk perubahan bulan atau tahun
document.getElementById('monthSelect').addEventListener('change', updateReport);
document.getElementById('yearInput').addEventListener('change', updateReport);

function updateReport() {
    const month = document.getElementById('monthSelect').value;
    const year = document.getElementById('yearInput').value;
    const branch = '{{ $user->id_pt }}'; // Sesuaikan dengan nilai branch yang diinginkan
    
    window.location.href = `${reportUrl}?bulan=${month}&tahun=${year}&branch=${branch}`;
}
    // Update export URL dynamically based on selected month and year
    document.getElementById('exportExcel').addEventListener('click', function (e) {
        e.preventDefault();
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearInput').value;

        // Redirect to the export route with query parameters
        window.location.href = `{{ route('report.initial.recognition.effective.export.excel', ['id_pt' => Auth::user()->id_pt]) }}?bulan=${month}&tahun=${year}`;
    });

    document.getElementById('exportPdf').addEventListener('click', function (e) {
        e.preventDefault();
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearInput').value;

        // Redirect to the export route with query parameters
        window.location.href = `{{ route('report.initial.recognition.effective.export.pdf', ['id_pt' => Auth::user()->id_pt]) }}?bulan=${month}&tahun=${year}`;
    });


// Event listener untuk document ready
document.addEventListener('DOMContentLoaded', function() {
    const entityInput = document.getElementById('entityNumber');
    const entityLabel = document.getElementById('entityLabel');
    
    const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
    
    if (!isSuperAdmin) {
        entityInput.value = '{{ $user->id_pt ?? "" }}';
        entityInput.disabled = true;
        
        const entityNumber = entityInput.value;
        if (entityNumber) {
            fetch(`/check-entity/${entityNumber}`)
                .then(response => response.json())
                .then(data => {
                    const entityLabel = document.getElementById('entityLabel');
                    const entityError = document.getElementById('entityError');
                    
                    if (data.success) {
                        // Reset classes terlebih dahulu
                        entityLabel.className = '';
                        
                        // Set content
                        entityLabel.innerHTML = data.entity_name;
                        
                        // Tambahkan style inline
                        entityLabel.style = `
                            display: inline-block;
                            visibility: visible;
                            margin-left: 10px;
                            padding: 4px 8px;
                            background-color: #d4edda;
                            color: #155724;
                            border: 1px solid #c3e6cb;
                            border-radius: 4px;
                            font-size: 14px;
                        `;
                        
                        entityError.style.display = 'none';
                        
                    } else {
                        // Reset classes
                        entityLabel.className = '';
                        
                        entityLabel.innerHTML = 'Data tidak ditemukan';
                        entityLabel.style = `
                            display: inline-block;
                            visibility: visible;
                            margin-left: 10px;
                            padding: 4px 8px;
                            background-color: #f8d7da;
                            color: #721c24;
                            border: 1px solid #f5c6cb;
                            border-radius: 4px;
                            font-size: 14px;
                        `;
                        entityError.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    } else {
        entityInput.addEventListener('blur', entityCheckFunction);
    }
});

// Definisikan fungsi check entity
const entityCheckFunction = function() {
    const entityNumber = this.value;
    const entityLabel = document.getElementById('entityLabel');
    const entityError = document.getElementById('entityError');

    if (entityNumber) {
        fetch(`/check-entity/${entityNumber}`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    entityLabel.textContent = data.entity_name;
                    entityLabel.classList.remove('alert-danger');
                    entityLabel.classList.add('alert', 'alert-success', 'py-1', 'px-2');
                    entityLabel.style.display = 'inline-block';
                    entityError.style.display = 'none';
                } else {
                    entityLabel.textContent = 'Data tidak ditemukan';
                    entityLabel.classList.remove('alert-success');
                    entityLabel.classList.add('alert', 'alert-danger', 'py-1', 'px-2');
                    entityLabel.style.display = 'inline-block';
                    entityError.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        entityLabel.style.display = 'none';
        entityError.style.display = 'none';
    }
};

// Event listener untuk account number
document.getElementById('accountNumber').addEventListener('blur', function() {
    const accountNumber = this.value;
    const accountLabel = document.getElementById('accountLabel');
    const accountError = document.getElementById('accountError');
    const entityNumber = document.getElementById('entityNumber').value;
    const reportType = document.getElementById('reportType').value;
    
    if (accountNumber) {
        // Tentukan URL berdasarkan tipe report
        const accountCheckUrl = reportType.includes('simple') 
            ? `/check-account-corporate/${accountNumber}?entity_number=${entityNumber}` 
            : `/check-account/${accountNumber}?entity_number=${entityNumber}`;
        
        fetch(accountCheckUrl)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    accountLabel.textContent = data.deb_name;
                    accountLabel.classList.remove('alert-danger');
                    accountLabel.classList.add('alert', 'alert-success', 'py-1', 'px-2');
                    accountLabel.style.display = 'inline-block';
                    accountError.style.display = 'none';
                } else {
                    accountLabel.textContent = 'Data tidak ditemukan';
                    accountLabel.classList.remove('alert-success');
                    accountLabel.classList.add('alert', 'alert-danger', 'py-1', 'px-2');
                    accountLabel.style.display = 'inline-block';
                    accountError.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        accountLabel.style.display = 'none';
        accountError.style.display = 'none';
    }
});

function showModalWithAccount(accountNumber, type) {
    const reportTypeSelect = document.getElementById('reportType');
    const outstandingDateInputs = document.getElementById('outstandingDateInputs');
    const accountNumberSection = document.getElementById('accountNumberSection');
    const accountNumberInput = document.getElementById('accountNumber');
    reportTypeSelect.innerHTML = ''; 

    if (type.includes('outstanding')) {
        // Hide account number input and all related elements
        accountNumberSection.style.display = 'none';
        outstandingDateInputs.style.display = 'block';
        accountNumberInput.removeAttribute('required');
        
        document.getElementById('modalMonth').value = document.getElementById('monthSelect').value;
        document.getElementById('modalYear').value = document.getElementById('yearInput').value;
    } else {
        accountNumberSection.style.display = 'block';
        outstandingDateInputs.style.display = 'none';
        accountNumberInput.setAttribute('required', 'required');
        accountNumberInput.value = accountNumber;
    }
    
    const cleanAccountNumber = accountNumber.toString().trim();
    

    setTimeout(() => {
        // Set nilai menggunakan setAttribute
        accountNumberInput.setAttribute('value', cleanAccountNumber);
        // Set juga menggunakan property value
        accountNumberInput.value = cleanAccountNumber;
        
        const event = new Event('change', { bubbles: true });
        accountNumberInput.dispatchEvent(event);
        
        // Trigger blur event
        const blurEvent = new Event('blur', { bubbles: true });
        accountNumberInput.dispatchEvent(blurEvent);
    }, 100);


    let options;
    switch(type) {
        case 'accrual_interest_effective':
        case 'accrual_interest_simple':
            options = `
                <option value="accrual_interest_effective">Effective</option>
                <option value="accrual_interest_simple">Simple Interest</option>
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
            
        case 'initial_recognition_effective':
        case 'initial_recognition_simple':
            options = `
                <option value="initial_recognition_effective">Effective</option>
                <option value="initial_recognition_simple">Simple Interest</option>
            `;
            break;
            
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
    }
    
    reportTypeSelect.innerHTML = options;
    reportTypeSelect.value = type;

    
    if (!{{ $isSuperAdmin ? 'true' : 'false' }}) {
        const entityNumber = document.getElementById('entityNumber').value;
        if (entityNumber) {
            fetch(`/check-entity/${entityNumber}`)
                .then(response => response.json())
                .then(data => {
                    const entityLabel = document.getElementById('entityLabel');
                    if (data.success) {
                        entityLabel.innerHTML = data.entity_name;
                        entityLabel.style = `
                            display: inline-block;
                            visibility: visible;
                            margin-left: 10px;
                            padding: 4px 8px;
                            background-color: #d4edda;
                            color: #155724;
                            border: 1px solid #c3e6cb;
                            border-radius: 4px;
                            font-size: 14px;
                        `;
                    }
                });
        }
    }
    $('#reportModal').modal('show');

    setTimeout(() => {
        const accountNumberInput = document.getElementById('accountNumber');
        const entityNumberInput = document.getElementById('entityNumber');
        const event = new Event('blur');
        
        accountNumberInput.dispatchEvent(event);
        entityNumberInput.dispatchEvent(event);
    }, 100);
}

    // Trigger blur eventss
    const accountNumberInput = document.getElementById('accountNumber');
    const entityNumberInput = document.getElementById('entityNumber');
    const event = new Event('blur');
    accountNumberInput.dispatchEvent(event);
    entityNumberInput.dispatchEvent(event);

</script>