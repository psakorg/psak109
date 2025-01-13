<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-acc-si.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch])   }}" class="btn btn-danger "><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-acc-si.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch])   }}" class="btn btn-success "><i class="fas fa-file-excel"></i>Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT ACCRUED INTEREST - SIMPLE INTEREST</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $loan->no_acc }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Outstanding Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->nbal, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Debitor Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $loan->deb_name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">EIR Conversion Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->eircalc_conv*100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->org_bal, 2) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->org_date)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 43 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $loan->term }} Month" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->rate*100, 5) }}%" readonly>
                                    </div>
                                </div>
                             </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    {{-- <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px;"> --}}
                    <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px; text-align: right;font-weight: normal;">
                        <thead class="thead-dark" style="text-align: center">
                            <tr>
                                <th>Month</th>
                                <th>Transaction Date</th>
                                <th>Days Interest</th>
                                <th>Payment Amount</th>
                                <th>Withdrawal</th>
                                <th>Reimbursement</th>
                                <th>Accrued Interest</th>
                                <th>Interest Payment</th>
                                <th>Time Gap</th>
                                <th>Outstanding Amount</th>
                                <th>Cummulative Time Gap</th>
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
                                $totalWithdrawal = 0;
                                $totalReimbursement = 0;
                                $reportCount = count($reports);
                            @endphp
                            @foreach ($reports as $report)
                                @php
                                    $accruedInterest = $report->accrconv ?? 0;
                                    $interestPayment = $report->bunga ?? 0;
                                    $timegap = $report->timegap;
                                    $cumulativeTimeGap += floatval($timegap);
                                    $totalTimeGap += $timegap;
                                    $totalPaymentAmount += $report->pmtamt;
                                    $totalAccruedInterest += $report->accrconv;
                                    $totalInterestPayment += $report->bunga;
                                    $totalOutstandingAmount += $report->outsamtconv;
                                    $totalWithdrawal += $report->penarikan;
                                    $totalReimbursement += $report->pengembalian;
                                @endphp
                                <tr style="font-weight:normal">
                                    <td class="text-center" >{{ $report->bulanke }}</td>
                                    <td class="text-center" >{{ date('d/m/Y', strtotime($report->tglangsuran)) }}</td>
                                    <td class="text-right" >{{  number_format($report->haribunga) ?? 0 }}</td>
                                    <td class="text-right"  >{{ number_format($report->pmtamt ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->penarikan ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->pengembalian ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->accrconv ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->bunga ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($timegap ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->outsamtconv ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($cumulativeTimeGap ?? 0) }}</td>
                                </tr>
                            @endforeach
                            <tr style="font-weight:normal">
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="text-right">{{ number_format($reports->sum('haribunga') ?? 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('pmtamt') ?? 0) }}</td>
                                <td class="text-right">{{ number_format($totalWithdrawal ?? 0) }}</td>
                                <td class="text-right">{{ number_format($totalReimbursement ?? 0) }}</td>
                                <td class="text-right">{{ number_format($totalAccruedInterest ?? 0) }}</td>
                                <td class="text-right">{{ number_format($totalInterestPayment ?? 0) }}</td>
                                <td class="text-right">{{ number_format($totalTimeGap ?? 0) }}</td>
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
