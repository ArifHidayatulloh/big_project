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
                    <h1 class="m-0">Paid Payments Recap</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Paid Payments Recap</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content text-sm">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title font-weight-bold">Filter Payments by Date Range</h3>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form action="" method="GET">
                    <div class="row mb-3">
                        <!-- Search by Invoice Number -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search" class="font-weight-normal">Search</label>
                                <input type="text" id="search" name="search" class="form-control form-control-sm"
                                    placeholder="Search by Invoice or Supplier" value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="startDate" class="font-weight-normal">Start Date</label>
                                <input type="date" id="startDate" name="startDate" class="form-control form-control-sm"
                                    value="{{ request('startDate') }}">
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="endDate" class="font-weight-normal">End Date</label>
                                <input type="date" id="endDate" name="endDate" class="form-control form-control-sm"
                                    value="{{ request('endDate') }}">
                            </div>
                        </div>

                        <!-- Submit and Reset Buttons -->
                        <div class="col-md-3 d-flex align-items-center mt-3">
                            <button type="submit" class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="/payment_schedule/paid_recap" class="btn btn-secondary btn-sm">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="content pb-3">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="font-weight-bold">Total Paid Payments</h5>
                        <p class="text-muted">Sum of all paid payments</p>
                        <h3 class="text-success">Rp
                            {{ number_format($paymentSchedules->sum('payment_amount'), 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Pembayaran Bulan Ini -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="font-weight-bold">Total Paid This Month</h5>
                        <p class="text-muted">Sum of payments this month</p>
                        <h3 class="text-primary">Rp
                            {{ number_format(
                                \App\Models\PaymentSchedule::whereMonth('paid_date', now()->month)->whereYear('paid_date', now()->year)->sum('payment_amount'),
                                0,
                                ',',
                                '.',
                            ) }}
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Pembayaran Minggu Ini -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="font-weight-bold">Total Paid This Week</h5>
                        <p class="text-muted">Sum of payments this week</p>
                        <h3 class="text-warning">Rp
                            <?php
                            $now = \Carbon\Carbon::now();
                            $weekStartDate = $now->startOfWeek();
                            $weekEndDate = $now->endOfWeek();

                            $paymentThisWeek = \App\Models\PaymentSchedule::whereBetween('paid_date', [$weekStartDate, $weekEndDate])->sum('payment_amount'); // menjumlahkan payment_amount dalam rentang tanggal minggu ini
                            ?>
                            {{ number_format($paymentThisWeek, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paid Payments Table -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="card-title">Paid Payments (Sorted by Latest)</h3>
                <div class="card-tools">
                    <!-- Export Dropdown Button -->

                    <button class="btn btn-dark dropdown-toggle shadow-sm btn-sm" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" @if ($paymentSchedules->isEmpty()) disabled @endif>
                        <i class="fas fa-file-export"></i> Export
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-right">
                        <form action="/export/payment_supplier" method="GET">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="Paid">
                            <input type="hidden" name="startDate" value="{{ request('startDate') }}">
                            <input type="hidden" name="endDate" value="{{ request('endDate') }}">
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
                            <th>Paid Date</th>
                            <th>Attachment</th>
                            <th>Description</th>
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
                                <td>{{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->paid_date)->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal"
                                        data-target="#pdfModal{{ $item->id }}">
                                        <i class="fas fa-paperclip"></i>
                                    </a>
                                    <!-- PDF Viewer Modal -->
                                    <div class="modal fade" id="pdfModal{{ $item->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="pdfModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content rounded-3">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title" id="pdfModalLabel{{ $item->id }}">
                                                        Attachment Viewer
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <!-- PDF Viewer -->
                                                    <div class="embed-responsive embed-responsive-16by9">
                                                        <iframe src="{{ asset('storage/' . $item->attachment) }}"
                                                            class="embed-responsive-item" width="100%" height="500px"
                                                            allow="fullscreen"></iframe>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <a href="{{ asset('storage/' . $item->attachment) }}"
                                                        class="btn btn-primary" download>
                                                        <i class="fas fa-download"></i> Download PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End of PDF Viewer Modal -->
                                </td>
                                <td>{{ $item->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No paid payment schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
