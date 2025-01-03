<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="mb-3">
                    <a href="{{ route('report-acc-si.exportPdf',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch]) }}" class="btn btn-danger">Export to PDF</a>
                    <a href="{{ route('report-acc-si.exportExcel',  ['no_acc' => $loan->no_acc, 'id_pt' => $loan->no_branch])}}" class="btn btn-success">Export to Excel</a>
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT JOURNAL - SIMPLE INTEREST</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Entity Number</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ $loan->no_branch }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Entity Name</label>
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
                                <th>Entity Number</th>
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
                                    <td colspan="10" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
                            @foreach ($reports as $report)
                                <tr>
                                    <td>{{ $report->bulanke }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($report->tglangsuran)) }}</td>
                                    <td>{{ $report->haribunga }}</td>
                                    <td>{{ number_format($report->pmtamt, 2) }}</td>
                                    <td>{{ number_format($report->penarikan, 2) }}</td>
                                    <td>{{ number_format($report->pengembalian, 2) }}</td>
                                    <td>{{ number_format($report->bunga, 2) }}</td>
                                    <td>{{ number_format($report->balance, 2) }}</td>
                                    <td>{{ number_format($report->timegap, 2) }}</td>
                                    <td>{{ number_format($report->outsamtconv, 2) }}</td>
                                </tr>
                            @endforeach
                            @endif
                            <!-- Row Total / Average -->
                            <tr class="font-weight-normal">
                                <td class="text-center" colspan="3">TOTAL / AVERAGE</td>
                                <td>{{ number_format($reports->sum('valuta'), 2) }}</td>
                                <td>{{ number_format($reports->sum('post') / $reports->count(), 2) }}</td>
                                <td>{{ number_format($reports->sum('amount') / $reports->count(), 2) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
