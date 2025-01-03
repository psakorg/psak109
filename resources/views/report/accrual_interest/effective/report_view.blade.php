<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 10px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-2">
                    <a href="{{ route('report-acc-eff.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])  }}" class="btn btn-danger "><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-acc-eff.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-success "><i class="fas fa-file-excel"></i>Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-2" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT ACCRUED INTEREST - EFFECTIVE</h5>
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
                                    <label class="col-sm-3 col-form-label text-right">EIR Conversion Calculated</label>
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



                <!-- Report Tablee -->
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
                                $totalPaymentAmount = 0;
                                $totalAccruedInterest = 0;
                                $totalInterestPayment = 0;
                                $totalOutstandingAmount = 0;
                                $reportCount = count($reports);
                            @endphp
                            @foreach ($reports as $report)
                            @php
                                $accruedInterest = $report->accrconv ?? 0;
                                $interestPayment = $report->bunga ?? 0;
                                $timegap = $accruedInterest - $interestPayment;
                                $cumulativeTimeGap += floatval($timegap);
                                $totalTimeGap += $timegap;
                                $totalPaymentAmount += $report->pmtamt;
                                $totalAccruedInterest += $report->accrconv;
                                $totalInterestPayment += $report->bunga;
                                $totalOutstandingAmount += $report->outsamtconv;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $report->bulanke }}</td>
                                <td class="text-center" >{{ date('d/m/Y', strtotime($report->tglangsuran)) }}</td>
                                <td>{{ number_format($report->pmtamt ?? 0) }}</td>
                                <td>{{ number_format($report->accrconv ?? 0) }}</td>
                                <td>{{ number_format($report->bunga ?? 0) }}</td>
                                <td>{{ number_format($timegap ?? 0) }}</td>
                                <td>{{ number_format($report->outsamtconv ?? 0) }}</td>
                                <td>{{ number_format($cumulativeTimeGap ?? 0) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                <td>{{ number_format($totalPaymentAmount ?? 0) }}</td>
                                <td>{{ number_format($totalAccruedInterest ?? 0) }}</td>
                                <td>{{ number_format($totalInterestPayment ?? 0) }}</td>
                                <td>{{ number_format($totalTimeGap ?? 0) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>