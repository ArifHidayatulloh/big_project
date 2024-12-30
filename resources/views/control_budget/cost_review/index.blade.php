@extends('layouts.app') {{-- Pastikan untuk mengganti dengan layout yang sesuai di proyekmu --}}

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cost Review</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item active">Cost Review</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content pb-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-dark">
                <h5 class="card-title mb-0 text-white">Manage Cost Reviews</h5>
                @php
                    $belongsToAccounting = false; // Inisialisasi default

                    // Pastikan hanya role 3 yang melakukan pencarian ini
                    if (auth()->user()->role == 3) {
                        // Ambil unit ID dari "ACCOUNTING"
                        $accountingUnit = \App\Models\Unit::where('name', 'ACCOUNTING')->first();

                        if ($accountingUnit) {
                            // Cek apakah user yang login terdaftar di DepartmenUser dengan unit "ACCOUNTING"
                            $belongsToAccounting = \App\Models\DepartmenUser::where('user_id', auth()->user()->id)
                                ->where('unit_id', $accountingUnit->id)
                                ->exists();
                        }
                    }
                @endphp
                @if ($belongsToAccounting)
                    <div class="ml-auto">
                        <button class="btn btn-outline-light " data-toggle="modal" data-target="#createModal">
                            <i class="fas fa-plus"></i> Cost Review
                        </button>
                        <a href="/category" class="btn btn-outline-light">
                            <i class="fas fa-plus"></i> Category
                        </a>
                    </div>
                @endif
            </div>

            <div class="card-body shadow-lg">
                <div class="row">
                    @forelse ($costReviews as $index => $costReview)
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">{{ $costReview->unit->name }}</h5>
                                    <p class="card-text text-muted">{{ $costReview->review_name }}</p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-info">{{ $costReview->unit->code }}</span>
                                        <a href="/cost-review/{{ $costReview->id }}" class="btn btn-sm btn-outline-primary">
                                            More Info <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                @php
                                    $belongsToAccounting = false; // Inisialisasi default

                                    // Pastikan hanya role 3 yang melakukan pencarian ini
                                    if (auth()->user()->role == 3) {
                                        // Ambil unit ID dari "ACCOUNTING"
                                        $accountingUnit = \App\Models\Unit::where('name', 'ACCOUNTING')->first();

                                        if ($accountingUnit) {
                                            // Cek apakah user yang login terdaftar di DepartmenUser dengan unit "ACCOUNTING"
                                            $belongsToAccounting = \App\Models\DepartmenUser::where(
                                                'user_id',
                                                auth()->user()->id,
                                            )
                                                ->where('unit_id', $accountingUnit->id)
                                                ->exists();
                                        }
                                    }
                                @endphp
                                @if ($belongsToAccounting)
                                    <div class="card-footer d-flex">
                                        <button class="btn btn-outline-warning btn-sm mr-2" data-toggle="modal"
                                            data-target="#editModal{{ $costReview->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/cost-review/destroy-cost-review/{{ $costReview->id }}"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center">No cost reviews available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    @foreach ($costReviews as $costReview)
        <!-- Modal for Edit Cost Review -->
        <div class="modal fade" id="editModal{{ $costReview->id }}" tabindex="-1"
            aria-labelledby="editModalLabel{{ $costReview->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $costReview->id }}">Edit Cost Review</h5>
                        <button type="button" class="btn-close bg-transparent border-0 text-lg" data-dismiss="modal"
                            aria-label="Close">&times;</button>
                    </div>
                    <form action="/cost-review/update-cost-review/{{ $costReview->id }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="unit_id">Unit</label>
                                <select name="unit_id" id="unit_id" class="form-control" required>
                                    <option value="" disabled>Select Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ $costReview->unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="review_name">Review Name</label>
                                <input type="text" name="review_name" id="review_name" class="form-control"
                                    value="{{ $costReview->review_name }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal for Create Cost Review -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">New Cost Review</h5>
                    <button type="button" class="btn-close bg-transparent border-0" data-dismiss="modal"
                        aria-label="Close">&times;</button>
                </div>
                <form action="/cost-review/store-cost-review" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="unit_id">Unit</label>
                            <select name="unit_id" id="unit_id" class="form-control" required>
                                <option value="" disabled selected>Select Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="review_name">Review Name</label>
                            <input type="text" name="review_name" id="review_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
