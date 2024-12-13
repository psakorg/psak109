<div class="container" style="margin-top: 7rem;">
    <div class="row justify-content-center">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{{ route('superadmin.add.user') }}" class="btn btn-primary btn-md float-end">
                        <i class="fas fa-plus"></i> Tambah User
                    </a>
                </div>

                @if (Session::has('success'))
                    <span class="alert alert-success p-1">{{ Session::get('success') }}</span>
                @endif
                @if (Session::has('fail'))
                    <span class="alert alert-danger p-1">{{ Session::get('fail') }}</span>
                @endif

                <div class="card-body">
                    <table class="table table-sm table-striped table-bordered">
                        <thead class="p-1">
                            <tr>
                                <th>S/N</th>
                                <th>Nama Lengkap</th>
                                <th>Nama PT</th>
                                <th>Alamat PT</th>
                                <th>Nomor Whatsapp</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Company Type</th>
                                <th>Registrations Date</th>
                                <th>Last Updated</th>
                                <th colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!is_null($all_users) && count($all_users) > 0)
                                @foreach ($all_users as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->nama_pt ?? 'N/A' }}</td>
                                        <td>{{ $item->alamat_pt ?? 'N/A' }}</td>
                                        <td>{{ $item->nomor_wa }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->role }}</td>
                                        <td>{{ $item->company_type ?? 'N/A' }}</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>{{ $item->updated_at }}</td>
                                        <td>
                                            <a href="{{ route('superadmin.edit.user', ['user_id' => $item->user_id]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('superadmin.delete.user', ['user_id' => $item->user_id]) }}" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="12">No User Found!</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
