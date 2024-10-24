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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cost Review</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i>
                    <b>Cost Review</b></button>
                <div class="card-tools mt-2 mr-1">
                    <form action="/user">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="search" class="form-control float-right" placeholder="Search..." name="search">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="row">
            @forelse ($costReviews as $index => $costReview)
                <div class="col-lg-4 col-12">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $costReview->unit->name }}</h3>
                            <p>{{ $costReview->review_name }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-briefcase"></i>
                        </div>

                        @if (auth()->user()->role == 2)
                            <div class="d-flex pl-2 pb-2" style="gap:5px;">
                                <!-- Edit Button -->
                                <a href="javascript:void(0)" data-toggle="modal" class="btn btn-outline-warning btn-sm shadow-sm"
                                    data-target="#editModal{{ $costReview->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <!-- Delete Button -->
                                <a href="/control-budget/destroy_cost_review/{{ $costReview->id }}" class="btn btn-outline-danger btn-sm shadow-sm" onclick="return confirm('Are you sure you want to delete this item?')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </div>
                        @endif

                        <a href="/control-budget/{{ $costReview->id }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            @empty
                <p>No cost reviews available</p>
            @endforelse

        </div>

        @foreach ($costReviews as $costReview)
            <!-- Modal for Edit Cost Review -->
            <div class="modal fade" id="editModal{{ $costReview->id }}" tabindex="-1" role="dialog"
                aria-labelledby="editModalLabel{{ $costReview->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $costReview->id }}">Edit Cost Review</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="/control-budget/update_cost_review/{{ $costReview->id }}" method="POST">
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
        <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">New Cost Review</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/control-budget/store_cost_review" method="POST">
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
    </section>
@endsection
