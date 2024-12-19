<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-acc-si.exportPdf',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-danger">Export to PDF</a>
                    <a href="{{ route('report-acc-si.exportExcel',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success">Export to Excel</a>
                </div>
                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT AMORTISED INITIAL FEE - SIMPLE INTEREST</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->no_acc }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Up Front Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->prov, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Debitor Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->deb_name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end white-space: nowrap;">Outstanding Amount Initial Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format(0, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->org_bal, 2) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">EIR Fee Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->eircalc_fee * 100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->org_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->term }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->interest * 100, 2) }}%" readonly>
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
                        <thead class="thead-dark" style="text-align: center">
                            <tr>
                                <th>Month</th>
                                <th>Transaction Date</th>
                                <th>Days Interest</th>
                                <th>Payment Amount</th>
                                <th>Withdrawal</th>
                                <th>Reimbursement</th>
                                <th>Effective Interest Base On Effective Yield</th>
                                <th>Accrued Interest</th>
                                <th>Amortised Up Front Fee</th>
                                <th>Outstanding Amount Initial Up Front Fee</th>
                                <th>Cummulative Amortized Up Front Fee</th>
                                <th>Unamortized Up Front Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr class="text-right" style="font-weight:normal">
                                    <td class="text-center">{{ $report->bulanke }}</td>
                                    <td class="text-center">{{ date('d-m-Y ', strtotime($report->tglangsuran)) }}</td>
                                    <td class="text-center">{{ $report->haribunga }}</td>
                                    <td>{{ number_format($report->pmtamt, 2) }}</td>
                                    <td>{{ number_format($report->penarikan, 2) }}</td>
                                    <td>{{ number_format($report->pengembalian, 2) }}</td>
                                    <td>{{ number_format($report->bunga, 2) }}</td>
                                    <td>{{ number_format($report->balance, 2) }}</td>
                                    <td>{{ number_format($report->timegap, 2) }}</td>
                                    <td>{{ number_format($report->outsamtconv, 2) }}</td>
                                    <td>{{ number_format(0, 2) }}</td>
                                    <td>{{ number_format(0, 2) }}</td>
                                </tr>
                            @endforeach
                            <!-- Row Total -->
                            <tr class="text-right" style="font-weight:normal">
                                <td colspan="12" class="text-center">Total</td>
                            </tr>
                            <!-- Row Average -->
                            <tr class="text-right" style="font-weight:normal">
                                <td colspan="4" class="text-center">Average</td>
                                <td>{{ number_format($reports->avg('penarikan'), 2) }}</td>
                                <td>{{ number_format($reports->avg('pengembalian'), 2) }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ number_format($reports->avg('timegap'), 2) }}</td>
                                <td></td>
                                <td>{{ number_format(0, 2) }}</td>
                                <td>{{ number_format(0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
