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

    @php
        $belongsToAccounting = false; // Inisialisasi default untuk menghindari undefined variable error

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

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="row mb-3 align-items-center">
                            <!-- Dropdown for Year Selection -->
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

                            <!-- Buttons for Add Planned and Description -->
                            <div class="col-md-8 text-right mt-2 mt-md-auto">
                                @if ($belongsToAccounting)
                                    <a href="/description/{{ $costReview->id }}"
                                        class="btn btn-outline-secondary">Description</a>
                                    <a href="/budget/{{ $costReview->id }}" class="btn btn-outline-primary">Budget Plan</a>
                                @endif
                                <a href="/cost-review/{{ $costReview->id }}/period"
                                    class="btn btn-outline-primary">Recap</a>
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
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-3">
        <div class="container-fluid">
            <div class="card ">
                <div class="card-header">
                    <h3 class="card-title">Summary</h3>
                    <div class="card-tools ml-auto">
                        <a href="/budget/edit/{{ $costReview->id }}/{{ $selectedMonth }}/{{ $selectedYear }}"
                            type="button" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-cyan">
                            <tr class="text-center">
                                <th style="width: 300px;">DESCRIPTION</th>
                                <th>ACTUAL</th>
                                <th>PLAN</th>
                                <th>VAR</th>
                                <th>%</th>
                                <th>REMARK</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @if ($hasDataForSelectedMonth)
                                @php
                                    $currentCategory = null;
                                    $currentSubcategory = null;
                                @endphp

                                @forelse ($descriptions as $description)
                                    @if ($currentCategory !== ($description->subcategory->category->category_name ?? 'N/A'))
                                        <!-- Menampilkan Category -->
                                        @php $currentCategory = $description->subcategory->category->category_name ?? 'N/A'; @endphp
                                        <tr>
                                            <td colspan="6" class="font-weight-bold text-primary">
                                                {{ $currentCategory }}</td>
                                        </tr>
                                    @endif

                                    @if ($currentSubcategory !== ($description->subcategory->sub_category_name ?? 'N/A'))
                                        <!-- Menampilkan Subcategory -->
                                        @php $currentSubcategory = $description->subcategory->sub_category_name ?? 'N/A'; @endphp
                                        <tr>
                                            <td colspan="6" class="font-italic bg-light" style="padding-left: 20px;">
                                                {{ $currentSubcategory }}
                                            </td>
                                        </tr>
                                    @endif

                                    @php
                                        $monthlyBudget = optional($description->monthly_budget)->first();
                                        $plannedBudget = optional($monthlyBudget)->planned_budget ?? 0;

                                        $actualSpent = optional($monthlyBudget->actual ?? collect())->sum(
                                            'actual_spent',
                                        );
                                        $var = $plannedBudget - $actualSpent;
                                        $remarks =
                                            optional($monthlyBudget->actual ?? collect())
                                                ->pluck('remarks')
                                                ->first() ?? '-';
                                        $percentage = $plannedBudget > 0 ? ($actualSpent / $plannedBudget) * 100 : 0;
                                    @endphp

                                    <!-- Menampilkan Description -->
                                    @if ($monthlyBudget == null)
                                        <tr>
                                        @else
                                        <tr onclick="window.location.href='/actual/{{ $monthlyBudget->id }}';"
                                            style="cursor: pointer;">
                                    @endif

                                    <td style="padding-left: 40px;">{{ $description->description_text ?? 'N/A' }}</td>
                                    <td class="text-right">{{ number_format($actualSpent, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($plannedBudget, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($var, 2, ',', '.') }}</td>
                                    <td class="text-center">{{ $percentage }}%</td>
                                    <td>{{ $remarks }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No descriptions available for this Cost
                                            Review.</td>
                                    </tr>
                                @endforelse
                            @else
                                <td colspan="8" class="text-center"> <span>Budget for {{ $selectedMonth }}
                                        {{ $selectedYear }} is not available.</span></td>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- <!-- Main content -->
    <section class="content pb-2">
        <div class="container-fluid">
            <!-- Filter Year -->
            <form method="GET" action="{{ url()->current() }}">
                <div class="row mb-3 align-items-center">
                    <!-- Dropdown for Year Selection -->
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

                    @if ($belongsToAccounting)
                        <!-- Buttons for Add Planned and Description -->
                        <div class="col-md-8 text-right">
                            <a href="/control-budget/{{ $costReview->id }}" class="btn btn-secondary btn-sm">Description</a>
                            <a href="/control-budget/planned_budget/{{ $costReview->id }}"
                                class="btn btn-primary btn-sm">Add
                                Planned</a>
                        </div>
                    @endif
                </div>
            </form>

            <a href="{{ url('/control-budget/year-recap/' . $costReview->id . '/' . $selectedYear) }}">One Year</a>


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
                    <h3 class="card-title">Summary for {{ $selectedMonth }}</h3>
                    @if ($belongsToAccounting)
                        <div class="d-flex justify-content-end mb-3">
                            <a href="/control-budget/individual_update_page/{{ $costReview->id }}/{{ $selectedMonth }}/{{ $selectedYear }}"
                                class="btn btn-primary mx-1 btn-sm @if (!$hasDataForSelectedMonth) disabled-link @endif"
                                @if (!$hasDataForSelectedMonth) tabindex="-1"
                                aria-disabled="true" @endif>
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="card-body table-responsive p-0" style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                    @if ($hasDataForSelectedMonth)
                        <!-- Tampilkan tabel jika ada data -->
                        <table class="table table-hover text-nowrap table-bordered text-sm">
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
                                @php
                                    $totalActualOverall = 0;
                                    $totalPlanOverall = 0;
                                @endphp

                                @foreach ($categories as $category)
                                    @php
                                        $totalActCategory = 0;
                                        $totalPlanCategory = 0;
                                    @endphp
                                    <tr>
                                        <td colspan="6" class="text-left text-primary">
                                            <strong>{{ $category->category_name }}</strong>
                                        </td>
                                    </tr>

                                    @foreach ($category->subcategory as $subCategory)
                                        @php
                                            $totalActSubCategory = 0;
                                            $totalPlanSubCategory = 0;
                                        @endphp
                                        @foreach ($subCategory->descriptions as $description)
                                            @php
                                                $monthlyBudget = optional($description->monthly_budget)->first();
                                                $plannedBudget = optional($monthlyBudget)->planned_budget ?? 0;

                                                $actualSpent = optional($monthlyBudget->actual ?? collect())->sum('actual_spent');
                                                $remarks = optional($monthlyBudget->actual ?? collect())->pluck('remarks')->first() ?? '-';

                                                $variance = $plannedBudget - $actualSpent;
                                                $percentage = $plannedBudget > 0 ? ($actualSpent / $plannedBudget) * 100 : 0;

                                                $totalActSubCategory += $actualSpent;
                                                $totalPlanSubCategory += $plannedBudget;
                                                $totalActCategory += $actualSpent;
                                                $totalPlanCategory += $plannedBudget;

                                                // Add to the overall total
                                                $totalActualOverall += $actualSpent;
                                                $totalPlanOverall += $plannedBudget;
                                            @endphp
                                        @endforeach
                                        <tr class="bg-secondary">
                                            <td class="text-left" style="padding-left: 20px;">
                                                <strong>{{ $subCategory->sub_category_name }}</strong>
                                            </td>
                                            <td>
                                                <div class="rupiah">
                                                    <span class="symbol">Rp</span>
                                                    <span class="amount">
                                                        {{ number_format($totalActSubCategory, 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rupiah">
                                                    <span class="symbol">Rp</span>
                                                    <span class="amount">
                                                        {{ number_format($totalPlanSubCategory, 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rupiah">
                                                    <span class="symbol">Rp</span>
                                                    <span class="amount">
                                                        {{ number_format($totalPlanSubCategory - $totalActSubCategory, 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $totalPlanSubCategory > 0 ? number_format(($totalActSubCategory / $totalPlanSubCategory) * 100, 2) : 0 }}%
                                            </td>
                                            <td></td>
                                        </tr>

                                        @foreach ($subCategory->descriptions as $description)
                                            @php
                                                $monthlyBudget = optional($description->monthly_budget)->first();
                                                $plannedBudget = optional($monthlyBudget)->planned_budget ?? 0;

                                                $actualSpent = optional($monthlyBudget->actual ?? collect())->sum('actual_spent');
                                                $remarks = optional($monthlyBudget->actual ?? collect())->pluck('remarks')->first() ?? '-';

                                                $variance = $plannedBudget - $actualSpent;
                                                $percentage = $plannedBudget > 0 ? ($actualSpent / $plannedBudget) * 100 : 0;
                                            @endphp

                                            <tr onclick="window.location.href='/control-budget/actual/details/{{ $monthlyBudget->id }}';" style="cursor: pointer;">
                                                <td class="text-left" style="padding-left: 40px;">
                                                    {{ $description->description_text }}
                                                </td>
                                                <td>
                                                    <div class="rupiah">
                                                        <span class="symbol">Rp</span>
                                                        <span class="amount">
                                                            {{ number_format($actualSpent, 2, ',', '.') }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="rupiah">
                                                        <span class="symbol">Rp</span>
                                                        <span class="amount">
                                                            {{ number_format($plannedBudget, 2, ',', '.') }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="rupiah">
                                                        <span class="symbol">Rp</span>
                                                        <span class="amount">
                                                            {{ number_format($variance, 2, ',', '.') }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($percentage) }}%</td>
                                                <td>{{ $remarks }}</td>
                                            </tr>
                                        @endforeach
                                        <!-- Pemisah antar subkategori -->
                                        <tr>
                                            <td colspan="6" class="bg-light" style="height: 5px;"></td>
                                        </tr>
                                    @endforeach

                                    <!-- Total Kategori -->
                                    <tr class="bg-secondary">
                                        <td class="text-left"><strong>TOTAL {{ $category->category_name }}</strong></td>
                                        <td>
                                            <div class="rupiah">
                                                <span class="symbol">Rp</span>
                                                <span class="amount">
                                                    {{ number_format($totalActCategory, 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rupiah">
                                                <span class="symbol">Rp</span>
                                                <span class="amount">
                                                    {{ number_format($totalPlanCategory, 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rupiah">
                                                <span class="symbol">Rp</span>
                                                <span class="amount">
                                                    {{ number_format($totalPlanCategory - $totalActCategory, 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ $totalPlanCategory > 0 ? number_format(($totalActCategory / $totalPlanCategory) * 100, 2) : 0 }}%</td>
                                        <td></td>
                                    </tr>

                                    <!-- Pemisah antar kategori -->
                                    <tr>
                                        <td colspan="6" class="bg-light" style="height: 10px;"></td>
                                    </tr>
                                @endforeach

                                <!-- Total Keseluruhan -->
                                <tr class="bg-primary text-white">
                                    <td class="text-left"><strong>TOTAL KESSELURUHAN</strong></td>
                                    <td>
                                        <div class="rupiah">
                                            <span class="symbol">Rp</span>
                                            <span class="amount">
                                                {{ number_format($totalActualOverall, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rupiah">
                                            <span class="symbol">Rp</span>
                                            <span class="amount">
                                                {{ number_format($totalPlanOverall, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rupiah">
                                            <span class="symbol">Rp</span>
                                            <span class="amount">
                                                {{ number_format($totalPlanOverall - $totalActualOverall, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $totalPlanOverall > 0 ? number_format(($totalActualOverall / $totalPlanOverall) * 100, 2) : 0 }}%</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                    @else
                        <!-- Tampilkan pesan jika tidak ada data -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="alert alert-warning text-center w-75" role="alert">
                                <span>Data for {{ $selectedMonth }} {{ $selectedYear }} is not available.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section> --}}
@endsection

@section('styles')
    <style>
        .disabled-link {
            pointer-events: none;
            opacity: 0.5;
        }

        .rupiah {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .rupiah span {
            margin: 0;
        }

        .rupiah .symbol {
            text-align: left;
        }

        .rupiah .amount {
            text-align: right;
            width: 100%;
        }
    </style>
@endsection
