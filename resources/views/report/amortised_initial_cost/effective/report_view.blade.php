<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-amorinitcost-eff.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-danger"><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-amorinitcost-eff.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success"><i class="fas fa-file-excel"></i>Export to Excel</a>
                </div>
                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;" >REPORT AMORTISED INITIAL COST - EFFECTIVE</h5>
                    </div>
                    <div class="card-body" style="font-size: 12px;">
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
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">Transaction Cost</label>
                                    <div class="col-sm-8">
                                        @php
                                            // Misalkan trxcost adalah string dengan simbol mata uang
                                            $trxcost = $master->trxcost; // Ambil nilai dari database
                                            // Hapus simbol mata uang dan pemisah ribuan
                                            $trxcost = preg_replace('/[^\d.]/', '', $trxcost);
                                            // Konversi ke float
                                            $trxcostFloat = (float)$trxcost;
                                        @endphp
                                        <input type="text" class="form-control" style="font-size: 12px;" value="{{ number_format($trxcostFloat, 2)}}" readonly>
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
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end" style="white-space: nowrap;">Outstanding Initial Cost</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->org_bal, 2) }}" readonly>
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
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">EIR Cost Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ number_format($loan->eircalc_cost* 100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->org_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">Maturity Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->term }} Month" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{number_format($master->rate  * 100, 5)  }} %" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive" style="font-size: 12px; text-align: right; font-weight:normal;">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Month</th>
                                <th>Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Effective Interest Base On Effective Yield (UF/TC)</th>
                                <th>Effective Interest Base On Effective Yield (UF)</th>
                                <th>Amortised Transaction Cost</th>
                                <th>Outstanding Amount Initial Transaction Cost</th>
                                <th>Cumulative Amortized Transaction Cost</th>
                                <th>Unamortized Transaction Cost</th>
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
                                $totalEffectiveInterestUF_TC = 0;
                                $totalEffectiveInterestUF = 0;
                                $totalAmortised = 0;
                                $totalOutstandingAmountInitialTransactionCost = 0;
                                $totalUnamortizedTransactionCost = 0;
                                $reportCount = count($reports);
                            @endphp
                                @foreach ($reports as $report)
                                @php
                                    $amortized = $report->amortisecost; // Ambil nilai amortized dari laporan
                                    $cumulativeAmortized += $amortized; // Tambahkan amortized ke total kumulatif
                                    $unamortrxcost = $trxcostFloat;
                                    $amorcost = $report->amortisecost;
                                    // Hitung nilai unamortized
                                    if ($loop->first) {
                                        // Untuk baris pertama, gunakan nilai trxcost
                                        $unamort = $unamortrxcost;
                                    } else {
                                        // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                                        $unamort = $unamort + $amortized;
                                    }

                                    // Hitung total untuk setiap kolom
                                    $totalPaymentAmount += $report->pmtamt ?? 0;
                                    $totalEffectiveInterestUF_TC += $report->bunga ?? 0;
                                    $totalEffectiveInterestUF += $report->bunga ?? 0;
                                    $totalAmortised += $amortized;
                                    $totalOutstandingAmountInitialTransactionCost += $report->outsamtcost ?? 0;
                                    $totalUnamortizedTransactionCost += $unamort;
                                @endphp
                                    <tr style="font-weight:normal;" class="text-right">
                                        <td class="text-center">{{ $report->bulanke ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate' }}</td>
                                        <td>{{ number_format($report->pmtamt ?? 0) }}</td>
                                        <td>{{ number_format($report->bunga ?? 0) }}</td>
                                        <td>{{ number_format($report->bunga ?? 0) }}</td>
                                        <td>{{ number_format($report->amortisecost ?? 0) }}</td>
                                        <td>{{ number_format($report->outsamtcost ?? 0) }}</td>
                                        <td>{{ number_format($cumulativeAmortized ?? 0) }}</td>
                                        <td>{{ number_format($unamort ?? 0) }}</td>
                                    </tr>
                                @endforeach
                                <!-- Row Total / Average -->
                                <tr style="font-weight:normal;" class="text-right">
                                    <td class="text-center" colspan="2">TOTAL</td>
                                    <td>{{ number_format($totalPaymentAmount ?? 0) }}</td>
                                    <td>{{ number_format($totalEffectiveInterestUF_TC ?? 0) }}</td>
                                    <td>{{ number_format($totalEffectiveInterestUF ?? 0) }}</td>
                                    <td>{{ number_format($totalAmortised ?? 0) }}</td>
                                    <td>{{ number_format($totalOutstandingAmountInitialTransactionCost ?? 0) }}</td>
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
