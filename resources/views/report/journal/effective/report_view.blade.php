<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-acc-eff.exportPdf', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-danger">Export to PDF</a>
                    <a href="{{ route('report-acc-eff.exportExcel', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-success">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT JOURNAL - EFFECTIVE</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Number</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->no_branch }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Name</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 3 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Date Of Report</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
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
                        <thead class="thead-dark">
                            <tr>
                                <th>Branch Number</th>
                                <th>GL Account</th>
                                <th>Description of Transaction</th>
                                <th>Valuta</th>
                                <th>Post</th>
                                <th>Amount</th>
                                <th>Posting Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($reports->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
                                @foreach ($reports as $report)
                                    <tr>
                                        <td>{{ $report->bulanke ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center>{{ isset($report->tglangsuran) ? date('d/m/Y', strtotime($report->tglangsuran)) : 'Belum di-generate' }}</td>
                                        <td>{{ $report->days_interest ?? 0 }}</td>
                                        <td>{{ number_format($report->pmtamt ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->withdrawal ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->reimbursement ?? 0, 2) }}</td>
                                        <td>{{ number_format($report->balance ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
