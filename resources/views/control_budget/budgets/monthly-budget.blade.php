@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Monthly Budget for {{ $unit->name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/control-budget">Control Budget</a></li>
                        <li class="breadcrumb-item"><a href="/control-budget/unit/{{ $unit->id }}">Budget Categories</a>
                        </li>
                        <li class="breadcrumb-item active">Monthly Budget</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="container">
                <!-- Form untuk Input Budget -->
                <form action="/control-budget/monthly-budget/store" method="POST">
                    @csrf

                    <!-- Input untuk Year dan Month -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="year">Year</label>
                            <input type="number" name="year" class="form-control" placeholder="Enter year"
                                min="2020" required>
                        </div>
                        <div class="col-md-6">
                            <label for="month">Month</label>
                            <select name="month" class="form-control" required>
                                <option value="" disabled selected>Select Month</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

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
                                                            class="form-control budget-input"
                                                            placeholder="Enter budget for this description" required>
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
                        <button type="submit" class="btn btn-secondary mb-3">Save All Budgets</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        // Fungsi untuk format angka menjadi format rupiah
        function formatRupiah(value) {
            let formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);

            // Menambahkan spasi setelah 'Rp'
            return formatted.replace('Rp', 'Rp ');
        }

        // Event listener untuk mengubah input menjadi format Rupiah
        document.querySelectorAll('.budget-input').forEach(function(input) {
            input.addEventListener('input', function(e) {
                // Hapus format lama
                let value = e.target.value.replace(/[^,\d]/g, '');
                // Tambahkan format baru
                e.target.value = formatRupiah(value);
            });
        });
    </script>
@endsection
