<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 10px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-2">
                    <a href="{{ route('amortisedinitialbrokeragefee.exportPdf', ['no_acc' => $reports->first()->no_acc, 'id_pt' => $reports->first()->id_pt])  }}" class="btn btn-danger ">Export to PDF</a>
                    <a href="{{ route('amortisedinitialbrokeragefee.exportExcel', ['no_acc' => $reports->first()->no_acc, 'id_pt' => $reports->first()->id_pt]) }}" class="btn btn-success ">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-2" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT AMORTISED INITIAL BROKERAGE FEE - TREASURY </h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->no_acc }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Roww 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Deal Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->bond_id }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Issuer Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->issuer_name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Brokerage Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->brokerage }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Face Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->face_value, 0) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Outstanding Amount Initial Brokerage Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->outsamt_brok, 0) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Settlement Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->org_date }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">EIR Calculated conversion</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->eircalc_conv*100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 6 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Tenor (TTM)</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->tenor }} Tahun" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">EIR Calculated Brokerage Fee</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->eircalc_brok*100, 14) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 7 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->mtr_date }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Price</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->price*100, 5) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 8 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Coupon Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->coupon_rate*100, 5) }}%" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">IBase</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->ibase, 0) }}" readonly>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px; text-align: right;font-weight: normal;">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Month</th>
                                <th>Transaction Date</th>
                                <th>Days Interest</th>
                                <th>Payment Amount</th>
                                <th>Effective Interest Based on Effective Yield</th>
                                <th>Accrual Coupon</th>
                                <th>Amortised Brokerage Fee</th>
                                <th>Outstanding Amount Initial Brokerage Fee </th>
                                <th>Cummulative Amortized Brokerage Fee</th>
                                <th>Unamortized Brokerage Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $unamort = $reports->first()->brokerage;
                            @endphp
                            @foreach ($reports as $report)
                                @php
                                    $amortized = (float)$report->brokerage;
                                    if (!$loop->first) {
                                        $unamort -= $amortized;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $report->month_to }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($report->transac_dt)) }}</td>
                                    <td class="text-center">{{ number_format($report->haribunga, 0) }}</td>
                                    <td>{{ number_format($report->outbrok, 0) }}</td>
                                    <td>{{ number_format($report->interest, 0) }}</td>
                                    <td>{{ number_format($report->accr_brok, 0) }}</td>
                                    <td>{{ number_format($report->amortise_brok, 0) }}</td>
                                    <td>{{ number_format($report->outsamt_brok, 0) }}</td>
                                    <td>{{ number_format($report->cum_amortisebrok, 0) }}</td>
                                    <td>-{{ number_format($unamort, 0) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-normal">
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td>{{ number_format($reports->sum('haribunga'), 0) }}</td>
                                <td>{{ number_format($reports->sum('outbrok'), 0) }}</td>
                                <td>{{ number_format($reports->sum('interest'), 0) }}</td>
                                <td>{{ number_format($reports->sum('accr_brok'), 0) }}</td>
                                <td>{{ number_format($reports->sum('amortise_brok'), 0) }}</td>
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
