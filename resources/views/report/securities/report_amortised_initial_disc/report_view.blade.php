<div class="content-wrapper" style="font-size: 12px; ">
    <div class="main-content" style="padding-top: 10px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-2">
                    <a href="{{ route('amortisedinitialdisc.exportPdf', ['no_acc' => $reports->first()->no_acc, 'id_pt' => $reports->first()->id_pt])  }}" class="btn btn-danger ">Export to PDF</a>
                    <a href="{{ route('amortisedinitialdisc.exportExcel', ['no_acc' => $reports->first()->no_acc, 'id_pt' => $reports->first()->id_pt]) }}" class="btn btn-success ">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-2" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT AMORTISED INITIAL AT DISCOUNT - TREASURY</h5>
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
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">At Discount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="-{{ number_format($reports->first()->atdiscount, 0) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Deal Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->bond_id }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Outstanding Amount Initial Discount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->outsamt_disc, 0) }}" readonly>
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
                                    <label class="col-sm-4 col-form-label text-right">EIR Calculated Convertion</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->eircalc_conv * 100, 15) }}%" readonly>
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
                                    <label class="col-sm-4 col-form-label text-right">EIR Calculated At Discount</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->atdiscount, 0) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Settlement Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->settle_dt }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Tenor (TTM)</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->tenor }} Year" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 6 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->mtr_date }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Settlement Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $reports->first()->settle_dt }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 7 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Coupon Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->coupon_rate*100, 5) }}%" readonly>
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
                                    <label class="col-sm-3 col-form-label">Fair Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($reports->first()->fair_value, 0) }}" readonly>
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
                                <th>Transaction Date </th>
                                <th>Days Interest</th>
                                <th>Payment Amount</th>
                                <th>Effective Interest Base On Effective Yield</th>
                                <th>Accrual Coupon</th>
                                <th>Amortised At Discount</th>
                                <th>Outstanding Amount Initial At Discount</th>
                                <th>Cummulative Amortized At Discount</th>
                                <th>Unamortized At Discount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $unamortisedAtDiscount = $reports->first()->atdiscount * (-1);
                            @endphp
                            @foreach ($reports as $report)
                            @php
                                $unamortisedAtDiscount += $report->amortise_disc;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $report->month_to }}</td>
                                <td class="text-center">{{ date('d/m/Y', strtotime($report->transac_dt)) }}</td>
                                <td class="text-center">{{ number_format($report->haribunga, 0) }}</td>
                                <td>{{ number_format($report->pmtamt, 0) }}</td>
                                <td>{{ number_format($report->interest_eir ?? 0, 0) }}</td>
                                <td>{{ number_format($report->interest, 0) }}</td>
                                <td>{{ number_format($report->amortise_disc, 0) }}</td>
                                <td>{{ number_format($report->outsamt_disc, 0) }}</td>
                                <td>{{ number_format($report->cum_amortisedisc, 0) }}</td>
                                <td>{{ number_format($unamortisedAtDiscount ?? 0, 0) }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-weight-normal">
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="text-center">{{ number_format($reports->sum('haribunga'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('pmtamt'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('interest_eir'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('interest'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('amortise_disc'), 0) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <!-- <td class="text-right">{{ number_format($reports->first()->atdiscount, 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('amortise_disc'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('amortise_disc') + $reports->sum('outsamt_disc'), 0) }}</td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
