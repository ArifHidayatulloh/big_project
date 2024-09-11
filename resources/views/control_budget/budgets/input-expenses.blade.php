@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Input Expenses for {{ $unit->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/control-budget">Control Budget</a></li>
                        <li class="breadcrumb-item"><a href="/control-budget/unit/{{ $unit->id }}">Budget Categories</a>
                        </li>
                        <li class="breadcrumb-item active">Input Expenses</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="/control-budget/inputExpense/{{ $unit->id }}" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <label for="year">Year</label>
                        <input type="number" name="year" value="{{ old('year', $year) }}" class="form-control"
                            min="2020" required>
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

            <form action="/control-budget/storeExpense/{{ $unit->id }}" method="POST">
                @csrf
                @if ($year && $month)
                    @if ($groupedBudgets && $groupedBudgets->isNotEmpty())
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>DESCRIPTION</th>
                                        <th>PLAN (Rp)</th>
                                        <th>ACT (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedBudgets as $categoryId => $subcategories)
                                        <tr>
                                            <td colspan="3" class="font-weight-bold bg-secondary text-white">
                                                {{ $subcategories->first()->first()->description->subcategory->category->name }}
                                            </td>
                                        </tr>
                                        @foreach ($subcategories as $subcategoryId => $budgets)
                                            <tr>
                                                <td colspan="3" class="font-weight-bold">
                                                    &emsp;{{ $budgets->first()->description->subcategory->name }}
                                                </td>
                                            </tr>
                                            @foreach ($budgets as $budget)
                                                @php
                                                    // Cari nilai expense yang sesuai dengan budget saat ini
                                                    $currentExpense = $expenses->firstWhere(
                                                        'budget_id',
                                                        $budget->id,
                                                    );
                                                    $currentExpenseValue = $currentExpense
                                                        ? number_format($currentExpense->amount, 2, ',', '.')
                                                        : '';
                                                @endphp
                                                <tr>
                                                    <td>&emsp;&emsp;{{ $budget->description->description }}</td>
                                                    <td>{{ number_format($budget->budget_amount, 2, ',', '.') }}</td>
                                                    <td>
                                                        <!-- Input terlihat untuk pengguna -->
                                                        <input type="text" class="form-control rupiah"
                                                            placeholder="Enter expense amount"
                                                            value="{{ $currentExpenseValue }}"
                                                            data-budget-id="{{ $budget->id }}"
                                                            id="formatted_{{ $budget->id }}">

                                                        <!-- Input hidden untuk menyimpan nilai asli -->
                                                        <input type="hidden" name="expenses[{{ $budget->id }}][amount]"
                                                            value="{{ $currentExpense ? $currentExpense->amount : '' }}"
                                                            id="hidden_{{ $budget->id }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-secondary mt-3 mb-3">Save Expenses</button>
                        </div>
                    @else
                        <p>No budgets available for the selected year and month. Please choose a different year or month.
                        </p>
                    @endif
                @else
                    <p>Please select a year and month to view the budget data.</p>
                @endif
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Fungsi format ke Rupiah
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        // Saat pengguna mengetik, ubah format ke Rupiah
        document.querySelectorAll('.rupiah').forEach(function(input) {
            input.addEventListener('input', function(e) {
                var id = e.target.dataset.budgetId;
                var originalValue = e.target.value.replace(/[Rp.]/g,
                ''); // Menghapus format rupiah dari value
                var formattedValue = formatRupiah(originalValue, 'Rp');

                // Set tampilan dengan format Rupiah
                e.target.value = formattedValue;

                // Simpan nilai asli ke input hidden
                document.getElementById('hidden_' + id).value = originalValue;
            });
        });

        // Inisialisasi format Rupiah pada halaman dimuat
        document.querySelectorAll('.rupiah').forEach(function(input) {
            var originalValue = input.value.replace(/[Rp.]/g, ''); // Menghapus format rupiah dari value
            input.value = formatRupiah(originalValue, 'Rp');
        });
    </script>
@endsection
