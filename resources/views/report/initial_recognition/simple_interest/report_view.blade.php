<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 10px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-2">
                    <a href="{{ route('report-acc-eff.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])  }}" class="btn btn-danger ">Export to PDF</a>
                    <a href="{{ route('report-acc-eff.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-success ">Export to Excel</a>
                </div>

                <!-- Loan Details Formm -->
                <div class="card mb-2" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT ACCRUAL INTEREST - EFFECTIVE</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->no_acc }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label text-right">Outstanding Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->org_bal,2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Debitor Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->deb_name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label text-right" style="white-space: nowrap;">EIR Conversion Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->eircalc_conv * 100, 14) . '%' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->org_bal, 2) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label text-right">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ date('d/m/Y', strtotime($master->org_date_dt)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $master->term ?? ''}} Month" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label text-right">Maturity Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ date('d/m/Y', strtotime($master->mtr_date_dt)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($master->rate * 100, 5) . '%'}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label text-right">Payment Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($master->pmtamt,2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>



                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px; text-align: right;">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th>Month</th>
                                <th>Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Accrued Interest</th>
                                <th>Interest Payment</th>
                                <th>Time Gap</th>
                                <th>Outstanding Amount</th>
                                <th>Cumulative Time Gap</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cumulativeTimeGap = 0;
                                $totalTimeGap = 0;
                            @endphp
                            @foreach ($reports as $report)
                            @php
                                $cumulativeTimeGap += floatval($report->timegap);
                                $totalTimeGap += ($report->timegap);
                            @endphp
                            <tr>
                                <td>{{ $report->bulanke }}</td>
                                <td class="text-center" >{{ date('d/m/Y', strtotime($report->tglangsuran)) }}</td>
                                <td>{{ number_format($report->pmtamt, 2) }}</td>
                                <td>{{ number_format($report->accrconv, 5) }}</td>
                                <td>{{ number_format($report->accrconv, 5) }}</td>
                                <td>{{ number_format($report->timegap, 2) }}</td>
                                <td>{{ number_format($report->outsamtconv, 2) }}</td>
                                <td>{{ number_format($totalTimeGap, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>