<div class="content-wrapper">
    <div class="main-content" style="padding-top: 20px; ">
        <div class="container mt-5" style="padding-right: 50px;">
            <section class="section">
                <div class="section-header">
                    <h4>REPORT AMORTISED INITIAL AT DISCOUNT - TREASURY</h4>
                </div>
                @if(session('pesan'))
                    <div class="alert alert-success">{{ session('pesan') }}</div>
                @endif
                <div class="table-responsive text-center">
                    <table class="table table-striped table-bordered custom-table" style="width: 100%; margin: 0 auto; font-size:12px;">
                        <thead>
                            <tr>
                                <th style="width: 15%; white-space: nowrap ;">Account Number</th>
                                <th class="text-left" style="width: 20%; white-space: nowrap;">Deal Number</th>
                                <th style="width: 15%; white-space: nowrap;">Issuer Name</th>
                                <th style="width: 15%; white-space: nowrap;">Face Value</th>
                                <th style="width: 10%; white-space: nowrap;">Settlement Date</th>
                                <th style="width: 15%; white-space: nowrap;">Tenor (TTM)</th>
                                <th style="width: 10%; white-space: nowrap;">Maturity Date</th>
                                <th style="width: 15%; white-space: nowrap;">Coupon Rate</th>
                                <th style="width: 10%; white-space: nowrap;">Price</th>
                                <th style="width: 15%; white-space: nowrap;">Fair Value</th>
                                <th style="width: 15%; white-space: nowrap;">At Discount</th>
                                <th style="width: 15%; white-space: nowrap;">Outstanding Amount Initial At Discount</th>
                                <th style="width: 15%; white-space: nowrap;">EIR Calculated Convertion</th>
                                <th style="width: 15%; white-space: nowrap;">EIR Calculated At Discount</th>
                                <th style="width: 10%; white-space: nowrap;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loans as $loan)
                                <tr>
                                    <td class="text-right">{{ $loan->no_acc }}</td>
                                    <td>{{ $loan->bond_id }}</td>
                                    <td >{{$loan->issuer_name}}</td>
                                    <td>{{number_format((float) str_replace(['$', ','], '',$loan->face_value)) }}</td>
                                    <td style="white-space: nowrap;">{{ date('d/m/Y', strtotime($loan->settle_dt)) }}</td>
                                    <td>{{ $loan->tenor}} Tahun</td>
                                    <td>{{ date('d/m/Y', strtotime($loan->mtr_date)) }}</td>
                                    <td>{{ number_format($loan->coupon_rate*100,5) }}%</td>
                                    <td>{{ number_format($loan->price*100,5)}}</td>
                                    <td>{{ number_format((float) str_replace(['$', ','], '', $loan->fair_value)) }}</td>
                                    <td>{{ number_format((float) str_replace(['$', ','], '', $loan->atdiscount),2) }}</td>
                                    <td>{{ number_format((float) str_replace(['$', ','], '', $loan->atdiscount),2) }}</td>
                                    <td>{{ number_format($loan->eircalc_conv * 100, 15) }}%</td>
                                    <td>{{ number_format($loan->eircalc_disc * 100, 15) }}%</td>
                                        <td>
                                            <a href="{{ route('report-amortised-initial-disc.view', ['no_acc' => $loan->no_acc, 'id_pt' => $loan->id_pt]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye" style="margin-right: 5px;"></i> View
                                            </a>
                                            {{-- <a href="{{route('under')}}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye" style="margin-right: 5px;"></i> View
                                            </a> ---}}
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
