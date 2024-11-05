@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Individual Update</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Individual Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content pb-4">
        <div class="container-fluid">
            <form action="" method="post">
                @csrf
                @method('PUT')

                <!-- Select Month and Year -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="form-group">
                            <label for="year">Select Year:</label>
                            <input type="number" class="form-control" name="year" id="year"
                                value="{{ $year }}">
                        </div>
                        <div class="form-group">
                            <label for="month">Select Month:</label>
                            <select name="month" id="month" class="form-control" disabled>
                                <option value="{{ $month }}"> {{ $month }}</option>
                                {{-- @foreach ($months as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $month ? 'selected' : '' }}>
                                        {{ $value }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Category Blocks -->
                @foreach ($categories as $category)
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ $category->category_name }}</h3>
                        </div>
                        <div class="card-body">
                            @foreach ($category->subcategory as $subCategory)
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $subCategory->sub_category_name }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            @foreach ($subCategory->descriptions as $description)
                                                <li class="list-group-item">
                                                    <strong>{{ $description->description_text }}</strong>
                                                    <div class="float-right">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="text" class="form-control"
                                                                name="planned_budgets[{{ $description->id }}]"
                                                                value="{{ old('planned_budgets.' . $description->id, $description->monthlyBudgetPlanned?->planned_budget) }}"
                                                                onkeyup="formatRupiah(this)">
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Save Button -->
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success">
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
