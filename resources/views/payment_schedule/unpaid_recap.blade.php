@extends('layouts.app')

@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/LOGO_KKI.png') }}" alt="AdminLTELogo" height="60"
            width="60">
    </div>
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Unpaid Payments Recap</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Unpaid Payment Recap</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="card text-sm">
            <div class="card-header">
                <form action="" method="GET">
                    <div class="row align-items-center">
                        <!-- Input Filter -->
                        <div class="col-lg-4 col-md-12 mb-2 mb-lg-0">
                            <div class="form-group row mb-0">
                                <label for="due_date" class="col-md-6 col-form-label">Filter by Payment Date</label>
                                <div class="col-md-6">
                                    <input type="date" name="due_date" id="due_date" class="form-control form-control-sm"
                                        value="{{ request('due_date') }}">
                                </div>
                            </div>
                        </div>
                        <!-- Filter Buttons -->
                        <div class="col-lg-4 col-md-12 text-lg-right">
                            <div class="d-flex gap-2 justify-content-md-start justify-content-lg-start">
                                <button type="submit" class="btn btn-primary btn-sm mr-1">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="/payment_schedule/unpaid_recap" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>




    <section class="content pb-3">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="font-weight-bold">Total Unpaid Payments</h5>
                        <p class="text-muted">Sum of all unpaid payments</p>
                        <h3 class="text-danger">Rp
                            {{ number_format($paymentSchedules->sum('payment_amount'), 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="font-weight-bold">Overdue Payments</h5>
                        <p class="text-muted">Unpaid and overdue payments</p>
                        <h3 class="text-warning">Rp
                            {{ number_format(
                                $paymentSchedules->filter(function ($item) {
                                        return now() > \Carbon\Carbon::parse($item->due_date);
                                    })->sum('payment_amount'),
                                0,
                                ',',
                                '.',
                            ) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpaid Payments Table -->
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h3 class="card-title">Unpaid Payments (Sorted by Latest)</h3>
                <div class="card-tools">
                    <!-- Export Dropdown Button -->

                    <button class="btn btn-dark dropdown-toggle shadow-sm btn-sm" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" @if ($paymentSchedules->isEmpty()) disabled @endif>
                        <i class="fas fa-file-export"></i> Export
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-right">
                        <form action="/export/payment_supplier" method="GET">
                            <input type="hidden" name="status" value="Unpaid">
                            <input type="hidden" name="due_date" value="{{ request('due_date') }}">
                            <button type="submit" name="format" value="excel" class="dropdown-item">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Invoice Number</th>
                            <th>Supplier Name</th>
                            <th>Payment Amount</th>
                            <th>Purchase Date</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentSchedules as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->invoice_number }}</td>
                                <td>{{ $item->supplier_name }}</td>
                                <td>Rp {{ number_format($item->payment_amount, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->purchase_date)->format('d M Y') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}
                                    @if (now() > \Carbon\Carbon::parse($item->due_date))
                                        <span class="text-danger font-weight-bold ml-2">(Overdue)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No unpaid payment schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
