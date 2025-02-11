<html>
<head>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT JOURNAL - SECURITIES</h5>
                    </div>

                    
                    <!-- <div class="card-body">
                        <form>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Number</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Name</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">GL Group</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Date Of Report</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div> -->
                </div>
                 <div class="d-flex justify-content-start mb-3 align-items-center">
                     <div class="dropdown me-1">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-import"></i> Bulan/Tahun
                        </button>
                     <!--</div> 

                       
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Accrual Interest <i class="fas fa-chevron-right float-end"></i>
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

                    <div class="d-flex align-items-center ">

                        <button type="button" class="btn btn-primary dropdown-toggle me-2" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-file-import"></i> Tanggal
                        </button>
                        <select class="form-select me-2" style="width: 80px; height: 40px; font-size: 14px" id="daySelect" onchange="updateReport()">
                            <!-- Will be populated by JavaScript -->
                        </select>

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

                    <!-- Tombol Export -->
                    <div class="d-flex gap-2">
                        <form action="{{ route('securities.report-journal-securities.execute-procedure') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahun" value="{{ request()->query('tahun', date('Y')) }}">
                            <input type="hidden" name="bulan" value="{{ request()->query('bulan', date('n')) }}">
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
                        <a href="#" id="exportCsv" class="btn btn-primary">
                            <i class="fas fa-file-csv"></i> Download File CSV
                        </a>
                    </div>
                </div>
                    
                <!-- Report Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" style="font-size: 12px;">
                        <thead class="thead-dark">
                            <tr>
                                <th style="white-space: nowrap;" class="text-center">No.</th>
                                <th style="white-space: nowrap;" class="text-center">Entity Number</th>
                                <th style="white-space: nowrap;" class="text-center">GL Account</th>
                                <th style="white-space: nowrap;" class="text-center">Description</th>
                                <th style="white-space: nowrap;" class="text-center">Debit</th>
                                <th style="white-space: nowrap;" class="text-center">Credit</th>
                                <th style="white-space: nowrap;" class="text-center">Posting Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($master->isEmpty())
                                <tr>
                                    <td colspan="22" class="text-left">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
                            @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                                @endphp
                                
                                @foreach ($master as $index => $loan)
                                    @php
                                    // Hitung total debit dan credit
                                    if ($loan->post == 'D') {
                                        $totalDebit += $loan->amount;
                                    } else if ($loan->post == 'C') {
                                        $totalCredit += $loan->amount;
                                    }
                                    @endphp
                                        <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $loan->no_branch ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ $loan->coa ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ $loan->deskripsi ?? 'Data tidak ditemukan' }}</td>
                                        <!-- <td class="text-center">{{ $loan->post ?? 'Data tidak ditemukan' }}</td> -->
                                        <td class="text-end">{{ $loan->post == 'D' ? number_format($loan->amount, 2) : '' }}</td>
                                        <td class="text-end">{{ $loan->post == 'C' ? number_format($loan->amount, 2) : '' }}</td>
                                        <td class="text-center">{{ date('d/m/Y', strtotime($loan->post_date))  }}</td>
                                    </tr>
                                @endforeach

                                <!-- Row Total / Average -->
                                <tr class="table-secondary font-weight-normal">
                                    <td colspan="4" class="text-center">TOTAL:</td>
                                    <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                                    <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

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
                    <div class="mb-3">
                        <label for="accountNumber" id="accountNumberLabel" class="form-label">Account Number</label>
                        <div class="d-flex align-items-center">
                            <input type="number" class="form-control" id="accountNumber" required>
                            <span id="accountLabel" class="ms-3 text-muted"></span>
                        </div>
                        <small id="accountError" class="text-danger" style="display: none;">Data tidak ditemukan</small>
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

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ session('error') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
//Pagination
    function changePerPage() {
        const perPage = document.getElementById('per_page').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Hapus parameter page saat mengganti per_page
        window.location.href = url;
    }

