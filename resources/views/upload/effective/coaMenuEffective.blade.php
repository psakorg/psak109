<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data CoA Effective</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background-color: #f4f7fc;
      justify-content: center; /* Center horizontally */
      align-items: center; /* Center vertically */
      left: 0;        /* Align it to the right */
      top: 0;          /* Start from the top */
      height: 100%;  
    }
    .section-header {
      text-align: center;
      margin-top: 20px; /* Adjusted for better vertical centering */
      margin-bottom: 30px;
    }
    h1 {
      font-size: 25px;
      font-weight: bold;
      color: #007bff;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      margin-top: 20px;
    }
    .table th{
      vertical-align: middle;
      padding: 5px;
      background-color: #007bff;
      color: white;
    }
    .table td {
      vertical-align: middle;
      padding: 10px;
    }
    .table-hover tbody tr:hover {
      background-color: #e1f5fe;
    }

    .container {
        overflow: visible !important;
        max-width: 1200px; /* Optional: Limit the maximum width */
        margin: 0 auto; /* Center the container */
        padding: 20px; /* Add padding */
    }

    .section {
        overflow: visible !important;
    }

    .table-responsive {
    overflow-x: auto; 
}
/* Style select elements */
select {
    width: 10%; 
    padding: 5px;
    border: 1px solid #ccc; 
    border-radius: 4px;
    background-color: #fff; 
    font-size: 12px; 
    outline: none; 
}

select:focus {
    border-color: #007bff; 
    box-shadow: 0 0 4px #007bff; 
}
label[for="interface"] {
    background-color: #e0a800;
    width: 10%; 
    padding: 5px;
    border: 1px solid #ccc; 
    border-radius: 4px;
    font-size: 14px; 
    outline: #000000; 
    text-align: center;
}
label[for="coa"] {
    background-color: #218838;
    width: 10%; 
    padding: 5px;
    border: 1px solid #ccc; 
    border-radius: 4px;
    font-size: 14px; 
    outline: #000000; 
    text-align: center;
}
label[for="group"] {
    background-color: #0056b3;
    width: 10%; 
    padding: 5px;
    border: 1px solid #ccc; 
    border-radius: 4px;
    font-size: 14px; 
    outline: #000000; 
    text-align: center;
}
.footer{
    left: 0;
}

  </style>
</head>
<body>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-wrapper">
        <div class="container mt-5">
            <div class="section-header">
                <h1>Data Table CoA</h1>
            </div>
<div class="relative flex items-center justify-between gap-2"> 
<form action="{{ route('coaEffective.index') }}" method="GET" id="filterForm">
<!-- SELECT INTERFACE -->
        <label for="interface" style="width: 100px">Interface</label>
        <select name="interface" id="interface" style="width: 120px;" required>
        <option value="">Select Interface</option>
        @foreach($loans->unique('interface') as $interfaceOption)
            <option value="{{ $interfaceOption->interface }}" 
                {{ request('interface') == $interfaceOption ? 'selected' : '' }}>
                {{ $interfaceOption->interface }}
            </option>
        @endforeach
        </select>
<!-- SELECT COA -->
        <label for="coa" style="width: 100px">CoA</label>
        <select name="coa" id="coa"  style="width: 120px;" required>
        <option value="">Select CoA</option>
        @foreach($loans->unique('coa') as $coaOption)
            <option value="{{ $coaOption->coa }}" 
                {{ request('coa') == $coaOption ? 'selected' : '' }}>
                {{ $coaOption->coa }}
            </option>
        @endforeach
        </select>
<!-- SELECT GROUP -->
        <label for="group" style="width: 100px">Group</label>
        <select name="group" id="group"  style="width: 120px;" required>
        <option value="">Select Group</option>
        @foreach($loans->unique('GROUP') as $groupOption)
            <option value="{{ $groupOption->GROUP }}" 
                {{ request('group') == $groupOption ? 'selected' : '' }}>
                {{ $groupOption->GROUP }}
            </option>
        @endforeach
        </select>
</form>
<!-- Download Excel -->
        <div class="flex justify-end mb-3 gap-2" style="float: right;">
        <a href="#" id="exportExcel" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export to Excel
        </a>
        </div>
</div>
            <!-- Data Table -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">tblcoaloancorporate</h6>
                </div>
                <div class="table-responsive p-3" style="font-size: 12px;">
                    <table class="table table-hover table-bordered text-center">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Interface</th>
                                <th>CoA</th>
                                <th>Group</th>
                                <th>Post</th>
                                <th>Description</th>
                                <th>Event</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($loans) > 0)
                        @php
                        $nourut = ($loans->currentPage() - 1) * $loans->perPage();
                        @endphp
                            @foreach($loans as $loan)
                            @php
                            $nourut ++;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $nourut }}</td>
                                <td class="text-center">{{ $loan->interface }}</td>
                                <td class="text-center">{{ $loan->coa }}</td>
                                <td class="text-center">{{ $loan->GROUP }}</td>
                                <td class="text-center">{{ $loan->mut }}</td>
                                <td class="text-left">{{ $loan->keterangan }}</td>
                                <td class="text-left">{{ $loan->EVENT }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            @if(empty($loans))
            <div class="alert alert-warning text-center mt-3">Data not found</div>
            @endif
            <!-- Pagination Links -->
            <div class="ms-3 mt-4">
                        {{ $loans->links() }}
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    <script>
      $(document).ready(function() {
        @if(session('success'))
          $('#notificationMessage').text("{{ session('success') }}");
          $('#notificationModal').modal('show');
        @elseif(session('error'))
          $('#notificationMessage').text("{{ session('error') }}");
          $('#notificationModal').modal('show');
        @endif
      });

      document.addEventListener('DOMContentLoaded', function() {
      // Set nilai default dari parameter URL atau data yang dikirim dari controller
      const selectedINTERFACE = "{{ $interface }}";
      const selectedCOA = "{{ $coa }}";
      const selectedGROUP = "{{ $group }}";
    
    document.getElementById('interface').value = selectedINTERFACE || '';
    document.getElementById('coa').value = selectedCOA || '';
    document.getElementById('group').value = selectedGROUP || '';
});

// Event listener untuk perubahan interface, coa, dan group
document.getElementById('interface').addEventListener('change', updateReport);
document.getElementById('coa').addEventListener('change', updateReport);
document.getElementById('group').addEventListener('change', updateReport);

function updateReport() {
    const interface = document.getElementById('interface').value;
    const coa = document.getElementById('coa').value;
    const group = document.getElementById('group').value;
    const id_pt = "{{ Auth::user()->id_pt ?? '' }}";
    
    let reportUrl = `/CoA-menu-effective`;
    
     // Build query parameters dynamically
     const params = new URLSearchParams();
    if (interface) params.append('interface', interface);
    if (coa) params.append('coa', coa);
    if (group) params.append('group', group);

    // Redirect with query parameters
    window.location.href = `${reportUrl}?${params.toString()}`;
}
 // Update export URL dynamically based on selected interface, coa, and group
 document.getElementById('exportExcel').addEventListener('click', function (e) {
        e.preventDefault();
        const interface = document.getElementById('interface').value;
        const coa = document.getElementById('coa').value;
        const group = document.getElementById('group').value;
        const id_pt = "{{ Auth::user()->id_pt ?? '' }}";

        // Redirect to the export route with query parameters
        window.location.href = `{{ route('coaEffective.downloadExcel', ['id_pt' => Auth::user()->id_pt]) }}?interface=${interface}&coa=${coa}&group=${group}`;
    });
    </script>
</body>
</html>