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
                        <h5 class="card-title" style="font-size: 16px;">REPORT OUTSTANDING BALANCE - AMORTIZED COST</h5>
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
                                    <label class="col-sm-4 col-form-label text-right">Deal Number</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{$loan->bond_id}}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Issuer Name</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $loan->issuer_name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Face Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->face_value)) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Settlement Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->settle_dt)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Tenor (TTM)</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ $loan->tenor}} Tahun" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Maturity Date</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ date('d/m/Y', strtotime($loan->mtr_date)) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Coupon Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->coupon_rate*100,5) }}%" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Price</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format($loan->price*100,5)}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-4 col-form-label text-right">Fair Value</label>
                                    <div class="col-sm-8">
                                        <input type="text font-size 12px" class="form-control form-control-sm" style="font-size: 12px;" value="{{ number_format((float) str_replace(['$', ','], '', $loan->fair_value)) }}" readonly>
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
                                <th>No.</th>
                                <th>Transaction Date</th>
                                <th>Days Interst </th>
                                <th>Payment Amount</th>
                                <th>Principal Payment</th>
                                <th>Coupon Recognition</th>
                                <th>Coupon Payment</th>
                                <th>Amortized</th>
                                <th>Carrying Amount</th>
                                <th>Cummulative Amortized</th>
                                <th>Unamortized</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cumulativeAmortized=0;
                                $atdisc = $loan->atdiscount;
                                $atdisc = preg_replace('/[^\d.]/', '', $atdisc);
                                $atdiscFloat = (float)$atdisc;
                                $totalharibunga = 0;
                                $totalamortized = 0;
                                $unamort = $atdiscFloat;

                            @endphp
                            @foreach ($reports as $report)
                                @php
                                    $amortized = (float)$report->amortized; // Ambil nilai amortized dari laporan
                                    $cumulativeAmortized += $amortized; // Tambahkan amortized ke total kumulatif

                                    // Ambil nilai carrying amount dari laporan
                                    // Hitung nilai unamortized
                                    if ($loop->first) {
                                        // Untuk baris pertama, gunakan nilai trxcost
                                        // $unamort = $unamortat;
                                    } else {
                                        // Untuk baris selanjutnya, hitung unamortized berdasarkan cumulative amortized
                                        $unamort -= $amortized;
                                    }
                                    $totalharibunga = $totalharibunga + $report->haribunga;
                                    $totalamortized += $report->amortized;
                                @endphp
                            <tr>
                                <td>{{ $report->month_to }}</td>
                                <td class="text-center" >{{ date('d/m/Y', strtotime($report->transac_dt)) }}</td>
                                <td>{{ $report->haribunga }}</td>
                                <td>{{ number_format($report->pmtamt) }}</td>
                                <td>{{ number_format($report->principal_in)}}</td>
                                <td>{{ number_format($report->interest_eir)}}</td>
                                <td>{{ number_format($report->interest) }}</td>
                                <td>{{ number_format($report->amortized) }}</td>
                                <td>{{ number_format($report->fair_value) }}</td>
                                <td>{{ number_format($cumulativeAmortized) }} </td>
                                <td>-{{ number_format($unamort) }}</td>
                            </tr>
                            @endforeach
                            <!-- Row Total -->
                            <tr class="font-weight-normal">
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td>{{ number_format($totalharibunga) }}</td>
                                <td>{{ number_format($reports->sum('pmtamt'), 0) }}</td>
                                <td>{{ number_format($reports->sum('principal_in'), 0) }}</td>
                                <td>{{ number_format($reports->sum('interest_eir'), 0) }}</td>
                                <td>{{ number_format($reports->sum('interest'), 0) }}</td>
                                <td>{{ number_format($totalamortized, 0) }}</td>
                                <!-- <td>{{ number_format($reports->sum('amortized'), 0) }}</td> -->
                                <td></td>
                                <td></td>
                                <td></td>
                                <!-- <td>{{ number_format($reports->sum('fair_value'), 0) }}</td> -->
                                <!-- <td>{{ number_format($reports->sum('cumulativeAmortized'), 0) }}</td>
                                <td>{{ number_format($reports->sum('unamort'), 0) }}</td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
