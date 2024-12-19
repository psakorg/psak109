<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-acc-eff.exportPdf',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-danger ">Export to PDF</a>
                    <a href="{{ route('report-acc-eff.exportExcel',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success ">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT AMORTISED COST - SIMPLE INTEREST</h5>
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
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Outstanding Interest</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->bilint, 2) }}" readonly>
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
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Up Front Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->prov, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->nbal, 2) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Transaction Cost</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->trxcost, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->org_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->interest*100, 2) }}%" readonly>
                                    </div>
                                    {{-- <label class="col-sm-4 col-form-label d-flex justify-content-end">Carrying Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('Y-m-d', strtotime($loan->mtr_date)) }}" readonly>
                                    </div> --}}
                                </div>
                            </div>
                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $loan->term }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">EIR Exposure</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->eirex*100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 6 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">EIR Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->eircalc*100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 7 -->
                            {{-- <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d-m-Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                            </div> --}}
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px; text-align:right; font-weight: bold;">
                        <thead class="thead-dark" style="text-align: center">
                            <tr>
                                <th>Month</th>
                                <th>Transaction Date</th>
                                <th>Days Interest</th>
                                <th>Payment Amount</th>
                                <th>Withdrawal</th>
                                <th>Reimbursement</th>
                                <th>Interest Recognition</th>
                                <th>Interest Payment</th>
                                <th>Amortised</th>
                                <th>Carrying Amount</th>
                                <th>Cummulative Amortized</th>
                                <th>Unamortized</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($reports->isEmpty())
                                <tr>
                                    <td colspan="12" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
                            @php
                                $totalAmortised = 0;
                                $totalDaysInterest = 0;
                                $totalWithdrawal = 0;
                                $totalReimbursement = 0;
                                $totalInterestPayment = 0;
                                $reportCount = count($reports);
                            @endphp
                                @foreach ($reports as $report)
                                @php
                                    $totalAmortised += $report->amortized ?? 0;
                                    $totalDaysInterest += $report->days_interest ?? 0;
                                    $totalWithdrawal += $report->withdrawal ?? 0;
                                    $totalReimbursement += $report->reimbursement ?? 0;
                                    $totalInterestPayment += $report->bunga ?? 0;
                                @endphp
                                    <tr style="font-weight:normal">
                                        <td class="text-center">{{ $report->bulanke ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate' }}</td>
                                        <td>{{ number_format($report->days_interest ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->pmtamt ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->withdrawal ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->reimbursement ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->bunga ?? 0, 2) }}</td>
                                        <td>{{ number_format(0, 2) }}</td>
                                        <td>{{ number_format($report->amortized ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->baleir ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->outsamtconv ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->amortized + $report->outsamtconv ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                                <!-- Row Total -->
                                <tr style="font-weight:bold;">
                                    <td class="text-center" colspan="8">TOTAL</td>
                                    <td>{{ number_format($totalAmortised, 2) }}</td>
                                    <td colspan="3"></td>
                                </tr>
                                <!-- Row Average -->
                                <tr style="font-weight:bold;">
                                    <td class="text-center" colspan="2">AVERAGE</td>
                                    <td>{{ number_format($totalDaysInterest / $reportCount, 2) }}</td>
                                    <td colspan="2"></td>
                                    <td>{{ number_format($totalWithdrawal / $reportCount, 2) }}</td>
                                    <td>{{ number_format($totalReimbursement / $reportCount, 2) }}</td>
                                    <td>{{ number_format(0, 2) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
