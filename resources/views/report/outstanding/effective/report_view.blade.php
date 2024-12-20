<div class="content-wrapper" style="font-size: 12px;">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5">
            <section class="section">
                <div class="d-flex justify-content-start mb-3 align-items-center">
                   

                    @if(isset($masters) && count($masters) > 0)
                        <a href="{{ route('report-outstanding-eff.exportPdf', ['id_pt' => $masters[0]['no_branch']])}}" class="btn btn-danger">Export to PDF</a>
                        <a href="{{ route('report-outstanding-eff.exportExcel', ['id_pt' => $masters[0]['no_branch']])}}" class="btn btn-success">Export to Excel</a>
                    @endif
                </div>

                <!-- Loan Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"style="font-size: 16px;">REPORT OUTSTANDING - EFFECTIVE</h5>
                    </div>

                    
                    <!-- <div class="card-body">
                        <form>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Number</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Branch Name</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">GL Group</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 row d-flex align-items-center mb-1">
                                    <label class="col-sm-3 col-form-label">Date Of Report</label>
                                    <div class="col-sm-6">
                                        <input type="text font-size 12px" class="form-control" style="font-size: 12px;" value="{{ 'null' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div> -->
                </div>

                 <div class="d-flex align-items-center me-3 mb-3">
                        <select class="form-select me-2" style="width: 140px;" id="monthSelect">
                            <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>January</option>
                            <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>February</option>
                            <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>March</option>
                            <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                            <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>May</option>
                            <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>June</option>
                            <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>July</option>
                            <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>August</option>
                            <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                            <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>October</option>
                            <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                            <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>December</option>
                        </select>

                        <input type="number" class="form-select" id="yearInput" 
                               style="width: 100px;" 
                               value="{{ date('Y') }}" 
                               min="2000" 
                               max="2099">
                    </div>

                <!-- Report Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" style="font-size: 12px;">
                        <thead class="thead-dark">
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">Entity Number</th>
                                <th style="white-space: nowrap;">Account Number</th>
                                <th style="white-space: nowrap;">Debitor Name</th>
                                <th style="white-space: nowrap;">GL Account</th>
                                <th style="white-space: nowrap;">Loan Type</th>
                                <th style="white-space: nowrap;">GL Group</th>
                                <th style="white-space: nowrap;">Original Date</th>
                                <th style="white-space: nowrap;">Term (Months)</th>
                                <th style="white-space: nowrap;">Maturity Date</th>
                                <th style="white-space: nowrap;">Interest Rate</th>
                                <th style="white-space: nowrap;">Payment Amount</th>
                                <th style="white-space: nowrap;">EIR Amortised Cost Exposure</th>
                                <th style="white-space: nowrap;">EIR Amortised Cost Calculated</th>
                                <th style="white-space: nowrap;">Current Balance</th>
                                <th style="white-space: nowrap;">Carrying Amount</th>
                                <th style="white-space: nowrap;">Oustanding Receivable</th> 
                                <th style="white-space: nowrap;">Outstanding Interest</th>
                                <th style="white-space: nowrap;">Cummulative Time Gap</th>
                                <th style="white-space: nowrap;">Unamortized Transaction Cost</th> 
                                <th style="white-space: nowrap;">Unamortized UpFront Fee</th>
                                <th style="white-space: nowrap;">Unamortized Interest Deferred</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($master->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Data tidak ditemukan atau belum di-generate</td>
                                </tr>
                            @else
                                @foreach ($master as $index => $loan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $loan->no_branch ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ $loan->no_acc ?? 'Data tidak ditemukan' }}</td>
                                        <td style="white-space: nowrap;">{{ $loan->deb_name ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ $loan->coa ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ $loan->ln_type ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ $loan->GROUP ?? 'Data tidak ditemukan' }}</td>
                                        <td class="text-center">{{ isset($loan->org_date_dt) ? date('d/m/Y', strtotime($loan->org_date_dt)) : 'Belum di-generate' }}</td>
                                        <td class="text-center">{{ $loan->term ?? 0 }}</td>
                                        <td class="text-center">{{ isset($loan->mtr_date_dt) ? date('d/m/Y', strtotime($loan->mtr_date_dt)) : 'Belum di-generate' }}</td>
                                        <td class="text-right">{{ number_format($loan->rate * 100?? 0, 5) }}%</td>
                                        <td class="text-right">{{ number_format($loan->pmtamt ?? 0, 2) }}</td>
                                        <td class="text-right">{{ number_format($loan->eirex*100 ?? 0, 14) }}%</td>
                                        <td class="text-right">{{ number_format($loan->eircalc*100 ?? 0, 14) }}%</td>
                                        <td class="text-right">{{ number_format($loan->cbal ?? 0,2) }}</td>
                                        <td class="text-right">{{ number_format($loan->carrying_amount ?? 0, 2) }}</td>
                                        <td class="text-right">{{ number_format($loan->bilprn + $loan->bilint ?? 0, 2) }}</td> //
                                        <td class="text-right">{{ number_format($loan->bilint + $loan->bilprn ?? 0, 2) }}</td>
                                        <td class="text-right">{{ number_format($loan->cum_timegap ?? 0, 2) }}</td>
                                        <td class="text-right">{{ number_format($loan->cum_amortisecost ?? 0, 2) }}</td>
                                        <td class="text-right">{{ number_format($loan->cum_amortisefee ?? 0, 2) }}</td>
                                        <td class="text-right">{{ number_format($loan->cum_bunga ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach

                                <!-- Row Total / Average -->
                                <tr class="table-secondary font-weight-bold">
                                    <td colspan="11" class="text-center"><strong>TOTAL / AVERAGE:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($master->sum('pmtamt') ?? 0, 2) }}</strong></td>
                                    <td class="text-right"><strong>{{ number_format(($master->avg('eirex') * 100) ?? 0, 14) }}%</strong></td>
                                    <td class="text-right"><strong>{{ number_format(($master->avg('eircalc') * 100) ?? 0, 14) }}%</strong></td>
                                    <td class="text-end"><strong>{{ number_format($master->sum('cbal') ?? 0, 2) }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($master->sum('carrying_amount') ?? 0, 2) }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($master->sum('cbal') - $master->sum('carrying_amount') ?? 0, 2) }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($master->sum('cum_bunga') ?? 0, 2) }}</strong></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end"><strong>{{ number_format($master->sum('cum_amortisefee') ?? 0, 2) }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($master->sum('cum_amortized') ?? 0, 2) }}</strong></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set nilai default untuk bulan dan tahun dari parameter URL atau data yang dikirim dari controller
    const selectedMonth = "{{ $bulan ?? date('n') }}";
    const selectedYear = "{{ $tahun ?? date('Y') }}";
    
    // Set nilai default untuk select bulan dan input tahun
    document.getElementById('monthSelect').value = selectedMonth;
    document.getElementById('yearInput').value = selectedYear;
});

// Event listener untuk perubahan bulan atau tahun
document.getElementById('monthSelect').addEventListener('change', updateReport);
document.getElementById('yearInput').addEventListener('change', updateReport);

function updateReport() {
    const month = document.getElementById('monthSelect').value;
    const year = document.getElementById('yearInput').value;
    const id_pt = "{{ Auth::user()->id_pt ?? '' }}";
    
    // Sesuaikan dengan route yang benar
    window.location.href = `/report-outstanding-effective/view/${id_pt}?bulan=${month}&tahun=${year}`;
}
</script>

<style>
    /* ... style yang sudah ada ... */
    
    .table-secondary {
        background-color: #f2f2f2 !important;
    }

    .font-weight-bold {
        font-weight: bold;
    }

    .text-end {
        text-align: right;
    }

    .text-right {
        text-align: right;
        padding-right: 20px;
    }
</style>
