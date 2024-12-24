<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px; ">
        <div class="container mt-5" style="padding-right: 50px;">
            <section class="section">
                <div class="section-header">
                    <h4>REPORT ACCRUAL INTEREST - EFFECTIVE</h4>
                </div>
                @if(session('pesan'))
                    <div class="alert alert-success">{{ session('pesan') }}</div>
                @endif
                <div class="table-responsive text-center">
                    <table class="table table-striped table-bordered custom-table" style="width: 100%; margin: 0 auto; font-size:12px;">
                        <thead>
                            <tr>
                                <th style="width: 15%; white-space: nowrap ;">Account Number</th>
                                <th class="text-left" style="width: 20%; white-space: nowrap;">Debitor Name</th>
                                <th style="width: 15%; white-space: nowrap;">Original Amount</th>
                                <th style="width: 15%; white-space: nowrap;">Original  Date</th>
                                <th style="width: 10%; white-space: nowrap;">Term</th>
                                <th style="width: 15%; white-space: nowrap;">Maturity  Date</th>
                                <th style="width: 10%; white-space: nowrap;">Interest Rate</th>
                                <th style="width: 15%; white-space: nowrap;">Payment Amount</th>
                                <th style="width: 10%; white-space: nowrap;">Outstanding Amount</th>
                                <th style="width: 15%; white-space: nowrap;">EIR Calculated</th>
                                <th style="width: 10%; white-space: nowrap;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loans as $loan)
                                <tr style="height: 50px;" >
                                    <td class="text-right">{{ $loan->no_acc }}</td>
                                    <td class="text-left">{{ $loan->deb_name }}</td>
                                    <td class="text-right">{{ number_format($loan->org_bal, 2) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($loan->org_date)) }}</td>
                                    <td style="white-space: nowrap;">{{ $loan->term ?? 0 }} Month</td>
                                    <td>{{ date('d/m/Y', strtotime($loan->mtr_date)) }}</td>
                                    <td>{{ number_format($loan->rate * 100, 5) }}%</td>
                                    <td>{{ number_format($loan->pmtamt ?? 0, 2) }}</td>
                                    <td>{{ number_format($loan->org_bal ?? 0, 2) }}</td>
                                    <td>{{ number_format($loan->eircalc_conv * 100, 14) }}%</td>

                                        <td>
                                            <a href="{{ route('report-acc-eff.view', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye" style="margin-right: 5px;"></i> View
                                            </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="showing-entries">
                    Showing
                    {{$loans->firstItem()}}
                    to
                    {{$loans->lastItem()}}
                    of
                    {{$loans->total()}}
                    Results
                </div>
                <div class="d-flex align-items-center">
                    {{ $loans->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-4') }}
                    <label for="per_page" class="form-label mb-0" style="font-size: 0.8rem; margin-right: 15px; margin-left:30px;">Show</label>
                    <select id="per_page" class="form-select form-select-sm" onchange="changePerPage()" style="width: auto;">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    function changePerPage() {
        const perPage = document.getElementById('per_page').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Hapus parameter page saat mengganti per_page
        window.location.href = url;
    }
</script>

<!-- Custom CSS -->
<style>
    .custom-table td, 
    .custom-table th {
        padding-top: 4px !important;    /* Mengurangi padding atas */
        padding-bottom: 4px !important; /* Mengurangi padding bawah */
        vertical-align: middle !important; /* Memastikan konten tetap di tengah vertikal */
    }
</style>