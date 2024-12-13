<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-outstanding-eff.exportPdf', ['id_pt' => $loanfirst->id_pt])}}" class="btn btn-danger">Export to PDF</a>
                    <a href="{{ route('report-outstanding-eff.exportExcel', ['id_pt' => $loanfirst->id_pt])}}" class="btn btn-success">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT OUTSTANDING - EFFECTIVE</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Number</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loanfirst->no_branch }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Name</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">GL Group</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Date Of Report</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" style="font-size: 12px;">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Branch Number</th>
                                <th>Account Number</th>
                                <th>Debitor Name</th>
                                <th>GL Account</th>
                                <th>Loan Type</th>
                                <th>GL Group</th>
                                <th>Original Date</th>
                                <th>Term (Months)</th>
                                <th>Maturity Date</th>
                                <th>Interest Rate</th>
                                <th>Payment Amount</th>
                                <th>EIR Amortised Cost Exposure</th>
                                <th>EIR Amortised Cost Calculated</th>
                                <th>Current Balance</th>
                                <th>Carrying Amount</th>
                                <th>Oustanding Receivable</th>
                                <th>Outstanding Interest</th>
                                <th>Cummulative Time Gap</th>
                                <th>Unamortized Transaction Cost</th>
                                <th>Unamortized UpFront Fee</th>
                                <th>Unamortized Interest Deferred</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($loan->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else

                            @foreach ($loanjoin as $index => $loan)
                            @php
                                // Menghitung nilai upfrontFee
                                $upfrontFee = round(-($loan->org_bal * 0.01), 0);

                                $CarryingAmount=$loan->org_bal+$upfrontFee;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $loan->no_branch ?? 'Data tidak ditemukan' }}</td>
                                <td>{{ $loan->no_acc ?? 'Data tidak ditemukan' }}</td>
                                <td>{{ $loan->deb_name ?? 'Data tidak ditemukan' }}</td>
                                <td>{{ $loan->ln_type ?? 'Data tidak ditemukan' }}</td>
                                <td>{{ $loan->ln_type ?? 'Data tidak ditemukan' }}</td>
                                <td>{{ $loan->GROUP ?? 'Data tidak ditemukan' }}</td>
                                <td>{{ isset($loan->org_date_dt) ? date('d/m/Y', strtotime($loan->org_date_dt)) : 'Belum di-generate' }}</td>
                                <td>{{ $loan->master_term ?? 0 }}</td>
                                <td>{{ isset($loan->mtr_date) ? date('d/m/Y', strtotime($loan->mtr_date)) : 'Belum di-generate' }}</td>
                                <td>{{ number_format($loan->rate ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->pmtamt ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->eirex*100 ?? 0, 14) }}%</td>
                                <td>{{ number_format($loan->eircalc*100 ?? 0, 14) }}%</td>
                                <td>{{ number_format($loan->nbal ?? 0,2) }}</td>
                                <td>{{ number_format($CarryingAmount ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->interest ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->adjsmnt ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->interest ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->interest ?? 0, 2) }}</td>
                                <td>{{ number_format($upfrontFee ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->interest ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
