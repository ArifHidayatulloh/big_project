@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Budget</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">{{ $costReview->review_name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content pb-3">
        <div class="container-fluid">
            <form action="{{ url('/budget/update') }}" method="POST">
                @csrf
                <input type="hidden" name="cost_review_id" value="{{ $costReview->id }}">

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Planned Budget Details</h3>
                    </div>

                    <div class="card-body">
                        <!-- Input Tahun -->
                        <div class="form-group">
                            <label for="year">Year:</label>
                            <input type="number" class="form-control" name="year" id="year"
                                placeholder="Enter year" value="{{ date('Y') }}" min="1" required readonly>
                        </div>

                        <!-- Input Bulan -->
                        <!-- Input Bulan -->
                        <div class="form-group">
                            <label>Month:</label>
                            <input type="text" class="form-control"
                                value="{{ date('F', mktime(0, 0, 0, $monthNumber, 10)) }}" readonly name="month">
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="card-title">Input Amounts</h3>
                    </div>

                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentCategory = null;
                                    $currentSubcategory = null;
                                @endphp

                                @forelse ($descriptions as $description)
                                    @if ($currentCategory !== ($description->subcategory->category->category_name ?? 'N/A'))
                                        <!-- Menampilkan Category -->
                                        @php $currentCategory = $description->subcategory->category->category_name ?? 'N/A'; @endphp
                                        <tr>
                                            <td colspan="2" class="font-weight-bold bg-light">{{ $currentCategory }}</td>
                                        </tr>
                                    @endif

                                    @if ($currentSubcategory !== ($description->subcategory->sub_category_name ?? 'N/A'))
                                        <!-- Menampilkan Subcategory -->
                                        @php $currentSubcategory = $description->subcategory->sub_category_name ?? 'N/A'; @endphp
                                        <tr>
                                            <td colspan="2" class="font-italic" style="padding-left: 20px;">
                                                {{ $currentSubcategory }}
                                            </td>
                                        </tr>
                                    @endif

                                    <!-- Menampilkan Description -->
                                    <tr>
                                        <td style="padding-left: 40px;">{{ $description->description_text ?? 'N/A' }}</td>
                                        <td>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <strong class="input-group-text">
                                                        Rp
                                                    </strong>
                                                </div>
                                                @php
                                                    $plannedBudget =
                                                    $description->monthly_budget->first()->planned_budget ?? 0;
                                                @endphp
                                                <input type="text" name="planned_budget[{{ $description->id }}]"
                                                    class="form-control amount-input" placeholder="Enter Amount"
                                                    value="{{ old('planned_budget.' . $description->id, number_format($plannedBudget, 0, ',', '.')) }}">
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No descriptions available for this Cost
                                            Review.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Buttons Section -->
                <div class="d-flex justify-content-between mt-4 align-items-center">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-m">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-outline-success btn-m">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Format Rupiah Function
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
            return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
        }

        // Apply format Rupiah on input
        document.querySelectorAll('.amount-input').forEach(function(input) {
            input.addEventListener('input', function(e) {
                this.value = formatRupiah(this.value, 'Rp ');
            });
        });
    </script>
@endsection
