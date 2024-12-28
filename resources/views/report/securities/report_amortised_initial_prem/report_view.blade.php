<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-acc-si.exportPdf',['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])   }}" class="btn btn-danger ">Export to PDF</a>
                    <a href="{{ route('report-acc-si.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])   }}" class="btn btn-success ">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4" style="font-size: 12px;">
                    <div class="card-header">
                        <h5 class="card-title" style="font-size: 16px;">REPORT AMORTISED INITIAL AT PREMIUM - TREASURY</h5>
                    </div>
                    <div class="card-body">
                        <form>
                             <!-- Row 1 -->
                             <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->no_acc }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">At Premium</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{$loan->atpremium}}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Deal Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->bond_id }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Outstanding Amount Premium</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{'null'}}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Issuer Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{$loan->issuer_name}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">EIR Calculated Convertion</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->eircalc_conv * 100, 15) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Face Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ date('d/m/Y', strtotime($loan->face_value)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">EIR Calculated At Premium</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->eircalc_prem * 100, 15) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Settlement Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ date('d/m/Y', strtotime($loan->settle_dt)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Tenor (TTM)</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ $loan->tenor}} Tahun" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 6 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ date('d/m/Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Coupon Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->coupon_rate*100,5) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                             <!-- Row 7 -->
                             <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Price</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format($loan->price*100,5)}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Fair Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" value="{{ number_format((float) str_replace(['$', ','], '', $loan->fair_value)) }}" readonly>
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
                                <th class="text-right">Payment Amount</th>
                                <th class="text-right">Effective Interest Base On Effective Yield </th>
                                <th class="text-right">Reimbursement</th>
                                <th class="text-right">Accrual Coupon</th>
                                <th class="text-right">Amortised At Premium</th>
                                <th class="text-right">Outstanding Amount Initial At Premium</th>
                                <th class="text-right">Cummulative Amortized At Premium</th>
                                <th class="text-right">Unamortized  At Premium</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cummulativeAmortizedPrem = 0;
                                $totalPaymentAmount = 0;
                                $totalEffectiveInterest = 0;
                                $totalReimbursement = 0;
                                $totalAccrualCoupon = 0;
                                $totalAmortisedPrem = 0;
                                $totalOutstandingAmount = 0;
                                $totalUnamortized = 0;
                            @endphp
                            @foreach ($reports as $report)
                                @php
                                    $cummulativeAmortizedPrem += floatval($report->amortise_prem);
                                    $totalPaymentAmount += $report->pmtamt;
                                    $totalEffectiveInterest += $report->interest;
                                    $totalReimbursement += $report->principal_out;
                                    $totalAccrualCoupon += $report->principal_in;
                                    $totalAmortisedPrem += $report->amortise_prem;
                                    $totalOutstandingAmount += $report->outsamt_prem;
                                    $totalUnamortized += ($report->amortise_prem + $report->outsamt_prem);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $report->month_to }}</td>
                                    <td class="text-center"> {{ date('d/m/Y', strtotime($report->transac_dt)) }}</td>
                                    <td class="text-center">{{ $report->haribunga }}</td>
                                    <td class="text-right">{{ number_format($report->pmtamt, 2) }}</td>
                                    <td class="text-right">{{ number_format($report->interest ?? 0, 5) }}</td>
                                    <td class="text-right">{{ number_format($report->principal_out, 2) }}</td>
                                    <td class="text-right">{{ number_format($report->principal_in, 2) }}</td>
                                    <td class="text-right">{{ number_format($report->amortise_prem, 2) }}</td>
                                    <td class="text-right">{{ number_format($report->outsamt_prem, 2) }}</td>
                                    <td class="text-right">{{ number_format($cummulativeAmortizedPrem, 2) }}</td>
                                    <td class="text-right">{{ number_format($report->amortise_prem + $report->outsamt_prem ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                            
                            <!-- Row Total -->
                            <tr class="table-secondary font-weight-normal">
                                <td colspan="3" class="text-center">TOTAL</td>
                                <td class="text-right">{{ number_format($totalPaymentAmount, 2) }}</td>
                                <td class="text-right">{{ number_format($totalEffectiveInterest, 5) }}</td>
                                <td class="text-right">{{ number_format($totalReimbursement, 2) }}</td>
                                <td class="text-right">{{ number_format($totalAccrualCoupon, 2) }}</td>
                                <td class="text-right">{{ number_format($totalAmortisedPrem, 2) }}</td>
                                <td class="text-right">{{ number_format($totalOutstandingAmount, 2) }}</td>
                                <td class="text-right">{{ number_format($cummulativeAmortizedPrem, 2) }}</td>
                                <td class="text-right">{{ number_format($totalUnamortized, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
