@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $costReview->review_name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cost Review</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content pb-3">
        <!-- Filter Year -->
        <form method="GET" action="{{ url()->current() }}">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="year">Select Year</label>
                    <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
        <!-- Paginate Bulan -->
        <div class="btn-group mt-3 d-flex justify-content-center pb-3 flex-wrap" role="group"
            aria-label="Months Navigation">
            @foreach ($months as $month)
                <a href="{{ request()->fullUrlWithQuery(['month' => $month]) }}"
                    class="btn {{ $month == $selectedMonth ? 'btn-primary' : 'btn-outline-primary' }} mx-1 mb-2">
                    {{ $month }}
                </a>
            @endforeach
        </div>
        <div class="card">
            <!-- Tabel terpisah dari card header -->
            <div class="card-body table-responsive p-0"
                style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-radius: 10px;">
                <table class="table table-bordered">
                    <thead class="bg-primary text-center">
                        <tr>
                            <th>Description</th>
                            <th>PLAN</th>
                            <th>ACTUAL</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <!-- Category Row -->
                            <tr>
                                <td colspan="4" class="bg-light font-weight-bold">
                                    {{ $category->category_name }}
                                </td>
                            </tr>
                            @if ($category->subcategory->isNotEmpty())
                                @foreach ($category->subcategory as $subCategory)
                                    <!-- Subcategory Row -->
                                    <tr>
                                        <td colspan="4" class="pl-4 bg-secondary text-white">
                                            {{ $subCategory->sub_category_name }}
                                        </td>
                                    </tr>
                                    @if ($subCategory->descriptions->isNotEmpty())
                                        @foreach ($subCategory->descriptions as $description)
                                            @if ($description->monthly_budget->isNotEmpty())
                                                @foreach ($description->monthly_budget as $monthlyBudget)
                                                    <!-- Description Row -->
                                                    <tr>
                                                        <td class="pl-5">
                                                            {{ $description->description_text }}
                                                        </td>
                                                        <td>
                                                            Rp
                                                            {{ number_format($monthlyBudget->planned_budget, 0, ',', '.') }}
                                                        </td>
                                                        <td>
                                                            Rp
                                                            {{ number_format($monthlyBudget->actual->sum('actual_spent'), 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{-- {{ route('actuals.details', ['id' => $monthlyBudget->id]) }} --}}
                                                            <a href="/control-budget/actual/details/{{ $monthlyBudget->id }}"
                                                                class="btn btn-primary btn-sm">
                                                                Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="pl-5" colspan="4">
                                                        No monthly budgets available for this description.
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="pl-4">No descriptions available for this
                                                subcategory.</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No subcategories available for this category.</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
