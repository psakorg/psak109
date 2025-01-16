<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-amorcost-eff.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-danger "><i class="fas fa-file-pdf"></i>Export to PDF</a>
                    <a href="{{ route('report-amorcost-eff.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success "><i class="fas fa-file-excel"></i>Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT AMORTISED COST - EFFECTIVE</h5>
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
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">UpFront Fee</label>
                                    <div class="col-sm-8">
                                        @php
                                        // Menghitung nilai org amountt
                                        //    $upfrontFee = round(-($loan->org_bal * 0.01), 0);
                                        @endphp
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="-{{ number_format($master->prov, 2) }}" readonly>
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
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($trxcostFloat, 2)}}" readonly>
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
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">Carrying Amount</label>
                                    <div class="col-sm-8">
                                        @php
                                        // Menghitung nilai org amount
                                        //    $CarryingAmount=$loan->org_bal+$upfrontFee
                                            $CarryingAmount=$loan->nbal-$master->prov;
                                        @endphp
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($CarryingAmount?? 0, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Original Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->org_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">EIR Exposure</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->eirex * 100, 14) }} %" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 6 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Term</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $loan->term }} Month" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">EIR Calculated</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->eircalc * 100, 14)  }} %" readonly>
                                    </div>
                                </div>

                            </div>
                            <!-- Row 7 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Loan Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end">Payment Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($master->pmtamt ?? 0, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 8 -->
                             <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($master->rate  * 100, 5) }}%" readonly>
                                    </div>
                                </div>
                                <!-- <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end" style="white-space: nowrap;">Ibase (Year Base)</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="360" readonly>
                                    </div>
                                </div> -->
                            </div>
                            <!-- Row 9 -->
                            <!-- <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label" style="white-space: nowrap;">Outstanding Interest</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->bilint + $loan->bilprn ?? 0, 5) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label d-flex justify-content-end" style="white-space: nowrap;">Outstanding Receivable</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->bilprn + $loan->bilint ?? 0, 2) }}" readonly>
                                    </div>
                                </div>
                            </div> -->
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px; text-align: right; font-weight:normal;">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Month</th>
                                <th>Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Interest Recognition</th>
                                <th>Interest Payment</th>
                                <th>Amortised</th>
                                <th>Carrying Amount</th>
                                <th>Cumulative Amortized</th>
                                <th>Unamortized</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($reports->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
                            @php
                                $cumulativeAmortized = 0; // Inisialisasi variabel kumulatif
                                $totalPaymentAmount = 0;
                                $totalInterestRecognition = 0;
                                $totalInterestPayment = 0;
                                $totalAmortised = 0;
                                $totalCarryingAmount = 0;
                                $totalCumulativeAmortized = 0;
                            @endphp
                                @foreach ($reports as $report)
                                @php
                                    $amortized = $report->amortized; // Ambil nilai amortized dari laporan
                                    $cumulativeAmortized += $amortized; // Tambahkan amortized ke total kumulatif
                                    $carryingAmount = $report->baleir ?? 0; // Ambil nilai carrying amount dari laporan

                                    // Hitung nilai unamortized
                                    if ($loop->first) {
                                        // Untuk baris pertama, gunakan nilai pov negatif
                                        $unamortized = -$master->prov;
                                    } else {
                                        // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                                        $unamortized = $unamortized + $amortized;
                                    }

                                    // Hitung total untuk setiap kolom
                                    $totalPaymentAmount += $report->pmtamt ?? 0;
                                    $totalInterestRecognition += $report->bungaeir ?? 0;
                                    $totalInterestPayment += $report->bunga ?? 0;
                                    $totalAmortised += $amortized;
                                    $totalCarryingAmount += $carryingAmount;
                                    $totalCumulativeAmortized += $cumulativeAmortized;
                                @endphp
                                    <tr style="font-weight:normal;">
                                        <td class="text-center">{{ $report->bulanke ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate' }}</td>
                                        <td class="text-right">{{ number_format($report->pmtamt ?? 0) }}</td>
                                        <td class="text-right">{{ number_format($report->bungaeir ?? 0) }}</td>
                                        <td class="text-right">{{ number_format($report->bunga ?? 0) }}</td>
                                        <td class="text-right">{{ number_format($report->amortized ?? 0) }}</td>
                                        <td class="text-right">{{ number_format($report->baleir ?? 0) }}</td>
                                        <td class="text-right">{{ number_format($cumulativeAmortized ?? 0) }}</td>
                                        <td class="text-right">{{ number_format($unamortized ?? 0) }}</td>
                                    </tr>
                                @endforeach
                                <!-- Row Total -->
                                <tr style="font-weight:normal;">
                                    <td class="text-center" colspan="2">TOTAL</td>
                                    <td class="text-right">{{ number_format($reports->sum('pmtamt') ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($reports->sum('bungaeir') ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($reports->sum('bunga') ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($reports->sum('amortized') ?? 0) }}</td>
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
