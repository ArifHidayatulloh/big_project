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
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i></a></li>
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
                            <!-- Dropdown untuk Pilihan Tahun -->
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

                            <!-- Tombol Navigasi -->
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

                    <!-- Pagination untuk Bulan -->
                    <div class="btn-group mt-3 d-flex justify-content-center pb-3 flex-wrap" role="group">
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Summary</h3>
                    @if ($belongsToAccounting)
                        <div class="card-tools ml-auto">
                            <a href="/budget/edit/{{ $costReview->id }}/{{ $selectedMonth }}/{{ $selectedYear }}"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    @endif
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
                                    $totalOverallPlannedBudget = 0;
                                    $totalOverallActualBudget = 0;
                                    $totalOverallVar = 0;
                                    $totalOverallPercentage = 0;
                                @endphp
                                @php $currentCategory = $currentSubcategory = null; @endphp

                                @foreach ($descriptions as $description)
                                    @if ($currentCategory !== ($description->subcategory->category->category_name ?? 'N/A'))
                                        @php $currentCategory = $description->subcategory->category->category_name ?? 'N/A'; @endphp
                                        <tr>
                                            <td colspan="6" class="font-weight-bold text-primary">{{ $currentCategory }}
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($currentSubcategory !== ($description->subcategory->sub_category_name ?? 'N/A'))
                                        @php
                                            $currentSubcategory = $description->subcategory->sub_category_name ?? 'N/A';
                                            $subCategoryDescriptions = $descriptions->where(
                                                'subcategory.sub_category_name',
                                                $currentSubcategory,
                                            );

                                            // Total planned dan actual untuk subcategory dengan validasi tambahan
                                            $totalPlanned = $subCategoryDescriptions->sum(function ($desc) {
                                                return $desc->monthly_budget
                                                    ? $desc->monthly_budget->sum('planned_budget')
                                                    : 0;
                                            });

                                            $totalActual = $subCategoryDescriptions->sum(function ($desc) {
                                                return $desc->monthly_budget
                                                    ? $desc->monthly_budget->sum(function ($budget) {
                                                        return $budget->actual
                                                            ? $budget->actual->sum('actual_spent')
                                                            : 0;
                                                    })
                                                    : 0;
                                            });

                                            $varSubcategory = $totalPlanned - $totalActual;
                                            $percentageSubcategory =
                                                $totalPlanned > 0 ? ($totalActual / $totalPlanned) * 100 : 0;

                                            $totalOverallPlannedBudget += $totalPlanned;
                                            $totalOverallActualBudget += $totalActual;
                                            $totalOverallVar = $totalOverallPlannedBudget - $totalOverallActualBudget;
                                            $totalOverallPercentage =
                                                $totalOverallPlannedBudget > 0
                                                    ? ($totalOverallActualBudget / $totalOverallPlannedBudget) * 100
                                                    : 0;
                                        @endphp
                                        <tr class="bg-light">
                                            <td class="font-italic" style="padding-left: 20px;">
                                                {{ $currentSubcategory }}</td>
                                            <td class="text-right">{{ number_format($totalActual, 2, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($totalPlanned, 2, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($varSubcategory, 2, ',', '.') }}</td>
                                            <td class="text-center">{{ number_format($percentageSubcategory, 2) }}%</td>
                                            <td></td>
                                        </tr>
                                    @endif

                                    @php
                                        $monthlyBudget = $description->monthly_budget->first();
                                        $planned = $monthlyBudget?->planned_budget ?? 0;
                                        $actual = optional($monthlyBudget?->actual)->sum('actual_spent') ?? 0;
                                        $var = $planned - $actual;
                                        $percentage = $planned > 0 ? ($actual / $planned) * 100 : 0;
                                        $remarks = optional($monthlyBudget?->actual->first())->remarks ?? '-';
                                    @endphp

                                    <tr
                                        @if ($monthlyBudget) onclick="window.location.href='/actual/{{ $monthlyBudget->id }}';"
                                        style="cursor: pointer;" @endif>
                                        <td style="padding-left: 40px;">{{ $description->description_text ?? 'N/A' }}</td>
                                        <td class="text-right">{{ number_format($actual, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($planned, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($var, 2, ',', '.') }}</td>
                                        <td class="text-center">{{ number_format($percentage, 2) }}%</td>
                                        <td>{{ $remarks }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No data available for {{ $selectedMonth }}
                                        {{ $selectedYear }}.</td>
                                </tr>
                            @endif
                        </tbody>
                        @if (isset($totalOverallActualBudget) && isset($totalOverallPlannedBudget))
                            <tfoot class="bg-info">
                                <tr>
                                    <td class="text-left font-weight-bold">Total</td>
                                    <td class="text-right font-weight-bold">
                                        {{ number_format($totalOverallActualBudget, 2, ',', '.') }}
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ number_format($totalOverallPlannedBudget, 2, ',', '.') }}
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ number_format($totalOverallVar, 2, ',', '.') }}</td>
                                    <td class="text-center">{{ number_format($totalOverallPercentage, 2) }}%</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                    </table>
                </div>
            </div>
            <!-- Back to previous page -->
            <a href="{{ url('/cost-review/') }}" class="btn btn-outline-secondary mt-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </section>
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
