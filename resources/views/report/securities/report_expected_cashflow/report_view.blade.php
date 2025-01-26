<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-expected-cashflow.exportPdf',['no_acc' => $reports->first()->no_acc, 'id_pt' => $reports->first()->id_pt])   }}" class="btn btn-danger ">Export to PDF</a>
                    <a href="{{ route('report-expected-cashflow.exportExcel', ['no_acc' => $reports->first()->no_acc, 'id_pt' => $reports->first()->id_pt])   }}" class="btn btn-success ">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT EXPECTED CASH FLOW - TREASURY</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $reports->first()->no_acc }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Deal Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $reports->first()->bond_id }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Issuer Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $reports->first()->issuer_name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Face Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($reports->first()->face_value, 0) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Settlement Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $reports->first()->settle_dt }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Tenor (TTM)</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $reports->first()->tenor }} Year" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $reports->first()->mtr_date }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Coupon Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($reports->first()->coupon_rate*100, 5) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Price</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($reports->first()->price*100, 5) }}%" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Fair Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($reports->first()->fair_value, 0) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <h2 style="font-size: 16px;">Report Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px;">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Month</th>
                                <th class="text-center">Transaction Date</th>
                                <th class="text-center">Days Interest</th>
                                <th class="text-center">Payment Amount</th>
                                <th class="text-center">Principal Payment</th>
                                <th class="text-center">Coupon Payment</th>
                                <th class="text-center">Balance Contractual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td class="text-center">{{ $report->month_to }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($report->transac_dt)) }}</td>
                                    <td class="text-center">{{ number_format($report->haribunga, 0) }}</td>
                                    <td class="text-right">{{ number_format($report->expected_cash_flow, 0) }}</td>
                                    <td class="text-right">{{ number_format($report->principal_in, 0) }}</td>
                                    <td class="text-right">{{ number_format($report->interest, 0) }}</td>
                                    <td class="text-right">{{ number_format($report->face_value, 0) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-normal">
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="text-center">{{ number_format($reports->sum('haribunga'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('expected_cash_flow'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('principal_in'), 0) }}</td>
                                <td class="text-right">{{ number_format($reports->sum('interest'), 0) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
