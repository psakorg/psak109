<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-amorinitfee-si.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch]) }}" class="btn btn-danger"><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-amorinitfee-si.exportExcel',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch])}}" class="btn btn-success"><i class="fas fa-file-excel"></i>Export to Excel</a>
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
                                    @php
                                            // Misalkan trxcost adalah string dengan simbol mata uang
                                            $prov = $loan->prov; // Ambil nilai dari database
                                            // Hapus simbol mata uang dan pemisah ribuan
                                            $prov = preg_replace('/[^\d.]/', '', $prov);
                                            // Konversi ke float
                                            $provFloat = (float)$prov* -1;
                                            $org_bal = $loan->org_bal;
                                            $outinitfee = $org_bal+$provFloat;
                                        @endphp
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="-{{ number_format($loan->prov ?? 0, 2)}}" readonly>
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
                                    <label class="col-sm-4 col-form-label d-flex justify-content-end">Outstanding Amount Initial Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format( $outinitfee ?? 0, 2)  }}" readonly>
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
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->term }} Month" readonly>
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
                            @php 
                            $totalAmortizedUpFrontFee = 0;
                            $cumulativeAmortizedUpFrontFee = 0;
                            @endphp
                            @foreach ($reports as $report)
                            @php 
                            $accrfee = $report->accrfee;
                            $accrconv = $report->accrconv;
                            $amortizedUpFrontFee = $accrfee - $accrconv;
                            $cumulativeAmortizedUpFrontFee += $amortizedUpFrontFee;
                            $totalAmortizedUpFrontFee += $amortizedUpFrontFee;
                            $unamortprovFloat = $provFloat;

                                        // Hitung nilai unamortized
                                        if ($loop->first) {
                                            $unamort = $unamortprovFloat;
                                        } else {
                                            $unamort = $unamort + $amortizedUpFrontFee;
                                        }
                            @endphp
                                <tr class="text-right" style="font-weight:normal">
                                    <td class="text-center">{{ $report->bulanke }}</td>
                                    <td class="text-center">{{ date('d/m/Y ', strtotime($report->tglangsuran)) }}</td>
                                    <td>{{ $report->haribunga ?? 0}}</td>
                                    <td>{{ number_format($report->pmtamt ?? 0) }}</td>
                                    <td>{{ number_format($report->penarikan ?? 0) }}</td>
                                    <td>{{ number_format($report->pengembalian ?? 0) }}</td>
                                    <td>{{ number_format($report->bunga ?? 0) }}</td>
                                    <td>{{ number_format($report->bungaeir ?? 0) }}</td>
                                    <td>{{ number_format($amortizedUpFrontFee ?? 0) }}</td>
                                    <td>{{ number_format($report->outsamtfee ?? 0) }}</td>
                                    <td>{{ number_format($cumulativeAmortizedUpFrontFee ?? 0) }}</td>
                                    <td>{{ number_format($unamort ?? 0) }}</td>
                                </tr>
                            @endforeach
                            <!-- Row Total / Average -->
                            <tr class="text-right font-weight-normal">
                                <td class="text-center" colspan="2">TOTAL</td>
                                <td>{{ number_format($reports->sum('haribunga') ?? 0) }}</td>
                                <td>{{ number_format($reports->sum('pmtamt') ?? 0) }}</td>
                                <td>{{ number_format($reports->sum('penarikan') ?? 0) }}</td>
                                <td>{{ number_format($reports->sum('pengembalian') ?? 0) }}</td>
                                <td>{{ number_format($reports->sum('bunga') ?? 0) }}</td>
                                <td>{{ number_format($reports->sum('bungaeir') ?? 0) }}</td>
                                <td>{{ number_format($totalAmortizedUpFrontFee ?? 0) }}</td>
                                <td></td>
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
