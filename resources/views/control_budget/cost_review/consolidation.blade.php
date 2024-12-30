@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Review</h1>
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

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="">
                        <div>
                            <label for="year">Year</label>
                            <select name="years" id="year" class="form-control">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="months" class="btn-group mt-3 d-flex justify-content-center flex-wrap" role="group"
                            aria-label="Months Navigation">
                            @foreach ($months as $month)
                                <label class="btn btn-outline-primary mx-1 mb-2">
                                    <input type="checkbox" name="months[]" value="{{ $month }}"
                                        {{ in_array($month, $selectedMonths) ? 'checked' : '' }} class="btn-check">
                                    {{ $month }}
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary">Show Recap</button>
                        </div>
                    </form>
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
                        <form action="/export/cost-review-consolidated" method="GET">
                            <input type="hidden" name="years" value="{{ $selectedYear }}">

                            @if (is_array(request('months')))
                                @foreach (request('months') as $month)
                                    <input type="hidden" name="months[]" value="{{ $month }}">
                                @endforeach
                            @else
                                <input type="hidden" name="months[]" value="{{ request('months') }}">
                            @endif

                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-0">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </form>
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
                            @php
                                $currentCategory = null;
                                $currentSubcategory = null;
                                $hasData = false; // Inisialisasi variabel
                                $totalPlannedBudget = 0; // Inisialisasi variabel
                                $totalActualSpent = 0; // Inisialisasi variabel
                            @endphp

                            @foreach ($description_groups as $description)
                                @if ($description['has_monthly_budget'])
                                    @php
                                        $hasData = true;
                                    @endphp

                                    @if ($currentCategory !== $description['category'])
                                        @php
                                            $currentCategory = $description['category'];
                                        @endphp
                                        <tr>
                                            <td colspan="6" class="font-weight-bold text-primary">
                                                {{ $currentCategory }}
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($currentSubcategory !== $description['subcategory'])
                                        @php
                                            $currentSubcategory = $description['subcategory'];

                                            // Ambil semua description_group_id untuk subkategori ini
                                            $descriptionGroups = \App\Models\BudgetDescriptionGrouping::where(
                                                'sub_category_id',
                                                $description['subcategory_id'],
                                            )->pluck('id');

                                            // Ambil semua description yang terkait dengan description_group_id
                                            $desc = \App\Models\BudgetDescription::whereIn(
                                                'description_grouping_id',
                                                $descriptionGroups,
                                            )->get();

                                            // Menghitung total planned budget berdasarkan filter tahun dan bulan
                                            $totalPlannSubcategory = $desc
                                                ? $desc
                                                    ->flatMap(function ($item) use ($selectedYear, $selectedMonths) {
                                                        return $item->monthly_budget
                                                            ->where('year', $selectedYear)
                                                            ->whereIn('month', $selectedMonths);
                                                    })
                                                    ->sum('planned_budget')
                                                : 0;

                                            // Menghitung total actual budget berdasarkan filter tahun dan bulan
                                            $totalActSubcategory = $desc
                                                ? $desc
                                                    ->flatMap(function ($item) use ($selectedYear, $selectedMonths) {
                                                        return $item->monthly_budget
                                                            ->where('year', $selectedYear)
                                                            ->whereIn('month', $selectedMonths)
                                                            ->flatMap(function ($monthlyBudget) {
                                                                return $monthlyBudget->actual; // Mengakses relasi actual
                                                            });
                                                    })
                                                    ->sum('actual_spent') // Sum nilai actual_spent dari actual
                                                : 0;

                                            $variance = $totalPlannSubcategory - $totalActSubcategory;
                                            $percentage =
                                                $totalPlannSubcategory > 0
                                                    ? ($totalActSubcategory / $totalPlannSubcategory) * 100
                                                    : 0;

                                            // Tambahkan ke total keseluruhan
                                            $totalPlannedBudget += $totalPlannSubcategory;
                                            $totalActualSpent += $totalActSubcategory;
                                            $totalVar = $totalPlannedBudget - $totalActualSpent;
                                            $totalPercentage =
                                                $totalPlannedBudget > 0
                                                    ? ($totalActualSpent / $totalPlannedBudget) * 100
                                                    : 0;
                                        @endphp

                                        <tr class="bg-light">
                                            <td class="font-italic  style="padding-left: 20px;">
                                                {{ $currentSubcategory }}
                                            </td>
                                            <td class="text-right">{{ number_format($totalActSubcategory, 2, ',', '.') }}
                                            </td>
                                            <td class="text-right">{{ number_format($totalPlannSubcategory, 2, ',', '.') }}
                                            </td>
                                            <td class="text-right">{{ number_format($variance, 2, ',', '.') }}</td>
                                            <td class="text-center">{{ number_format($percentage, 2) }}%</td>
                                            <td></td>
                                        </tr>
                                    @endif


                                    <!-- Menampilkan Description -->
                                    <tr>
                                        <td style="padding-left: 40px;">{{ $description['description_group'] ?? 'N/A' }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($description['total_actual_spent'], 2, ',', '.') }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($description['total_planned_budget'], 2, ',', '.') }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($description['variance'], 2, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($description['percentage'], 2) }}%
                                        </td>
                                        <td>{{ $description['remarks'] }}</td>
                                    </tr>
                                @endif
                            @endforeach

                            @if (!$hasData)
                                <tr>
                                    <td colspan="6" class="text-center text-danger font-weight-bold">
                                        No data available for monthly budget.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-info">
                            <tr>
                                <td class="text-left font-weight-bold">Total</td>
                                <td class="text-right font-weight-bold">
                                    {{ number_format($totalActualSpent, 2, ',', '.') }}
                                </td>
                                <td class="text-right font-weight-bold">
                                    {{ number_format($totalPlannedBudget, 2, ',', '.') }}
                                </td>
                                <td class="text-right font-weight-bold">{{ number_format($totalVar, 2, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($totalPercentage, 2) }}%</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
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
