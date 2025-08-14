@extends('superadmin')

@section('title', 'Manajemen Kategori - Superadmin')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tags mr-2"></i>Manajemen Kategori
        </h1>
        <a href="{{ route('superadmin.manage.branches') }}" class="btn btn-success">
            <i class="fas fa-map-marker-alt mr-2"></i>Kelola Cabang
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
                <div class="card-header py-3 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-tags mr-2"></i>Manajemen Kategori
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Form Tambah Kategori -->
                    <form action="{{ route('superadmin.category.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Nama kategori baru..." required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </form>

                    <!-- Daftar Kategori -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Jumlah Laporan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ \DB::table('report_details')
                                                    ->join('reports', 'report_details.report_id', '=', 'reports.id')
                                                    ->where('report_details.item_id', $item->id)
                                                    ->distinct('reports.id')
                                                    ->count('reports.id') }}
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn btn-sm btn-warning mr-1" data-toggle="modal" data-target="#editCategoryModal{{ $item->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            @if($item->reports()->count() == 0)
                                                <form action="{{ route('superadmin.category.delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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

                                    <!-- Modal Edit Kategori -->
                                    <div class="modal fade" id="editCategoryModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editCategoryModalLabel{{ $item->id }}">Edit Kategori</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('superadmin.category.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="categoryName{{ $item->id }}">Nama Kategori</label>
                                                            <input type="text" class="form-control" id="categoryName{{ $item->id }}" name="name" value="{{ $item->name }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada kategori.</td>
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