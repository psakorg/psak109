<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-expectcf-si.exportPdf',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch])}}" class="btn btn-danger"><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-expectcf-si.exportExcel',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch])}}" class="btn btn-success"><i class="fas fa-file-excel"></i>Export to Excel</a>
                </div>
                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT EXPECTED CASH FLOW - SIMPLE INTEREST</h5>
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
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Debitor Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->deb_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->org_bal, 2) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->org_date)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->term }} Month" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Maturity Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->rate * 100, 5) }}%" readonly>
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
                        <thead class="thead-dark text-center">
                            <tr>
                                <th style="width: 50px">Month</th>
                                <th style="width: 10%">Transaction Date</th>
                                <th>Days Interest</th>
                                <th>Payment Amount</th>
                                <th>Withdrawal</th>
                                <th>Reimbursement</th>
                                <th>Interest Payment</th>
                                <th>Balance Contractual</th>
                                <th>Unearned Interest Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                    $cumulativebunga = 0;
                                    $totalPaymentAmount = 0;
                                    $totalInterestPayment = 0;
                                    $totalPrincipalPayment = 0;
                                    $totalBalanceContractual = 0;
                                    $sumBunga = 0;
                                    $totalInterestIncome = 0;
                            @endphp
                            @foreach ($reports as $report)
                                @php
                                    $totalInterestIncome += $report->bunga;
                                @endphp
                            @endforeach
                            @foreach ($reports as $report)
                                @php
                                        $bunga = $report->bunga;
                                        $interestPayment = $report->bunga;
                                        $sumBunga += $bunga;
                                        $totalPaymentAmount += $report->pmtamt;
                                        $totalInterestPayment += $report->bunga;
                                        $totalBalanceContractual += $report->balance;
                                        if ($loop->first) {
                                            $interestIncome = $totalInterestIncome;
                                        } else {
                                            $totalInterestIncome -= $bunga;
                                            $interestIncome = $totalInterestIncome;
                                        }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $report->bulanke }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($report->tglangsuran)) }}</td>
                                    <td class="text-right">{{ number_format($report->haribunga ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->pmtamt ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->penarikan ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->pengembalian ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->bunga ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($report->balance ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($interestIncome ?? 0) }}</td>
                                </tr>
                            @endforeach
                            <!-- Row Total / Average -->
                            <tr class="font-weight-normal">
                                <td class="text-center" colspan="2">TOTAL</td>
                                <td class="text-right">{{ number_format(array_sum(array_column($reports, 'haribunga'))?? 0) }}</td>
                                <td class="text-right">{{ number_format(array_sum(array_column($reports, 'pmtamt'))?? 0) }}</td>
                                <td class="text-right">{{ number_format(array_sum(array_column($reports, 'penarikan'))?? 0) }}</td>
                                <td class="text-right">{{ number_format(array_sum(array_column($reports, 'pengembalian'))?? 0) }}</td>
                                <td class="text-right">{{ number_format(array_sum(array_column($reports, 'bunga'))?? 0) }}</td>
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
