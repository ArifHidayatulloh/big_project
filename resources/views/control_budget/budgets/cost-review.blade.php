@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cost Review for {{ $unit->name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/control-budget">Control Budget</a></li>
                        @if (Auth::user()->role == 4 || Auth::user()->role == 5)
                            <li class="breadcrumb-item"><a href="/control-budget/unit/{{ $unit->id }}">Budget
                                    Categories</a>
                        @endif
                        </li>
                        <li class="breadcrumb-item active">Monthly Budget Overview</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="container">
                <!-- Form untuk memilih bulan dan tahun -->
                <form action="/control-budget/cost-review/{{ $unit->id }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="year">Year</label>
                            <input type="number" name="year" value="{{ old('year', $year) }}" class="form-control"
                                placeholder="Enter year" min="2020" required>
                        </div>
                        <div class="col-md-6">
                            <label for="month">Month</label>
                            <select name="month" class="form-control" required>
                                <option value="" disabled>Select Month</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-secondary mt-3">Show Budget</button>
                </form>

                <!-- Tabel untuk menampilkan budget -->
                @if ($year && $month)
                    @if ($groupedBudgets->isEmpty())
                        <p>No budget data available for the selected year and month.</p>
                    @else
                        <div class="text-right mb-2">
                            <form action="/control-budget/monthly-budget/{{ $unit->id }}/edit">
                                <input type="number" name="year" value="{{ old('year', $year) }}" class="form-control"
                                    placeholder="Enter year" min="2020" required hidden>
                                <select name="month" class="form-control" required hidden>
                                    <option value="" disabled>Select Month</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>

                                <button type="submit" class="btn btn-warning">Edit</button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>DESCRIPTION</th>
                                        <th>PLAN (Rp)</th>
                                        <th>ACT (Rp)</th>
                                        <th>VAR (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedBudgets as $categoryId => $subcategories)
                                        <tr>
                                            <td colspan="4" class="font-weight-bold bg-secondary text-white">
                                                {{ $subcategories->first()->first()->description->subcategory->category->name }}
                                            </td>
                                        </tr>
                                        @foreach ($subcategories as $subcategoryId => $budgets)
                                            <tr>
                                                <td colspan="4" class="font-weight-bold">
                                                    &emsp;{{ $budgets->first()->description->subcategory->name }}
                                                </td>
                                            </tr>
                                            @foreach ($budgets as $budget)
                                                <tr>
                                                    <td class="description">
                                                        &emsp;&emsp;{{ $budget->description->description }}
                                                    </td>
                                                    <td>{{ number_format($budget->budget_amount, 2, ',', '.') }}</td>
                                                    <td>{{ number_format($budget->expenses->sum('amount'), 2, ',', '.') }}
                                                    </td>
                                                    <td>{{ number_format($budget->variance, 2, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        <!-- Total per kategori -->
                                        <tr>
                                            <td class="font-weight-bold">
                                                TOTAL
                                                {{ $subcategories->first()->first()->description->subcategory->category->name }}
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ number_format($categoryTotals[$categoryId]['totalPlan'], 2, ',', '.') }}
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ number_format($categoryTotals[$categoryId]['totalAct'], 2, ',', '.') }}
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ number_format($categoryTotals[$categoryId]['totalVar'], 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- Total overall -->
                                    <tr class="font-weight-bold bg-secondary">
                                        <td>TOTAL OVERALL</td>
                                        <td>
                                            {{ number_format($overallTotals['totalPlan'], 2, ',', '.') }}
                                        </td>
                                        <td>
                                            {{ number_format($overallTotals['totalAct'], 2, ',', '.') }}
                                        </td>
                                        <td>
                                            {{ number_format($overallTotals['totalVar'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <p>Please select a year and month to view the budget data.</p>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style>
        /* Media query for mobile devices */
        @media (max-width: 767px) {
            .table td.description {
                text-align: left;
                padding-left: 1rem;
                padding-right: 1rem;
                word-break: break-word;
                /* Ensure long words break correctly */
            }
        }
    </style>
@endsection
