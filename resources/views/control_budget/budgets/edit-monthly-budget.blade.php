@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @php
                        $monthNames = [
                            '1' => 'Jan',
                            '2' => 'Feb',
                            '3' => 'Mar',
                            '4' => 'Apr',
                            '5' => 'May',
                            '6' => 'Jun',
                            '7' => 'Jul',
                            '8' => 'Aug',
                            '9' => 'Sep',
                            '10' => 'Oct',
                            '11' => 'Nov',
                            '12' => 'Dec',
                        ];
                        $monthName = $monthNames[$month] ?? 'Unknown';
                    @endphp
                    <h1 class="m-0">Edit Monthly Budget for {{ $unit->name }} ({{ $monthName }} / {{ $year }})
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/control-budget">Control Budget</a></li>
                        <li class="breadcrumb-item"><a href="/control-budget/unit/{{ $unit->id }}">Budget Categories</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Monthly Budget</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="/control-budget/monthly-budget/{{ $unit->id }}/update" method="POST">
                @csrf

                <!-- Year dan Month (readonly, karena tidak bisa diubah) -->
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">

                <!-- Display Categories, Subcategories, and Descriptions -->
                @foreach ($categories as $category)
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            {{ $category->name }} <!-- Category Name -->
                        </div>
                        <div class="card-body">
                            @foreach ($category->subcategories as $subcategory)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        {{ $subcategory->name }} <!-- Subcategory Name -->
                                    </div>
                                    <div class="card-body">
                                        @foreach ($subcategory->descriptions as $description)
                                            <div class="row align-items-center mb-2">
                                                <div class="col-md-6">
                                                    {{ $description->description }} <!-- Description Name -->
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" name="budget[{{ $description->id }}]"
                                                        class="form-control rupiah"
                                                        placeholder="Enter budget for this description"
                                                        value="{{ number_format($budgets[$description->id]->budget_amount ?? 0, 0, ',', '.') }}"
                                                        data-description-id="{{ $description->id }}"
                                                        id="formatted_{{ $description->id }}">

                                                    <!-- Input hidden untuk menyimpan nilai asli -->
                                                    <input type="hidden" name="budget_amounts[{{ $description->id }}]"
                                                        value="{{ $budgets[$description->id]->budget_amount ?? 0 }}"
                                                        id="hidden_{{ $description->id }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Button Submit -->
                <div class="text-right">
                    <button type="submit" class="btn btn-secondary mb-3">Update Budgets</button>
                </div>
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
                var id = e.target.dataset.descriptionId;
                var originalValue = e.target.value.replace(/[Rp.]/g,
                    ''); // Menghapus format Rupiah dari value
                var formattedValue = formatRupiah(originalValue, 'Rp');

                // Set tampilan dengan format Rupiah
                e.target.value = formattedValue;

                // Simpan nilai asli ke input hidden
                document.getElementById('hidden_' + id).value = originalValue;
            });
        });

        // Inisialisasi format Rupiah pada halaman dimuat
        document.querySelectorAll('.rupiah').forEach(function(input) {
            var originalValue = input.value.replace(/[Rp.]/g, ''); // Menghapus format Rupiah dari value
            input.value = formatRupiah(originalValue, 'Rp');
        });
    </script>
@endsection
