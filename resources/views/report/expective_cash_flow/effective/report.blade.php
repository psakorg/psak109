<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px;">
        <div class="container mt-5" style="padding-right: 50px;">
            <section class="section">
                <div class="section-header">
                    <h4>REPORT EXPECTIVE CASH FLOW - EFFECTIVE</h4>
                </div>
                @if(session('pesan'))
                    <div class="alert alert-success">{{ session('pesan') }}</div>
                @endif
                <div class="table-responsive text-center">
                    <table class="table table-striped table-bordered custom-table" style="width: 100%; margin: 0 auto;">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Account Number</th>
                                <th style="width: 25%;">Debitor Name</th>
                                <th style="width: 20%;">Original Amount</th>
                                <th style="width: 15%;">Original Date</th>
                                <th style="width: 15%;">Term</th>
                                <th style="width: 20%;">Maturity Date</th> <!-- Perlebar kolom Maturity Date -->
                                <th style="width: 10%;">Interest Rate</th>
                                <th style="width: 10%;">Payment Amount</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loans as $loan)
                                <tr>
                                    <td>{{ $loan->no_acc }}</td>
                                    <td>{{ $loan->deb_name }}</td>
                                    <td>{{ number_format($loan->org_bal, 2) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($loan->org_date)) }}</td>
                                    <td>{{ $loan->term }} Month</td>
                                    <td>{{ date('d/mY', strtotime($loan->mtr_date)) }}</td>
                                    <td>{{ number_format($loan->rate  * 100, 2) }}%</td>
                                    <td>{{number_format($loan->pmtamt ?? 0, 2)}}</td>
                                    <td>
                                        <a href="{{ route('report-expectcfeff-eff.view', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt])}}" class="btn btn-sm btn-info">
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

</style>

<!-- Font Awesome Link -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous">
function changePerPage() {
        const perPage = document.getElementById('per_page').value;
        window.location.href = `?per_page=${perPage}`; // Redirect dengan parameter per_page
    }
</script>
