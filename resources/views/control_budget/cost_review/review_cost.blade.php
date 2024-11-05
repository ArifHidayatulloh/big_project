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

    <!-- Main content -->
    <section class="content pb-2">
        <div class="container-fluid">

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

            <!-- Cost Review Table -->
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-header">
                    <h3 class="card-title">Cost Review Summary</h3>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="/control-budget/individual_update_page/{{ $selectedMonth }}/{{ $selectedYear }}"
                            class="btn btn-primary mx-1">Individual Update</a>
                        <a href="" class="btn btn-secondary mx-1">Mass Update</a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead style="background: linear-gradient(to right, #007bff, #00c6ff); color: white;">
                            <tr class="text-center">
                                <th>DESCRIPTION</th>
                                <th>ACTUAL</th>
                                <th>PLAN</th>
                                <th>VAR</th>
                                <th>PERCENTAGE</th>
                                <th>REMARKS</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($categories as $category)
                                @php
                                    $totalActCategory = 0;
                                    $totalPlanCategory = 0;
                                @endphp

                                <tr class="bg-light">
                                    <td colspan="6" class="text-left"><strong>{{ $category->category_name }}</strong>
                                    </td>
                                </tr>

                                @foreach ($category->subcategory as $subCategory)
                                    @php
                                        $totalActSubCategory = 0;
                                        $totalPlanSubCategory = 0;
                                    @endphp

                                    <tr style="background-color: #d1ecf1; color: #0c5460;">
                                        <td colspan="6" class="text-left" style="padding-left: 20px;">
                                            <strong>{{ $subCategory->sub_category_name }}</strong></td>
                                    </tr>

                                    @foreach ($subCategory->descriptions as $description)
                                        @php
                                            $monthlyBudget = optional($description->monthly_budget)->first();
                                            $plannedBudget = optional($monthlyBudget)->planned_budget ?? 0;

                                            $actualData = optional($monthlyBudget->actual ?? collect())->first();
                                            $actualSpent = optional($actualData)->actual_spent ?? 0;
                                            $remarks = optional($actualData)->remarks ?? '-';

                                            $variance = $plannedBudget - $actualSpent;
                                            $percentage =
                                                $plannedBudget > 0 ? ($actualSpent / $plannedBudget) * 100 : 0;

                                            $totalActSubCategory += $actualSpent;
                                            $totalPlanSubCategory += $plannedBudget;
                                            $totalActCategory += $actualSpent;
                                            $totalPlanCategory += $plannedBudget;
                                        @endphp

                                        <tr>
                                            <td class="text-left" style="padding-left: 40px;">
                                                {{ $description->description_text }}</td>
                                            <td>Rp {{ number_format($actualSpent, 2, ',', '.') }}</td>
                                            <td>Rp {{ number_format($plannedBudget, 2, ',', '.') }}</td>
                                            <td>Rp {{ number_format($variance, 2, ',', '.') }}</td>
                                            <td>{{ number_format($percentage, 2) }}%</td>
                                            <td>{{ $remarks }}</td>
                                        </tr>
                                    @endforeach

                                    <!-- Total Subkategori -->
                                    <tr style="background-color: #c8e6c9; color: #256029;">
                                        <td class="text-left"><strong>TOTAL {{ $subCategory->sub_category_name }}</strong>
                                        </td>
                                        <td>Rp {{ number_format($totalActSubCategory, 2, ',', '.') }}</td>
                                        <td>Rp {{ number_format($totalPlanSubCategory, 2, ',', '.') }}</td>
                                        <td>Rp
                                            {{ number_format($totalPlanSubCategory - $totalActSubCategory, 2, ',', '.') }}
                                        </td>
                                        <td>{{ $totalPlanSubCategory > 0 ? number_format(($totalActSubCategory / $totalPlanSubCategory) * 100, 2) : 0 }}%
                                        </td>
                                        <td></td>
                                    </tr>

                                    <!-- Pemisah antar subkategori -->
                                    <tr>
                                        <td colspan="6" class="bg-light" style="height: 5px;"></td>
                                    </tr>
                                @endforeach

                                <!-- Total Kategori -->
                                <tr style="background-color: #f8bbd0; color: #880e4f;">
                                    <td class="text-left"><strong>TOTAL {{ $category->category_name }}</strong></td>
                                    <td>Rp {{ number_format($totalActCategory, 2, ',', '.') }}</td>
                                    <td>Rp {{ number_format($totalPlanCategory, 2, ',', '.') }}</td>
                                    <td>Rp {{ number_format($totalPlanCategory - $totalActCategory, 2, ',', '.') }}</td>
                                    <td>{{ $totalPlanCategory > 0 ? number_format(($totalActCategory / $totalPlanCategory) * 100, 2) : 0 }}%
                                    </td>
                                    <td></td>
                                </tr>

                                <!-- Pemisah antar kategori -->
                                <tr>
                                    <td colspan="6" class="bg-light" style="height: 10px;"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
