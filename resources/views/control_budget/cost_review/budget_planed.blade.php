@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $costReview->review_name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Plan Budgets</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content pb-4">
        <div class="container-fluid">
            <form action="/control-budget/budget_plan_add" method="post">
                @csrf
                <!-- Pilihan untuk bulan menggunakan checkbox -->
                <section class="content pt-2">
                    <div class="card">
                        <div class="card-header">
                            <!-- Input Tahun -->
                            <div class="form-group">
                                <label>Select Year:</label>
                                <input type="number" class="form-control" name="year" placeholder="Enter year"
                                    value="{{ date('Y') }}" min="1">
                            </div>

                            <!-- Input Bulan -->
                            <div class="form-group">
                                <label>Select Months:</label>
                                <div class="row">
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                                        <div class="col-md-4"> <!-- Menggunakan 4 untuk membagi menjadi 3 kolom -->
                                            <div class="icheck-primary d-inline">
                                                <input class="form-check-input" type="checkbox" name="months[]"
                                                    value="{{ $month }}" id="month_{{ $month }}">
                                                <label class="form-check-label" for="month_{{ $month }}">
                                                    {{ $month }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Block -->
                    @forelse($categories as $category)
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ $category->category_name }}</h3>
                                <div class="card-tools">
                                    <button class="btn btn-tool" data-toggle="collapse"
                                        data-target="#category{{ $category->id }}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body collapse show" id="category{{ $category->id }}">
                                <div class="row">
                                    @forelse($category->subcategory as $subCategory)
                                        <div class="col-md-12">
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ $subCategory->sub_category_name }}</h3>
                                                </div>

                                                <div class="card-body collapse show"
                                                    id="subcategory{{ $subCategory->id }}">
                                                    <ul class="list-group">
                                                        @forelse($subCategory->descriptions as $description)
                                                            <li class="list-group-item">
                                                                <strong>{{ $description->description_text }}</strong>
                                                                <div class="float-right">
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <strong class="input-group-text">
                                                                                Rp
                                                                            </strong>
                                                                        </div>
                                                                        <input type="text" class="form-control"
                                                                            name="planned_budgets[{{ $description->id }}]"
                                                                            placeholder="Enter planned budget" min="0"
                                                                            onkeyup="formatRupiah(this)">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @empty
                                                            <li class="list-group-item">No descriptions found</li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p>No subcategories found</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No categories found</p>
                    @endforelse

                    <!-- Buttons Section -->
                    <div class="d-flex justify-content-between mt-4 align-items-center">
                        <a href="/control-budget/{{ $costReview->id }}" class="btn btn-secondary btn-m">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-m">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function formatRupiah(input) {
            // Menghapus karakter yang bukan angka
            let value = input.value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let rupiah = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            if (split[1]) {
                rupiah += ',' + split[1];
            }

            input.value = rupiah;
        }
    </script>
@endsection
