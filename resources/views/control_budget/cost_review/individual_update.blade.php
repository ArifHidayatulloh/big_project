@extends('layouts.app')

@section('content')
<section class="content pb-4">
    <div class="container-fluid">
        {{-- {{ route('budget.updateIndividual', ['month' => $month, 'year' => $year]) }} --}}
        <form action="/control-budget/individual_update/{{ $costReview->id }}/{{ date('F', mktime(0, 0, 0, $monthNumber, 10)) }}/{{ $year }}" method="post">
            @csrf

            <!-- Pilihan untuk bulan dan tahun -->
            <section class="content pt-2">
                <div class="card">
                    <div class="card-header">
                        <!-- Input Tahun -->
                        <div class="form-group">
                            <label>Year:</label>
                            <input type="number" class="form-control" name="year" value="{{ $year }}" readonly>
                        </div>

                        <!-- Input Bulan -->
                        <div class="form-group">
                            <label>Month:</label>
                            <input type="text" class="form-control" value="{{ date('F', mktime(0, 0, 0, $monthNumber, 10)) }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Category Block -->
                @forelse($categories as $category)
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ $category->category_name }}</h3>
                            <div class="card-tools">
                                <button class="btn btn-tool" data-toggle="collapse" data-target="#category{{ $category->id }}">
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

                                            <div class="card-body collapse show" id="subcategory{{ $subCategory->id }}">
                                                <ul class="list-group">
                                                    @forelse($subCategory->descriptions as $description)
                                                        <li class="list-group-item">
                                                            <strong>{{ $description->description_text }}</strong>
                                                            <div class="float-right">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <strong class="input-group-text">Rp</strong>
                                                                    </div>
                                                                    @php
                                                                        $plannedBudget = $description->monthly_budget->first()->planned_budget ?? 0;
                                                                    @endphp
                                                                    <input type="text" class="form-control"
                                                                        name="planned_budgets[{{ $description->id }}]"
                                                                        value="{{ old('planned_budgets.' . $description->id, $plannedBudget) }}"
                                                                        placeholder="Enter planned budget"
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
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-m">
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
