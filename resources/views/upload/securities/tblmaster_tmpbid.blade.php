<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Table Master - Effective</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f4f7fc;
      display: flex;
      justify-content: center; /* Center horizontally */
      align-items: center; /* Center vertically */
      height: 100vh; /* Full height of the viewport */
      margin: 0; /* Remove default margin */
      font-size: 12px;
    }
    .section-header {
      text-align: center;
      margin-top: 50px; /* Adjusted for better vertical centering */
      margin-bottom: 30px;
    }
    h1 {
      font-weight: bold;
      font-size: 2rem;
      color: #007bff;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .table th, .table td {
      vertical-align: middle;
      padding: 12px;
    }
    .table th {
      background-color: #007bff;
      color: white;
    }
    .table-hover tbody tr:hover {
      background-color: #e1f5fe;
    }
    .modal-header {
      background-color: #007bff;
      color: white;
    }
    .btn-primary, .btn-warning, .btn-success {
      transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }
    .btn-warning:hover {
      background-color: #e0a800;
      transform: scale(1.05);
    }
    .btn-success:hover {
      background-color: #218838;
      transform: scale(1.05);
    }
        @keyframes popupFadeIn {
        0% {
            opacity: 0;
            transform: scale(0.5); /* Mulai dari ukuran setengah */
        }
        100% {
            opacity: 1;
            transform: scale(1); /* Ukuran normal */
        }
    }

    .popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        z-index: 1000;
        display: none; /* Hide by default */
        text-align: center; /* Rata tengah */
        animation: popupFadeIn 0.5s ease; /* Tambahkan animasi */
    }
    .popup.success {
        border-left: 5px solid green;
    }
    .popup.error {
        border-left: 5px solid red;
    }
    .popup .icon {
        font-size: 40px;
        margin-bottom: 10px;
    }
    .overlay {
    display: none; /* Sembunyikan overlay secara default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Latar belakang hitam transparan */
    z-index: 999; /* Pastikan overlay di bawah pop-up */
}
  </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="container ms-5 mt-5">
            <div class="section-header">
                <h1>Data Table Master TmpBid</h1>

            </div>
            <div class="overlay" id="overlay">
            <div id="messagePopup" class="popup">
                <div id="popupIcon" class="icon"></div>
                <div id="popupMessage"></div>
                <button id="closePopup" class="btn btn-secondary mt-3">Close</button>
            </div>
            </div>
            <!-- Button Section -->
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <button type="button" class="btn btn-success btn-icon-split" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import"></i> Upload
                    </button>

                    <!-- <button type="button" class="btn btn-warning btn-icon-split" data-bs-toggle="modal" data-bs-target="#executeModal">
                        <i class="fas fa-play"></i> Execute
                    </button>

                    <form action="{{ route('effective.tblmaster.clear') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-icon-split">
                            <i class="fas fa-trash"></i> Clear Data
                        </button>
                    </form> -->
                </div>
            </div>


            <!-- Data Table -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">tblmaster_tmpbid</h6>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-hover table-bordered text-center">
                        <thead>
                            <tr>
                                <th>No Branch</th>
                                <th>Bond ID</th>
                                <th>Issuer Name</th>
                                <th>Status</th>
                                <th>Bond Type</th>
                                <th>Org Date</th>
                                <th>Org Date DT</th>
                                <th>Tenor</th>
                                <th>MTR Date</th>
                                <th>MTR Date DT</th>
                                <th>Org Bal</th>
                                <th>Coupon Rate</th>
                                <th>Price</th>
                                <th>Face Value</th>
                                <th>Prebal</th>
                                <th>LREBD</th>
                                <th>LREBD DT</th>
                                <th>NREBD</th>
                                <th>NREBD DT</th>
                                <th>PMT AMT</th>
                                <th>Bond GRP</th>
                                <th>GL Group</th>
                                <th>Trade DT</th>
                                <th>Trade DT Time</th>
                                <th>Settle DT</th>
                                <th>Settle DT Time</th>
                                <th>Eval DT</th>
                                <th>Eval DT Time</th>
                                <th>Brokerage</th>
                                <th>At Discount</th>
                                <th>At Premium</th>
                                <th>Classification</th>
                                <th>ID PT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($securities as $item)
                            <tr>
                                <td>{{ $item->no_branch }}</td>
                                <td>{{ $item->bond_id }}</td>
                                <td>{{ $item->issuer_name }}</td>
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->bond_type }}</td>
                                <td>{{ $item->org_date }}</td>
                                <td>{{ $item->org_date_dt ? date('d/m/Y', strtotime($item->org_date_dt)) : '' }}</td>
                                <td>{{ number_format($item->tenor, 2) }} Tahun</td>
                                <td>{{ $item->mtr_date }}</td>
                                <td>{{ $item->mtr_date_dt ? date('d/m/Y', strtotime($item->mtr_date_dt)) : '' }}</td>
                                <td>{{ number_format($item->org_bal, 0) }}</td>
                                <td>{{ number_format($item->coupon_rate*100, 5) }}%</td>
                                <td>{{ number_format($item->price*100, 5) }}%</td>
                                <td>{{ number_format($item->face_value, 0) }}</td>
                                <td>{{ number_format($item->prebal, 0) }}</td>
                                <td>{{ $item->lrebd }}</td>
                                <td>{{ $item->lrebd_dt ? date('d/m/Y', strtotime($item->lrebd_dt)) : '' }}</td>
                                <td>{{ $item->nrebd }}</td>
                                <td>{{ $item->nrebd_dt ? date('d/m/Y', strtotime($item->nrebd_dt)) : '' }}</td>
                                <td>{{ number_format($item->pmtamt, 0) }}</td>
                                <td>{{ $item->bond_grp }}</td>
                                <td>{{ $item->gl_group }}</td>
                                <td>{{ $item->tradedt }}</td>
                                <td>{{ $item->trade_dt ? date('d/m/Y', strtotime($item->trade_dt)) : '' }}</td>
                                <td>{{ $item->settledt }}</td>
                                <td>{{ $item->settle_dt ? date('d/m/Y', strtotime($item->settle_dt)) : '' }}</td>
                                <td>{{ $item->evaldt }}</td>
                                <td>{{ $item->eval_dt ? date('d/m/Y', strtotime($item->eval_dt)) : '' }}</td>
                                <td>{{ number_format($item->brokerage, 0) }}</td>
                                <td>{{ number_format($item->atdiscount, 0) }}</td>
                                <td>{{ number_format($item->atpremium, 0) }}</td>
                                <td>{{ $item->clasification }}</td>
                                <td>{{ $item->id_pt }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            @if($securities->isEmpty())
            <div class="alert alert-warning text-center mt-3">Data not found</div>
            @endif

       <!-- Pagination Links -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>

                {{-- Showing {{ $securities->firstItem() }} to {{ $securities->lastItem() }} of {{ $securities->total() }} entries --}}
            </div>
            <div class="d-flex align-items-center">
                <div class="me-2 d-flex align-items-center">

                </div>
                {{ $securities->links() }} <!-- Menampilkan pagination -->
                <label for="per_page" class="form-label mb-0" style="font-size: 0.8rem; margin-right: 5px;">Show</label>
                <select id="per_page" class="form-select form-select-sm" onchange="changePerPage()" style="width: auto;">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('upload.securities.tblmaster_tmpbid.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="uploadFile" class="form-label">Choose File</label>
                                <input type="file"
                                       class="form-control @error('uploadFile') is-invalid @enderror"
                                       id="uploadFile"
                                       name="uploadFile"
                                       accept=".xlsx,.csv"
                                       required>
                                @error('uploadFile')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Execute Modal -->
        <div class="modal fade" id="executeModal" tabindex="-1" aria-labelledby="executeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="executeModalLabel">Execute Function</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('effective.tblmaster.execute-procedure') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan:</label>
                                <input type="number" name="bulan" id="bulan" class="form-control" min="1" max="12" required>
                            </div>
                            <div class="mb-3">
                                <label for="tahun" class="form-label">Tahun:</label>
                                <input type="number" name="tahun" id="tahun" class="form-control" min="2000" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Execute</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->
        <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Notifikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="notificationMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
    @if(session('status'))
        $('#popupIcon').html('<i class="fas fa-check-circle" style="color: green;"></i>'); // Ikon ceklis
        $('#popupMessage').text("{{ session('status') }}");
        $('#overlay').show(); // Tampilkan overlay
        $('#messagePopup').css({ "display": "block", "opacity": 0 }); // Mulai dengan opacity 0
        setTimeout(function() {
            $('#messagePopup').css("opacity", 1); // Fade in
        }, 10); // Delay sedikit untuk efek fade
    @elseif(session('error'))
        $('#popupIcon').html('<i class="fas fa-times-circle" style="color: red;"></i>'); // Ikon silang
        $('#popupMessage').text("{{ session('error') }}");
        $('#overlay').show(); // Tampilkan overlay
        $('#messagePopup').css({ "display": "block", "opacity": 0 }); // Mulai dengan opacity 0
        setTimeout(function() {
            $('#messagePopup').css("opacity", 1); // Fade in
        }, 10); // Delay sedikit untuk efek fade
    @endif

    // Close popup button
    $('#closePopup, #overlay').click(function() {
        $('#messagePopup').css("opacity", 0); // Fade out
        setTimeout(function() {
            $('#messagePopup').hide(); // Sembunyikan popup setelah fade out
            $('#overlay').hide(); // Sembunyikan overlay
        }, 500); // Delay sesuai durasi animasi
        $('#messagePopup').removeClass('success error'); // Reset class
    });

    @if(session('success'))
        $('#notificationMessage').text("{{ session('success') }}");
        $('#notificationModal').modal('show');
    @elseif(session('error'))
        $('#notificationMessage').text("{{ session('error') }}");
        $('#notificationModal').modal('show');
    @endif

    @if(session('swal'))
        Swal.fire({
            title: "{{ session('swal.title') }}",
            text: "{{ session('swal.text') }}",
            icon: "{{ session('swal.icon') }}",
            confirmButtonText: 'OK'
        });
    @endif
});

function changePerPage() {
        const perPage = document.getElementById('per_page').value;
        window.location.href = `?per_page=${perPage}`; // Redirect dengan parameter per_page
    }
    </script>
</body>
</html>
