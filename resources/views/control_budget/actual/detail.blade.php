@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Department</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Department</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content pb-3 pt-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Actual Details</h3>
            </div>
            <div class="card-body">
                <!-- Informasi Anggaran Bulanan -->
                <h5 class="font-weight-bold">Monthly Budget Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Description</th>
                        <td>{{ $monthlyBudget->description->description_text ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Planned Budget</th>
                        <td>Rp {{ number_format($monthlyBudget->planned_budget, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Month</th>
                        <td>{{ $monthlyBudget->month }} {{ $monthlyBudget->year }}</td>
                    </tr>
                </table>

                <!-- Detail Pengeluaran Aktual -->
                <h5 class="font-weight-bold mt-4">Actual Spent Details</h5>
                @if ($monthlyBudget->actual->isNotEmpty())
                    <table class="table table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthlyBudget->actual as $actual)
                                <tr>
                                    <td>{{ $actual->spent_date->format('d-m-Y') }}</td>
                                    <td>{{ $actual->spent_description }}</td>
                                    <td>Rp {{ number_format($actual->actual_spent, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No actual spent records available for this budget.</p>
                @endif

                <!-- Kembali ke halaman sebelumnya -->
                <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Back</a>
            </div>
        </div>
    </section>
@endsection
