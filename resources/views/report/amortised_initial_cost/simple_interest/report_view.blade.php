<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-acc-si.exportPdf',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-danger ">Export to PDF</a>
                    <a href="{{ route('report-acc-si.exportExcel',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success ">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT AMORTISED INITIAL COST - SIMPLE INTEREST</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->no_acc }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end" style="font-size: 12px;">Transaction Cost</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->trxcost*100, 2) }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Debitor Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->deb_name }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end" style="font-size: 12px; white-space: nowrap;">Outstanding Amount Initial Cost</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->org_bal, 2) }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Original Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->org_bal, 2) }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end" style="font-size: 12px;">EIR Cost Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->eircalc_cost*100, 14) }}%" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ date('d-m-Y', strtotime($loan->org_date)) }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end" style="font-size: 12px;">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->term }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                            </div>
                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ date('d-m-Y', strtotime($loan->mtr_date)) }}" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end" style="font-size: 12px;">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->interest * 100, 2) }}%" readonly style="font-size: 12px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px;">
                        <thead class="thead-dark" style="text-align: center">
                            <tr>
                                <th>Month</th>
                                <th>Transaction Date</th>
                                <th>Days Interest</th>
                                <th>Payment Amount</th>
                                <th>Withdrawal</th>
                                <th>Reimbursement</th>
                                <th>Effective Interest Based on Effective Yield (UF/TC)</th>
                                <th>Effective Interest Based on Effective Yield (UF)</th>
                                <th>Amortised Transaction Cost</th>
                                <th>Outstanding Amount Initial Transaction Cost</th>
                                <th>Cummulative Amortized Transaction Cost</th>
                                <th>Unamortized Transaction Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr class="text-right" style="font-weight:normal">
                                    <td class="text-center">{{ $report->bulanke }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($report->tglangsuran)) }}</td>
                                    <td class="text-center">{{ $report->haribunga }}</td>
                                    <td>{{ number_format($report->pmtamt, 2) }}</td>
                                    <td>{{ number_format($report->penarikan, 2) }}</td>
                                    <td>{{ number_format($report->pengembalian, 2) }}</td>
                                    <td>{{ number_format(0, 2) }}</td>
                                    <td>{{ number_format(0, 2) }}</td>
                                    <td>{{ number_format($report->amortisecost, 2) }}</td>
                                    <td>{{ number_format($report->outsamtconv, 2) }}</td>
                                    <td>{{ number_format(0, 2) }}</td>
                                    <td>{{ number_format(0, 2) }}</td>
                                </tr>
                            @endforeach
                            <!-- Row Total / Average -->
                            <tr class="text-right font-weight-bold">
                                <td class="text-center" colspan="3">TOTAL</td>
                                <td>{{ number_format($reports->sum('pmtamt'), 2) }}</td>
                                <td>{{ number_format($reports->sum('penarikan'), 2) }}</td>
                                <td>{{ number_format($reports->sum('pengembalian'), 2) }}</td>
                                <td>{{ number_format($reports->sum('effective_interest_uf_tc'), 2) }}</td>
                                <td>{{ number_format($reports->sum('effective_interest_uf'), 2) }}</td>
                                <td>{{ number_format($reports->sum('amortisecost'), 2) }}</td>
                                <td>{{ number_format($reports->sum('outsamtconv'), 2) }}</td>
                                <td>{{ number_format($reports->sum('cumm_amortized_cost'), 2) }}</td>
                                <td>{{ number_format($reports->sum('unamortized_cost'), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
