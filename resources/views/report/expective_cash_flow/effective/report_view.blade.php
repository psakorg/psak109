<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-expectcfeff-eff.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-danger"><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-expectcfeff-eff.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success"><i class="fas fa-file-excel"></i>Export to Excel</a>
                </div>
                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT EXPECTED CASH FLOW - EFFECTIVE</h5>
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
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $master->term ?? 0 }} Month" readonly>
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
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($master->rate  * 100, 5) }}%" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Payment Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($master->pmtamt, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" style="font-size: 12px; text-align: right; font-weight:normal; padding: 5px;">
                        <thead class="thead-dark text-center">
                            <tr style="height: 40px;">
                                <th style="width: 50px">Month</th>
                                <th style="width: 10%">Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Interest Payment</th>
                                <th>Principal Payment</th>
                                <th>Balance Contractual</th>
                                <th>Unearned Interest Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($reports->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
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
                                    <tr style="font-weight:normal;">
                                        <td class="text-center">{{ $report->bulanke ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ isset($report->tglangsuran) ? date('d/m/Y ', strtotime($report->tglangsuran)) : 'Belum di-generate' }}</td>
                                        <td>{{ number_format($report->pmtamt ?? 0) }}</td>
                                        <td>{{ number_format($report->bunga ?? 0) }}</td>
                                        <td>{{ number_format($report->pokok ?? 0) }}</td>
                                        <td>{{ number_format($report->balance ?? 0) }}</td>
                                        <td>{{ number_format($interestIncome ?? 0) }}</td>
                                    </tr>
                                @endforeach
                                <!-- Row Total -->
                                <tr style="font-weight:normal; height: 40px;" class="text-right">
                                    <td colspan="2" class="text-center">TOTAL</td>
                                    <td>{{ number_format($totalPaymentAmount ?? 0) }}</td>
                                    <td>{{ number_format($totalInterestPayment ?? 0) }}</td>
                                    <td>{{ number_format($totalPrincipalPayment ?? 0) }}</td>
                                    <td></td>
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
