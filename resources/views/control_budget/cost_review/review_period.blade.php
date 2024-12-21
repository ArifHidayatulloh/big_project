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
                    <form action="/export/cost-review" method="GET">
                        <input type="hidden" name="cost_review_id" value="{{ $costReview->id }}">
                        <input type="hidden" name="years" value="{{ $selectedYear }}">

                        @if (is_array(request('months')))
                            @foreach (request('months') as $month)
                                <input type="hidden" name="months[]" value="{{ $month }}">
                            @endforeach
                        @else
                            <input type="hidden" name="months[]" value="{{ request('months') }}">
                        @endif

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </button>
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
                                $hasData = false; // Pastikan variabel ini diinisialisasi di luar loop
                            @endphp

                            @if ($descriptions->isEmpty())
                                <!-- Jika tidak ada data di $descriptions -->
                                <tr>
                                    <td colspan="6" class="text-center text-danger font-weight-bold">
                                        No descriptions available for this Cost Review.
                                    </td>
                                </tr>
                            @else
                                @foreach ($descriptions as $description)
                                    @if ($description['has_monthly_budget'])
                                        @php $hasData = true; @endphp <!-- Set true jika ada data valid -->

                                        @if ($currentCategory !== $description['category'])
                                            <!-- Menampilkan Category -->
                                            @php $currentCategory = $description['category']; @endphp
                                            <tr>
                                                <td colspan="6" class="font-weight-bold text-primary">
                                                    {{ $currentCategory }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($currentSubcategory !== $description['subcategory'])
                                            <!-- Menampilkan Subcategory -->
                                            @php $currentSubcategory = $description['subcategory']; @endphp
                                            <tr>
                                                <td colspan="6" class="font-italic bg-light" style="padding-left: 20px;">
                                                    {{ $currentSubcategory }}
                                                </td>
                                            </tr>
                                        @endif

                                        <!-- Menampilkan Description -->
                                        <tr>
                                            <td style="padding-left: 40px;">{{ $description['description'] ?? 'N/A' }}</td>
                                            <td class="text-right">
                                                {{ number_format($description['actual_spent'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($description['planned_budget'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($description['variance'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-center">{{ number_format($description['percentage'], 2) }}%
                                            </td>
                                            <td>{{ $description['remarks'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if (!$hasData)
                                    <!-- Jika semua data `monthly_budget` kosong -->
                                    <tr>
                                        <td colspan="6" class="text-center text-danger font-weight-bold">
                                            No data available for monthly budget.
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
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