function showModal(type) {
    const reportTypeSelect = document.getElementById('reportType');
    const accountNumberInput = document.getElementById('accountNumber');
    const accountNumberLabel = document.getElementById('accountNumberLabel');
    reportTypeSelect.innerHTML = ''; 
    
    // Sembunyikan atau tampilkan input "Account Number" berdasarkan tipe laporan
    if (type.includes('outstanding')) {
        accountNumberInput.style.display = 'none';
        accountNumberLabel.style.display = 'none';
        accountNumberInput.removeAttribute('required');
    } else {
        accountNumberInput.style.display = 'block';
        accountNumberLabel.style.display = 'block';
        accountNumberInput.setAttribute('required', 'required');
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

function showModalWithAccount(accountNumber, type) {
    const reportTypeSelect = document.getElementById('reportType');
    reportTypeSelect.innerHTML = ''; 
    
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
    
    // Set nilai account dan entity
    document.getElementById('accountNumber').value = accountNumber;
    
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

    // Trigger blur events
    const accountNumberInput = document.getElementById('accountNumber');
    const entityNumberInput = document.getElementById('entityNumber');
    const event = new Event('blur');
    accountNumberInput.dispatchEvent(event);
    entityNumberInput.dispatchEvent(event);

    // Tampilkan modal
    $('#reportModal').modal('show');
}

function viewReport() {
    const form = document.getElementById('reportForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const reportType = document.getElementById('reportType').value;
    const entityNumber = document.getElementById('entityNumber').value;
    const accountNumber = document.getElementById('accountNumber').value;

    // Jika tipe laporan adalah "Outstanding", langsung anggap data.success = true
    if (reportType.includes('outstanding')) {
        closeModal();
        redirectToReport(reportType, null, entityNumber);
        return;
    }

    // Tentukan URL pengecekan berdasarkan jenis reportt
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

    // Cek ketersediaan data
    fetch(checkUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tutup modal
                closeModal();
                
                // Redirect ke halaman report
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

function redirectToReport(reportType, accountNumber, entityNumber) {
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
            url = `/report-outstanding-effective/view/${entityNumber}`;
            break;
        case 'outstanding_simple':
            url = `/report-outstanding-simple-interest/view/${entityNumber}`;
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
                console.log(response);
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
    
    if (accountNumber) {
        fetch(`/check-account/${accountNumber}?entity_number=${entityNumber}`)
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

const reportUrl = "{{ route('report-journal-securities-daily.index') }}";

document.addEventListener('DOMContentLoaded', function() {
    // Set nilai default untuk bulan dan tahun dari parameter URL atau data yang dikirim dari controller
    const selectedMonth = "{{ $bulan ?? date('n') }}";
    const selectedYear = "{{ $tahun ?? date('Y') }}";
    
    // Set nilai default untuk select bulan dan input tahun
    document.getElementById('monthSelect').value = selectedMonth;
    document.getElementById('yearInput').value = selectedYear;
});

// Event listener untuk perubahan bulan atau tahun
document.getElementById('monthSelect').addEventListener('change', updateReport);
document.getElementById('yearInput').addEventListener('change', updateReport);
//document.getElementById('jenisSelect').addEventListener('change', updateReport);

function updateReport() {
        const month = document.getElementById('monthSelect').value.padStart(2, '0');
        const day = document.getElementById('daySelect').value.padStart(2, '0');
        const year = document.getElementById('yearInput').value;
        const branch = '{{ $user->id_pt }}';
        
        window.location.href = `${reportUrl}?bulan=${month}&hari=${day}&tahun=${year}&branch=${branch}`;
    }

    document.getElementById('exportPdf').addEventListener('click', function (e) {
        e.preventDefault();
        const month = document.getElementById('monthSelect').value.padStart(2, '0');
        const year = document.getElementById('yearInput').value;
        const day = document.getElementById('daySelect').value.padStart(2, '0');
        // Redirect to the export route with query parameters
        window.location.href = `{{ route('report-journal-securities-daily.exportPdf', ['id_pt' => Auth::user()->id_pt]) }}?bulan=${month}&tahun=${year}&hari=${day}`;
    });

    document.getElementById('exportExcel').addEventListener('click', function (e) {
        e.preventDefault();
        const month = document.getElementById('monthSelect').value.padStart(2, '0');
        const year = document.getElementById('yearInput').value;
        const day = document.getElementById('daySelect').value.padStart(2, '0');
        // Redirect to the export route with query parameters
        window.location.href = `{{ route('report-journal-securities-daily.exportExcel', ['id_pt' => Auth::user()->id_pt]) }}?bulan=${month}&tahun=${year}&hari=${day}`;
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Check if there's an error message in the session
        @if(Session::has('error'))
            Swal.fire({
                title: 'Error',
                text: "{{ Session::get('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    });

    document.addEventListener('DOMContentLoaded', function() {
        const selectedMonth = "{{ $bulan }}";
        const selectedYear = "{{ $tahun }}";
        const selectedDay = "{{ $hari ?? '1' }}";
        
        document.getElementById('monthSelect').value = parseInt(selectedMonth);
        document.getElementById('yearInput').value = selectedYear;
        
        // Initialize days first
        updateDays();
        
        // Set selected day after initializing days
        const daySelect = document.getElementById('daySelect');
        if (selectedDay && selectedDay <= new Date(selectedYear, selectedMonth, 0).getDate()) {
            daySelect.value = parseInt(selectedDay);
        }
    });

    function updateDays() {
        const month = parseInt(document.getElementById('monthSelect').value);
        const year = parseInt(document.getElementById('yearInput').value);
        const daySelect = document.getElementById('daySelect');
        const currentSelectedDay = daySelect.value; // Simpan nilai hari yang dipilih
        
        // Clear existing options
        daySelect.innerHTML = '';
        
        // Get number of days in the selected month
        const daysInMonth = new Date(year, month, 0).getDate();
        
        // Populate days
        for(let i = 1; i <= daysInMonth; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            daySelect.appendChild(option);
        }
        
        // Kembalikan nilai hari yang dipilih jika masih valid
        if (currentSelectedDay && currentSelectedDay <= daysInMonth) {
            daySelect.value = currentSelectedDay;
        } else {
            daySelect.value = 1;
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

<style>
    /* ... style yang sudah ada ... */
    
    .table-secondary {
        background-color: #f2f2f2 !important;
    }

    .font-weight-bold {
        font-weight: normal;
    }

    .text-end {
        text-align: right;
    }

    .text-right {
        text-align: right;
        padding-right: 20px;
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
        color: #212529;
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

    /* Style untuk dropdown menu */
    .dropdown-menu {
        margin: 0;
        padding: 0.5rem 0;
        position: absolute;
        z-index: 1000;
        min-width: 250px;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 4px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #212529 !important;
        font-size: 15px !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    /* Style untuk submenu */
    .dropdown-submenu {
        display: none;
        position: absolute;
        left: 100%;
        top: 0;
        min-width: 200px;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 4px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }

    /* Perbaikan untuk hover behavior */
    .dropdown-menu li {
        position: relative;
    }

    .dropdown-menu li:hover > .dropdown-submenu {
        display: block;
    }

    .dropdown-menu li:hover > a {
        background-color: #f8f9fa;
    }

    /* Style untuk icon */
    .dropdown-item i.float-end {
        margin-left: 8px;
        font-size: 14px;
    }

    /* Memastikan container tidak overflow */
    .container {
        overflow: visible !important;
    }

    .section {
        overflow: visible !important;
    }

    .dropdown {
        position: relative;
    }

    /* Tambahan untuk memastikan submenu tetap terlihat */
    .dropdown-menu .dropdown-item {
        position: relative;
        padding-right: 30px; /* Ruang untuk icon chevron */
    }

    /* Memastikan submenu muncul di level yang sama */
    .dropdown-menu > li > .dropdown-submenu {
        top: 0;
    }

    /* Style untuk form input dan select */
    .form-control, .form-select {
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 4px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    /* Style untuk label */
    .form-label {
        font-weight: normal;
        color: #495057;
        font-size: 14px;
        margin-bottom: 0.5rem;
    }

    /* Style untuk entity dan account label */
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

    /* Style untuk modal */
    .modal-content {
        border-radius: 6px;
    }

    .modal-header {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-body {
        padding: 1rem;
    }

    .modal-footer {
        padding: 1rem;
        border-top: 1px solid #dee2e6;
    }

    /* Style untuk form groups */
    .mb-3 {
        margin-bottom: 1rem !important;
    }

    /* Style untuk input groups */
    .d-flex.align-items-center {
        gap: 0.5rem;
    }

    /* Style untuk error messages */
    .text-danger {
        font-size: 12px;
        margin-top: 0.25rem;
    }

    /* Style untuk buttons dalam modal */
    .modal-footer .btn {
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
    }

    .clickable-account {
        cursor: pointer;
        color: #007bff;
        text-decoration: none;
    }

    .clickable-account {
        text-decoration: underline;
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
        color: #212529;
    }
</style>
</body>
</html>
