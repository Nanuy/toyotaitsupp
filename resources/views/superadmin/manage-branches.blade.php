@extends('superadmin')

@section('title', 'Manajemen Cabang - Superadmin')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marker-alt mr-2"></i>Manajemen Cabang
        </h1>
        <a href="{{ route('superadmin.manage.categories') }}" class="btn btn-primary">
            <i class="fas fa-tags mr-2"></i>Kelola Kategori
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-map-marker-alt mr-2"></i>Manajemen Cabang
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Form Tambah Cabang -->
                    <form action="{{ route('superadmin.branch.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" name="name" class="form-control" placeholder="Nama cabang baru..." required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <select name="category" class="form-control" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="SJM">SJM</option>
                                    <option value="Non SJM">Non SJM</option>
                                </select>
                                @error('category')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Tambah Cabang
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Daftar Cabang -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Cabang</th>
                                    <th>Kategori</th>
                                    <th>Jumlah Laporan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($locations as $index => $location)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $location->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ ($location->category ?? 'Non SJM') == 'SJM' ? 'primary' : 'secondary' }}">
                                                {{ $location->category ?? 'Non SJM' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ \DB::table('reports')
                                                    ->where('location_id', $location->id)
                                                    ->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn btn-sm btn-warning mr-1" data-toggle="modal" data-target="#editBranchModal{{ $location->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            @if($location->reports()->count() == 0)
                                                <form action="{{ route('superadmin.branch.delete', $location->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus cabang ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Tidak dapat dihapus</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal Edit Cabang -->
                                    <div class="modal fade" id="editBranchModal{{ $location->id }}" tabindex="-1" role="dialog" aria-labelledby="editBranchModalLabel{{ $location->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editBranchModalLabel{{ $location->id }}">Edit Cabang</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('superadmin.branch.update', $location->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="branchName{{ $location->id }}">Nama Cabang</label>
                                                            <input type="text" class="form-control" id="branchName{{ $location->id }}" name="name" value="{{ $location->name }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="branchCategory{{ $location->id }}">Kategori</label>
                                                            <select class="form-control" id="branchCategory{{ $location->id }}" name="category" required>
                                                                <option value="SJM" {{ ($location->category ?? 'Non SJM') == 'SJM' ? 'selected' : '' }}>SJM</option>
                                                                <option value="Non SJM" {{ ($location->category ?? 'Non SJM') == 'Non SJM' ? 'selected' : '' }}>Non SJM</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada cabang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush