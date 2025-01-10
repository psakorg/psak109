<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-amorinitfee-eff.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-danger"><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-amorinitfee-eff.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success"><i class="fas fa-file-excel"></i>Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title "style="font-size: 16px;">REPORT AMORTISED INITIAL FEE - EFFECTIVE</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->no_acc }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">UpFront Fee</label>
                                    <div class="col-sm-8">
                                        @php
                                            // Misalkan trxcost adalah string dengan simbol mata uang
                                            $prov = $master->prov; // Ambil nilai dari database
                                            // Hapus simbol mata uang dan pemisah ribuan
                                            $prov = preg_replace('/[^\d.]/', '', $prov);
                                            // Konversi ke float
                                            $provFloat = (float)$prov* -1;
                                            $org_bal = $loan->org_bal;
                                            $outinitfee = $org_bal+$provFloat;
                                        @endphp
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="-{{ number_format($master->prov ?? 0, 2)}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Debitor Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ ($loan->deb_name) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Outstanding Initial Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format( $outinitfee ?? 0, 2)  }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Original Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->org_bal, 2) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">EIR Fee Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->eircalc_fee  * 100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->org_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value=" {{ $master->term }} Month " readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Maturity Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($master->rate  * 100, 5) }}%" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 6 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="font-size: 12px;">Payment Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{number_format($master->pmtamt ?? 0, 2)}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover"  style="font-size: 12px; text-align: right;font-weight: normal;">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Month</th>
                                <th>Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Effective Interest Base On Effective Yield</th>
                                <th>Accrued Interest</th>
                                <th>Amortised UpFront Fee</th>
                                <th>Outstanding Amount Initial UpFront Fee</th>
                                <th>Cummulative Amortized UpFront Fee</th>
                                <th>Unamortized UpFront Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($reports->isEmpty())
                                <tr>
                                    <td colspan="9" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
                                @php
                                    $cumulativeAmortized = 0; // Inisialisasi variabel kumulatif
                                    $totalPaymentAmount = 0;
                                    $totalEffectiveInterest = 0;
                                    $totalAccruedInterest = 0;
                                    $totalAmortisedUpFrontFee = 0;
                                    $totalOutstandingAmountInitialUpFrontFee = 0;
                                    $totalCumulativeAmortizedUpFrontFee = 0;
                                @endphp
                                @foreach ($reports as $report)
                                    @php
                                        $amortisefee = $report->amortisefee;
                                        $cumulativeAmortized += $amortisefee;
                                        $unamortprovFloat = $provFloat;

                                        // Hitung nilai unamortized
                                        if ($loop->first) {
                                            $unamort = $unamortprovFloat;
                                        } else {
                                            $unamort = $unamort + $amortisefee;
                                        }

                                        // Hitung total untuk setiap kolom
                                        $totalPaymentAmount += $report->pmtamt ?? 0;
                                        $totalEffectiveInterest += $report->bunga ?? 0;
                                        $totalAccruedInterest += $report->accrfee ?? 0;
                                        $totalAmortisedUpFrontFee += $report->amortisefee ?? 0;
                                        $totalOutstandingAmountInitialUpFrontFee += $report->outsamtfee ?? 0;
                                        $totalCumulativeAmortizedUpFrontFee += $cumulativeAmortized;
                                    @endphp
                                    <tr style="font-weight:normal;" class="text-right">
                                        <td class="text-center">{{ $report->bulanke ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate' }}</td>
                                        <td>{{ number_format($report->pmtamt ?? 0) }}</td>
                                        <td>{{ number_format($report->bunga ?? 0) }}</td>
                                        <td>{{ number_format($report->accrfee ?? 0) }}</td>
                                        <td>{{ number_format($amortisefee ?? 0) }}</td>
                                        <td>{{ number_format($report->outsamtfee ?? 0) }}</td>
                                        <td>{{ number_format($cumulativeAmortized ?? 0) }}</td>
                                        <td>{{ number_format($unamort ?? 0) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="font-weight:normal;" class="text-right">
                                    <td class="text-center" colspan="2">TOTAL</td>
                                    <td>{{ number_format($totalPaymentAmount ?? 0) }}</td>
                                    <td>{{ number_format($totalEffectiveInterest ?? 0) }}</td>
                                    <td>{{ number_format($totalAccruedInterest ?? 0) }}</td>
                                    <td>{{ number_format($totalAmortisedUpFrontFee ?? 0) }}</td>
                                    <td></td>
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
