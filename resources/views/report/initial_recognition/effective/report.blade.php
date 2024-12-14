<head>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5" style="padding-right: 50px;">
            <section class="section">
                <div class="section-header">
                    <h4>REPORT INITIAL RECOGNITION NEW LOAN BY ENTITY - CONTRACTUAL EFFECTIVE</h4>
                </div>
                
                <div class="d-flex justify-content-start mb-0">
                    <div class="dropdown">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-import"></i> Report
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Accrual Interest <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Amortised Cost <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Amortised Initial Cost <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Amortised Initial Fee <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Expected Cash Flow <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Initial Recognition <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Outstanding <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                                    Journal <i class="fas fa-chevron-right float-end"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a class="dropdown-item" href="#" onclick="showModal('effective')">Effective</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showModal('simple_interest')">Simple Interest</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="table-responsive">
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
                                    'outsamtfee' => 0
                                ]; @endphp
                                
                                @foreach($loans as $index => $loan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $loan->no_branch }}</td>
                                        <td>{{ $loan->no_acc }}</td>
                                        <td>{{ $loan->deb_name }}</td>
                                        <td>{{ $loan->coa }}</td>
                                        <td>{{ $loan->LN_TYPE }}</td>
                                        <td>{{ $loan->glgroup }}</td>
                                        <td>{{ $loan->OrgdtConv }}</td>
                                        <td>{{ $loan->term }}</td>
                                        <td>{{ number_format($loan->RATE, 4) }}%</td>
                                        <td>{{ $loan->MtrdtConv }}</td>
                                        <td class="text-right">{{ number_format($loan->pmtamt, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->org_bal, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->oldbal, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->baleir, 0) }}</td>
                                        <td>{{ number_format($loan->eirex, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc_conv, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc_cost, 14) }}%</td>
                                        <td>{{ number_format($loan->eircalc_fee, 14) }}%</td>
                                        <td class="text-right">{{ number_format($loan->outsamtconv, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->outsamtcost, 0) }}</td>
                                        <td class="text-right">{{ number_format($loan->outsamtfee, 0) }}</td>
                                    </tr>
                                    @php
                                        $total['org_bal'] += $loan->org_bal;
                                        $total['oldbal'] += $loan->oldbal;
                                        $total['baleir'] += $loan->baleir;
                                        $total['rate'] += $loan->RATE;
                                        $total['eirex'] += $loan->eirex;
                                        $total['eircalc'] += $loan->eircalc;
                                        $total['eircalc_conv'] += $loan->eircalc_conv;
                                        $total['eircalc_cost'] += $loan->eircalc_cost;
                                        $total['eircalc_fee'] += $loan->eircalc_fee;
                                        $total['outsamtconv'] += $loan->outsamtconv;
                                        $total['outsamtcost'] += $loan->outsamtcost;
                                        $total['outsamtfee'] += $loan->outsamtfee;
                                    @endphp
                                @endforeach
                                
                                <!-- Row Total/Average -->
                                <tr class="font-weight-bold">
                                    <td colspan="8">Average</td>
                                    <td></td>
                                    <td>{{ count($loans) > 0 ? number_format($total['rate'] / count($loans), 4) : 0 }}%</td>
                                    <td colspan="2"></td>
                                    <td class="text-right">{{ number_format($total['org_bal'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['oldbal'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['baleir'], 0) }}</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eirex'] / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc'] / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc_conv'] / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc_cost'] / count($loans), 14) : 0 }}%</td>
                                    <td>{{ count($loans) > 0 ? number_format($total['eircalc_fee'] / count($loans), 14) : 0 }}%</td>
                                    <td class="text-right">{{ number_format($total['outsamtconv'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['outsamtcost'], 0) }}</td>
                                    <td class="text-right">{{ number_format($total['outsamtfee'], 0) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="23" class="text-center py-4">
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
        font-size: 11px;
    }
    
    .custom-table th, .custom-table td {
        padding: 8px 12px;
        white-space: nowrap;
    }
    
    .text-right {
        text-align: right;
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
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType" required>
                            <option value="">Pilih Report Type</option>
                            <option value="effective">Effective</option>
                            <option value="simple_interest">Simple Interest</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="entityNumber" class="form-label">Entity Number</label>
                        <input type="text" class="form-control" id="entityNumber" required>
                    </div>
                    <div class="mb-3">
                        <label for="accountNumber" class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="accountNumber" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportDate" class="form-label">Date of Report</label>
                        <input type="date" class="form-control" id="reportDate" required>
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
    // Set value untuk report type
    document.getElementById('reportType').value = type;
    
    // Tampilkan modal menggunakan jQuery
    $('#reportModal').modal('show');
}

function closeModal() {
    $('#reportModal').modal('hide');
    
    // Bersihkan modal backdrop dan class
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('overflow', '');
    $('body').css('padding-right', '');
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
    const reportDate = document.getElementById('reportDate').value;

    // Tutup modal sebelum redirect
    closeModal();

    // Buat URL dengan parameter
    const url = `/report/initial-recognition/${reportType}?` + new URLSearchParams({
        entity_number: entityNumber,
        account_number: accountNumber,
        report_date: reportDate
    });

    // Redirect ke halaman report
    window.location.href = url;
}

// Tambahkan event listener saat dokumen siap
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
});
</script>